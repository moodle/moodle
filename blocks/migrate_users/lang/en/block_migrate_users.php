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
 * @package    block_migrate_users
 * @copyright  2019 onwards Louisiana State University
 * @copyright  2019 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowed_users'] = 'Allowed Users';
$string['allowed_users_desc'] = 'A comma seperated list of all allowed users. Only use usernames.';
$string['courseid'] = 'Course ID';
$string['migrate'] = 'Migrate';
$string['migrate_users'] = 'Migrate Users';
$string['migrate_users:addinstance'] = 'Add a new Migrate Users block';
$string['migrate_users:migrate_user'] = 'Migrate User';
$string['migrate_users:myaddinstance'] = 'Add a new Migrate Users block to the My Moodle page';
$string['pluginname'] = 'Migrate Users block';
$string['securityviolation'] = 'You do not have access to this plugin.';
$string['userfrom'] = 'Username From:&nbsp;&nbsp;';
$string['userto'] = 'Username To:&nbsp;&nbsp;';

// Handler return strings.
$string['handle_user_enrollments'] = 'user enrollments';
$string['handle_role_enrollments'] = 'role based enrollments';
$string['handle_groups_membership'] = 'group memberships';
$string['handle_logs'] = 'legacy logs';
$string['handle_standard_logs'] = 'standard logs';
$string['handle_events'] = 'events';
$string['handle_forum_posts'] = 'forum posts';
$string['handle_forum_discussions'] = 'forum discussions';
$string['handle_forum_digests'] = 'forum digests';
$string['handle_forum_read'] = 'forums read';
$string['handle_forum_subscriptions'] = 'forum subscriptions';
$string['handle_forum_prefs'] = 'forum preferences';
$string['handle_forum_grades'] = 'forum grades';
$string['handle_course_modules_completions'] = 'course module completions';
$string['handle_course_modules_viewed'] = 'course modules viewed';
$string['handle_course_completions'] = 'course completions';
$string['handle_course_completion_criteria'] = 'course completion criteria';
$string['handle_grades'] = 'grades';
$string['handle_grades_history'] = 'grade histories';
$string['handle_assign_grades'] = 'assignment grades';
$string['handle_assign_submissions'] = 'assignment submissions';
$string['handle_assign_user_flags'] = 'assignment user flags';
$string['handle_assign_user_mapping'] = 'assignment user mappings';
$string['handle_lesson_attempts'] = 'lesson attempts';
$string['handle_lesson_grades'] = 'lesson grades';
$string['handle_quiz_attempts'] = 'quiz attempts';
$string['handle_quiz_grades'] = 'quiz grades';
$string['handle_scorm_scoes'] = 'SCORM submissions';
$string['handle_choice_answers'] = 'choice responses';
$string['handle_board_notes'] = 'board notes';
$string['handle_board_note_owners'] = 'board note owners';
$string['handle_board_note_ratings'] = 'board note ratings';
$string['handle_board_comments'] = 'board comments';
$string['handle_board_user_history'] = 'board user history';
$string['handle_board_owner_history'] = 'board owner history';
$string['handle_chat_messages'] = 'chat messages';
$string['handle_chat_messages_current'] = 'chat messages current';
$string['handle_custom_certificates'] = 'custom certificates';
$string['handle_databases'] = 'database entries';
$string['handle_feedbacks'] = 'feedbacks';
$string['handle_flashcards'] = 'flashcards';
$string['handle_flashcard_decks'] = 'flashcard decks';
$string['handle_journals'] = 'journals';
$string['handle_courseposts'] = 'course posts';
$string['handle_lastaccess'] = 'course last access';
$string['handle_pucodes'] = 'proctor u codes';

// Non-handler return strings.
$string['found'] = ' were found, they have been migrated.';
$string['nonefound'] = 'Something went wrong finding ';
$string['prefix'] = 'If any ';
$string['exception'] = 'We have experienced a failure: ';
$string['success'] = 'You have successfully migrated your user data. The original user can now re-take the course.';
$string['continue'] = 'Are you sure you want to continue with the process?';
$string['alldata'] = 'All data from the user ';
$string['moveto'] = ' will be moved to ';
$string['deleted'] = '. There is NO TURNING BACK!';
$string['missingboth'] = 'Both your "from" and "to" users are invalid or their usernames are incorrect, please try again.';
$string['missingfrom'] = 'Your "from" user is not valid or their username is incorrect, please try again.';
$string['missingto'] = 'Your "to" user is not valid or their username is incorrect, please try again.';
