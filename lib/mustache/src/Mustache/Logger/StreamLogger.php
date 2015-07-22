<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A Mustache Stream Logger.
 *
 * The Stream Logger wraps a file resource instance (such as a stream) or a
 * stream URL. All log messages over the threshold level will be appended to
 * this stream.
 *
 * Hint: Try `php://stderr` for your stream URL.
 */
class Mustache_Logger_StreamLogger extends Mustache_Logger_AbstractLogger
{
    protected static $levels = array(
        self::DEBUG     => 100,
        self::INFO      => 200,
        self::NOTICE    => 250,
        self::WARNING   => 300,
        self::ERROR     => 400,
        self::CRITICAL  => 500,
        self::ALERT     => 550,
        self::EMERGENCY => 600,
    );

    protected $level;
    protected $stream = null;
    protected $url    = null;

    /**
     * @throws InvalidArgumentException if the logging level is unknown.
     *
     * @param resource|string $stream Resource instance or URL
     * @param int             $level  The minimum logging level at which this handler will be triggered
     */
    public function __construct($stream, $level = Mustache_Logger::ERROR)
    {
        $this->setLevel($level);

        if (is_resource($stream)) {
            $this->stream = $stream;
        } else {
            $this->url = $stream;
        }
    }

    /**
     * Close stream resources.
     */
    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    /**
     * Set the minimum logging level.
     *
     * @throws Mustache_Exception_InvalidArgumentException if the logging level is unknown.
     *
     * @param int $level The minimum logging level which will be written
     */
    public function setLevel($level)
    {
        if (!array_key_exists($level, self::$levels)) {
            throw new Mustache_Exception_InvalidArgumentException(sprintf('Unexpected logging level: %s', $level));
        }

        $this->level = $level;
    }

    /**
     * Get the current minimum logging level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @throws Mustache_Exception_InvalidArgumentException if the logging level is unknown.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        if (!array_key_exists($level, self::$levels)) {
            throw new Mustache_Exception_InvalidArgumentException(sprintf('Unexpected logging level: %s', $level));
        }

        if (self::$levels[$level] >= self::$levels[$this->level]) {
            $this->writeLog($level, $message, $context);
        }
    }

    /**
     * Write a record to the log.
     *
     * @throws Mustache_Exception_LogicException   If neither a stream resource nor url is present.
     * @throws Mustache_Exception_RuntimeException If the stream url cannot be opened.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    protected function writeLog($level, $message, array $context = array())
    {
        if (!is_resource($this->stream)) {
            if (!isset($this->url)) {
                throw new Mustache_Exception_LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }

            $this->stream = fopen($this->url, 'a');
            if (!is_resource($this->stream)) {
                // @codeCoverageIgnoreStart
                throw new Mustache_Exception_RuntimeException(sprintf('The stream or file "%s" could not be opened.', $this->url));
                // @codeCoverageIgnoreEnd
            }
        }

        fwrite($this->stream, self::formatLine($level, $message, $context));
    }

    /**
     * Gets the name of the logging level.
     *
     * @throws InvalidArgumentException if the logging level is unknown.
     *
     * @param int $level
     *
     * @return string
     */
    protected static function getLevelName($level)
    {
        return strtoupper($level);
    }

    /**
     * Format a log line for output.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     *
     * @return string
     */
    protected static function formatLine($level, $message, array $context = array())
    {
        return sprintf(
            "%s: %s\n",
            self::getLevelName($level),
            self::interpolateMessage($message, $context)
        );
    }

    /**
     * Interpolate context values into the message placeholders.
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    protected static function interpolateMessage($message, array $context = array())
    {
        if (strpos($message, '{') === false) {
            return $message;
        }

        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the the message and return
        return strtr($message, $replace);
    }
}
