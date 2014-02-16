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
 * This file contains functions specific to the needs of the afterburner theme.
 *
 * @package    theme_afterburner
 * @copyright  2011 Mary Evans
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Processes CSS for the afterburner theme before it is cached and delivered.
 *
 * This function performs any customisations on the CSS that this theme requires.
 * This includes setting the theme logo, and including any custom CSS.
 *
 * @param string $css The raw CSS.
 * @param theme_config $theme
 * @return string The now processed CSS.
 */
function afterburner_process_css($css, $theme) {

    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = afterburner_set_logo($css, $logo, $theme);

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = afterburner_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds the set logo to the CSS before it is cached and delivered.
 *
 * @param string $css
 * @param string $logo
 * @param theme_config $theme
 * @return string
 */
function afterburner_set_logo($css, $logo, $theme) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('images/logo', 'theme');
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Adds any custom CSS the admin has set to the CSS file before it is cached and delivered.
 * @param string $css
 * @param string $customcss
 * @return string
 */
function afterburner_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Serves any theme associated files when they are requested.
 *
 * @param stdClass $course
 * @param cm_info $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_afterburner_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'logo') {
        $theme = theme_config::load('afterburner');
        return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}
