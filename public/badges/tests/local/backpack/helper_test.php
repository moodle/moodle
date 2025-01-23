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

namespace core_badges\local\backpack;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/badgeslib.php');

use core_badges_generator;
use core_badges\local\backpack\helper;

/**
 * Tests for helper class in backpack.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\helper
 */
final class helper_test extends \advanced_testcase {
    /**
     * Test convert_apiversion().
     *
     * @param mixed $version The Open Badges version to test.
     * @param string $expected The expected result of the conversion.
     * @dataProvider convert_apiversion_provider
     * @covers ::convert_apiversion
     */
    public function test_convert_apiversion(
        $version,
        string $expected,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        if ($expected === 'exception') {
            $this->expectException(\coding_exception::class);
        }
        $this->assertEquals($expected, helper::convert_apiversion($version));
    }

    /**
     * Data provider for test_convert_apiversion.
     *
     * @return array The data provider array.
     */
    public static function convert_apiversion_provider(): array {
        return [
            'OPEN_BADGES_V2' => [
                'version' => OPEN_BADGES_V2,
                'expected' => 'v2p0',
            ],
            'OPEN_BADGES_V2P1' => [
                'version' => OPEN_BADGES_V2P1,
                'expected' => 'v2p1',
            ],
            '3' => [
                'version' => '3',
                'expected' => 'v3p0',
            ],
            '3.0' => [
                'version' => '3.0',
                'expected' => 'v3p0',
            ],
            '3.1' => [
                'version' => '3.1',
                'expected' => 'v3p1',
            ],
            '26.78' => [
                'version' => '26.78',
                'expected' => 'v26p78',
            ],
            'Invalid number' => [
                'version' => 'a',
                'expected' => 'exception',
            ],
        ];
    }

    /**
     * Test assertion_exists().
     *
     * @covers ::assertion_exists
     */
    public function test_assertion_exists(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge and issue it to a user.
        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);

        // Valid assertion should exist.
        $this->assertTrue(helper::assertion_exists($issuedbadge->uniquehash));

        // Non-existing hash should not exist.
        $this->assertFalse(helper::assertion_exists('non-existing-hash'));
    }

    /**
     * Test badge_available().
     *
     * @param int $status The status of the badge.
     * @param bool $expected The expected result of badge availability.
     * @dataProvider badge_available_provider
     * @covers ::badge_available
     */
    public function test_badge_available(
        int $status,
        bool $expected,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge with the given status.
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge', 'status' => $status]);

        if ($expected) {
            $this->assertTrue(helper::badge_available($badge->id));
        } else {
            $this->assertFalse(helper::badge_available($badge->id));
        }
    }

    /**
     * Data provider for test_badge_available.
     *
     * @return array The data provider array.
     */
    public static function badge_available_provider(): array {
        return [
            'Active badge' => [
                'status' => BADGE_STATUS_ACTIVE,
                'expected' => true,
            ],
            'Active lock badge' => [
                'status' => BADGE_STATUS_ACTIVE_LOCKED,
                'expected' => true,
            ],
            'Inactive badge' => [
                'status' => BADGE_STATUS_INACTIVE,
                'expected' => false,
            ],
            'Inactive lock badge' => [
                'status' => BADGE_STATUS_INACTIVE_LOCKED,
                'expected' => false,
            ],
            'Archived badge' => [
                'status' => BADGE_STATUS_ARCHIVED,
                'expected' => false,
            ],
        ];
    }

    /**
     * Test get_badgeid_from_hash().
     *
     * @covers ::get_badgeid_from_hash
     */
    public function test_get_badgeid_from_hash(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge and issue it to a user.
        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);
        $badgeid = helper::get_badgeid_from_hash($issuedbadge->uniquehash);
        $this->assertEquals($badge->id, $badgeid);

        // Test with a non-existing hash.
        $badgeid = helper::get_badgeid_from_hash('non-existing-hash');
        $this->assertNull($badgeid);
    }
}
