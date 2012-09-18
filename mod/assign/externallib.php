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
 * External assign API
 *
 * @package    mod_assign
 * @since      Moodle 2.4
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Assign functions
 */
class mod_assign_external extends external_api {

    /**
     * Describes the parameters for get_grades
     * @return external_external_function_parameters
     * @since  Moodle 2.4
     */
    public static function get_grades_parameters() {
        return new external_function_parameters(
            array(
                'assignmentids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'assignment id'),
                    '1 or more assignment ids',
                    VALUE_REQUIRED),
                'since' => new external_value(PARAM_INT,
                          'timestamp, only return records where timemodified >= since',
                          VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns grade information from assign_grades for the requested assignment ids
     * @param array of ints $assignmentids
     * @param int $since only return records with timemodified >= since
     * @return array of grade records for each requested assignment
     * @since  Moodle 2.4
     */
    public static function get_grades($assignmentids, $since = 0) {
        global $DB;
        $params = self::validate_parameters(self::get_grades_parameters(),
                        array('assignmentids' => $assignmentids,
                              'since' => $since));

        $assignments = array();
        $warnings = array();
        $requestedassignmentids = $params['assignmentids'];

        // Check the user is allowed to get the grades for the assignments requested.
        $placeholders = array();
        list($sqlassignmentids, $placeholders) = $DB->get_in_or_equal($requestedassignmentids, SQL_PARAMS_NAMED);
        $sql = "SELECT cm.id, cm.instance FROM {course_modules} cm JOIN {modules} md ON md.id = cm.module ".
               "WHERE md.name = :modname AND cm.instance ".$sqlassignmentids;
        $placeholders['modname'] = 'assign';
        $cms = $DB->get_records_sql($sql, $placeholders);
        foreach ($cms as $cm) {
            try {
                $context = context_module::instance($cm->id);
                self::validate_context($context);
                require_capability('mod/assign:grade', $context);
            } catch (Exception $e) {
                $requestedassignmentids = array_diff($requestedassignmentids, array($cm->instance));
                $warning = array();
                $warning['item'] = 'assignment';
                $warning['itemid'] = $cm->instance;
                $warning['warningcode'] = '1';
                $warning['message'] = 'No access rights in module context';
                $warnings[] = $warning;
            }
        }

        // Create the query and populate an array of grade records from the recordset results.
        if (count ($requestedassignmentids) > 0) {
            $placeholders = array();
            list($inorequalsql, $placeholders) = $DB->get_in_or_equal($requestedassignmentids, SQL_PARAMS_NAMED);
            $sql = "SELECT ag.id,ag.assignment,ag.userid,ag.timecreated,ag.timemodified,".
                   "ag.grader,ag.grade,ag.locked,ag.mailed ".
                   "FROM {assign_grades} ag ".
                   "WHERE ag.assignment ".$inorequalsql.
                   " AND ag.timemodified  >= :since".
                   " ORDER BY ag.assignment, ag.id";
            $placeholders['since'] = $params['since'];
            $rs = $DB->get_recordset_sql($sql, $placeholders);
            $currentassignmentid = null;
            $assignment = null;
            foreach ($rs as $rd) {
                $grade = array();
                $grade['id'] = $rd->id;
                $grade['userid'] = $rd->userid;
                $grade['timecreated'] = $rd->timecreated;
                $grade['timemodified'] = $rd->timemodified;
                $grade['grader'] = $rd->grader;
                $grade['grade'] = (string)$rd->grade;
                $grade['locked'] = $rd->locked;
                $grade['mailed'] = $rd->mailed;

                if (is_null($currentassignmentid) || ($rd->assignment != $currentassignmentid )) {
                    if (!is_null($assignment)) {
                        $assignments[] = $assignment;
                    }
                    $assignment = array();
                    $assignment['assignmentid'] = $rd->assignment;
                    $assignment['grades'] = array();
                    $requestedassignmentids = array_diff($requestedassignmentids, array($rd->assignment));
                }
                $assignment['grades'][] = $grade;

                $currentassignmentid = $rd->assignment;
            }
            if (!is_null($assignment)) {
                $assignments[] = $assignment;
            }
            $rs->close();
        }
        foreach ($requestedassignmentids as $assignmentid) {
            $warning = array();
            $warning['item'] = 'assignment';
            $warning['itemid'] = $assignmentid;
            $warning['warningcode'] = '3';
            $warning['message'] = 'No grades found';
            $warnings[] = $warning;
        }

        $result = array();
        $result['assignments'] = $assignments;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Creates an assign_grades external_single_structure
     * @return external_single_structure
     * @since  Moodle 2.4
     */
    private static function assign_grades() {
        return new external_single_structure(
            array (
                'assignmentid'    => new external_value(PARAM_INT, 'assignment id'),
                'grades'   => new external_multiple_structure(new external_single_structure(
                        array(
                            'id'            => new external_value(PARAM_INT, 'grade id'),
                            'userid'        => new external_value(PARAM_INT, 'student id'),
                            'timecreated'   => new external_value(PARAM_INT, 'grade creation time'),
                            'timemodified'  => new external_value(PARAM_INT, 'grade last modified time'),
                            'grader'        => new external_value(PARAM_INT, 'grader'),
                            'grade'         => new external_value(PARAM_TEXT, 'grade'),
                            'locked'        => new external_value(PARAM_BOOL, 'locked'),
                            'mailed'        => new external_value(PARAM_BOOL, 'mailed')
                        )
                    )
                )
            )
        );
    }

    /**
     * Describes the get_grades return value
     * @return external_single_structure
     * @since  Moodle 2.4
     */
    public static function get_grades_returns() {
        return new external_single_structure(
            array(
                'assignments' => new external_multiple_structure(self::assign_grades(), 'list of assignment grade information'),
                'warnings'      => new external_warnings()
            )
        );
    }

}
