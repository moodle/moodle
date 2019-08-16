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
                    new external_single_structure(
                        array(
                            'id'             => new external_value(PARAM_INT,  'Comment ID'),
                            'content'        => new external_value(PARAM_RAW,  'The content text formated'),
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
                    ), 'List of comments'
                ),
                'count' => new external_value(PARAM_INT,  'Total number of comments.', VALUE_OPTIONAL),
                'perpage' => new external_value(PARAM_INT,  'Number of comments per page.', VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }
}
