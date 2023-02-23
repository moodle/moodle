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

use MongoDB\BSON\UTCDateTime;

use function assert;
use function explode;
use function in_array;
use function is_integer;
use function is_resource;
use function stream_context_get_options;
use function stream_get_wrappers;
use function stream_wrapper_register;
use function stream_wrapper_unregister;

use const SEEK_CUR;
use const SEEK_END;
use const SEEK_SET;
use const STREAM_IS_URL;

/**
 * Stream wrapper for reading and writing a GridFS file.
 *
 * @internal
 * @see Bucket::openUploadStream()
 * @see Bucket::openDownloadStream()
 */
class StreamWrapper
{
    /** @var resource|null Stream context (set by PHP) */
    public $context;

    /** @var string|null */
    private $mode;

    /** @var string|null */
    private $protocol;

    /** @var ReadableStream|WritableStream|null */
    private $stream;

    public function __destruct()
    {
        /* This destructor is a workaround for PHP trying to use the stream well
         * after all objects have been destructed. This can cause autoloading
         * issues and possibly segmentation faults during PHP shutdown. */
        $this->stream = null;
    }

    /**
     * Return the stream's file document.
     */
    public function getFile(): object
    {
        assert($this->stream !== null);

        return $this->stream->getFile();
    }

    /**
     * Register the GridFS stream wrapper.
     *
     * @param string $protocol Protocol to use for stream_wrapper_register()
     */
    public static function register(string $protocol = 'gridfs'): void
    {
        if (in_array($protocol, stream_get_wrappers())) {
            stream_wrapper_unregister($protocol);
        }

        stream_wrapper_register($protocol, static::class, STREAM_IS_URL);
    }

    /**
     * Closes the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-close.php
     */
    public function stream_close(): void
    {
        if (! $this->stream) {
            return;
        }

        $this->stream->close();
    }

    /**
     * Returns whether the file pointer is at the end of the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-eof.php
     */
    public function stream_eof(): bool
    {
        if (! $this->stream instanceof ReadableStream) {
            return false;
        }

        return $this->stream->isEOF();
    }

    /**
     * Opens the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-open.php
     * @param string      $path       Path to the file resource
     * @param string      $mode       Mode used to open the file (only "r" and "w" are supported)
     * @param integer     $options    Additional flags set by the streams API
     * @param string|null $openedPath Not used
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        $this->initProtocol($path);
        $this->mode = $mode;

        if ($mode === 'r') {
            return $this->initReadableStream();
        }

        if ($mode === 'w') {
            return $this->initWritableStream();
        }

        return false;
    }

    /**
     * Read bytes from the stream.
     *
     * Note: this method may return a string smaller than the requested length
     * if data is not available to be read.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-read.php
     * @param integer $length Number of bytes to read
     */
    public function stream_read(int $length): string
    {
        if (! $this->stream instanceof ReadableStream) {
            return '';
        }

        return $this->stream->readBytes($length);
    }

    /**
     * Return the current position of the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-seek.php
     * @param integer $offset Stream offset to seek to
     * @param integer $whence One of SEEK_SET, SEEK_CUR, or SEEK_END
     * @return boolean True if the position was updated and false otherwise
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        assert($this->stream !== null);

        $size = $this->stream->getSize();

        if ($whence === SEEK_CUR) {
            $offset += $this->stream->tell();
        }

        if ($whence === SEEK_END) {
            $offset += $size;
        }

        // WritableStreams are always positioned at the end of the stream
        if ($this->stream instanceof WritableStream) {
            return $offset === $size;
        }

        if ($offset < 0 || $offset > $size) {
            return false;
        }

        $this->stream->seek($offset);

        return true;
    }

    /**
     * Return information about the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-stat.php
     */
    public function stream_stat(): array
    {
        assert($this->stream !== null);

        $stat = $this->getStatTemplate();

        $stat[2] = $stat['mode'] = $this->stream instanceof ReadableStream
            ? 0100444  // S_IFREG & S_IRUSR & S_IRGRP & S_IROTH
            : 0100222; // S_IFREG & S_IWUSR & S_IWGRP & S_IWOTH
        $stat[7] = $stat['size'] = $this->stream->getSize();

        $file = $this->stream->getFile();

        if (isset($file->uploadDate) && $file->uploadDate instanceof UTCDateTime) {
            $timestamp = $file->uploadDate->toDateTime()->getTimestamp();
            $stat[9] = $stat['mtime'] = $timestamp;
            $stat[10] = $stat['ctime'] = $timestamp;
        }

        if (isset($file->chunkSize) && is_integer($file->chunkSize)) {
            $stat[11] = $stat['blksize'] = $file->chunkSize;
        }

        return $stat;
    }

    /**
     * Return the current position of the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-tell.php
     * @return integer The current position of the stream
     */
    public function stream_tell(): int
    {
        assert($this->stream !== null);

        return $this->stream->tell();
    }

    /**
     * Write bytes to the stream.
     *
     * @see https://php.net/manual/en/streamwrapper.stream-write.php
     * @param string $data Data to write
     * @return integer The number of bytes written
     */
    public function stream_write(string $data): int
    {
        if (! $this->stream instanceof WritableStream) {
            return 0;
        }

        return $this->stream->writeBytes($data);
    }

    /**
     * Returns a stat template with default values.
     */
    private function getStatTemplate(): array
    {
        return [
            // phpcs:disable Squiz.Arrays.ArrayDeclaration.IndexNoNewline
            0  => 0,  'dev'     => 0,
            1  => 0,  'ino'     => 0,
            2  => 0,  'mode'    => 0,
            3  => 0,  'nlink'   => 0,
            4  => 0,  'uid'     => 0,
            5  => 0,  'gid'     => 0,
            6  => -1, 'rdev'    => -1,
            7  => 0,  'size'    => 0,
            8  => 0,  'atime'   => 0,
            9  => 0,  'mtime'   => 0,
            10 => 0,  'ctime'   => 0,
            11 => -1, 'blksize' => -1,
            12 => -1, 'blocks'  => -1,
            // phpcs:enable
        ];
    }

    /**
     * Initialize the protocol from the given path.
     *
     * @see StreamWrapper::stream_open()
     */
    private function initProtocol(string $path): void
    {
        $parts = explode('://', $path, 2);
        $this->protocol = $parts[0] ?: 'gridfs';
    }

    /**
     * Initialize the internal stream for reading.
     *
     * @see StreamWrapper::stream_open()
     */
    private function initReadableStream(): bool
    {
        assert(is_resource($this->context));
        $context = stream_context_get_options($this->context);

        assert($this->protocol !== null);
        $this->stream = new ReadableStream(
            $context[$this->protocol]['collectionWrapper'],
            $context[$this->protocol]['file']
        );

        return true;
    }

    /**
     * Initialize the internal stream for writing.
     *
     * @see StreamWrapper::stream_open()
     */
    private function initWritableStream(): bool
    {
        assert(is_resource($this->context));
        $context = stream_context_get_options($this->context);

        assert($this->protocol !== null);
        $this->stream = new WritableStream(
            $context[$this->protocol]['collectionWrapper'],
            $context[$this->protocol]['filename'],
            $context[$this->protocol]['options']
        );

        return true;
    }
}
