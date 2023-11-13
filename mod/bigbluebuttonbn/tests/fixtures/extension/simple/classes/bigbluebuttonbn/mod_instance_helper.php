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

use stdClass;

/**
 * Class defining a way to deal with instance save/update/delete in extension
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class mod_instance_helper extends \mod_bigbluebuttonbn\local\extension\mod_instance_helper {
    /**
     * Runs any processes that must run before a bigbluebuttonbn insert/update.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    public function add_instance(stdClass $bigbluebuttonbn) {
        global $DB;
        $DB->insert_record('bbbext_simple', (object) [
            'bigbluebuttonbnid' => $bigbluebuttonbn->id,
            'newfield' => $bigbluebuttonbn->newfield ?? '',
            'completionextraisehandtwice' => $bigbluebuttonbn->completionextraisehandtwice ?? '',
        ]);
    }

    /**
     * Runs any processes that must be run after a bigbluebuttonbn insert/update.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    public function update_instance(stdClass $bigbluebuttonbn): void {
        global $DB;
        $record = $DB->get_record('bbbext_simple', [
            'bigbluebuttonbnid' => $bigbluebuttonbn->id,
        ]);
        // Just in case the instance was created before the extension was installed.
        if (empty($record)) {
            $record = new stdClass();
            $record->bigbluebuttonbnid = $bigbluebuttonbn->id;
            $record->newfield = $bigbluebuttonbn->newfield ?? '';
            $record->completionextraisehandtwice = $bigbluebuttonbn->completionextraisehandtwice ?? 0;
            $DB->insert_record('bbbext_simple', $record);
        } else {
            $record->newfield = $bigbluebuttonbn->newfield ?? '';
            $record->completionextraisehandtwice = $bigbluebuttonbn->completionextraisehandtwice ?? 0;
            $DB->update_record('bbbext_simple', $record);
        }
    }

    /**
     * Runs any processes that must be run after a bigbluebuttonbn delete.
     *
     * @param int $id
     */
    public function delete_instance(int $id): void {
        global $DB;
        $DB->delete_records('bbbext_simple', [
            'bigbluebuttonbnid' => $id,
        ]);
    }

    /**
     * Get any join table name that is used to store additional data for the instance.
     * @return string[]
     */
    public function get_join_tables(): array {
        return ['bbbext_simple'];
    }
}
