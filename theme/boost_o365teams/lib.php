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
 * Boost o365teams.
 *
 * @package    theme_boost_o365teams
 * @copyright  2018 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use theme_boost_o365teams\css_processor;

/**
 * Serve any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_boost_o365teams_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('boost_o365teams');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'footer_stamp') {
            return $theme->setting_file_serve('footer_stamp', $args, $forcedownload, $options);

        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}
