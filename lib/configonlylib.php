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
 * @package   moodlecore
 * @copyright 2009 petr Skoda (skodak)
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
        // very, very, very ugly hack, unforunately $OUTPUT->old_icon_url() is not used properly in javascript code :-(
        $value = $_GET['amp;'.$name];
    }
    switch($type) {
        case 'INT':      $value = (int)$value; break;
        case 'SAFEDIR':  $value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value); break;
        case 'SAFEPATH': $value = preg_replace('/[^a-zA-Z0-9\/_-]/', '', $value); break;
        default:         die("Coding error: incorrent parameter type specified ($type).");
    }
    return $value;
}

