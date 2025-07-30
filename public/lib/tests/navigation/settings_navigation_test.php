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

namespace core\navigation;

use core\tests\navigation\exposed_settings_navigation;

/**
 * Tests for
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(settings_navigation::class)]
final class settings_navigation_test extends \advanced_testcase {
    public function test_setting___construct(): settings_navigation {
        global $PAGE, $SITE;

        $this->resetAfterTest(false);

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $node = new exposed_settings_navigation();

        return $node;
    }

    /**
     * @depends test_setting___construct
     * @param mixed $node
     * @return mixed
     */
    public function test_setting__initialise($node): settings_navigation {
        $this->resetAfterTest(false);

        $node->initialise();
        $this->assertEquals($node->id, 'settingsnav');

        return $node;
    }


    /**
     * @depends test_setting__initialise
     * @param mixed $node
     * @return mixed
     */
    public function test_setting_in_alternative_role($node): void {
        $this->resetAfterTest();

        $this->assertFalse($node->exposed_in_alternative_role());
    }

    /**
     * Test that users with the correct permissions can view the preferences page.
     */
    public function test_can_view_user_preferences(): void {
        global $PAGE, $DB, $SITE;
        $this->resetAfterTest();

        $persontoview = $this->getDataGenerator()->create_user();
        $persondoingtheviewing = $this->getDataGenerator()->create_user();

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        // Check that a standard user can not view the preferences page.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->role_assign($studentrole->id, $persondoingtheviewing->id);
        $this->setUser($persondoingtheviewing);
        $settingsnav = new exposed_settings_navigation();
        $settingsnav->initialise();
        $settingsnav->extend_for_user($persontoview->id);
        $this->assertFalse($settingsnav->can_view_user_preferences($persontoview->id));

        // Set persondoingtheviewing as a manager.
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        $this->getDataGenerator()->role_assign($managerrole->id, $persondoingtheviewing->id);
        $settingsnav = new exposed_settings_navigation();
        $settingsnav->initialise();
        $settingsnav->extend_for_user($persontoview->id);
        $this->assertTrue($settingsnav->can_view_user_preferences($persontoview->id));

        // Check that the admin can view the preferences page.
        $this->setAdminUser();
        $settingsnav = new exposed_settings_navigation();
        $settingsnav->initialise();
        $settingsnav->extend_for_user($persontoview->id);
        $preferencenode = $settingsnav->find('userviewingsettings' . $persontoview->id, null);
        $this->assertTrue($settingsnav->can_view_user_preferences($persontoview->id));
    }
}
