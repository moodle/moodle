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

namespace qbank_editquestion\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/bank.php');

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use qbank_editquestion\editquestion_helper;
use question_bank;

/**
 * Update question status external api.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_question_version_status extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'questionid' => new external_value(PARAM_INT, 'The question id'),
            'status' => new external_value(PARAM_TEXT, 'The updated question status')
        ]);
    }

    /**
     * Handles the status form submission.
     *
     * @param int $questionid The question id.
     * @param string $status The updated question status.
     * @return array The created or modified question tag
     */
    public static function execute($questionid, $status) {
        global $DB;

        $result = [
            'status' => false,
            'statusname' => '',
            'error' => ''
        ];

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'questionid' => $questionid,
            'status' => $status
        ]);

        $statuslist = editquestion_helper::get_question_status_list();
        $statusexists = array_key_exists($status, $statuslist);
        if (!$statusexists) {
            return [
                'status' => false,
                'statusname' => '',
                'error' => get_string('unrecognizedstatus', 'qbank_editquestion')
            ];
        }
        $question = question_bank::load_question($params['questionid']);
        $editingcontext = \context::instance_by_id($question->contextid);
        self::validate_context($editingcontext);
        $canedit = question_has_capability_on($question, 'edit');
        if ($canedit) {
            $versionrecord = $DB->get_record('question_versions', ['questionid' => $params['questionid']]);
            $versionrecord->status = $params['status'];
            $DB->update_record('question_versions', $versionrecord);
            question_bank::notify_question_edited($question->id);
            $result = [
                'status' => true,
                'statusname' => editquestion_helper::get_question_status_string($versionrecord->status),
                'error' => ''
            ];
            $event = \core\event\question_updated::create_from_question_instance($question, $editingcontext);
            $event->trigger();
        }

        return $result;
    }

    /**
     * Returns description of method result value.
     */
    public static function  execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status: true if success'),
            'statusname' => new external_value(PARAM_RAW, 'statusname: name of the status'),
            'error' => new external_value(PARAM_TEXT, 'Error message if error exists')
        ]);
    }
}
