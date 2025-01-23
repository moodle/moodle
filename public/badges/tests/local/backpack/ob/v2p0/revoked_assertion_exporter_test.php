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

use core_badges\local\backpack\ob_factory;

/**
 * Tests for revoked achievement credential (or assertion) exporter class in the Open Badges v2.0 backpack integration.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob\v2p0\badge_exporter
 */
class revoked_assertion_exporter_test extends \advanced_testcase {
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
     */
    public function test_export(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Export badges.
        $exporter = ob_factory::create_assertion_exporter_from_hash('non-existing-hash', $this->get_obversion());
        $data = $exporter->export();

        // Check the data structure.
        $this->assertArrayNotHasKey('@context', $data);
        $this->assertArrayNotHasKey('type', $data);
        $this->assertEquals($exporter->get_json_url()->out(false), $data['id']);
        $this->assertTrue($data['revoked']);
        // Revoked badge.
        $this->assertTrue($exporter->is_revoked());
    }

    /**
     * Test get_json method.
     *
     * @covers ::get_json
     */
    public function test_get_json(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $exporter = ob_factory::create_assertion_exporter_from_hash('non-existing-hash', $this->get_obversion());
        $data = $exporter->export();

        $json = $exporter->get_json();
        $this->assertIsString($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertTrue($data['revoked']);
    }
}
