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

require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/engine/datalib.php');
require_once($CFG->libdir . '/questionlib.php');

use core\context\module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use mod_quiz\quiz_settings;

/**
 * External api for changing the question version in the quiz.
 *
 * @package    mod_quiz
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submit_question_version extends external_api {

    /**
     * Parameters for the submit_question_version.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'slotid' => new external_value(PARAM_INT),
            'newversion' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * Set the questions slot parameters to display the question template.
     *
     * @param int $slotid Slot ID to display.
     * @param int|null $newversion The version to set. Passing null means 'always latest'.
     *                             For historical reasons, 0 also means 'always latest'.
     * @return array
     */
    public static function execute(int $slotid, ?int $newversion): array {
        global $DB;
        $params = self::validate_parameters(self::execute_parameters(), ['slotid' => $slotid, 'newversion' => $newversion]);
        $slot = $DB->get_record('quiz_slots', ['id' => $slotid], '*', MUST_EXIST);

        $context = module::instance(get_course_and_cm_from_instance($slot->quizid, 'quiz')[1]->id);
        self::validate_context($context);
        require_capability('mod/quiz:manage', $context);

        $quizobj = quiz_settings::create($slot->quizid);

        // This WS historically (and wrongly) accepted 0 to mean 'always latest'. The correct behaviour
        // is that null implies awlays latest. To preserve backwards compatibility, we continue to accept
        // 0, but just turn it in to null before passing to the appropriate API. See: MDL-82587.
        $newversionnormalised = $params['newversion'] === 0 ? null : $params['newversion'];
        return ['result' => $quizobj->get_structure()->update_slot_version($slot->id, $newversionnormalised)];
    }

    /**
     * Define the webservice response.
     *
     * @return \core_external\external_description
     */
    public static function execute_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, '')

        ]);
    }
}
