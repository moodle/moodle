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
 * Mock events for xAPI testing.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\event;

use context_system;
use core_xapi\local\statement;

defined('MOODLE_INTERNAL') || die();

/**
 * xAPI statement webservice testing event.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xapi_test_statement_post extends \core\event\base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return "xAPI test statement";
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "User '$this->userid' send a statement to component '$this->component'";
    }

    /**
     * Compare if a given statement is similar to the one on the record.
     *
     * The information stored in the logstore is not exactly a xAPI standard.
     * Similar checks for actor, verb, object (+ definition) and result for now.
     *
     * @param statement $statement An xAPI compatible statement.
     * @return bool True if the $statement represents this event.
     */
    public function compare_statement(statement $statement): bool {
        // Check minified version.
        $calculatedfields = ['actor', 'id', 'timestamp', 'stored', 'version'];
        foreach ($calculatedfields as $field) {
            if (isset($this->data['other'][$field])) {
                return false;
            }
        }
        // Check verb structure.
        $data = $statement->get_verb()->get_data();
        if ($this->data['other']['verb']['id'] != $data->id) {
            return false;
        }
        // Check user.
        $users = $statement->get_all_users();
        if (empty($users) || !isset($users[$this->data['userid']])) {
            return false;
        }
        // Check object.
        $data = $statement->get_object()->get_data();
        if ($this->data['other']['object']['id'] != $data->id) {
            return false;
        }
        return true;
    }
}
