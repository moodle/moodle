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
 * Provides the {@link workshopallocation_manual_privacy_provider_testcase} class.
 *
 * @package     workshopallocation_manual
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the privacy API implementation.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workshopallocation_manual_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * When no preference exists, there should be no export.
     */
    public function test_no_preference() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        \workshopallocation_manual\privacy\provider::export_user_preferences($USER->id);
        $this->assertFalse(writer::with_context(\context_system::instance())->has_any_data());
    }

    /**
     * Test that the recently selected perpage is exported.
     */
    public function test_export_preferences() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        set_user_preference('workshopallocation_manual_perpage', 81);

        \workshopallocation_manual\privacy\provider::export_user_preferences($USER->id);
        $this->assertTrue(writer::with_context(\context_system::instance())->has_any_data());

        $prefs = writer::with_context(\context_system::instance())->get_user_preferences('workshopallocation_manual');
        $this->assertNotEmpty($prefs->workshopallocation_manual_perpage);
        $this->assertEquals(81, $prefs->workshopallocation_manual_perpage->value);
        $this->assertContains(get_string('privacy:metadata:preference:perpage', 'workshopallocation_manual'),
            $prefs->workshopallocation_manual_perpage->description);
    }
}
