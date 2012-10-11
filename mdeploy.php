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
 * Moodle deployment utility
 *
 * This script looks after deploying available updates to the local Moodle site.
 *
 * CLI usage example:
 *  $ sudo -u apache php mdeploy.php --upgrade \
 *                                   --package=https://moodle.org/plugins/download.php/...zip \
 *                                   --dataroot=/home/mudrd8mz/moodledata/moodle24
 *
 * @package     core
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (defined('MOODLE_INTERNAL')) {
    die('This is a standalone utility that should not be included by any other Moodle code.');
}


// Exceptions //////////////////////////////////////////////////////////////////

class invalid_coding_exception extends Exception {}
class missing_option_exception extends Exception {}
class invalid_option_exception extends Exception {}
class unauthorized_access_exception extends Exception {}
class download_file_exception extends Exception {}


// Various support classes /////////////////////////////////////////////////////

/**
 * Base class implementing the singleton pattern using late static binding feature.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class singleton_pattern {

    /** @var array singleton_pattern instances */
    protected static $singletoninstances = array();

    /**
     * Factory method returning the singleton instance.
     *
     * Subclasses may want to override the {@link self::initialize()} method that is
     * called right after their instantiation.
     *
     * @return mixed the singleton instance
     */
    final public static function instance() {
        $class = get_called_class();
        if (!isset(static::$singletoninstances[$class])) {
            static::$singletoninstances[$class] = new static();
            static::$singletoninstances[$class]->initialize();
        }
        return static::$singletoninstances[$class];
    }

    /**
     * Optional post-instantiation code.
     */
    protected function initialize() {
        // Do nothing in this base class.
    }

    /**
     * Direct instantiation not allowed, use the factory method {@link instance()}
     */
    final protected function __construct() {
    }

    /**
     * Sorry, this is singleton.
     */
    final protected function __clone() {
    }
}


// User input handling /////////////////////////////////////////////////////////

/**
 * Provides access to the script options.
 *
 * Implements the delegate pattern by dispatching the calls to appropriate
 * helper class (CLI or HTTP).
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class input_manager extends singleton_pattern {

    const TYPE_FILE         = 'file';   // File name
    const TYPE_FLAG         = 'flag';   // No value, just a flag (switch)
    const TYPE_INT          = 'int';    // Integer
    const TYPE_PATH         = 'path';   // Full path to a file or a directory
    const TYPE_RAW          = 'raw';    // Raw value, keep as is
    const TYPE_URL          = 'url';    // URL to a file

    /** @var input_cli_provider|input_http_provider the provider of the input */
    protected $inputprovider = null;

    /**
     * Returns the value of an option passed to the script.
     *
     * If the caller passes just the $name, the requested argument is considered
     * required. The caller may specify the second argument which then
     * makes the argument optional with the given default value.
     *
     * If the type of the $name option is TYPE_FLAG (switch), this method returns
     * true if the flag has been passed or false if it was not. Specifying the
     * default value makes no sense in this case and leads to invalid coding exception.
     *
     * The array options are not supported.
     *
     * @example $filename = $input->get_option('f');
     * @example $filename = $input->get_option('filename');
     * @example if ($input->get_option('verbose')) { ... }
     * @param string $name
     * @return mixed
     */
    public function get_option($name, $default = 'provide_default_value_explicitly') {

        $this->validate_option_name($name);

        $info = $this->get_option_info($name);

        if ($info->type === input_manager::TYPE_FLAG) {
            return $this->inputprovider->has_option($name);
        }

        if (func_num_args() == 1) {
            return $this->get_required_option($name);
        } else {
            return $this->get_optional_option($name, $default);
        }
    }

    /**
     * Returns the meta-information about the given option.
     *
     * @param string|null $name short or long option name, defaults to returning the list of all
     * @return array|object|false array with all, object with the specific option meta-information or false of no such an option
     */
    public function get_option_info($name=null) {

        $supportedoptions = array(
            array('', 'passfile', input_manager::TYPE_FILE, 'File name of the passphrase file (HTTP access only)'),
            array('', 'password', input_manager::TYPE_RAW, 'Session passphrase (HTTP access only)'),
            array('d', 'dataroot', input_manager::TYPE_PATH, 'Full path to the dataroot (moodledata) directory'),
            array('h', 'help', input_manager::TYPE_FLAG, 'Prints usage information'),
            array('i', 'install', input_manager::TYPE_FLAG, 'Installation mode'),
            array('p', 'package', input_manager::TYPE_URL, 'URL to the ZIP package to deploy'),
            array('u', 'upgrade', input_manager::TYPE_FLAG, 'Upgrade mode'),
        );

        if (is_null($name)) {
            $all = array();
            foreach ($supportedoptions as $optioninfo) {
                $info = new stdClass();
                $info->shortname = $optioninfo[0];
                $info->longname = $optioninfo[1];
                $info->type = $optioninfo[2];
                $info->desc = $optioninfo[3];
                $all[] = $info;
            }
            return $all;
        }

        $found = false;

        foreach ($supportedoptions as $optioninfo) {
            if (strlen($name) == 1) {
                // Search by the short option name
                if ($optioninfo[0] === $name) {
                    $found = $optioninfo;
                    break;
                }
            } else {
                // Search by the long option name
                if ($optioninfo[1] === $name) {
                    $found = $optioninfo;
                    break;
                }
            }
        }

        if (!$found) {
            return false;
        }

        $info = new stdClass();
        $info->shortname = $found[0];
        $info->longname = $found[1];
        $info->type = $found[2];
        $info->desc = $found[3];

        return $info;
    }

    /**
     * Casts the value to the given type.
     *
     * @param mixed $raw the raw value
     * @param string $type the expected value type, e.g. {@link input_manager::TYPE_INT}
     * @return mixed
     */
    public function cast_value($raw, $type) {

        if (is_array($raw)) {
            throw new invalid_coding_exception('Unsupported array option.');
        } else if (is_object($raw)) {
            throw new invalid_coding_exception('Unsupported object option.');
        }

        switch ($type) {

            case input_manager::TYPE_FILE:
                $raw = preg_replace('~[[:cntrl:]]|[&<>"`\|\':\\\\/]~u', '', $raw);
                $raw = preg_replace('~\.\.+~', '', $raw);
                if ($raw === '.') {
                    $raw = '';
                }
                return $raw;

            case input_manager::TYPE_FLAG:
                return true;

            case input_manager::TYPE_INT:
                return (int)$raw;

            case input_manager::TYPE_PATH:
                $raw = str_replace('\\', '/', $raw);
                $raw = preg_replace('~[[:cntrl:]]|[&<>"`\|\':]~u', '', $raw);
                $raw = preg_replace('~\.\.+~', '', $raw);
                $raw = preg_replace('~//+~', '/', $raw);
                $raw = preg_replace('~/(\./)+~', '/', $raw);
                return $raw;

            case input_manager::TYPE_RAW:
                return $raw;

            case input_manager::TYPE_URL:
                $regex  = '^(https?|ftp)\:\/\/'; // protocol
                $regex .= '([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?'; // optional user and password
                $regex .= '[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*'; // hostname or IP (one word like http://localhost/ allowed)
                $regex .= '(\:[0-9]{2,5})?'; // port (optional)
                $regex .= '(\/([a-z0-9+\$_-]\.?)+)*\/?'; // path to the file
                $regex .= '(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?'; // HTTP params

                if (preg_match('#'.$regex.'#i', $raw)) {
                    return $raw;
                } else {
                    return '';
                }

            default:
                throw new invalid_coding_exception('Unknown option type.');

        }
    }

    /**
     * Picks the appropriate helper class to delegate calls to.
     */
    protected function initialize() {
        if (PHP_SAPI === 'cli') {
            $this->inputprovider = input_cli_provider::instance();
        } else {
            $this->inputprovider = input_http_provider::instance();
        }
    }

    // End of external API

    /**
     * Validates the parameter name.
     *
     * @param string $name
     * @throws invalid_coding_exception
     */
    protected function validate_option_name($name) {

        if (empty($name)) {
            throw new invalid_coding_exception('Invalid empty option name.');
        }

        $meta = $this->get_option_info($name);
        if (empty($meta)) {
            throw new invalid_coding_exception('Invalid option name: '.$name);
        }
    }

    /**
     * Returns cleaned option value or throws exception.
     *
     * @param string $name the name of the parameter
     * @param string $type the parameter type, e.g. {@link input_manager::TYPE_INT}
     * @return mixed
     */
    protected function get_required_option($name) {
        if ($this->inputprovider->has_option($name)) {
            return $this->inputprovider->get_option($name);
        } else {
            throw new missing_option_exception('Missing required option: '.$name);
        }
    }

    /**
     * Returns cleaned option value or the default value
     *
     * @param string $name the name of the parameter
     * @param string $type the parameter type, e.g. {@link input_manager::TYPE_INT}
     * @param mixed $default the default value.
     * @return mixed
     */
    protected function get_optional_option($name, $default) {
        if ($this->inputprovider->has_option($name)) {
            return $this->inputprovider->get_option($name);
        } else {
            return $default;
        }
    }
}


/**
 * Base class for input providers.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class input_provider extends singleton_pattern {

    /** @var array list of all passed valid options */
    protected $options = array();

    /**
     * Returns the casted value of the option.
     *
     * @param string $name option name
     * @throws invalid_coding_exception if the option has not been passed
     * @return mixed casted value of the option
     */
    public function get_option($name) {

        if (!$this->has_option($name)) {
            throw new invalid_coding_exception('Option not passed: '.$name);
        }

        return $this->options[$name];
    }

    /**
     * Was the given option passed?
     *
     * @param string $name optionname
     * @return bool
     */
    public function has_option($name) {
        return array_key_exists($name, $this->options);
    }

    /**
     * Initializes the input provider.
     */
    protected function initialize() {
        $this->populate_options();
    }

    // End of external API

    /**
     * Parses and validates all supported options passed to the script.
     */
    protected function populate_options() {

        $input = input_manager::instance();
        $raw = $this->parse_raw_options();
        $cooked = array();

        foreach ($raw as $k => $v) {
            if (is_array($v) or is_object($v)) {
                // Not supported.
            }

            $info = $input->get_option_info($k);
            if (!$info) {
                continue;
            }

            $casted = $input->cast_value($v, $info->type);

            if (!empty($info->shortname)) {
                $cooked[$info->shortname] = $casted;
            }

            if (!empty($info->longname)) {
                $cooked[$info->longname] = $casted;
            }
        }

        // Store the options.
        $this->options = $cooked;
    }
}


/**
 * Provides access to the script options passed via CLI.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class input_cli_provider extends input_provider {

    /**
     * Parses raw options passed to the script.
     *
     * @return array as returned by getopt()
     */
    protected function parse_raw_options() {

        $input = input_manager::instance();

        // Signatures of some in-built PHP functions are just crazy, aren't they.
        $short = '';
        $long = array();

        foreach ($input->get_option_info() as $option) {
            if ($option->type === input_manager::TYPE_FLAG) {
                // No value expected for this option.
                $short .= $option->shortname;
                $long[] = $option->longname;
            } else {
                // A value expected for the option, all considered as optional.
                $short .= empty($option->shortname) ? '' : $option->shortname.'::';
                $long[] = empty($option->longname) ? '' : $option->longname.'::';
            }
        }

        return getopt($short, $long);
    }
}


/**
 * Provides access to the script options passed via HTTP request.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class input_http_provider extends input_provider {

    /**
     * Parses raw options passed to the script.
     *
     * @return array of raw values passed via HTTP request
     */
    protected function parse_raw_options() {
        return $_POST;
    }
}


// Output handling /////////////////////////////////////////////////////////////

/**
 * Provides output operations.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class output_manager extends singleton_pattern {

    /** @var output_cli_provider|output_http_provider the provider of the output functionality */
    protected $outputprovider = null;

    /**
     * Magic method triggered when invoking an inaccessible method.
     *
     * @param string $name method name
     * @param array $arguments method arguments
     */
    public function __call($name, array $arguments = array()) {
        call_user_func_array(array($this->outputprovider, $name), $arguments);
    }

    /**
     * Picks the appropriate helper class to delegate calls to.
     */
    protected function initialize() {
        if (PHP_SAPI === 'cli') {
            $this->outputprovider = output_cli_provider::instance();
        } else {
            $this->outputprovider = output_http_provider::instance();
        }
    }
}


/**
 * Base class for all output providers.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class output_provider extends singleton_pattern {
}

/**
 * Provides output to the command line.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class output_cli_provider extends output_provider {

    /**
     * Prints help information in CLI mode.
     */
    public function help() {

        $this->outln('mdeploy.php - Moodle (http://moodle.org) deployment utility');
        $this->outln();
        $this->outln('Usage: $ sudo -u apache php mdeploy.php [options]');
        $this->outln();
        $input = input_manager::instance();
        foreach($input->get_option_info() as $info) {
            $option = array();
            if (!empty($info->shortname)) {
                $option[] = '-'.$info->shortname;
            }
            if (!empty($info->longname)) {
                $option[] = '--'.$info->longname;
            }
            $this->outln(sprintf('%-20s %s', implode(', ', $option), $info->desc));
        }
    }

    // End of external API

    /**
     * Writes a text to the STDOUT followed by a new line character.
     *
     * @param string $text text to print
     */
    protected function outln($text='') {
        fputs(STDOUT, $text.PHP_EOL);
    }
}


/**
 * Provides HTML output as a part of HTTP response.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class output_http_provider extends output_provider {

    /**
     * Prints help on the script usage.
     */
    public function help() {
        // No help available via HTTP
    }
}

// The main class providing all the functionality //////////////////////////////

/**
 * The actual worker class implementing the main functionality of the script.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class worker extends singleton_pattern {

    const EXIT_OK                       = 0;    // Success exit code.
    const EXIT_HELP                     = 1;    // Explicit help required.
    const EXIT_UNKNOWN_ACTION           = 127;  // Neither -i nor -u provided.

    /** @var input_manager */
    protected $input = null;

    /** @var output_manager */
    protected $output = null;

    /** @var int the most recent cURL error number, zero for no error */
    private $curlerrno = null;

    /** @var string the most recent cURL error message, empty string for no error */
    private $curlerror = null;

    /** @var array|false the most recent cURL request info, if it was successful */
    private $curlinfo = null;

    /**
     * Main - the one that actually does something
     */
    public function execute() {

        // Authorize access. None in CLI. Passphrase in HTTP.
        $this->authorize();

        // Asking for help in the CLI mode.
        if ($this->input->get_option('help')) {
            $this->output->help();
            $this->done(self::EXIT_HELP);
        }

        if ($this->input->get_option('upgrade')) {
            // Fetch the ZIP file into a temporary location.
            $source = $this->input->get_option('package');
            if (empty($source)) {
                throw new invalid_option_exception('Not a valid package URL');
            }
            $target = $this->target_location($source);

            if ($this->download_file($source, $target)) {
                $this->log('ZIP fetched into '.$target);
            } else {
                $this->log('cURL error ' . $this->curlerrno . ' ' . $this->curlerror);
                $this->log('Unable to download the file');
            }

            // Compare MD5 checksum of the ZIP file - TODO

            // If the target location exists, backup it - TODO

            // Unzip the ZIP file into the target location.

            // Redirect to the given URL (in HTTP) or exit (in CLI).
            $this->done();

        } else if ($this->input->get_option('install')) {
            // Installing a new plugin not implemented yet.
        }

        // Print help in CLI by default.
        $this->output->help();
        $this->done(self::EXIT_UNKNOWN_ACTION);
    }

    /**
     * Initialize the worker class.
     */
    protected function initialize() {
        $this->input = input_manager::instance();
        $this->output = output_manager::instance();
    }

    // End of external API

    /**
     * Finish this script execution.
     *
     * @param int $exitcode
     */
    protected function done($exitcode = self::EXIT_OK) {

        if (PHP_SAPI === 'cli') {
            exit($exitcode);

        } else {
            $returnurl = $this->input->get_option('returnurl');
            redirect($returnurl);
            exit($exitcode);
        }
    }

    /**
     * Authorize access to the script.
     *
     * In CLI mode, the access is automatically authorized. In HTTP mode, the
     * passphrase submitted via the request params must match the contents of the
     * file, the name of which is passed in another parameter.
     *
     * @throws unauthorized_access_exception
     */
    protected function authorize() {

        if (PHP_SAPI === 'cli') {
            return;
        }

        $dataroot = $this->input->get_option('dataroot');
        $passfile = $this->input->get_option('passfile');
        $password = $this->input->get_option('password');

        $passpath = $dataroot.'/mdeploy/auth/'.$passfile;

        if (!is_readable($passpath)) {
            throw new unauthorized_access_exception('Unable to read the passphrase file.');
        }

        $stored = file($passpath, FILE_IGNORE_NEW_LINES);

        // "This message will self-destruct in five seconds." -- Mission Commander Swanbeck, Mission: Impossible II
        unlink($passpath);

        if (is_readable($passpath)) {
            throw new unauthorized_access_exception('Unable to remove the passphrase file.');
        }

        if (count($stored) < 2) {
            throw new unauthorized_access_exception('Invalid format of the passphrase file.');
        }

        if (time() - (int)$stored[1] > 30 * 60) {
            throw new unauthorized_access_exception('Passphrase timeout.');
        }

        if (strlen($stored[0]) < 24) {
            throw new unauthorized_access_exception('Session passphrase not long enough.');
        }

        if ($password !== $stored[0]) {
            throw new unauthorized_access_exception('Session passphrase does not match the stored one.');
        }
    }

    /**
     * Choose the target location for the given ZIP's URL.
     *
     * @param string $source URL
     * @return string
     */
    protected function target_location($source) {

        $dataroot = $this->input->get_option('dataroot');
        $pool = $dataroot.'/mdeploy/var';

        if (!is_dir($pool)) {
            mkdir($pool, 02777, true);
        }

        $target = $pool.'/'.md5($source);

        $suffix = 0;
        while (file_exists($target.'.'.$suffix.'.zip')) {
            $suffix++;
        }

        return $target.'.'.$suffix.'.zip';
    }

    /**
     * Downloads the given file into the given destination.
     *
     * This is basically a simplified version of {@link download_file_content()} from
     * Moodle itself, tuned for fetching files from moodle.org servers.
     *
     * @param string $source file url starting with http(s)://
     * @param string $target store the downloaded content to this file (full path)
     * @return bool true on success, false otherwise
     * @throws download_file_exception
     */
    protected function download_file($source, $target) {

        $newlines = array("\r", "\n");
        $source = str_replace($newlines, '', $source);
        if (!preg_match('|^https?://|i', $source)) {
            throw new download_file_exception('Unsupported transport protocol.');
        }
        if (!$ch = curl_init($source)) {
            // $this->log('Unable to init cURL.');
            return false;
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verify the peer's certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // check the existence of a common name and also verify that it matches the hostname provided
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the transfer as a string
        curl_setopt($ch, CURLOPT_HEADER, false); // don't include the header in the output
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // nah, moodle.org is never unavailable! :-p
        curl_setopt($ch, CURLOPT_URL, $source);

        $targetfile = fopen($target, 'w');

        if (!$targetfile) {
            throw new download_file_exception('Unable to create local file '.$target);
        }

        curl_setopt($ch, CURLOPT_FILE, $targetfile);

        $result = curl_exec($ch);

        // try to detect encoding problems
        if ((curl_errno($ch) == 23 or curl_errno($ch) == 61) and defined('CURLOPT_ENCODING')) {
            curl_setopt($ch, CURLOPT_ENCODING, 'none');
            $result = curl_exec($ch);
        }

        fclose($targetfile);

        $this->curlerrno = curl_errno($ch);
        $this->curlerror = curl_error($ch);
        $this->curlinfo = curl_getinfo($ch);

        if (!$result or $this->curlerrno) {
            return false;

        } else if (is_array($this->curlinfo) and (empty($this->curlinfo['http_code']) or $this->curlinfo['http_code'] != 200)) {
            return false;
        }

        return true;
    }

    /**
     * Log a message
     *
     * @param string $message
     */
    protected function log($message) {
        // TODO
    }
}


////////////////////////////////////////////////////////////////////////////////

// Check if the script is actually executed or if it was just included by someone
// else - typically by the PHPUnit. This is a PHP alternative to the Python's
// if __name__ == '__main__'
if (!debug_backtrace()) {
    // We are executed by the SAPI.
    // Initialize the worker class to actually make the job.
    $worker = worker::instance();

    // Lights, Camera, Action!
    $worker->execute();

} else {
    // We are included - probably by some unit testing framework. Do nothing.
}
