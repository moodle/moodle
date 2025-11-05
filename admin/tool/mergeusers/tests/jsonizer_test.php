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

/**
 * Tests for jsonizer class.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers;

use basic_testcase;
use tool_mergeusers\local\jsonizer;

/**
 * Tests for jsonizer class.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class jsonizer_test extends basic_testcase {
    /**
     * Tests to_json() method.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_jsonizer
     * @dataProvider to_json_provider
     */
    public function test_to_json($valuetotest, $expectedresult): void {
        $this->assertEquals($expectedresult, jsonizer::to_json($valuetotest));
    }

    /**
     * Provides a pair of positions per test: [actual input value, expected result].
     *
     * @return array[]
     */
    public static function to_json_provider(): array {
        return [
            'null' => [null, "null"],
            'null as string' => ['null', "\"null\""],
            'empty array' => [[], '[]'],
            'empty object' => [new \stdClass(), '{}'],
            'empty string' => ['', '""'],
            'array' => [[ 'a' => 'b'], json_encode(['a' => 'b'], JSON_PRETTY_PRINT)],
            'object' => [(object)[ 'a' => 'b'], json_encode((object)['a' => 'b'], JSON_PRETTY_PRINT)],
            'string with backslash' => ['\\a', "\"\\\\a\""],
        ];
    }

    /**
     * Tests from_json() method.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_jsonizer
     * @dataProvider from_json_provider
     */
    public function test_from_json($valuetotest, $expectedresult): void {
        $this->assertEquals($expectedresult, jsonizer::from_json($valuetotest));
    }

    /**
     * Provides a pair of positions per test: [actual input value, expected result].
     *
     * @return array[]
     */
    public static function from_json_provider(): array {
        return [
            // Note: Input value cannot be null. Test case skipped.
            'null as string' => ["null", null],
            'empty array' => ["[]", []],
            'empty object' => ['{}', []],
            'empty string' => ['', ''],
            'array' => [
                json_encode(['a' => 'b'], JSON_PRETTY_PRINT),
                json_decode(json_encode(['a' => 'b'], JSON_PRETTY_PRINT), true),
            ],
            'object' => [
                json_encode((object)[ 'a' => 'b']),
                json_decode(json_encode((object)['a' => 'b'], JSON_PRETTY_PRINT), true),
            ],
            'string with backslash' => ["\"\\\\a\"", '\\a'],
        ];
    }
}
