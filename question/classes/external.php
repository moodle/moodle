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
require_once($CFG->dirroot . '/question/engine/datalib.php');
require_once($CFG->libdir . '/questionlib.php');

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
     * @deprecated since Moodle 4.0
     * @see \qbank_tagquestion\external\qbank_tagquestion_external
     * @todo Final deprecation on Moodle 4.4 MDL-72438
     */
    public static function submit_tags_form_parameters() {
        return new external_function_parameters([
                'questionid' => new external_value(PARAM_INT, 'The question id'),
                'contextid' => new external_value(PARAM_INT, 'The editing context id'),
                'formdata' => new external_value(PARAM_RAW, 'The data from the tag form'),
        ]);
    }

    /**
     * Handles the tags form submission.
     *
     * @param int $questionid The question id.
     * @param int $contextid The editing context id.
     * @param string $formdata The question tag form data in a URI encoded param string
     * @return array The created or modified question tag
     * @deprecated since Moodle 4.0
     * @see \qbank_tagquestion\external\qbank_tagquestion_external
     * @todo Final deprecation on Moodle 4.4 MDL-72438
     */
    public static function submit_tags_form($questionid, $contextid, $formdata) {
        global $DB, $CFG;

        $data = [];
        $result = ['status' => false];

        // Parameter validation.
        $params = self::validate_parameters(self::submit_tags_form_parameters(), [
            'questionid' => $questionid,
            'contextid' => $contextid,
            'formdata' => $formdata
        ]);

        $editingcontext = \context::instance_by_id($contextid);
        self::validate_context($editingcontext);
        parse_str($params['formdata'], $data);

        if (!$question = $DB->get_record_sql('
                SELECT q.*, qc.contextid
                FROM {question} q
                JOIN {question_categories} qc ON qc.id = q.category
                WHERE q.id = ?', [$questionid])) {
            throw new \moodle_exception('questiondoesnotexist', 'question');
        }

        require_once($CFG->libdir . '/questionlib.php');

        $cantag = question_has_capability_on($question, 'tag');
        $questioncontext = \context::instance_by_id($question->contextid);
        $contexts = new \core_question\local\bank\question_edit_contexts($editingcontext);

        $formoptions = [
            'editingcontext' => $editingcontext,
            'questioncontext' => $questioncontext,
            'contexts' => $contexts->all()
        ];

        $mform = new \qbank_tagquestion\form\tags_form(null, $formoptions, 'post', '', null, $cantag, $data);

        if ($validateddata = $mform->get_data()) {
            if ($cantag) {
                if (isset($validateddata->tags)) {
                    // Due to a mform bug, if there's no tags set on the tag element, it submits the name as the value.
                    // The only way to discover is checking if the tag element is an array.
                    $tags = is_array($validateddata->tags) ? $validateddata->tags : [];

                    core_tag_tag::set_item_tags('core_question', 'question', $validateddata->id,
                        $questioncontext, $tags);

                    $result['status'] = true;
                }

                if (isset($validateddata->coursetags)) {
                    $coursetags = is_array($validateddata->coursetags) ? $validateddata->coursetags : [];
                    core_tag_tag::set_item_tags('core_question', 'question', $validateddata->id,
                        $editingcontext->get_course_context(false), $coursetags);

                    $result['status'] = true;
                }
            }
        }

        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @deprecated since Moodle 4.0
     * @see \qbank_tagquestion\external\qbank_tagquestion_external
     * @todo Final deprecation on Moodle 4.4 MDL-72438
     */
    public static function  submit_tags_form_returns() {
        return new external_single_structure([
                'status' => new external_value(PARAM_BOOL, 'status: true if success')
        ]);
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     * @todo Final deprecation on Moodle 4.4 MDL-72438
     */
    public static function submit_tags_form_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function get_random_question_summaries_parameters() {
        return new external_function_parameters([
                'categoryid' => new external_value(PARAM_INT, 'Category id to find random questions'),
                'includesubcategories' => new external_value(PARAM_BOOL, 'Include the subcategories in the search'),
                'tagids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Tag id')
                ),
                'contextid' => new external_value(PARAM_INT,
                    'Context id that the questions will be rendered in (used for exporting)'),
                'limit' => new external_value(PARAM_INT, 'Maximum number of results to return',
                    VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'Number of items to skip from the begging of the result set',
                    VALUE_DEFAULT, 0)
        ]);
    }

    /**
     * Gets the list of random questions for the given criteria. The questions
     * will be exported in a summaries format and won't include all of the
     * question data.
     *
     * @param int $categoryid Category id to find random questions
     * @param bool $includesubcategories Include the subcategories in the search
     * @param int[] $tagids Only include questions with these tags
     * @param int $contextid The context id where the questions will be rendered
     * @param int $limit Maximum number of results to return
     * @param int $offset Number of items to skip from the beginning of the result set.
     * @return array The list of questions and total question count.
     */
    public static function get_random_question_summaries(
        $categoryid,
        $includesubcategories,
        $tagids,
        $contextid,
        $limit = 0,
        $offset = 0
    ) {
        global $DB, $PAGE;

        // Parameter validation.
        $params = self::validate_parameters(
            self::get_random_question_summaries_parameters(),
            [
                'categoryid' => $categoryid,
                'includesubcategories' => $includesubcategories,
                'tagids' => $tagids,
                'contextid' => $contextid,
                'limit' => $limit,
                'offset' => $offset
            ]
        );
        $categoryid = $params['categoryid'];
        $includesubcategories = $params['includesubcategories'];
        $tagids = $params['tagids'];
        $contextid = $params['contextid'];
        $limit = $params['limit'];
        $offset = $params['offset'];

        $context = \context::instance_by_id($contextid);
        self::validate_context($context);

        $categorycontextid = $DB->get_field('question_categories', 'contextid', ['id' => $categoryid], MUST_EXIST);
        $categorycontext = \context::instance_by_id($categorycontextid);
        $editcontexts = new \core_question\local\bank\question_edit_contexts($categorycontext);
        // The user must be able to view all questions in the category that they are requesting.
        $editcontexts->require_cap('moodle/question:viewall');

        $loader = new \core_question\local\bank\random_question_loader(new qubaid_list([]));
        // Only load the properties we require from the DB.
        $properties = \core_question\external\question_summary_exporter::get_mandatory_properties();
        $questions = $loader->get_questions($categoryid, $includesubcategories, $tagids, $limit, $offset, $properties);
        $totalcount = $loader->count_questions($categoryid, $includesubcategories, $tagids);
        $renderer = $PAGE->get_renderer('core');

        $formattedquestions = array_map(function($question) use ($context, $renderer) {
            $exporter = new \core_question\external\question_summary_exporter($question, ['context' => $context]);
            return $exporter->export($renderer);
        }, $questions);

        return [
            'totalcount' => $totalcount,
            'questions' => $formattedquestions
        ];
    }

    /**
     * Returns description of method result value.
     */
    public static function  get_random_question_summaries_returns() {
        return new external_single_structure([
            'totalcount' => new external_value(PARAM_INT, 'total number of questions in result set'),
            'questions' => new external_multiple_structure(
                \core_question\external\question_summary_exporter::get_read_structure()
            )
        ]);
    }
}
