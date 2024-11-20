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
 * Map the Moodle events to transformers.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer;

/**
 * Return a map of the Moodle events to their transformers.
 *
 * @return array
 */
function get_event_function_map() {
    $availableevents = [
        '\core\event\course_completed' => 'core\course_completed',
        '\core\event\course_viewed' => 'core\course_viewed',
        '\core\event\user_created' => 'core\user_created',
        '\core\event\user_enrolment_created' => 'core\user_enrolment_created',
        '\core\event\user_loggedin' => 'core\user_loggedin',
        '\core\event\user_loggedout' => 'core\user_loggedout',
        '\core\event\course_module_completion_updated' => 'core\course_module_completion_updated',
        '\mod_assign\event\assessable_submitted' => 'mod_assign\assignment_submitted',
        '\mod_assign\event\submission_graded' => 'mod_assign\assignment_graded',
        '\mod_bigbluebuttonbn\event\activity_viewed' => 'mod_bigbluebuttonbn\activity_viewed',
        '\mod_bigbluebuttonbn\event\activity_management_viewed' => 'mod_bigbluebuttonbn\activity_management_viewed',
        '\mod_bigbluebuttonbn\event\live_session_event' => 'mod_bigbluebuttonbn\live_session',
        '\mod_bigbluebuttonbn\event\meeting_created' => 'mod_bigbluebuttonbn\meeting_created',
        '\mod_bigbluebuttonbn\event\meeting_ended' => 'mod_bigbluebuttonbn\meeting_ended',
        '\mod_bigbluebuttonbn\event\meeting_joined' => 'mod_bigbluebuttonbn\meeting_joined',
        '\mod_bigbluebuttonbn\event\meeting_left' => 'mod_bigbluebuttonbn\meeting_left',
        '\mod_bigbluebuttonbn\event\recording_deleted' => 'mod_bigbluebuttonbn\recording_deleted',
        '\mod_bigbluebuttonbn\event\recording_edited' => 'mod_bigbluebuttonbn\recording_edited',
        '\mod_bigbluebuttonbn\event\recording_imported' => 'mod_bigbluebuttonbn\recording_imported',
        '\mod_bigbluebuttonbn\event\recording_protected' => 'mod_bigbluebuttonbn\recording_protected',
        '\mod_bigbluebuttonbn\event\recording_published' => 'mod_bigbluebuttonbn\recording_published',
        '\mod_bigbluebuttonbn\event\recording_unprotected' => 'mod_bigbluebuttonbn\recording_unprotected',
        '\mod_bigbluebuttonbn\event\recording_unpublished' => 'mod_bigbluebuttonbn\recording_unpublished',
        '\mod_bigbluebuttonbn\event\recording_viewed' => 'mod_bigbluebuttonbn\recording_viewed',
        '\mod_book\event\course_module_viewed' => 'mod_book\course_module_viewed',
        '\mod_book\event\chapter_viewed' => 'mod_book\chapter_viewed',
        '\mod_chat\event\course_module_viewed' => 'mod_chat\course_module_viewed',
        '\mod_choice\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_data\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_facetoface\event\cancel_booking' => 'mod_facetoface\cancel_booking',
        '\mod_facetoface\event\course_module_viewed' => 'mod_facetoface\course_module_viewed',
        '\mod_facetoface\event\signup_success' => 'mod_facetoface\signup_success',
        '\mod_facetoface\event\take_attendance' => 'mod_facetoface\take_attendance',
        '\mod_feedback\event\course_module_viewed' => 'mod_feedback\course_module_viewed',
        '\mod_feedback\event\response_submitted' => 'mod_feedback\response_submitted\handler',
        '\mod_folder\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_forum\event\course_module_viewed' => 'mod_forum\course_module_viewed',
        '\mod_forum\event\discussion_created' => 'mod_forum\discussion_created',
        '\mod_forum\event\discussion_viewed' => 'mod_forum\discussion_viewed',
        '\mod_forum\event\post_created' => 'mod_forum\post_created',
        '\mod_forum\event\user_report_viewed' => 'mod_forum\user_report_viewed',
        '\mod_glossary\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_imscp\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_lesson\event\course_module_viewed' => 'mod_lesson\course_module_viewed',
        '\mod_lti\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_page\event\course_module_viewed' => 'mod_page\course_module_viewed',
        '\mod_quiz\event\course_module_viewed' => 'mod_quiz\course_module_viewed',
        '\mod_quiz\event\attempt_abandoned' => 'mod_quiz\attempt_submitted\handler',
        '\mod_quiz\event\attempt_started' => 'mod_quiz\attempt_started',
        '\mod_quiz\event\attempt_reviewed' => 'mod_quiz\attempt_reviewed',
        '\mod_quiz\event\attempt_submitted' => 'mod_quiz\attempt_submitted\handler',
        '\mod_quiz\event\attempt_viewed' => 'mod_quiz\attempt_viewed',
        '\mod_resource\event\course_module_viewed' => 'mod_resource\course_module_viewed',
        '\mod_scorm\event\course_module_viewed' => 'mod_scorm\course_module_viewed',
        '\mod_scorm\event\sco_launched' => 'mod_scorm\sco_launched',
        '\mod_scorm\event\scoreraw_submitted' => 'mod_scorm\scoreraw_submitted',
        '\mod_scorm\event\status_submitted' => 'mod_scorm\status_submitted',
        '\mod_survey\event\course_module_viewed' => 'mod_survey\course_module_viewed',
        '\mod_url\event\course_module_viewed' => 'mod_url\course_module_viewed',
        '\mod_wiki\event\course_module_viewed' => 'all\course_module_viewed',
        '\mod_workshop\event\course_module_viewed' => 'all\course_module_viewed',
        '\totara_program\event\program_assigned' => 'totara_program\program_assigned'
    ];

    $environmentevents = class_exists("report_eventlist_list_generator") ?
        array_keys(\report_eventlist_list_generator::get_all_events_list(false)) : array_keys($availableevents);

    return array_filter($availableevents, function($k) use ($environmentevents) {
        return in_array($k, $environmentevents);
    }, ARRAY_FILTER_USE_KEY);
}
