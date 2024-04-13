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
 * Unit tests for tool_moodlenet lib
 *
 * @package    tool_moodlenet
 * @copyright  2020 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlenet;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/moodlenet/lib.php');

/**
 * Test moodlenet functions
 */
class lib_test extends \advanced_testcase {

    /**
     * Test the generate_mnet_endpoint function
     *
     * @dataProvider get_endpoints_provider
     * @param string $profileurl
     * @param int $course
     * @param int $section
     * @param string $expected
     */
    public function test_generate_mnet_endpoint($profileurl, $course, $section, $expected): void {
        $endpoint = generate_mnet_endpoint($profileurl, $course, $section);
        $this->assertEquals($expected, $endpoint);
    }

    /**
     * Dataprovider for test_generate_mnet_endpoint
     *
     * @return array
     */
    public static function get_endpoints_provider(): array {
        global $CFG;
        return [
            [
                '@name@domain.name',
                1,
                2,
                'https://domain.name/' . MOODLENET_DEFAULT_ENDPOINT . '?site=' . urlencode($CFG->wwwroot)
                    . '&course=1&section=2'
            ],
            [
                '@profile@name@domain.name',
                1,
                2,
                'https://domain.name/' . MOODLENET_DEFAULT_ENDPOINT . '?site=' . urlencode($CFG->wwwroot)
                    . '&course=1&section=2'
            ],
            [
                'https://domain.name',
                1,
                2,
                'https://domain.name/' . MOODLENET_DEFAULT_ENDPOINT . '?site=' . urlencode($CFG->wwwroot)
                    . '&course=1&section=2'
            ]
        ];
    }
}
