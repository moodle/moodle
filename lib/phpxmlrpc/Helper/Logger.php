<?php

namespace PhpXmlRpc\Helper;

/**
 * @todo implement an interface
 * @todo make constructor private to force users to go through `instance()` ?
 */
class Logger
{
    protected static $instance = null;

    /**
     * This class can be used as singleton, so that later we can move to DI patterns (ish...)
     *
     * @return Logger
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // *** Implement the same interface as PSR/LOG, for the sake of interoperability ***

    /**
     * NB: unlike other "traditional" loggers, this one echoes to screen the debug messages instead of logging them.
     *
     * @param string $message
     * @param array $context known key: 'encoding'
     * @return void
     */
    public function debug($message, $context = array())
    {
        if (isset($context['encoding'])) {
            $this->debugMessage($message, $context['encoding']);
        } else {
            $this->debugMessage($message);
        }
    }

    /**
     * Following the general principle of 'never break stdout', the default behaviour
     *
     * @param string $message
     * @param $context
     * @return void
     */
    public function warning($message, $context = array())
    {
        $this->errorLog(preg_replace('/^XML-RPC :/', 'XML-RPC Warning: ', $message));
    }

    /**
     * Triggers the writing of a message to php's error log
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, $context = array())
    {
        $this->errorLog(preg_replace('/^XML-RPC :/', 'XML-RPC Error: ', $message));
    }

    // BC interface

    /**
     * Echoes a debug message, taking care of escaping it when not in console mode.
     * NB: if the encoding of the message is not known or wrong, and we are working in web mode, there is no guarantee
     *     of 100% accuracy, which kind of defeats the purpose of debugging
     *
     * @param string $message
     * @param string $encoding deprecated
     * @return void
     *
     * @internal left in purely for BC
     */
    public function debugMessage($message, $encoding = null)
    {
        // US-ASCII is a warning for PHP and a fatal for HHVM
        if ($encoding == 'US-ASCII') {
            $encoding = 'UTF-8';
        }

        if (PHP_SAPI != 'cli') {
            $flags = ENT_COMPAT;
            // avoid warnings on php < 5.4...
            if (defined('ENT_HTML401')) {
                $flags =  $flags | ENT_HTML401;
            }
            if (defined('ENT_SUBSTITUTE')) {
                $flags =  $flags | ENT_SUBSTITUTE;
            }
            if ($encoding != null) {
                print "<PRE>\n".htmlentities($message, $flags, $encoding)."\n</PRE>";
            } else {
                print "<PRE>\n".htmlentities($message, $flags)."\n</PRE>";
            }
        } else {
            print "\n$message\n";
        }

        // let the user see this now in case there's a time-out later...
        flush();
    }

    /**
     * Writes a message to the error log.
     *
     * @param string $message
     * @return void
     *
     * @internal left in purely for BC
     */
    public function errorLog($message)
    {
        error_log($message);
    }
}
