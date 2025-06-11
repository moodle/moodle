<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser;

/**
 * A stream reader class
 */
class StreamReader
{
    /**
     * Creates a stream reader instance by a string value.
     *
     * @param string $content
     * @param int $maxMemory
     * @return StreamReader
     */
    public static function createByString($content, $maxMemory = 2097152)
    {
        $h = \fopen('php://temp/maxmemory:' . ((int) $maxMemory), 'r+b');
        \fwrite($h, $content);
        \rewind($h);

        return new self($h, true);
    }

    /**
     * Creates a stream reader instance by a filename.
     *
     * @param string $filename
     * @return StreamReader
     */
    public static function createByFile($filename)
    {
        $h = \fopen($filename, 'rb');
        return new self($h, true);
    }

    /**
     * Defines whether the stream should be closed when the stream reader instance is deconstructed or not.
     *
     * @var bool
     */
    protected $closeStream;

    /**
     * The stream resource.
     *
     * @var resource
     */
    protected $stream;

    /**
     * The byte-offset position in the stream.
     *
     * @var int
     */
    protected $position;

    /**
     * The byte-offset position in the buffer.
     *
     * @var int
     */
    protected $offset;

    /**
     * The buffer length.
     *
     * @var int
     */
    protected $bufferLength;

    /**
     * The total length of the stream.
     *
     * @var int
     */
    protected $totalLength;

    /**
     * The buffer.
     *
     * @var string
     */
    protected $buffer;

    /**
     * StreamReader constructor.
     *
     * @param resource $stream
     * @param bool $closeStream Defines whether to close the stream resource if the instance is destructed or not.
     */
    public function __construct($stream, $closeStream = false)
    {
        if (!\is_resource($stream)) {
            throw new \InvalidArgumentException(
                'No stream given.'
            );
        }

        $metaData = \stream_get_meta_data($stream);
        if (!$metaData['seekable']) {
            throw new \InvalidArgumentException(
                'Given stream is not seekable!'
            );
        }

        if (fseek($stream, 0) === -1) {
            throw new \InvalidArgumentException(
                'Given stream is not seekable!'
            );
        }

        $this->stream = $stream;
        $this->closeStream = $closeStream;
        $this->reset();
    }

    /**
     * The destructor.
     */
    public function __destruct()
    {
        $this->cleanUp();
    }

    /**
     * Closes the file handle.
     */
    public function cleanUp()
    {
        if ($this->closeStream && is_resource($this->stream)) {
            \fclose($this->stream);
        }
    }

    /**
     * Returns the byte length of the buffer.
     *
     * @param bool $atOffset
     * @return int
     */
    public function getBufferLength($atOffset = false)
    {
        if ($atOffset === false) {
            return $this->bufferLength;
        }

        return $this->bufferLength - $this->offset;
    }

    /**
     * Get the current position in the stream.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns the current buffer.
     *
     * @param bool $atOffset
     * @return string
     */
    public function getBuffer($atOffset = true)
    {
        if ($atOffset === false) {
            return $this->buffer;
        }

        $string = \substr($this->buffer, $this->offset);

        return (string) $string;
    }

    /**
     * Gets a byte at a specific position in the buffer.
     *
     * If the position is invalid the method will return false.
     *
     * If the $position parameter is set to null the value of $this->offset will be used.
     *
     * @param int|null $position
     * @return string|bool
     */
    public function getByte($position = null)
    {
        $position = (int) ($position !== null ? $position : $this->offset);
        if (
            $position >= $this->bufferLength
            && (!$this->increaseLength() || $position >= $this->bufferLength)
        ) {
            return false;
        }

        return $this->buffer[$position];
    }

    /**
     * Returns a byte at a specific position, and set the offset to the next byte position.
     *
     * If the position is invalid the method will return false.
     *
     * If the $position parameter is set to null the value of $this->offset will be used.
     *
     * @param int|null $position
     * @return string|bool
     */
    public function readByte($position = null)
    {
        if ($position !== null) {
            $position = (int) $position;
            // check if needed bytes are available in the current buffer
            if (!($position >= $this->position && $position < $this->position + $this->bufferLength)) {
                $this->reset($position);
                $offset = $this->offset;
            } else {
                $offset = $position - $this->position;
            }
        } else {
            $offset = $this->offset;
        }

        if (
            $offset >= $this->bufferLength
            && ((!$this->increaseLength()) || $offset >= $this->bufferLength)
        ) {
            return false;
        }

        $this->offset = $offset + 1;
        return $this->buffer[$offset];
    }

    /**
     * Read bytes from the current or a specific offset position and set the internal pointer to the next byte.
     *
     * If the position is invalid the method will return false.
     *
     * If the $position parameter is set to null the value of $this->offset will be used.
     *
     * @param int $length
     * @param int|null $position
     * @return string|false
     */
    public function readBytes($length, $position = null)
    {
        $length = (int) $length;
        if ($position !== null) {
            // check if needed bytes are available in the current buffer
            if (!($position >= $this->position && $position < $this->position + $this->bufferLength)) {
                $this->reset($position, $length);
                $offset = $this->offset;
            } else {
                $offset = $position - $this->position;
            }
        } else {
            $offset = $this->offset;
        }

        if (
            ($offset + $length) > $this->bufferLength
            && ((!$this->increaseLength($length)) || ($offset + $length) > $this->bufferLength)
        ) {
            return false;
        }

        $bytes = \substr($this->buffer, $offset, $length);
        $this->offset = $offset + $length;

        return $bytes;
    }

    /**
     * Read a line from the current position.
     *
     * @param int $length
     * @return string|bool
     */
    public function readLine($length = 1024)
    {
        if ($this->ensureContent() === false) {
            return false;
        }

        $line = '';
        while ($this->ensureContent()) {
            $char = $this->readByte();

            if ($char === "\n") {
                break;
            }

            if ($char === "\r") {
                if ($this->getByte() === "\n") {
                    $this->addOffset(1);
                }
                break;
            }

            $line .= $char;

            if (\strlen($line) >= $length) {
                break;
            }
        }

        return $line;
    }

    /**
     * Set the offset position in the current buffer.
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        if ($offset > $this->bufferLength || $offset < 0) {
            throw new \OutOfRangeException(
                \sprintf('Offset (%s) out of range (length: %s)', $offset, $this->bufferLength)
            );
        }

        $this->offset = (int) $offset;
    }

    /**
     * Returns the current offset in the current buffer.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Add an offset to the current offset.
     *
     * @param int $offset
     */
    public function addOffset($offset)
    {
        $this->setOffset($this->offset + $offset);
    }

    /**
     * Make sure that there is at least one character beyond the current offset in the buffer.
     *
     * @return bool
     */
    public function ensureContent()
    {
        while ($this->offset >= $this->bufferLength) {
            if (!$this->increaseLength()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the stream.
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Gets the total available length.
     *
     * @return int
     */
    public function getTotalLength()
    {
        if ($this->totalLength === null) {
            $stat = \fstat($this->stream);
            $this->totalLength = $stat['size'];
        }

        return $this->totalLength;
    }

    /**
     * Resets the buffer to a position and re-read the buffer with the given length.
     *
     * If the $pos parameter is negative the start buffer position will be the $pos'th position from
     * the end of the file.
     *
     * If the $pos parameter is negative and the absolute value is bigger then the totalLength of
     * the file $pos will set to zero.
     *
     * @param int|null $pos Start position of the new buffer
     * @param int $length Length of the new buffer. Mustn't be negative
     */
    public function reset($pos = 0, $length = 200)
    {
        if ($pos === null) {
            $pos = $this->position + $this->offset;
        } elseif ($pos < 0) {
            $pos = \max(0, $this->getTotalLength() + $pos);
        }

        \fseek($this->stream, $pos);

        $this->position = $pos;
        $this->buffer = $length > 0 ? \fread($this->stream, $length) : '';
        $this->bufferLength = \strlen($this->buffer);
        $this->offset = 0;

        // If a stream wrapper is in use it is possible that
        // length values > 8096 will be ignored, so use the
        // increaseLength()-method to correct that behavior
        if ($this->bufferLength < $length && $this->increaseLength($length - $this->bufferLength)) {
            // increaseLength parameter is $minLength, so cut to have only the required bytes in the buffer
            $this->buffer = \substr($this->buffer, 0, $length);
            $this->bufferLength = \strlen($this->buffer);
        }
    }

    /**
     * Ensures bytes in the buffer with a specific length and location in the file.
     *
     * @param int $pos
     * @param int $length
     * @see reset()
     */
    public function ensure($pos, $length)
    {
        if (
            $pos >= $this->position
            && $pos < ($this->position + $this->bufferLength)
            && ($this->position + $this->bufferLength) >= ($pos + $length)
        ) {
            $this->offset = $pos - $this->position;
        } else {
            $this->reset($pos, $length);
        }
    }

    /**
     * Forcefully read more data into the buffer.
     *
     * @param int $minLength
     * @return bool Returns false if the stream reaches the end
     */
    public function increaseLength($minLength = 100)
    {
        $length = \max($minLength, 100);

        if (\feof($this->stream) || $this->getTotalLength() === $this->position + $this->bufferLength) {
            return false;
        }

        $newLength = $this->bufferLength + $length;
        do {
            $this->buffer .= \fread($this->stream, $newLength - $this->bufferLength);
            $this->bufferLength = \strlen($this->buffer);
        } while (($this->bufferLength !== $newLength) && !\feof($this->stream));

        return true;
    }
}
