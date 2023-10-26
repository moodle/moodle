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
 * An external function that throws an exception, for tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2020 Dani Palou
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * Create an external function that throws an exception, for tests.
 *
 * @copyright  2020 Dani Palou
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_external_function_throwable extends external_api {

    /**
     * Returns description of throw_exception() parameters.
     *
     * @return external_function_parameters
     */
    public static function throw_exception_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Throws a PHP error.
     *
     * @return array empty array.
     */
    public static function throw_exception() {
        $a = 1 % 0;

        return array();
    }

    /**
     * Returns description of throw_exception() result value.
     *
     * @return external_description
     */
    public static function throw_exception_returns() {
        return new external_single_structure(array());
    }

    /**
     * Override external_function_info to accept our fake WebService.
     */
    public static function external_function_info($function, $strictness=MUST_EXIST) {
        if ($function == 'core_throw_exception') {
            // Convert it to an object.
            $function = new stdClass();
            $function->name = $function;
            $function->classname = 'test_external_function_throwable';
            $function->methodname = 'throw_exception';
            $function->classpath = ''; // No need to define class path because current file is already loaded.
            $function->component = 'fake';
            $function->capabilities = '';
            $function->services = 'moodle_mobile_app';
            $function->loginrequired = false;
        }

        return parent::external_function_info($function, $strictness);
    }
}
