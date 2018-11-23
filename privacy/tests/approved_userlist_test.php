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
 * Unit Tests for the approved userlist Class
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\userlist;

/**
 * Tests for the \core_privacy API's approved userlist functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class approved_userlist_test extends advanced_testcase {
    /**
     * The approved userlist should not be modifiable once set.
     */
    public function test_default_values_set() {
        $this->resetAfterTest();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();

        $context = \context_system::instance();
        $component = 'core_privacy';

        $uut = new approved_userlist($context, $component, [$u1->id, $u2->id]);

        $this->assertEquals($context, $uut->get_context());
        $this->assertEquals($component, $uut->get_component());

        $expected = [
            $u1->id,
            $u2->id,
        ];
        sort($expected);

        $result = $uut->get_userids();
        sort($result);

        $this->assertEquals($expected, $result);
    }

    public function test_create_from_userlist() {
        $this->resetAfterTest();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();

        $context = \context_system::instance();
        $component = 'core_privacy';

        $sourcelist = new userlist($context, $component);
        $sourcelist->add_users([$u1->id, $u3->id]);

        $expected = [
            $u1->id,
            $u3->id,
        ];
        sort($expected);

        $approvedlist = approved_userlist::create_from_userlist($sourcelist);

        $this->assertEquals($component, $approvedlist->get_component());
        $this->assertEquals($context, $approvedlist->get_context());

        $result = $approvedlist->get_userids();
        sort($result);
        $this->assertEquals($expected, $result);
    }
}
