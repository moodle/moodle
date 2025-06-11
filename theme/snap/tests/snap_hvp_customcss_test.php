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
namespace theme_snap;
defined('MOODLE_INTERNAL') || die();

use core_plugin_manager;
use theme_snap\snap_base_test;

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Testing for H5P Custom CSS.
 *
 * @package   theme_snap
 * @author    Diego Monroy
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snap_hvp_customcss_test extends snap_base_test {

    /**
     * Testing renderer Custom CSS for H5P activities.
     *
     * @return void
     */
    public function test_hvp_alter_styles() {
        global $CFG;

        $pluginname = 'mod_hvp';
        $plugins = core_plugin_manager::instance()->get_plugins_of_type('local');
        if (!array_key_exists($pluginname, $plugins)) {
            $this->markTestSkipped("This test should only be run when the mod hvp plugin is installed.");
        }

        require_once($CFG->dirroot . '/theme/snap/classes/mod_hvp_renderer.php');
        $content = 'hvpcustomcss';
        $target = 1;
        $hvpurl = null;
        $urlstartswith = 'http';
        $urlendswith = '.css';

        $this->resetAfterTest();

        // Set new Moodle Page and set context.
        $page = new \moodle_page();
        $page->set_context(CONTEXT_SYSTEM);

        // Use and get object from class theme_snap_mod_hvp_renderer.
        $snaphvp = new \theme_snap_mod_hvp_renderer($page, $target);
        $this->assertIsObject($snaphvp);

        if ($snaphvp) {
            $hvpurl = (string) $snaphvp->get_style_url($content);
        }

        // Test asserting that hvpurl is a string and has certain attributes.
        $this->assertIsString($hvpurl, 'It is not a valid string.');
        $this->assertStringStartsWith($urlstartswith, $hvpurl);
        $this->assertStringEndsWith($urlendswith, $hvpurl);
    }
}
