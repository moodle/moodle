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
 * This file contains functions specific to the needs of the Nimble theme.
 *
 * @package   theme_nimble
 * @copyright 2010 Patrick Malley
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string 
 */
function nimble_process_css($css, $theme) {

    // Set the link color.
    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = nimble_set_linkcolor($css, $linkcolor);

	// Set the link hover color.
    if (!empty($theme->settings->linkhover)) {
        $linkhover = $theme->settings->linkhover;
    } else {
        $linkhover = null;
    }
    $css = nimble_set_linkhover($css, $linkhover);

    // Set the background color.
    if (!empty($theme->settings->backgroundcolor)) {
        $backgroundcolor = $theme->settings->backgroundcolor;
    } else {
        $backgroundcolor = null;
    }
    $css = nimble_set_backgroundcolor($css, $backgroundcolor);

    // Return the CSS.
    return $css;
}

/**
 * Sets the link color variable in CSS
 *
 * @param string $css
 * @param string $linkcolor
 * @return string
 */
function nimble_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#2a65b1';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the link hover colour in CSS.
 *
 * @param string $css
 * @param string $linkhover
 * @return string
 */
function nimble_set_linkhover($css, $linkhover) {
    $tag = '[[setting:linkhover]]';
    $replacement = $linkhover;
    if (is_null($replacement)) {
        $replacement = '#222222';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the background colour in CSS.
 * @param string $css
 * @param string $backgroundcolor
 * @return string
 */
function nimble_set_backgroundcolor($css, $backgroundcolor) {
    $tag = '[[setting:backgroundcolor]]';
    $replacement = $backgroundcolor;
    if (is_null($replacement)) {
        $replacement = '#454545';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}