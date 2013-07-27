<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Behat basic functions
 *
 * It does not include MOODLE_INTERNAL because is part of the bootstrap
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../testing/lib.php');

define('BEHAT_EXITCODE_CONFIG', 250);
define('BEHAT_EXITCODE_REQUIREMENT', 251);
define('BEHAT_EXITCODE_PERMISSIONS', 252);
define('BEHAT_EXITCODE_REINSTALL', 253);
define('BEHAT_EXITCODE_INSTALL', 254);
define('BEHAT_EXITCODE_COMPOSER', 255);

/**
 * Exits with an error code
 *
 * @param  mixed $errorcode
 * @param  string $text
 * @return void Stops execution with error code
 */
function behat_error($errorcode, $text = '') {

    // Adding error prefixes.
    switch ($errorcode) {
        case BEHAT_EXITCODE_CONFIG:
            $text = 'Behat config error: ' . $text;
            break;
        case BEHAT_EXITCODE_REQUIREMENT:
            $text = 'Behat requirement not satisfied: ' . $text;
            break;
        case BEHAT_EXITCODE_PERMISSIONS:
            $text = 'Behat permissions problem: ' . $text . ', check the permissions';
            break;
        case BEHAT_EXITCODE_REINSTALL:
            $path = testing_cli_argument_path('/admin/tool/behat/cli/init.php');
            $text = "Reinstall Behat: ".$text.", use:\n php ".$path;
            break;
        case BEHAT_EXITCODE_INSTALL:
            $path = testing_cli_argument_path('/admin/tool/behat/cli/init.php');
            $text = "Install Behat before enabling it, use:\n php ".$path;
            break;
        default:
            $text = 'Unknown error ' . $errorcode . ' ' . $text;
            break;
    }

    testing_error($errorcode, $text);
}

/**
 * PHP errors handler to use when running behat tests.
 *
 * Adds specific CSS classes to identify
 * the messages.
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @param array $errcontext
 * @return bool
 */
function behat_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
    global $OUTPUT;

    // Only after something has been writen.
    if (!$OUTPUT->has_started()) {
        return false;
    }

    // If is preceded by an @ we don't show it.
    if (!error_reporting()) {
        return true;
    }

    // This error handler receives E_ALL | E_STRICT, running the behat test site the debug level is
    // set to DEVELOPER and will always include E_NOTICE,E_USER_NOTICE... as part of E_ALL, if the current
    // error_reporting() value does not include one of those levels is because it has been forced through
    // the moodle code (see fix_utf8() for example) in that cases we respect the forced error level value.
    $respect = array(E_NOTICE, E_USER_NOTICE, E_STRICT, E_WARNING, E_USER_WARNING);
    foreach ($respect as $respectable) {

        // If the current value does not include this kind of errors and the reported error is
        // at that level don't print anything.
        if ($errno == $respectable && !(error_reporting() & $respectable)) {
            return true;
        }
    }

    // Using the default one in case there is a fatal catchable error.
    default_error_handler($errno, $errstr, $errfile, $errline, $errcontext);

    switch ($errno) {
        case E_USER_ERROR:
            $errnostr = 'Fatal error';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $errnostr = 'Warning';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
        case E_STRICT:
            $errnostr = 'Notice';
            break;
        case E_RECOVERABLE_ERROR:
            $errnostr = 'Catchable';
            break;
        default:
            $errnostr = 'Unknown error type';
    }

    // Wrapping the output.
    echo '<div class="phpdebugmessage">' . PHP_EOL;
    echo "$errnostr: $errstr in $errfile on line $errline" . PHP_EOL;
    echo '</div>';

    // Also use the internal error handler so we keep the usual behaviour.
    return false;
}

/**
 * Restrict the config.php settings allowed.
 *
 * When running the behat features the config.php
 * settings should not affect the results.
 *
 * @return void
 */
function behat_clean_init_config() {
    global $CFG;

    $allowed = array_flip(array(
        'wwwroot', 'dataroot', 'dirroot', 'admin', 'directorypermissions', 'filepermissions',
        'dbtype', 'dblibrary', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'prefix', 'dboptions',
        'proxyhost', 'proxyport', 'proxytype', 'proxyuser', 'proxypassword', 'proxybypass'
    ));

    // Also allowing behat_ prefixed attributes.
    foreach ($CFG as $key => $value) {
        if (!isset($allowed[$key]) && strpos($key, 'behat_') !== 0) {
            unset($CFG->{$key});
        }
    }

}
