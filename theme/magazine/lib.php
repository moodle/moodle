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
 * This file contains functions specific to the needs of the Magazine theme.
 *
 * @package    theme_magazine
 * @copyright  2010 John Stabinger (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string 
 */
function magazine_process_css($css, $theme) {

    // Set the link color.
    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = magazine_set_linkcolor($css, $linkcolor);

    // Set the link hover color.
    if (!empty($theme->settings->linkhover)) {
        $linkhover = $theme->settings->linkhover;
    } else {
        $linkhover = null;
    }
    $css = magazine_set_linkhover($css, $linkhover);

    // Set the main color.
    if (!empty($theme->settings->maincolor)) {
        $maincolor = $theme->settings->maincolor;
    } else {
        $maincolor = null;
    }
    $css = magazine_set_maincolor($css, $maincolor);

    // Set the main accent color.
    if (!empty($theme->settings->maincoloraccent)) {
        $maincoloraccent = $theme->settings->maincoloraccent;
    } else {
        $maincoloraccent = null;
    }
    $css = magazine_set_maincoloraccent($css, $maincoloraccent);

    // Set the main headings color.
    if (!empty($theme->settings->headingcolor)) {
        $headingcolor = $theme->settings->headingcolor;
    } else {
        $headingcolor = null;
    }
    $css = magazine_set_headingcolor($css, $headingcolor);

    // Set the block headings color.
    if (!empty($theme->settings->blockcolor)) {
        $blockcolor = $theme->settings->blockcolor;
    } else {
        $blockcolor = null;
    }
    $css = magazine_set_blockcolor($css, $blockcolor);

    // Set the forum background color.
    if (!empty($theme->settings->forumback)) {
        $forumback = $theme->settings->forumback;
    } else {
        $forumback = null;
    }
    $css = magazine_set_forumback($css, $forumback);

    // Set the body background image.
    if (!empty($theme->settings->background)) {
        $background = $theme->settings->background;
    } else {
        $background = null;
    }
    $css = magazine_set_background($css, $background, $theme);

    // Set the logo image.
    if (!empty($theme->settings->logo)) {
        $logo = $theme->settings->logo;
    } else {
        $logo = null;
    }
    $css = magazine_set_logo($css, $logo, $theme);

    // Return the CSS.
    return $css;
}



/**
 * Sets the link color variable in CSS.
 *
 * @param string $css
 * @param string $linkcolor
 * @return string
 */
function magazine_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#32529a';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the link hover colour in CSS.
 * @param string $css
 * @param string $linkhover
 * @return string
 */
function magazine_set_linkhover($css, $linkhover) {
    $tag = '[[setting:linkhover]]';
    $replacement = $linkhover;
    if (is_null($replacement)) {
        $replacement = '#4e2300';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the main colour used by the Magazine theme.
 * @param string $css
 * @param string $maincolor
 * @return string
 */
function magazine_set_maincolor($css, $maincolor) {
    $tag = '[[setting:maincolor]]';
    $replacement = $maincolor;
    if (is_null($replacement)) {
        $replacement = '#002f2f';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the colour for accents within the Magazine theme.
 * @param string $css
 * @param string $maincoloraccent
 * @return string
 */
function magazine_set_maincoloraccent($css, $maincoloraccent) {
    $tag = '[[setting:maincoloraccent]]';
    $replacement = $maincoloraccent;
    if (is_null($replacement)) {
        $replacement = '#092323';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the heading colour in CSS.
 * @param string $css
 * @param string $headingcolor
 * @return string
 */
function magazine_set_headingcolor($css, $headingcolor) {
    $tag = '[[setting:headingcolor]]';
    $replacement = $headingcolor;
    if (is_null($replacement)) {
        $replacement = '#4e0000';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the block colour in CSS.
 * @param string $css
 * @param string $blockcolor
 * @return string
 */
function magazine_set_blockcolor($css, $blockcolor) {
    $tag = '[[setting:blockcolor]]';
    $replacement = $blockcolor;
    if (is_null($replacement)) {
        $replacement = '#002f2f';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the forum back colour in CSS.
 * @param string $css
 * @param string $forumback
 * @return string
 */
function magazine_set_forumback($css, $forumback) {
    $tag = '[[setting:forumback]]';
    $replacement = $forumback;
    if (is_null($replacement)) {
        $replacement = '#e6e2af';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the background for the Magazine theme.
 * @param string $css
 * @param string $background
 * @param theme_config $theme
 * @return string
 */
function magazine_set_background($css, $background, $theme) {
    $tag = '[[setting:background]]';
    $replacement = $background;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('bg4', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Sets the logo for the Magazine theme.
 *
 * @param string $css
 * @param string $logo
 * @param theme_config $theme
 * @return string
 */
function magazine_set_logo($css, $logo, $theme) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('logo', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}
