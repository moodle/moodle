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

namespace qbank_tagquestion\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/engine/datalib.php');
require_once($CFG->libdir . '/questionlib.php');

use core_tag_tag;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use qbank_tagquestion\form\tags_form;

/**
 * External qbank_tagquestion API.
 *
 * @package    qbank_tagquestion
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submit_tags extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
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
     */
    public static function execute($questionid, $contextid, $formdata) {
        global $DB, $CFG;

        $data = [];
        $result = ['status' => false];

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
                'questionid' => $questionid,
                'contextid' => $contextid,
                'formdata' => $formdata
        ]);

        $editingcontext = \context::instance_by_id($params['contextid']);
        self::validate_context($editingcontext);
        parse_str($params['formdata'], $data);

        if (!$question = $DB->get_record_sql('
                SELECT q.*, qc.contextid
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE q.id = ?', [$questionid])) {
            throw new \moodle_exception('questiondoesnotexist', 'question');
        }

        $cantag = question_has_capability_on($question, 'tag');
        $questioncontext = \context::instance_by_id($question->contextid);
        $contexts = new \core_question\local\bank\question_edit_contexts($editingcontext);

        $formoptions = [
                'editingcontext' => $editingcontext,
                'questioncontext' => $questioncontext,
                'contexts' => $contexts->all()
        ];

        $mform = new tags_form(null, $formoptions, 'post', '', null, $cantag, $data);

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
     */
    public static function  execute_returns() {
        return new external_single_structure([
                'status' => new external_value(PARAM_BOOL, 'status: true if success')
        ]);
    }

}
