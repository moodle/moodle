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

use core\attribute\deprecated;
use core\context\module;
use core\di;
use mod_quiz\quiz_settings;

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

    /**
     * Return input version and expected output renumbers for versions::renumber_versions.
     *
     * @return array
     * @todo Deprecate in 6.0 MDL-87844 for removal in 7.0 MDL-87845.
     */
    public static function renumber_versions_provider(): array {
        return [
            'simple sequential versions' => [
                'versions' => [
                    (object) [
                        'id' => 1,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 2,
                        'version' => 2,
                    ],
                    (object) [
                        'id' => 3,
                        'version' => 3,
                    ],
                ],
                'expectedrenumbers' => [
                    1 => (object) [
                        'oldversion' => 1,
                        'newversion' => 4,
                    ],
                    2 => (object) [
                        'oldversion' => 2,
                        'newversion' => 5,
                    ],
                    3 => (object) [
                        'oldversion' => 3,
                        'newversion' => 6,
                    ],
                ],
            ],
            'duplicate versions' => [
                'versions' => [
                    (object) [
                        'id' => 1,
                        'version' => 2,
                    ],
                    (object) [
                        'id' => 2,
                        'version' => 2,
                    ],
                    (object) [
                        'id' => 3,
                        'version' => 2,
                    ],
                ],
                'expectedrenumbers' => [
                    1 => (object) [
                        'oldversion' => 2,
                        'newversion' => 4,
                    ],
                    2 => (object) [
                        'oldversion' => 2,
                        'newversion' => 5,
                    ],
                    3 => (object) [
                        'oldversion' => 2,
                        'newversion' => 6,
                    ],
                ],
            ],
            'non-sequential duplicate versions' => [
                'versions' => [
                    (object) [
                        'id' => 1,
                        'version' => 2,
                    ],
                    (object) [
                        'id' => 2,
                        'version' => 2,
                    ],
                    (object) [
                        'id' => 3,
                        'version' => 3,
                    ],
                    (object) [
                        'id' => 4,
                        'version' => 2,
                    ],
                    (object) [
                        'id' => 5,
                        'version' => 3,
                    ],
                    (object) [
                        'id' => 6,
                        'version' => 4,
                    ],
                ],
                'expectedrenumbers' => [
                    1 => (object) [
                        'oldversion' => 2,
                        'newversion' => 7,
                    ],
                    2 => (object) [
                        'oldversion' => 2,
                        'newversion' => 8,
                    ],
                    3 => (object) [
                        'oldversion' => 3,
                        'newversion' => 9,
                    ],
                    4 => (object) [
                        'oldversion' => 2,
                        'newversion' => 10,
                    ],
                    5 => (object) [
                        'oldversion' => 3,
                        'newversion' => 11,
                    ],
                    6 => (object) [
                        'oldversion' => 4,
                        'newversion' => 12,
                    ],
                ],
            ],
            'duplicate versions with gaps, count higher than max' => [
                'versions' => [
                    (object) [
                        'id' => 1,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 2,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 3,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 4,
                        'version' => 3,
                    ],
                    (object) [
                        'id' => 5,
                        'version' => 4,
                    ],
                    (object) [
                        'id' => 6,
                        'version' => 4,
                    ],
                ],
                'expectedrenumbers' => [
                    1 => (object) [
                        'oldversion' => 1,
                        'newversion' => 7,
                    ],
                    2 => (object) [
                        'oldversion' => 1,
                        'newversion' => 8,
                    ],
                    3 => (object) [
                        'oldversion' => 1,
                        'newversion' => 9,
                    ],
                    4 => (object) [
                        'oldversion' => 3,
                        'newversion' => 10,
                    ],
                    5 => (object) [
                        'oldversion' => 4,
                        'newversion' => 11,
                    ],
                    6 => (object) [
                        'oldversion' => 4,
                        'newversion' => 12,
                    ],
                ],
            ],
            'duplicate versions with gaps, max higher than count' => [
                'versions' => [
                    (object) [
                        'id' => 1,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 2,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 3,
                        'version' => 1,
                    ],
                    (object) [
                        'id' => 4,
                        'version' => 5,
                    ],
                    (object) [
                        'id' => 5,
                        'version' => 5,
                    ],
                    (object) [
                        'id' => 6,
                        'version' => 7,
                    ],
                ],
                'expectedrenumbers' => [
                    1 => (object) [
                        'oldversion' => 1,
                        'newversion' => 8,
                    ],
                    2 => (object) [
                        'oldversion' => 1,
                        'newversion' => 9,
                    ],
                    3 => (object) [
                        'oldversion' => 1,
                        'newversion' => 10,
                    ],
                    4 => (object) [
                        'oldversion' => 5,
                        'newversion' => 11,
                    ],
                    5 => (object) [
                        'oldversion' => 5,
                        'newversion' => 12,
                    ],
                    6 => (object) [
                        'oldversion' => 7,
                        'newversion' => 13,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test renumbering question versions, ensuring any duplicate version numbers are correctly resolved.
     *
     * @param $versions
     * @param $expectedrenumbers
     * @return array
     * @dataProvider renumber_versions_provider
     * @todo Deprecate in 6.0 MDL-87844 for removal in 7.0 MDL-87845.
     */
    public function test_renumber_versions(array $versions, array $expectedrenumbers): void {
        $this->assertEquals($expectedrenumbers, versions::renumber_versions($versions));
    }

    /**
     * Resolve any violations of the unique questionbankentryid-version key on the question_versions table.
     *
     * It is not possible to set up actual records with duplicate version numbers, as this would violate the key. Instead
     * we use a mocked DB object to return records that look like they violate the key, then we make sure the actual records are
     * updated in a way that doesn't result in any violations.
     *
     * @todo Deprecate in 6.0 MDL-87844 for removal in 7.0 MDL-87845.
     */
    public function test_resolve_unique_version_violations(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Required for mock DB.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);

        $questiongenerator = $generator->get_plugin_generator('core_question');
        $questions = $questiongenerator->create_categories_and_questions(
            module::instance($qbank->cmid),
            [
                'testcategory' => [
                    'q1' => 'truefalse',
                    'q2' => 'truefalse',
                ],
            ],
        );
        // Generate 2 questions, with 6 versions each.
        $q1v1 = $questions['testcategory']['q1'];
        $q1v2 = $questiongenerator->update_question($q1v1, overrides: ['name' => 'q1 version 2']);
        $q1v3 = $questiongenerator->update_question($q1v2, overrides: ['name' => 'q1 version 3']);
        $q1v4 = $questiongenerator->update_question($q1v3, overrides: ['name' => 'q1 version 4']);
        $q1v5 = $questiongenerator->update_question($q1v4, overrides: ['name' => 'q1 version 5']);
        $q1v6 = $questiongenerator->update_question($q1v5, overrides: ['name' => 'q1 version 6']);
        $q2v1 = $questions['testcategory']['q2'];
        $q2v2 = $questiongenerator->update_question($q2v1, overrides: ['name' => 'q2 version 2']);
        $q2v3 = $questiongenerator->update_question($q2v2, overrides: ['name' => 'q2 version 3']);
        $q2v4 = $questiongenerator->update_question($q2v3, overrides: ['name' => 'q2 version 4']);
        $q2v5 = $questiongenerator->update_question($q2v4, overrides: ['name' => 'q2 version 5']);
        $q2v6 = $questiongenerator->update_question($q2v5, overrides: ['name' => 'q2 version 6']);

        // Create some quizzes referencing different versions of the questions.
        $quiz1 = $generator->create_module('quiz', ['course' => $course->id]);
        $quiz2 = $generator->create_module('quiz', ['course' => $course->id]);
        $quiz3 = $generator->create_module('quiz', ['course' => $course->id]);

        // Quiz 1 has version 2 of each question.
        quiz_add_quiz_question($q1v1->id, $quiz1);
        quiz_add_quiz_question($q2v1->id, $quiz1);
        $quiz1structure = quiz_settings::create($quiz1->id)->get_structure();
        $quiz1slot1 = $quiz1structure->get_slot_by_number(1);
        $quiz1slot2 = $quiz1structure->get_slot_by_number(2);
        $quiz1structure->update_slot_version($quiz1slot1->id, 2);
        $quiz1structure->update_slot_version($quiz1slot2->id, 2);

        // Quiz 2 has version 5 of each question.
        quiz_add_quiz_question($q1v1->id, $quiz2);
        quiz_add_quiz_question($q2v1->id, $quiz2);
        $quiz2structure = quiz_settings::create($quiz2->id)->get_structure();
        $quiz2slot1 = $quiz2structure->get_slot_by_number(1);
        $quiz2slot2 = $quiz2structure->get_slot_by_number(2);
        $quiz2structure->update_slot_version($quiz2slot1->id, 5);
        $quiz2structure->update_slot_version($quiz2slot2->id, 5);

        // Quiz 3 has the latest version of each question.
        quiz_add_quiz_question($q1v1->id, $quiz3);
        quiz_add_quiz_question($q2v1->id, $quiz3);

        // Set up mock records showing that question 1 has multiple versions with version number 2.
        $mockviolations = [
            $q1v1->questionbankentryid,
        ];

        $mockversions = [
            (object) [
                'id' => $q1v1->versionid,
                'version' => 1,
                'timecreated' => time() - 100,
            ],
            (object) [
                'id' => $q1v2->versionid,
                'version' => 2,
                'timecreated' => time() - 99,
            ],
            (object) [
                'id' => $q1v3->versionid,
                'version' => 2,
                'timecreated' => time() - 98,
            ],
            (object) [
                'id' => $q1v4->versionid,
                'version' => 4,
                'timecreated' => time() - 97,
            ],
            (object) [
                'id' => $q1v5->versionid,
                'version' => 5,
                'timecreated' => time() - 96,
            ],
            (object) [
                'id' => $q1v6->versionid,
                'version' => 6,
                'timecreated' => time() - 95,
            ],
        ];

        // Mock the get_fieldset_sql and get_records_sql to return our mocked results in response to the specific queries we expect.
        // Any other queries will run as normal on the real $db object.
        $db = di::get(\moodle_database::class);
        $mockdb = $this->getMockBuilder($db::class)
            ->onlyMethods(['get_fieldset_sql', 'get_records_sql'])
            ->getMock();
        $mockdb->method('get_fieldset_sql')
            ->willReturnCallback(fn($sql, $params) => match (true) {
                str_starts_with(trim($sql), 'SELECT DISTINCT questionbankentryid') => $mockviolations,
                default => $db->get_fieldset_sql($sql, $params),
            });
        $mockdb->method('get_records_sql')
            ->willReturnCallback(fn($sql, $params) => match (true) {
                str_starts_with(trim($sql), 'SELECT qv.id, qv.version, q.timecreated') => $mockversions,
                default => $db->get_records_sql($sql, $params),
            });
        $cfg = $db->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }
        $mockdb->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        di::set(\moodle_database::class, $mockdb);

        versions::resolve_unique_version_violations();

        // Question 1 should have had its versions renumbered, and corresponding references updated. Question 2 should not.
        $expectedversions = [
            $q1v1->id => 7,
            $q1v2->id => 8,
            $q1v3->id => 9,
            $q1v4->id => 10,
            $q1v5->id => 11,
            $q1v6->id => 12,
            $q2v1->id => 1,
            $q2v2->id => 2,
            $q2v3->id => 3,
            $q2v4->id => 4,
            $q2v5->id => 5,
            $q2v6->id => 6,
        ];
        foreach ($expectedversions as $id => $expectedversion) {
            $this->assertEquals($expectedversion, $db->get_field('question_versions', 'version', ['questionid' => $id]), $id);
        }
        $quiz1structure = quiz_settings::create($quiz1->id)->get_structure();
        $quiz1q1 = $quiz1structure->get_question_in_slot(1);
        $this->assertEquals($quiz1q1->name, $q1v3->name);
        $this->assertEquals(
            $expectedversions[$q1v3->id],
            $db->get_field('question_references', 'version', ['itemid' => $quiz1q1->slotid, 'component' => 'mod_quiz']),
        );
        $quiz1q2 = $quiz1structure->get_question_in_slot(2);
        $this->assertEquals($quiz1q2->name, $q2v2->name);

        $quiz2structure = quiz_settings::create($quiz2->id)->get_structure();
        $quiz2q1 = $quiz2structure->get_question_in_slot(1);
        $this->assertEquals($quiz2q1->name, $q1v5->name);
        $quiz2q2 = $quiz2structure->get_question_in_slot(2);
        $this->assertEquals($quiz2q2->name, $q2v5->name);

        $quiz3structure = quiz_settings::create($quiz3->id)->get_structure();
        $quiz3q1 = $quiz3structure->get_question_in_slot(1);
        $this->assertEquals($quiz3q1->name, $q1v6->name);
        $quiz3q2 = $quiz3structure->get_question_in_slot(2);
        $this->assertEquals($quiz3q2->name, $q2v6->name);
    }
}
