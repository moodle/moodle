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

namespace mod_quiz\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

use external_function_parameters;
use external_single_structure;
use external_value;
use external_api;
use stdClass;

/**
 * Update the filter condition for a random question.
 *
 * @package    mod_quiz
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_filter_condition extends external_api {

    /**
     * Parameters for the web service function
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters ([
            'cmid' => new external_value(PARAM_INT, 'The cmid of the quiz'),
            'slotid' => new external_value(PARAM_INT, 'The quiz slot ID for the random question.'),
            'filtercondition' => new external_value(PARAM_TEXT, 'Filter condition'),
        ]);
    }

    /**
     * Add random questions.
     *
     * @param int $cmid course module id
     * @param int $slotid The quiz slot id
     * @param string $filtercondition
     * @return array  result
     */
    public static function execute(
        int $cmid,
        int $slotid,
        string $filtercondition,
    ): array {
        global  $DB;

        [
            'cmid' => $cmid,
            'slotid' => $slotid,
            'filtercondition' => $filtercondition,
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'slotid' => $slotid,
            'filtercondition' => $filtercondition,
        ]);

        // Validate context.
        $thiscontext = \context_module::instance($cmid);
        self::validate_context($thiscontext);
        require_capability('mod/quiz:manage', $thiscontext);

        // Validate question category is supplied in filter.
        $decodedfiltercondition = json_decode($filtercondition, true);
        $categoryid = (isset($decodedfiltercondition['filter']['category']['values'][0])) ?
            $decodedfiltercondition['filter']['category']['values'][0] : false;
        if (!$categoryid) {
            throw new \moodle_exception('invalidcategoryid');
        }

        // Validate question category exists.
        $categorycontextid = $DB->get_field('question_categories', 'contextid', ['id' => $categoryid], MUST_EXIST);

        // Validate set_reference record exists for this quiz and slot.
        $setreferenceid = $DB->get_field('question_set_references', 'id', [
            'itemid' => $slotid,
            'questionarea' => 'slot',
            'component' => 'mod_quiz',
        ], MUST_EXIST);

        // Update set_reference record with new filtercondition and context.
        $update = new stdClass();
        $update->id = $setreferenceid;
        $update->filtercondition = $filtercondition;
        $update->questionscontextid = $categorycontextid;
        $DB->update_record('question_set_references', $update);

        return ['message' => get_string('updatefilterconditon_success', 'mod_quiz')];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_single_structure([
            'message' => new external_value(PARAM_TEXT, 'Message', VALUE_OPTIONAL)
        ]);
    }
}
