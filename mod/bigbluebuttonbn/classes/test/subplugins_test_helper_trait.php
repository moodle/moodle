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
 * Subplugin test helper trait
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

namespace mod_bigbluebuttonbn\test;

use core_component;
use mod_bigbluebuttonbn\extension;
use ReflectionClass;

trait subplugins_test_helper_trait {
    /**
     * Setup a fake extension plugin
     *
     * This is intended to behave in most case like a real subplugina and will
     * allow most functionalities to be tested.
     *
     * @param string $pluginname plugin name
     * @return void
     */
    protected function setup_fake_plugin(string $pluginname): void {
        global $CFG;
        require_once("$CFG->libdir/upgradelib.php");
        $bbbextpath = "{$CFG->dirroot}/mod/bigbluebuttonbn/tests/fixtures/extension";
        // This is similar to accesslib_test::setup_fake_plugin.
        $mockedcomponent = new ReflectionClass(core_component::class);

        $mockedplugins = $mockedcomponent->getProperty('plugins');
        $mockedplugins->setAccessible(true);
        $plugins = $mockedplugins->getValue();
        $plugins[extension::BBB_EXTENSION_PLUGIN_NAME] = [$pluginname => $bbbextpath . "/$pluginname"];
        $mockedplugins->setValue($plugins);

        $mockedplugintypes = $mockedcomponent->getProperty('plugintypes');
        $mockedplugintypes->setAccessible(true);
        $pluginstypes = $mockedplugintypes->getValue();
        $pluginstypes[extension::BBB_EXTENSION_PLUGIN_NAME] = $bbbextpath;
        $mockedplugintypes->setValue($pluginstypes);

        $fillclassmap = $mockedcomponent->getMethod('fill_classmap_cache');
        $fillclassmap->setAccessible(true);
        $fillclassmap->invoke(null);

        $fillfilemap = $mockedcomponent->getMethod('fill_filemap_cache');
        $fillfilemap->setAccessible(true);
        $fillfilemap->invoke(null);

        // Make sure the plugin is installed.
        ob_start();
        upgrade_noncore(false);
        upgrade_finished();
        ob_end_clean();
        \core_plugin_manager::reset_caches();
        $this->resetDebugging(); // We might have debugging messages here that we need to get rid of.
        // End of the component loader mock.
    }

}
