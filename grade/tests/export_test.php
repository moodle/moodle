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
 * Unit tests for grade/report/lib.php.
 *
 * @package  core_grades
 * @category phpunit
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/export/lib.php');

/**
 * A test class used to test grade_report, the abstract grade report parent class
 */
class core_grade_export_test extends advanced_testcase {

    /**
     * Ensure that feedback is correct formatted.
     *
     * @dataProvider    format_feedback_provider
     * @param   string  $input The input string to test
     * @param   int     $inputformat The format of the input string
     * @param   string  $expected The expected result of the format.
     */
    public function test_format_feedback($input, $inputformat, $expected) {
        $feedback = $this->getMockForAbstractClass(
                \grade_export::class,
                [],
                '',
                false
            );

        $this->assertEquals(
            $expected,
            $feedback->format_feedback((object) [
                    'feedback' => $input,
                    'feedbackformat' => $inputformat,
                ])
            );
    }

    /**
     * Data provider for the format_feedback tests.
     *
     * @return  array
     */
    public function format_feedback_provider() : array {
        return [
            'Basic string (PLAIN)' => [
                'This is an example string',
                FORMAT_PLAIN,
                'This is an example string',
            ],
            'Basic string (HTML)' => [
                '<p>This is an example string</p>',
                FORMAT_HTML,
                'This is an example string',
            ],
            'Has image (HTML)' => [
                '<p>See this reference: <img src="http://example.com/myimage.jpg"></p>',
                FORMAT_HTML,
                'See this reference:  ',
            ],
            'Has image and more (HTML)' => [
                '<p>See <img src="http://example.com/myimage.jpg"> for <em>reference</em></p>',
                FORMAT_HTML,
                'See for reference',
            ],
            'Has video and more (HTML)' => [
                '<p>See <video src="http://example.com/myimage.jpg">video of a duck</video> for <em>reference</em></p>',
                FORMAT_HTML,
                'See video of a duck for reference',
            ],
            'Multiple videos (HTML)' => [
                '<p>See <video src="http://example.com/myimage.jpg">video of a duck</video> and <video src="http://example.com/myimage.jpg">video of a cat</video> for <em>reference</em></p>',
                FORMAT_HTML,
                'See video of a duck and video of a cat for reference',
            ],
            'HTML Looking tags in PLAIN' => [
                'The way you have written the <img thing looks pretty fun >',
                FORMAT_PLAIN,
                'The way you have written the <img thing looks pretty fun >',
            ],
        ];
    }
}
