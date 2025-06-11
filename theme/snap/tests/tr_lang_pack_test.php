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
 * Test TR lang pack.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;

use core_plugin_manager;

class tr_lang_pack_test extends \advanced_testcase {

    /**
     * Setup for each test.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_tr_lang_pack_correct() {
        global $CFG;

        $pluginname = 'local_mrooms';
        $plugins = core_plugin_manager::instance()->get_plugins_of_type('local');
        if (!array_key_exists($pluginname, $plugins)) {
            $this->markTestSkipped("This test is only for the openLMS environment.");
        }

        $strfile = file_get_contents($CFG->dirroot . '/theme/snap/cli/trstrings.json');
        $stringsarr = json_decode($strfile, true);

        // Array is contained in "Strings" attribute.
        $stringsarr = $stringsarr['Strings'];

        $discrepancies = 0;
        $CFG->lang = 'tr';

        foreach ($stringsarr as $stringitem) {
            $stringid = $stringitem['Stringid'];
            $stringlocal = $stringitem['Local'];
            $expected = get_string($stringid, 'theme_snap');
            if ($expected !== $stringlocal) {
                $discrepancies++;
            }
        }

        $message = "There are $discrepancies discrepancies on the use of the tr language. ";
        $message .= 'Make sure you run theme/snap/cli/fix_tr_lang_strings.php to fix them.';
        $this->assertEmpty($discrepancies, $message);
    }
}
