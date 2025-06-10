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
 * Lti test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\services\lti_service;

/**
 * Lti test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class lti_test extends \advanced_testcase {

    /** @var string */
    private $endpoint;

    /** @var string */
    private $key;

    /** @var string */
    private $secret;

    /** @var bool */
    private $debug;

    public function setUp(): void {
        $this->setAdminUser();
        $this->endpoint = 'http://localhost/lti';
        $this->key = 'lticonsumerkey';
        $this->secret = 'ltisharedsecret';
        $this->debug = true;

        set_config('ltitoolurl', $this->endpoint, 'local_intellidata');
        set_config('lticonsumerkey', $this->key, 'local_intellidata');
        set_config('ltisharedsecret', $this->secret,  'local_intellidata');
    }

    /**
     * Test Save last processed data.
     *
     * @covers \local_intellidata\services\lti_service
     */
    public function test_save_last_processed_data() {
        $this->resetAfterTest(false);

        $ltiservice = new lti_service();

        list($endpoint, $parms, $debug) = $ltiservice->lti_get_launch_data();

        $this->assertEquals($endpoint, $this->endpoint);
        $this->assertEquals($parms['oauth_consumer_key'], $this->key);

        // Check for required params.
        $this->assertArrayHasKey('oauth_version', $parms);
        $this->assertArrayHasKey('oauth_nonce', $parms);
        $this->assertArrayHasKey('user_id', $parms);
        $this->assertArrayHasKey('lis_person_contact_email_primary', $parms);
        $this->assertArrayHasKey('lti_version', $parms);
        $this->assertArrayHasKey('oauth_signature', $parms);
        $this->assertArrayHasKey('oauth_signature_method', $parms);
    }
}
