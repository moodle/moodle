<?php
/*
 * Copyright 2016-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\GridFS;

use HashContext;
use MongoDB\BSON\Binary;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;

use function array_intersect_key;
use function hash_final;
use function hash_init;
use function hash_update;
use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function is_string;
use function MongoDB\is_string_array;
use function sprintf;
use function strlen;
use function substr;

/**
 * WritableStream abstracts the process of writing a GridFS file.
 *
 * @internal
 */
class WritableStream
{
    /** @var integer */
    private static $defaultChunkSizeBytes = 261120;

    /** @var string */
    private $buffer = '';

    /** @var integer */
    private $chunkOffset = 0;

    /** @var integer */
    private $chunkSize;

    /** @var boolean */
    private $disableMD5;

    /** @var CollectionWrapper */
    private $collectionWrapper;

    /** @var array */
    private $file;

    /** @var HashContext|null */
    private $hashCtx;

    /** @var boolean */
    private $isClosed = false;

    /** @var integer */
    private $length = 0;

    /**
     * Constructs a writable GridFS stream.
     *
     * Supported options:
     *
     *  * _id (mixed): File document identifier. Defaults to a new ObjectId.
     *
     *  * aliases (array of strings): DEPRECATED An array of aliases.
     *    Applications wishing to store aliases should add an aliases field to
     *    the metadata document instead.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to
     *    261120 (i.e. 255 KiB).
     *
     *  * disableMD5 (boolean): When true, no MD5 sum will be generated.
     *    Defaults to "false".
     *
     *  * contentType (string): DEPRECATED content type to be stored with the
     *    file. This information should now be added to the metadata.
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param CollectionWrapper $collectionWrapper GridFS collection wrapper
     * @param string            $filename          Filename
     * @param array             $options           Upload options
     * @throws InvalidArgumentException
     */
    public function __construct(CollectionWrapper $collectionWrapper, string $filename, array $options = [])
    {
        $options += [
            '_id' => new ObjectId(),
            'chunkSizeBytes' => self::$defaultChunkSizeBytes,
            'disableMD5' => false,
        ];

        if (isset($options['aliases']) && ! is_string_array($options['aliases'])) {
            throw InvalidArgumentException::invalidType('"aliases" option', $options['aliases'], 'array of strings');
        }

        if (! is_integer($options['chunkSizeBytes'])) {
            throw InvalidArgumentException::invalidType('"chunkSizeBytes" option', $options['chunkSizeBytes'], 'integer');
        }

        if ($options['chunkSizeBytes'] < 1) {
            throw new InvalidArgumentException(sprintf('Expected "chunkSizeBytes" option to be >= 1, %d given', $options['chunkSizeBytes']));
        }

        if (! is_bool($options['disableMD5'])) {
            throw InvalidArgumentException::invalidType('"disableMD5" option', $options['disableMD5'], 'boolean');
        }

        if (isset($options['contentType']) && ! is_string($options['contentType'])) {
            throw InvalidArgumentException::invalidType('"contentType" option', $options['contentType'], 'string');
        }

        if (isset($options['metadata']) && ! is_array($options['metadata']) && ! is_object($options['metadata'])) {
            throw InvalidArgumentException::invalidType('"metadata" option', $options['metadata'], 'array or object');
        }

        $this->chunkSize = $options['chunkSizeBytes'];
        $this->collectionWrapper = $collectionWrapper;
        $this->disableMD5 = $options['disableMD5'];

        if (! $this->disableMD5) {
            $this->hashCtx = hash_init('md5');
        }

        $this->file = [
            '_id' => $options['_id'],
            'chunkSize' => $this->chunkSize,
            'filename' => $filename,
        ] + array_intersect_key($options, ['aliases' => 1, 'contentType' => 1, 'metadata' => 1]);
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return [
            'bucketName' => $this->collectionWrapper->getBucketName(),
            'databaseName' => $this->collectionWrapper->getDatabaseName(),
            'file' => $this->file,
        ];
    }

    /**
     * Closes an active stream and flushes all buffered data to GridFS.
     */
    public function close(): void
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return;
        }

        if (strlen($this->buffer) > 0) {
            $this->insertChunkFromBuffer();
        }

        $this->fileCollectionInsert();
        $this->isClosed = true;
    }

    /**
     * Return the stream's file document.
     */
    public function getFile(): object
    {
        return (object) $this->file;
    }

    /**
     * Return the stream's size in bytes.
     *
     * Note: this value will increase as more data is written to the stream.
     */
    public function getSize(): int
    {
        return $this->length + strlen($this->buffer);
    }

    /**
     * Return the current position of the stream.
     *
     * This is the offset within the stream where the next byte would be
     * written. Since seeking is not supported and writes are appended, this is
     * always the end of the stream.
     *
     * @see WritableStream::getSize()
     */
    public function tell(): int
    {
        return $this->getSize();
    }

    /**
     * Inserts binary data into GridFS via chunks.
     *
     * Data will be buffered internally until chunkSizeBytes are accumulated, at
     * which point a chunk document will be inserted and the buffer reset.
     *
     * @param string $data Binary data to write
     */
    public function writeBytes(string $data): int
    {
        if ($this->isClosed) {
            // TODO: Should this be an error condition? e.g. BadMethodCallException
            return 0;
        }

        $bytesRead = 0;

        while ($bytesRead != strlen($data)) {
            $initialBufferLength = strlen($this->buffer);
            $this->buffer .= substr($data, $bytesRead, $this->chunkSize - $initialBufferLength);
            $bytesRead += strlen($this->buffer) - $initialBufferLength;

            if (strlen($this->buffer) == $this->chunkSize) {
                $this->insertChunkFromBuffer();
            }
        }

        return $bytesRead;
    }

    private function abort(): void
    {
        try {
            $this->collectionWrapper->deleteChunksByFilesId($this->file['_id']);
        } catch (DriverRuntimeException $e) {
            // We are already handling an error if abort() is called, so suppress this
        }

        $this->isClosed = true;
    }

    /**
     * @return mixed
     */
    private function fileCollectionInsert()
    {
        $this->file['length'] = $this->length;
        $this->file['uploadDate'] = new UTCDateTime();

        if (! $this->disableMD5 && $this->hashCtx) {
            $this->file['md5'] = hash_final($this->hashCtx);
        }

        try {
            $this->collectionWrapper->insertFile($this->file);
        } catch (DriverRuntimeException $e) {
            $this->abort();

            throw $e;
        }

        return $this->file['_id'];
    }

    private function insertChunkFromBuffer(): void
    {
        if (strlen($this->buffer) == 0) {
            return;
        }

        $data = $this->buffer;
        $this->buffer = '';

        $chunk = [
            'files_id' => $this->file['_id'],
            'n' => $this->chunkOffset,
            'data' => new Binary($data, Binary::TYPE_GENERIC),
        ];

        if (! $this->disableMD5 && $this->hashCtx) {
            hash_update($this->hashCtx, $data);
        }

        try {
            $this->collectionWrapper->insertChunk($chunk);
        } catch (DriverRuntimeException $e) {
            $this->abort();

            throw $e;
        }

        $this->length += strlen($data);
        $this->chunkOffset++;
    }
}
