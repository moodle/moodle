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
 * SCORM module external API
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/scorm/lib.php');
require_once($CFG->dirroot . '/mod/scorm/locallib.php');

/**
 * SCORM module external functions
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_scorm_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_scorm_parameters() {
        return new external_function_parameters(
            array(
                'scormid' => new external_value(PARAM_INT, 'scorm instance id')
            )
        );
    }

    /**
     * Trigger the course module viewed event.
     *
     * @param int $scormid the scorm instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_scorm($scormid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/scorm/lib.php');

        $params = self::validate_parameters(self::view_scorm_parameters(),
                                            array(
                                                'scormid' => $scormid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $scorm = $DB->get_record('scorm', array('id' => $params['scormid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($scorm, 'scorm');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Call the scorm/lib API.
        scorm_view($scorm, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_scorm_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_scorm_attempt_count.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_scorm_attempt_count_parameters() {
        return new external_function_parameters(
            array(
                'scormid' => new external_value(PARAM_INT, 'SCORM instance id'),
                'userid' => new external_value(PARAM_INT, 'User id'),
                'ignoremissingcompletion' => new external_value(PARAM_BOOL,
                                                'Ignores attempts that haven\'t reported a grade/completion',
                                                VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Return the number of attempts done by a user in the given SCORM.
     *
     * @param int $scormid the scorm id
     * @param int $userid the user id
     * @param bool $ignoremissingcompletion ignores attempts that haven't reported a grade/completion
     * @return array of warnings and the attempts count
     * @since Moodle 3.0
     */
    public static function get_scorm_attempt_count($scormid, $userid, $ignoremissingcompletion = false) {
        global $USER, $DB;

        $params = self::validate_parameters(self::get_scorm_attempt_count_parameters(),
                                            array('scormid' => $scormid, 'userid' => $userid,
                                                'ignoremissingcompletion' => $ignoremissingcompletion));

        $attempts = array();
        $warnings = array();

        $scorm = $DB->get_record('scorm', array('id' => $params['scormid']), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the user obtaining the context, it will fail if the user doesn't exists or have been deleted.
        context_user::instance($params['userid']);

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            require_capability('mod/scorm:viewreport', $context);
        }

        // If the SCORM is not open this function will throw exceptions.
        scorm_require_available($scorm);

        $attemptscount = scorm_get_attempt_count($params['userid'], $scorm, false, $params['ignoremissingcompletion']);

        $result = array();
        $result['attemptscount'] = $attemptscount;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_scorm_attempt_count return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_scorm_attempt_count_returns() {

        return new external_single_structure(
            array(
                'attemptscount' => new external_value(PARAM_INT, 'Attempts count'),
                'warnings' => new external_warnings(),
            )
        );
    }

}
