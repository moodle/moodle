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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lti_advantage_testcase.php');

/**
 * Tests for legacy_consumer_repository.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\legacy_consumer_repository
 */
final class legacy_consumer_repository_test extends \lti_advantage_testcase {
    /**
     * Test the get_consumer_secrets repository method.
     *
     * @covers ::get_consumer_secrets
     */
    public function test_get_consumer_secrets(): void {
        $this->resetAfterTest();
        // Set up legacy consumer information.
        $course = $this->getDataGenerator()->create_course();

        // Note below that 2 tools with the same secret have been used (three tools total)
        // but we expect only the distinct secrets to be returned.
        $legacydata = [
            'users' => [
                ['user_id' => '123-abc'],
            ],
            'consumer_key' => 'CONSUMER_1',
            'tools' => [
                ['secret' => 'toolsecret1'],
                ['secret' => 'toolsecret1'],
                ['secret' => 'toolsecret2'],
            ]
        ];
        [$tools, $consumer, $users] = $this->setup_legacy_data($course, $legacydata);

        $legacyconsumerrepo = new legacy_consumer_repository();

        // Find the tool secrets associated with 'CONSUMER_1'.
        $consumersecrets = $legacyconsumerrepo->get_consumer_secrets('CONSUMER_1');
        $this->assertCount(2, $consumersecrets);
        foreach ($consumersecrets as $consumersecret) {
            $this->assertContains($consumersecret, ['toolsecret1', 'toolsecret2']);
        }

        // Verify an empty array is returned for a non-existent consumer.
        $this->assertEmpty($legacyconsumerrepo->get_consumer_secrets('CONSUMER_2'));
    }
}
