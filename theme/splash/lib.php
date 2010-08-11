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
 * Library functions for the Splash theme
 *
 * @package   theme_splash
 * @copyright 2010 Caroline Kennedy of Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Splash theme post process function for CSS
 * @param string $css Incoming CSS to process
 * @param stdClass $theme The theme object
 * @return string The processed CSS
 */
function splash_process_css($css, $theme) {
 
    if (!empty($theme->settings->regionwidth)) {
        $regionwidth = $theme->settings->regionwidth;
    } else {
        $regionwidth = null;
    }
    $css = splash_set_regionwidth($css, $regionwidth);
 
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = splash_set_customcss($css, $customcss);
 
    return $css;
}

/**
 * Sets the region width variable in CSS
 *
 * @param string $css
 * @param mixed $regionwidth
 * @return string
 */
function splash_set_regionwidth($css, $regionwidth) {
    $tag = '[[setting:regionwidth]]';
    $doubletag = '[[setting:regionwidthdouble]]';
    $leftmargintag = '[[setting:leftregionwidthmargin]]';
    $rightmargintag = '[[setting:rightregionwidthmargin]]';
    $replacement = $regionwidth;
    if (is_null($replacement)) {
        $replacement = 240;
    }
    $css = str_replace($tag, $replacement.'px', $css);
    $css = str_replace($doubletag, ($replacement*2).'px', $css);
    $css = str_replace($rightmargintag, ($replacement*3-5).'px', $css);
    $css = str_replace($leftmargintag, ($replacement+5).'px', $css);
    return $css;
}

/**
 * Sets the custom css variable in CSS
 *
 * @param string $css
 * @param mixed $customcss
 * @return string
 */
function splash_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Adds the JavaScript for the colour switcher to the page.
 *
 * The colour switcher is a YUI moodle module that is located in
 *     theme/splash/yui/splash/splash.js
 *
 * @param moodle_page $page 
 */
function splash_initialise_colourswitcher(moodle_page $page) {
    user_preference_allow_ajax_update('theme_splash_chosen_colour', PARAM_ALPHA);
    $page->requires->yui_module('moodle-theme_splash-colourswitcher', 'M.theme_splash.initColourSwitcher', array(array('div'=>'#colourswitcher')));
}

/**
 * Gets the colour the user has selected, or the default if they have never changed
 *
 * @param string $default The default colour to use, normally red
 * @return string The colour the user has selected
 */
function splash_get_colour($default='red') {
    return get_user_preferences('theme_splash_chosen_colour', $default);
}

/**
 * Checks if the user is switching colours with a refresh (JS disabled)
 *
 * If they are this updates the users preference in the database
 *
 * @return bool
 */
function splash_check_colourswitch() {
    $changecolour = optional_param('splashcolour', null, PARAM_ALPHA);
    if (in_array($changecolour, array('red','green','blue','orange'))) {
        return set_user_preference('theme_splash_chosen_colour', $changecolour);
    }
    return false;
}