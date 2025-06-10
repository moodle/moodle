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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_heading('block_csv_profile/heading'
                                , get_string('settingstitle', 'block_csv_profile')
                                , get_string('settingshelp', 'block_csv_profile')));
    $options = array(0 => 'username', 1 => 'email', 2 => 'idnumber');
    $settings->add(new admin_setting_configselect('block_csv_profile/userfield'
                                , get_string('userfield', 'block_csv_profile')
                                , get_string('userfielddesc', 'block_csv_profile')
                                , ''
                                , $options));
    $settings->add(new admin_setting_configtext('block_csv_profile/profilefield'
                                , get_string('profilefield', 'block_csv_profile')
                                , get_string('profilefielddesc', 'block_csv_profile')
                                , ''));
}