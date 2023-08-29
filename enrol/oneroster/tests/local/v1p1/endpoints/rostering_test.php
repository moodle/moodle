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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1\endpoints;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../../oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

use advanced_testcase;
use enrol_oneroster\local\interfaces\coursecat_representation;
use stdClass;
use InvalidArgumentException;
use ReflectionClass;

/**
 * One Roster tests for filters.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\endpoints\rostering
 * @covers  enrol_oneroster\local\v1p1\endpoints\rostering
 */
class rostering_testcase extends oneroster_testcase {

    /**
     * Ensure that the required scopes are correct for the One Roster specification.
     */
    public function test_get_required_scopes(): void {
        $endpoint = new rostering($this->get_mocked_container());

        $this->assertIsArray($endpoint->get_required_scopes());
        $this->assertContains('https://purl.imsglobal.org/spec/or/v1p1/scope/roster.readonly', $endpoint->get_required_scopes());
    }

    /**
     * Ensure that the get_url_for_command function returns the correct url.
     *
     * @dataProvider    get_url_for_command_provider
     * @param   string $baseurl
     * @param   string $suffix
     * @param   string $expected
     */
    public function test_get_url_for_command(string $baseurl, string $suffix, string $expected): void {
        $endpoint = new rostering($this->get_mocked_container());

        $this->assertEquals($expected, $endpoint->get_url_for_command($baseurl, $suffix));
    }

    /**
     * Data provider for get_url_for_command.
     *
     * @return  array
     */
    public function get_url_for_command_provider(): array {
        return array_map(function($command) {
            return [
                'https://example.com/ims/oneroster/v1p1',
                $command['url'],
                "https://example.com/ims/oneroster/v1p1{$command['url']}",
            ];
        }, rostering::get_all_commands());
    }
}
