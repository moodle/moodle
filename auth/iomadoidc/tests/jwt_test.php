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
 * JWT test cases.
 *
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Tests jwt.
 *
 * @group auth_iomadoidc
 * @group office365
 */
class auth_iomadoidc_jwt_testcase extends \advanced_testcase {
    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp():void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Dataprovider for test_decode.
     *
     * @return array Array of arrays of test parameters.
     */
    public function dataprovider_decode() {
        $tests = [];

        $tests['emptytest'] = [
            '', '', ['Exception', 'Empty or non-string JWT received.']
        ];

        $tests['nonstringtest'] = [
            100, '', ['Exception', 'Empty or non-string JWT received.']
        ];

        $tests['malformed1'] = [
            'a', '', ['Exception', 'Malformed JWT received.']
        ];

        $tests['malformed2'] = [
            'a.b', '', ['Exception', 'Malformed JWT received.']
        ];

        $tests['malformed3'] = [
            'a.b.c.d', '', ['Exception', 'Malformed JWT received.']
        ];

        $tests['badheader1'] = [
            'h.p.s', '', ['Exception', 'Could not read JWT header']
        ];

        $header = base64_encode(json_encode(['key' => 'val']));
        $tests['invalidheader1'] = [
            $header.'.p.s', '', ['Exception', 'Invalid JWT header']
        ];

        $header = base64_encode(json_encode(['alg' => 'ROT13']));
        $tests['badalg1'] = [
            $header.'.p.s', '', ['Exception', 'JWS Alg or JWE not supported']
        ];

        $header = base64_encode(json_encode(['alg' => 'RS256']));
        $payload = 'p';
        $tests['badpayload1'] = [
            $header.'.'.$payload.'.s', '', ['Exception', 'Could not read JWT payload.']
        ];

        $header = base64_encode(json_encode(['alg' => 'RS256']));
        $payload = base64_encode('nothing');
        $tests['badpayload2'] = [
            $header.'.'.$payload.'.s', '', ['Exception', 'Could not read JWT payload.']
        ];

        $header = ['alg' => 'RS256'];
        $payload = ['payload' => 'found'];
        $headerenc = base64_encode(json_encode($header));
        $payloadenc = base64_encode(json_encode($payload));
        $expected = [$header, $payload];
        $tests['goodpayload1'] = [
            $headerenc.'.'.$payloadenc.'.s', $expected, []
        ];

        return $tests;
    }

    /**
     * Test decode.
     *
     * @dataProvider dataprovider_decode
     */
    public function test_decode($encodedjwt, $expectedresult, $expectedexception) {
        if (!empty($expectedexception)) {
            $this->expectException($expectedexception[0]);
            $this->expectExceptionMessage($expectedexception[1]);
        }
        $actualresult = \auth_iomadoidc\jwt::decode($encodedjwt);
        $this->assertEquals($expectedresult, $actualresult);

    }
}
