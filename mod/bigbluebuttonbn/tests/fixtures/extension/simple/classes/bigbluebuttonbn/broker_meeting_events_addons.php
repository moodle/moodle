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

namespace bbbext_simple\bigbluebuttonbn;

/**
 * When meeting_events callback is implemented by BigBlueButton, Moodle receives a POST request
 * which is processed in the function using super globals.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class broker_meeting_events_addons extends \mod_bigbluebuttonbn\local\extension\broker_meeting_events_addons {
    /**
     * Data processing action
     */
    public function process_action() {
        global $DB;
        if ($this->instance) {
            $bigbluebuttonbnid = $this->instance->get_instance_id();
            $record = $DB->get_record('bbbext_simple', [
                'bigbluebuttonbnid' => $bigbluebuttonbnid,
            ]);
            if ($record) {
                $record->meetingevents = $this->data;
                return $DB->update_record('bbbext_simple', $record);
            }
        }
        return false;
    }
}
