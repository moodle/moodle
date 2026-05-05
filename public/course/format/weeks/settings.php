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
 * Settings for format_weeks
 *
 * @package    format_weeks
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $url = new moodle_url('/admin/course/resetindentation.php', ['format' => 'weeks']);
    $link = html_writer::link($url, get_string('resetindentation', 'admin'));
    $settings->add(new admin_setting_configcheckbox(
        'format_weeks/indentation',
        new lang_string('indentation', 'format_weeks'),
        new lang_string('indentation_help', 'format_weeks').'<br />'.$link,
        1
    ));

    $settings->add(new admin_setting_configtext(
        name: 'format_weeks/maxinitialsections',
        visiblename: new lang_string('maxinitialsections', 'format_weeks'),
        description: new lang_string('maxinitialsections_help', 'format_weeks'),
        defaultsetting: 52,
        paramtype: PARAM_INT,
    ));
    $options = [
        1 => get_string('yes'),
        0 => get_string('no'),
    ];
    $settings->add(new admin_setting_configselect(
        'format_weeks/enablelinearnav',
        new lang_string('linearnavigationsettings', 'core_courseformat'),
        new lang_string('linearnavigationsettings_help', 'core_courseformat'),
        1,
        $options,
    ));
}
