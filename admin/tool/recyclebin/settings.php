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
 * Recycle bin settings.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $PAGE;

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_recyclebin', get_string('pluginname', 'tool_recyclebin'));
    $ADMIN->add('tools', $settings);

    $lifetimes = array(
        0    => new lang_string('neverdelete', 'tool_recyclebin'),
        1000 => new lang_string('numdays', '', 1000),
        365  => new lang_string('numdays', '', 365),
        180  => new lang_string('numdays', '', 180),
        150  => new lang_string('numdays', '', 150),
        120  => new lang_string('numdays', '', 120),
        90   => new lang_string('numdays', '', 90),
        60   => new lang_string('numdays', '', 60),
        35   => new lang_string('numdays', '', 35),
        21   => new lang_string('numdays', '', 21),
        14   => new lang_string('numdays', '', 14),
        10   => new lang_string('numdays', '', 10),
        5    => new lang_string('numdays', '', 5),
        2    => new lang_string('numdays', '', 2)
    );

    $settings->add(new admin_setting_configcheckbox(
        'tool_recyclebin/enablecourse',
        new lang_string('enablecourse', 'tool_recyclebin'),
        new lang_string('enablecourse_desc', 'tool_recyclebin'),
        1
    ));

    $settings->add(new admin_setting_configselect(
        'tool_recyclebin/expiry',
        new lang_string('expiry', 'tool_recyclebin'),
        new lang_string('expiry_desc', 'tool_recyclebin'),
        0,
        $lifetimes
    ));


    $settings->add(new admin_setting_configcheckbox(
        'tool_recyclebin/enablecategory',
        new lang_string('enablecategory', 'tool_recyclebin'),
        new lang_string('enablecategory_desc', 'tool_recyclebin'),
        1
    ));

    $settings->add(new admin_setting_configselect(
        'tool_recyclebin/course_expiry',
        new lang_string('course_expiry', 'tool_recyclebin'),
        new lang_string('course_expiry_desc', 'tool_recyclebin'),
        0,
        $lifetimes
    ));

    unset($lifetimes);

    $settings->add(new admin_setting_configcheckbox(
        'tool_recyclebin/autohide',
        new lang_string('autohide', 'tool_recyclebin'),
        new lang_string('autohide_desc', 'tool_recyclebin'),
        0
    ));

    $settings->add(new admin_setting_configmultiselect(
        'tool_recyclebin/protectedmods',
        new lang_string('protectedmods', 'tool_recyclebin'),
        new lang_string('protectedmods_desc', 'tool_recyclebin'),
        array(),
        $DB->get_records_menu('modules', array('visible' => 1), 'name ASC', 'id, name')
    ));
}
