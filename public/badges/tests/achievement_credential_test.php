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

declare(strict_types=1);

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

use core_badges_generator;

/**
 * Unit tests for badge class.
 *
 * @package     core_badges
 * @category    test
 * @covers      \core_badges\achievement_credential
 * @copyright   2025 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class achievement_credential_test extends \advanced_testcase {
    /**
     * Test the achievement_credential::instance() method.
     */
    public function test_instance(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge and issue it to a user.
        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $tags = ['tag1', 'tag2', 'tag3'];
        $badge = $generator->create_badge([
            'name' => 'Test Badge',
            'tags' => $tags,
        ]);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);

        // Existing badge uniquehash should return an instance of achievement_credential.
        $credential = achievement_credential::instance($issuedbadge->uniquehash);
        $this->assertInstanceOf(achievement_credential::class, $credential);
        // Test getters.
        $this->assertEquals($issuedbadge->uniquehash, $credential->get_hash());
        $this->assertEquals($user->email, $credential->get_email());
        $this->assertEquals($badge->id, $credential->get_badge_id());
        $this->assertEquals($issuedbadge->dateissued, $credential->get_dateissued());
        if (property_exists($issuedbadge, 'expiredate')) {
            $this->assertEquals($issuedbadge->expiredate, $credential->get_dateexpire());
        } else {
            $this->assertNull($credential->get_dateexpire());
        }
        $this->assertEquals($tags, $credential->get_tags());

        // When the user has a backpack, the email should be taken from there.
        helper::create_fake_backpack([
            'userid' => $user->id,
            'email' => 'mybackpackemail@moodle.cat',
        ]);
        $credential = achievement_credential::instance($issuedbadge->uniquehash);
        $this->assertInstanceOf(achievement_credential::class, $credential);
        $this->assertEquals($issuedbadge->uniquehash, $credential->get_hash());
        $this->assertEquals('mybackpackemail@moodle.cat', $credential->get_email());

        // Non existing badge uniquehash should return null.
        $this->assertNull(achievement_credential::instance('non-existing-hash'));
    }
}
