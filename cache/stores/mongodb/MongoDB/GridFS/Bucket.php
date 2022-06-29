<?php
/*
 * Copyright 2016-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\GridFS;

use MongoDB\Collection;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\GridFS\Exception\CorruptFileException;
use MongoDB\GridFS\Exception\FileNotFoundException;
use MongoDB\GridFS\Exception\StreamException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\Find;
use stdClass;
use function array_intersect_key;
use function fopen;
use function get_resource_type;
use function in_array;
use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function is_resource;
use function is_string;
use function method_exists;
use function MongoDB\apply_type_map_to_document;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toJSON;
use function property_exists;
use function sprintf;
use function stream_context_create;
use function stream_copy_to_stream;
use function stream_get_meta_data;
use function stream_get_wrappers;
use function urlencode;

/**
 * Bucket provides a public API for interacting with the GridFS files and chunks
 * collections.
 *
 * @api
 */
class Bucket
{
    /** @var string */
    private static $defaultBucketName = 'fs';

    /** @var integer */
    private static $defaultChunkSizeBytes = 261120;

    /** @var array */
    private static $defaultTypeMap = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    /** @var string */
    private static $streamWrapperProtocol = 'gridfs';

    /** @var CollectionWrapper */
    private $collectionWrapper;

    /** @var string */
    private $databaseName;

    /** @var Manager */
    private $manager;

    /** @var string */
    private $bucketName;

    /** @var boolean */
    private $disableMD5;

    /** @var integer */
    private $chunkSizeBytes;

    /** @var ReadConcern */
    private $readConcern;

    /** @var ReadPreference */
    private $readPreference;

    /** @var array */
    private $typeMap;

    /** @var WriteConcern */
    private $writeConcern;

    /**
     * Constructs a GridFS bucket.
     *
     * Supported options:
     *
     *  * bucketName (string): The bucket name, which will be used as a prefix
     *    for the files and chunks collections. Defaults to "fs".
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to
     *    261120 (i.e. 255 KiB).
     *
     *  * disableMD5 (boolean): When true, no MD5 sum will be generated for
     *    each stored file. Defaults to "false".
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Bucket options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(Manager $manager, $databaseName, array $options = [])
    {
        $options += [
            'bucketName' => self::$defaultBucketName,
            'chunkSizeBytes' => self::$defaultChunkSizeBytes,
            'disableMD5' => false,
        ];

        if (! is_string($options['bucketName'])) {
            throw InvalidArgumentException::invalidType('"bucketName" option', $options['bucketName'], 'string');
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

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        $this->manager = $manager;
        $this->databaseName = (string) $databaseName;
        $this->bucketName = $options['bucketName'];
        $this->chunkSizeBytes = $options['chunkSizeBytes'];
        $this->disableMD5 = $options['disableMD5'];
        $this->readConcern = $options['readConcern'] ?? $this->manager->getReadConcern();
        $this->readPreference = $options['readPreference'] ?? $this->manager->getReadPreference();
        $this->typeMap = $options['typeMap'] ?? self::$defaultTypeMap;
        $this->writeConcern = $options['writeConcern'] ?? $this->manager->getWriteConcern();

        $collectionOptions = array_intersect_key($options, ['readConcern' => 1, 'readPreference' => 1, 'typeMap' => 1, 'writeConcern' => 1]);

        $this->collectionWrapper = new CollectionWrapper($manager, $databaseName, $options['bucketName'], $collectionOptions);
        $this->registerStreamWrapper();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'bucketName' => $this->bucketName,
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'chunkSizeBytes' => $this->chunkSizeBytes,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Delete a file from the GridFS bucket.
     *
     * If the files collection document is not found, this method will still
     * attempt to delete orphaned chunks.
     *
     * @param mixed $id File ID
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function delete($id)
    {
        $file = $this->collectionWrapper->findFileById($id);
        $this->collectionWrapper->deleteFileAndChunksById($id);

        if ($file === null) {
            throw FileNotFoundException::byId($id, $this->getFilesNamespace());
        }
    }

    /**
     * Writes the contents of a GridFS file to a writable stream.
     *
     * @param mixed    $id          File ID
     * @param resource $destination Writable Stream
     * @throws FileNotFoundException if no file could be selected
     * @throws InvalidArgumentException if $destination is not a stream
     * @throws StreamException if the file could not be uploaded
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function downloadToStream($id, $destination)
    {
        if (! is_resource($destination) || get_resource_type($destination) != "stream") {
            throw InvalidArgumentException::invalidType('$destination', $destination, 'resource');
        }

        $source = $this->openDownloadStream($id);
        if (@stream_copy_to_stream($source, $destination) === false) {
            throw StreamException::downloadFromIdFailed($id, $source, $destination);
        }
    }

    /**
     * Writes the contents of a GridFS file, which is selected by name and
     * revision, to a writable stream.
     *
     * Supported options:
     *
     *  * revision (integer): Which revision (i.e. documents with the same
     *    filename and different uploadDate) of the file to retrieve. Defaults
     *    to -1 (i.e. the most recent revision).
     *
     * Revision numbers are defined as follows:
     *
     *  * 0 = the original stored file
     *  * 1 = the first revision
     *  * 2 = the second revision
     *  * etc…
     *  * -2 = the second most recent revision
     *  * -1 = the most recent revision
     *
     * @param string   $filename    Filename
     * @param resource $destination Writable Stream
     * @param array    $options     Download options
     * @throws FileNotFoundException if no file could be selected
     * @throws InvalidArgumentException if $destination is not a stream
     * @throws StreamException if the file could not be uploaded
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function downloadToStreamByName($filename, $destination, array $options = [])
    {
        if (! is_resource($destination) || get_resource_type($destination) != "stream") {
            throw InvalidArgumentException::invalidType('$destination', $destination, 'resource');
        }

        $source = $this->openDownloadStreamByName($filename, $options);
        if (@stream_copy_to_stream($source, $destination) === false) {
            throw StreamException::downloadFromFilenameFailed($filename, $source, $destination);
        }
    }

    /**
     * Drops the files and chunks collections associated with this GridFS
     * bucket.
     *
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function drop()
    {
        $this->collectionWrapper->dropCollections();
    }

    /**
     * Finds documents from the GridFS bucket's files collection matching the
     * query.
     *
     * @see Find::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return Cursor
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function find($filter = [], array $options = [])
    {
        return $this->collectionWrapper->findFiles($filter, $options);
    }

    /**
     * Finds a single document from the GridFS bucket's files collection
     * matching the query.
     *
     * @see FindOne::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return array|object|null
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function findOne($filter = [], array $options = [])
    {
        return $this->collectionWrapper->findOneFile($filter, $options);
    }

    /**
     * Return the bucket name.
     *
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * Return the chunks collection.
     *
     * @return Collection
     */
    public function getChunksCollection()
    {
        return $this->collectionWrapper->getChunksCollection();
    }

    /**
     * Return the chunk size in bytes.
     *
     * @return integer
     */
    public function getChunkSizeBytes()
    {
        return $this->chunkSizeBytes;
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Gets the file document of the GridFS file associated with a stream.
     *
     * @param resource $stream GridFS stream
     * @return array|object
     * @throws InvalidArgumentException if $stream is not a GridFS stream
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function getFileDocumentForStream($stream)
    {
        $file = $this->getRawFileDocumentForStream($stream);

        // Filter the raw document through the specified type map
        return apply_type_map_to_document($file, $this->typeMap);
    }

    /**
     * Gets the file document's ID of the GridFS file associated with a stream.
     *
     * @param resource $stream GridFS stream
     * @return mixed
     * @throws CorruptFileException if the file "_id" field does not exist
     * @throws InvalidArgumentException if $stream is not a GridFS stream
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function getFileIdForStream($stream)
    {
        $file = $this->getRawFileDocumentForStream($stream);

        /* Filter the raw document through the specified type map, but override
         * the root type so we can reliably access the ID.
         */
        $typeMap = ['root' => 'stdClass'] + $this->typeMap;
        $file = apply_type_map_to_document($file, $typeMap);

        if (! isset($file->_id) && ! property_exists($file, '_id')) {
            throw new CorruptFileException('file._id does not exist');
        }

        return $file->_id;
    }

    /**
     * Return the files collection.
     *
     * @return Collection
     */
    public function getFilesCollection()
    {
        return $this->collectionWrapper->getFilesCollection();
    }

    /**
     * Return the read concern for this GridFS bucket.
     *
     * @see http://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @return ReadConcern
     */
    public function getReadConcern()
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this GridFS bucket.
     *
     * @return ReadPreference
     */
    public function getReadPreference()
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this GridFS bucket.
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this GridFS bucket.
     *
     * @see http://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     * @return WriteConcern
     */
    public function getWriteConcern()
    {
        return $this->writeConcern;
    }

    /**
     * Opens a readable stream for reading a GridFS file.
     *
     * @param mixed $id File ID
     * @return resource
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function openDownloadStream($id)
    {
        $file = $this->collectionWrapper->findFileById($id);

        if ($file === null) {
            throw FileNotFoundException::byId($id, $this->getFilesNamespace());
        }

        return $this->openDownloadStreamByFile($file);
    }

    /**
     * Opens a readable stream stream to read a GridFS file, which is selected
     * by name and revision.
     *
     * Supported options:
     *
     *  * revision (integer): Which revision (i.e. documents with the same
     *    filename and different uploadDate) of the file to retrieve. Defaults
     *    to -1 (i.e. the most recent revision).
     *
     * Revision numbers are defined as follows:
     *
     *  * 0 = the original stored file
     *  * 1 = the first revision
     *  * 2 = the second revision
     *  * etc…
     *  * -2 = the second most recent revision
     *  * -1 = the most recent revision
     *
     * @param string $filename Filename
     * @param array  $options  Download options
     * @return resource
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function openDownloadStreamByName($filename, array $options = [])
    {
        $options += ['revision' => -1];

        $file = $this->collectionWrapper->findFileByFilenameAndRevision($filename, $options['revision']);

        if ($file === null) {
            throw FileNotFoundException::byFilenameAndRevision($filename, $options['revision'], $this->getFilesNamespace());
        }

        return $this->openDownloadStreamByFile($file);
    }

    /**
     * Opens a writable stream for writing a GridFS file.
     *
     * Supported options:
     *
     *  * _id (mixed): File document identifier. Defaults to a new ObjectId.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to the
     *    bucket's chunk size.
     *
     *  * disableMD5 (boolean): When true, no MD5 sum will be generated for
     *    the stored file. Defaults to "false".
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param string $filename Filename
     * @param array  $options  Upload options
     * @return resource
     */
    public function openUploadStream($filename, array $options = [])
    {
        $options += ['chunkSizeBytes' => $this->chunkSizeBytes];

        $path = $this->createPathForUpload();
        $context = stream_context_create([
            self::$streamWrapperProtocol => [
                'collectionWrapper' => $this->collectionWrapper,
                'filename' => $filename,
                'options' => $options,
            ],
        ]);

        return fopen($path, 'w', false, $context);
    }

    /**
     * Renames the GridFS file with the specified ID.
     *
     * @param mixed  $id          File ID
     * @param string $newFilename New filename
     * @throws FileNotFoundException if no file could be selected
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function rename($id, $newFilename)
    {
        $updateResult = $this->collectionWrapper->updateFilenameForId($id, $newFilename);

        if ($updateResult->getModifiedCount() === 1) {
            return;
        }

        /* If the update resulted in no modification, it's possible that the
         * file did not exist, in which case we must raise an error. Checking
         * the write result's matched count will be most efficient, but fall
         * back to a findOne operation if necessary (i.e. legacy writes).
         */
        $found = $updateResult->getMatchedCount() !== null
            ? $updateResult->getMatchedCount() === 1
            : $this->collectionWrapper->findFileById($id) !== null;

        if (! $found) {
            throw FileNotFoundException::byId($id, $this->getFilesNamespace());
        }
    }

    /**
     * Writes the contents of a readable stream to a GridFS file.
     *
     * Supported options:
     *
     *  * _id (mixed): File document identifier. Defaults to a new ObjectId.
     *
     *  * chunkSizeBytes (integer): The chunk size in bytes. Defaults to the
     *    bucket's chunk size.
     *
     *  * disableMD5 (boolean): When true, no MD5 sum will be generated for
     *    the stored file. Defaults to "false".
     *
     *  * metadata (document): User data for the "metadata" field of the files
     *    collection document.
     *
     * @param string   $filename Filename
     * @param resource $source   Readable stream
     * @param array    $options  Stream options
     * @return mixed ID of the newly created GridFS file
     * @throws InvalidArgumentException if $source is not a GridFS stream
     * @throws StreamException if the file could not be uploaded
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function uploadFromStream($filename, $source, array $options = [])
    {
        if (! is_resource($source) || get_resource_type($source) != "stream") {
            throw InvalidArgumentException::invalidType('$source', $source, 'resource');
        }

        $destination = $this->openUploadStream($filename, $options);

        if (@stream_copy_to_stream($source, $destination) === false) {
            $destinationUri = $this->createPathForFile($this->getRawFileDocumentForStream($destination));
            throw StreamException::uploadFailed($filename, $source, $destinationUri);
        }

        return $this->getFileIdForStream($destination);
    }

    /**
     * Creates a path for an existing GridFS file.
     *
     * @param stdClass $file GridFS file document
     * @return string
     */
    private function createPathForFile(stdClass $file)
    {
        if (! is_object($file->_id) || method_exists($file->_id, '__toString')) {
            $id = (string) $file->_id;
        } else {
            $id = toJSON(fromPHP(['_id' => $file->_id]));
        }

        return sprintf(
            '%s://%s/%s.files/%s',
            self::$streamWrapperProtocol,
            urlencode($this->databaseName),
            urlencode($this->bucketName),
            urlencode($id)
        );
    }

    /**
     * Creates a path for a new GridFS file, which does not yet have an ID.
     *
     * @return string
     */
    private function createPathForUpload()
    {
        return sprintf(
            '%s://%s/%s.files',
            self::$streamWrapperProtocol,
            urlencode($this->databaseName),
            urlencode($this->bucketName)
        );
    }

    /**
     * Returns the names of the files collection.
     *
     * @return string
     */
    private function getFilesNamespace()
    {
        return sprintf('%s.%s.files', $this->databaseName, $this->bucketName);
    }

    /**
     * Gets the file document of the GridFS file associated with a stream.
     *
     * This returns the raw document from the StreamWrapper, which does not
     * respect the Bucket's type map.
     *
     * @param resource $stream GridFS stream
     * @return stdClass
     * @throws InvalidArgumentException
     */
    private function getRawFileDocumentForStream($stream)
    {
        if (! is_resource($stream) || get_resource_type($stream) != "stream") {
            throw InvalidArgumentException::invalidType('$stream', $stream, 'resource');
        }

        $metadata = stream_get_meta_data($stream);

        if (! isset($metadata['wrapper_data']) || ! $metadata['wrapper_data'] instanceof StreamWrapper) {
            throw InvalidArgumentException::invalidType('$stream wrapper data', $metadata['wrapper_data'] ?? null, StreamWrapper::class);
        }

        return $metadata['wrapper_data']->getFile();
    }

    /**
     * Opens a readable stream for the GridFS file.
     *
     * @param stdClass $file GridFS file document
     * @return resource
     */
    private function openDownloadStreamByFile(stdClass $file)
    {
        $path = $this->createPathForFile($file);
        $context = stream_context_create([
            self::$streamWrapperProtocol => [
                'collectionWrapper' => $this->collectionWrapper,
                'file' => $file,
            ],
        ]);

        return fopen($path, 'r', false, $context);
    }

    /**
     * Registers the GridFS stream wrapper if it is not already registered.
     */
    private function registerStreamWrapper()
    {
        if (in_array(self::$streamWrapperProtocol, stream_get_wrappers())) {
            return;
        }

        StreamWrapper::register(self::$streamWrapperProtocol);
    }
}
