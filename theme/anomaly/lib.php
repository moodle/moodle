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
 * This file contains functions specific to the needs of the Anomaly theme.
 *
 * @package    theme_anomaly
 * @copyright  2010 Patrick Malley (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Process CSS applying theme customisations before it is cached and delivered.
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function anomaly_process_css($css, $theme) {

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = anomaly_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds custom CSS set by the admin to the CSS for the Anomaly theme.
 *
 * @param string $css
 * @param string $customcss
 * @return string
 */
function anomaly_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}
