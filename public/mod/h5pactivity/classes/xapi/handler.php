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

namespace mod_h5pactivity\xapi;

use mod_h5pactivity\local\attempt;
use mod_h5pactivity\local\manager;
use mod_h5pactivity\event\statement_received;
use core_xapi\local\statement;
use core_xapi\handler as handler_base;
use core\event\base as event_base;
use core_xapi\local\state;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/h5pactivity/lib.php');

/**
 * Class xapi_handler for H5P statements and states.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler extends handler_base {

    /**
     * Convert a statement object into a Moodle xAPI Event.
     *
     * If a statement is accepted by the xAPI webservice the component must provide
     * an event to handle that statement, otherwise the statement will be rejected.
     *
     * @param statement $statement
     * @return core\event\base|null a Moodle event to trigger
     */
    public function statement_to_event(statement $statement): ?event_base {

        // Only process statements with results.
        $xapiresult = $statement->get_result();
        if (empty($xapiresult)) {
            return null;
        }

        // Statements can contain any verb, for security reasons each
        // plugin needs to filter it's own specific verbs. For now the only verbs the H5P
        // plugin keeps track on are "answered" and "completed" because they are realted to grading.
        // In the future this list can be increased to track more user interactions.
        $validvalues = [
                'http://adlnet.gov/expapi/verbs/answered',
                'http://adlnet.gov/expapi/verbs/completed',
            ];
        $xapiverbid = $statement->get_verb_id();
        if (!in_array($xapiverbid, $validvalues)) {
            return null;
        }

        // Validate object.
        $xapiobject = $statement->get_activity_id();

        // H5P add some extra params to ID to define subcontents.
        $parts = explode('?', $xapiobject, 2);
        $contextid = array_shift($parts);
        $subcontent = str_replace('subContentId=', '', array_shift($parts) ?? '');
        if (empty($contextid) || !is_numeric($contextid)) {
            return null;
        }
        $context = \context::instance_by_id($contextid);
        if (!$context instanceof \context_module) {
            return null;
        }

        // As the activity does not accept group statement, the code can assume that the
        // statement user is valid (otherwise the xAPI library will reject the statement).
        $user = $statement->get_user();
        if (!has_capability('mod/h5pactivity:view', $context, $user)) {
            return null;
        }

        $cm = get_coursemodule_from_id('h5pactivity', $context->instanceid, 0, false);
        if (!$cm) {
            return null;
        }

        $manager = manager::create_from_coursemodule($cm);

        if (!($manager->is_tracking_enabled() && $manager->can_submit($user))) {
            return null;
        }

        // For now, attempts are only processed on a single batch starting with the final "completed"
        // and "answered" statements (this could change in the future). This initial statement have no
        // subcontent defined as they are the main finishing statement. For this reason, this statement
        // indicates a new attempt creation. This way, simpler H5P activies like multichoice can generate
        // an attempt each time the user answers while complex like question-set could group all questions
        // in a single attempt (using subcontents).
        if (empty($subcontent)) {
            $attempt = attempt::new_attempt($user, $cm);
        } else {
            $attempt = attempt::last_attempt($user, $cm);
        }
        if (!$attempt) {
            return null;
        }
        $result = $attempt->save_statement($statement, $subcontent);
        if (!$result) {
            return null;
        }

        // Update activity if necessary.
        if ($attempt->get_scoreupdated()) {
            $grader = $manager->get_grader();
            $grader->update_grades($user->id);
        }

        // Convert into a Moodle event.
        $minstatement = $statement->minify();
        $params = [
            'other' => $minstatement,
            'context' => $context,
            'objectid' => $cm->instance,
            'userid' => $user->id,
        ];
        return statement_received::create($params);
    }

    /**
     * Validate a xAPI state.
     *
     * Check if the state is valid for this handler.
     *
     * This method is used also for the state get requests so the validation
     * cannot rely on having state data.
     *
     * @param state $state
     * @return bool if the state is valid or not
     */
    protected function validate_state(state $state): bool {
        $xapiobject = $state->get_activity_id();

        // H5P add some extra params to ID to define subcontents.
        $parts = explode('?', $xapiobject, 2);
        $contextid = array_shift($parts);
        if (empty($contextid) || !is_numeric($contextid)) {
            return false;
        }

        try {
            $context = \context::instance_by_id($contextid);
            if (!$context instanceof \context_module) {
                return false;
            }
        } catch (moodle_exception $exception) {
            return false;
        }

        $cm = get_coursemodule_from_id('h5pactivity', $context->instanceid, 0, false);
        if (!$cm) {
            return false;
        }

        // If tracking is not enabled or the user can't submit, the state won't be considered valid.
        $manager = manager::create_from_coursemodule($cm);
        $user = $state->get_user();
        if (!($manager->is_tracking_enabled() && $manager->can_submit($user))) {
            return false;
        }

        return true;
    }
}
