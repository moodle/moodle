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

namespace enrol_lti\local\ltiadvantage\repository;
use enrol_lti\local\ltiadvantage\entity\registration_url;

/**
 * Tests for the registration_url_repository class.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\registration_url_repository
 */
class registration_url_repository_test extends \advanced_testcase {
    /**
     * Test saving a new registration_url instance.
     *
     * @covers ::save
     */
    public function test_save_new() {
        $this->resetAfterTest();
        global $DB;
        $regurl = new registration_url(3600, 'token');
        $regurlrepo = new registration_url_repository();
        $savedregurl = $regurlrepo->save($regurl);

        $this->assertEquals($regurl, $savedregurl);
        $rec = $DB->get_record('enrol_lti_reg_token', []);
        $this->assertEquals('token', $rec->token);
        $this->assertEquals(3600, $rec->expirytime);
    }

    /**
     * Test saving an existing registration_url instance.
     *
     * @covers ::save
     */
    public function test_save_existing() {
        $this->resetAfterTest();
        global $DB;
        $regurl = new registration_url(time() + 3600, 'token');
        $regurlrepo = new registration_url_repository();
        $savedregurl = $regurlrepo->save($regurl);
        $this->assertEquals(1, $DB->count_records('enrol_lti_reg_token'));

        $regurlrepo->save($savedregurl);
        $this->assertEquals(1, $DB->count_records('enrol_lti_reg_token'));
    }

    /**
     * Test finding the registration_url.
     *
     * @dataProvider find_data_provider
     * @param int $expiryoffset the expiry time offset, relative to time(), for the registration URL.
     * @param bool $found whether the find() method is expected to return the URL or not.
     * @covers ::find
     */
    public function test_find(int $expiryoffset, bool $found) {
        $this->resetAfterTest();
        $regurl = new registration_url(time() + $expiryoffset, 'token');
        $regurlrepo = new registration_url_repository();

        // No URL saved yet, so nothing found.
        $this->assertNull($regurlrepo->find());

        // Now save and confirm the find returns the correct URL, or null.
        $regurlrepo->save($regurl);

        if ($found) {
            $this->assertEquals($regurl, $regurlrepo->find());
        } else {
            $this->assertNull($regurlrepo->find());
        }
    }

    /**
     * Data provider for testing the find() method.
     *
     * @return array the test data.
     */
    public function find_data_provider() {
        return [
            'still valid' => [
                'expirytimeoffset' => 3600,
                'found' => true
            ],
            'expired' => [
                'expirytimeoffset' => -1000,
                'found' => false
            ]
        ];
    }

    /**
     * Test finding a registration URL by its token.
     *
     * @covers ::find_by_token
     */
    public function test_find_by_token() {
        $this->resetAfterTest();
        $regurl = new registration_url(time() + 3600, 'token');
        $regurlrepo = new registration_url_repository();

        // No URL saved yet, so nothing found.
        $this->assertNull($regurlrepo->find_by_token('token'));

        // Now save and confirm the find returns the correct URL, or null.
        $regurlrepo->save($regurl);

        $this->assertEquals($regurl, $regurlrepo->find_by_token('token'));
        $this->assertNull($regurlrepo->find_by_token('notfound'));
    }

    /**
     * Test deleting the registration_url.
     *
     * @covers ::delete
     */
    public function test_delete() {
        $this->resetAfterTest();
        global $DB;
        $regurl = new registration_url(time() + 3600, 'token');
        $regurlrepo = new registration_url_repository();
        $regurlrepo->save($regurl);
        $this->assertInstanceOf(registration_url::class, $regurlrepo->find());

        $regurlrepo->delete();
        $this->assertNull($regurlrepo->find());
        $this->assertFalse($DB->record_exists('enrol_lti_reg_token', []));
    }
}
