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
$pluginsheets = optional_param('pluginsheets', '', PARAM_BOOL);

// Load the configuration of the selected theme. (See comment at the top of the file.)
$PAGE->force_theme($fortheme);

$DEFAULT_SHEET_LIST = array('styles_layout', 'styles_fonts', 'styles_color');

// Fix for IE6 caching - we don't want the filemtime('styles.php'), instead use now.
$lastmodified = time();

// Set the correct content type. (Should we also be specifying charset here?)
header('Content-type: text/css');
header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastmodified) . ' GMT');
header('Pragma: ');

// Set the caching for these style sheets
if (debugging('', DEBUG_DEVELOPER)) {        // Use very short caching time
    header('Cache-Control: max-age=60');     // One minute
    header('Expires: ' . gmdate("D, d M Y H:i:s", $lastmodified + 60) . ' GMT');
} else if ($themename == 'standard') {       // Give this one extra long caching MDL-19953
    header('Cache-Control: max-age=172801'); // Two days plus one second
    header('Expires: ' . gmdate("D, d M Y H:i:s", $lastmodified + 172801) . ' GMT');
} else {                                     // Use whatever time the theme has set
    header('Cache-Control: max-age='.$THEME->csslifetime);
    header('Expires: ' . gmdate("D, d M Y H:i:s", $lastmodified + $THEME->csslifetime) . ' GMT');
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

// This is a bit tricky, but the following initialisation code may output
// notices or debug developer warnings (for example, if the theme uses some
// Deprecated settings in it config.php file. Therefore start a CSS comment
// so that any debugging output does not break the CSS. This comment is closed
// below.
echo '/*';



// We will build up a list of CSS file path names, then concatenate them all.
$files = array();

// If this theme wants plugin sheets, include them. Do this first, so styles
// here can be overridden by theme CSS.
if ($pluginsheets) {
    foreach ($THEME->pluginsheets as $plugintype) {
        $files = array_merge($files, get_sheets_for_plugin_type($plugintype));
    }
}

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
        $themesheets = array();
    }

} else if ($themename == 'standard') {
    if ($THEME->standardsheets === true) {
        // Use all the sheets we have.
        $themesheets = $DEFAULT_SHEET_LIST;
    } else if (!empty($THEME->standardsheets)) {
        $themesheets = $THEME->standardsheets;
    } else {
        $themesheets = array();
    }
}

// Conver the theme stylessheet names to file names.
foreach ($themesheets as $sheet) {
    $files[] = $CFG->themedir . '/' . $themename . '/' . $sheet . '.css';
}

if (empty($files)) {
    echo " The $fortheme theme does not require anything from the $themename theme. */\n\n";
    exit;
}

// Output a commen with a summary of the included files.
echo <<<END

 * Styles from theme '$themename' for theme '$fortheme'
 *
 * Files included here:
 *

END;
$toreplace = array($CFG->dirroot . '/', $CFG->themedir . '/');
foreach ($files as $file) {
    echo ' *   ' . str_replace($toreplace, '', $file) . "\n";
}
echo " */\n\n";

if (!empty($THEME->cssoutputfunction)) {
    call_user_func($THEME->cssoutputfunction, $files, $toreplace);

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
