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
 * External comment API
 *
 * @package    core_comment
 * @category   external
 * @copyright  Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/comment/lib.php");

/**
 * External comment API functions
 *
 * @package    core_comment
 * @category   external
 * @copyright  Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_comment_external extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_comments_parameters() {

        return new external_function_parameters(
            array(
                'contextlevel'  => new external_value(PARAM_ALPHA, 'contextlevel system, course, user...'),
                'instanceid'    => new external_value(PARAM_INT, 'the Instance id of item associated with the context level'),
                'component'     => new external_value(PARAM_COMPONENT, 'component'),
                'itemid'        => new external_value(PARAM_INT, 'associated id'),
                'area'          => new external_value(PARAM_AREA, 'string comment area', VALUE_DEFAULT, ''),
                'page'          => new external_value(PARAM_INT, 'page number (0 based)', VALUE_DEFAULT, 0),
                'sortdirection' => new external_value(PARAM_ALPHA, 'Sort direction: ASC or DESC', VALUE_DEFAULT, 'DESC'),
            )
        );
    }

    /**
     * Return a list of comments
     *
     * @param string $contextlevel ('system, course, user', etc..)
     * @param int $instanceid
     * @param string $component the name of the component
     * @param int $itemid the item id
     * @param string $area comment area
     * @param int $page page number
     * @param string $sortdirection sort direction
     * @return array of comments and warnings
     * @since Moodle 2.9
     */
    public static function get_comments($contextlevel, $instanceid, $component, $itemid, $area = '', $page = 0,
            $sortdirection = 'DESC') {
        global $CFG;

        $warnings = array();
        $arrayparams = array(
            'contextlevel'  => $contextlevel,
            'instanceid'    => $instanceid,
            'component'     => $component,
            'itemid'        => $itemid,
            'area'          => $area,
            'page'          => $page,
            'sortdirection' => $sortdirection,
        );
        $params = self::validate_parameters(self::get_comments_parameters(), $arrayparams);

        $sortdirection = strtoupper($params['sortdirection']);
        $directionallowedvalues = array('ASC', 'DESC');
        if (!in_array($sortdirection, $directionallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $sortdirection . '),' .
                'allowed values are: ' . implode(',', $directionallowedvalues));
        }

        $context = self::get_context_from_params($params);
        self::validate_context($context);

        require_capability('moodle/comment:view', $context);

        $args = new stdClass;
        $args->context   = $context;
        $args->area      = $params['area'];
        $args->itemid    = $params['itemid'];
        $args->component = $params['component'];

        $commentobject = new comment($args);
        $comments = $commentobject->get_comments($params['page'], $sortdirection);

        // False means no permissions to see comments.
        if ($comments === false) {
            throw new moodle_exception('nopermissions', 'error', '', 'view comments');
        }
        $options = array('blanktarget' => true);

        foreach ($comments as $key => $comment) {

                list($comments[$key]->content, $comments[$key]->format) = external_format_text($comment->content,
                                                                                                 $comment->format,
                                                                                                 $context->id,
                                                                                                 $params['component'],
                                                                                                 '',
                                                                                                 0,
                                                                                                 $options);
        }

        $results = array(
            'comments' => $comments,
            'count' => $commentobject->count(),
            'perpage' => (!empty($CFG->commentsperpage)) ? $CFG->commentsperpage : 15,
            'canpost'  => $commentobject->can_post(),
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_comments_returns() {
        return new external_single_structure(
            array(
                'comments' => new external_multiple_structure(
                    self::get_comment_structure(), 'List of comments'
                ),
                'count' => new external_value(PARAM_INT,  'Total number of comments.', VALUE_OPTIONAL),
                'perpage' => new external_value(PARAM_INT,  'Number of comments per page.', VALUE_OPTIONAL),
                'canpost' => new external_value(PARAM_BOOL, 'Whether the user can post in this comment area.', VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Helper to get the structure of a single comment.
     *
     * @return external_single_structure the comment structure.
     */
    protected static function get_comment_structure() {
        return new external_single_structure(
            array(
                'id'             => new external_value(PARAM_INT,  'Comment ID'),
                'content'        => new external_value(PARAM_RAW,  'The content text formatted'),
                'format'         => new external_format_value('content'),
                'timecreated'    => new external_value(PARAM_INT,  'Time created (timestamp)'),
                'strftimeformat' => new external_value(PARAM_NOTAGS, 'Time format'),
                'profileurl'     => new external_value(PARAM_URL,  'URL profile'),
                'fullname'       => new external_value(PARAM_NOTAGS, 'fullname'),
                'time'           => new external_value(PARAM_NOTAGS, 'Time in human format'),
                'avatar'         => new external_value(PARAM_RAW,  'HTML user picture'),
                'userid'         => new external_value(PARAM_INT,  'User ID'),
                'delete'         => new external_value(PARAM_BOOL, 'Permission to delete=true/false', VALUE_OPTIONAL)
            ), 'comment'
        );
    }

    /**
     * Returns description of method parameters for the add_comments method.
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */
    public static function add_comments_parameters() {
        return new external_function_parameters(
            [
                'comments' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'contextlevel' => new external_value(PARAM_ALPHA, 'contextlevel system, course, user...'),
                            'instanceid'   => new external_value(PARAM_INT, 'the id of item associated with the contextlevel'),
                            'component'    => new external_value(PARAM_COMPONENT, 'component'),
                            'content'      => new external_value(PARAM_RAW, 'component'),
                            'itemid'       => new external_value(PARAM_INT, 'associated id'),
                            'area'         => new external_value(PARAM_AREA, 'string comment area', VALUE_DEFAULT, ''),
                        ]
                    )
                )
            ]
        );
    }

    /**
     * Add a comment or comments.
     *
     * @param array $comments the array of comments to create.
     * @return array the array containing those comments created.
     * @throws comment_exception
     * @since Moodle 3.8
     */
    public static function add_comments($comments) {
        global $CFG, $SITE;

        if (empty($CFG->usecomments)) {
            throw new comment_exception('commentsnotenabled', 'moodle');
        }

        $params = self::validate_parameters(self::add_comments_parameters(), ['comments' => $comments]);

        // Validate every intended comment before creating anything, storing the validated comment for use below.
        foreach ($params['comments'] as $index => $comment) {
            $context = self::get_context_from_params($comment);
            self::validate_context($context);

            list($context, $course, $cm) = get_context_info_array($context->id);
            if ($context->id == SYSCONTEXTID) {
                $course = $SITE;
            }

            // Initialising comment object.
            $args = new stdClass();
            $args->context   = $context;
            $args->course    = $course;
            $args->cm        = $cm;
            $args->component = $comment['component'];
            $args->itemid    = $comment['itemid'];
            $args->area      = $comment['area'];

            $manager = new comment($args);
            if (!$manager->can_post()) {
                throw new comment_exception('nopermissiontocomment');
            }

            $params['comments'][$index]['preparedcomment'] = $manager;
        }

        // Create the comments.
        $results = [];
        foreach ($params['comments'] as $comment) {
            $manager = $comment['preparedcomment'];
            $newcomment = $manager->add($comment['content']);
            $newcomment->delete = true; // USER created the comment, so they can delete it.
            $results[] = $newcomment;
        }

        return $results;
    }

    /**
     * Returns description of method result value for the add_comments method.
     *
     * @return external_description
     * @since Moodle 3.8
     */
    public static function add_comments_returns() {
        return new external_multiple_structure(
            self::get_comment_structure()
        );
    }

    /**
     * Returns description of method parameters for the delete_comments() method.
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */
    public static function delete_comments_parameters() {
        return new external_function_parameters(
            [
                'comments' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'id of the comment', VALUE_DEFAULT, 0)
                )
            ]
        );
    }

    /**
     * Deletes a comment or comments.
     *
     * @param array $comments array of comment ids to be deleted
     * @return array
     * @throws comment_exception
     * @since Moodle 3.8
     */
    public static function delete_comments(array $comments) {
        global $CFG, $DB, $USER, $SITE;

        if (empty($CFG->usecomments)) {
            throw new comment_exception('commentsnotenabled', 'moodle');
        }

        $params = self::validate_parameters(self::delete_comments_parameters(), ['comments' => $comments]);
        $commentids = $params['comments'];

        list($insql, $inparams) = $DB->get_in_or_equal($commentids);
        $commentrecords = $DB->get_records_select('comments', "id {$insql}", $inparams);

        // If one or more of the records could not be found, report this and fail early.
        if (count($commentrecords) != count($comments)) {
            $invalidcomments = array_diff($commentids, array_column($commentrecords, 'id'));
            $invalidcommentsstr = implode(',', $invalidcomments);
            throw new comment_exception("One or more comments could not be found by id: $invalidcommentsstr");
        }

        // Make sure we can delete every one of the comments before actually doing so.
        $comments = []; // Holds the comment objects, for later deletion.
        foreach ($commentrecords as $commentrecord) {
            // Validate the context.
            list($context, $course, $cm) = get_context_info_array($commentrecord->contextid);
            if ($context->id == SYSCONTEXTID) {
                $course = $SITE;
            }
            self::validate_context($context);

            // Make sure the user is allowed to delete the comment.
            $args = new stdClass;
            $args->context   = $context;
            $args->course    = $course;
            $args->cm        = $cm;
            $args->component = $commentrecord->component;
            $args->itemid    = $commentrecord->itemid;
            $args->area      = $commentrecord->commentarea;
            $manager = new comment($args);

            if ($commentrecord->userid != $USER->id && !$manager->can_delete($commentrecord->id)) {
                throw new comment_exception('nopermissiontodelentry');
            }

            // User is allowed to delete it, so store the comment object, for use below in final deletion.
            $comments[$commentrecord->id] = $manager;
        }

        // All comments can be deleted by the user. Make it so.
        foreach ($comments as $commentid => $comment) {
            $comment->delete($commentid);
        }

        return [];
    }

    /**
     * Returns description of method result value for the delete_comments() method.
     *
     * @return external_description
     * @since Moodle 3.8
     */
    public static function delete_comments_returns() {
        return new external_warnings();
    }
}
