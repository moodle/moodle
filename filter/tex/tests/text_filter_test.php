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

namespace filter_tex;

use core\context\system as context_system;

/**
 * Unit tests for text_filter.
 *
 * Test the delimiter parsing used by the tex filter.
 *
 * @package    filter_tex
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_tex\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    /**
     * Test the delimeter support.
     *
     * @param string $start
     * @param string $end
     * @param bool $filtershouldrun
     * @dataProvider delimiter_provider
     */
    public function test_delimiter_support(
        string $start,
        string $end,
        bool $filtershouldrun,
    ): void {
        $this->resetAfterTest();

        $filter = new text_filter(context_system::instance(), []);

        $pre = 'Some pre text';
        $post = 'Some post text';
        $equation = ' \sum{a^b} ';

        $before = $pre . $start . $equation . $end . $post;

        $after = trim($filter->filter($before));

        if ($filtershouldrun) {
            $this->assertNotEquals($after, $before);
        } else {
            $this->assertEquals($after, $before);
        }
    }

    /**
     * Data provider for delimeters.
     *
     * @return array
     */
    public static function delimiter_provider(): array {
        return [
            // First test the list of supported delimiters.
            ['$$', '$$', true],
            ['\\(', '\\)', true],
            ['\\[', '\\]', true],
            ['[tex]', '[/tex]', true],
            ['<tex>', '</tex>', true],
            ['<tex alt="nonsense">', '</tex>', true],

            // Now test some cases that shouldn't be executed.
            ['<textarea>', '</textarea>', false],
            ['$', '$', false],
            ['(', ')', false],
            ['[', ']', false],
            ['$$', '\\]', false],
        ];
    }
}
