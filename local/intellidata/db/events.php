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
 * Add event handlers for the local intellidata
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die;

$observers = [
    // Users events.
    [
        'eventname' => '\core\event\user_created',
        'callback' => '\local_intellidata\entities\users\observer::user_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\config_log_created',
        'callback' => '\local_intellidata\entities\config_log_observer::config_log_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_updated',
        'callback' => '\local_intellidata\entities\users\observer::user_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_deleted',
        'callback' => '\local_intellidata\entities\users\observer::user_deleted',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_loggedin',
        'callback' => '\local_intellidata\entities\users\observer::user_loggedin',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_loggedout',
        'callback' => '\local_intellidata\entities\users\observer::user_loggedout',
        'priority' => 1,
    ],

    // Users Auth events.
    [
        'eventname' => '\core\event\user_loggedin',
        'callback' => '\local_intellidata\entities\userlogins\observer::user_loggedin',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_loggedout',
        'callback' => '\local_intellidata\entities\userlogins\observer::user_loggedout',
        'priority' => 1,
    ],

    // Categories events.
    [
        'eventname' => '\core\event\course_category_created',
        'callback' => '\local_intellidata\entities\categories\observer::course_category_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_category_updated',
        'callback' => '\local_intellidata\entities\categories\observer::course_category_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_category_deleted',
        'callback' => '\local_intellidata\entities\categories\observer::course_category_deleted',
        'priority' => 1,
    ],

    // Courses events.
    [
        'eventname' => '\core\event\course_created',
        'callback' => '\local_intellidata\entities\courses\observer::course_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_updated',
        'callback' => '\local_intellidata\entities\courses\observer::course_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_deleted',
        'callback' => '\local_intellidata\entities\courses\observer::course_deleted',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_restored',
        'callback' => '\local_intellidata\entities\courses\observer::course_restored',
        'priority' => 1,
    ],

    // Enrollments events.
    [
        'eventname' => '\core\event\user_enrolment_created',
        'callback' => '\local_intellidata\entities\enrolments\observer::user_enrolment_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_enrolment_updated',
        'callback' => '\local_intellidata\entities\enrolments\observer::user_enrolment_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback' => '\local_intellidata\entities\enrolments\observer::user_enrolment_deleted',
        'priority' => 1,
    ],

    // Roles events.
    [
        'eventname' => '\core\event\role_assigned',
        'callback' => '\local_intellidata\entities\roles\observer::role_assigned',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\role_unassigned',
        'callback' => '\local_intellidata\entities\roles\observer::role_unassigned',
        'priority' => 1,
    ],

    // Course completion events.
    [
        'eventname' => '\core\event\course_completed',
        'callback' => '\local_intellidata\entities\coursecompletions\observer::course_completed',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_completion_updated',
        'callback' => '\local_intellidata\entities\coursecompletions\observer::course_completion_updated',
        'priority' => 1,
    ],

    // Activities events.
    [
        'eventname' => '\core\event\course_module_created',
        'callback' => '\local_intellidata\entities\activities\observer::course_module_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_module_updated',
        'callback' => '\local_intellidata\entities\activities\observer::course_module_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback' => '\local_intellidata\entities\activities\observer::course_module_deleted',
        'priority' => 1,
    ],

    // Activity completion.
    [
        'eventname' => '\core\event\course_module_completion_updated',
        'callback' => '\local_intellidata\entities\activitycompletions\observer::course_module_completion_updated',
        'priority' => 1,
    ],

    // User grades events.
    [
        'eventname' => '\core\event\user_graded',
        'callback' => '\local_intellidata\entities\usergrades\observer::user_graded',
        'priority' => 1,
    ],

    // Grade letter updated events.
    [
        'eventname' => '\core\event\grade_letter_updated',
        'callback' => '\local_intellidata\entities\usergrades\observer::grade_letter_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\grade_letter_created',
        'callback' => '\local_intellidata\entities\usergrades\observer::grade_letter_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\grade_letter_deleted',
        'callback' => '\local_intellidata\entities\usergrades\observer::grade_letter_deleted',
        'priority' => 1,
    ],

    // Forum discussions.
    [
        'eventname' => '\mod_forum\event\discussion_created',
        'callback' => '\local_intellidata\entities\forums\observer::discussion_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_forum\event\discussion_updated',
        'callback' => '\local_intellidata\entities\forums\observer::discussion_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_forum\event\discussion_moved',
        'callback' => '\local_intellidata\entities\forums\observer::discussion_moved',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_forum\event\discussion_deleted',
        'callback' => '\local_intellidata\entities\forums\observer::discussion_deleted',
        'priority' => 1,
    ],

    // Forum posts.
    [
        'eventname' => '\mod_forum\event\post_created',
        'callback' => '\local_intellidata\entities\forums\observer::post_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_forum\event\post_updated',
        'callback' => '\local_intellidata\entities\forums\observer::post_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_forum\event\post_deleted',
        'callback' => '\local_intellidata\entities\forums\observer::post_deleted',
        'priority' => 1,
    ],

    // Quiz Attempt.
    [
        'eventname' => '\mod_quiz\event\attempt_started',
        'callback' => '\local_intellidata\entities\quizzes\observer::attempt_started',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => '\local_intellidata\entities\quizzes\observer::attempt_submitted',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_deleted',
        'callback' => '\local_intellidata\entities\quizzes\observer::attempt_deleted',
        'priority' => 1,
    ],

    // Quiz questions events.
    [
        'eventname' => '\core\event\question_created',
        'callback' => '\local_intellidata\entities\quizquestions\observer::question_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\question_updated',
        'callback' => '\local_intellidata\entities\quizquestions\observer::question_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\question_deleted',
        'callback' => '\local_intellidata\entities\quizquestions\observer::question_deleted',
        'priority' => 1,
    ],

    // Quiz question relations events.
    [
        'eventname' => '\mod_quiz\event\slot_deleted',
        'callback' => '\local_intellidata\entities\quizquestionrelations\observer::slot_deleted',
        'priority' => 1,
    ],

    // Assignment Submissions.
    [
        'eventname' => '\mod_assign\event\submission_created',
        'callback' => '\local_intellidata\entities\assignments\observer::submission_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_assign\event\submission_updated',
        'callback' => '\local_intellidata\entities\assignments\observer::submission_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_assign\event\submission_duplicated',
        'callback' => '\local_intellidata\entities\assignments\observer::submission_duplicated',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_assign\event\submission_graded',
        'callback' => '\local_intellidata\entities\assignments\observer::submission_graded',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => '\local_intellidata\entities\assignments\observer::assessable_submitted',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_assign\event\submission_status_updated',
        'callback' => '\local_intellidata\entities\assignments\observer::submission_status_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\mod_assign\event\submission_status_viewed',
        'callback' => '\local_intellidata\entities\assignments\observer::submission_status_viewed',
        'priority' => 1,
    ],

    // Cohorts events.
    [
        'eventname' => '\core\event\cohort_created',
        'callback' => '\local_intellidata\entities\cohorts\observer::cohort_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\cohort_updated',
        'callback' => '\local_intellidata\entities\cohorts\observer::cohort_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\cohort_deleted',
        'callback' => '\local_intellidata\entities\cohorts\observer::cohort_deleted',
        'priority' => 1,
    ],

    // Course sections events.
    [
        'eventname' => '\core\event\course_module_created',
        'callback' => '\local_intellidata\entities\coursesections\observer::course_module_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_section_created',
        'callback' => '\local_intellidata\entities\coursesections\observer::course_section_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_section_updated',
        'callback' => '\local_intellidata\entities\coursesections\observer::course_section_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_section_deleted',
        'callback' => '\local_intellidata\entities\coursesections\observer::course_section_deleted',
        'priority' => 1,
    ],

    // Cohort members events.
    [
        'eventname' => '\core\event\cohort_member_added',
        'callback' => '\local_intellidata\entities\cohortmembers\observer::cohort_member_added',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\cohort_member_removed',
        'callback' => '\local_intellidata\entities\cohortmembers\observer::cohort_member_removed',
        'priority' => 1,
    ],

    // Grade Item events.
    [
        'eventname' => '\core\event\grade_item_deleted',
        'callback' => '\local_intellidata\entities\gradeitems\observer::grade_item_deleted',
        'priority' => 1,
    ],

    // Groups events.
    [
        'eventname' => '\core\event\group_created',
        'callback' => '\local_intellidata\entities\groups\observer::group_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\group_deleted',
        'callback' => '\local_intellidata\entities\groups\observer::group_deleted',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\group_updated',
        'callback' => '\local_intellidata\entities\groups\observer::group_updated',
        'priority' => 1,
    ],

    // Group members events.
    [
        'eventname' => '\core\event\group_member_removed',
        'callback' => '\local_intellidata\entities\groupmembers\observer::group_member_removed',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\group_member_added',
        'callback' => '\local_intellidata\entities\groupmembers\observer::group_member_added',
        'priority' => 1,
    ],

    // Participations.
    [
        'eventname' => '*',
        'callback' => '\local_intellidata\entities\participations\observer::new_participation',
        'priority' => 1,
    ],

    // Tracking.
    [
        'eventname' => '\core\event\user_deleted',
        'callback' => '\local_intellidata\entities\usertrackings\observer::user_deleted',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_deleted',
        'callback' => '\local_intellidata\entities\usertrackings\observer::course_deleted',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback' => '\local_intellidata\entities\usertrackings\observer::course_module_deleted',
        'priority' => 1,
    ],

    // User info categories events.
    [
        'eventname' => '\core\event\user_info_category_created',
        'callback' => '\local_intellidata\entities\userinfocategories\observer::user_info_category_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_info_category_updated',
        'callback' => '\local_intellidata\entities\userinfocategories\observer::user_info_category_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_info_category_deleted',
        'callback' => '\local_intellidata\entities\userinfocategories\observer::user_info_category_deleted',
        'priority' => 1,
    ],

    // User info fields events.
    [
        'eventname' => '\core\event\user_info_field_created',
        'callback' => '\local_intellidata\entities\userinfofields\observer::user_info_field_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_info_field_updated',
        'callback' => '\local_intellidata\entities\userinfofields\observer::user_info_field_updated',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_info_field_deleted',
        'callback' => '\local_intellidata\entities\userinfofields\observer::user_info_field_deleted',
        'priority' => 1,
    ],

    // User info data events.
    [
        'eventname' => '\core\event\user_created',
        'callback' => '\local_intellidata\entities\userinfodatas\observer::user_created',
        'priority' => 1,
    ],
    [
        'eventname' => '\core\event\user_updated',
        'callback' => '\local_intellidata\entities\userinfodatas\observer::user_updated',
        'priority' => 1,
    ],

    // Track Logs to generate custom logs datatypes.
    [
        'eventname' => '*',
        'callback' => '\local_intellidata\entities\logs\observer::log_created',
        'priority' => 1,
    ],

    // Track any deleted event in a system.
    [
        'eventname' => '*',
        'callback' => '\local_intellidata\observers\record_deleted::execute',
        'priority' => 1,
    ],
];
