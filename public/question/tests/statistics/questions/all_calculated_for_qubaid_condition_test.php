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

namespace core_question\statistics\questions;

use advanced_testcase;
use qubaid_list;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/lib.php');

/**
 * Tests for all_calculated_for_qubaid_condition.
 *
 * @package   core_question
 * @copyright 2026 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\statistics\questions\all_calculated_for_qubaid_condition::get_cached
 */
final class all_calculated_for_qubaid_condition_test extends advanced_testcase {
    /**
     * Test that get_cached() gracefully skips a DB row whose slot is not in questionstats.
     */
    public function test_get_cached_skips_stale_slot_row(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a question so the FK constraint on question_statistics.questionid is satisfied.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        // Build a qubaid_list and derive the hashcode that get_cached() will query.
        $qubaids = new qubaid_list([]);
        $hashcode = $qubaids->get_hash_code();
        $now = time();

        // Insert a valid slot row (slot 1 — will be pre-populated in the stats object).
        $DB->insert_record('question_statistics', (object)[
            'hashcode'    => $hashcode,
            'timemodified' => $now,
            'questionid'  => $q->id,
            'subquestion' => 0,
            'slot'        => 1,
            'variant'     => null,
            's'           => 0,
            'negcovar'    => 0,
        ]);

        // Insert a stale slot row (slot 99 — NOT pre-populated in the stats object).
        // This simulates a row left behind after an admin deleted specific stats rows.
        $DB->insert_record('question_statistics', (object)[
            'hashcode'    => $hashcode,
            'timemodified' => $now,
            'questionid'  => $q->id,
            'subquestion' => 0,
            'slot'        => 99,
            'variant'     => null,
            's'           => 0,
            'negcovar'    => 0,
        ]);

        // Prepare the stats object: pre-populate slot 1 only.
        $stats = new all_calculated_for_qubaid_condition();
        // The initialise_for_slot() method needs maxmark and number properties on the question object.
        $q->maxmark = 1.0;
        $q->number = 1;
        $stats->initialise_for_slot(1, $q);

        // The get_cached() call must complete without a fatal error.
        $stats->get_cached($qubaids);

        // The fix emits a debugging() notice for the stale slot — assert it was called.
        $this->assertDebuggingCalled();

        // Slot 1 should have been loaded from the DB row.
        $this->assertArrayHasKey(1, $stats->questionstats, 'Valid slot 1 should be present in questionstats');
        // The stale row should be skipped.
        $this->assertArrayNotHasKey(99, $stats->questionstats, 'Stale slot 99 must not be in questionstats');
    }

    /**
     * Test that get_cached() gracefully skips an orphan variant row whose parent
     * sub-question entry is missing from subquestionstats.
     */
    public function test_get_cached_skips_orphan_variant_row(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a question so the FK constraint on question_statistics.questionid is satisfied.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        // Build a qubaid_list and derive the hashcode that get_cached() will query.
        $qubaids = new qubaid_list([]);
        $hashcode = $qubaids->get_hash_code();
        $now = time();

        // Insert ONLY a variant row for $q->id (subquestion=1, variant=1, slot=null).
        // The parent non-variant row is intentionally omitted to simulate the orphan condition.
        $DB->insert_record('question_statistics', (object)[
            'hashcode'    => $hashcode,
            'timemodified' => $now,
            'questionid'  => $q->id,
            'subquestion' => 1,
            'slot'        => null,
            'variant'     => 1,
            's'           => 0,
            'negcovar'    => 0,
        ]);

        // Prepare a stats object with no pre-populated sub-questions (parent never set).
        $stats = new all_calculated_for_qubaid_condition();

        // The get_cached() call must complete without a fatal error.
        $stats->get_cached($qubaids);

        // The orphan variant should not create a subquestionstats entry.
        $this->assertArrayNotHasKey(
            $q->id,
            $stats->subquestionstats,
            'Orphan variant must not create a subquestionstats entry'
        );
        // The fix emits a debugging() notice for the stale subquestion.
        $this->assertDebuggingCalled();
    }
}
