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
 * Tex filter library functions tests
 *
 * @package   filter_tex
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace filter_tex;

use advanced_testcase;

global $CFG;
require_once($CFG->dirroot . '/filter/tex/lib.php');

/**
 * Tex filter library functions tests
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends advanced_testcase {
    /**
     * Data provider for test_filter_tex_sanitize_formula.
     *
     * @return array
     */
    public function filter_tex_sanitize_formula_provider(): array {
        return [
            ['x\ =\ \frac{\sqrt{144}}{2}\ \times\ (y\ +\ 12)', 'x\ =\ \frac{\sqrt{144}}{2}\ \times\ (y\ +\ 12)'],
            ['\usepackage[latin1]{inputenc}', '\usepackage[latin1]{inputenc}'],
            ['\newcommand{\A}{\verbatiminput}', '\newforbiddenkeyword_command{\A}{\verbatimforbiddenkeyword_input}'],
        ];
    }

    /**
     * Tests for filter_tex_sanitize_formula() function.
     *
     * @dataProvider filter_tex_sanitize_formula_provider
     * @param $formula The formula to test
     * @param $expected The sanitized version of the formula we expect to get
     */
    public function test_filter_tex_sanitize_formula(string $formula, string $expected) {
        $this->assertEquals($expected, filter_tex_sanitize_formula($formula));
    }
}
