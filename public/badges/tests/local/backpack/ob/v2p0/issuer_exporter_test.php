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
 * Tests for issuer exporter class in the Open Badges v2.0 backpack integration.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob\v2p0\badge_exporter
 */
class issuer_exporter_test extends \advanced_testcase {
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
     * @covers ::export
     */
    public function test_export(): void {
        global $SITE, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $badge = $generator->create_badge([
            'name' => 'Test Badge',
            'issuername' => 'Test Issuer',
            'issuerurl' => 'https://example.com/issuer',
            'issuercontact' => 'issuer@moodle.cat',
        ]);

        // Export issuer with existing badge.
        $exporter = ob_factory::create_issuer_exporter_from_id($badge->id, $this->get_obversion());
        $data = $exporter->export();

        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_ISSUER, $data['type']);
        $this->assertEquals($exporter->get_json_url()->out(false), $data['id']);
        $this->assertEquals($badge->issuername, $data['name']);
        $this->assertEquals($badge->issuerurl, $data['url']);
        $this->assertEquals($badge->issuercontact, $data['email']);

        // Export with empty badge ID.
        $exporter = ob_factory::create_issuer_exporter_from_id(null, $this->get_obversion());
        $data = $exporter->export();

        $sitebackpack = badges_get_site_primary_backpack();
        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_ISSUER, $data['type']);
        $this->assertEquals($exporter->get_json_url()->out(false), $data['id']);
        $this->assertEquals($SITE->fullname, $data['name']);
        $this->assertEquals((new \core\url('/'))->out(false), $data['url']);
        $this->assertEquals($sitebackpack->backpackemail, $data['email']);

        // Export with empty badge ID but default issuer settings.
        set_config('badges_defaultissuername', 'Default Issuer');
        set_config('badges_defaultissuercontact', 'default@issuer.cat');
        $exporter = ob_factory::create_issuer_exporter_from_id(null, $this->get_obversion());
        $data = $exporter->export();

        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_ISSUER, $data['type']);
        $this->assertEquals($exporter->get_json_url()->out(false), $data['id']);
        $this->assertEquals($CFG->badges_defaultissuername, $data['name']);
        $this->assertEquals((new \core\url('/'))->out(false), $data['url']);
        $this->assertEquals($CFG->badges_defaultissuercontact, $data['email']);
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
        $badge = $generator->create_badge([
            'name' => 'Test Badge',
            'issuername' => 'Test Issuer',
        ]);
        $exporter = ob_factory::create_issuer_exporter_from_id($badge->id, $this->get_obversion());

        $json = $exporter->get_json();
        $this->assertIsString($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertEquals(OPEN_BADGES_V2_CONTEXT, $data['@context']);
        $this->assertEquals(OPEN_BADGES_V2_TYPE_ISSUER, $data['type']);
        $this->assertEquals($badge->issuername, $data['name']);
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

        $exporter = ob_factory::create_issuer_exporter_from_id($badge->id, $this->get_obversion());
        $url = $exporter->get_json_url();

        $this->assertInstanceOf(\core\url::class, $url);
        $this->assertStringContainsString('badges/json/issuer.php', $url->out(false));
        $this->assertStringContainsString('id=' . $badge->id, $url->out(false));
        $this->assertStringContainsString('obversion=' . $this->get_obversion(), $url->out(false));

        // Test with no badge ID.
        $exporter = ob_factory::create_issuer_exporter_from_id(null, $this->get_obversion());
        $url = $exporter->get_json_url();
        $this->assertInstanceOf(\core\url::class, $url);
        $this->assertStringContainsString('badges/json/issuer.php', $url->out(false));
        $this->assertStringContainsString('obversion=' . $this->get_obversion(), $url->out(false));
        $this->assertStringNotContainsString('id=', $url->out(false));
    }
}
