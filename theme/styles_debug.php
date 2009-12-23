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
 * This file is responsible for serving of individual style sheets in designer mode.
 *
 * @package   moodlecore
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// no chaching
define('NO_MOODLE_COOKIES', true); // Session not used here
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check
require('../config.php');

$themename = required_param('theme', PARAM_SAFEDIR);
$type      = required_param('type', PARAM_SAFEDIR);
$subtype   = optional_param('subtype', '', PARAM_SAFEDIR);
$sheet     = optional_param('sheet', '', PARAM_SAFEDIR);

if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
    // exists
} else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
    // exists
} else {
    css_not_found();
}

if (theme_get_revision() > -1) {
    //bad luck - this should not happen!
    css_not_found();
}

$theme = theme_config::load($themename);

if ($type === 'editor') {
    $css = $theme->editor_css_content();
    send_uncached_css($css);
}

$css = $theme->css_content();

if ($type === 'yui') {
    send_uncached_css(reset($css['yui']));

} else if ($type === 'plugin') {
    if (isset($css['plugins'][$subtype])) {
        send_uncached_css($css['plugins'][$subtype]);
    }

} else if ($type === 'parent') {
    if (isset($css['parents'][$subtype][$sheet])) {
        send_uncached_css($css['parents'][$subtype][$sheet]);
    }

} else if ($type === 'theme') {
    if (isset($css['theme'][$sheet])) {
        send_uncached_css($css['theme'][$sheet]);
    }
}
css_not_found();

//=================================================================================
//=== utility functions ==
// we are not using filelib because we need to fine tune all header
// parameters to get the best performance.

function send_uncached_css($css) {
    header('Content-Disposition: inline; filename="styles_debug.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 2) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: text/css');
    header('Content-Length: '.strlen($css));

    while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
    echo($css);
    die;
}

function css_not_found() {
    header('HTTP/1.0 404 not found');
    die('CSS was not found, sorry.');
}