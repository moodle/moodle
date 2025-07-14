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

namespace core\tests\session;

use core\clock;
use core\di;
use core\session\database;

/**
 * Mock handler methods class.
 *
 * @package    core
 * @author     Darren Cocco <moodle@darren.cocco.id.au>
 * @author     Trisha Milan <trishamilan@catalyst-au.net>
 * @copyright  2022 Monash University (http://www.monash.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mock_handler extends database {
    #[\Override]
    public function init(): bool {
        // Nothing special to do in the mock.
        return true;
    }

    #[\Override]
    public function session_exists($sid): bool {
        global $DB;

        return $DB->record_exists('sessions', ['sid' => $sid]);
    }

    /**
     * Insert a new session record to be used in unit tests.
     *
     * @param \stdClass $record
     * @return int Inserted record id.
     */
    public function add_test_session(\stdClass $record): int {
        global $DB, $USER;

        $data = new \stdClass();
        $data->state = $record->state ?? 0;
        $data->sid = $record->sid ?? session_id();
        $data->sessdata = $record->sessdata ?? null;
        $data->userid = $record->userid ?? $USER->id;
        $data->timecreated = $record->timecreated ?? di::get(clock::class)->time();
        $data->timemodified = $record->timemodified ?? di::get(clock::class)->time();
        $data->firstip = $record->firstip ?? getremoteaddr();
        $data->lastip = $record->lastip ?? getremoteaddr();

        return $DB->insert_record('sessions', $data);
    }

    #[\Override]
    public function get_all_sessions(): \Iterator {
        global $DB;

        $records = $DB->get_records('sessions');
        return new \ArrayIterator($records);
    }

    /**
     * Returns the number of all sessions stored.
     *
     * @return int
     */
    public function count_sessions(): int {
        global $DB;

        return $DB->count_records('sessions');
    }
}
