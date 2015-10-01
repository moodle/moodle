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
 * Unit tests for plugin manager class.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/lib/tests/fixtures/testable_plugin_manager.php');
require_once($CFG->dirroot.'/lib/tests/fixtures/testable_plugininfo_base.php');

/**
 * Tests of the basic API of the plugin manager.
 */
class core_plugin_manager_testcase extends advanced_testcase {

    public function test_instance() {
        $pluginman1 = core_plugin_manager::instance();
        $this->assertInstanceOf('core_plugin_manager', $pluginman1);
        $pluginman2 = core_plugin_manager::instance();
        $this->assertSame($pluginman1, $pluginman2);
        $pluginman3 = testable_core_plugin_manager::instance();
        $this->assertInstanceOf('core_plugin_manager', $pluginman3);
        $this->assertInstanceOf('testable_core_plugin_manager', $pluginman3);
        $pluginman4 = testable_core_plugin_manager::instance();
        $this->assertSame($pluginman3, $pluginman4);
        $this->assertNotSame($pluginman1, $pluginman3);
    }

    public function test_reset_caches() {
        // Make sure there are no warnings or errors.
        core_plugin_manager::reset_caches();
        testable_core_plugin_manager::reset_caches();
    }

    public function test_get_plugin_types() {
        // Make sure there are no warnings or errors.
        $types = core_plugin_manager::instance()->get_plugin_types();
        $this->assertInternalType('array', $types);
        foreach ($types as $type => $fulldir) {
            $this->assertFileExists($fulldir);
        }
    }

    public function test_get_installed_plugins() {
        $types = core_plugin_manager::instance()->get_plugin_types();
        foreach ($types as $type => $fulldir) {
            $installed = core_plugin_manager::instance()->get_installed_plugins($type);
            foreach ($installed as $plugin => $version) {
                $this->assertRegExp('/^[a-z]+[a-z0-9_]*$/', $plugin);
                $this->assertTrue(is_numeric($version), 'All plugins should have a version, plugin '.$type.'_'.$plugin.' does not have version info.');
            }
        }
    }

    public function test_get_enabled_plugins() {
        $types = core_plugin_manager::instance()->get_plugin_types();
        foreach ($types as $type => $fulldir) {
            $enabled = core_plugin_manager::instance()->get_enabled_plugins($type);
            if (is_array($enabled)) {
                foreach ($enabled as $key => $val) {
                    $this->assertRegExp('/^[a-z]+[a-z0-9_]*$/', $key);
                    $this->assertSame($key, $val);
                }
            } else {
                $this->assertNull($enabled);
            }
        }
    }

    public function test_get_present_plugins() {
        $types = core_plugin_manager::instance()->get_plugin_types();
        foreach ($types as $type => $fulldir) {
            $present = core_plugin_manager::instance()->get_present_plugins($type);
            if (is_array($present)) {
                foreach ($present as $plugin => $version) {
                    $this->assertRegExp('/^[a-z]+[a-z0-9_]*$/', $plugin, 'All plugins are supposed to have version.php file.');
                    $this->assertInternalType('object', $version);
                    $this->assertTrue(is_numeric($version->version), 'All plugins should have a version, plugin '.$type.'_'.$plugin.' does not have version info.');
                }
            } else {
                // No plugins of this type exist.
                $this->assertNull($present);
            }
        }
    }

    public function test_get_plugins() {
        $plugininfos1 = core_plugin_manager::instance()->get_plugins();
        foreach ($plugininfos1 as $type => $infos) {
            foreach ($infos as $name => $info) {
                $this->assertInstanceOf('\core\plugininfo\base', $info);
            }
        }

        // The testable variant of the manager holds its own tree of the
        // plugininfo objects.
        $plugininfos2 = testable_core_plugin_manager::instance()->get_plugins();
        $this->assertNotSame($plugininfos1['mod']['forum'], $plugininfos2['mod']['forum']);

        // Singletons of each manager class share the same tree.
        $plugininfos3 = core_plugin_manager::instance()->get_plugins();
        $this->assertSame($plugininfos1['mod']['forum'], $plugininfos3['mod']['forum']);
        $plugininfos4 = testable_core_plugin_manager::instance()->get_plugins();
        $this->assertSame($plugininfos2['mod']['forum'], $plugininfos4['mod']['forum']);
    }

    public function test_plugininfo_back_reference_to_the_plugin_manager() {
        $plugman1 = core_plugin_manager::instance();
        $plugman2 = testable_core_plugin_manager::instance();

        foreach ($plugman1->get_plugins() as $type => $infos) {
            foreach ($infos as $info) {
                $this->assertSame($info->pluginman, $plugman1);
            }
        }

        foreach ($plugman2->get_plugins() as $type => $infos) {
            foreach ($infos as $info) {
                $this->assertSame($info->pluginman, $plugman2);
            }
        }
    }

    public function test_get_plugins_of_type() {
        $plugininfos = core_plugin_manager::instance()->get_plugins();
        foreach ($plugininfos as $type => $infos) {
            $this->assertSame($infos, core_plugin_manager::instance()->get_plugins_of_type($type));
        }
    }

    public function test_get_subplugins_of_plugin() {
        global $CFG;

        // Any standard plugin with subplugins is suitable.
        $this->assertFileExists("$CFG->dirroot/lib/editor/tinymce", 'TinyMCE is not present.');

        $subplugins = core_plugin_manager::instance()->get_subplugins_of_plugin('editor_tinymce');
        foreach ($subplugins as $component => $info) {
            $this->assertInstanceOf('\core\plugininfo\base', $info);
        }
    }

    public function test_get_subplugins() {
        // Tested already indirectly from test_get_subplugins_of_plugin().
        $subplugins = core_plugin_manager::instance()->get_subplugins();
        $this->assertInternalType('array', $subplugins);
    }

    public function test_get_parent_of_subplugin() {
        global $CFG;

        // Any standard plugin with subplugins is suitable.
        $this->assertFileExists("$CFG->dirroot/lib/editor/tinymce", 'TinyMCE is not present.');

        $parent = core_plugin_manager::instance()->get_parent_of_subplugin('tinymce');
        $this->assertSame('editor_tinymce', $parent);
    }

    public function test_plugin_name() {
        global $CFG;

        // Any standard plugin is suitable.
        $this->assertFileExists("$CFG->dirroot/lib/editor/tinymce", 'TinyMCE is not present.');

        $name = core_plugin_manager::instance()->plugin_name('editor_tinymce');
        $this->assertSame(get_string('pluginname', 'editor_tinymce'), $name);
    }

    public function test_plugintype_name() {
        $name = core_plugin_manager::instance()->plugintype_name('editor');
        $this->assertSame(get_string('type_editor', 'core_plugin'), $name);
    }

    public function test_plugintype_name_plural() {
        $name = core_plugin_manager::instance()->plugintype_name_plural('editor');
        $this->assertSame(get_string('type_editor_plural', 'core_plugin'), $name);
    }

    public function test_get_plugin_info() {
        global $CFG;

        // Any standard plugin is suitable.
        $this->assertFileExists("$CFG->dirroot/lib/editor/tinymce", 'TinyMCE is not present.');

        $info = core_plugin_manager::instance()->get_plugin_info('editor_tinymce');
        $this->assertInstanceOf('\core\plugininfo\editor', $info);
    }

    public function test_can_uninstall_plugin() {
        global $CFG;

        // Any standard plugin that is required by some other standard plugin is ok.
        $this->assertFileExists("$CFG->dirroot/$CFG->admin/tool/assignmentupgrade", 'assign upgrade tool is not present');
        $this->assertFileExists("$CFG->dirroot/mod/assign", 'assign module is not present');

        $this->assertFalse(core_plugin_manager::instance()->can_uninstall_plugin('mod_assign'));
        $this->assertTrue(core_plugin_manager::instance()->can_uninstall_plugin('tool_assignmentupgrade'));
    }

    public function test_plugin_states() {
        global $CFG;
        $this->resetAfterTest();

        // Any standard plugin that is ok.
        $this->assertFileExists("$CFG->dirroot/mod/assign", 'assign module is not present');
        $this->assertFileExists("$CFG->dirroot/mod/forum", 'forum module is not present');
        $this->assertFileExists("$CFG->dirroot/$CFG->admin/tool/phpunit", 'phpunit tool is not present');
        $this->assertFileNotExists("$CFG->dirroot/mod/xxxxxxx");
        $this->assertFileNotExists("$CFG->dirroot/enrol/autorize");

        // Ready for upgrade.
        $assignversion = get_config('mod_assign', 'version');
        set_config('version', $assignversion - 1, 'mod_assign');
        // Downgrade problem.
        $forumversion = get_config('mod_forum', 'version');
        set_config('version', $forumversion + 1, 'mod_forum');
        // Not installed yet.
        unset_config('version', 'tool_phpunit');
        // Missing already installed.
        set_config('version', 2013091300, 'mod_xxxxxxx');
        // Deleted present.
        set_config('version', 2013091300, 'enrol_authorize');

        core_plugin_manager::reset_caches();

        $plugininfos = core_plugin_manager::instance()->get_plugins();
        foreach ($plugininfos as $type => $infos) {
            foreach ($infos as $name => $info) {
                /** @var core\plugininfo\base $info */
                if ($info->component === 'mod_assign') {
                    $this->assertSame(core_plugin_manager::PLUGIN_STATUS_UPGRADE, $info->get_status(), 'Invalid '.$info->component.' state');
                } else if ($info->component === 'mod_forum') {
                    $this->assertSame(core_plugin_manager::PLUGIN_STATUS_DOWNGRADE, $info->get_status(), 'Invalid '.$info->component.' state');
                } else if ($info->component === 'tool_phpunit') {
                    $this->assertSame(core_plugin_manager::PLUGIN_STATUS_NEW, $info->get_status(), 'Invalid '.$info->component.' state');
                } else if ($info->component === 'mod_xxxxxxx') {
                    $this->assertSame(core_plugin_manager::PLUGIN_STATUS_MISSING, $info->get_status(), 'Invalid '.$info->component.' state');
                } else if ($info->component === 'enrol_authorize') {
                    $this->assertSame(core_plugin_manager::PLUGIN_STATUS_DELETE, $info->get_status(), 'Invalid '.$info->component.' state');
                } else {
                    $this->assertSame(core_plugin_manager::PLUGIN_STATUS_UPTODATE, $info->get_status(), 'Invalid '.$info->component.' state');
                }
            }
        }
    }

    public function test_plugin_available_updates() {

        $pluginman = testable_core_plugin_manager::instance();

        foreach ($pluginman->get_plugins() as $type => $infos) {
            foreach ($infos as $name => $info) {
                $updates = $info->available_updates();
                if ($info->component != 'mod_forum') {
                    $this->assertNull($updates);
                } else {
                    $this->assertEquals(1, count($updates));
                    $update = array_shift($updates);
                    $this->assertInstanceOf('\core\update\info', $update);
                    $this->assertEquals('mod_forum', $update->component);
                    $this->assertEquals('2999122400', $update->version);
                }
            }
        }
    }

    public function test_some_plugins_updatable() {
        $pluginman = testable_core_plugin_manager::instance();
        $this->assertTrue($pluginman->some_plugins_updatable());
    }

    public function test_is_remote_plugin_available() {
        // See {@link testable_core_plugin_manager::get_remote_plugin_info()}.
        $pluginman = testable_core_plugin_manager::instance();
        $this->assertTrue($pluginman->is_remote_plugin_available('foo_bar', ANY_VERSION));
        $this->assertTrue($pluginman->is_remote_plugin_available('foo_bar', 2015010100));
        $this->assertFalse($pluginman->is_remote_plugin_available('foo_bar', 2016010100));
        $this->assertFalse($pluginman->is_remote_plugin_available('bar_bar', ANY_VERSION));
    }

    public function test_resolve_requirements() {

        $pluginman = testable_core_plugin_manager::instance();

        // Prepare a fake pluginfo instance.
        $pluginfo = testable_plugininfo_base::fake_plugin_instance('fake', '/dev/null', 'one', '/dev/null/fake',
            'testable_plugininfo_base', $pluginman);

        // Test no $plugin->requires is specified in version.php.
        $pluginfo->versionrequires = null;
        $this->assertTrue($pluginfo->is_core_dependency_satisfied(2015100100));
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015100100, 29);
        $this->assertEquals(2015100100, $reqs['core']->hasver);
        $this->assertEquals(ANY_VERSION, $reqs['core']->reqver);
        $this->assertEquals($pluginman::REQUIREMENT_STATUS_OK, $reqs['core']->status);

        // Test plugin requires higher core version.
        $pluginfo->versionrequires = 2015110900;
        $this->assertFalse($pluginfo->is_core_dependency_satisfied(2015100100));
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015100100, 29);
        $this->assertEquals(2015100100, $reqs['core']->hasver);
        $this->assertEquals(2015110900, $reqs['core']->reqver);
        $this->assertEquals($pluginman::REQUIREMENT_STATUS_OUTDATED, $reqs['core']->status);

        // Test plugin requires current core version.
        $pluginfo->versionrequires = 2015110900;
        $this->assertTrue($pluginfo->is_core_dependency_satisfied(2015110900));
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015110900, 30);
        $this->assertEquals(2015110900, $reqs['core']->hasver);
        $this->assertEquals(2015110900, $reqs['core']->reqver);
        $this->assertEquals($pluginman::REQUIREMENT_STATUS_OK, $reqs['core']->status);

        // Test plugin requires lower core version.
        $pluginfo->versionrequires = 2014122400;
        $this->assertTrue($pluginfo->is_core_dependency_satisfied(2015100100));
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015100100, 29);
        $this->assertEquals(2015100100, $reqs['core']->hasver);
        $this->assertEquals(2014122400, $reqs['core']->reqver);
        $this->assertEquals($pluginman::REQUIREMENT_STATUS_OK, $reqs['core']->status);

        // Test plugin dependencies and their availability.
        // See {@link testable_core_plugin_manager::get_remote_plugin_info()}.
        $pluginfo->dependencies = array(
            'foo_bar' => ANY_VERSION,
            'foo_baz' => 2014010100,
            'foo_crazy' => ANY_VERSION,
        );
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015110900, 30);
        $this->assertNull($reqs['foo_bar']->hasver);
        $this->assertEquals(ANY_VERSION, $reqs['foo_bar']->reqver);
        $this->assertEquals($pluginman::REQUIREMENT_STATUS_MISSING, $reqs['foo_bar']->status);
        $this->assertEquals($pluginman::REQUIREMENT_AVAILABLE, $reqs['foo_bar']->availability);
        $this->assertEquals($pluginman::REQUIREMENT_AVAILABLE, $reqs['foo_baz']->availability);
        $this->assertEquals($pluginman::REQUIREMENT_UNAVAILABLE, $reqs['foo_crazy']->availability);

        $pluginfo->dependencies = array(
            'foo_baz' => 2015010100,
        );
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015110900, 30);
        $this->assertEquals($pluginman::REQUIREMENT_AVAILABLE, $reqs['foo_baz']->availability);

        $pluginfo->dependencies = array(
            'foo_baz' => 2015010101,
        );
        $reqs = $pluginman->resolve_requirements($pluginfo, 2015110900, 30);
        $this->assertEquals($pluginman::REQUIREMENT_UNAVAILABLE, $reqs['foo_baz']->availability);
    }
}
