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
 * This file contains the settings for the Nonzero theme.
 *
 * Currently you can set the following settings:
 *    - Region pre width
 *    - Region post width
 *    - Some custom CSS
 *
 * @package  theme_nonzero
 * @copyright 2010 Dietmar Wagner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function nonzero_process_css($css, $theme) {


    // Set the region-pre and region-post widths
    if (!empty($theme->settings->regionprewidth) && !empty($theme->settings->regionpostwidth)) {
        $regionprewidth = $theme->settings->regionprewidth;
        $regionpostwidth = $theme->settings->regionpostwidth;
    } else {
        $regionprewidth = null;
        $regionpostwidth = null;
    }
    $css = nonzero_set_regionwidths($css, $regionprewidth, $regionpostwidth);


    // Set the custom CSS
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = nonzero_set_customcss($css, $customcss);

    // Return the CSS
    return $css;
}

/**
 * Sets the region width variable in CSS
 *
 * @param string $css
 * @param mixed $regionwidth
 * @return string
 */

function nonzero_set_regionwidths($css, $regionprewidth, $regionpostwidth) {
    $tag1 = '[[setting:regionprewidth]]';
    $tag2 = '[[setting:regionpostwidth]]';
    $tag3 = '[[setting:regionsumwidth]]';
    $tag4 = '[[setting:regiondoublepresumwidth]]';
    $replacement1 = $regionprewidth;
    $replacement2 = $regionpostwidth;
    if (is_null($replacement1) or is_null($replacement2)) {
        $replacement1 = 200;
        $replacement2 = 200;
    }
    $css = str_replace($tag1, $replacement1.'px', $css);
    $css = str_replace($tag2, $replacement2.'px', $css);
    $css = str_replace($tag3, ($replacement1+$replacement2).'px', $css);
    $css = str_replace($tag4, (2*$replacement1+$replacement2).'px', $css);
    return $css;
}


/**
 * Sets the custom css variable in CSS
 *
 * @param string $css
 * @param mixed $customcss
 * @return string
 */

function nonzero_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}