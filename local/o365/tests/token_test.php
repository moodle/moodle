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
 * Token test cases.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365;

use advanced_testcase;

/**
 * Tests \local_o365\oauth2\token
 *
 * @group local_o365
 * @group office365
 */
final class token_test extends advanced_testcase {
    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test refresh method.
     *
     * @covers \local_o365\oauth2\token::refresh
     */
    public function test_refresh(): void {
        global $USER, $DB;
        $this->setAdminUser();
        $now = time();

        $httpclient = new \local_o365\tests\mockhttpclient();
        $newtokenresponse = [
            'access_token' => 'newtoken',
            'expires_on' => $now + 1000,
            'refresh_token' => 'newrefreshtoken',
            'scope' => 'newscope',
            'resource' => 'newresource',
        ];
        $newtokenresponse = json_encode($newtokenresponse);
        $httpclient->set_response($newtokenresponse);

        $oidcconfig = (object)[
            'clientid' => 'clientid',
            'clientsecret' => 'clientsecret',
            'authendpoint' => 'http://example.com/auth',
            'tokenendpoint' => 'http://example.com/token',
        ];

        $tokenrec = (object)[
            'token' => 'oldtoken',
            'expiry' => $now - 1000,
            'refreshtoken' => 'refreshtoken',
            'scope' => 'oldscope',
            'tokenresource' => 'oldresource',
            'user_id' => $USER->id,
        ];
        $tokenrec->id = $DB->insert_record('local_o365_token', $tokenrec);

        $clientdata = new \local_o365\oauth2\clientdata($oidcconfig->clientid, $oidcconfig->clientsecret,
            $oidcconfig->authendpoint, $oidcconfig->tokenendpoint);
        $token = new \local_o365\oauth2\token($tokenrec->token, $tokenrec->expiry, $tokenrec->refreshtoken,
            $tokenrec->scope, $tokenrec->tokenresource, $tokenrec->user_id, $clientdata, $httpclient);
        $token->refresh();

        $this->assertEquals(1, $DB->count_records('local_o365_token'));

        $tokenrec = $DB->get_record('local_o365_token', ['id' => $tokenrec->id]);
        $this->assertEquals('newtoken', $tokenrec->token);
        $this->assertEquals('newrefreshtoken', $tokenrec->refreshtoken);
        $this->assertEquals('newscope', $tokenrec->scope);
        $this->assertEquals('newresource', $tokenrec->tokenresource);
        $this->assertEquals($now + 1000, $tokenrec->expiry);

        $this->assertEquals('newtoken', $token->get_token());
        $this->assertEquals('newrefreshtoken', $token->get_refreshtoken());
        $this->assertEquals('newscope', $token->get_scope());
        $this->assertEquals('newresource', $token->get_tokenresource());
        $this->assertEquals($now + 1000, $token->get_expiry());
    }
}
