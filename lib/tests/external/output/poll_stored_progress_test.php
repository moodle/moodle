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

namespace core\external\output;

/**
 * Unit tests for poll_stored_progress
 *
 * @package   core
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\external\poll_stored_progress_test
 */
final class poll_stored_progress_test extends \advanced_testcase {
    /**
     * Throw an exception if the wrong data type is passed for an ID.
     */
    public function test_execute_invalid_id(): void {
        $debuginfo = 'Invalid external api parameter: the value is "foo", the server was expecting "int" type';
        $pollstoredprogress = new poll_stored_progress();
        $this->expectExceptionObject(new \invalid_parameter_exception($debuginfo));
        $pollstoredprogress->execute(['foo']);
    }

    /**
     * Passing a list of IDs returns a corresponding list of records.
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $progress1 = $generator->create_stored_progress();
        $progress2 = $generator->create_stored_progress();
        $falseid = $progress2->id + 1;

        $ids = [
            $progress1->id,
            $progress2->id,
            $falseid,
        ];

        $pollstoredprogress = new poll_stored_progress();
        $result = $pollstoredprogress->execute($ids);

        $this->assertEquals($progress1->id, $result[$progress1->id]['id']);
        $this->assertEquals($progress1->idnumber, $result[$progress1->id]['uniqueid']);
        $this->assertEquals($progress2->id, $result[$progress2->id]['id']);
        $this->assertEquals($progress2->idnumber, $result[$progress2->id]['uniqueid']);
        $this->assertEquals($falseid, $result[$falseid]['id']);
        $this->assertEmpty($result[$falseid]['uniqueid']); // Empty when no matching record is found.
    }
}
