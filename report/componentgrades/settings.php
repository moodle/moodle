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
 * Data to control defaults when creating and running a question
 *
 * @package    report_componentgrades
 * @copyright  2021 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox(
        'report_componentgrades/showstudentid',
        get_string('showstudentid', 'report_componentgrades'),
        get_string('showstudentid_text', 'report_componentgrades'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'report_componentgrades/showgroups',
        get_string('showgroups', 'report_componentgrades'),
        get_string('showgroups_desc', 'report_componentgrades'),
        1
    ));
}
