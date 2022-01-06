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
 * This file contains tests for the repository_nextcloud class.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Jan Dageförde (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class repository_nextcloud_ocs_testcase
 * @group repository_nextcloud
 * @copyright  2017 Jan Dageförde (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_nextcloud_ocs_testcase extends advanced_testcase {

    /**
     * @var \core\oauth2\issuer
     */
    private $issuer;

    /**
     * SetUp to create issuer and endpoints for OCS testing.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);

        // Admin is neccessary to create issuer object.
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('repository_nextcloud');
        $this->issuer = $generator->test_create_issuer();
        $generator->test_create_endpoints($this->issuer->get('id'));
    }

    /**
     * Test whether required REST API functions are declared.
     */
    public function test_api_functions() {
        $mock = $this->createMock(\core\oauth2\client::class);
        $mock->expects($this->once())->method('get_issuer')->willReturn($this->issuer);

        $client = new \repository_nextcloud\ocs_client($mock);
        $functions = $client->get_api_functions();

        // Assert that relevant (and used) functions are actually present.
        $this->assertArrayHasKey('create_share', $functions);
    }
}
