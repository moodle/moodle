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

namespace filter_mathjaxloader;

/**
 * Unit tests for the MathJax loader filter.
 *
 * @package   filter_mathjaxloader
 * @category  test
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_mathjaxloader\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    /**
     * Test the functionality of {@see text_filter::map_language_code()}.
     *
     * @param string $moodlelangcode the user's current language
     * @param string $mathjaxlangcode the mathjax language to be used for the moodle language
     * @dataProvider map_language_code_expected_mappings
     */
    public function test_map_language_code($moodlelangcode, $mathjaxlangcode): void {
        $filter = new text_filter(\context_system::instance(), []);
        $this->assertEquals($mathjaxlangcode, $filter->map_language_code($moodlelangcode));
    }

    /**
     * Data provider for {@link self::test_map_language_code}
     *
     * @return array of [moodlelangcode, mathjaxcode] tuples
     */
    public static function map_language_code_expected_mappings(): array {
        return [
            ['cz', 'cs'], // Explicit mapping.
            ['cs', 'cs'], // Implicit mapping (exact match).
            ['ca_valencia', 'ca'], // Implicit mapping of a Moodle language variant.
            ['pt_br', 'pt-br'], // Explicit mapping.
            ['en_kids', 'en'], // Implicit mapping of English variant.
            ['de_kids', 'de'], // Implicit mapping of non-English variant.
            ['es_mx_kids', 'es'], // More than one underscore in the name.
            ['zh_tw', 'zh-hant'], // Explicit mapping of the Taiwain Chinese in the traditional script.
            ['zh_cn', 'zh-hans'], // Explicit mapping of the Simplified Chinese script.
        ];
    }
}
