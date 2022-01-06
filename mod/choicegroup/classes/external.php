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
 * Choice group module external API
 *
 * @package    mod_choicegroup
 * @category   external
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/choicegroup/lib.php');

/**
 * Choice group module external functions
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_choicegroup_external extends external_api {

    /**
     * Describes the parameters for get_choicegroup_options.
     *
     * @return external_function_parameters
     */
    public static function get_choicegroup_options_parameters() {
        return new external_function_parameters(
            array(
                'choicegroupid' => new external_value(PARAM_INT, 'Choice group instance id'),
                'userid' => new external_value(PARAM_INT, 'User id')
            )
        );
    }

    /**
     * Returns the options list for the provided choice group instance.
     *
     * @param int $choicegroupid The choice group id.
     * @param int $userid The user id.
     * @param boolean $alloptionsdisabled True when all the options should be disabled, because activity is not open or a limit has been reached.
     * @return array The choice group options.
     */
    public static function get_choicegroup_options($choicegroupid, $userid, $alloptionsdisabled = false) {
        global $CFG, $choicegroup_groups;

        $result = array();
        $returnedoptions = array();
        $warnings = array();

        $params = array(
            'choicegroupid' => $choicegroupid,
            'userid' => $userid
        );
        $params = self::validate_parameters(self::get_choicegroup_options_parameters(), $params);
        $choicegroup = choicegroup_get_choicegroup($choicegroupid);
        $cm = get_coursemodule_from_instance('choicegroup', $choicegroupid);
        $context = context_module::instance($cm->id);

        self::validate_context($context);
        require_capability('mod/choicegroup:choose', $context);

        $groupmode = groups_get_activity_groupmode($cm);
        $allresponses = choicegroup_get_response_data($choicegroup, $cm, $groupmode, $choicegroup->onlyactive);
        $answers = choicegroup_get_user_answer($choicegroup, $userid, true);

        foreach ($choicegroup->option as $optionid => $text) {
            if (isset($text)) {
                $option = array();
                $option['id'] = $optionid;
                $option['groupid'] = $text;
                $option['name'] = $choicegroup_groups[$text]->name;
                $option['maxanswers'] = $choicegroup->maxanswers[$optionid];
                $option['displaylayout'] = $choicegroup->display;

                if (isset($allresponses[$text])) {
                    $option['countanswers'] = count($allresponses[$text]);
                } else {
                    $option['countanswers'] = 0;
                }
                // Check if the option has been answered previously by the user.
                $option['checked'] = false;
                if (is_array($answers)) {
                    foreach($answers as $answer) {
                        if ($answer && $text == $answer->id) {
                            $option['checked'] = true;
                        }
                    }
                }
                // Check if the option has to be disabled because the limit has been reached.
                $limitreached = $choicegroup->limitanswers && ($option['countanswers'] >= $option['maxanswers']);
                $stillnotanswered = $option['checked'] === false;
                $option['disabled'] = $alloptionsdisabled;
                if ($limitreached && $stillnotanswered) {
                    $option['disabled'] = true;
                    $option['name'] .= ' '.get_string('full', 'choicegroup');
                }

                $returnedoptions[] = $option;
            }
        }

        $result = array();
        $result['options'] = $returnedoptions;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_choicegroup_options return value.
     *
     * @return external_single_structure
     */
    public static function get_choicegroup_options_returns() {

        return new external_single_structure(
            array(
                'options' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Option id'),
                            'groupid' => new external_value(PARAM_INT, 'Group id'),
                            'name' => new external_value(PARAM_RAW, 'Group choice name'),
                            'maxanswers' => new external_value(PARAM_INT, 'Maximum number of accepted answers', VALUE_OPTIONAL),
                            'displaylayout' => new external_value(PARAM_INT, 'Display layout', VALUE_OPTIONAL),
                            'countanswers' => new external_value(PARAM_INT, 'Current number of answers', VALUE_OPTIONAL),
                            'checked' => new external_value(PARAM_BOOL, 'Checked', VALUE_OPTIONAL),
                            'disabled' => new external_value(PARAM_BOOL, 'Disabled', VALUE_OPTIONAL),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for view_choicegroup.
     *
     * @return external_function_parameters
     */
    public static function view_choicegroup_parameters() {
        return new external_function_parameters(
            array(
                'choicegroupid' => new external_value(PARAM_INT, 'Choice group instance id')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $choicegroupid The choice group id.
     * @return array of warnings and status result
     * @throws moodle_exception
     */
    public static function view_choicegroup($choicegroupid) {
        global $DB;

        $params = array(
            'choicegroupid' => $choicegroupid
        );
        $params = self::validate_parameters(self::view_choicegroup_parameters(), $params);
        $warnings = array();

        // Request and permission validation.
        $choicegroup = $DB->get_record('choicegroup', array('id' => $params['choicegroupid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($choicegroup, 'choicegroup');

        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/choicegroup:choose', $context);

        $event = \mod_choicegroup\event\course_module_viewed::create(array(
            'objectid' => $choicegroup->id,
            'context' => $context,
        ));
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('choicegroup', $choicegroup);
        $event->trigger();

        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function view_choicegroup_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for submit_response.
     *
     * @return external_function_parameters
     */
    public static function submit_choicegroup_response_parameters() {
        return new external_function_parameters (
            array(
                'choicegroupid' => new external_value(PARAM_INT, 'Choice group instance id'),
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'Data name'),
                            'value' => new external_value(PARAM_RAW, 'Data value'),
                        )
                    ),
                    'The data to be saved',
                    VALUE_DEFAULT,
                    array()
                )
            )
        );
    }

    /**
     * Returns the options list for the provided choice group instance.
     *
     * @param int $choicegroupid The choice group id.
     * @param array $data The user responses.
     * @return array The choice group options.
     */
    public static function submit_choicegroup_response($choicegroupid, $data) {
        global $CFG, $DB, $USER;

        $warnings = array();

        $params = array(
            'choicegroupid' => $choicegroupid,
            'data' => $data
        );

        $params = self::validate_parameters(self::submit_choicegroup_response_parameters(), $params);

        if (!$choicegroup = choicegroup_get_choicegroup($choicegroupid)) {
            throw new moodle_exception('invalidcoursemodule', 'error');
        }
        list($course, $cm) = get_course_and_cm_from_instance($choicegroup, 'choicegroup');
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/choicegroup:choose', $context);

        $timenow = time();
        if (!empty($choicegroup->timeopen) && ($choicegroup->timeopen > $timenow)) {
            throw new moodle_exception('notopenyet', 'choicegroup', '', userdate($choicegroup->timeopen));
        } else if (!empty($choicegroup->timeclose) && ($timenow > $choicegroup->timeclose)) {
            throw new moodle_exception('expired', 'choicegroup', '', userdate($choice->timeclose));
        }

        $responses = self::parse_data_to_responses(
            $data,
            $choicegroup->multipleenrollmentspossible
        );
        if (empty($responses)) {
            // Update completion state
            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) && $choicegroup->completionsubmit) {
                $completion->update_state($cm, COMPLETION_INCOMPLETE);
            }
        }

        if (!choicegroup_get_user_answer($choicegroup, $USER) || $choicegroup->allowupdate) {
            if ($choicegroup->multipleenrollmentspossible) {
                foreach($choicegroup->option as $optionid => $text) {
                    if (in_array($optionid, $responses)) {
                        choicegroup_user_submit_response($optionid, $choicegroup, $USER->id, $course, $cm);
                    } else {
                        // Remove group selection if selected.
                        if (groups_is_member($text, $USER->id)) {
                            $answer_value_group = $DB->get_record('groups', array('id' => $text), 'id, name', MUST_EXIST);
                            groups_remove_member($answer_value_group->id, $USER->id);
                            $eventparams = array(
                                'context' => $context,
                                'objectid' => $choicegroup->id
                            );
                            $event = \mod_choicegroup\event\choice_removed::create($eventparams);
                            $event->add_record_snapshot('course_modules', $cm);
                            $event->add_record_snapshot('course', $course);
                            $event->add_record_snapshot('choicegroup', $choicegroup);
                            $event->trigger();
                        }
                    }
                }
            } else { // !multipleenrollmentspossible
                if (count($responses) == 1) {
                    $responses = reset($responses);
                    choicegroup_user_submit_response($responses, $choicegroup, $USER->id, $course, $cm);
                }
            }
        } else {
            throw new moodle_exception('missingrequiredcapability', 'webservice', '', 'allowupdate');
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the submit_response return value.
     *
     * @return external_single_structure
     */
    public static function submit_choicegroup_response_returns() {

        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Extract user responses from the WS data.
     * @param  array $data The data received from the WS.
     * @param  boolean $allowmultiple True if more than one response can be selected.
     * @return array The optionid from the selected responses.
     */
    protected static function parse_data_to_responses($data, $allowmultiple) {
        $responses = array();
        foreach($data as $index => $datavalue) {
            $name = $datavalue['name'];
            $value = $datavalue['value'];
            if ($allowmultiple) {
                if ($name != 'responses' && $value === 'true') {
                    $responses[] = substr($name, strrpos($name, '_')+1);
                }
            } else if ($name === 'responses') {
                $responses[] = $value;
                break;
            }
        }

        return $responses;
    }

    /**
     * Describes the parameters for delete_choicegroup_responses.
     *
     * @return external_function_parameters
     */
    public static function delete_choicegroup_responses_parameters() {
        return new external_function_parameters (
            array(
                'choicegroupid' => new external_value(PARAM_INT, 'Choice group instance id'),
            )
        );
    }

    /**
     * Delete the given submitted responses in a choice group
     *
     * @param int $choicegroupid The choicegroup instance id
     * @return array status information and warnings
     * @throws moodle_exception
     */
    public static function delete_choicegroup_responses($choicegroupid) {
        global $USER, $DB;

        $status = false;
        $warnings = array();

        $params = array(
            'choicegroupid' => $choicegroupid
        );

        $params = self::validate_parameters(self::submit_choicegroup_response_parameters(), $params);

        if (!$choicegroup = choicegroup_get_choicegroup($choicegroupid)) {
            throw new moodle_exception('invalidcoursemodule', 'error');
        }
        list($course, $cm) = get_course_and_cm_from_instance($choicegroup, 'choicegroup');
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/choicegroup:choose', $context);

        $timenow = time();
        if (!empty($choicegroup->timeopen) && ($choicegroup->timeopen > $timenow)) {
            throw new moodle_exception('notopenyet', 'choicegroup', '', userdate($choicegroup->timeopen));
        } else if (!empty($choicegroup->timeclose) && ($timenow > $choicegroup->timeclose)) {
            throw new moodle_exception('expired', 'choicegroup', '', userdate($choice->timeclose));
        }

        $answergiven = choicegroup_get_user_answer($choicegroup, $USER, true);
        if (!empty($answergiven)) {
            if ($choicegroup->allowupdate && !$choicegroup->multipleenrollmentspossible) {
                $params = array('groupid' => reset($answergiven)->id, 'userid' => $USER->id);
                $groupmember = $DB->get_record('groups_members', $params, 'id', MUST_EXIST);
                $status = choicegroup_delete_responses([$groupmember->id], $choicegroup, $cm, $course);
            } else {
                throw new moodle_exception('missingrequiredcapability', 'webservice', '', 'allowupdate');
            }
        } else {
            // User didn't give any answer, so there's no need to delete anything.
            $status = true;
        }

        $result = array(
            'status' => $status,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the delete_choicegroup_responses return value.
     *
     * @return external_multiple_structure
     */
    public static function delete_choicegroup_responses_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status, True if everything went right'),
                'warnings' => new external_warnings(),
            )
        );
    }

}
