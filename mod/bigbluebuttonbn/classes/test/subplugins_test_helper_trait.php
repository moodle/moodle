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
use core_h5p\core;
use core_plugin_manager;
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
        $mockedplugins->setValue(null, $plugins);

        $mockedplugintypes = $mockedcomponent->getProperty('plugintypes');
        $mockedplugintypes->setAccessible(true);
        $pluginstypes = $mockedplugintypes->getValue();
        $pluginstypes[extension::BBB_EXTENSION_PLUGIN_NAME] = $bbbextpath;
        $mockedplugintypes->setValue(null, $pluginstypes);

        $fillclassmap = $mockedcomponent->getMethod('fill_classmap_cache');
        $fillclassmap->setAccessible(true);
        $fillclassmap->invoke(null);

        $fillfilemap = $mockedcomponent->getMethod('fill_filemap_cache');
        $fillfilemap->setAccessible(true);
        $fillfilemap->invoke(null);

        $mockedsubplugins = $mockedcomponent->getProperty('subplugins');
        $mockedsubplugins->setAccessible(true);
        $subplugins = $mockedsubplugins->getValue();
        $subplugins['mod_bigbluebuttonbn'][extension::BBB_EXTENSION_PLUGIN_NAME][] = $pluginname;
        $mockedsubplugins->setValue(null, $subplugins);

        // Now write the content of the cache in a file so we can use it later.
        $content = core_component::get_cache_content();
        self::write_fake_component_cache($content);

        // Make sure the plugin is installed.
        ob_start();
        upgrade_noncore(false);
        upgrade_finished();
        ob_end_clean();

        // Cache has been cleared so let's write it again.
        self::write_fake_component_cache($content);

    }

    /**
     * Write the content of the cache in a file for later use.
     *
     * This is used exclusively in behat test as the cache is filled with new values at each session/page load.
     *
     * @param string $content content of the cache
     * @return void
     */
    protected function write_fake_component_cache($content) {
        global $CFG;
        $cachefile = "$CFG->cachedir/core_component.php";
        if (file_exists($cachefile)) {
            // Stale cache detected!
            unlink($cachefile);
        }

        // Permissions might not be setup properly in installers.
        $dirpermissions = !isset($CFG->directorypermissions) ? 02777 : $CFG->directorypermissions;
        $filepermissions = !isset($CFG->filepermissions) ? ($dirpermissions & 0666) : $CFG->filepermissions;

        clearstatcache();
        $cachedir = dirname($cachefile);
        if (!is_dir($cachedir)) {
            mkdir($cachedir, $dirpermissions, true);
        }

        if ($fp = @fopen($cachefile . '.tmp', 'xb')) {
            fwrite($fp, $content);
            fclose($fp);
            @rename($cachefile . '.tmp', $cachefile);
            @chmod($cachefile, $filepermissions);
        }
        @unlink($cachefile . '.tmp'); // Just in case anything fails (race condition).
        core_component::invalidate_opcode_php_cache($cachefile);

    }
    /**
     * Uninstall a fake extension plugin
     *
     * This is intended to behave in most case like a real subplugina and will
     * allow most functionalities to be tested.
     *
     * @param string $pluginname plugin name
     * @return void
     */
    protected function uninstall_fake_plugin(string $pluginname): void {
        global $CFG;
        require_once("$CFG->libdir/adminlib.php");
        // We just need access to fill_all_caches so everything goes back to normal.
        // If we don't do this, there are some side effects that will make other test fails
        // (such as mod_bigbluebuttonbn\task\upgrade_recordings_task_test::test_upgrade_recordings_imported_basic).
        $cachefile = "$CFG->cachedir/core_component.php";
        if (file_exists($cachefile)) {
            // Stale cache detected!
            unlink($cachefile);
        }
        $mockedcomponent = new ReflectionClass(core_component::class);
        // Here we reset the plugin caches.
        $mockedplugintypes = $mockedcomponent->getProperty('plugintypes');
        $mockedplugintypes->setAccessible(true);
        $mockedplugintypes->setValue(null, null);
        $fillclassmap = $mockedcomponent->getMethod('init');
        $fillclassmap->setAccessible(true);
        $fillclassmap->invoke(null);

        // Now uninstall the plugin and clean everything up for other tests.
        $pluginman = core_plugin_manager::instance();
        $plugininfo = $pluginman->get_plugins();
        foreach ($plugininfo as $type => $plugins) {
            foreach ($plugins as $name => $plugin) {
                if ($name === $pluginname) {
                    ob_start();
                    uninstall_plugin($type, $name);
                    ob_end_clean();
                }
            }
        }
    }
}
