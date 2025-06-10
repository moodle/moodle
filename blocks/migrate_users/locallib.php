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

class migrate {

    /*
     * Gets an array of handlers.
     *
     * @return @array
     *
     */
    public static function get_handlers() {
        // Build the array.
        $handlers = array(
            "handle_user_enrollments",
            "handle_role_enrollments",
            "handle_groups_membership",
            "handle_logs",
            "handle_standard_logs",
            "handle_events",
            "handle_forum_posts",
            "handle_forum_discussions",
            "handle_forum_digests",
            "handle_forum_read",
            "handle_forum_subscriptions",
            "handle_forum_prefs",
            "handle_forum_grades",
            "handle_course_modules_completions",
            "handle_course_modules_viewed",
            "handle_course_completions",
            "handle_course_completion_criteria",
            "handle_grades",
            "handle_grades_history",
            "handle_assign_grades",
            "handle_assign_submissions",
            "handle_assign_user_flags",
            "handle_assign_user_mapping",
            "handle_lesson_attempts",
            "handle_lesson_grades",
            "handle_quiz_attempts",
            "handle_quiz_grades",
            "handle_scorm_scoes",
            "handle_board_notes",
            "handle_board_note_owners",
            "handle_board_note_ratings",
            "handle_board_user_history",
            "handle_board_owner_history",
            "handle_chat_messages",
            "handle_chat_messages_current",
            "handle_choice_answers",
            "handle_custom_certificates",
            "handle_databases",
            "handle_feedbacks",
            "handle_flashcards",
            "handle_flashcard_decks",
            "handle_journals",
            "handle_lastaccess",
            "handle_courseposts",
            "handle_pucodes"
        );

        return $handlers;
    }

    /*
     * Updates the user_enrollments for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_user_enrollments($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {user_enrolments}
                        SET userid = :userto
                    WHERE userid = :userfrom
                        AND enrolid IN (
                            SELECT id FROM {enrol}
                            WHERE courseid = :courseid
                        )
                    ';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the role enrollment for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_role_enrollments($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $dbfamily = $DB->get_dbfamily();
            if ($dbfamily == 'mssql') {
                $sql = 'UPDATE mdl_role_assignments
                            SET mdl_role_assignments.userid = :userto
                        FROM mdl_role_assignments INNER JOIN mdl_context ON mdl_role_assignments.contextid = mdl_context.id
                            INNER JOIN mdl_role ON mdl_role_assignments.roleid = mdl_role.id
                        WHERE mdl_context.instanceid = :courseid
                            AND mdl_role_assignments.userid = :userfrom
                            AND mdl_context.contextlevel = 50';
            } else if ($dbfamily == 'postgres') {
                $sql = 'UPDATE {role_assignments}
                            SET {role_assignments}.userid = :userto
                        FROM {role_assignments} INNER JOIN {context} ON {role_assignments}.contextid = {context}.id
                            INNER JOIN {role} ON {role_assignments}.roleid = {role}.id
                        WHERE {context}.instanceid = :courseid
                            AND {role_assignments}.userid = :userfrom
                            AND {context}.contextlevel = "50"';
            } else {
                $sql = 'UPDATE {role_assignments}
                            INNER JOIN {context} ON {role_assignments}.contextid = {context}.id
                            INNER JOIN {role} ON {role_assignments}.roleid = {role}.id
                            SET {role_assignments}.userid = :userto
                        WHERE {context}.instanceid = :courseid
                            AND {role_assignments}.userid = :userfrom
                            AND {context}.contextlevel = "50"';
            }

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the group membership for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_groups_membership($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $dbfamily = $DB->get_dbfamily();
            if ($dbfamily == 'postgres' or $dbfamily == 'mssql') {
                $sql = 'UPDATE {groups_members}
                            SET {groups_members}.userid = :userto
                        FROM {groups_members} INNER JOIN {groups} ON {groups_members}.groupid = {groups}.id
                        WHERE {groups}.courseid = :courseid
                            AND {groups_members}.userid = :userfrom';
            } else {
                $sql = 'UPDATE {groups_members}
                            INNER JOIN {groups} ON {groups_members}.groupid = {groups}.id
                            SET {groups_members}.userid = :userto
                        WHERE {groups}.courseid = :courseid
                            AND {groups_members}.userid = :userfrom';
            }

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the log data for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_logs($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {log} SET userid = :userto WHERE course = :courseid AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the standard_log for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_standard_logs($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {logstore_standard_log} SET userid = :userto WHERE courseid = :courseid AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the event data for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_events($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {event} SET userid = :userto WHERE courseid = :courseid AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the forum posts for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_posts($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_discussions} fd ON fd.forum = f.id
                      INNER JOIN {forum_posts} fp ON fp.discussion = fd.id
                    SET fp.userid = :userto
                    WHERE f.course = :courseid
                      AND fp.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the Forum Discussions for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_discussions($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_discussions} fd ON fd.forum = f.id
                    SET fd.userid = :userto
                    WHERE f.course = :courseid
                      AND fd.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the Forum Digests for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_digests($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_digests} fd ON fd.forum = f.id
                    SET fd.userid = :userto
                    WHERE f.course = :courseid
                      AND fd.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the forum post read tracking for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_read($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_discussions} fd ON fd.forum = f.id
                      INNER JOIN {forum_posts} fp ON fp.discussion = fd.id
                      INNER JOIN {forum_read} fr ON fr.postid = fp.id
                    SET fr.userid = :userto
                    WHERE f.course = :courseid
                      AND fr.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the forum subscriptions for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_subscriptions($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_subscriptions} fs ON fs.forum = f.id
                    SET fs.userid = :userto
                    WHERE f.course = :courseid
                      AND fs.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the forum preferences for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_prefs($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_track_prefs} fp ON fp.forumid = f.id
                    SET fp.userid = :userto
                    WHERE f.course = :courseid
                      AND fp.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the forum grades for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_forum_grades($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {forum} f
                      INNER JOIN {forum_grades} fg ON fg.forum = f.id
                    SET fg.userid = :userto
                    WHERE f.course = :courseid
                      AND fg.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the course modules completion for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_course_modules_completions($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {course_modules_completion} SET userid = :userto 
                    WHERE coursemoduleid IN (
                        SELECT id FROM {course_modules} 
                        WHERE course = :courseid) AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the course modules viewed for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_course_modules_viewed($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {course_modules_viewed} SET userid = :userto
                    WHERE coursemoduleid IN (
                        SELECT id FROM {course_modules}
                        WHERE course = :courseid) AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the course completions for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_course_completions($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {course_completions} SET userid = :userto WHERE course = :courseid AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the course completion criteria for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_course_completion_criteria($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {course_completion_crit_compl} SET userid = :userto WHERE course = :courseid AND userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the grades for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_grades($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {grade_grades} SET userid = :userto
                      WHERE userid = :userfrom
                        AND itemid IN (
                          SELECT id FROM {grade_items} WHERE courseid = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the grades history for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_grades_history($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {grade_grades_history} SET userid = :userto
                      WHERE userid = :userfrom 
                        AND itemid IN (
                          SELECT id FROM {grade_items} WHERE courseid = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the assignment grades for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_assign_grades($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {assign_grades} SET userid = :userto
                      WHERE userid = :userfrom
                        AND assignment IN (
                          SELECT id FROM {assign} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the assignment overrides for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_assign_overrides($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {assign_overrides} SET userid = :userto
                      WHERE userid = :userfrom
                        AND assignid IN (
                          SELECT id FROM {assign} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the assignment submissions for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_assign_submissions($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {assign_submission} SET userid = :userto
                      WHERE userid = :userfrom
                        AND assignment IN (
                          SELECT id FROM {assign} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the assignment user flags for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_assign_user_flags($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {assign_user_flags} SET userid = :userto
                      WHERE userid = :userfrom
                        AND assignment IN (
                          SELECT id FROM {assign} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the assignment user mapping for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_assign_user_mapping($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {assign_user_mapping} SET userid = :userto
                      WHERE userid = :userfrom
                        AND assignment IN (
                          SELECT id FROM {assign} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the lesson attempts for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_lesson_attempts($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {lesson_attempts} SET userid = :userto WHERE userid = :userfrom AND lessonid IN (SELECT id FROM {lesson} WHERE course = :courseid)';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the lesson grades for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_lesson_grades($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {lesson_grades} SET userid = :userto WHERE userid = :userfrom AND lessonid IN (SELECT id FROM {lesson} WHERE course = :courseid)';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the quiz attempts for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_quiz_attempts($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {quiz_attempts} SET userid = :userto
                      WHERE userid = :userfrom
                        AND quiz IN (
                          SELECT id FROM {quiz} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the quiz grades for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_quiz_grades($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {quiz_grades} SET userid = :userto
                      WHERE userid = :userfrom
                        AND quiz IN (
                          SELECT id FROM {quiz} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the quiz overrides for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_quiz_overrides($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {quiz_overrides} SET userid = :userto
                      WHERE userid = :userfrom
                        AND quiz IN (
                          SELECT id FROM {quiz} WHERE course = :courseid
                        )';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the scorm tracking data for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_scorm_scoes($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            $sql = 'UPDATE {scorm_scoes_track} SET userid = :userto WHERE userid = :userfrom AND scormid IN (SELECT id FROM {scorm} WHERE course = :courseid)';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the board plugin for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_board_notes($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {board} b
                      INNER JOIN {board_columns} bc ON bc.boardid = b.id
                      INNER JOIN {board_notes} bn ON bn.columnid = bc.id
                    SET bn.userid = :userto
                    WHERE b.course = :courseid AND bn.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the board plugin for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_board_note_owners($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {board} b
                      INNER JOIN {board_columns} bc ON bc.boardid = b.id
                      INNER JOIN {board_notes} bn ON bn.columnid = bc.id
                    SET bn.ownerid = :userto
                    WHERE b.course = :courseid AND bn.ownerid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the board plugin for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_board_note_ratings($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {board} b
                      INNER JOIN {board_columns} bc ON bc.boardid = b.id
                      INNER JOIN {board_notes} bn ON bn.columnid = bc.id
                      INNER JOIN {board_note_ratings} bnr ON bnr.noteid = bn.id
                    SET bnr.userid = :userto
                    WHERE b.course = :courseid AND bnr.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the board plugin for the specified users / course
     *
     * @return true
     *
     */
/*
    public static function handle_board_comments($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {board} b
                      INNER JOIN {board_columns} bc ON bc.boardid = b.id
                      INNER JOIN {board_notes} bn ON bn.columnid = bc.id
                      INNER JOIN {board_note_comments} bnc ON bnc.noteid = bn.id
                    SET bnc.userid = :userto
                    WHERE b.course = :courseid AND bnc.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }
*/

    /*
     * Updates the board plugin for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_board_user_history($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {board} b
                      INNER JOIN {board_history} bh ON bh.boardid = b.id
                    SET bh.userid = :userto
                    WHERE b.course = :courseid AND bh.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates the board plugin for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_board_owner_history($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {board} b
                      INNER JOIN {board_history} bh ON bh.boardid = b.id
                    SET bh.ownerid = :userto
                    WHERE b.course = :courseid AND bh.ownerid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates chat for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_chat_messages($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {chat} c
                      INNER JOIN {chat_messages} cm ON cm.chatid = c.id
                    SET cm.userid = :userto
                    WHERE c.course = :courseid AND cm.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates chat for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_chat_messages_current($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {chat} c
                      INNER JOIN {chat_messages_current} cm ON cm.chatid = c.id
                    SET cm.userid = :userto
                    WHERE c.course = :courseid AND cm.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Choice Answers for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_choice_answers($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {choice} c
                      INNER JOIN {choice_answers} ca ON ca.choiceid = c.id
                    SET ca.userid = :userto
                    WHERE c.course = :courseid AND ca.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Custom Certificates for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_custom_certificates($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {customcert} c
                      INNER JOIN {customcert_issues} ci ON ci.customcertid = c.id
                    SET ci.userid = :userto
                    WHERE c.course = :courseid AND ci.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Databases for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_databases($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {data} d
                      INNER JOIN {data_records} dr ON dr.dataid = d.id
                    SET dr.userid = :userto
                    WHERE d.course = :courseid AND dr.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Feedbacks for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_feedbacks($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {feedback} f
                      INNER JOIN {feedback_completed} fc ON fc.feedback = f.id
                    SET fc.userid = :userto
                    WHERE f.course = :courseid AND fc.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Flash Cards for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_flashcards($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {flashcard} f
                      INNER JOIN {flashcard_card} fc ON fc.flashcardid = f.id
                    SET fc.userid = :userto
                    WHERE f.course = :courseid AND fc.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Flash Card decks for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_flashcard_decks($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {flashcard} f
                      INNER JOIN {flashcard_userdeck_state} fus ON fus.flashcardid = f.id
                    SET fus.userid = :userto
                    WHERE f.course = :courseid AND fus.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates course lastaccess for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_lastaccess($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {course} c
                      INNER JOIN {user_lastaccess} ul ON ul.courseid = c.id
                    SET ul.userid = :userto
                    WHERE ul.courseid = :courseid AND ul.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Journals for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_journals($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {journal} j
                      INNER JOIN {journal_entries} je ON je.journal = j.id
                    SET je.userid = :userto
                    WHERE j.course = :courseid AND je.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates Posts for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_courseposts($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {post} p
                    SET p.userid = :userto
                    WHERE p.courseid = :courseid AND p.userid = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /*
     * Updates ProctorU Code Mapping for the specified users / course
     *
     * @return true
     *
     */
    public static function handle_pucodes($userfrom, $userto, $courseid) {
        global $DB;
        // Check if the user can do this.
        if (!self::can_use()) {
            return get_string('securityviolation', 'block_migrate_users');
        } else {
            // Build out the SQL.
            $sql = 'UPDATE {block_pu_guildmaps} gm
                      INNER JOIN {block_pu_codemaps} cm ON cm.guild = gm.id
                    SET gm.user = :userto
                    WHERE gm.course = :courseid AND gm.user = :userfrom';

            // Execute the SQL.
            $success = $DB->execute(
                $sql,
                array(
                    'userfrom' => self::get_userid($userfrom),
                    'userto' => self::get_userid($userto),
                    'courseid' => $courseid
                )
            );

            // Return the status.
            return $success;

        }
    }

    /**
     * Returns the userid for the username in question.
     *
     * @return int
     */
    public static function get_userid($username) {
        global $DB;
        $user = $DB->get_record('user', array('username' => $username));
        $userid = $user->id;
        return $userid;
    }

    /**
     * Returns the user object for the username in question.
     *
     * @return object
     */
    public static function get_user($username) {
        global $DB;
        $user = $DB->get_record('user', array('username' => $username));
        return $user;
    }
    /**
     * Returns if a user can use the tool or not.
     *
     * @return bool
     */
    public static function can_use() {
        global $CFG, $USER;
        $allowed_users = array();
        if (!isset($CFG->block_migrate_users_allowed)) {
            return true;
        }
        $allowed_users = array_map("trim",explode(',', $CFG->block_migrate_users_allowed));
        if (count($allowed_users) == 0) {
            return true;
        }
        $allowed = is_siteadmin() && in_array($USER->username, $allowed_users);
        return $allowed;
    }
}
