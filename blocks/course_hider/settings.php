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
 * Course Hider Tool
 *
 * @package   block_course_hider
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Set the string for use later.
// $fn = new lang_string('foldername', 'block_course_hider');

// Create the folder / submenu.
// $ADMIN->add('blocksettings', new admin_category('blockchfolder', $fn));

// Create the settings block.
// $settings = new admin_settingpage($section, get_string('settings'));

// Make sure only admins see this one.
if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_heading(
            'block_course_hider_formoptionsheader',
            get_string('formoptionsheader', 'block_course_hider'),
            ''
        )
    );

    // Range of years.
    $settings->add(
        new admin_setting_configtext(
            'block_course_hider_form_years',
            get_string('years', 'block_course_hider'),
            get_string('yearsdesc', 'block_course_hider'),
            '2020-2030' // Default.
        )
    );

    // Semesters Types.
    $settings->add(
        new admin_setting_configtext(
            'block_course_hider_form_semester_type',
            get_string('semestertype', 'block_course_hider'),
            get_string('semestertypedesc', 'block_course_hider'),
            'First,Second' // Default.
        )
    );

    // Semesters.
    $settings->add(
        new admin_setting_configtext(
            'block_course_hider_form_semester',
            get_string('semester', 'block_course_hider'),
            get_string('semesterdesc', 'block_course_hider'),
            'Spring,Summer,Fall,Winter' // Default.
        )
    );
    // Semesters Section.
    $settings->add(
        new admin_setting_configtext(
            'block_course_hider_form_semester_section',
            get_string('semestersection', 'block_course_hider'),
            get_string('semestersectiondesc', 'block_course_hider'),
            '(B),(C)' // Default.
        )
    );
}


$ADMIN->add('courses', new admin_externalpage('coursehider', get_string('hidecourses', 'block_course_hider'), "$CFG->wwwroot/blocks/course_hider/course_hider.php"));
