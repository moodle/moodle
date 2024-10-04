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

namespace core_badges\external;

use core_badges\tests\external_helper;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for external function get_user_badge_by_hash.
 *
 * @package    core_badges
 * @category   external
 * @copyright  2023 Rodrigo Mady <rodrigo.mady@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 * @coversDefaultClass \core_badges\external\get_user_badge_by_hash
 */
final class get_user_badge_by_hash_test extends externallib_advanced_testcase {
    use external_helper;

    /**
     * Test get user badge by hash.
     * These are a basic tests since the badges_get_my_user_badges used by the external function already has unit tests.
     * @covers ::execute
     */
    public function test_get_user_badge_by_hash(): void {
        $data = $this->prepare_test_data();

        // Site badge fetched by recipient.
        $this->setUser($data['student']);
        $result = get_user_badge_by_hash::execute($data['sitebadge']['uniquehash']);
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assert_issued_badge($data['sitebadge'], $result['badge'][0], true, false);
        $this->assertEmpty($result['warnings']);

        // Site badge fetched by user without "moodle/badges:configuredetails" capability.
        $this->setGuestUser();
        $result = get_user_badge_by_hash::execute($data['sitebadge']['uniquehash']);
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assert_issued_badge($data['sitebadge'], $result['badge'][0], false, false);
        $this->assertEmpty($result['warnings']);

        // Site badge fetched by user with "moodle/badges:configuredetails" capability.
        $this->setAdminUser();
        $result = get_user_badge_by_hash::execute($data['sitebadge']['uniquehash']);
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assert_issued_badge($data['sitebadge'], $result['badge'][0], false, true);
        $this->assertEmpty($result['warnings']);

        // Course badge fetched by recipient.
        $this->setUser($data['student']);
        $result = get_user_badge_by_hash::execute($data['coursebadge']['uniquehash']);
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assert_issued_badge($data['coursebadge'], $result['badge'][0], true, false);
        $this->assertEmpty($result['warnings']);

        // Course badge fetched by user without "moodle/badges:configuredetails" capability.
        $this->setGuestUser();
        $result = get_user_badge_by_hash::execute($data['coursebadge']['uniquehash']);
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assert_issued_badge($data['coursebadge'], $result['badge'][0], false, false);
        $this->assertEmpty($result['warnings']);

        // Course badge fetched by user with "moodle/badges:configuredetails" capability.
        $this->setAdminUser();
        $result = get_user_badge_by_hash::execute($data['coursebadge']['uniquehash']);
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assert_issued_badge($data['coursebadge'], $result['badge'][0], false, true);
        $this->assertEmpty($result['warnings']);

        // Wrong hash.
        $result = get_user_badge_by_hash::execute('1234');
        $result = \core_external\external_api::clean_returnvalue(get_user_badge_by_hash::execute_returns(), $result);
        $this->assertEmpty($result['badge']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('badgeawardnotfound', $result['warnings'][0]['warningcode']);
    }
}
