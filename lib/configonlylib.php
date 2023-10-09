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
    if (isset($_GET[$name])) {
        $value = $_GET[$name];

    } else if (isset($_GET['amp;'.$name])) {
        // very, very, very ugly hack, unfortunately $OUTPUT->pix_url() is not used properly in javascript code :-(
        $value = $_GET['amp;'.$name];

    } else if (isset($_POST[$name])) {
        $value = $_POST[$name];

    } else {
        return $default;
    }

    return min_clean_param($value, $type);
}

/**
 * Minimalistic parameter cleaning function.
 *
 * Note: Can not use optional param because moodlelib.php is not loaded yet.
 *
 * @param string $value
 * @param string $type
 * @return mixed
 */
function min_clean_param($value, $type) {
    switch($type) {
        case 'RAW':      $value = min_fix_utf8((string)$value);
                         break;
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
 * Minimalistic UTF-8 sanitisation.
 *
 * Note: This duplicates fix_utf8() intentionally for now.
 *
 * @param string $value
 * @return string
 */
function min_fix_utf8($value) {
    // No null bytes expected in our data, so let's remove it.
    $value = str_replace("\0", '', $value);

    static $buggyiconv = null;
    if ($buggyiconv === null) {
        set_error_handler(function () {
            return true;
        });
        $buggyiconv = (!function_exists('iconv') or iconv('UTF-8', 'UTF-8//IGNORE', '100'.chr(130).'€') !== '100€');
        restore_error_handler();
    }

    if ($buggyiconv) {
        if (function_exists('mb_convert_encoding')) {
            $subst = mb_substitute_character();
            mb_substitute_character('none');
            $result = mb_convert_encoding($value, 'utf-8', 'utf-8');
            mb_substitute_character($subst);

        } else {
            // Warn admins on admin/index.php page.
            $result = $value;
        }

    } else {
        $result = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
    }

    return $result;
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
    if (!empty($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false) {
        ini_set('zlib.output_compression', 'Off');
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', 1);
        }
        return false;
    }

    ini_set('output_handler', '');

    /*
     * docs clearly say 'on' means enable and number means size of buffer,
     * but unfortunately some PHP version break when we set 'on' here.
     * 1 probably sets chunk size to 4096. our CSS and JS scripts are much bigger,
     * so let's try some bigger sizes.
     */
    ini_set('zlib.output_compression', 65536);

    return true;
}

/**
 * Returns the slashargument part of the URL.
 *
 * Note: ".php" is NOT allowed in slasharguments,
 *       it is intended for ASCII characters only.
 *
 * @param boolean $clean - Should we do cleaning on this path argument. If you set this
 *                         to false you MUST be very careful and do the cleaning manually.
 * @return string
 */
function min_get_slash_argument($clean = true) {
    // Note: This code has to work in the same cases as normal get_file_argument(),
    //       but at the same time it may be simpler because we do not have to deal
    //       with encodings and other tricky stuff.

    $relativepath = '';

    if (!empty($_GET['file']) and strpos($_GET['file'], '/') === 0) {
        // Server is using url rewriting, most probably IIS.
        // Always clean the result of this function as it may be used in unsafe calls to send_file.
        $relativepath = $_GET['file'];
        if ($clean) {
            $relativepath = min_clean_param($relativepath, 'SAFEPATH');
        }

        return $relativepath;

    } else if (stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false) {
        if (isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO'] !== '') {
            $relativepath = urldecode($_SERVER['PATH_INFO']);
        }

    } else {
        if (isset($_SERVER['PATH_INFO'])) {
            $relativepath = $_SERVER['PATH_INFO'];
        }
    }

    $matches = null;
    if (preg_match('|^.+\.php(.*)$|i', $relativepath, $matches)) {
        $relativepath = $matches[1];
    }

    // Always clean the result of this function as it may be used in unsafe calls to send_file.
    if ($clean) {
        $relativepath = min_clean_param($relativepath, 'SAFEPATH');
    }
    return $relativepath;
}

/**
 * Get the lowest possible currently valid revision number.
 *
 * This is based on the current Moodle version.
 *
 * @return int Unix timestamp
 */
function min_get_minimum_revision(): int {
    static $timestamp = null;

    if ($timestamp === null) {
        global $CFG;
        require("{$CFG->dirroot}/version.php");
        // Get YYYYMMDD from version.
        $datestring = floor($version / 100);
        // Parse the date components.
        $year = intval(substr($datestring, 0, 4));
        $month = intval(substr($datestring, 4, 2));
        $day = intval(substr($datestring, 6, 2));
        // Return converted GMT Unix timestamp.
        $timestamp = gmmktime(0, 0, 0, $month, $day, $year);
    }

    return $timestamp;
}

/**
 * Get the highest possible currently valid revision number.
 *
 * This is based on the current time, allowing for a small amount of clock skew between servers.
 *
 * Future values beyond the clock skew are not allowed to avoid the possibility of cache poisoning.
 *
 * @return int
 */
function min_get_maximum_revision(): int {
    return time() + 60;
}

/**
 * Helper function to determine if the given revision number is valid.
 *
 * @param int $revision A numeric revision to check for validity
 * @return bool Whether the revision is valid
 */
function min_is_revision_valid_and_current(int $revision): bool {
    // Invalid revision.
    if ($revision <= 0) {
        return false;
    }
    // Check revision is within range.
    return $revision >= min_get_minimum_revision() && $revision <= min_get_maximum_revision();
}
