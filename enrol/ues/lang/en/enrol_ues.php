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
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'UES Enrollment';
$string['pluginname_desc'] = 'The UES (Universal Enrollment Service) module is a pluggable enrollment system that adheres to common university criterion including Semesters, Courses, Sections tied to coures, and teacher and student enrollment tied to Sections.<br><br>UES will load any enrollment provider that handles the `ues_list_provider`. A fully defined provider will show up in the dropdown below.<br><br>UES is a scheduled task within Moodle and can be managed by going to: Site Administration > Server > Scheduled Tasks';

$string['full_process_task'] = 'UES - Full Process';

$string['semester_cleanup'] = 'Semester Cleanup';
$string['reprocess_failures'] = 'Reprocess Failures';

$string['run_adhoc'] = 'Run Adhoc';
$string['run_adhoc_desc'] = 'This will queue the UES process to run as soon as possible.';
$string['run_adhoc_success'] = 'This task has been queued successfully.';
$string['run_adhoc_status_disabled'] = 'disabled';
$string['run_adhoc_status_enabled'] = 'enabled';
$string['run_adhoc_last_run_time'] = 'The last time it ran as per schedule was: <strong>{$a}</strong>';
$string['run_adhoc_next_run_time'] = 'It is scheduled to run next at: <strong>{$a}</strong>';
$string['run_adhoc_scheduled_task_details'] = 'This scheduled task is currently: <strong>{$a->status}</strong><br><br>{$a->last}<br><br>{$a->next}';
$string['run_adhoc_confirm_msg'] = 'Are you sure you want to queue this task to run as soon as possible? <strong>Note:</strong> The task\'s schedule will not be changed.';

$string['reprocess_count'] = 'Found {$a} error(s)';

$string['reprocess'] = 'Reprocess';
$string['reprocess_all'] = $string['reprocess'] . ' All';
$string['reprocess_selected'] = $string['reprocess'] . ' Selected';
$string['reprocess_success'] = 'Reprocessing errors';

$string['delete'] = 'Delete';
$string['delete_all'] = $string['delete'] . ' All';
$string['delete_selected'] = $string['delete'] . ' Selected';
$string['delete_success'] = 'Successfully deleted errors';

$string['no_errors'] = 'Congratulations! You have handled all the enrollment errors.';

$string['already_running'] = 'UES did not run, but it was supposed to. UES may have failed unexpectedly in the last run, or there may have been an error during the enrollment process. An admin should disable the running status by going to Settings -> Site Administration -> Plugins -> Enrolments -> UES Enrollment or {$a}. Once enabled, UES will run as expected.';
$string['within_grace_period'] = 'UES was scheduled to run, however, not enough time has elapsed since the last scheduled run as per the plugin\'s _Grace Period_ setting. Either modify the scheduled task\'s frequency at: Site Administration -> Server -> Scheduled Tasks or an admin should modify the grace period by going to Settings -> Site Administration -> Plugins -> Enrolments -> UES Enrollment or {$a}';

$string['sub_days'] = 'Semester in Day Range';
$string['sub_days_desc'] = 'How many days in the past (and future) should UES query
the semester source. This might be important for installing the system for the first
time.';

$string['could_not_enroll'] = 'Could not process enrollment for courses in {$a->year} {$a->name} {$a->campus} {$a->session_key}. Consider changing the Process by Department setting';

$string['recover_grades'] = 'Recover Grades';
$string['recover_grades_desc'] = 'Recover grade history grades on enrollment, if grades were present on unenrollment.';

$string['suspend_enrollment'] = 'Inactivate Enrollment';
$string['suspend_enrollment_desc'] = 'Inactivate enrollment instead of un-enrolling students.';

$string['running'] = 'Currently Running';
$string['running_desc'] = 'If this is checked then it either means that the process is still running, or the process died unexpectedly. Uncheck this if you think the process should be enabled.

__Note__: One of the easiest ways to know the process has ended is to enable email logs.';

$string['ignore'] = 'Ignore';
$string['please_note'] = 'The following semesters were selected: {$a}';

$string['be_ignored'] = '{$a} - will be ignored';
$string['be_recoged'] = '{$a} - will be recognized';

$string['grace_period'] = 'Grace Period';
$string['grace_period_desc'] = 'Wait this long (in seconds) after the scheduled task\'s last run before running again. Typically, an hour is long enough, but some runs may exceed an hour.';

$string['error_threshold'] = 'Error Threshold';
$string['error_threshold_desc'] = 'The process will only automatically reprocess errors that occurred during the cron run whose numbers are less than or equal to the specified threshold.

__Note__: This setting only applies if _scheduled task_ is enabled';

$string['error_threshold_log'] = 'There are too many errors to reprocess automatically. Either clear out the error queue through the settings page, or raise the threshold number.';

$string['error_params'] = 'Parameters';
$string['error_when'] = 'Timestamp';
$string['error_shortname'] = 'Tried to create a course, but failed because the course appears to have already been created: {$a->shortname}';

$string['error_no_group'] = 'UES tried to add someone to the deleted group (name = {$a->name}) for course (id = {$a->courseid}). UES has reason to believe that this group should not be in existence (no more teachers are enrolled). Please verify the UES enrollment data (unmanifested entries) in the selected course, and file a bug report if the data looks sound and should have been manifested.';

$string['semester_ignore'] = 'Semester Ignore';

$string['general_settings'] = 'General Settings';
$string['task_status'] = 'Status';
$string['management'] = 'Internal Links';
$string['management_links'] = '
Below are some internal links to manage the enrollment data.

* ['.$string['semester_cleanup'].']({$a->cleanup_url})
* ['.$string['semester_ignore'].']({$a->ignore_url})
* ['.$string['reprocess_failures'].']({$a->failure_url})
* ['.$string['run_adhoc'].']({$a->adhoc_url})
';

$string['email_report'] = 'Email Logs';
$string['email_report_desc'] = 'Email UES execution log to all admins.

__Note__: Any errors will be reported regardless.';

$string['user_settings'] = 'User Creation Settings';
$string['un'] = 'Username';
$string['em'] = 'E-mail';
$string['use_username_email'] = 'Use Username or email?';
$string['use_username_email_desc'] = 'When fetching user information, use username and generate an email based on the suffix provided below OR use the email address from the webservice and cleanse string provided below to generate a username.';
$string['user_email'] = 'E-mail suffix';
$string['user_email_desc'] = 'The created user will have this email domain appended to their username.';
$string['user_email_cleanse'] = 'E-mail cleanse string';
$string['user_email_cleanse_desc'] = 'The above string will be removed from the email domain.';
$string['user_confirm'] = 'Confirmed';
$string['user_confirm_desc'] = 'The user will be _confirmed_ upon creation.';
$string['user_city'] = 'City/town';
$string['user_city_desc'] = 'The created user will have this default city assigned to them.';
$string['user_country'] = 'Country';
$string['user_country_desc'] = 'The created user will have this default country assigned to them.';
$string['user_auth'] = 'Authentication Method';
$string['user_auth_desc'] = 'The created user will have this authentication method assigned to them.';

$string['course_settings'] = 'Course Creation Settings';
$string['course_visible_desc'] = 'Upon creation the course will be visible to students.';
$string['course_shortname_desc'] = 'Generated Shortname for the course';
$string['course_shortname'] = '{year} {name} {department} {session}{course_number} for {fullname}';
$string['course_fullname_desc'] = 'Generated Fullname for the course';
$string['course_fullname'] = '{year} {name} {department} {session}{course_number} for {fullname}';

$string['course_form_replace'] = 'Replace course form';
$string['course_form_replace_desc'] = 'Displays a more friendly version of the
course form';

$string['course_restricted_fields'] = 'Restricted form fields';
$string['course_restricted_fields_desc'] = 'Will not allow the user to edit the
selected form fields. Fields not listed in the select means there is a capability
to hide the fields

__Note__: If used in conjuction with _Replace course form_, then the selected fields will be hidden.';

$string['bad_field'] = 'This setting cannot be changed.';

$string['provider'] = 'Enrollment Provider';
$string['provider_desc'] = 'This enrollment provider will be used to pull enrollment data.';

$string['process_by_department'] = 'Process by Department';
$string['process_by_section'] = 'Process by Section';
$string['reverse_lookups'] = 'Reverse Lookups';
$string['process_by_department_desc'] = 'This setting will make UES query enrollment by department
instead of sections. For network queries, this option may be more efficient.';

$string['provider_information'] = 'Provider Information';
$string['provider_information_desc'] = '__{$a->name}__ supports the following methods: <ul>{$a->list}</ul>';

$string['provider_problems'] = 'Provider Cannot be Instantiated';
$string['provider_problems_desc'] = '
_{$a->pluginname}_ cannot be instantiated with the current settings.

__Problem__: {$a->problem}

This will cause the enrollment plugin to abort in cron. Please address
these errors.

__Note to Developers__: Consider using the "adv_settings" for server side
validation of settings.';

$string['no_provider'] = 'No Enrollment Provider selected.';

$string['provider_settings'] = '{$a} Settings';

$string['provider_cron_problem'] = 'Could not instantiate {$a->pluginname}: {$a->problem}. Check provider configuration.';
$string['enrollment_unsupported'] = 'Provider does not fully support either
teacher_source() / student_source() or teacher_department_source() / student_department_source()
enrollment source';

$string['enrol_settings'] = 'User Enrollment Settings';
$string['student_role'] = 'Students';
$string['student_role_desc'] = 'UES students will be enrolled in this Moodle role';
$string['editingteacher_role'] = 'Primary Instructor';
$string['editingteacher_role_desc'] = 'UES *primary* teachers will be enrolled in this Moodle role';
$string['teacher_role'] = 'Non-Primary Instructor';
$string['teacher_role_desc'] = 'UES *non-primary* teachers will be enrolled in this Moodle role';

$string['failed_sem'] = 'The following semester does not have an end date: {$a->year} {$a->name} {$a->campus} {$a->session_key}';

$string['no_semester'] = 'The semester you have selected does not exists.';
$string['no_semesters'] = 'There are no semesters in your system. Consider running the enrollment process.';

$string['drop_semester'] = 'Drop {$a->year} {$a->name} {$a->campus} {$a->session_key} and all associated data';
$string['year'] = 'Year';
$string['campus'] = 'Campus';
$string['session_key'] = 'Session';
$string['sections'] = 'Sections';
$string['in_session'] = 'In Session?';
$string['clear_reprocess_task'] = 'Clear UES reprocess data';

// Username / Password settings strings.
$string['uesusername'] = 'UES Username';
$string['uesusername_desc'] = 'UES Username for the webservice from which UES grabs enrollment data.';
$string['uespassword'] = 'UES Password';
$string['uespassword_desc'] = 'UES Password that matches the above username.';
