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
 * @package   core
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php
require_once($CFG->dirroot.'/lib/csslib.php');

$themename = min_optional_param('theme', 'standard', 'SAFEDIR');
$type      = min_optional_param('type', '', 'SAFEDIR');
$subtype   = min_optional_param('subtype', '', 'SAFEDIR');
$sheet     = min_optional_param('sheet', '', 'SAFEDIR');
$usesvg    = (bool)min_optional_param('svg', '1', 'INT');

if (!defined('THEME_DESIGNER_CACHE_LIFETIME')) {
    define('THEME_DESIGNER_CACHE_LIFETIME', 4); // this can be also set in config.php
}

if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
    // exists
} else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
    // exists
} else {
    css_send_css_not_found();
}

// no gzip compression when debugging

if ($usesvg) {
    $candidatesheet = "$CFG->cachedir/theme/$themename/designer.ser";
} else {
    // Add to the sheet name, one day we'll be able to just drop this.
    $candidatesheet = "$CFG->cachedir/theme/$themename/designer_nosvg.ser";
}

if (!file_exists($candidatesheet)) {

    css_send_css_not_found();
}

if (!$css = file_get_contents($candidatesheet)) {
    css_send_css_not_found();
}

$css = unserialize($css);

if ($type === 'editor') {
    if (isset($css['editor'])) {
        css_send_uncached_css($css['editor']);
    }
} else if ($type === 'ie') {
    // IE is a sloppy browser with weird limits, sorry
    if ($subtype === 'plugins') {
        css_send_uncached_css($css['plugins']);

    } else if ($subtype === 'parents') {
        $sendcss = array();
        if (empty($sheet)) {
            // If not specific parent has been specified as $sheet then build a
            // collection of @import statements into this one sheet.
            // We shouldn't ever actually get here, but none the less we'll deal
            // with it incase we ever do.
            // @import statements arn't processed until after concurrent CSS requests
            // making them slightly evil.
            foreach (array_keys($css['parents']) as $sheet) {
                $sendcss[] = "@import url(styles_debug.php?theme=$themename&type=$type&subtype=$subtype&sheet=$sheet);";
            }
        } else {
            // Build up the CSS for that parent so we can serve it as one file.
            foreach ($css[$subtype][$sheet] as $parent=>$css) {
                $sendcss[] = $css;
            }
        }
        css_send_uncached_css($sendcss);
    } else if ($subtype === 'theme') {
        css_send_uncached_css($css['theme']);
    }

} else if ($type === 'plugin') {
    if (isset($css['plugins'][$subtype])) {
        css_send_uncached_css($css['plugins'][$subtype]);
    }

} else if ($type === 'parent') {
    if (isset($css['parents'][$subtype][$sheet])) {
        css_send_uncached_css($css['parents'][$subtype][$sheet]);
    }

} else if ($type === 'theme') {
    if (isset($css['theme'][$sheet])) {
        css_send_uncached_css($css['theme'][$sheet]);
    }
}
css_send_css_not_found();