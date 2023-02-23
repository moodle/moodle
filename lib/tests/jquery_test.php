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

namespace core;

/**
 * Test jQuery integration.
 *
 * This is not a complete jquery test, it just validates
 * Moodle integration is set up properly.
 *
 * Launch http://127.0.0.1/lib/tests/other/jquerypage.php to
 * verify it actually works in browser.
 *
 * @package    core
 * @category   test
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jquery_test extends \basic_testcase {

    public function test_plugins_file() {
        global $CFG;

        $plugins = null;
        require($CFG->libdir . '/jquery/plugins.php');
        $this->assertIsArray($plugins);
        $this->assertEquals(array('jquery', 'ui', 'ui-css'), array_keys($plugins));

        foreach ($plugins as $type => $files) {
            foreach ($files['files'] as $file) {
                $this->assertFileExists($CFG->libdir . '/jquery/' . $file);
            }
        }
    }
}
