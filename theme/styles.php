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
 * This file is responsible for serving the CSS of each theme.
 *
 * It should not be linked to directly. Instead, it gets included by
 * theme/themename/styles.php. See theme/standard/styles.php as an example.
 *
 * In this script, we are serving the styles for theme $themename, but we are
 * serving them on behalf of theme $fortheme.
 *
 * To understand this, image that the currently selected theme is standardwhite.
 * This theme uses both its own stylesheets, and also the ones from the standard theme.
 * So, when we are serving theme/standard/styles.php, we need to use the config in
 * theme/standardwhite/config.php to control the settings. This is controled by the
 * for=... parameter in the URL.
 *
 * In case you are wondering, in the above scenario, we have to serve the standard
 * theme CSS with a URL like theme/standard/styles.php, so that relative links from
 * the CSS to images in the theme folder will work.
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (empty($themename)) {
    die('Direct access to this script is forbidden.');
    // This script should only be required by theme/themename/styles.php.
}

// These may already be defined if we got here via style_sheet_setup in lib/deprecatedlib.php
if (!defined('NO_MOODLE_COOKIES')) {
    define('NO_MOODLE_COOKIES', true); // Session not used here
}
if (!defined('NO_UPGRADE_CHECK')) {
    define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check
}
require_once(dirname(__FILE__) . '/../config.php');


$fortheme = required_param('for', PARAM_FILE);
$lang = optional_param('lang', '', PARAM_FILE);

$CACHE_LIFETIME = 1800; // Cache stylesheets for half an hour.
$DEFAULT_SHEET_LIST = array('styles_layout', 'styles_fonts', 'styles_color');

// Fix for IE6 caching - we don't want the filemtime('styles.php'), instead use now.
$lastmodified = time();

// Set the correct content type. (Should we also be specifying charset here?)
header('Content-type: text/css'); 
if (!debugging('', DEBUG_DEVELOPER)) {
    // Do not send caching headers for developer. (This makes it easy to edit themes.
    // You don't have to keep clearing the browser cache.)
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastmodified) . ' GMT');
    header('Expires: ' . gmdate("D, d M Y H:i:s", $lastmodified + $CACHE_LIFETIME) . ' GMT');
    header('Cache-Control: max-age=' . $lifetime);
    header('Pragma: ');
}

if (!empty($showdeprecatedstylesheetsetupwarning)) {
    echo <<<END

/***************************************************************
 ***************************************************************
 ****                                                       ****
 **** WARNING! This theme uses an old-fashioned styles.php  ****
 **** file. It should be updated by copying styles.php from ****
 **** the standard theme of a recent version of Moodle.     ****
 ****                                                       ****
 ***************************************************************
 ***************************************************************/



END;
}

// Load the configuration of the selected theme. (See comment at the top of the file.)
$PAGE->force_theme($fortheme);

// Now work out which stylesheets we shold be serving from this theme.
if ($themename == $fortheme) {
    $themesheets = $THEME->sheets;

} else if (!empty($THEME->parent) && $themename == $THEME->parent) {
    if ($THEME->parentsheets === true) {
        // Use all the sheets we have.
        $themesheets = $DEFAULT_SHEET_LIST;
    } else if (!empty($THEME->parentsheets)) {
        $themesheets = $THEME->parentsheets;
    } else {
        echo "/* The current theme does not require anything from the standard theme. */\n\n";
        exit;
    }

} else if ($themename == 'standard') {
    if ($THEME->standardsheets === true) {
        // Use all the sheets we have.
        $themesheets = $DEFAULT_SHEET_LIST;
    } else if (!empty($THEME->standardsheets)) {
        $themesheets = $THEME->standardsheets;
    } else {
        echo "/* The current theme does not require anything from the standard theme. */\n\n";
        exit;
    }
}

// Conver the sheet names to file names.
$files = array();
foreach ($themesheets as $sheet) {
    $files[] = $CFG->themedir . '/' . $themename . '/' . $sheet . '.css';
}

// If this is the standard theme, then also include the styles.php files from
// each of the plugins, as determined by the theme settings.
if ($themename == 'standard') {
    if (!empty($THEME->modsheets)) {
        $files += get_sheets_for_plugin_type('mod');
    }

    if (!empty($THEME->blocksheets)) {
        $files += get_sheets_for_plugin_type('block');
    }

    if (!empty($THEME->courseformatsheets)) {
        $files += get_sheets_for_plugin_type('format');
    }

    if (!empty($THEME->gradereportsheets)) {
        $files += get_sheets_for_plugin_type('gradereport');
    }

    if (!empty($THEME->langsheets) && $lang) {
        $file = $CFG->dirroot . '/lang/' . $lang . '/styles.php';
        if (file_exists($file)) {
            $files[] = $file;
        }
    }
}

if (empty($files)) {
    echo "/* The current theme does not require anything from this theme. */\n\n";
    exit;
}

// Output a commen with a summary of the included files.
echo <<<END
/*
 * Styles from theme '$themename' for theme '$fortheme'
 *
 * Files included here:
 *

END;
$toreplace = array($CFG->dirroot, $CFG->themedir);
foreach ($files as $file) {
    echo ' *   ' . str_replace($toreplace, '', $file) . "\n";
}
echo " */\n\n";

if (!empty($THEME->cssoutputfunction)) {
    call_user_func($THEME->cssoutputfunction, $files);

} else {
    foreach ($files as $file) {
        $shortname = str_replace($toreplace, '', $file);
        echo '/******* ' . $shortname . " start *******/\n\n";
        @include_once($file);
        echo '/******* ' . $shortname . " end *******/\n\n";
    }
}

function get_sheets_for_plugin_type($type) {
    $files = array();
    $mods = get_plugin_list($type);
    foreach ($mods as $moddir) {
        $file = $moddir . '/styles.php';
        if (file_exists($file)) {
            $files[] = $file;
        }
    }
    return $files;
}
