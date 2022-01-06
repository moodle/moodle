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
 * Check API unit tests
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\check\result;

/**
 * Example unit tests for check API
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_testcase extends advanced_testcase {

    /**
     * A simple example showing how a check and result object works
     *
     * Conceptually a check is analgous to a unit test except at runtime
     * instead of build time so many checks in real life such as testing
     * an API is connecting aren't viable to unit test.
     */
    public function test_passwordpolicy() {
        global $CFG;
        $prior = $CFG->passwordpolicy;

        $check = new core\check\security\passwordpolicy();

        $CFG->passwordpolicy = false;
        $result = $check->get_result();
        $this->assertEquals($result->status, result::WARNING);

        $CFG->passwordpolicy = true;
        $result = $check->get_result();
        $this->assertEquals($result->status, result::OK);

        $CFG->passwordpolicy = $prior;
    }
}

