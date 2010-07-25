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
 * Minimalistic library, usable even when no other moodle libs are loaded.
 *
 * The only library that gets loaded if you define ABORT_AFTER_CONFIG
 * before including main config.php. You can resume normal script operation
 * if you define ABORT_AFTER_CONFIG_CANCEL and require the setup.php
 *
 * @package   core
 * @copyright 2009 Petr Skoda (skodak)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Minimalistic parameter validation function.
 * Can not use optional param because moodlelib.php is not loaded yet
 * @param string $name
 * @param mixed $default
 * @param string $type
 * @return mixed
 */
function min_optional_param($name, $default, $type) {
    $value = $default;
    if (isset($_GET[$name])) {
        $value = $_GET[$name];

    } else if (isset($_GET['amp;'.$name])) {
        // very, very, very ugly hack, unfortunately $OUTPUT->pix_url() is not used properly in javascript code :-(
        $value = $_GET['amp;'.$name];
    }

    return min_clean_param($value, $type);
}

/**
 * Minimalistic parameter cleaning function.
 * Can not use optional param because moodlelib.php is not loaded yet
 * @param string $name
 * @param mixed $default
 * @param string $type
 * @return mixed
 */
function min_clean_param($value, $type) {
    switch($type) {
        case 'RAW':      break;
        case 'INT':      $value = (int)$value;
                         break;
        case 'SAFEDIR':  $value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);
                         break;
        case 'SAFEPATH': $value = preg_replace('/[^a-zA-Z0-9\/\._-]/', '', $value);
                         $value = preg_replace('/\.+/', '.', $value);
                         $value = preg_replace('#/+#', '/', $value);
                         break;
        default:         die("Coding error: incorrect parameter type specified ($type).");
    }

    return $value;
}

/**
 * This method tries to enable output compression if possible.
 * This function must be called before any output or headers.
 *
 * (IE6 is not supported at all.)
 *
 * @return boolean, true if compression enabled
 */
function min_enable_zlib_compression() {

    if (headers_sent()) {
        return false;
    }

    // zlib.output_compression is preferred over ob_gzhandler()
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        // try to detect IE6 and prevent gzip because it is extremely buggy browser
        $parts = explode(';', $agent);
        if (isset($parts[1])) {
            $parts = explode(' ', trim($parts[1]));
            if (count($parts) > 1) {
                if ($parts[0] === 'MSIE' and (float)$parts[1] < 7) {
                    @ini_set('zlib.output_compression', '0');
                    return false;
                }
            }
        }
    }

    @ini_set('output_handler', '');

    /*
     * docs clearly say 'on' means enable and number means size of buffer,
     * but unfortunately some PHP version break when we set 'on' here.
     * 1 probably sets chunk size to 4096. our CSS and JS scripts are much bigger,
     * so let's try some bigger sizes.
     */
    @ini_set('zlib.output_compression', 65536);

    return true;
}