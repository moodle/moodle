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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Simple Restore';
$string['simple_restore:canrestore'] = 'Users can use the Simple Restore';
$string['simple_restore:canrestorearchive'] = 'Users can use the Simple Restore for course archives';
$string['block/simple_restore:addinstance'] = 'Add Simple Restore block to course';
$string['delete_restore'] = 'Overwrite current course';
$string['restore_course'] = 'Import all materials into current course';
$string['restore_course_archive'] = 'Create new course from backup';
$string['no_course'] = 'No course found for id: {$a}';
$string['no_context'] = 'Empty context: A restore object must be given a context.';
$string['no_file'] = 'Empty file: A restore object must be given a file.';
$string['no_filter'] = 'Enter a valid course shortname.';
$string['no_arguments'] = $string['pluginname'] . ' error: Invalid arguments were passed to prep restore.';
$string['no_restore'] = 'The restore was unable to complete due to the following error: {$a}';
$string['empty_backups'] = 'No course backups found.';
$string['have_grades'] = 'This course cannot use Simple Restore now that there are grades in the gradebook. Selective Import is available.';

$string['backup_name'] = 'Backup name / ';
$string['restore_stopped'] = 'Restore Stopped!';

$string['contains'] = 'contains';
$string['startswith'] = 'starts with';
$string['endswith'] = 'ends with';
$string['equals'] = 'equals';
$string['adminfilter'] = 'Filter Courses';

$string['semester_backups'] = 'Semester Backups';

$string['confirm_message'] = 'You are about to {$a}. Proceed?';
$string['general'] = 'General Restore Settings';
$string['general_desc'] = 'These are general restore settings to be applied to any selected backup.';
$string['course'] = 'Course Specific Settings';
$string['course_desc'] = 'These are course restore settings to be applied to any selected backup (when necessary).';
$string['enrol_migratetomanual'] = 'Migrate enrollments';
$string['users'] = 'Restore users';
$string['user_files'] = 'Restore user files';
$string['role_assignments'] = 'Restore roles';
$string['activities'] = 'Restore activities';
$string['blocks'] = 'Restore blocks';
$string['filters'] = 'Restore filters';
$string['comments'] = 'Restore comments';
$string['userscompletion'] = 'Restore user completions';
$string['logs'] = 'Restore course logs';
$string['grade_histories'] = 'Restore grade histories';
$string['keep_roles_and_enrolments'] = 'Keep current roles and enrolments';
$string['keep_groups_and_groupings'] = 'Keep current groups and groupings';
$string['overwrite_conf'] = 'Overwrite course configuration';

$string['module'] = 'Module Restore Settings';
$string['module_desc'] = 'These are module restore settings to be applied to any selected course where applicable.';
$string['section'] = 'Section';
$string['restore_included'] = 'Restore {$a}';
$string['restore_userinfo'] = 'Restore {$a} user data';

$string['is_archive_server'] = 'Archive Server Mode';
$string['is_archive_server_desc'] = 'Archive Server Mode determines whether or not the block will be available on the My page or in Course pages, and alters the functionality to suit the needs of a read-only archive server.';
$string['archive_restore'] = 'Restore Archived Course';
$string['simple_restore:myaddinstance'] = 'Add Simple restore block to My page';
$string['simple_restore:addinstance'] = 'Add Simple restore block to Site page';

// Async Settings.
$string['async_title'] = 'Async Restore Settings';
$string['async_toggle_title'] = 'Async Restore';
$string['async_toggle_desc'] = 'Make course restores asyncronous.';
