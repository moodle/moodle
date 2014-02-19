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
 * This file contains functions specific to the needs of the Overlay theme.
 *
 * @package   theme_overlay
 * @copyright 2008 NodeThirtyThree (http://nodethirtythree.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sets the link colour in CSS.
 * @param string $css
 * @param string $linkcolor
 * @return string
 */
function overlay_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#428ab5';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the header colour in CSS.
 * @param string $css
 * @param string $headercolor
 * @return string
 */
function overlay_set_headercolor($css, $headercolor) {
    $tag = '[[setting:headercolor]]';
    $replacement = $headercolor;
    if (is_null($replacement)) {
        $replacement = '#2a4c7b';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Adds custom CSS to the theme CSS before it is cached and delivered.
 * @param string $css
 * @param string $customcss
 * @return string
 */
function overlay_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Processes CSS before it is cached and delivered, applying theme customisations.
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function overlay_process_css($css, $theme) {

    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = overlay_set_linkcolor($css, $linkcolor);

    if (!empty($theme->settings->headercolor)) {
        $headercolor = $theme->settings->headercolor;
    } else {
        $headercolor = null;
    }
    $css = overlay_set_headercolor($css, $headercolor);

    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = overlay_set_customcss($css, $customcss);

    return $css;
}

