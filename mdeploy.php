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

    const TYPE_FLAG         = 'flag';   // No value, just a flag (switch)
    const TYPE_INT          = 'int';    // Integer

    /** @var input_cli_manager|input_http_manager the provider of the input */
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
            array('h', 'help', input_manager::TYPE_FLAG, 'Prints usage information'),
            array('i', 'install', input_manager::TYPE_FLAG, 'Installation mode'),
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

            case input_manager::TYPE_FLAG:
                return true;

            case input_manager::TYPE_INT:
                return (int)$raw;

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
    protected function get_required_option($name, $type) {
        if ($this->inputprovider->has_option($name)) {
            return $this->cast_value($this->inputprovider->get_raw_option($name), $type);
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
    protected function get_optional_option($name, $type, $default) {
        if ($this->inputprovider->has_option($name)) {
            return $this->inputprovider->get_raw_option($name);
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
        return $_GET; // TODO switch to $_POST
    }
}


// Output handling /////////////////////////////////////////////////////////////

/**
 * TODO: short description.
 *
 * TODO: long description.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class output_manager extends singleton_pattern {

}


// The main class providing all the functionality //////////////////////////////

/**
 * TODO: short description.
 *
 * TODO: long description.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class worker extends singleton_pattern {

    /**
     * TODO: short description.
     *
     * @param input_manager  $input  
     * @param output_manager $output 
     * @return TODO
     */
    public function execute(input_manager $input, output_manager $output) {

        // Authorize access. None in CLI. Passphrase in HTTP.

        // Fetch the ZIP file into a temporary location.

        // If the target location exists, backup it.

        // Unzip the ZIP file into the target location.

        // Redirect to the given URL (in HTTP) or exit (in CLI).
    }

}


////////////////////////////////////////////////////////////////////////////////

// Check if the script is actually executed or if it was just included by someone
// else - typically by the PHPUnit. This is a PHP alternative to the Python's
// if __name__ == '__main__'

if (!debug_backtrace()) {
    // We are executed by the SAPI

    // Initialize the input options manager.
    $input = input_manager::instance();

    // Initialize the output (display) manager.
    $output = output_manager::instance();

    // Initialize the worker class to actually make the job.
    $worker = worker::instance();

    // Lights, Camera, Action!
    $worker->execute($input, $output);

} else {
    // We are included - probably by some unit testing framework. Do nothing.
}
