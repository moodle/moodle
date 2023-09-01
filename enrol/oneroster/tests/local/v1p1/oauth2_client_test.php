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

namespace enrol_oneroster\local\v1p1;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/v1p1_testcase.php');
use enrol_oneroster\local\v1p1\v1p1_testcase;

use enrol_oneroster\local\v1p1\endpoints\rostering as rostering_endpoint;

/**
 * One Roster tests for v1p1 OAuth2 Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\oauth2_client
 * @covers  enrol_oneroster\local\v1p1\oauth2_client
 * @covers  enrol_oneroster\local\v1p1\oneroster_client
 */
class oauth2_client_testcase extends v1p1_testcase {

    /**
     * Ensure that the `get_rostering_endpoint` function returns a v1p1 rostering endpoint.
     */
    public function test_get_rostering_endpoint(): void {
        $orclient = $this->getMockBuilder(oauth2_client::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $endpoint = $orclient->get_rostering_endpoint();
        $this->assertInstanceOf(rostering_endpoint::class, $endpoint);
    }

    /**
     * Ensure that the `get_container` function returns a v1p1 rostering endpoint.
     */
    public function test_get_container(): void {
        $orclient = $this->getMockBuilder(oauth2_client::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertInstanceOf(container::class, $orclient->get_container());
    }
}
