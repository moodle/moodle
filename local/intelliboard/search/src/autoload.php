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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

/**
 * Function for class auto loading
 *
 * @param string   $class Class name for required class
 * @return bool A status indicating that class has been loaded or not
 */

spl_autoload_register(function($class) {

    $baseDir = __DIR__;
    $path = $baseDir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once($path);
        return true;
    }

    return false;

});

function d($arg, $stop = true, $backtrace = false) {

    print "<pre>";
    print "Debug info:";
    var_dump($arg);
    print "</pre>";

    if ($backtrace) {
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }
    if ($stop) {
        die();
    }
}

