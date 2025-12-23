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

namespace core_question;

use core\context\module;

/**
 * Unit tests for versions
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\versions
 */
final class versions_test extends \advanced_testcase {
    /**
     * Generate 3 questions - one with 3 versions, one with 2, and one with 1.
     *
     * @return array
     */
    protected function create_question_versions(): array {
        $qbank = $this->getDataGenerator()->create_module('qbank', ['course' => SITEID]);
        $qbankcontext = module::instance($qbank->cmid);
        $category = question_get_default_category($qbankcontext->id);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $q1v1 = $questiongenerator->create_question('shortanswer', null, ['category' => $category->id]);
        $q1v2 = $questiongenerator->update_question($q1v1);
        $questiongenerator->update_question($q1v2);
        $q2v1 = $questiongenerator->create_question('shortanswer', null, ['category' => $category->id]);
        $questiongenerator->update_question($q2v1);
        $q3v1 = $questiongenerator->create_question('shortanswer', null, ['category' => $category->id]);

        return [
            $q1v1->questionbankentryid,
            $q2v1->questionbankentryid,
            $q3v1->questionbankentryid,
        ];
    }

    /**
     * We should get the correct next version for each question bank entry.
     */
    public function test_get_next_version(): void {
        $this->resetAfterTest();
        [$qbe1, $qbe2, $qbe3] = $this->create_question_versions();

        $this->assertEquals(4, versions::get_next_version($qbe1));
        $this->assertEquals(3, versions::get_next_version($qbe2));
        $this->assertEquals(2, versions::get_next_version($qbe3));
    }

    /**
     * Set the correct next version numbers for existing question bank entries.
     */
    public function test_get_next_version_null(): void {
        global $DB;
        $this->resetAfterTest();
        [$qbe1, $qbe2, $qbe3] = $this->create_question_versions();

        // Null the nextversion values so they have to be recalculated.
        $DB->set_field('question_bank_entries', 'nextversion', null);

        $this->assertEquals(4, versions::get_next_version($qbe1));
        $this->assertEquals(3, versions::get_next_version($qbe2));
        $this->assertEquals(2, versions::get_next_version($qbe3));
    }

    /**
     * The next version should be correctly incremented.
     */
    public function test_increment_next_version(): void {
        $this->resetAfterTest();
        global $DB;
        [$qbe1, $qbe2, $qbe3] = $this->create_question_versions();

        $this->assertEquals(4, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe1]));
        $this->assertEquals(3, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe2]));
        $this->assertEquals(2, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe3]));

        $this->assertEquals(3, versions::get_next_version($qbe2));

        // The specified question bank entry has had its nextversion incremented, the others are the same.
        $this->assertEquals(4, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe1]));
        $this->assertEquals(4, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe2]));
        $this->assertEquals(2, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe3]));
    }

    /**
     * We can get the next version without incrementing it.
     */
    public function test_get_without_increment(): void {
        $this->resetAfterTest();
        global $DB;
        [, $qbe2] = $this->create_question_versions();

        $this->assertEquals(3, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe2]));

        $this->assertEquals(3, versions::get_next_version($qbe2, increment: false));

        // The nextversion value has not changed.
        $this->assertEquals(3, $DB->get_field('question_bank_entries', 'nextversion', ['id' => $qbe2]));
    }
}
