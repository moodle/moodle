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
 * @package   block_mycourses
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_heading('mycoursesheader',
                                         get_string('headerconfig', 'block_mycourses'),
                                         get_string('headerdesc', 'block_mycourses')));

$settings->add(new admin_setting_configtext('mycourses_archivecutoff',
                                                get_string('archivecutofflabel', 'block_mycourses'),
                                                get_string('archivecutoffdesc', 'block_mycourses'),
                                                100, PARAM_INT));

$settings->add(new admin_setting_configcheckbox('mycourses_showsummary',
                                                get_string('showsummarylabel', 'block_mycourses'),
                                                get_string('showsummarydesc', 'block_mycourses'),
                                                0));
