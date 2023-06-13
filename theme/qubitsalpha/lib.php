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
 * Theme functions.
 *
 * @package    theme_qubitsalpha
 * @copyright  2023 Qubits Dev Team.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */

/**
 * Returns the main SCSS content.
 *
 * @return string
 */
function theme_qubitsalpha_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';

    $context = context_system::instance();
    $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    return $scss;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_qubitsalpha_get_precompiled_css() {
    global $CFG;
    $css = '';
    $css .= file_get_contents($CFG->dirroot . '/theme/qubitsalpha/style/qubitsfonts.css');
    $css .= file_get_contents($CFG->dirroot . '/theme/qubitsalpha/style/qubitsalpha.css');
    return $css;
}