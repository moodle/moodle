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

namespace mod_quiz\external;

/**
 * Tests for override webservices
 *
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\external\get_overrides
 * @covers \mod_quiz\external\save_overrides
 * @covers \mod_quiz\external\delete_overrides
 */
final class override_test extends \core_external\tests\externallib_testcase {
    /**
     * Creates a quiz for testing.
     *
     * @return object $quiz
     */
    private function create_quiz(): object {
        $course = $this->getDataGenerator()->create_course();
        return $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
    }

    /**
     * Provides values to test_get_overrides
     *
     * @return array
     */
    public static function get_override_provider(): array {
        return [
            'quiz that exists' => [
                'quizid' => ':quizid',
            ],
            'quiz that does not exist' => [
                'quizid' => -1,
                'expectedexception' => \dml_missing_record_exception::class,
            ],
        ];
    }

    /**
     * Tests get_overrides
     *
     * @param int|string $quizid
     * @param string $expectedexception
     * @dataProvider get_override_provider
     */
    public function test_get_overrides(int|string $quizid, string $expectedexception = ''): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $quiz = $this->create_quiz();

        // Create an override.
        $DB->insert_record('quiz_overrides', ['quiz' => $quiz->id]);

        // Replace placeholders.
        if ($quizid == ":quizid") {
            $quizid = $quiz->id;
        }

        if (!empty($expectedexception)) {
            $this->expectException($expectedexception);
        }

        $result = get_overrides::execute($quizid);
        $this->assertNotEmpty($result);
    }

    /**
     * Provides values to test_save_overrides
     *
     * @return array
     */
    public static function save_overrides_provider(): array {
        return [
            'good insert' => [
                'data' => [
                    'timeopen' => 999,
                ],
            ],
            'bad insert' => [
                'data' => [
                    'id' => ':existingid',
                    'timeopen' => -1,
                ],
                'expectedexception' => \invalid_parameter_exception::class,
            ],
            'good update' => [
                'data' => [
                    'timeopen' => 999,
                ],
            ],
            'bad update' => [
                'data' => [
                    'id' => ':existingid',
                    'timeopen' => -1,
                ],
                'expectedexception' => \invalid_parameter_exception::class,
            ],
        ];
    }

    /**
     * Tests save_overrides
     *
     * @dataProvider save_overrides_provider
     * @param array $data
     * @param string $expectedexception
     */
    public function test_save_overrides(array $data, string $expectedexception = ''): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $quiz = $this->create_quiz();
        $user = $this->getDataGenerator()->create_user();

        if (!empty($data['id'])) {
            $data['id'] = $DB->insert_record('quiz_overrides', ['quiz' => $quiz->id, 'userid' => $user->id]);
        }

        // Make a new user to insert a new override for.
        $user = $this->getDataGenerator()->create_user();
        $data = array_merge($data, ['userid' => $user->id]);

        $payload = [
            'quizid' => $quiz->id,
            'overrides' => [
                $data,
            ],
        ];

        if (!empty($expectedexception)) {
            $this->expectException($expectedexception);
        }

        $result = save_overrides::execute($payload);

        // If has reached here, but not thrown exception and was expected to, fail the test.
        if ($expectedexception) {
            $this->fail("Expected exception " . $expectedexception . " was not thrown");
        }

        $this->assertNotEmpty($result['ids']);
        $this->assertCount(1, $result['ids']);
    }

    /**
     * Provides values to test_delete_overrides
     *
     * @return array
     */
    public static function delete_overrides_provider(): array {
        return [
            'delete existing override' => [
                'id' => ':existingid',
            ],
            'delete override that does not exist' => [
                'id' => -1,
                'expectedexception' => \invalid_parameter_exception::class,
            ],
        ];
    }

    /**
     * Tests delete_overrides
     *
     * @dataProvider delete_overrides_provider
     * @param int|string $id
     * @param string $expectedexception
     */
    public function test_delete_overrides(int|string $id, string $expectedexception = ''): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $quiz = $this->create_quiz();
        $user = $this->getDataGenerator()->create_user();

        if ($id == ':existingid') {
            $id = $DB->insert_record('quiz_overrides', ['quiz' => $quiz->id, 'userid' => $user->id]);
        }

        if (!empty($expectedexception)) {
            $this->expectException($expectedexception);
        }

        $result = delete_overrides::execute(['quizid' => $quiz->id, 'ids' => [$id]]);

        // If has reached here, but not thrown exception and was expected to, fail the test.
        if ($expectedexception) {
            $this->fail("Expected exception " . $expectedexception . " was not thrown");
        }

        $this->assertNotEmpty($result['ids']);
        $this->assertContains($id, $result['ids']);
    }
}
