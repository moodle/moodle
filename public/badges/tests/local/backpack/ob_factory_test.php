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
use core_badges\local\backpack\ob_factory;

/**
 * Tests for Open Badges factory class in backpack.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob_factory
 */
final class ob_factory_test extends \advanced_testcase {
    /**
     * Test create_assertion_exporter_from_hash().
     *
     * @param mixed $obversion Open Badges version to test.
     * @param string $expectedclassvalid Expected class for a valid assertion hash or 'exception' to expect an exception.
     * @param string $expectedclassinvalid Expected class for an invalid assertion hash or 'exception' to expect an exception.
     * @dataProvider create_assertion_exporter_from_hash_provider
     * @covers ::create_assertion_exporter_from_hash
     */
    public function test_create_assertion_exporter_from_hash(
        $obversion,
        string $expectedclassvalid,
        string $expectedclassinvalid,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge and issue it to a user.
        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);

        if (str_contains($expectedclassvalid, 'exception')) {
            $this->expectException($expectedclassvalid);
        }

        /** @var \core_badges\local\backpack\ob\assertion_exporter_interface $exporter */
        $exporter = ob_factory::create_assertion_exporter_from_hash(
            $issuedbadge->uniquehash,
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassvalid, $exporter);
        $this->assertFalse($exporter->is_revoked());

        // Non existing hash.
        if (str_contains($expectedclassinvalid, 'exception')) {
            $this->expectException($expectedclassinvalid);
        }
        $exporter = ob_factory::create_assertion_exporter_from_hash(
            'non-existing-hash',
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassinvalid, $exporter);
        $this->assertTrue($exporter->is_revoked());
    }

    /**
     * Data provider for create_assertion_exporter_from_hash().
     *
     * @return array The data provider array.
     */
    public static function create_assertion_exporter_from_hash_provider(): array {
        return [
            'Open Badges v2.0' => [
                'obversion' => OPEN_BADGES_V2,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p0\assertion_exporter::class,
                'expectedclassinvalid' => \core_badges\local\backpack\ob\v2p0\revoked_assertion_exporter::class,
            ],
            'Open Badges v2.1' => [
                'obversion' => OPEN_BADGES_V2P1,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p1\assertion_exporter::class,
                'expectedclassinvalid' => \core_badges\local\backpack\ob\v2p1\revoked_assertion_exporter::class,
            ],
            'Unsupported Open Badges version should raise exception' => [
                'obversion' => '3.0',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
            'Invalid Open Badges version should raise exception' => [
                'obversion' => 'ABC',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
        ];
    }

    /**
     * Test create_badge_exporter_from_id().
     *
     * @param mixed $obversion Open Badges version to test.
     * @param string $expectedclassvalid Expected class for a valid assertion hash or 'exception' to expect an exception.
     * @param string $expectedclassinvalid Expected class for an invalid assertion hash or 'exception' to expect an exception.
     * @dataProvider create_badge_exporter_from_id_provider
     * @covers ::create_badge_exporter_from_id
     */
    public function test_create_badge_exporter_from_id(
        $obversion,
        string $expectedclassvalid,
        string $expectedclassinvalid,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge.
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);

        if (str_contains($expectedclassvalid, 'exception')) {
            $this->expectException($expectedclassvalid);
        }
        /** @var \core_badges\local\backpack\ob\badge_exporter_interface $exporter */
        $exporter = ob_factory::create_badge_exporter_from_id(
            $badge->id,
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassvalid, $exporter);

        // Non existing badge.
        if (str_contains($expectedclassinvalid, 'exception')) {
            $this->expectException($expectedclassinvalid);
        }
        $exporter = ob_factory::create_badge_exporter_from_id(
            $badge->id + 1000, // Non-existing badge ID.
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassinvalid, $exporter);
    }

    /**
     * Data provider for create_badge_exporter_from_id().
     *
     * @return array The data provider array.
     */
    public static function create_badge_exporter_from_id_provider(): array {
        return [
            'Open Badges v2.0' => [
                'obversion' => OPEN_BADGES_V2,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p0\badge_exporter::class,
                'expectedclassinvalid' => \moodle_exception::class,
            ],
            'Open Badges v2.1' => [
                'obversion' => OPEN_BADGES_V2P1,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p1\badge_exporter::class,
                'expectedclassinvalid' => \moodle_exception::class,
            ],
            'Unsupported Open Badges version should raise exception' => [
                'obversion' => '3.0',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
            'Invalid Open Badges version should raise exception' => [
                'obversion' => 'ABC',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
        ];
    }

    /**
     * Test create_badge_exporter_from_hash().
     *
     * @param mixed $obversion Open Badges version to test.
     * @param string $expectedclassvalid Expected class for a valid assertion hash or 'exception' to expect an exception.
     * @param string $expectedclassinvalid Expected class for an invalid assertion hash or 'exception' to expect an exception.
     * @dataProvider create_badge_exporter_from_hash_provider
     * @covers ::create_badge_exporter_from_hash
     */
    public function test_create_badge_exporter_from_hash(
        $obversion,
        string $expectedclassvalid,
        string $expectedclassinvalid,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge and issue it to a user.
        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);

        if (str_contains($expectedclassvalid, 'exception')) {
            $this->expectException($expectedclassvalid);
        }
        /** @var \core_badges\local\backpack\ob\badge_exporter_interface $exporter */
        $exporter = ob_factory::create_badge_exporter_from_hash(
            $issuedbadge->uniquehash,
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassvalid, $exporter);

        // Non existing hash.
        if (str_contains($expectedclassinvalid, 'exception')) {
            $this->expectException($expectedclassinvalid);
        }
        $exporter = ob_factory::create_badge_exporter_from_hash(
            'non-existing-hash',
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassinvalid, $exporter);
    }

    /**
     * Data provider for create_badge_exporter_from_hash().
     *
     * @return array The data provider array.
     */
    public static function create_badge_exporter_from_hash_provider(): array {
        return [
            'Open Badges v2.0' => [
                'obversion' => OPEN_BADGES_V2,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p0\badge_exporter::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
            'Open Badges v2.1' => [
                'obversion' => OPEN_BADGES_V2P1,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p1\badge_exporter::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
            'Unsupported Open Badges version should raise exception' => [
                'obversion' => '3.0',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
            'Invalid Open Badges version should raise exception' => [
                'obversion' => 'ABC',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
        ];
    }

    /**
     * Test create_issuer_exporter_from_id().
     *
     * @param mixed $obversion Open Badges version to test.
     * @param string $expectedclassvalid Expected class for a valid assertion hash or 'exception' to expect an exception.
     * @param string $expectedclassinvalid Expected class for an invalid assertion hash or 'exception' to expect an exception.
     * @dataProvider create_issuer_exporter_from_id_provider
     * @covers ::create_issuer_exporter_from_id
     */
    public function test_create_issuer_exporter_from_id(
        $obversion,
        string $expectedclassvalid,
        string $expectedclassinvalid,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a badge.
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);

        if (str_contains($expectedclassvalid, 'exception')) {
            $this->expectException($expectedclassvalid);
        }
        /** @var \core_badges\local\backpack\ob\issuer_exporter_interface $exporter */
        $exporter = ob_factory::create_issuer_exporter_from_id(
            $badge->id,
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassvalid, $exporter);

        // Non existing badge.
        if (str_contains($expectedclassinvalid, 'exception')) {
            $this->expectException($expectedclassinvalid);
        }
        $exporter = ob_factory::create_issuer_exporter_from_id(
            $badge->id + 1000, // Non-existing badge ID.
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassinvalid, $exporter);
    }

    /**
     * Test create_issuer_exporter_from_id() with null badge.
     *
     * @param mixed $obversion Open Badges version to test.
     * @param string $expectedclassvalid Expected class for a valid assertion hash or 'exception' to expect an exception.
     * @param string $expectedclassinvalid Ignored (not used) for this test.
     * @dataProvider create_issuer_exporter_from_id_provider
     * @covers ::create_issuer_exporter_from_id
     */
    public function test_create_issuer_exporter_from_id_with_null_badge(
        $obversion,
        string $expectedclassvalid,
        string $expectedclassinvalid,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        if (str_contains($expectedclassvalid, 'exception')) {
            $this->expectException($expectedclassvalid);
        }
        /** @var \core_badges\local\backpack\ob\issuer_exporter_interface $exporter */
        $exporter = ob_factory::create_issuer_exporter_from_id(
            null, // Null badge ID.
            \core_badges\local\backpack\helper::convert_apiversion($obversion),
        );
        $this->assertInstanceOf($expectedclassvalid, $exporter);
    }

    /**
     * Data provider for test_create_issuer_exporter_from_id().
     *
     * @return array The data provider array.
     */
    public static function create_issuer_exporter_from_id_provider(): array {
        return [
            'Open Badges v2.0' => [
                'obversion' => OPEN_BADGES_V2,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p0\issuer_exporter::class,
                'expectedclassinvalid' => \moodle_exception::class,
            ],
            'Open Badges v2.1' => [
                'obversion' => OPEN_BADGES_V2P1,
                'expectedclassvalid' => \core_badges\local\backpack\ob\v2p1\issuer_exporter::class,
                'expectedclassinvalid' => \moodle_exception::class,
            ],
            'Unsupported Open Badges version should raise exception' => [
                'obversion' => '3.0',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
            'Invalid Open Badges version should raise exception' => [
                'obversion' => 'ABC',
                'expectedclassvalid' => \coding_exception::class,
                'expectedclassinvalid' => \coding_exception::class,
            ],
        ];
    }
}
