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

namespace enrol_oneroster\local;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

/**
 * One Roster tests for OAuth2 Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\oauth2_client
 */
class oauth2_client_testcase extends oneroster_testcase {

    /**
     * Get a mock of the abstract container.
     *
     * @return  container
     */
    public function test_auth_url_is_unused(): container {
        $client = $this->getMockBuilder(oauth2_client::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $rc = new \ReflectionClass(oauth2_client::class);
        $rcm = $rc->getMethod('auth_url');
        $rcm->setAccessible(true);

        $this->expectException(\coding_exception::class);
        $rcm->invoke($client);
    }

    /**
     * Test the `authenticate` method.
     */
    public function test_authenticate(): void {
        $tokenurl = 'https://example.com/token';
        $server = 'https://example.com/';
        $clientid = 'thisIsMyClientId';
        $clientsecret = 'thisIsMyBiggestSecret';

        $client = $this->getMockBuilder(oauth2_client::class)
            ->setConstructorArgs([
                $tokenurl,
                $server,
                $clientid,
                $clientsecret
            ])
            ->setMethodsExcept([
                'authenticate',
            ])
            ->getMock();

        $scopes = [
            'https://example.org/spec/example/v1p1/scope/example.dosoemthing',
        ];
        $client
            ->method('get_all_scopes')
            ->willReturn($scopes);

        $client
            ->method('post')
            ->willReturn(json_encode((object) [
                'access_token' => 'exampleToken',
                'expires_in' => usergetmidnight(time()) + DAYSECS,
                'scope' => implode(',', $scopes),
            ]));

        $client
            ->method('get_request_info')
            ->willReturn([
                'http_code' => 200,
            ]);

        // Call Authenticate to authenticate the user.
        $client->authenticate();
    }
}
