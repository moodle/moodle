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

/**
 * Unit tests for {@see \question_display_options}.
 *
 * @coversDefaultClass \question_display_options
 * @package   core_question
 * @category  test
 * @copyright 2023 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_display_options_test extends \advanced_testcase {

    /**
     * Data provider for {@see self::test_has_question_identifier()}
     *
     * @return array[]
     */
    public function has_question_identifier_provider(): array {
        return [
            'Empty string' => ['', false],
            'Empty space' => ['   ', false],
            'Null' => [null, false],
            'Non-empty string' => ["Hello!", true],
        ];
    }

    /**
     * Tests for {@see \question_display_options::has_question_identifier}
     *
     * @covers ::has_question_identifier
     * @dataProvider has_question_identifier_provider
     * @param string|null $identifier The question identifier
     * @param bool $expected The expected return value
     * @return void
     */
    public function test_has_question_identifier(?string $identifier, bool $expected): void {
        $options = new \question_display_options();
        $options->questionidentifier = $identifier;
        $this->assertEquals($expected, $options->has_question_identifier());
    }

    /**
     * Data provider for {@see self::test_add_question_identifier_to_label()
     *
     * @return array[]
     */
    public function add_question_identifier_to_label_provider(): array {
        return [
            'Empty string identifier' => ['Hello', '', false, false, "Hello"],
            'Null identifier' => ['Hello', null, false, false, "Hello"],
            'With identifier' => ['Hello', 'World', false, false, "Hello World"],
            'With identifier, sr-only' => ['Hello', 'World', true, false, 'Hello <span class="sr-only">World</span>'],
            'With identifier, prepend' => ['Hello', 'World', false, true, "World Hello"],
        ];
    }

    /**
     * Tests for {@see \question_display_options::add_question_identifier_to_label()}
     *
     * @covers ::add_question_identifier_to_label
     * @dataProvider add_question_identifier_to_label_provider
     * @param string $label The label string.
     * @param string|null $identifier The question identifier.
     * @param bool $sronly Whether to render the question identifier in a sr-only container
     * @param bool $addbefore Whether to render the question identifier before the label.
     * @param string $expected The expected return value.
     * @return void
     */
    public function test_add_question_identifier_to_label(
        string $label,
        ?string $identifier,
        bool $sronly,
        bool $addbefore,
        string $expected
    ): void {
        $options = new \question_display_options();
        $options->questionidentifier = $identifier;
        $this->assertEquals($expected, $options->add_question_identifier_to_label($label, $sronly, $addbefore));
    }
}
