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

    /**
     * Describes the parameters for get_scorms_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_scorm_scoes_parameters() {
        return new external_function_parameters(
            array(
                'scormid' => new external_value(PARAM_INT, 'scorm instance id'),
                'organization' => new external_value(PARAM_RAW, 'organization id', VALUE_DEFAULT, '')
            )
        );
    }

    /**
     * Returns a list containing all the scoes data related to the given scorm id
     *
     * @param int $scormid the scorm id
     * @param string $organization the organization id
     * @return array warnings and the scoes data
     * @since Moodle 3.0
     */
    public static function get_scorm_scoes($scormid, $organization = '') {
        global $DB;

        $params = self::validate_parameters(self::get_scorm_scoes_parameters(),
                                            array('scormid' => $scormid, 'organization' => $organization));

        $scoes = array();
        $warnings = array();

        $scorm = $DB->get_record('scorm', array('id' => $params['scormid']), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check settings / permissions to view the SCORM.
        scorm_require_available($scorm, true, $context);

        if (!$scoes = scorm_get_scoes($scorm->id, $params['organization'])) {
            // Function scorm_get_scoes return false, not an empty array.
            $scoes = array();
        }

        $result = array();
        $result['scoes'] = $scoes;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_scorm_scoes return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_scorm_scoes_returns() {

        return new external_single_structure(
            array(
                'scoes' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'sco id'),
                            'scorm' => new external_value(PARAM_INT, 'scorm id'),
                            'manifest' => new external_value(PARAM_NOTAGS, 'manifest id'),
                            'organization' => new external_value(PARAM_NOTAGS, 'organization id'),
                            'parent' => new external_value(PARAM_NOTAGS, 'parent'),
                            'identifier' => new external_value(PARAM_NOTAGS, 'identifier'),
                            'launch' => new external_value(PARAM_NOTAGS, 'launch file'),
                            'scormtype' => new external_value(PARAM_ALPHA, 'scorm type (asset, sco)'),
                            'title' => new external_value(PARAM_NOTAGS, 'sco title'),
                            'sortorder' => new external_value(PARAM_INT, 'sort order'),
                        ), 'SCORM SCO data'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_scorm_user_data.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_scorm_user_data_parameters() {
        return new external_function_parameters(
            array(
                'scormid' => new external_value(PARAM_INT, 'scorm instance id'),
                'attempt' => new external_value(PARAM_INT, 'attempt number')
            )
        );
    }

    /**
     * Retrieves user tracking and SCO data and default SCORM values
     *
     * @param int $scormid the scorm id
     * @param int $attempt the attempt number
     * @return array warnings and the scoes data
     * @throws  moodle_exception
     * @since Moodle 3.0
     */
    public static function get_scorm_user_data($scormid, $attempt) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::get_scorm_user_data_parameters(),
                                            array('scormid' => $scormid, 'attempt' => $attempt));

        $data = array();
        $warnings = array();

        $scorm = $DB->get_record('scorm', array('id' => $params['scormid']), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        scorm_require_available($scorm, true, $context);

        $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));
        if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
            $scorm->version = 'scorm_12';
        }
        require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');

        if ($scoes = scorm_get_scoes($scorm->id)) {
            $def = new stdClass();
            $user = new stdClass();

            foreach ($scoes as $sco) {
                $def->{$sco->id} = new stdClass();
                $user->{$sco->id} = new stdClass();
                // We force mode normal, this can be override by the client at any time.
                $def->{$sco->id} = get_scorm_default($user->{$sco->id}, $scorm, $sco->id, $params['attempt'], 'normal');

                $userdata = array();
                $defaultdata = array();

                foreach ((array) $user->{$sco->id} as $key => $val) {
                    $userdata[] = array(
                        'element' => $key,
                        'value' => $val
                    );
                }
                foreach ($def->{$sco->id} as $key => $val) {
                    $defaultdata[] = array(
                        'element' => $key,
                        'value' => $val
                    );
                }

                $data[] = array(
                    'scoid' => $sco->id,
                    'userdata' => $userdata,
                    'defaultdata' => $defaultdata,
                );
            }
        }

        $result = array();
        $result['data'] = $data;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_scorm_user_data return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_scorm_user_data_returns() {

        return new external_single_structure(
            array(
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'scoid' => new external_value(PARAM_INT, 'sco id'),
                            'userdata' => new external_multiple_structure(
                                            new external_single_structure(
                                                array(
                                                    'element' => new external_value(PARAM_RAW, 'element name'),
                                                    'value' => new external_value(PARAM_RAW, 'element value')
                                                )
                                            )
                                          ),
                            'defaultdata' => new external_multiple_structure(
                                                new external_single_structure(
                                                    array(
                                                        'element' => new external_value(PARAM_RAW, 'element name'),
                                                        'value' => new external_value(PARAM_RAW, 'element value')
                                                    )
                                                )
                                             ),
                        ), 'SCO data'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for insert_scorm_tracks.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function insert_scorm_tracks_parameters() {
        return new external_function_parameters(
            array(
                'scoid' => new external_value(PARAM_INT, 'SCO id'),
                'attempt' => new external_value(PARAM_INT, 'attempt number'),
                'tracks' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'element' => new external_value(PARAM_RAW, 'element name'),
                            'value' => new external_value(PARAM_RAW, 'element value')
                        )
                    )
                ),
            )
        );
    }

    /**
     * Saves a SCORM tracking record.
     * It will overwrite any existing tracking data for this attempt.
     * Validation should be performed before running the function to ensure the user will not lose any existing attempt data.
     *
     * @param int $scoid the SCO id
     * @param string $attempt the attempt number
     * @param array $tracks the track records to be stored
     * @return array warnings and the scoes data
     * @throws moodle_exception
     * @since Moodle 3.0
     */
    public static function insert_scorm_tracks($scoid, $attempt, $tracks) {
        global $USER, $DB;

        $params = self::validate_parameters(self::insert_scorm_tracks_parameters(),
                                            array('scoid' => $scoid, 'attempt' => $attempt, 'tracks' => $tracks));

        $trackids = array();
        $warnings = array();

        $sco = scorm_get_sco($params['scoid'], SCO_ONLY);
        if (!$sco) {
            throw new moodle_exception('cannotfindsco', 'scorm');
        }

        $scorm = $DB->get_record('scorm', array('id' => $sco->scorm), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Check settings / permissions to view the SCORM.
        require_capability('mod/scorm:savetrack', $context);

        // Check settings / permissions to view the SCORM.
        scorm_require_available($scorm);

        foreach ($params['tracks'] as $track) {
            $element = $track['element'];
            $value = $track['value'];
            $trackid = scorm_insert_track($USER->id, $scorm->id, $sco->id, $params['attempt'], $element, $value,
                                            $scorm->forcecompleted);

            if ($trackid) {
                $trackids[] = $trackid;
            } else {
                $warnings[] = array(
                    'item' => 'scorm',
                    'itemid' => $scorm->id,
                    'warningcode' => 1,
                    'message' => 'Element: ' . $element . ' was not saved'
                );
            }
        }

        $result = array();
        $result['trackids'] = $trackids;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the insert_scorm_tracks return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function insert_scorm_tracks_returns() {

        return new external_single_structure(
            array(
                'trackids' => new external_multiple_structure(new external_value(PARAM_INT, 'track id')),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_scorms_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_scorm_sco_tracks_parameters() {
        return new external_function_parameters(
            array(
                'scoid' => new external_value(PARAM_INT, 'sco id'),
                'userid' => new external_value(PARAM_INT, 'user id'),
                'attempt' => new external_value(PARAM_INT, 'attempt number (0 for last attempt)', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Retrieves SCO tracking data for the given user id and attempt number
     *
     * @param int $scoid the sco id
     * @param int $userid the user id
     * @param int $attempt the attempt number
     * @return array warnings and the scoes data
     * @since Moodle 3.0
     */
    public static function get_scorm_sco_tracks($scoid, $userid, $attempt = 0) {
        global $USER, $DB;

        $params = self::validate_parameters(self::get_scorm_sco_tracks_parameters(),
                                            array('scoid' => $scoid, 'userid' => $userid, 'attempt' => $attempt));

        $tracks = array();
        $warnings = array();

        $sco = scorm_get_sco($params['scoid'], SCO_ONLY);
        if (!$sco) {
            throw new moodle_exception('cannotfindsco', 'scorm');
        }

        $scorm = $DB->get_record('scorm', array('id' => $sco->scorm), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the user obtaining the context, it will fail if the user doesn't exists or have been deleted.
        context_user::instance($params['userid']);

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            require_capability('mod/scorm:viewreport', $context);
        }

        scorm_require_available($scorm, true, $context);

        if (empty($params['attempt'])) {
            $params['attempt'] = scorm_get_last_attempt($scorm->id, $params['userid']);
        }

        if ($scormtracks = scorm_get_tracks($sco->id, $params['userid'], $params['attempt'])) {
            foreach ($scormtracks as $element => $value) {
                $tracks[] = array(
                    'element' => $element,
                    'value' => $value,
                );
            }
        }

        $result = array();
        $result['data']['attempt'] = $params['attempt'];
        $result['data']['tracks'] = $tracks;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_scorm_sco_tracks return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_scorm_sco_tracks_returns() {

        return new external_single_structure(
            array(
                'data' => new external_single_structure(
                    array(
                        'attempt' => new external_value(PARAM_INT, 'Attempt number'),
                        'tracks' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'element' => new external_value(PARAM_RAW, 'Element name'),
                                    'value' => new external_value(PARAM_RAW, 'Element value')
                                ), 'Tracks data'
                            )
                        ),
                    ), 'SCO data'
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
