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
 * Unit tests for the calculated_random_question_summary class.
 *
 * @package    core_question
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_question\statistics\questions\calculated_question_summary;

/**
 * Class core_question_calculated_question_summary_testcase
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_calculated_question_summary_testcase extends advanced_testcase {

    /**
     * Provider for test_get_min_max_of.
     *
     * @return array
     */
    public function get_min_max_provider() {
        return [
            'negative number and null' => [
                [
                    (object)['questionid' => 1, 'index' => 2],
                    (object)['questionid' => 2, 'index' => -7],
                    (object)['questionid' => 3, 'index' => null],
                    (object)['questionid' => 4, 'index' => 12],
                ],
                [-7, 12]
            ],
            'null and negative number' => [
                [
                    (object)['questionid' => 1, 'index' => 2],
                    (object)['questionid' => 2, 'index' => null],
                    (object)['questionid' => 3, 'index' => -7],
                    (object)['questionid' => 4, 'index' => 12],
                ],
                [-7, 12]
            ],
            'negative number and null as maximum' => [
                [
                    (object)['questionid' => 1, 'index' => -2],
                    (object)['questionid' => 2, 'index' => null],
                    (object)['questionid' => 3, 'index' => -7],
                ],
                [-7, null]
            ],
            'zero and null' => [
                [
                    (object)['questionid' => 1, 'index' => 2],
                    (object)['questionid' => 2, 'index' => 0],
                    (object)['questionid' => 3, 'index' => null],
                    (object)['questionid' => 4, 'index' => 12],
                ],
                [0, 12]
            ],
            'null as minimum' => [
                [
                    (object)['questionid' => 1, 'index' => 2],
                    (object)['questionid' => 2, 'index' => null],
                    (object)['questionid' => 3, 'index' => 12],
                ],
                [null, 12]
            ],
            'null and null' => [
                [
                    (object)['questionid' => 1, 'index' => 2],
                    (object)['questionid' => 2, 'index' => null],
                    (object)['questionid' => 3, 'index' => null],
                ],
                [null, 2]
            ],
        ];
    }

    /**
     * Unit test for get_min_max_of() method.
     *
     * @dataProvider get_min_max_provider
     */
    public function test_get_min_max_of($subqstats, $expected) {
        $calculatedsummary = new calculated_question_summary(null, null, $subqstats);
        $res = $calculatedsummary->get_min_max_of('index');
        $this->assertEquals($expected, $res);
    }

    /**
     * Provider for test_get_min_max_of.
     *
     * @return array
     */
    public function get_sd_min_max_provider() {
        return [
            'null and number' => [
                [
                    (object)['questionid' => 1, 'sd' => 0.2, 'maxmark' => 0.5],
                    (object)['questionid' => 2, 'sd' => null, 'maxmark' => 1],
                    (object)['questionid' => 3, 'sd' => 0.1049, 'maxmark' => 1],
                    (object)['questionid' => 4, 'sd' => 0.12, 'maxmark' => 1],
                ],
                [null, 0.4]
            ],
            'null and zero' => [
                [
                    (object)['questionid' => 1, 'sd' => 0.2, 'maxmark' => 0.5],
                    (object)['questionid' => 2, 'sd' => null, 'maxmark' => 1],
                    (object)['questionid' => 3, 'sd' => 0, 'maxmark' => 1],
                    (object)['questionid' => 4, 'sd' => 0.12, 'maxmark' => 1],
                ],
                [0, 0.4]
            ],
            'zero mark' => [
                [
                    (object)['questionid' => 1, 'sd' => 0.2, 'maxmark' => 0],
                    (object)['questionid' => 2, 'sd' => 0.1049, 'maxmark' => 1],
                ],
                [null, 0.1049]
            ],
            'nonzero and nonzero' => [
                [
                    (object)['questionid' => 1, 'sd' => 0.2, 'maxmark' => 0.5],
                    (object)['questionid' => 2, 'sd' => 0.7, 'maxmark' => 2],
                ],
                [0.35, 0.4]
            ],
        ];
    }

    /**
     * Unit test for get_min_max_of_sd() method.
     *
     * @dataProvider get_sd_min_max_provider
     */
    public function test_get_min_max_of_sd($subqstats, $expected) {
        $calculatedsummary = new calculated_question_summary(null, null, $subqstats);
        $res = $calculatedsummary->get_min_max_of('sd');
        $this->assertEquals($expected, $res);
    }
}
