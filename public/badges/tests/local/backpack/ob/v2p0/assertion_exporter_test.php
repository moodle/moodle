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

namespace core_badges\local\backpack\ob\v2p0;

use core_badges_generator;
use core_badges\local\backpack\ob_factory;

/**
 * Tests for achievement credential (or assertion) exporter class in the Open Badges v2.0 backpack integration.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob\v2p0\badge_exporter
 */
class assertion_exporter_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        static::load_requirements();
    }

    /**
     * Helper to load class dependencies.
     */
    protected static function load_requirements(): void {
        global $CFG;

        require_once($CFG->libdir . '/badgeslib.php');
    }

    /**
     * The Open Badges version to use in these tests.
     * It's calculated from the namespace of the class using this trait.
     * It could also be overridden by the classes that use this trait if they need a different version.
     *
     * @return string The converted Open Badges API version.
     */
    protected function get_obversion(): string {
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        preg_match('/v\d+p\d+/', $namespace, $matches);
        return $matches[0] ?? '';
    }

    /**
     * Test export method.
     *
     * @param bool $nested Whether to export nested objects or not.
     * @param bool $usesalt Whether to use salt in the export.
     * @dataProvider export_provider
     */
    public function test_export(
        bool $nested,
        bool $usesalt = true,
    ): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $clock = \core\di::get(\core\clock::class);
        $dateexpire = $clock->now()->getTimestamp() + 86400; // 1 day later.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        // Create a couple of badges, one with related badges and alignments and another without optional fields.
        $badge = $generator->create_badge([]);
        $issuedbadge1 = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user1->id]);
        $issuedbadge2 = $generator->create_issued_badge([
            'badgeid' => $badge->id,
            'userid' => $user2->id,
            'dateexpire' => $dateexpire,
        ]);

        // Export badges.
        $exporter1 = ob_factory::create_assertion_exporter_from_hash($issuedbadge1->uniquehash, $this->get_obversion());
        $data1 = $exporter1->export($nested, $usesalt);
        $exporter2 = ob_factory::create_assertion_exporter_from_hash($issuedbadge2->uniquehash, $this->get_obversion());
        $data2 = $exporter2->export($nested, $usesalt);

        // Check the data structure.
        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data1['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_ASSERTION, $data1['type']);
        $this->assertEquals($exporter1->get_json_url()->out(false), $data1['id']);
        $this->assertArrayHasKey('recipient', $data1);
        $this->assertIsArray($data1['verify']);
        $this->assertArrayHasKey('issuedOn', $data1);
        // Badge.
        if ($nested) {
            $this->assertIsArray($data1['badge']);
            $this->assertEquals($badge->name, $data1['badge']['name']);
        } else {
            $this->assertIsString($data1['badge']);
        }
        // Evidence.
        $this->assertIsString($data1['evidence']);
        // Date expire.
        $this->assertArrayNotHasKey('expires', $data1);
        $this->assertArrayHasKey('expires', $data2);
        $this->assertEquals(date('c', $issuedbadge2->dateexpire), $data2['expires']);
        // Revoked badge.
        $this->assertFalse($exporter1->is_revoked());
        $this->assertFalse($exporter2->is_revoked());
    }

    /**
     * Data provider for test_export method.
     *
     * @return array The data provider array.
     */
    public static function export_provider(): array {
        return [
            'Nested but not salted' => [
                'nested' => true,
                'usesalt' => false,
            ],
            'Not nested and not salted' => [
                'nested' => false,
                'usesalt' => false,
            ],
            'Nested and salted' => [
                'nested' => true,
                'usesalt' => true,
            ],
            'Not nested and salted' => [
                'nested' => false,
                'usesalt' => true,
            ],
        ];
    }

    /**
     * Test get_json method.
     *
     * @covers ::get_json
     */
    public function test_get_json(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);
        $exporter = ob_factory::create_assertion_exporter_from_hash($issuedbadge->uniquehash, $this->get_obversion());

        $json = $exporter->get_json();
        $this->assertIsString($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_ASSERTION, $data['type']);
        $this->assertEquals($badge->name, $data['badge']['name']);
    }

    /**
     * Test get_json_url method.
     *
     * @covers ::get_json_url
     */
    public function test_get_json_url(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $issuedbadge = $generator->create_issued_badge(['badgeid' => $badge->id, 'userid' => $user->id]);

        $exporter = ob_factory::create_assertion_exporter_from_hash($issuedbadge->uniquehash, $this->get_obversion());
        $url = $exporter->get_json_url();

        $this->assertInstanceOf(\core\url::class, $url);
        $this->assertStringContainsString('badges/json/assertion.php', $url->out(false));
        $this->assertStringContainsString('b=' . $issuedbadge->uniquehash, $url->out(false));
        $this->assertStringContainsString('obversion=' . $this->get_obversion(), $url->out(false));
    }

    /**
     * Get alignment data for testing purpose.
     *
     * @param int $badgeid Badge identifier.
     * @param int $code Alignment code.
     * @param bool $addextra Whether to add extra fields to the alignment data.
     * @return object Alignment data object.
     */
    protected function get_alignment_data(int $badgeid, int $code, bool $addextra = true): \stdClass {
        $data = [
            'badgeid' => $badgeid,
            'targetname' => 'CCSS.ELA-Literacy.RST.' . $code,
            'targeturl' => 'http://www.corestandards.org/ELA-Literacy/RST/' . $code,
        ];
        if ($addextra) {
            $data['targetdescription'] = 'Test target description';
            $data['targetframework'] = 'CCSS.RST.' . $code;
            $data['targetcode'] = 'CCSS.RST.' . $code;
        }
        return (object) $data;
    }
}
