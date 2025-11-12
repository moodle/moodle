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
 * Tests for badge exporter class in the Open Badges v2.0 backpack integration.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob\v2p0\badge_exporter
 */
class badge_exporter_test extends \advanced_testcase {
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
     * @dataProvider export_provider
     */
    public function test_export(bool $nested): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        // Create a couple of badges, one with related badges and alignments and another without optional fields.
        $badge1 = $generator->create_badge($this->get_testing_badge_data());
        $badge2 = $generator->create_badge($this->get_testing_badge_data([
            'name' => 'Related Badge',
            'description' => 'This badge is related to another badge.',
            'version' => '1.1',
            'language' => 'es',
        ]));
        $badge1->add_related_badges([$badge2->id]);
        $alignment1 = $this->get_alignment_data($badge1->id, 1);
        $badge1->save_alignment($alignment1);
        $alignment2 = $this->get_alignment_data($badge1->id, 2, false);
        $badge1->save_alignment($alignment2);
        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $generator->create_criteria(['badgeid' => $badge1->id, 'roleid' => $managerrole]);
        $badge3 = $generator->create_badge([]);
        $badge3->language = null;
        $badge3->version = null;
        $badge3->imagecaption = null;
        $badge3->save();

        // Export badges.
        $exporter1 = ob_factory::create_badge_exporter_from_id($badge1->id, $this->get_obversion());
        $data1 = $exporter1->export($nested);
        $exporter2 = ob_factory::create_badge_exporter_from_id($badge2->id, $this->get_obversion());
        $data2 = $exporter2->export($nested);
        $exporter3 = ob_factory::create_badge_exporter_from_id($badge3->id, $this->get_obversion());
        $data3 = $exporter3->export($nested);

        // Check the data structure.
        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data1['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_BADGE, $data1['type']);
        $this->assertEquals($badge1->name, $data1['name']);
        $this->assertEquals($badge1->description, $data1['description']);
        $this->assertEquals($exporter1->get_json_url()->out(false), $data1['id']);
        // Version and language are optional.
        $this->assertEquals($badge1->version, $data1['version']);
        $this->assertEquals($badge1->language, $data1['@language']);
        $this->assertArrayNotHasKey('version', $data3);
        $this->assertArrayNotHasKey('@language', $data3);
        // Image.
        $this->assertIsArray($data1['image']);
        $this->assertArrayHasKey('id', $data1['image']);
        $this->assertEquals($badge1->imagecaption, $data1['image']['caption']);
        $this->assertIsString($data3['image']);
        // Criteria.
        $this->assertIsArray($data1['criteria']);
        $this->assertArrayHasKey('id', $data1['criteria']);
        $this->assertStringContainsString(get_string('criteria_descr', 'badges'), $data1['criteria']['narrative']);
        $this->assertArrayHasKey('id', $data3['criteria']);
        $this->assertEquals(get_string('nocriteria', 'badges'), $data3['criteria']['narrative']);
        // Issuer.
        if ($nested) {
            $this->assertIsArray($data1['issuer']);
            $this->assertIsArray($data3['issuer']);
        } else {
            $this->assertIsString($data1['issuer']);
            $this->assertIsString($data3['issuer']);
        }
        // Tags.
        $this->assertEquals($badge1->get_badge_tags(), $data1['tags']);
        $this->assertArrayNotHasKey('tags', $data3);
        // Related badges.
        $this->assertArrayHasKey('related', $data1);
        $this->assertArrayHasKey('related', $data2);
        $this->assertArrayNotHasKey('related', $data3);
        // Alignments.
        $this->assertArrayNotHasKey('alignment', $data2);
        $this->assertArrayNotHasKey('alignment', $data3);
        $this->assertCount(2, $data1['alignment']);
        $this->assertEquals($alignment1->targetname, $data1['alignment'][0]['targetName']);
        $this->assertEquals($alignment1->targeturl, $data1['alignment'][0]['targetUrl']);
        $this->assertEquals($alignment1->targetdescription, $data1['alignment'][0]['targetDescription']);
        $this->assertEquals($alignment1->targetframework, $data1['alignment'][0]['targetFramework']);
        $this->assertEquals($alignment1->targetcode, $data1['alignment'][0]['targetCode']);
        $this->assertEquals($alignment2->targetname, $data1['alignment'][1]['targetName']);
        $this->assertEquals($alignment2->targeturl, $data1['alignment'][1]['targetUrl']);
        $this->assertArrayNotHasKey('targetDescription', $data1['alignment'][1]);
        $this->assertArrayNotHasKey('targetFramework', $data1['alignment'][1]);
        $this->assertArrayNotHasKey('targetCode', $data1['alignment'][1]);
    }

    /**
     * Data provider for test_export method.
     *
     * @return array The data provider array.
     */
    public static function export_provider(): array {
        return [
            'Nested' => [true],
            'Not nested' => [false],
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

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $exporter = ob_factory::create_badge_exporter_from_id($badge->id, $this->get_obversion());

        $json = $exporter->get_json();
        $this->assertIsString($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_BADGE, $data['type']);
        $this->assertEquals($badge->name, $data['name']);
    }

    /**
     * Test get_json_url method.
     *
     * @covers ::get_json_url
     */
    public function test_get_json_url(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge(['name' => 'Test Badge']);
        $exporter = ob_factory::create_badge_exporter_from_id($badge->id, $this->get_obversion());

        $url = $exporter->get_json_url();
        $this->assertInstanceOf(\core\url::class, $url);
        $this->assertStringContainsString('badges/json/badge.php', $url->out(false));
        $this->assertStringContainsString('id=' . $badge->id, $url->out(false));
        $this->assertStringContainsString('obversion=' . $this->get_obversion(), $url->out(false));
    }

    /**
     * Get default badge data for testing purpose.
     *
     * @param null|array $extra Extra data to override the default badge data.
     * @return array Badge data.
     */
    protected function get_testing_badge_data(?array $extra = []): array {
        global $USER;

        $data = [
            'name' => 'My testing badge',
            'description' => 'Testing badge description',
            'timecreated' => time(),
            'timemodified' => time(),
            'usercreated' => $USER->id,
            'usermodified' => $USER->id,
            'issuername' => 'Test issuer',
            'issuerurl' => 'http://issuer-url.domain.co.nz',
            'issuercontact' => 'issuer@example.com',
            'expiry' => 0,
            'expiredate' => null,
            'expireperiod' => null,
            'type' => BADGE_TYPE_SITE,
            'courseid' => null,
            'messagesubject' => 'The new test message subject',
            'messageformat' => '1',
            'message_editor' => [
                'text' => 'The new test message body',
            ],
            'attachment' => 1,
            'notification' => 0,
            'status' => BADGE_STATUS_ACTIVE_LOCKED,
            'version' => '1.0',
            'language' => 'ca',
            'imagecaption' => 'Image caption',
            'tags' => ['tag1', 'tag2'],
        ];

        return array_merge($data, $extra);
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
