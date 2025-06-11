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
 * OIDC client test cases.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */



namespace auth_oidc;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Tests oidcclient.
 *
 * @group auth_oidc
 * @group office365
 */
final class oidcclient_test extends \advanced_testcase {
    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test getting and setting credentials.
     *
     * @covers \auth_oidc\tests\mockoidcclient::setcreds
     */
    public function test_creds_getters_and_setters(): void {
        $httpclient = new \auth_oidc\tests\mockhttpclient();
        $client = new \auth_oidc\tests\mockoidcclient($httpclient);

        $this->assertNull($client->get_clientid());
        $this->assertNull($client->get_clientsecret());
        $this->assertNull($client->get_redirecturi());

        $id = 'id';
        $secret = 'secret';
        $redirecturi = 'redirecturi';
        $tokenresource = 'resource';
        $scope = (isset($this->config->oidcscope)) ? $this->config->oidcscope : null;
        $client->setcreds($id, $secret, $redirecturi, $tokenresource, $scope);

        $this->assertEquals($id, $client->get_clientid());
        $this->assertEquals($secret, $client->get_clientsecret());
        $this->assertEquals($redirecturi, $client->get_redirecturi());
        $this->assertEquals($tokenresource, $client->get_tokenresource());
    }

    /**
     * Dataprovider returning endpoints.
     *
     * @return array Array of arrays of test parameters.
     */
    public static function dataprovider_endpoints(): array {
        $tests = [];

        $tests['oneinvalid'] = [
                ['auth' => 100],
                ['Exception', 'Invalid Endpoint URI received.'],
        ];

        $tests['oneinvalidonevalid1'] = [
                ['auth' => 100, 'token' => 'http://example.com/token'],
                ['Exception', 'Invalid Endpoint URI received.'],
        ];

        $tests['oneinvalidonevalid2'] = [
                ['token' => 'http://example.com/token', 'auth' => 100],
                ['Exception', 'Invalid Endpoint URI received.'],
        ];

        $tests['onevalid'] = [
                ['token' => 'http://example.com/token'],
                [],
        ];

        $tests['twovalid'] = [
                ['auth' => 'http://example.com/auth', 'token' => 'http://example.com/token'],
                [],
        ];

        return $tests;
    }

    /**
     * Test setting and getting endpoints.
     *
     * @dataProvider dataprovider_endpoints
     * @covers \auth_oidc\tests\mockoidcclient::setendpoints
     * @param array $endpoints
     * @param array $expectedexception
     */
    public function test_endpoints_getters_and_setters(array $endpoints, array $expectedexception): void {
        if (!empty($expectedexception)) {
            $this->expectException($expectedexception[0]);
            $this->expectExceptionMessage($expectedexception[1]);
        }
        $httpclient = new \auth_oidc\tests\mockhttpclient();
        $client = new \auth_oidc\tests\mockoidcclient($httpclient);
        $client->setendpoints($endpoints);

        foreach ($endpoints as $type => $uri) {
            $this->assertEquals($uri, $client->get_endpoint($type));
        }
    }
}
