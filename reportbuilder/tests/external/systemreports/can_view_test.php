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

namespace core_reportbuilder\external\systemreports;

use core\context\system;
use core_external\external_api;
use externallib_advanced_testcase;
use core_reportbuilder\local\systemreports\reports_list;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/webservice/tests/helpers.php");

/**
 * Unit tests of external class for validating access to a system report
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\systemreports\can_view
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class can_view_test extends externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $result = can_view::execute(reports_list::class, ['contextid' => system::instance()->id], '', '', 0, []);
        $result = external_api::clean_returnvalue(can_view::execute_returns(), $result);

        $this->assertTrue($result);
    }

    /**
     * Test execute method for a user without permission to view report
     */
    public function test_execute_access_none(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        unassign_capability('moodle/reportbuilder:view', $userrole, system::instance());

        $result = can_view::execute(reports_list::class, ['contextid' => system::instance()->id], '', '', 0, []);
        $result = external_api::clean_returnvalue(can_view::execute_returns(), $result);

        $this->assertFalse($result);
    }
}
