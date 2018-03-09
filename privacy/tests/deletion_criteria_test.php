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
 * Unit Tests for the request deletion criteria.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\deletion_criteria;

/**
 * Tests for the \core_privacy API's request deletion criteria class.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deletion_criteria_test extends advanced_testcase {
    /**
     * The get_context function should return the entered context.
     */
    public function test_get_context() {
        $context = \context_system::instance();
        $uit = new deletion_criteria($context);
        $this->assertSame($context, $uit->get_context());
    }

    /**
     * The get_context function should return the entered context.
     */
    public function test_get_context_user_context() {
        $context = \context_user::instance(\core_user::get_user_by_username('admin')->id);
        $uit = new deletion_criteria($context);
        $this->assertSame($context, $uit->get_context());
    }
}
