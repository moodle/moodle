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

namespace gradepenalty_duedate\tests;

use advanced_testcase;
use context_system;

/**
 * Base test.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class penalty_testcase extends advanced_testcase {
    /**
     * Reset after test.
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Create sample rules.
     *
     * @param int|null $contextid The context id.
     */
    public function create_sample_rules(?int $contextid = null): void {
        global $DB;

        // Use system context by default.
        $contextid ??= context_system::instance()->id;

        // Remove initial rule.
        $DB->delete_records('gradepenalty_duedate_rule', ['contextid' => $contextid]);

        $rules = [
            ['contextid' => $contextid, 'overdueby' => DAYSECS, 'penalty' => 10, 'sortorder' => 0],
            ['contextid' => $contextid, 'overdueby' => DAYSECS * 2, 'penalty' => 20, 'sortorder' => 1],
            ['contextid' => $contextid, 'overdueby' => DAYSECS * 3, 'penalty' => 30, 'sortorder' => 2],
            ['contextid' => $contextid, 'overdueby' => DAYSECS * 4, 'penalty' => 40, 'sortorder' => 3],
            ['contextid' => $contextid, 'overdueby' => DAYSECS * 5, 'penalty' => 50, 'sortorder' => 4],
        ];
        foreach ($rules as $rule) {
            $DB->insert_record('gradepenalty_duedate_rule', (object)$rule);
        }
    }
}
