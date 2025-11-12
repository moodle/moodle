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

namespace core_badges\local\backpack\ob\v2p1;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/badges/tests/local/backpack/ob/v2p0/assertion_exporter_test.php');

use core_badges\local\backpack\ob\v2p0\assertion_exporter_test as assertion_exporter_v2p0_test;

/**
 * Tests for achievement credential (or assertion) exporter class in the Open Badges v2.1 backpack integration.
 * Most of tests methods are defined in the parent class, because the exporters for OBv2.0 and OBv2.1 are identical.
 *
 * @package    core_badges
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_badges\local\backpack\ob\v2p1\badge_exporter
 */
final class assertion_exporter_test extends assertion_exporter_v2p0_test {
    /**
     * Check this class is testing the expected OB version.
     */
    public function test_obversion(): void {
        $this->assertEquals('v2p1', $this->get_obversion());
    }
}
