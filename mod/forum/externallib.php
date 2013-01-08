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
 * External forum API
 *
 * @package    mod_forum
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class mod_forum_external extends external_api {

    /**
     * Describes the parameters for get_forum
     *
     * @return external_external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_forums_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'course ID',
                        '', VALUE_REQUIRED, '', NULL_NOT_ALLOWED), 'Array of course IDs', VALUE_DEFAULT, array()),
            )
        );
    }

    /**
     * Returns a list of forums in a provided list of courses,
     * if no list is provided all forums that the user can view
     * will be returned.
     *
     * @param array $courseids the course ids
     * @return array the forum details
     * @since Moodle 2.5
     */
    public static function get_forums_by_courses($courseids = array()) {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . "/mod/forum/lib.php");

        if (empty($courseids)) {
            // Get all the courses the user can view.
            $courseids = array_keys(enrol_get_my_courses());
        } else {
            $params = self::validate_parameters(self::get_forums_by_courses_parameters(), array('courseids' => $courseids));
            $courseids = $params['courseids'];
        }

        // Array to store the forums to return.
        $arrforums = array();

        // Go through the courseids and return the forums.
        if (!empty($courseids)) {
            // Keep track of the course ids we have performed a require_course_login check on to avoid repeating.
            $arrcourseschecked = array();
            // Convert array to string to use in query.
            list($subsql, $params) = $DB->get_in_or_equal($courseids);
            $sql = "SELECT *
                    FROM {forum}
                    WHERE course $subsql";
            if ($forums = $DB->get_records_sql($sql, $params)) {
                foreach ($forums as $forum) {
                    // Check that that user can view this course if check not performed yet.
                    if (!in_array($forum->course, $arrcourseschecked)) {
                        // Get the course context.
                        $context = context_course::instance($forum->course);
                        // Check the user can function in this context.
                        self::validate_context($context);
                        // Add to the array.
                        $arrcourseschecked[] = $forum->course;
                    }
                    // Get the course module.
                    $cm = get_coursemodule_from_instance('forum', $forum->id, 0, false, MUST_EXIST);
                    // Get the module context.
                    $context = context_module::instance($cm->id);
                    // Check they have the view forum capability.
                    require_capability('mod/forum:viewdiscussion', $context);
                    // Format the intro before being returning using the format setting.
                    list($forum->intro, $forum->introformat) = external_format_text($forum->intro, $forum->introformat,
                        $context->id, 'mod_forum', 'intro', 0);
                    // Add the course module id to the object, this information is useful.
                    $forum->cmid = $cm->id;
                    // Add the forum to the array to return.
                    $arrforums[$forum->id] = (array) $forum;
                }
            }
        }

        return $arrforums;
    }

    /**
     * Describes the get_forum return value
     *
     * @return external_single_structure
     * @since Moodle 2.5
     */
     public static function get_forums_by_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Forum id'),
                    'course' => new external_value(PARAM_TEXT, 'Course id'),
                    'type' => new external_value(PARAM_TEXT, 'The forum type'),
                    'name' => new external_value(PARAM_TEXT, 'Forum name'),
                    'intro' => new external_value(PARAM_RAW, 'The forum intro'),
                    'introformat' => new external_format_value('intro'),
                    'assessed' => new external_value(PARAM_INT, 'Aggregate type'),
                    'assesstimestart' => new external_value(PARAM_INT, 'Assess start time'),
                    'assesstimefinish' => new external_value(PARAM_INT, 'Assess finish time'),
                    'scale' => new external_value(PARAM_INT, 'Scale'),
                    'maxbytes' => new external_value(PARAM_INT, 'Maximum attachment size'),
                    'maxattachments' => new external_value(PARAM_INT, 'Maximum number of attachments'),
                    'forcesubscribe' => new external_value(PARAM_INT, 'Force users to subscribe'),
                    'trackingtype' => new external_value(PARAM_INT, 'Subscription mode'),
                    'rsstype' => new external_value(PARAM_INT, 'RSS feed for this activity'),
                    'rssarticles' => new external_value(PARAM_INT, 'Number of RSS recent articles'),
                    'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                    'warnafter' => new external_value(PARAM_INT, 'Post threshold for warning'),
                    'blockafter' => new external_value(PARAM_INT, 'Post threshold for blocking'),
                    'blockperiod' => new external_value(PARAM_INT, 'Time period for blocking'),
                    'completiondiscussions' => new external_value(PARAM_INT, 'Student must create discussions'),
                    'completionreplies' => new external_value(PARAM_INT, 'Student must post replies'),
                    'completionposts' => new external_value(PARAM_INT, 'Student must post discussions or replies'),
                    'cmid' => new external_value(PARAM_INT, 'Course module id')
                ), 'forum'
            )
        );
    }
}
