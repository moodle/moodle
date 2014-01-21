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
 * This script looks after deploying new add-ons and available updates for them
 * to the local Moodle site. It can operate via both HTTP and CLI mode.
 * Moodle itself calls this utility via the HTTP mode when the admin is about to
 * install or update an add-on. You can use the CLI mode in your custom deployment
 * shell scripts.
 *
 * CLI usage example:
 *
 *  $ sudo -u apache php mdeploy.php --install \
 *                                   --package=https://moodle.org/plugins/download.php/...zip \
 *                                   --typeroot=/var/www/moodle/htdocs/blocks
 *                                   --name=loancalc
 *                                   --md5=...
 *                                   --dataroot=/var/www/moodle/data
 *
 *  $ sudo -u apache php mdeploy.php --upgrade \
 *                                   --package=https://moodle.org/plugins/download.php/...zip \
 *                                   --typeroot=/var/www/moodle/htdocs/blocks
 *                                   --name=loancalc
 *                                   --md5=...
 *                                   --dataroot=/var/www/moodle/data
 *
 * When called via HTTP, additional parameters returnurl, passfile and password must be
 * provided. Optional proxy configuration can be passed using parameters proxy, proxytype
 * and proxyuserpwd.
 *
 * Changes
 *
 * 1.1 - Added support to install a new plugin from the Moodle Plugins directory.
 * 1.0 - Initial version used in Moodle 2.4 to deploy available updates.
 *
 * @package     core
 * @subpackage  mdeploy
 * @version     1.1
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
class backup_folder_exception extends Exception {}
class zip_exception extends Exception {}
class filesystem_exception extends Exception {}
class checksum_exception extends Exception {}


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
    const TYPE_PLUGIN       = 'plugin'; // Plugin name
    const TYPE_MD5          = 'md5';    // MD5 hash

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
            array('', 'proxy', input_manager::TYPE_RAW, 'HTTP proxy host and port (e.g. \'our.proxy.edu:8888\')'),
            array('', 'proxytype', input_manager::TYPE_RAW, 'Proxy type (HTTP or SOCKS5)'),
            array('', 'proxyuserpwd', input_manager::TYPE_RAW, 'Proxy username and password (e.g. \'username:password\')'),
            array('', 'returnurl', input_manager::TYPE_URL, 'Return URL (HTTP access only)'),
            array('d', 'dataroot', input_manager::TYPE_PATH, 'Full path to the dataroot (moodledata) directory'),
            array('h', 'help', input_manager::TYPE_FLAG, 'Prints usage information'),
            array('i', 'install', input_manager::TYPE_FLAG, 'Installation mode'),
            array('m', 'md5', input_manager::TYPE_MD5, 'Expected MD5 hash of the ZIP package to deploy'),
            array('n', 'name', input_manager::TYPE_PLUGIN, 'Plugin name (the name of its folder)'),
            array('p', 'package', input_manager::TYPE_URL, 'URL to the ZIP package to deploy'),
            array('r', 'typeroot', input_manager::TYPE_PATH, 'Full path of the container for this plugin type'),
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
                if (strpos($raw, '~') !== false) {
                    throw new invalid_option_exception('Using the tilde (~) character in paths is not supported');
                }
                $colonpos = strpos($raw, ':');
                if ($colonpos !== false) {
                    if ($colonpos !== 1 or strrpos($raw, ':') !== 1) {
                        throw new invalid_option_exception('Using the colon (:) character in paths is supported for Windows drive labels only.');
                    }
                    if (preg_match('/^[a-zA-Z]:/', $raw) !== 1) {
                        throw new invalid_option_exception('Using the colon (:) character in paths is supported for Windows drive labels only.');
                    }
                }
                $raw = str_replace('\\', '/', $raw);
                $raw = preg_replace('~[[:cntrl:]]|[&<>"`\|\']~u', '', $raw);
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
                    throw new invalid_option_exception('Not a valid URL');
                }

            case input_manager::TYPE_PLUGIN:
                if (!preg_match('/^[a-z][a-z0-9_]*[a-z0-9]$/', $raw)) {
                    throw new invalid_option_exception('Invalid plugin name');
                }
                if (strpos($raw, '__') !== false) {
                    throw new invalid_option_exception('Invalid plugin name');
                }
                return $raw;

            case input_manager::TYPE_MD5:
                if (!preg_match('/^[a-f0-9]{32}$/', $raw)) {
                    throw new invalid_option_exception('Invalid MD5 hash format');
                }
                return $raw;

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

    /**
     * Display the information about uncaught exception
     *
     * @param Exception $e uncaught exception
     */
    public function exception(Exception $e) {

        $docslink = 'http://docs.moodle.org/en/admin/mdeploy/'.get_class($e);
        $this->start_output();
        echo('<h1>Oops! It did it again</h1>');
        echo('<p><strong>Moodle deployment utility had a trouble with your request.
            See <a href="'.$docslink.'">the docs page</a> and the debugging information for more details.</strong></p>');
        echo('<pre>');
        echo exception_handlers::format_exception_info($e);
        echo('</pre>');
        $this->end_output();
    }

    // End of external API

    /**
     * Produce the HTML page header
     */
    protected function start_output() {
        echo '<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style type="text/css">
    body {background-color:#666;font-family:"DejaVu Sans","Liberation Sans",Freesans,sans-serif;}
    h1 {text-align:center;}
    pre {white-space: pre-wrap;}
    #page {background-color:#eee;width:1024px;margin:5em auto;border:3px solid #333;border-radius: 15px;padding:1em;}
  </style>
</head>
<body>
<div id="page">';
    }

    /**
     * Produce the HTML page footer
     */
    protected function end_output() {
        echo '</div></body></html>';
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

    /** @var string the full path to the log file */
    private $logfile = null;

    /**
     * Main - the one that actually does something
     */
    public function execute() {

        $this->log('=== MDEPLOY EXECUTION START ===');

        // Authorize access. None in CLI. Passphrase in HTTP.
        $this->authorize();

        // Asking for help in the CLI mode.
        if ($this->input->get_option('help')) {
            $this->output->help();
            $this->done(self::EXIT_HELP);
        }

        if ($this->input->get_option('upgrade')) {
            $this->log('Plugin upgrade requested');

            // Fetch the ZIP file into a temporary location.
            $source = $this->input->get_option('package');
            $target = $this->target_location($source);
            $this->log('Downloading package '.$source);

            if ($this->download_file($source, $target)) {
                $this->log('Package downloaded into '.$target);
            } else {
                $this->log('cURL error ' . $this->curlerrno . ' ' . $this->curlerror);
                $this->log('Unable to download the file from ' . $source . ' into ' . $target);
                throw new download_file_exception('Unable to download the package');
            }

            // Compare MD5 checksum of the ZIP file
            $md5remote = $this->input->get_option('md5');
            $md5local = md5_file($target);

            if ($md5local !== $md5remote) {
                $this->log('MD5 checksum failed. Expected: '.$md5remote.' Got: '.$md5local);
                throw new checksum_exception('MD5 checksum failed');
            }
            $this->log('MD5 checksum ok');

            // Backup the current version of the plugin
            $plugintyperoot = $this->input->get_option('typeroot');
            $pluginname = $this->input->get_option('name');
            $sourcelocation = $plugintyperoot.'/'.$pluginname;
            $backuplocation = $this->backup_location($sourcelocation);

            $this->log('Current plugin code location: '.$sourcelocation);
            $this->log('Moving the current code into archive: '.$backuplocation);

            if (file_exists($sourcelocation)) {
                // We don't want to touch files unless we are pretty sure it would be all ok.
                if (!$this->move_directory_source_precheck($sourcelocation)) {
                    throw new backup_folder_exception('Unable to backup the current version of the plugin (source precheck failed)');
                }
                if (!$this->move_directory_target_precheck($backuplocation)) {
                    throw new backup_folder_exception('Unable to backup the current version of the plugin (backup precheck failed)');
                }

                // Looking good, let's try it.
                if (!$this->move_directory($sourcelocation, $backuplocation, true)) {
                    throw new backup_folder_exception('Unable to backup the current version of the plugin (moving failed)');
                }

            } else {
                // Upgrading missing plugin - this happens often during upgrades.
                if (!$this->create_directory_precheck($sourcelocation)) {
                    throw new filesystem_exception('Unable to prepare the plugin location (cannot create new directory)');
                }
            }

            // Unzip the plugin package file into the target location.
            $this->unzip_plugin($target, $plugintyperoot, $sourcelocation, $backuplocation);
            $this->log('Package successfully extracted');

            // Redirect to the given URL (in HTTP) or exit (in CLI).
            $this->done();

        } else if ($this->input->get_option('install')) {
            $this->log('Plugin installation requested');

            $plugintyperoot = $this->input->get_option('typeroot');
            $pluginname     = $this->input->get_option('name');
            $source         = $this->input->get_option('package');
            $md5remote      = $this->input->get_option('md5');

            // Check if the plugin location if available for us.
            $pluginlocation = $plugintyperoot.'/'.$pluginname;

            $this->log('New plugin code location: '.$pluginlocation);

            if (file_exists($pluginlocation)) {
                throw new filesystem_exception('Unable to prepare the plugin location (directory already exists)');
            }

            if (!$this->create_directory_precheck($pluginlocation)) {
                throw new filesystem_exception('Unable to prepare the plugin location (cannot create new directory)');
            }

            // Fetch the ZIP file into a temporary location.
            $target = $this->target_location($source);
            $this->log('Downloading package '.$source);

            if ($this->download_file($source, $target)) {
                $this->log('Package downloaded into '.$target);
            } else {
                $this->log('cURL error ' . $this->curlerrno . ' ' . $this->curlerror);
                $this->log('Unable to download the file');
                throw new download_file_exception('Unable to download the package');
            }

            // Compare MD5 checksum of the ZIP file
            $md5local = md5_file($target);

            if ($md5local !== $md5remote) {
                $this->log('MD5 checksum failed. Expected: '.$md5remote.' Got: '.$md5local);
                throw new checksum_exception('MD5 checksum failed');
            }
            $this->log('MD5 checksum ok');

            // Unzip the plugin package file into the plugin location.
            $this->unzip_plugin($target, $plugintyperoot, $pluginlocation, false);
            $this->log('Package successfully extracted');

            // Redirect to the given URL (in HTTP) or exit (in CLI).
            $this->done();
        }

        // Print help in CLI by default.
        $this->output->help();
        $this->done(self::EXIT_UNKNOWN_ACTION);
    }

    /**
     * Attempts to log a thrown exception
     *
     * @param Exception $e uncaught exception
     */
    public function log_exception(Exception $e) {
        $this->log($e->__toString());
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
            $this->redirect($returnurl);
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
            $this->log('Successfully authorized using the CLI SAPI');
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

        $this->log('Successfully authorized using the passphrase file');
    }

    /**
     * Returns the full path to the log file.
     *
     * @return string
     */
    protected function log_location() {

        if (!is_null($this->logfile)) {
            return $this->logfile;
        }

        $dataroot = $this->input->get_option('dataroot', '');

        if (empty($dataroot)) {
            $this->logfile = false;
            return $this->logfile;
        }

        $myroot = $dataroot.'/mdeploy';

        if (!is_dir($myroot)) {
            mkdir($myroot, 02777, true);
        }

        $this->logfile = $myroot.'/mdeploy.log';
        return $this->logfile;
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
     * Choose the location of the current plugin folder backup
     *
     * @param string $path full path to the current folder
     * @return string
     */
    protected function backup_location($path) {

        $dataroot = $this->input->get_option('dataroot');
        $pool = $dataroot.'/mdeploy/archive';

        if (!is_dir($pool)) {
            mkdir($pool, 02777, true);
        }

        $target = $pool.'/'.basename($path).'_'.time();

        $suffix = 0;
        while (file_exists($target.'.'.$suffix)) {
            $suffix++;
        }

        return $target.'.'.$suffix;
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
            $this->log('Unable to init cURL.');
            return false;
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verify the peer's certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // check the existence of a common name and also verify that it matches the hostname provided
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the transfer as a string
        curl_setopt($ch, CURLOPT_HEADER, false); // don't include the header in the output
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // nah, moodle.org is never unavailable! :-p
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Allow redirection, we trust in ssl.
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

        if ($cacertfile = $this->get_cacert()) {
            // Do not use CA certs provided by the operating system. Instead,
            // use this CA cert to verify the ZIP provider.
            $this->log('Using custom CA certificate '.$cacertfile);
            curl_setopt($ch, CURLOPT_CAINFO, $cacertfile);
        } else {
            $this->log('Using operating system CA certificates.');
        }

        $proxy = $this->input->get_option('proxy', false);
        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);

            $proxytype = $this->input->get_option('proxytype', false);
            if (strtoupper($proxytype) === 'SOCKS5') {
                $this->log('Using SOCKS5 proxy');
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            } else if (!empty($proxytype)) {
                $this->log('Using HTTP proxy');
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
            }

            $proxyuserpwd = $this->input->get_option('proxyuserpwd', false);
            if (!empty($proxyuserpwd)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyuserpwd);
                curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
            }
        }

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
            $this->log('Curl Error.');
            return false;

        } else if (is_array($this->curlinfo) and (empty($this->curlinfo['http_code']) or ($this->curlinfo['http_code'] != 200))) {
            $this->log('Curl remote error.');
            $this->log(print_r($this->curlinfo,true));
            return false;
        }

        return true;
    }

    /**
     * Get the location of ca certificates.
     * @return string absolute file path or empty if default used
     */
    protected function get_cacert() {
        $dataroot = $this->input->get_option('dataroot');

        // Bundle in dataroot always wins.
        if (is_readable($dataroot.'/moodleorgca.crt')) {
            return realpath($dataroot.'/moodleorgca.crt');
        }

        // Next comes the default from php.ini
        $cacert = ini_get('curl.cainfo');
        if (!empty($cacert) and is_readable($cacert)) {
            return realpath($cacert);
        }

        // Windows PHP does not have any certs, we need to use something.
        if (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) {
            if (is_readable(__DIR__.'/lib/cacert.pem')) {
                return realpath(__DIR__.'/lib/cacert.pem');
            }
        }

        // Use default, this should work fine on all properly configured *nix systems.
        return null;
    }

    /**
     * Log a message
     *
     * @param string $message
     */
    protected function log($message) {

        $logpath = $this->log_location();

        if (empty($logpath)) {
            // no logging available
            return;
        }

        $f = fopen($logpath, 'ab');

        if ($f === false) {
            throw new filesystem_exception('Unable to open the log file for appending');
        }

        $message = $this->format_log_message($message);

        fwrite($f, $message);

        fclose($f);
    }

    /**
     * Prepares the log message for writing into the file
     *
     * @param string $msg
     * @return string
     */
    protected function format_log_message($msg) {

        $msg = trim($msg);
        $timestamp = date("Y-m-d H:i:s");

        return $timestamp . ' '. $msg . PHP_EOL;
    }

    /**
     * Checks to see if the given source could be safely moved into a new location
     *
     * @param string $source full path to the existing directory
     * @return bool
     */
    protected function move_directory_source_precheck($source) {

        if (!is_writable($source)) {
            return false;
        }

        if (is_dir($source)) {
            $handle = opendir($source);
        } else {
            return false;
        }

        $result = true;

        while ($filename = readdir($handle)) {
            $sourcepath = $source.'/'.$filename;

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_dir($sourcepath)) {
                $result = $result && $this->move_directory_source_precheck($sourcepath);

            } else {
                $result = $result && is_writable($sourcepath);
            }
        }

        closedir($handle);

        return $result;
    }

    /**
     * Checks to see if a source folder could be safely moved into the given new location
     *
     * @param string $destination full path to the new expected location of a folder
     * @return bool
     */
    protected function move_directory_target_precheck($target) {

        // Check if the target folder does not exist yet, can be created
        // and removed again.
        $result = $this->create_directory_precheck($target);

        // At the moment, it seems to be enough to check. We may want to add
        // more steps in the future.

        return $result;
    }

    /**
     * Make sure the given directory can be created (and removed)
     *
     * @param string $path full path to the folder
     * @return bool
     */
    protected function create_directory_precheck($path) {

        if (file_exists($path)) {
            return false;
        }

        $result = mkdir($path, 02777) && rmdir($path);

        return $result;
    }

    /**
     * Moves the given source into a new location recursively
     *
     * The target location can not exist.
     *
     * @param string $source full path to the existing directory
     * @param string $destination full path to the new location of the folder
     * @param bool $keepsourceroot should the root of the $source be kept or removed at the end
     * @return bool
     */
    protected function move_directory($source, $target, $keepsourceroot = false) {

        if (file_exists($target)) {
            throw new filesystem_exception('Unable to move the directory - target location already exists');
        }

        return $this->move_directory_into($source, $target, $keepsourceroot);
    }

    /**
     * Moves the given source into a new location recursively
     *
     * If the target already exists, files are moved into it. The target is created otherwise.
     *
     * @param string $source full path to the existing directory
     * @param string $destination full path to the new location of the folder
     * @param bool $keepsourceroot should the root of the $source be kept or removed at the end
     * @return bool
     */
    protected function move_directory_into($source, $target, $keepsourceroot = false) {

        if (is_dir($source)) {
            $handle = opendir($source);
        } else {
            throw new filesystem_exception('Source location is not a directory');
        }

        if (is_dir($target)) {
            $result = true;
        } else {
            $result = mkdir($target, 02777);
        }

        while ($filename = readdir($handle)) {
            $sourcepath = $source.'/'.$filename;
            $targetpath = $target.'/'.$filename;

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_dir($sourcepath)) {
                $result = $result && $this->move_directory($sourcepath, $targetpath, false);

            } else {
                $result = $result && rename($sourcepath, $targetpath);
            }
        }

        closedir($handle);

        if (!$keepsourceroot) {
            $result = $result && rmdir($source);
        }

        clearstatcache();

        return $result;
    }

    /**
     * Deletes the given directory recursively
     *
     * @param string $path full path to the directory
     * @param bool $keeppathroot should the root of the $path be kept (i.e. remove the content only) or removed too
     * @return bool
     */
    protected function remove_directory($path, $keeppathroot = false) {

        $result = true;

        if (!file_exists($path)) {
            return $result;
        }

        if (is_dir($path)) {
            $handle = opendir($path);
        } else {
            throw new filesystem_exception('Given path is not a directory');
        }

        while ($filename = readdir($handle)) {
            $filepath = $path.'/'.$filename;

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_dir($filepath)) {
                $result = $result && $this->remove_directory($filepath, false);

            } else {
                $result = $result && unlink($filepath);
            }
        }

        closedir($handle);

        if (!$keeppathroot) {
            $result = $result && rmdir($path);
        }

        clearstatcache();

        return $result;
    }

    /**
     * Unzip the file obtained from the Plugins directory to this site
     *
     * @param string $ziplocation full path to the ZIP file
     * @param string $plugintyperoot full path to the plugin's type location
     * @param string $expectedlocation expected full path to the plugin after it is extracted
     * @param string|bool $backuplocation location of the previous version of the plugin or false for no backup
     */
    protected function unzip_plugin($ziplocation, $plugintyperoot, $expectedlocation, $backuplocation) {

        $zip = new ZipArchive();
        $result = $zip->open($ziplocation);

        if ($result !== true) {
            if ($backuplocation !== false) {
                $this->move_directory($backuplocation, $expectedlocation);
            }
            throw new zip_exception('Unable to open the zip package');
        }

        // Make sure that the ZIP has expected structure
        $pluginname = basename($expectedlocation);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $filename = $stat['name'];
            $filename = explode('/', $filename);
            if ($filename[0] !== $pluginname) {
                $zip->close();
                throw new zip_exception('Invalid structure of the zip package');
            }
        }

        if (!$zip->extractTo($plugintyperoot)) {
            $zip->close();
            $this->remove_directory($expectedlocation, true); // just in case something was created
            if ($backuplocation !== false) {
                $this->move_directory_into($backuplocation, $expectedlocation);
            }
            throw new zip_exception('Unable to extract the zip package');
        }

        $zip->close();
        unlink($ziplocation);
    }

    /**
     * Redirect the browser
     *
     * @todo check if there has been some output yet
     * @param string $url
     */
    protected function redirect($url) {
        header('Location: '.$url);
    }
}


/**
 * Provides exception handlers for this script
 */
class exception_handlers {

    /**
     * Sets the exception handler
     *
     *
     * @param string $handler name
     */
    public static function set_handler($handler) {

        if (PHP_SAPI === 'cli') {
            // No custom handler available for CLI mode.
            set_exception_handler(null);
            return;
        }

        set_exception_handler('exception_handlers::'.$handler.'_exception_handler');
    }

    /**
     * Returns the text describing the thrown exception
     *
     * By default, PHP displays full path to scripts when the exception is thrown. In order to prevent
     * sensitive information leak (and yes, the path to scripts at a web server _is_ sensitive information)
     * the path to scripts is removed from the message.
     *
     * @param Exception $e thrown exception
     * @return string
     */
    public static function format_exception_info(Exception $e) {

        $mydir = dirname(__FILE__).'/';
        $text = $e->__toString();
        $text = str_replace($mydir, '', $text);
        return $text;
    }

    /**
     * Very basic exception handler
     *
     * @param Exception $e uncaught exception
     */
    public static function bootstrap_exception_handler(Exception $e) {
        echo('<h1>Oops! It did it again</h1>');
        echo('<p><strong>Moodle deployment utility had a trouble with your request. See the debugging information for more details.</strong></p>');
        echo('<pre>');
        echo self::format_exception_info($e);
        echo('</pre>');
    }

    /**
     * Default exception handler
     *
     * When this handler is used, input_manager and output_manager singleton instances already
     * exist in the memory and can be used.
     *
     * @param Exception $e uncaught exception
     */
    public static function default_exception_handler(Exception $e) {

        $worker = worker::instance();
        $worker->log_exception($e);

        $output = output_manager::instance();
        $output->exception($e);
    }
}

////////////////////////////////////////////////////////////////////////////////

// Check if the script is actually executed or if it was just included by someone
// else - typically by the PHPUnit. This is a PHP alternative to the Python's
// if __name__ == '__main__'
if (!debug_backtrace()) {
    // We are executed by the SAPI.
    exception_handlers::set_handler('bootstrap');
    // Initialize the worker class to actually make the job.
    $worker = worker::instance();
    exception_handlers::set_handler('default');

    // Lights, Camera, Action!
    $worker->execute();

} else {
    // We are included - probably by some unit testing framework. Do nothing.
}
