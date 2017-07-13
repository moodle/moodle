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
 * Choice module external API
 *
 * @package    mod_choice
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/choice/lib.php');

/**
 * Choice module external functions
 *
 * @package    mod_choice
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_choice_external extends external_api {

    /**
     * Describes the parameters for get_choices_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_choice_results_parameters() {
        return new external_function_parameters (array('choiceid' => new external_value(PARAM_INT, 'choice instance id')));
    }
    /**
     * Returns user's results for a specific choice
     * and a list of those users that did not answered yet.
     *
     * @param int $choiceid the choice instance id
     * @return array of responses details
     * @since Moodle 3.0
     */
    public static function get_choice_results($choiceid) {
        global $USER, $PAGE;

        $params = self::validate_parameters(self::get_choice_results_parameters(), array('choiceid' => $choiceid));

        if (!$choice = choice_get_choice($params['choiceid'])) {
            throw new moodle_exception("invalidcoursemodule", "error");
        }
        list($course, $cm) = get_course_and_cm_from_instance($choice, 'choice');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $groupmode = groups_get_activity_groupmode($cm);
        // Check if we have to include responses from inactive users.
        $onlyactive = $choice->includeinactive ? false : true;
        $users = choice_get_response_data($choice, $cm, $groupmode, $onlyactive);
        // Show those who haven't answered the question.
        if (!empty($choice->showunanswered)) {
            $choice->option[0] = get_string('notanswered', 'choice');
            $choice->maxanswers[0] = 0;
        }
        $results = prepare_choice_show_results($choice, $course, $cm, $users);

        $options = array();
        $fullnamecap = has_capability('moodle/site:viewfullnames', $context);
        foreach ($results->options as $optionid => $option) {

            $userresponses = array();
            $numberofuser = 0;
            $percentageamount = 0;
            if (property_exists($option, 'user') and
                (has_capability('mod/choice:readresponses', $context) or choice_can_view_results($choice))) {
                $numberofuser = count($option->user);
                $percentageamount = ((float)$numberofuser / (float)$results->numberofuser) * 100.0;
                if ($choice->publish) {
                    foreach ($option->user as $userresponse) {
                        $response = array();
                        $response['userid'] = $userresponse->id;
                        $response['fullname'] = fullname($userresponse, $fullnamecap);

                        $userpicture = new user_picture($userresponse);
                        $userpicture->size = 1; // Size f1.
                        $response['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);

                        // Add optional properties.
                        foreach (array('answerid', 'timemodified') as $field) {
                            if (property_exists($userresponse, 'answerid')) {
                                $response[$field] = $userresponse->$field;
                            }
                        }
                        $userresponses[] = $response;
                    }
                }
            }

            $options[] = array('id'               => $optionid,
                               'text'             => external_format_string($option->text, $context->id),
                               'maxanswer'        => $option->maxanswer,
                               'userresponses'    => $userresponses,
                               'numberofuser'     => $numberofuser,
                               'percentageamount' => $percentageamount
                              );
        }

        $warnings = array();
        return array(
            'options' => $options,
            'warnings' => $warnings
        );
    }

    /**
     * Describes the get_choice_results return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_choice_results_returns() {
        return new external_single_structure(
            array(
                'options' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'choice instance id'),
                            'text' => new external_value(PARAM_RAW, 'text of the choice'),
                            'maxanswer' => new external_value(PARAM_INT, 'maximum number of answers'),
                            'userresponses' => new external_multiple_structure(
                                 new external_single_structure(
                                     array(
                                        'userid' => new external_value(PARAM_INT, 'user id'),
                                        'fullname' => new external_value(PARAM_NOTAGS, 'user full name'),
                                        'profileimageurl' => new external_value(PARAM_URL, 'profile user image url'),
                                        'answerid' => new external_value(PARAM_INT, 'answer id', VALUE_OPTIONAL),
                                        'timemodified' => new external_value(PARAM_INT, 'time of modification', VALUE_OPTIONAL),
                                     ), 'User responses'
                                 )
                            ),
                            'numberofuser' => new external_value(PARAM_INT, 'number of users answers'),
                            'percentageamount' => new external_value(PARAM_FLOAT, 'percentage of users answers')
                        ), 'Options'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for mod_choice_get_choice_options.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_choice_options_parameters() {
        return new external_function_parameters (array('choiceid' => new external_value(PARAM_INT, 'choice instance id')));
    }

    /**
     * Returns options for a specific choice
     *
     * @param int $choiceid the choice instance id
     * @return array of options details
     * @since Moodle 3.0
     */
    public static function get_choice_options($choiceid) {
        global $USER;
        $warnings = array();
        $params = self::validate_parameters(self::get_choice_options_parameters(), array('choiceid' => $choiceid));

        if (!$choice = choice_get_choice($params['choiceid'])) {
            throw new moodle_exception("invalidcoursemodule", "error");
        }
        list($course, $cm) = get_course_and_cm_from_instance($choice, 'choice');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/choice:choose', $context);

        $groupmode = groups_get_activity_groupmode($cm);
        $onlyactive = $choice->includeinactive ? false : true;
        $allresponses = choice_get_response_data($choice, $cm, $groupmode, $onlyactive);

        $timenow = time();
        $choiceopen = true;
        $showpreview = false;

        if (!empty($choice->timeopen) && ($choice->timeopen > $timenow)) {
            $choiceopen = false;
            $warnings[1] = get_string("notopenyet", "choice", userdate($choice->timeopen));
            if ($choice->showpreview) {
                $warnings[2] = get_string('previewonly', 'choice', userdate($choice->timeopen));
                $showpreview = true;
            }
        }
        if (!empty($choice->timeclose) && ($timenow > $choice->timeclose)) {
            $choiceopen = false;
            $warnings[3] = get_string("expired", "choice", userdate($choice->timeclose));
        }

        $optionsarray = array();

        if ($choiceopen or $showpreview) {

            $options = choice_prepare_options($choice, $USER, $cm, $allresponses);

            foreach ($options['options'] as $option) {
                $optionarr = array();
                $optionarr['id']            = $option->attributes->value;
                $optionarr['text']          = external_format_string($option->text, $context->id);
                $optionarr['maxanswers']    = $option->maxanswers;
                $optionarr['displaylayout'] = $option->displaylayout;
                $optionarr['countanswers']  = $option->countanswers;
                foreach (array('checked', 'disabled') as $field) {
                    if (property_exists($option->attributes, $field) and $option->attributes->$field == 1) {
                        $optionarr[$field] = 1;
                    } else {
                        $optionarr[$field] = 0;
                    }
                }
                // When showpreview is active, we show options as disabled.
                if ($showpreview or ($optionarr['checked'] == 1 and !$choice->allowupdate)) {
                    $optionarr['disabled'] = 1;
                }
                $optionsarray[] = $optionarr;
            }
        }
        foreach ($warnings as $key => $message) {
            $warnings[$key] = array(
                'item' => 'choice',
                'itemid' => $cm->id,
                'warningcode' => $key,
                'message' => $message
            );
        }
        return array(
            'options' => $optionsarray,
            'warnings' => $warnings
        );
    }

    /**
     * Describes the get_choice_results return value.
     *
     * @return external_multiple_structure
     * @since Moodle 3.0
     */
    public static function get_choice_options_returns() {
        return new external_single_structure(
            array(
                'options' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'option id'),
                            'text' => new external_value(PARAM_RAW, 'text of the choice'),
                            'maxanswers' => new external_value(PARAM_INT, 'maximum number of answers'),
                            'displaylayout' => new external_value(PARAM_BOOL, 'true for orizontal, otherwise vertical'),
                            'countanswers' => new external_value(PARAM_INT, 'number of answers'),
                            'checked' => new external_value(PARAM_BOOL, 'we already answered'),
                            'disabled' => new external_value(PARAM_BOOL, 'option disabled'),
                            )
                    ), 'Options'
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for submit_choice_response.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function submit_choice_response_parameters() {
        return new external_function_parameters (
            array(
                'choiceid' => new external_value(PARAM_INT, 'choice instance id'),
                'responses' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'answer id'),
                    'Array of response ids'
                ),
            )
        );
    }

    /**
     * Submit choice responses
     *
     * @param int $choiceid the choice instance id
     * @param array $responses the response ids
     * @return array answers information and warnings
     * @since Moodle 3.0
     */
    public static function submit_choice_response($choiceid, $responses) {
        global $USER;

        $warnings = array();
        $params = self::validate_parameters(self::submit_choice_response_parameters(),
                                            array(
                                                'choiceid' => $choiceid,
                                                'responses' => $responses
                                            ));

        if (!$choice = choice_get_choice($params['choiceid'])) {
            throw new moodle_exception("invalidcoursemodule", "error");
        }
        list($course, $cm) = get_course_and_cm_from_instance($choice, 'choice');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/choice:choose', $context);

        $timenow = time();
        if (!empty($choice->timeopen) && ($choice->timeopen > $timenow)) {
            throw new moodle_exception("notopenyet", "choice", '', userdate($choice->timeopen));
        } else if (!empty($choice->timeclose) && ($timenow > $choice->timeclose)) {
            throw new moodle_exception("expired", "choice", '', userdate($choice->timeclose));
        }

        if (!choice_get_my_response($choice) or $choice->allowupdate) {
            // When a single response is given, we convert the array to a simple variable
            // in order to avoid choice_user_submit_response to check with allowmultiple even
            // for a single response.
            if (count($params['responses']) == 1) {
                $params['responses'] = reset($params['responses']);
            }
            choice_user_submit_response($params['responses'], $choice, $USER->id, $course, $cm);
        } else {
            throw new moodle_exception('missingrequiredcapability', 'webservice', '', 'allowupdate');
        }
        $answers = choice_get_my_response($choice);

        return array(
            'answers' => $answers,
            'warnings' => $warnings
        );
    }

    /**
     * Describes the submit_choice_response return value.
     *
     * @return external_multiple_structure
     * @since Moodle 3.0
     */
    public static function submit_choice_response_returns() {
        return new external_single_structure(
            array(
                'answers' => new external_multiple_structure(
                     new external_single_structure(
                         array(
                             'id'           => new external_value(PARAM_INT, 'answer id'),
                             'choiceid'     => new external_value(PARAM_INT, 'choiceid'),
                             'userid'       => new external_value(PARAM_INT, 'user id'),
                             'optionid'     => new external_value(PARAM_INT, 'optionid'),
                             'timemodified' => new external_value(PARAM_INT, 'time of last modification')
                         ), 'Answers'
                     )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_choice_parameters() {
        return new external_function_parameters(
            array(
                'choiceid' => new external_value(PARAM_INT, 'choice instance id')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $choiceid the choice instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_choice($choiceid) {
        global $CFG;

        $params = self::validate_parameters(self::view_choice_parameters(),
                                            array(
                                                'choiceid' => $choiceid
                                            ));
        $warnings = array();

        // Request and permission validation.
        if (!$choice = choice_get_choice($params['choiceid'])) {
            throw new moodle_exception("invalidcoursemodule", "error");
        }
        list($course, $cm) = get_course_and_cm_from_instance($choice, 'choice');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Trigger course_module_viewed event and completion.
        choice_view($choice, $course, $cm, $context);

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
    public static function view_choice_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_choices_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_choices_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of choices in a provided list of courses,
     * if no list is provided all choices that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array of choices details
     * @since Moodle 3.0
     */
    public static function get_choices_by_courses($courseids = array()) {
        global $CFG;

        $returnedchoices = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_choices_by_courses_parameters(), array('courseids' => $courseids));

        $courses = array();
        if (empty($params['courseids'])) {
            $courses = enrol_get_my_courses();
            $params['courseids'] = array_keys($courses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $courses);

            // Get the choices in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $choices = get_all_instances_in_courses("choice", $courses);
            foreach ($choices as $choice) {
                $context = context_module::instance($choice->coursemodule);
                // Entry to return.
                $choicedetails = array();
                // First, we return information that any user can see in the web interface.
                $choicedetails['id'] = $choice->id;
                $choicedetails['coursemodule'] = $choice->coursemodule;
                $choicedetails['course'] = $choice->course;
                $choicedetails['name']  = external_format_string($choice->name, $context->id);
                // Format intro.
                list($choicedetails['intro'], $choicedetails['introformat']) =
                    external_format_text($choice->intro, $choice->introformat,
                                            $context->id, 'mod_choice', 'intro', null);
                    $choicedetails['introfiles'] = external_util::get_area_files($context->id, 'mod_choice', 'intro', false,
                                                                                    false);

                if (has_capability('mod/choice:choose', $context)) {
                    $choicedetails['publish']  = $choice->publish;
                    $choicedetails['showresults']  = $choice->showresults;
                    $choicedetails['showpreview']  = $choice->showpreview;
                    $choicedetails['timeopen']  = $choice->timeopen;
                    $choicedetails['timeclose']  = $choice->timeclose;
                    $choicedetails['display']  = $choice->display;
                    $choicedetails['allowupdate']  = $choice->allowupdate;
                    $choicedetails['allowmultiple']  = $choice->allowmultiple;
                    $choicedetails['limitanswers']  = $choice->limitanswers;
                    $choicedetails['showunanswered']  = $choice->showunanswered;
                    $choicedetails['includeinactive']  = $choice->includeinactive;
                }

                if (has_capability('moodle/course:manageactivities', $context)) {
                    $choicedetails['timemodified']  = $choice->timemodified;
                    $choicedetails['completionsubmit']  = $choice->completionsubmit;
                    $choicedetails['section']  = $choice->section;
                    $choicedetails['visible']  = $choice->visible;
                    $choicedetails['groupmode']  = $choice->groupmode;
                    $choicedetails['groupingid']  = $choice->groupingid;
                }
                $returnedchoices[] = $choicedetails;
            }
        }
        $result = array();
        $result['choices'] = $returnedchoices;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the mod_choice_get_choices_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_choices_by_courses_returns() {
        return new external_single_structure(
            array(
                'choices' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Choice instance id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'Choice name'),
                            'intro' => new external_value(PARAM_RAW, 'The choice intro'),
                            'introformat' => new external_format_value('intro'),
                            'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                            'publish' => new external_value(PARAM_BOOL, 'If choice is published', VALUE_OPTIONAL),
                            'showresults' => new external_value(PARAM_INT, '0 never, 1 after answer, 2 after close, 3 always',
                                                                VALUE_OPTIONAL),
                            'display' => new external_value(PARAM_INT, 'Display mode (vertical, horizontal)', VALUE_OPTIONAL),
                            'allowupdate' => new external_value(PARAM_BOOL, 'Allow update', VALUE_OPTIONAL),
                            'allowmultiple' => new external_value(PARAM_BOOL, 'Allow multiple choices', VALUE_OPTIONAL),
                            'showunanswered' => new external_value(PARAM_BOOL, 'Show users who not answered yet', VALUE_OPTIONAL),
                            'includeinactive' => new external_value(PARAM_BOOL, 'Include inactive users', VALUE_OPTIONAL),
                            'limitanswers' => new external_value(PARAM_BOOL, 'Limit unswers', VALUE_OPTIONAL),
                            'timeopen' => new external_value(PARAM_INT, 'Date of opening validity', VALUE_OPTIONAL),
                            'timeclose' => new external_value(PARAM_INT, 'Date of closing validity', VALUE_OPTIONAL),
                            'showpreview' => new external_value(PARAM_BOOL, 'Show preview before timeopen', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification', VALUE_OPTIONAL),
                            'completionsubmit' => new external_value(PARAM_BOOL, 'Completion on user submission', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section id', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_BOOL, 'Visible', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL),
                        ), 'Choices'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for delete_choice_responses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function delete_choice_responses_parameters() {
        return new external_function_parameters (
            array(
                'choiceid' => new external_value(PARAM_INT, 'choice instance id'),
                'responses' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'response id'),
                    'Array of response ids, empty for deleting all the user responses',
                    VALUE_DEFAULT,
                    array()
                ),
            )
        );
    }

    /**
     * Delete the given submitted responses in a choice
     *
     * @param int $choiceid the choice instance id
     * @param array $responses the response ids,  empty for deleting all the user responses
     * @return array status information and warnings
     * @throws moodle_exception
     * @since Moodle 3.0
     */
    public static function delete_choice_responses($choiceid, $responses = array()) {

        $status = false;
        $warnings = array();
        $params = self::validate_parameters(self::delete_choice_responses_parameters(),
                                            array(
                                                'choiceid' => $choiceid,
                                                'responses' => $responses
                                            ));

        if (!$choice = choice_get_choice($params['choiceid'])) {
            throw new moodle_exception("invalidcoursemodule", "error");
        }
        list($course, $cm) = get_course_and_cm_from_instance($choice, 'choice');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/choice:choose', $context);

        // If we have the capability, delete all the passed responses.
        if (has_capability('mod/choice:deleteresponses', $context)) {
            if (empty($params['responses'])) {
                // Get all the responses for the choice.
                $params['responses'] = array_keys(choice_get_all_responses($choice));
            }
            $status = choice_delete_responses($params['responses'], $choice, $cm, $course);
        } else if ($choice->allowupdate) {
            // Check if we can delate our own responses.
            $timenow = time();
            if (!empty($choice->timeclose) && ($timenow > $choice->timeclose)) {
                throw new moodle_exception("expired", "choice", '', userdate($choice->timeclose));
            }
            // Delete only our responses.
            $myresponses = array_keys(choice_get_my_response($choice));

            if (empty($params['responses'])) {
                $todelete = $myresponses;
            } else {
                $todelete = array();
                foreach ($params['responses'] as $response) {
                    if (!in_array($response, $myresponses)) {
                        $warnings[] = array(
                            'item' => 'response',
                            'itemid' => $response,
                            'warningcode' => 'nopermissions',
                            'message' => 'No permission to delete this response'
                        );
                    } else {
                        $todelete[] = $response;
                    }
                }
            }

            $status = choice_delete_responses($todelete, $choice, $cm, $course);
        } else {
            // The user requires the capability to delete responses.
            throw new required_capability_exception($context, 'mod/choice:deleteresponses', 'nopermissions', '');
        }

        return array(
            'status' => $status,
            'warnings' => $warnings
        );
    }

    /**
     * Describes the delete_choice_responses return value.
     *
     * @return external_multiple_structure
     * @since Moodle 3.0
     */
    public static function delete_choice_responses_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status, true if everything went right'),
                'warnings' => new external_warnings(),
            )
        );
    }

}
