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

/**
 * Tests for antivirus manager.
 *
 * @package    core_antivirus
 * @category   phpunit
 * @copyright  2016 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class core_antivirus_testcase extends advanced_testcase {

    public function test_manager_get_antivirus() {
        // We are using clamav plugin in the test,
        // as the only plugin we know exists for sure.
        $antivirusviaget = \core\antivirus\manager::get_antivirus('clamav');
        $antivirusdirect = new \antivirus_clamav\scanner();
        $this->assertEquals($antivirusdirect, $antivirusviaget);
    }
}
