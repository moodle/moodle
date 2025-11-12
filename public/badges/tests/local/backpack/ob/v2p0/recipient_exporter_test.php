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

/**
 * Tests for achievement subject (or recipient) exporter class in the Open Badges v2.0 backpack integration.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob\v2p0\badge_exporter
 */
class recipient_exporter_test extends \advanced_testcase {
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
     * @param bool $usesalt Whether to use salt for hashing the recipient's email.
     * @dataProvider export_provider
     * @covers ::export
     */
    public function test_export(bool $usesalt): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $classname = '\\core_badges\\local\\backpack\\ob\\' . $this->get_obversion() . '\\recipient_exporter';
        $email = 'my@email.cat';
        $exporter = new $classname($email);
        $data = $exporter->export($usesalt);

        $this->assertEquals('email', $data['type']);
        $this->assertEquals(true, $data['hashed']);
        if ($usesalt) {
            $this->assertEquals($CFG->badges_badgesalt, $data['salt']);
            $this->assertNotEquals($email, $data['identity']);
            $this->assertStringStartsWith('sha256$', $data['identity']);
        } else {
            $this->assertEquals($email, $data['identity']);
        }
    }

    /**
     * Data provider for test_export method.
     *
     * @return array The data provider array.
     */
    public static function export_provider(): array {
        return [
            'Salted' => ['usesalt' => true],
            'Unsalted' => ['usesalt' => false],
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

        $classname = '\\core_badges\\local\\backpack\\ob\\' . $this->get_obversion() . '\\recipient_exporter';
        $email = 'my@email.cat';
        $exporter = new $classname($email);

        $json = $exporter->get_json();
        $this->assertIsString($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertEquals('email', $data['type']);
        $this->assertEquals(true, $data['hashed']);
    }
}
