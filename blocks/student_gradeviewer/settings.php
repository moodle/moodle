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
 * The main block file.
 *
 * @package    block_ues_people
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $roles = role_get_names(null, null, true);

    $internalroles = array(
        'academic_mentor', 'sports_mentor',
        'academic_admin', 'sports_admin'
    );

    $settings->add(new admin_setting_heading('role_heading', '',
        get_string('role_help', 'block_student_gradeviewer')
    ));

    foreach ($internalroles as $internalrole) {
        $settings->add(new admin_setting_configselect(
            'block_student_gradeviewer/' . $internalrole,
            get_string($internalrole, 'block_student_gradeviewer'),
            get_string($internalrole . '_help', 'block_student_gradeviewer'),
            key($roles), $roles
        ));
    }
}
