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

namespace enrol_oneroster;

use advanced_testcase;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\client_helper
 */
class client_helper_testcase extends advanced_testcase {

    /**
     * Test that the `get_client` function returns the correct client type.
     *
     * @dataProvider get_client_provider
     * @param   string $oauthversion
     * @param   string $orversion
     * @param   string $classname
     */
    public function test_get_client(string $oauthversion, string $orversion, string $classname): void {
        $client = client_helper::get_client(
            $oauthversion,
            $orversion,
            'https://example.com/token',
            'https://example.com',
            'id',
            'secret'
        );

        $this->assertInstanceOf($classname, $client);
    }

    /**
     * Data provider for testing valid client instantiation.
     *
     * @return array
     */
    public function get_client_provider(): array {
        return [
            [
                client_helper::OAUTH_10,
                client_helper::VERSION_V1P1,
                \enrol_oneroster\local\v1p1\oauth1_client::class,
            ],
            [
                client_helper::OAUTH_20,
                client_helper::VERSION_V1P1,
                \enrol_oneroster\local\v1p1\oauth2_client::class,
            ],
        ];
    }

    /**
     * Test that the `get_client` function returns the correct client type.
     *
     * @dataProvider get_invalid_client_provider
     * @param   string $oauthversion
     * @param   string $orversion
     */
    public function test_invalid_get_client(string $oauthversion, string $orversion): void {
        $this->expectException(\InvalidArgumentException::class);

        client_helper::get_client(
            $oauthversion,
            $orversion,
            'https://example.com/token',
            'https://example.com',
            'id',
            'secret'
        );
    }

    /**
     * Data provider for testing invalid client specification.
     *
     * @return  arrayt
     */
    public function get_invalid_client_provider(): array {
        return [
            'Invalid oauth version' => [
                client_helper::OAUTH_20 . '.0',
                client_helper::VERSION_V1P1,
            ],
            'Invalid OneRoster version' => [
                client_helper::OAUTH_20,
                client_helper::VERSION_V1P1 . '0',
            ],
        ];
    }
}
