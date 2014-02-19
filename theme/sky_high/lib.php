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
 * This file contains functions specific to the needs of the Sky High theme.
 *
 * @package   theme_sky_high
 * @copyright 2010 John Stabinger (http://newschoollearning.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Processes CSS applying theme customisations before it is cached and delivered.
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function sky_high_process_css($css, $theme) {

    if (!empty($theme->settings->regionwidth)) {
        $regionwidth = $theme->settings->regionwidth;
    } else {
        $regionwidth = null;
    }
    $css = sky_high_set_regionwidth($css, $regionwidth);

    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = sky_high_set_customcss($css, $customcss);

    return $css;
}

/**
 * Sets the region width variable in CSS
 *
 * @param string $css
 * @param string $regionwidth
 * @return string
 */
function sky_high_set_regionwidth($css, $regionwidth) {
    $tag = '[[setting:regionwidth]]';
    $doubletag = '[[setting:regionwidthdouble]]';
    $leftmargintag = '[[setting:leftregionwidthmargin]]';
    $rightmargintag = '[[setting:rightregionwidthmargin]]';
    $replacement = $regionwidth;
    if (is_null($replacement)) {
        $replacement = 240;
    }
    $css = str_replace($tag, $replacement.'px', $css);
    $css = str_replace($doubletag, ($replacement * 2).'px', $css);
    $css = str_replace($rightmargintag, ($replacement * 3 - 5).'px', $css);
    $css = str_replace($leftmargintag, ($replacement + 5).'px', $css);
    return $css;
}

/**
 * Sets the custom css variable in CSS
 *
 * @param string $css
 * @param string $customcss
 * @return string
 */
function sky_high_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}