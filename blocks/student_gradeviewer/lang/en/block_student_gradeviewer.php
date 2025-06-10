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
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Adam Zapletal, Philip Cali, Jason Peak, Chad Mazilly, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General block strings.
$string['pluginname'] = 'Mentor Assignment';
$string['admin'] = 'Administration';

// View grades.
$string['viewgrades'] = 'Grades Overview for {$a}';
$string['viewgrades_help'] = 'If the student is enrolled in any courses, the
list of the courses will show up below. The current final grade for each
course is located to the right of the course name. If the grade appears as "-",
then there is currently no grade available for the course.

Each course can be clicked on to get an in-depth look at the course gradebook
and how the student ranks in the class.';

// Event Strings.
$string['athletic'] = 'Athletic';
$string['username'] = 'Username';
$string['idnumber'] = 'ID number';
$string['firstname'] = 'First name';
$string['lastname'] = 'Surname';
$string['specified_sport'] = 'Sport';
$string['user_reg_status'] = 'Registration';
$string['user_year'] = 'Year';
$string['user_college'] = 'College';
$string['user_major'] = 'Major';
$string['user_keypadid'] = 'Keypad ID';

// Error strings.
$string['no_permission'] = 'You do not have the correct permission to view this page. If you think that you should, please contact the Moodle administrator.';
$string['no_courses'] = '{$a} is not enrolled in any courses.';

// Administration strings.
$string['admin_person_mentor'] = 'Assign Students to Mentors';
$string['admin_sports_mentor'] = 'Assign Athletic Mentors';
$string['admin_academic_mentor'] = 'Assign Course Mentors';

$string['na_sports'] = 'Do not assign to a specific sport';
$string['na_person'] = 'Select a mentor to assign to students to.';

$string['assigning_to'] = 'Assigning mentors to: {$a}';
$string['assigning_students'] = 'Assigning students to: {$a}';

$string['selected'] = 'Selected';
$string['available'] = 'Available';

$string['admin_mentor'] = 'Mentor Assignment';

// Settings strings.
$string['role'] = 'Role assignment';
$string['role_help'] = 'Below are configured role mappings used by the Student Grade Viewer when assigning mentors to their respective areas of concern.';

$string['academic_mentor'] = 'Academic Mentor';
$string['academic_mentor_help'] = 'Academic mentors focus on course departments and mentor the students within.';

$string['sports_mentor'] = 'Athletic Mentor';
$string['sports_mentor_help'] = 'Athletic mentors focus on students in sports or inidividuals.';

$string['academic_admin'] = 'Academic Administrator';
$string['academic_admin_help'] = 'Academic administrators assign users to be Academic mentors';

$string['sports_admin'] = 'Athletic Administrator';
$string['sports_admin_help'] = 'Athletic administrators assign users to be Athletic mentors to sport categories or student athletes';

// Capability strings.
$string['student_gradeviewer:viewgrades'] = 'Allows users to use the Student Grade Viewer';
$string['student_gradeviewer:sportsgrades'] = 'Allows users to use the Student Grade Viewer for athletes';
$string['student_gradeviewer:academicadmin'] = 'Allows users to add mentors in academic categories';
$string['student_gradeviewer:sportsadmin'] = 'Allows users to add mentors in sports categories';
$string['student_gradeviewer:myaddinstance'] = 'Add Student Gradeviewer to My Page';
$string['student_gradeviewer:addinstance'] = 'Add Student Gradeviewer to My Page';
