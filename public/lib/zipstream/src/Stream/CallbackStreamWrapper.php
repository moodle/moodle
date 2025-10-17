<?php

declare(strict_types=1);

namespace ZipStream\Stream;

use RuntimeException;
use Throwable;

/**
 * Stream wrapper that allows writing data to a callback function.
 *
 * This wrapper creates a virtual stream that forwards all written data
 * to a provided callback function, enabling custom output handling
 * such as streaming to HTTP responses, files, or other destinations.
 *
 * @psalm-suppress UnusedClass Used dynamically through stream_wrapper_register
 */
final class CallbackStreamWrapper
{
    public const PROTOCOL = 'zipcb';

    /** @var resource|null */
    public $context;

    /** @var array<string, callable(string):void> Map of stream IDs to callback functions */
    private static array $callbacks = [];

    /** @var string|null Unique identifier for this stream instance */
    private ?string $id = null;

    /** @var int Current position in the stream */
    private int $pos = 0;

    /**
     * Destructor - ensures cleanup even if stream_close() isn't called.
     * Prevents memory leaks in long-running processes.
     */
    public function __destruct()
    {
        $this->stream_close();
    }

    /**
     * Create a new callback stream.
     *
     * @param callable(string):void $callback Function to call with written data
     * @return resource|false Stream resource or false on failure
     */
    public static function open(callable $callback)
    {
        if (!in_array(self::PROTOCOL, stream_get_wrappers(), true)) {
            if (!stream_wrapper_register(self::PROTOCOL, self::class)) {
                return false;
            }
        }

        // Generate cryptographically secure unique ID to prevent collisions
        $id = 'cb_' . bin2hex(random_bytes(16));
        self::$callbacks[$id] = $callback;

        return fopen(self::PROTOCOL . "://{$id}", 'wb');
    }

    /**
     * Clean up all registered callbacks (useful for testing).
     *
     * @internal
     */
    public static function cleanup(): void
    {
        self::$callbacks = [];
    }

    /**
     * Open the stream.
     *
     * @param string $path Stream path containing the callback ID
     * @param string $mode File mode (must contain 'w' for writing)
     * @param int $options Stream options (required by interface, unused)
     * @param string|null $opened_path Opened path reference (required by interface, unused)
     * @return bool True if stream opened successfully
     * @psalm-suppress UnusedParam $options and $opened_path are required by the stream wrapper interface
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        if (!str_contains($mode, 'w')) {
            return false;
        }

        $host = parse_url($path, PHP_URL_HOST);
        if ($host === false || $host === null) {
            return false;
        }

        $this->id = $host;
        return isset(self::$callbacks[$this->id]);
    }

    /**
     * Write data to the callback.
     *
     * @param string $data Data to write
     * @return int Number of bytes written
     * @throws RuntimeException If callback execution fails
     */
    public function stream_write(string $data): int
    {
        if ($this->id === null) {
            trigger_error('Stream not properly initialized', E_USER_WARNING);
            return 0;
        }

        $callback = self::$callbacks[$this->id] ?? null;
        if ($callback === null) {
            trigger_error('Callback not found for stream', E_USER_WARNING);
            return 0;
        }

        try {
            $callback($data);
        } catch (Throwable $e) {
            throw new RuntimeException(
                'Callback function failed during stream write: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $length = strlen($data);
        $this->pos += $length;
        return $length;
    }

    /**
     * Get current position in stream.
     *
     * @return int Current position
     */
    public function stream_tell(): int
    {
        return $this->pos;
    }

    /**
     * Check if stream has reached end of file.
     *
     * @return bool Always false for write-only streams
     */
    public function stream_eof(): bool
    {
        return false;
    }

    /**
     * Flush stream buffers.
     *
     * @return bool Always true (no buffering)
     */
    public function stream_flush(): bool
    {
        return true;
    }

    /**
     * Close the stream and clean up callback.
     */
    public function stream_close(): void
    {
        if ($this->id !== null) {
            unset(self::$callbacks[$this->id]);
            $this->id = null;
        }
    }

    /**
     * Get stream statistics.
     *
     * @return array<string, mixed> Stream statistics
     */
    public function stream_stat(): array
    {
        return [
            'dev' => 0,
            'ino' => 0,
            'mode' => 0o100666, // Regular file, read/write permissions
            'nlink' => 1,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => $this->pos,
            'atime' => time(),
            'mtime' => time(),
            'ctime' => time(),
            'blksize' => 4096,
            'blocks' => ceil($this->pos / 4096),
        ];
    }

    /**
     * Read data from stream (not supported - write-only stream).
     *
     * @param int $count Number of bytes to read (required by interface, unused)
     * @return string Always empty string
     * @psalm-suppress UnusedParam $count is required by the stream wrapper interface
     */
    public function stream_read(int $count): string
    {
        trigger_error('Read operations not supported on callback streams', E_USER_WARNING);
        return '';
    }

    /**
     * Seek to position in stream (not supported).
     *
     * @param int $offset Offset to seek to (required by interface, unused)
     * @param int $whence Seek mode (required by interface, unused)
     * @return bool Always false
     * @psalm-suppress UnusedParam $offset and $whence are required by the stream wrapper interface
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        trigger_error('Seek operations not supported on callback streams', E_USER_WARNING);
        return false;
    }

    /**
     * Set options on stream (not supported).
     *
     * @param int $option Option to set (required by interface, unused)
     * @param int $arg1 First argument (required by interface, unused)
     * @param int $arg2 Second argument (required by interface, unused)
     * @return bool Always false
     * @psalm-suppress UnusedParam All parameters are required by the stream wrapper interface
     */
    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        return false;
    }

    /**
     * Truncate stream (not supported).
     *
     * @param int $new_size New size (required by interface, unused)
     * @return bool Always false
     * @psalm-suppress UnusedParam $new_size is required by the stream wrapper interface
     */
    public function stream_truncate(int $new_size): bool
    {
        trigger_error('Truncate operations not supported on callback streams', E_USER_WARNING);
        return false;
    }
}
