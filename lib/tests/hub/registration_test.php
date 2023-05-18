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

namespace core\hub;

/**
 * Class containing unit tests for the site registration class.
 *
 * @package   core
 * @copyright  2023 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class registration_test extends \advanced_testcase {

    /**
     * Test getting site registration information.
     *
     * return void
     * @covers \core\hub\registration
     */
    public function test_get_site_info(): void {
        global $CFG;
        $this->resetAfterTest();

        // Create some courses with end dates.
        $generator = $this->getDataGenerator();
        $generator->create_course(['enddate' => time() + 1000]);
        $generator->create_course(['enddate' => time() + 1000]);

        $generator->create_course(); // Course with no end date.

        $siteinfo = registration::get_site_info();

        $this->assertNull($siteinfo['policyagreed']);
        $this->assertEquals($CFG->dbtype, $siteinfo['dbtype']);
        $this->assertEquals('manual', $siteinfo['primaryauthtype']);
        $this->assertEquals(1, $siteinfo['coursesnodates']);
    }
}
