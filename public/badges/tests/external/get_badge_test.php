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

/**
 * Tests for external function get_badge.
 *
 * @package    core_badges
 * @category   external
 *
 * @copyright  2024 Daniel Ureña <durenadev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.5
 * @coversDefaultClass \core_badges\external\get_badge
 */
final class get_badge_test extends \core_external\tests\externallib_testcase {
    use external_helper;

    /**
     * Test get badge by id without enablebadges active in moodle.
     * @covers ::execute
     */
    public function test_get_badge_without_enablebadges(): void {
        $data = $this->prepare_test_data();
        // Badges are not enabled on this site.
        set_config('enablebadges', 0);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('Badges are not enabled on this site.');
        get_badge::execute($data['sitebadge']['id']);
    }

    /**
     * Test get badge by id.
     * @covers ::execute
     */
    public function test_get_badge(): void {
        $data = $this->prepare_test_data();

        // Test with an existing site badge.
        $result = get_badge::execute($data['sitebadge']['id']);
        $result = \core_external\external_api::clean_returnvalue(get_badge::execute_returns(), $result);
        $this->assert_badge_class($data['sitebadge'], $result['badge'], true);
        $this->assertEmpty($result['warnings']);

        // Test with an existing course badge.
        $result = get_badge::execute($data['coursebadge']['id']);
        $result = \core_external\external_api::clean_returnvalue(get_badge::execute_returns(), $result);
        $this->assert_badge_class($data['coursebadge'], $result['badge'], true);
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test get badge by id with an unprivileged user.
     * @covers ::execute
     */
    public function test_get_badge_with_unprivileged_user(): void {
        $data = $this->prepare_test_data();
        $this->setUser($this->getDataGenerator()->create_user());

        // Site badge.
        $result = get_badge::execute($data['sitebadge']['id']);
        $result = \core_external\external_api::clean_returnvalue(get_badge::execute_returns(), $result);
        $this->assert_badge_class($data['sitebadge'], $result['badge'], false);
        $this->assertEmpty($result['warnings']);

        // Course badge.
        $result = get_badge::execute($data['coursebadge']['id']);
        $result = \core_external\external_api::clean_returnvalue(get_badge::execute_returns(), $result);
        $this->assert_badge_class($data['coursebadge'], $result['badge'], false);
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test get badge by id with an invalid badge id.
     * @covers ::execute
     */
    public function test_get_badge_with_invalid_badge_id(): void {
        $this->prepare_test_data();

        $this->expectException(\moodle_exception::class);
        get_badge::execute(123);
    }
}
