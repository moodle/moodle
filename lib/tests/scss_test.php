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
 * This file contains the unittests for core scss.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2016 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This file contains the unittests for core scss.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2016 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_scss_testcase extends advanced_testcase {

    /**
     * Data provider for is_valid_file
     * @return array
     */
    public function is_valid_file_provider() {
        $themedirectory = core_component::get_component_directory('theme_boost');
        $realroot = realpath($themedirectory);
        return [
            "File import 1" => [
                "path" => "../test.php",
                "valid" => false
            ],
            "File import 2" => [
                "path" => "../test.py",
                "valid" => false
            ],
            "File import 3" => [
                "path" => $realroot . "/scss/moodle.scss",
                "valid" => true
            ],
            "File import 4" => [
                "path" => $realroot . "/scss/../../../config.php",
                "valid" => false
            ],
            "File import 5" => [
                "path" => "/../../../../etc/passwd",
                "valid" => false
            ],
            "File import 6" => [
                "path" => "random",
                "valid" => false
            ]
        ];
    }

    /**
     * @dataProvider is_valid_file_provider
     */
    public function test_is_valid_file($path, $valid) {
        $scss = new \core_scss();
        $pathvalid = phpunit_util::call_internal_method($scss, 'is_valid_file', [$path], \core_scss::class);
        $this->assertSame($valid, $pathvalid);
    }
}