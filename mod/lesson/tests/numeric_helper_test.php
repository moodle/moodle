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

namespace mod_lesson;

use mod_lesson\local\numeric\helper;

/**
 * This class contains the test cases for the numeric helper functions
 *
 * @package   mod_lesson
 * @category  test
 * @copyright 2020 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
final class numeric_helper_test extends \advanced_testcase {
    /**
     * Test the lesson_unformat_numeric_value function.
     *
     * @dataProvider lesson_unformat_dataprovider
     * @param $decsep
     * @param $tests
     */
    public function test_lesson_unformat_numeric_value($decsep, $tests): void {
        $this->define_local_decimal_separator($decsep);

        foreach ($tests as $test) {
            $this->assertEquals($test[1], helper::lesson_unformat_numeric_value($test[0]));
        }
    }

    /**
     * Test the lesson_format_numeric_value function.
     *
     * @dataProvider lesson_format_dataprovider
     * @param $decsep
     * @param $tests
     */
    public function test_lesson_format_numeric_value($decsep, $tests): void {
        $this->define_local_decimal_separator($decsep);

        foreach ($tests as $test) {
            $this->assertEquals($test[1], helper::lesson_format_numeric_value($test[0]));
        }
    }

    /**
     * Provide various cases for the unformat test function
     *
     * @return array
     */
    public static function lesson_unformat_dataprovider(): array {
        return [
            "Using a decimal as a separator" => [
                "decsep" => ".",
                "tests" => [
                    ["2.1", 2.1],
                    ["1:4.2", "1:4.2"],
                    ["2,1", 2],
                    ["1:4,2", "1:4"],
                    ["", null]
                ]
            ],
            "Using a comma as a separator" => [
                "decsep" => ",",
                "tests" => [
                    ["2,1", 2.1],
                    ["1:4,2", "1:4.2"],
                    ["2.1", 2.1],
                    ["1:4.2", "1:4.2"],
                ]
            ],
            "Using a X as a separator" => [
                "decsep" => "X",
                "tests" => [
                    ["2X1", 2.1],
                    ["1:4X2", "1:4.2"],
                    ["2.1", 2.1],
                    ["1:4.2", "1:4.2"],
                ]
            ]
        ];
    }

    /**
     * Provide various cases for the unformat test function
     *
     * @return array
     */
    public static function lesson_format_dataprovider(): array {
        return [
            "Using a decimal as a separator" => [
                "decsep" => ".",
                "tests" => [
                    ["2.1", 2.1],
                    ["1:4.2", "1:4.2"],
                    ["2,1", "2,1"],
                    ["1:4,2", "1:4,2"]
                ]
            ],
            "Using a comma as a separator" => [
                "decsep" => ",",
                "tests" => [
                    ["2,1", "2,1"],
                    ["1:4,2", "1:4,2"],
                    ["2.1", "2,1"],
                    [2.1, "2,1"],
                    ["1:4.2", "1:4,2"],
                ]
            ],
            "Using a X as a separator" => [
                "decsep" => "X",
                "tests" => [
                    ["2X1", "2X1"],
                    ["1:4X2", "1:4X2"],
                    ["2.1", "2X1"],
                    ["1:4.2", "1:4X2"],
                ]
            ]
        ];
    }


    /**
     * Define a local decimal separator.
     *
     * It is not possible to directly change the result of get_string in
     * a unit test. Instead, we create a language pack for language 'xx' in
     * dataroot and make langconfig.php with the string we need to change.
     * The default example separator used here is 'X'.
     *
     * @param string $decsep Separator character. Defaults to `'X'`.
     */
    protected function define_local_decimal_separator(string $decsep = 'X') {
        global $SESSION, $CFG;

        $SESSION->lang = 'xx';
        $langconfig = "<?php\n\$string['decsep'] = '$decsep';";
        $langfolder = $CFG->dataroot . '/lang/xx';
        check_dir_exists($langfolder);
        file_put_contents($langfolder . '/langconfig.php', $langconfig);

        // Ensure the new value is picked up and not taken from the cache.
        $stringmanager = get_string_manager();
        $stringmanager->reset_caches(true);
    }
}
