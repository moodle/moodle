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
 * Language strings for Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'WDS Sports Grades';
$string['sportsgrades:addinstance'] = 'Add a new Sports Grades block';
$string['sportsgrades:myaddinstance'] = 'Add a new Sports Grades block to Dashboard';
$string['sportsgrades:view'] = 'View Sports Grades block';
$string['sportsgrades:viewgrades'] = 'View student grades';
$string['noaccess'] = 'You do not have access to view this block.';
$string['block_title'] = 'WDS Sports Grades';
$string['page_title'] = 'WDS Sports Grades Search';

// Search form
$string['search_title'] = 'Search Student Athletes';
$string['search_placeholder'] = 'Search...';
$string['search_button'] = 'Search';
$string['search_advanced'] = 'Advanced Search';
$string['search_advanced_hide'] = 'Hide Advanced Search';
$string['search_universal_id'] = 'Universal ID';
$string['search_username'] = 'Username';
$string['search_firstname'] = 'First Name';
$string['search_lastname'] = 'Last Name';
$string['search_major'] = 'Major';
$string['search_classification'] = 'Classification';
$string['search_sport'] = 'Sport';
$string['search_sport_all'] = 'All Sports';
$string['search_results'] = 'Search Results';
$string['search_no_results'] = 'No students found matching your criteria.';
$string['search_error'] = 'An error occurred while searching.';
$string['search_page_link'] = 'Sports Grades';
$string['direct_link'] = 'Direct Link';

// Results table
$string['result_username'] = 'Username';
$string['result_universal_id'] = 'Universal ID';
$string['result_firstname'] = 'First Name';
$string['result_lastname'] = 'Last Name';
$string['result_college'] = 'College';
$string['result_major'] = 'Major';
$string['result_classification'] = 'Classification';
$string['result_sports'] = 'Sports';
$string['result_view_grades'] = 'View Grades';

// Grade display
$string['grade_title'] = 'Grades for {$a}';
$string['grade_course'] = 'Course';
$string['grade_final'] = 'Final Grade';
$string['grade_letter'] = 'Letter Grade';
$string['grade_details'] = 'Grade Details';
$string['grade_item'] = 'Grade Item';
$string['grade_weight'] = 'Category / Item Weight';
$string['grade_value'] = 'Grade';
$string['grade_percentage'] = 'Percentage';
$string['grade_contribution'] = 'Contribution to Final Grade';
$string['grade_no_courses'] = 'No courses found for this student.';
$string['grade_no_items'] = 'No grade items found for this course.';
$string['grade_loading'] = 'Loading grades...';
$string['grade_back_to_results'] = 'Back to search results';
$string['grade_section'] = 'Section';
$string['grade_term'] = 'Term';

// Permissions.
$string['wds_sportsgrades:addinstance'] = 'Add WDS Sports Grade Block';
$string['wds_sportsgrades:myaddinstance'] = 'Add WDS Sports Grade to /my';
$string['wds_sportsgrades:view'] = 'View WDS Sports Grade block';
$string['wds_sportsgrades:manageaccess'] = 'Manage Sports Grade Access';
$string['wds_sportsgrades:viewgrades'] = 'View Student Sports Grades';

// Privacy
$string['privacy:metadata:block_wds_sportsgrades_cache'] = 'Temporarily stores grade information for performance.';
$string['privacy:metadata:block_wds_sportsgrades_cache:studentid'] = 'The ID of the student whose grades are cached.';
$string['privacy:metadata:block_wds_sportsgrades_cache:data'] = 'The cached grade data.';
$string['privacy:metadata:block_wds_sportsgrades_cache:timecreated'] = 'When this cache was created.';
$string['privacy:metadata:block_wds_sportsgrades_cache:timeexpires'] = 'When this cache expires.';

$string['privacy:metadata:block_wds_sportsgrades_access'] = 'Stores user access information for the Sports Grades block.';
$string['privacy:metadata:block_wds_sportsgrades_access:userid'] = 'The ID of the user who has access.';
$string['privacy:metadata:block_wds_sportsgrades_access:sportid'] = 'The ID of the sport the user has access to.';
$string['privacy:metadata:block_wds_sportsgrades_access:timecreated'] = 'When this access was created.';
$string['privacy:metadata:block_wds_sportsgrades_access:timemodified'] = 'When this access was last modified.';
$string['privacy:metadata:block_wds_sportsgrades_access:createdby'] = 'Who created this access.';
$string['privacy:metadata:block_wds_sportsgrades_access:modifiedby'] = 'Who last modified this access.';

// User management.
$string['wdsaddusertitle'] = 'Manage Sport Associations';
$string['manageaccess'] = 'Sport Mentors';
$string['adduser'] = 'Add User';
$string['removeuser'] = 'Remove Users';
$string['sport'] = 'Sport';
$string['all_sports'] = 'All Sports';

// Settings.
$string['wds_sportsgrades:pluginsettings'] = 'Plugin Settings';
$string['wds_sportsgrades:adminaccessall'] = 'Admin Access All Sports';
$string['wds_sportsgrades:adminaccessall_desc'] = 'Moodle admins can access all sports and athletes.';
$string['wds_sportsgrades:daysprior'] = 'Days Prior';
$string['wds_sportsgrades:daysprior_desc'] = 'Number of days prior to the start of classes for the period.';
$string['wds_sportsgrades:daysafter'] = 'Days After';
$string['wds_sportsgrades:daysafter_desc'] = 'Number of days after the last day of classes for the period.';

// Mentor management.
$string['filter'] = 'Filter students';
$string['assignmentors'] = 'Student Mentors';
$string['assignmentor'] = 'Assign Mentor';
$string['mentor'] = 'Mentor';
$string['students'] = 'Students';
$string['removementor'] = 'Remove student assignment from mentor';
$string['wds_sportsgrades:assignmentors'] = 'Assign students to mentors';
$string['wds_sportsgrades:nostudents'] = 'No students found with provided search term.';
