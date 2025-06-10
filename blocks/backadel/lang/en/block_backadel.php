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
 * @package    block_backadel
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Strings for block.
$string['backup_and_delete'] = 'Backup And Delete';
$string['block_index'] = 'Backup';
$string['block_delete'] = 'Delete';
$string['block_pending'] = 'Pending';
$string['block_config'] = 'Config';
$string['block_failed'] = 'Failures';
$string['block_large_backups'] = 'Large Backups';
$string['backing_up'] = 'Backing Up';
$string['backadel_settings'] = 'Backup and Delete Settings';
$string['status_running'] = 'Running: {$a}.';
$string['cron_backup_error'] = 'Error backing up {$a}';
$string['status_not_running'] = 'Not Running';
$string['cron_already_running'] = 'Backadel claims it has been running for {$a} minute(s), but the task manager disagrees.';
$string['backuptask'] = 'Backup job';

// Stings shared by pages.
$string['pluginname'] = 'Backup And Delete';
$string['blockname'] = 'Backup And Delete';
$string['need_permission'] = 'You do not have permission to view this page';
$string['toggle_all'] = 'Select All/None';

// Strings for index.php.
$string['build_search'] = 'Build Search';
$string['saved_searches'] = 'Saved Searches';
$string['no_searches'] = 'There are no saved searches at this time';
$string['match'] = 'Match';
$string['of_these_constraints'] = 'of these constraints';
$string['build_search_button'] = 'Build Search Query';
$string['search_name'] = 'Search Name';
$string['created_at'] = 'Created At';
$string['run_query_button'] = 'Run Saved Query';
$string['upload_a_file'] = 'Upload A File';
$string['upload'] = 'Upload';
$string['cancel'] = 'Cancel';
$string['delete_queries_link'] = 'Select Saved Queries for Deletion';
$string['course_id'] = 'Course ID #';
$string['is'] = 'is';
$string['is_not'] = 'is not';
$string['contains'] = 'contains';
$string['does_not_contain'] = 'does not contain';
$string['name_missing'] = 'Please select a name for this query';
$string['term_missing'] = 'Please select at least one search term for this constraint';
$string['search_missing'] = 'Please select a saved search';

// Strings for backup.php.
$string['backup'] = 'Select Backup Courses';
$string['backup_button'] = 'Backup Selected Courses';

// Strings for results.php.
$string['search_results'] = 'Search Results';
$string['save_query'] = 'Save Query';
$string['create_new_query'] = 'Create New Query';

// Strings for delete.php.
$string['delete'] = 'Delete?';
$string['delete_header'] = 'Completed Backups';
$string['deleted'] = 'Successfully deleted {$a}';
$string['delete_error'] = ', but there may have been an error, please check';
$string['none_completed'] = 'There are no completed backups at this time';
$string['delete_button'] = 'Delete Selected Courses';

// Strings for delete_queries.php.
$string['delete_queries_header'] = 'Delete Saved Queries';
$string['delete_queries_button'] = 'Delete Selected Queries';
$string['delete_queries_success'] = '{$a} successful query deletion(s)';

// Strings for send_job.php.
$string['job_sent'] = 'Backup Job Sent';
$string['job_sent_body'] = 'Your backup job will start during the next cron run. ' .
    'You will receive an email when all backups are complete.';
$string['already_successful'] = ' was not scheduled for backup because was ' .
    'already successfully backed up but never deleted.';
$string['already_scheduled'] = ' was not scheduled for backup because it is ' .
    'already scheduled for backup.';
$string['already_failed'] = ' was not scheduled for backup because it is an ' .
    'unresolved failure. Please fix this.';

// String for failed.php.
$string['failed_header'] = 'Failed Backups';
$string['none_failed'] = 'There are no failed backups at this time';
$string['failed_button'] = 'Reschedule Selected Backups';
$string['failed'] = 'Failed?';
$string['statuses_updated'] = 'Selected courses have been rescheduled for backup';

// Strings for settings.php.
$string['config_path'] = 'Storage Path';
$string['config_path_desc'] = 'Relative to {$a}, include the surrounding slashes.
    Ensure that this directory is created and writable.';
$string['config_pattern'] = 'Archive suffix';
$string['config_pattern_desc'] = 'Data that will be appended onto backup names';
$string['config_roles'] = 'Roles';
$string['config_roles_desc'] = 'Roles to include when naming backup files';
$string['config_size_limit'] = 'Size limit before warning';
$string['config_size_limit_desc'] = 'In megabytes';
$string['path_error'] = 'Error: Please ensure that the path you provided is a ' .
    'writable directory';
$string['sched_config'] = 'Access scheduled backup settings as
    (' . $string['pluginname'] . ') uses these settings.';
$string['here'] = 'here';
$string['config_path_not_exists'] = 'The path you have entered does not exists.';
$string['config_path_not_writable'] = 'The path you have entered is not writable.';
$string['config_path_surround'] = 'Surround the path with slashes.';

// Strings for email.
$string['email_subject'] = 'Backup Job Completed';
$string['email_from'] = 'noreply@lsu.edu';
$string['email_body']  = "The Backup And Delete tool has completed the jobs in it's queue.";

// Capabilities.
$string['backadel:addinstance'] = 'Add '.$string['pluginname'].' block.';
