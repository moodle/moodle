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
 * Tests user menu functionality.
 *
 * @package    core
 * @copyright  2015 Jetha Chan <jetha@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_menu_testcase extends advanced_testcase {

    /**
     * Custom user menu data for the test_custom_user_menu test.
     *
     * @return array containing testing data
     */
    public function custom_user_menu_data() {
        return array(
            // These are fillers only.
            array('###', 0, 1),
            array('#####', 0, 1),

            // These are invalid and will not generate any entry or filler.
            array('-----', 0, 0),
            array('_____', 0, 0),
            array('test', 0, 0),
            array('#Garbage#', 0, 0),

            // These are valid but have an invalid string identifiers or components. They will still produce a menu
            // item, and no exception should be thrown.
            array('#my1files,moodle|/user/files.php|download', 1, 0),
            array('#my1files,moodleakjladf|/user/files.php|download', 1, 0),
            array('#my1files,a/b|/user/files.php|download', 1, 0),
            array('#my1files,#b|/user/files.php|download', 1, 0),

            // These are unusual, but valid and will generate a menu entry (no filler).
            array('-|-|-|-', 1, 0),
            array('-|-|-', 1, 0),
            array('-|-', 1, 0),
            array('#f234|2', 1, 0),

            // This is a pretty typical entry.
            array('messages,message|/message/index.php|message', 1, 0),

            // And these are combinations containing both valid and invalid.
            array('messages,message|/message/index.php|message
privatefiles,moodle|/user/files.php|download
###
badges,badges|/badges/mybadges.php|award
-|-|-
test
-
#####
#f234|2', 5, 2),
        );
    }

    /**
     * Test the custom user menu.
     *
     * @dataProvider custom_user_menu_data
     * @param string $input The menu text to test
     * @param int $entrycount The numbers of entries expected
     */
    public function test_custom_user_menu($data, $entrycount, $dividercount) {
        global $CFG, $OUTPUT, $USER, $PAGE;

        // Must reset because of config and user modifications.
        $this->resetAfterTest(true);

        // Test using an admin user at the root of Moodle; this way we don't have to create a test user with avatar.
        $this->setAdminUser();
        $PAGE->set_url('/');
        $CFG->theme = 'classic';
        $PAGE->reset_theme_and_output();
        $PAGE->initialise_theme_and_output();

        // Set the configuration.
        set_config('customusermenuitems', $data);

        // We always add two dividers as standard.
        $dividercount += 2;

        // The basic entry count will additionally include the wrapper menu, Dashboard, Profile, Logout and switch roles link.
        $entrycount += 4;

        $output = $OUTPUT->user_menu($USER);
        preg_match_all('/<a [^>]+role="menuitem"[^>]+>/', $output, $results);
        $this->assertCount($entrycount, $results[0]);

        preg_match_all('/<span class="filler">/', $output, $results);
        $this->assertCount($dividercount, $results[0]);
    }

}
