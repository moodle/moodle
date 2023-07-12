<?php

// initialize the autoloader
require_once(dirname(dirname(__FILE__)) . '/lib/_autoload.php');

// enable assertion handler for all pages
\SimpleSAML\Error\Assertion::installHandler();

// show error page on unhandled exceptions
function SimpleSAML_exception_handler($exception)
{
    \SimpleSAML\Module::callHooks('exception_handler', $exception);

    if ($exception instanceof \SimpleSAML\Error\Error) {
        $exception->show();
    } elseif ($exception instanceof \Exception) {
        $e = new \SimpleSAML\Error\Error('UNHANDLEDEXCEPTION', $exception);
        $e->show();
    } elseif (class_exists('Error') && $exception instanceof \Error) {
        $e = new \SimpleSAML\Error\Error('UNHANDLEDEXCEPTION', $exception);
        $e->show();
    }
}

set_exception_handler('SimpleSAML_exception_handler');

// log full backtrace on errors and warnings
function SimpleSAML_error_handler($errno, $errstr, $errfile = null, $errline = 0, $errcontext = null)
{
    if (\SimpleSAML\Logger::isErrorMasked($errno)) {
        // masked error
        return false;
    }

    static $limit = 5;
    $limit -= 1;
    if ($limit < 0) {
        // we have reached the limit in the number of backtraces we will log
        return false;
    }

    // show an error with a full backtrace
    $context = (is_null($errfile) ? '' : " at $errfile:$errline");
    $e = new \SimpleSAML\Error\Exception('Error ' . $errno . ' - ' . $errstr . $context);
    $e->logError();

    // resume normal error processing
    return false;
}

set_error_handler('SimpleSAML_error_handler');

try {
    \SimpleSAML\Configuration::getInstance();
} catch (\Exception $e) {
    throw new \SimpleSAML\Error\CriticalConfigurationError(
        $e->getMessage()
    );
}

// set the timezone
\SimpleSAML\Utils\Time::initTimezone();
