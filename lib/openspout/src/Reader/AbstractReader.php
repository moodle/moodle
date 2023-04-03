<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\ReaderException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;

/**
 * @template T of SheetIteratorInterface
 *
 * @implements ReaderInterface<T>
 */
abstract class AbstractReader implements ReaderInterface
{
    /** @var bool Indicates whether the stream is currently open */
    private bool $isStreamOpened = false;

    /**
     * Prepares the reader to read the given file. It also makes sure
     * that the file exists and is readable.
     *
     * @param string $filePath Path of the file to be read
     *
     * @throws \OpenSpout\Common\Exception\IOException If the file at the given path does not exist, is not readable or is corrupted
     */
    public function open(string $filePath): void
    {
        if ($this->isStreamWrapper($filePath) && (!$this->doesSupportStreamWrapper() || !$this->isSupportedStreamWrapper($filePath))) {
            throw new IOException("Could not open {$filePath} for reading! Stream wrapper used is not supported for this type of file.");
        }

        if (!$this->isPhpStream($filePath)) {
            // we skip the checks if the provided file path points to a PHP stream
            if (!file_exists($filePath)) {
                throw new IOException("Could not open {$filePath} for reading! File does not exist.");
            }
            if (!is_readable($filePath)) {
                throw new IOException("Could not open {$filePath} for reading! File is not readable.");
            }
        }

        try {
            $fileRealPath = $this->getFileRealPath($filePath);
            $this->openReader($fileRealPath);
            $this->isStreamOpened = true;
        } catch (ReaderException $exception) {
            throw new IOException(
                "Could not open {$filePath} for reading!",
                0,
                $exception
            );
        }
    }

    /**
     * Closes the reader, preventing any additional reading.
     */
    final public function close(): void
    {
        if ($this->isStreamOpened) {
            $this->closeReader();

            $this->isStreamOpened = false;
        }
    }

    /**
     * Returns whether stream wrappers are supported.
     */
    abstract protected function doesSupportStreamWrapper(): bool;

    /**
     * Opens the file at the given file path to make it ready to be read.
     *
     * @param string $filePath Path of the file to be read
     */
    abstract protected function openReader(string $filePath): void;

    /**
     * Closes the reader. To be used after reading the file.
     */
    abstract protected function closeReader(): void;

    final protected function ensureStreamOpened(): void
    {
        if (!$this->isStreamOpened) {
            throw new ReaderNotOpenedException('Reader should be opened first.');
        }
    }

    /**
     * Returns the real path of the given path.
     * If the given path is a valid stream wrapper, returns the path unchanged.
     */
    private function getFileRealPath(string $filePath): string
    {
        if ($this->isSupportedStreamWrapper($filePath)) {
            return $filePath;
        }

        // Need to use realpath to fix "Can't open file" on some Windows setup
        $realpath = realpath($filePath);
        \assert(false !== $realpath);

        return $realpath;
    }

    /**
     * Returns the scheme of the custom stream wrapper, if the path indicates a stream wrapper is used.
     * For example, php://temp => php, s3://path/to/file => s3...
     *
     * @param string $filePath Path of the file to be read
     *
     * @return null|string The stream wrapper scheme or NULL if not a stream wrapper
     */
    private function getStreamWrapperScheme(string $filePath): ?string
    {
        $streamScheme = null;
        if (1 === preg_match('/^(\w+):\/\//', $filePath, $matches)) {
            $streamScheme = $matches[1];
        }

        return $streamScheme;
    }

    /**
     * Checks if the given path is an unsupported stream wrapper
     * (like local path, php://temp, mystream://foo/bar...).
     *
     * @param string $filePath Path of the file to be read
     *
     * @return bool Whether the given path is an unsupported stream wrapper
     */
    private function isStreamWrapper(string $filePath): bool
    {
        return null !== $this->getStreamWrapperScheme($filePath);
    }

    /**
     * Checks if the given path is an supported stream wrapper
     * (like php://temp, mystream://foo/bar...).
     * If the given path is a local path, returns true.
     *
     * @param string $filePath Path of the file to be read
     *
     * @return bool Whether the given path is an supported stream wrapper
     */
    private function isSupportedStreamWrapper(string $filePath): bool
    {
        $streamScheme = $this->getStreamWrapperScheme($filePath);

        return null === $streamScheme || \in_array($streamScheme, stream_get_wrappers(), true);
    }

    /**
     * Checks if a path is a PHP stream (like php://output, php://memory, ...).
     *
     * @param string $filePath Path of the file to be read
     *
     * @return bool Whether the given path maps to a PHP stream
     */
    private function isPhpStream(string $filePath): bool
    {
        $streamScheme = $this->getStreamWrapperScheme($filePath);

        return 'php' === $streamScheme;
    }
}
