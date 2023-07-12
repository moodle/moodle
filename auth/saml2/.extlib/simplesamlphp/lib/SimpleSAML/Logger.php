<?php

declare(strict_types=1);

namespace SimpleSAML;

use SimpleSAML\Logger\ErrorLogLoggingHandler;

/**
 * The main logger class for SimpleSAMLphp.
 *
 * @author Lasse Birnbaum Jensen, SDU.
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */
class Logger
{
    /**
     * @var \SimpleSAML\Logger\LoggingHandlerInterface
     */
    private static $loggingHandler;

    /**
     * @var bool
     */
    private static $initializing = false;

    /**
     * @var integer|null
     */
    private static $logLevel = null;

    /**
     * @var boolean
     */
    private static $captureLog = false;

    /**
     * @var array
     */
    private static $capturedLog = [];

    /**
     * Array with messages logged before the logging handler was initialized.
     *
     * @var array
     */
    private static $earlyLog = [];

    /**
     * List of log levels.
     *
     * This list is used to restore the log levels after some log levels have been disabled.
     *
     * @var array
     */
    private static $logLevelStack = [];

    /**
     * The current mask of log levels disabled.
     *
     * Note: this mask is not directly related to the PHP error reporting level.
     *
     * @var int
     */
    private static $logMask = 0;


    /**
     * This constant defines the string we set the track ID to while we are fetching the track ID from the session
     * class. This is used to prevent infinite recursion.
     *
     * @var string
     */
    public const NO_TRACKID = '_NOTRACKIDYET_';

    /**
     * This variable holds the track ID we have retrieved from the session class. It can also be NULL, in which case
     * we haven't fetched the track ID yet, or self::NO_TRACKID, which means that we are fetching the track ID now.
     *
     * @var string
     */
    private static $trackid = self::NO_TRACKID;

    /**
     * This variable holds the format used to log any message. Its use varies depending on the log handler used (for
     * instance, you cannot control here how dates are displayed when using syslog or errorlog handlers), but in
     * general the options are:
     *
     * - %date{<format>}: the date and time, with its format specified inside the brackets. See the PHP documentation
     *   of the strftime() function for more information on the format. If the brackets are omitted, the standard
     *   format is applied. This can be useful if you just want to control the placement of the date, but don't care
     *   about the format.
     *
     * - %process: the name of the SimpleSAMLphp process. Remember you can configure this in the 'logging.processname'
     *   option. The SyslogLoggingHandler will just remove this.
     *
     * - %level: the log level (name or number depending on the handler used). Please note different logging handlers
     *   will print the log level differently.
     *
     * - %stat: if the log entry is intended for statistical purposes, it will print the string 'STAT ' (bear in mind
     *   the trailing space).
     *
     * - %trackid: the track ID, an identifier that allows you to track a single session.
     *
     * - %srcip: the IP address of the client. If you are behind a proxy, make sure to modify the
     *   $_SERVER['REMOTE_ADDR'] variable on your code accordingly to the X-Forwarded-For header.
     *
     * - %msg: the message to be logged.
     *
     * @var string The format of the log line.
     */
    private static $format = '%date{%b %d %H:%M:%S} %process %level %stat[%trackid] %msg';

    /**
     * This variable tells if we have a shutdown function registered or not.
     *
     * @var bool
     */
    private static $shutdownRegistered = false;

    /**
     * This variable tells if we are shutting down.
     *
     * @var bool
     */
    private static $shuttingDown = false;

    /** @var int */
    public const EMERG = 0;

    /** @var int */
    public const ALERT = 1;

    /** @var int */
    public const CRIT = 2;

    /** @var int */
    public const ERR = 3;

    /** @var int */
    public const WARNING = 4;

    /** @var int */
    public const NOTICE = 5;

    /** @var int */
    public const INFO = 6;

    /** @var int */
    public const DEBUG = 7;


    /**
     * Log an emergency message.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function emergency($string)
    {
        self::log(self::EMERG, $string);
    }


    /**
     * Log a critical message.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function critical($string)
    {
        self::log(self::CRIT, $string);
    }


    /**
     * Log an alert.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function alert($string)
    {
        self::log(self::ALERT, $string);
    }


    /**
     * Log an error.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function error($string)
    {
        self::log(self::ERR, $string);
    }


    /**
     * Log a warning.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function warning($string)
    {
        self::log(self::WARNING, $string);
    }


    /**
     * We reserve the notice level for statistics, so do not use this level for other kind of log messages.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function notice($string)
    {
        self::log(self::NOTICE, $string);
    }


    /**
     * Info messages are a bit less verbose than debug messages. This is useful to trace a session.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function info($string)
    {
        self::log(self::INFO, $string);
    }


    /**
     * Debug messages are very verbose, and will contain more information than what is necessary for a production
     * system.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function debug($string)
    {
        self::log(self::DEBUG, $string);
    }


    /**
     * Statistics.
     *
     * @param string $string The message to log.
     * @return void
     */
    public static function stats($string)
    {
        self::log(self::NOTICE, $string, self::$logLevel >= self::NOTICE);
    }


    /**
     * Set the logger to capture logs.
     *
     * @param boolean $val Whether to capture logs or not. Defaults to TRUE.
     * @return void
     */
    public static function setCaptureLog($val = true)
    {
        self::$captureLog = $val;
    }


    /**
     * Get the captured log.
     * @return array
     */
    public static function getCapturedLog()
    {
        return self::$capturedLog;
    }


    /**
     * Set the track identifier to use in all logs.
     *
     * @param string $trackId The track identifier to use during this session.
     * @return void
     */
    public static function setTrackId($trackId)
    {
        self::$trackid = $trackId;
        self::flush();
    }


    /**
     * Flush any pending log messages to the logging handler.
     *
     * @return void
     */
    public static function flush()
    {
        foreach (self::$earlyLog as $msg) {
            self::log($msg['level'], $msg['string'], $msg['statsLog']);
        }
        self::$earlyLog = [];
    }


    /**
     * Flush any pending deferred logs during shutdown.
     *
     * This method is intended to be registered as a shutdown handler, so that any pending messages that weren't sent
     * to the logging handler at that point, can still make it. It is therefore not intended to be called manually.
     *
     * @return void
     */
    public static function shutdown()
    {
        if (self::$trackid === self::NO_TRACKID) {
            try {
                $s = Session::getSessionFromRequest();
            } catch (\Exception $e) {
                // loading session failed. We don't care why, at this point we have a transient session, so we use that
                $s = Session::getSessionFromRequest();
            }
            self::$trackid = $s->getTrackID();
        }
        self::$shuttingDown = true;
        self::flush();
    }


    /**
     * Evaluate whether errors of a certain error level are masked or not.
     *
     * @param int $errno The level of the error to check.
     *
     * @return bool True if the error is masked, false otherwise.
     */
    public static function isErrorMasked($errno)
    {
        return ($errno & self::$logMask) || !($errno & error_reporting());
    }


    /**
     * Disable error reporting for the given log levels.
     *
     * Every call to this function must be followed by a call to popErrorMask().
     *
     * @param int $mask The log levels that should be masked.
     * @return void
     */
    public static function maskErrors($mask)
    {
        assert(is_int($mask));

        $currentEnabled = error_reporting();
        self::$logLevelStack[] = [$currentEnabled, self::$logMask];

        $currentEnabled &= ~$mask;
        error_reporting($currentEnabled);
        self::$logMask |= $mask;
    }


    /**
     * Pop an error mask.
     *
     * This function restores the previous error mask.
     *
     * @return void
     */
    public static function popErrorMask()
    {
        $lastMask = array_pop(self::$logLevelStack);
        error_reporting($lastMask[0]);
        self::$logMask = $lastMask[1];
    }


    /**
     * Defer a message for later logging.
     *
     * @param int     $level The log level corresponding to this message.
     * @param string  $message The message itself to log.
     * @param boolean $stats Whether this is a stats message or a regular one.
     * @return void
     */
    private static function defer(int $level, string $message, bool $stats): void
    {
        // save the message for later
        self::$earlyLog[] = ['level' => $level, 'string' => $message, 'statsLog' => $stats];

        // register a shutdown handler if needed
        if (!self::$shutdownRegistered) {
            register_shutdown_function([self::class, 'shutdown']);
            self::$shutdownRegistered = true;
        }
    }


    /**
     * @param string|null $handler
     * @return void
     * @throws \Exception
     */
    private static function createLoggingHandler(string $handler = null): void
    {
        self::$initializing = true;

        // a set of known logging handlers
        $known_handlers = [
            'syslog'   => 'SimpleSAML\Logger\SyslogLoggingHandler',
            'file'     => 'SimpleSAML\Logger\FileLoggingHandler',
            'errorlog' => 'SimpleSAML\Logger\ErrorLogLoggingHandler',
            'stderr' => 'SimpleSAML\Logger\StandardErrorLoggingHandler',
        ];

        // get the configuration
        $config = Configuration::getInstance();
        assert($config instanceof Configuration);

        // setting minimum log_level
        self::$logLevel = $config->getInteger('logging.level', self::INFO);

        // get the metadata handler option from the configuration
        if (is_null($handler)) {
            $handler = $config->getString('logging.handler', 'syslog');
        }

        if (!array_key_exists($handler, $known_handlers) && class_exists($handler)) {
            if (!in_array('SimpleSAML\Logger\LoggingHandlerInterface', class_implements($handler), true)) {
                throw new \Exception("The logging handler '$handler' is invalid.");
            }
        } else {
            $handler = strtolower($handler);
            if (!array_key_exists($handler, $known_handlers)) {
                throw new \Exception(
                    "Invalid value for the 'logging.handler' configuration option. Unknown handler '" . $handler . "'."
                );
            }
            $handler = $known_handlers[$handler];
        }

        self::$format = $config->getString('logging.format', self::$format);

        try {
            /** @var \SimpleSAML\Logger\LoggingHandlerInterface */
            self::$loggingHandler = new $handler($config);
            self::$loggingHandler->setLogFormat(self::$format);
            self::$initializing = false;
        } catch (\Exception $e) {
            self::$loggingHandler = new ErrorLogLoggingHandler($config);
            self::$initializing = false;
            self::log(self::CRIT, $e->getMessage(), false);
        }
    }


    /**
     * @param int $level
     * @param string $string
     * @param bool $statsLog
     * @return void
     */
    private static function log(int $level, string $string, bool $statsLog = false): void
    {
        if (self::$initializing) {
            // some error occurred while initializing logging
            self::defer($level, $string, $statsLog);
            return;
        } elseif (php_sapi_name() === 'cli' || defined('STDIN')) {
            // we are being executed from the CLI, nowhere to log
            if (!isset(self::$loggingHandler)) {
                self::createLoggingHandler(\SimpleSAML\Logger\StandardErrorLoggingHandler::class);
            }
            $_SERVER['REMOTE_ADDR'] = "CLI";
            if (self::$trackid === self::NO_TRACKID) {
                self::$trackid = 'CL' . bin2hex(openssl_random_pseudo_bytes(4));
            }
        } elseif (!isset(self::$loggingHandler)) {
            // Initialize logging
            self::createLoggingHandler();
        }

        if (self::$captureLog) {
            $sample = microtime(false);
            list($msecs, $mtime) = explode(' ', $sample);

            $time = intval($mtime);
            $usec = substr($msecs, 2, 3);

            $ts = gmdate('H:i:s', $time) . '.' . $usec . 'Z';
            self::$capturedLog[] = $ts . ' ' . $string;
        }

        if (self::$logLevel >= $level || $statsLog) {
            $formats = ['%trackid', '%msg', '%srcip', '%stat'];
            $replacements = [self::$trackid, $string, $_SERVER['REMOTE_ADDR']];

            $stat = '';
            if ($statsLog) {
                $stat = 'STAT ';
            }
            array_push($replacements, $stat);

            if (self::$trackid === self::NO_TRACKID && !self::$shuttingDown) {
                // we have a log without track ID and we are not still shutting down, so defer logging
                self::defer($level, $string, $statsLog);
                return;
            } elseif (self::$trackid === self::NO_TRACKID) {
                // shutting down without a track ID, prettify it
                array_shift($replacements);
                array_unshift($replacements, 'N/A');
            }

            // we either have a track ID or we are shutting down, so just log the message
            $string = str_replace($formats, $replacements, self::$format);
            self::$loggingHandler->log($level, $string);
        }
    }
}
