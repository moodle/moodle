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
 * This file contains functions specific to the Arialist theme.
 *
 * @package    theme_arialist
 * @copyright  2010 Patrick Malley (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function arialist_process_css($css, $theme) {

    // Set the link color
    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = arialist_set_linkcolor($css, $linkcolor);

    // Set the region width
    if (!empty($theme->settings->regionwidth)) {
        $regionwidth = $theme->settings->regionwidth;
    } else {
        $regionwidth = null;
    }
    $css = arialist_set_regionwidth($css, $regionwidth);

    // Set the custom CSS
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = arialist_set_customcss($css, $customcss);

    // Return the CSS
    return $css;
}

/**
 * Sets the background colour variable in CSS
 *
 * @param string $css
 * @param mixed $backgroundcolor
 * @return string
 */
function arialist_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#f25f0f';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the region width variable in CSS
 *
 * @param string $css
 * @param mixed $regionwidth
 * @return string
 */
 
function arialist_set_regionwidth($css, $regionwidth) {
    $tag = '[[setting:regionwidth]]';
    $doubletag = '[[setting:regionwidthdouble]]';
    $replacement = $regionwidth;
    if (is_null($replacement)) {
        $replacement = 250;
    }
    $css = str_replace($tag, $replacement.'px', $css);
    $css = str_replace($doubletag, ($replacement*2).'px', $css);
    return $css;
}

/**
 * Sets the custom css variable in CSS
 *
 * @param string $css
 * @param mixed $customcss
 * @return string
 */
function arialist_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}