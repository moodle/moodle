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
 * External question API
 *
 * @package    core_question
 * @category   external
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/question/engine/lib.php');

/**
 * Question external functions
 *
 * @package    core_question
 * @category   external
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.1
 */
class core_question_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function update_flag_parameters() {
        return new external_function_parameters(
            array(
                'qubaid' => new external_value(PARAM_INT, 'the question usage id.'),
                'questionid' => new external_value(PARAM_INT, 'the question id'),
                'qaid' => new external_value(PARAM_INT, 'the question_attempt id'),
                'slot' => new external_value(PARAM_INT, 'the slot number within the usage'),
                'checksum' => new external_value(PARAM_ALPHANUM, 'computed checksum with the last three arguments and
                             the users username'),
                'newstate' => new external_value(PARAM_BOOL, 'the new state of the flag. true = flagged')
            )
        );
    }

    /**
     * Update the flag state of a question attempt.
     *
     * @param int $qubaid the question usage id.
     * @param int $questionid the question id.
     * @param int $qaid the question_attempt id.
     * @param int $slot the slot number within the usage.
     * @param string $checksum checksum, as computed by {@link get_toggle_checksum()}
     *      corresponding to the last three arguments and the users username.
     * @param bool $newstate the new state of the flag. true = flagged.
     * @return array (success infos and fail infos)
     * @since Moodle 3.1
     */
    public static function update_flag($qubaid, $questionid, $qaid, $slot, $checksum, $newstate) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::update_flag_parameters(),
            array(
                'qubaid' => $qubaid,
                'questionid' => $questionid,
                'qaid' => $qaid,
                'slot' => $slot,
                'checksum' => $checksum,
                'newstate' => $newstate
            )
        );

        $warnings = array();
        self::validate_context(context_system::instance());

        // The checksum will be checked to provide security flagging other users questions.
        question_flags::update_flag($params['qubaid'], $params['questionid'], $params['qaid'], $params['slot'], $params['checksum'],
                                    $params['newstate']);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function update_flag_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function submit_tags_form_parameters() {
        return new external_function_parameters([
                'formdata' => new external_value(PARAM_RAW, 'The data from the tag form'),
        ]);
    }

    /**
     * Handles the tags form submission.
     *
     * @param string $formdata The question tag form data in a URI encoded param string
     * @return array The created or modified question tag
     * @throws moodle_exception
     */
    public static function submit_tags_form($formdata) {
        global $USER, $DB, $CFG;

        $data = [];
        $result = ['status' => false];

        // Parameter validation.
        $params = self::validate_parameters(self::submit_tags_form_parameters(), ['formdata' => $formdata]);
        $context = \context_user::instance($USER->id);

        self::validate_context($context);
        parse_str($params['formdata'], $data);

        if (!empty($data['id'])) {
            $questionid = clean_param($data['id'], PARAM_INT);
            $question = $DB->get_record('question', array('id' => $questionid));

            require_once($CFG->libdir . '/questionlib.php');
            $cantag = question_has_capability_on($question, 'tag');

            require_once($CFG->dirroot . '/question/type/tags_form.php');
            $mform = new \core_question\form\tags(null, null, 'post', '', null, $cantag, $data);

            if ($validateddata = $mform->get_data()) {
                // Due to a mform bug, if there's no tags set on the tag element, it submits the name as the value.
                // The only way to discover is checking if the tag element is an array.
                if ($cantag) {
                    if (is_array($validateddata->tags)) {
                        $categorycontext = context::instance_by_id($validateddata->contextid);

                        core_tag_tag::set_item_tags('core_question', 'question', $validateddata->id,
                            $categorycontext, $validateddata->tags);

                        $result['status'] = true;
                    } else {
                        // If the tags element is not array, this means we don't have any tags to be set.
                        // This is the only way to assume the user removed all tags from the question.
                        core_tag_tag::remove_all_item_tags('core_question', 'question', $validateddata->id);

                        $result['status'] = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns description of method result value.
     */
    public static function  submit_tags_form_returns() {
        return new external_single_structure([
                'status' => new external_value(PARAM_BOOL, 'status: true if success')
        ]);
    }
}
