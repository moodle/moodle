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

namespace core\navigation\views;

/**
 * Class core_primary_testcase
 *
 * Unit test for the primary nav view.
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_test extends \advanced_testcase {
    /**
     * Test the initialise in different contexts
     *
     * @param string $usertype The user to setup for - admin, guest, regular user
     * @param string $expected The expected nodes
     * @dataProvider test_setting_initialise_provider
     */
    public function test_setting_initialise($usertype, $expected) {
        global $PAGE;
        $PAGE->set_url("/");
        $this->resetAfterTest();
        if ($usertype == 'admin') {
            $this->setAdminUser();
        } else if ($usertype == 'guest') {
            $this->setGuestUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            $this->setUser($user);
        }

        $node = new primary($PAGE);
        $node->initialise();
        $children = $node->get_children_key_list();
        $this->assertEquals($expected, $children);
    }

    /**
     * Data provider for the test_setting_initialise function
     */
    public function test_setting_initialise_provider() {
        return [
            'Testing as a guest user' => ['guest', ['home', 'courses']],
            'Testing as an admin' => ['admin', ['home', 'myhome', 'courses', 'siteadminnode']],
            'Testing as a regular user' => ['user', ['home', 'myhome', 'courses']]
        ];
    }
}
