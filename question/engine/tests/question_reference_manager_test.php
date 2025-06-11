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

use advanced_testcase;
use context_system;
use core_question\local\bank\question_version_status;
use core_question_generator;

/**
 * Unit tests for the {@see question_reference_manager} class.
 *
 * @package   core_question
 * @category  test
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\question_reference_manager
 */
final class question_reference_manager_test extends advanced_testcase {

    public function test_questions_with_references(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $systemcontext = context_system::instance();

        // Create three questions, each with three versions.
        // In each case, the third version is draft.
        $cat = $questiongenerator->create_question_category();
        $q1v1 = $questiongenerator->create_question('truefalse', null, ['name' => 'Q1V1', 'category' => $cat->id]);
        $q1v2 = $questiongenerator->update_question($q1v1, null, ['name' => 'Q1V2']);
        $q1v3 = $questiongenerator->update_question($q1v2, null,
                ['name' => 'Q1V3', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);
        $q2v1 = $questiongenerator->create_question('truefalse', null, ['name' => 'Q2V1', 'category' => $cat->id]);
        $q2v2 = $questiongenerator->update_question($q2v1, null, ['name' => 'Q2V2']);
        $q2v3 = $questiongenerator->update_question($q2v2, null,
                ['name' => 'Q2V3', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);
        $q3v1 = $questiongenerator->create_question('truefalse', null, ['name' => 'Q3V1', 'category' => $cat->id]);
        $q3v2 = $questiongenerator->update_question($q3v1, null, ['name' => 'Q3V2']);
        $q3v3 = $questiongenerator->update_question($q3v2, null,
                ['name' => 'Q3V3', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);

        // Create specific references to Q2V1 and Q2V3.
        $DB->insert_record('question_references', ['usingcontextid' => $systemcontext->id,
                'component' => 'core_question', 'questionarea' => 'test', 'itemid' => 0,
                'questionbankentryid' => $q2v1->questionbankentryid, 'version' => 1]);
        $DB->insert_record('question_references', ['usingcontextid' => $systemcontext->id,
                'component' => 'core_question', 'questionarea' => 'test', 'itemid' => 1,
                'questionbankentryid' => $q2v1->questionbankentryid, 'version' => 3]);

        // Create an always-latest reference to Q3.
        $DB->insert_record('question_references', ['usingcontextid' => $systemcontext->id,
                'component' => 'core_question', 'questionarea' => 'test', 'itemid' => 2,
                'questionbankentryid' => $q3v1->questionbankentryid, 'version' => null]);

        // Verify which versions of Q1 are used.
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q1v1->id]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q1v2->id]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q1v3->id]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q1v1->id, $q1v2->id, $q1v3->id]));

        // Verify which versions of Q2 are used.
        $this->assertEqualsCanonicalizing([$q2v1->id],
                question_reference_manager::questions_with_references([$q2v1->id]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q2v2->id]));
        $this->assertEqualsCanonicalizing([$q2v3->id],
                question_reference_manager::questions_with_references([$q2v3->id]));
        $this->assertEqualsCanonicalizing([$q2v1->id, $q2v3->id],
                question_reference_manager::questions_with_references([$q2v1->id, $q2v2->id, $q2v3->id]));

        // Verify which versions of Q1 are used.
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q3v1->id]));
        $this->assertEqualsCanonicalizing([$q3v2->id],
                question_reference_manager::questions_with_references([$q3v2->id]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([$q3v3->id]));
        $this->assertEqualsCanonicalizing([$q3v2->id],
                question_reference_manager::questions_with_references([$q3v1->id, $q3v2->id, $q3v3->id]));

        // Do some combined queries.
        $this->assertEqualsCanonicalizing([$q2v1->id, $q2v3->id, $q3v2->id],
                question_reference_manager::questions_with_references([
                        $q1v1->id, $q1v2->id, $q1v3->id,
                        $q2v1->id, $q2v2->id, $q2v3->id,
                        $q3v1->id, $q3v2->id, $q3v3->id]));
        $this->assertEqualsCanonicalizing([$q2v1->id, $q2v3->id, $q3v2->id],
                question_reference_manager::questions_with_references([$q2v1->id, $q2v3->id, $q3v2->id]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([
                        $q1v1->id, $q1v2->id, $q1v3->id,
                        $q2v2->id,
                        $q3v1->id, $q3v3->id]));

        // Test some edge cases.
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([]));
        $this->assertEqualsCanonicalizing([],
                question_reference_manager::questions_with_references([-1]));

    }
}
