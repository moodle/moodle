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
 * This file contains unit test related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_xapi;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains test cases for testing xAPI iri class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class iri_testcase extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
    }

    /**
     * Test IRI generation.
     *
     * @dataProvider iri_samples_provider
     * @param string $value Value to generate IRI
     * @param string $expected Expected result
     * @param string $type = null If some special type is provided
     */
    public function test_generate(string $value, string $expected, string $type = null) {
        $iri = iri::generate($value, $type);
        $this->assertEquals($iri, $expected);
    }

    /**
     * Test IRI extraction.
     *
     * @dataProvider iri_samples_provider
     * @param string $expected Expected result
     * @param string $value Value to generate IRI
     * @param string $type = null If some special type is provided
     */
    public function test_extract(string $expected, string $value, string $type = null) {
        $extract = iri::extract($value, $type);
        $this->assertEquals($extract, $expected);
    }

    /**
     * Data provider for the test_generate and test_extract tests.
     *
     * @return  array
     */
    public function iri_samples_provider() : array {
        global $CFG;

        return [
            'Fake IRI without type' => [
                'paella',
                "{$CFG->wwwroot}/xapi/element/paella",
                null,
            ],
            'Real IRI without type' => [
                'http://adlnet.gov/expapi/activities/example',
                'http://adlnet.gov/expapi/activities/example',
                null,
            ],
            'Fake IRI with type' => [
                'paella',
                "{$CFG->wwwroot}/xapi/dish/paella",
                'dish',
            ],
            'Real IRI with type' => [
                'http://adlnet.gov/expapi/activities/example',
                'http://adlnet.gov/expapi/activities/example',
                'dish',
            ],
        ];
    }

    /**
     * Test IRI generation.
     *
     * @dataProvider iri_check_provider
     * @param string $value Value to generate IRI
     * @param bool $expected Expected result
     */
    public function test_check(string $value, bool $expected) {
        $check = iri::check($value);
        $this->assertEquals($check, $expected);
    }

    /**
     * Data provider for the test_check.
     *
     * @return  array
     */
    public function iri_check_provider() : array {
        return [
            'Real IRI http' => [
                'http://adlnet.gov/expapi/activities/example',
                true,
            ],
            'Real IRI https' => [
                'https://adlnet.gov/expapi/activities/example',
                true,
            ],
            'Invalid IRI' => [
                'paella',
                false,
            ],
        ];
    }
}
