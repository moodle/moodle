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
 * Cards Format - A topics based format that uses card layout to diaply the content.
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

if ($ADMIN->fulltree) {
    // Default length of sumary of the section/activities.
    $name = 'format_remuiformat/defaultsectionsummarymaxlength';
    $title = get_string('defaultsectionsummarymaxlength', 'format_remuiformat');
    $description = get_string('defaultsectionsummarymaxlength_desc', 'format_remuiformat');
    $default = 100;
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    // Default setting hide general section when empty.
    $name = 'format_remuiformat/hidegeneralsectionwhenempty';
    $title = new lang_string('hidegeneralsectionwhenempty', 'format_remuiformat');
    $description = new lang_string('hidegeneralsectionwhenempty_help', 'format_remuiformat');
    $default = 0;
    $settings->add(new admin_setting_configselect(
        $name,
        $title,
        $description,
        $default,
        array(
            0 => new lang_string('show'),
            1 => new lang_string('hide')
        )
    ));

    // Default setting hide general section when empty.
    $name = 'format_remuiformat/remuicourseformat';
    $title = new lang_string('remuicourseformat', 'format_remuiformat');
    $description = new lang_string('remuicourseformat_help', 'format_remuiformat');
    $default = 0;
    $settings->add(new admin_setting_configselect(
        $name,
        $title,
        $description,
        $default,
        array(
            0 => new lang_string('remuicourseformat_card', 'format_remuiformat'),
            1 => new lang_string('remuicourseformat_list', 'format_remuiformat')
        )
    ));

    // Usage tracking GDPR setting.
    $name = 'format_remuiformat/enableusagetracking';
    $title = get_string('enableusagetracking', 'format_remuiformat');
    $description = get_string('enableusagetrackingdesc', 'format_remuiformat');
    $default = true;
    $settings->add(new admin_setting_configcheckbox($name, $title, $description, $default, true, false));
}
