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
 * Definition of core scheduled tasks.
 *
 * The handlers defined on this file are processed and registered into
 * the Moodle DB after any install or upgrade operation. All plugins
 * support this.
 *
 * @package   core
 * @category  task
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* List of handlers */

$tasks = array(
    array(
        'classname' => 'core\task\session_cleanup_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\delete_unconfirmed_users_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\delete_incomplete_users_task',
        'blocking' => 0,
        'minute' => '5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\backup_cleanup_task',
        'blocking' => 0,
        'minute' => '10',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\tag_cron_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '3',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\context_cleanup_task',
        'blocking' => 0,
        'minute' => '25',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\cache_cleanup_task',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\messaging_cleanup_task',
        'blocking' => 0,
        'minute' => '35',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\send_new_user_passwords_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\send_failed_login_notifications_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\create_contexts_task',
        'blocking' => 1,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\grade_cron_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\grade_history_cleanup_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\completion_regular_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\completion_daily_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => 'R',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\portfolio_cron_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\plagiarism_cron_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\calendar_cron_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\blog_cron_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\question_preview_cleanup_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\registration_cron_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => 'R',
        'day' => '*',
        'dayofweek' => 'R',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\check_for_updates_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => 'R',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\cache_cron_task',
        'blocking' => 0,
        'minute' => '50',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\automated_backup_task',
        'blocking' => 0,
        'minute' => '50',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\badges_cron_task',
        'blocking' => 0,
        'minute' => '*/5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\badges_message_task',
        'blocking' => 0,
        'minute' => '*/5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\file_temp_cleanup_task',
        'blocking' => 0,
        'minute' => '55',
        'hour' => '*/6',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\file_trash_cleanup_task',
        'blocking' => 0,
        'minute' => '55',
        'hour' => '*/6',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\search_index_task',
        'blocking' => 0,
        'minute' => '*/30',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\search_optimize_task',
        'blocking' => 0,
        'minute' => '15',
        'hour' => '*/12',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\stats_cron_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\password_reset_cleanup_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*/6',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\complete_plans_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\sync_plans_from_template_cohorts_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core_files\task\conversion_cleanup_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '2',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\oauth2\refresh_system_tokens_task',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\analytics_cleanup_task',
        'blocking' => 0,
        'minute' => '42',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\task_log_cleanup_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => 'R',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\h5p_get_content_types_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => 'R',
        'day' => '1',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\h5p_clean_orphaned_records_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => 'core\task\antivirus_cleanup_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
    ),
    array(
        'classname' => 'core_reportbuilder\task\send_schedules',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ),
    [
        'classname' => 'core\task\task_lock_cleanup_task',
        'blocking' => 0,
        'minute' => '*/10',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ],
    [
        'classname' => 'core_xapi\task\state_cleanup_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => '0',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ],
    [
        'classname' => 'core\task\show_started_courses_task',
        'blocking' => 0,
        'minute' => '00',
        'hour' => '01',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => true,
    ],
    [
        'classname' => 'core\task\hide_ended_courses_task',
        'blocking' => 0,
        'minute' => '00',
        'hour' => '01',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => true,
    ],
    [
        'classname' => 'core_communication\task\synchronise_providers_task',
        'blocking' => 0,
        'minute' => 'R',
        'hour' => 'R',
        'day' => '*',
        'dayofweek' => 'R',
        'month' => '*',
    ],
    [
        'classname' => 'core\task\automated_backup_report_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'core\task\update_geoip2file_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => 'R',
        'day' => '*',
        'month' => '*',
        'dayofweek' => 'R',
        'disabled' => true,
    ],
    [
        'classname' => 'core\task\stored_progress_bar_cleanup_task',
        'blocking' => 0,
        'minute' => '00',
        'hour' => '01',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false,
    ],
);
