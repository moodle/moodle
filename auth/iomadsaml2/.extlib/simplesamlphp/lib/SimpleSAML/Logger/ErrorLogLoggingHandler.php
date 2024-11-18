<?php

declare(strict_types=1);

namespace SimpleSAML\Logger;

use SimpleSAML\Configuration;
use SimpleSAML\Logger;

/**
 * A class for logging to the default php error log.
 *
 * @author Lasse Birnbaum Jensen, SDU.
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */
class ErrorLogLoggingHandler implements LoggingHandlerInterface
{
    /**
     * This array contains the mappings from syslog log level to names.
     *
     * @var array
     */
    private static $levelNames = [
        Logger::EMERG   => 'EMERG',
        Logger::ALERT   => 'ALERT',
        Logger::CRIT    => 'CRIT',
        Logger::ERR     => 'ERR',
        Logger::WARNING => 'WARNING',
        Logger::NOTICE  => 'NOTICE',
        Logger::INFO    => 'INFO',
        Logger::DEBUG   => 'DEBUG',
    ];

    /**
     * The name of this process.
     *
     * @var string
     */
    private $processname;


    /**
     * ErrorLogLoggingHandler constructor.
     *
     * @param \SimpleSAML\Configuration $config The configuration object for this handler.
     */
    public function __construct(Configuration $config)
    {
        // Remove any non-printable characters before storing
        $this->processname = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $config->getString('logging.processname', 'SimpleSAMLphp'));
    }


    /**
     * Set the format desired for the logs.
     *
     * @param string $format The format used for logs.
     * @return void
     */
    public function setLogFormat($format)
    {
        // we don't need the format here
    }


    /**
     * Log a message to syslog.
     *
     * @param int $level The log level.
     * @param string $string The formatted message to log.
     * @return void
     */
    public function log($level, $string)
    {
        if (array_key_exists($level, self::$levelNames)) {
            $levelName = self::$levelNames[$level];
        } else {
            $levelName = sprintf('UNKNOWN%d', $level);
        }

        $formats = ['%process', '%level'];
        $replacements = [$this->processname, $levelName];
        $string = str_replace($formats, $replacements, $string);
        $string = trim($string);

        error_log($string);
    }
}
