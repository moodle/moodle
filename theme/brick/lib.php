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
 * This file contains functions specific to the Brick theme.
 *
 * @package    theme_brick
 * @copyright  2010 John Stabinger (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sets the link colour used by the Brick theme.
 *
 * @param string $css
 * @param string $linkcolor
 * @return string
 */
function brick_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#06365b';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the link hover colour used by the Brick theme.
 *
 * @param string $css
 * @param string $linkhover
 * @return string
 */
function brick_set_linkhover($css, $linkhover) {
    $tag = '[[setting:linkhover]]';
    $replacement = $linkhover;
    if (is_null($replacement)) {
        $replacement = '#5487ad';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the main colour used by the Brick theme.
 *
 * @param string $css
 * @param string $maincolor
 * @return string
 */
function brick_set_maincolor($css, $maincolor) {
    $tag = '[[setting:maincolor]]';
    $replacement = $maincolor;
    if (is_null($replacement)) {
        $replacement = '#8e2800';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the main link colour used by the Brick theme.
 *
 * @param string $css
 * @param string $maincolorlink
 * @return string
 */
function brick_set_maincolorlink($css, $maincolorlink) {
    $tag = '[[setting:maincolorlink]]';
    $replacement = $maincolorlink;
    if (is_null($replacement)) {
        $replacement = '#fff0a5';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the headig colour used by the brick theme.
 *
 * @param string $css
 * @param string $headingcolor
 * @return string
 */
function brick_set_headingcolor($css, $headingcolor) {
    $tag = '[[setting:headingcolor]]';
    $replacement = $headingcolor;
    if (is_null($replacement)) {
        $replacement = '#5c3500';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the logo used by the Brick theme.
 *
 * @param string $css
 * @param string $logo
 * @param theme_config $theme
 * @return string
 */
function brick_set_logo($css, $logo, $theme) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('logo', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Processes CSS adding Brick customisations to it before it is cached and delivered.
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function brick_process_css($css, $theme) {

    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = brick_set_linkcolor($css, $linkcolor);

    // Set the link hover color.
    if (!empty($theme->settings->linkhover)) {
        $linkhover = $theme->settings->linkhover;
    } else {
        $linkhover = null;
    }
    $css = brick_set_linkhover($css, $linkhover);

    // Set the main color.
    if (!empty($theme->settings->maincolor)) {
        $maincolor = $theme->settings->maincolor;
    } else {
        $maincolor = null;
    }
    $css = brick_set_maincolor($css, $maincolor);

      // Set the main accent color.
    if (!empty($theme->settings->maincolorlink)) {
        $maincolorlink = $theme->settings->maincolorlink;
    } else {
        $maincolorlink = null;
    }
    $css = brick_set_maincolorlink($css, $maincolorlink);

    // Set the main headings color.
    if (!empty($theme->settings->headingcolor)) {
        $headingcolor = $theme->settings->headingcolor;
    } else {
        $headingcolor = null;
    }
    $css = brick_set_headingcolor($css, $headingcolor);
    
    // Set the logo image.
    if (!empty($theme->settings->logo)) {
        $logo = $theme->settings->logo;
    } else {
        $logo = null;
    }
    $css = brick_set_logo($css, $logo, $theme);

    return $css;
}