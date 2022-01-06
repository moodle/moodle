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
 * The core_xapi test class for xAPI statements.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace fake_component\xapi;

use core_xapi\local\statement;
use core_xapi\handler as handler_base;
use core_xapi\event\xapi_test_statement_post;
use context_system;
use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class xapi_handler testing dummie class.
 *
 * @package core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 */
class handler extends handler_base {

    /**
     * Convert statements to event.
     *
     * Convert a statement object into a Moodle xAPI Event. If a statement is accepted
     * by validate_statement the component must provide a event to handle that statement,
     * otherwise the statement will be rejected.
     *
     * @param statement $statement the statement object
     * @return \core\event\base|null a Moodle event to trigger
     */
    public function statement_to_event(statement $statement): ?base {
        global $USER;

        // Validate verb.
        $validvalues = [
                'cook',
                'http://adlnet.gov/expapi/verbs/answered'
            ];
        $verbid = $statement->get_verb_id();
        if (!in_array($verbid, $validvalues)) {
            return null;
        }
        // Validate object.
        $validvalues = [
                'paella',
                'http://adlnet.gov/expapi/activities/example'
            ];
        $activityid = $statement->get_activity_id();
        if (!in_array($activityid, $validvalues)) {
            return null;
        }

        if ($this->supports_group_actors()) {
            $users = $statement->get_all_users();
            // In most cases we can use $USER->id as the event userid but because
            // this is just a test class it checks first for $USER and, if not
            // present just pick the first one.
            $user = $users[$USER->id] ?? array_shift($users);
        } else {
            $user = $statement->get_user();
        }

        // Convert into a Moodle event.
        $minstatement = $statement->minify();
        $params = array(
            'other' => $minstatement,
            'context' => context_system::instance(),
            'userid' => $user->id,
        );
        return xapi_test_statement_post::create($params);
    }

    /**
     * Return true if group actor is enabled.
     *
     * NOTE: the use of a global is only for testing. We need to change
     * the behaviour from the PHPUnitTest to test all possible scenarios.
     *
     * Note: this method must be overridden by the plugins which want to
     * use groups in statements.
     *
     * @return bool
     */
    public function supports_group_actors(): bool {
        global $CFG;
        if (isset($CFG->xapitestforcegroupactors)) {
            return $CFG->xapitestforcegroupactors;
        }
        return parent::supports_group_actors();
    }
}
