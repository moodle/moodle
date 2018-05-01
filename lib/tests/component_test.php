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
 * core_component related tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Class core_component_testcase.
 */
class core_component_testcase extends advanced_testcase {

    // To be changed if number of subsystems increases/decreases,
    // this is defined here to annoy devs that try to add more without any thinking,
    // always verify that it does not collide with any existing add-on modules and subplugins!!!
    const SUBSYSTEMCOUNT = 65;

    public function setUp() {
        $psr0namespaces = new ReflectionProperty('core_component', 'psr0namespaces');
        $psr0namespaces->setAccessible(true);
        $this->oldpsr0namespaces = $psr0namespaces->getValue(null);

        $psr4namespaces = new ReflectionProperty('core_component', 'psr4namespaces');
        $psr4namespaces->setAccessible(true);
        $this->oldpsr4namespaces = $psr4namespaces->getValue(null);
    }
    public function tearDown() {
        $psr0namespaces = new ReflectionProperty('core_component', 'psr0namespaces');
        $psr0namespaces->setAccessible(true);
        $psr0namespaces->setValue(null, $this->oldpsr0namespaces);

        $psr4namespaces = new ReflectionProperty('core_component', 'psr4namespaces');
        $psr4namespaces->setAccessible(true);
        $psr4namespaces->setValue(null, $this->oldpsr4namespaces);
    }

    public function test_get_core_subsystems() {
        global $CFG;

        $subsystems = core_component::get_core_subsystems();

        $this->assertCount(self::SUBSYSTEMCOUNT, $subsystems, 'Oh, somebody added or removed a core subsystem, think twice before doing that!');

        // Make sure all paths are full/null, exist and are inside dirroot.
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertFalse(strpos($subsystem, '_'), 'Core subsystems must be one work without underscores');
            if ($fulldir === null) {
                if ($subsystem === 'filepicker' or $subsystem === 'help') {
                    // Arrgghh, let's not introduce more subsystems for no real reason...
                } else {
                    // Lang strings.
                    $this->assertFileExists("$CFG->dirroot/lang/en/$subsystem.php", 'Core subsystems without fulldir are usually used for lang strings.');
                }
                continue;
            }
            $this->assertFileExists($fulldir);
            // Check that base uses realpath() separators and "/" in the subdirs.
            $this->assertStringStartsWith($CFG->dirroot.'/', $fulldir);
            $reldir = substr($fulldir, strlen($CFG->dirroot)+1);
            $this->assertFalse(strpos($reldir, '\\'));
        }

        // Make sure all core language files are also subsystems!
        $items = new DirectoryIterator("$CFG->dirroot/lang/en");
        foreach ($items as $item) {
            if ($item->isDot() or $item->isDir()) {
                continue;
            }
            $file = $item->getFilename();
            if ($file === 'moodle.php') {
                // Do not add new lang strings unless really necessary!!!
                continue;
            }

            if (substr($file, -4) !== '.php') {
                continue;
            }
            $file = substr($file, 0, strlen($file)-4);
            $this->assertArrayHasKey($file, $subsystems, 'All core lang files should be subsystems, think twice before adding anything!');
        }
        unset($item);
        unset($items);

    }

    public function test_deprecated_get_core_subsystems() {
        global $CFG;

        $subsystems = core_component::get_core_subsystems();

        $this->assertSame($subsystems, get_core_subsystems(true));

        $realsubsystems = get_core_subsystems();
        $this->assertDebuggingCalled();
        $this->assertSame($realsubsystems, get_core_subsystems(false));
        $this->assertDebuggingCalled();

        $this->assertEquals(count($subsystems), count($realsubsystems));

        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertArrayHasKey($subsystem, $realsubsystems);
            if ($fulldir === null) {
                $this->assertNull($realsubsystems[$subsystem]);
                continue;
            }
            $this->assertSame($fulldir, $CFG->dirroot.'/'.$realsubsystems[$subsystem]);
        }
    }

    public function test_get_plugin_types() {
        global $CFG;

        $this->assertTrue(empty($CFG->themedir), 'Non-empty $CFG->themedir is not covered by any tests yet, you need to disable it.');

        $plugintypes = core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $this->assertStringStartsWith("$CFG->dirroot/", $fulldir);
        }
    }

    public function test_deprecated_get_plugin_types() {
        global $CFG;

        $plugintypes = core_component::get_plugin_types();

        $this->assertSame($plugintypes, get_plugin_types());
        $this->assertSame($plugintypes, get_plugin_types(true));

        $realplugintypes = get_plugin_types(false);
        $this->assertDebuggingCalled();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $this->assertSame($fulldir, $CFG->dirroot.'/'.$realplugintypes[$plugintype]);
        }
    }

    public function test_get_plugin_list() {
        global $CFG;

        $plugintypes = core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertStringStartsWith("$CFG->dirroot/", $plugindir);
            }
            if ($plugintype !== 'auth') {
                // Let's crosscheck it with independent implementation (auth/db is an exception).
                $reldir = substr($fulldir, strlen($CFG->dirroot)+1);
                $dirs = get_list_of_plugins($reldir);
                $dirs = array_values($dirs);
                $this->assertDebuggingCalled();
                $this->assertSame($dirs, array_keys($plugins));
            }
        }
    }

    public function test_deprecated_get_plugin_list() {
        $plugintypes = core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = core_component::get_plugin_list($plugintype);
            $this->assertSame($plugins, get_plugin_list($plugintype));
        }
    }

    public function test_get_plugin_directory() {
        $plugintypes = core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame($plugindir, core_component::get_plugin_directory($plugintype, $pluginname));
            }
        }
    }

    public function test_deprecated_get_plugin_directory() {
        $plugintypes = core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame(core_component::get_plugin_directory($plugintype, $pluginname), get_plugin_directory($plugintype, $pluginname));
            }
        }
    }

    public function test_get_subsystem_directory() {
        $subsystems = core_component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertSame($fulldir, core_component::get_subsystem_directory($subsystem));
        }
    }

    public function test_is_valid_plugin_name() {
        $this->assertTrue(core_component::is_valid_plugin_name('mod', 'example1'));
        $this->assertTrue(core_component::is_valid_plugin_name('mod', 'feedback360'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', 'feedback_360'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', '2feedback'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', '1example'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', 'example.xx'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', '.example'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', '_example'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', 'example_'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', 'example_x1'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', 'example-x1'));
        $this->assertFalse(core_component::is_valid_plugin_name('mod', 'role'));

        $this->assertTrue(core_component::is_valid_plugin_name('tool', 'example1'));
        $this->assertTrue(core_component::is_valid_plugin_name('tool', 'example_x1'));
        $this->assertTrue(core_component::is_valid_plugin_name('tool', 'example_x1_xxx'));
        $this->assertTrue(core_component::is_valid_plugin_name('tool', 'feedback360'));
        $this->assertTrue(core_component::is_valid_plugin_name('tool', 'feed_back360'));
        $this->assertTrue(core_component::is_valid_plugin_name('tool', 'role'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', '1example'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', 'example.xx'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', 'example-xx'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', '.example'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', '_example'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', 'example_'));
        $this->assertFalse(core_component::is_valid_plugin_name('tool', 'example__x1'));
    }

    public function test_normalize_componentname() {
        // Moodle core.
        $this->assertSame('core', core_component::normalize_componentname('core'));
        $this->assertSame('core', core_component::normalize_componentname('moodle'));
        $this->assertSame('core', core_component::normalize_componentname(''));

        // Moodle core subsystems.
        $this->assertSame('core_admin', core_component::normalize_componentname('admin'));
        $this->assertSame('core_admin', core_component::normalize_componentname('core_admin'));
        $this->assertSame('core_admin', core_component::normalize_componentname('moodle_admin'));

        // Activity modules and their subplugins.
        $this->assertSame('mod_workshop', core_component::normalize_componentname('workshop'));
        $this->assertSame('mod_workshop', core_component::normalize_componentname('mod_workshop'));
        $this->assertSame('workshopform_accumulative', core_component::normalize_componentname('workshopform_accumulative'));
        $this->assertSame('mod_quiz', core_component::normalize_componentname('quiz'));
        $this->assertSame('quiz_grading', core_component::normalize_componentname('quiz_grading'));
        $this->assertSame('mod_data', core_component::normalize_componentname('data'));
        $this->assertSame('datafield_checkbox', core_component::normalize_componentname('datafield_checkbox'));

        // Other plugin types.
        $this->assertSame('auth_mnet', core_component::normalize_componentname('auth_mnet'));
        $this->assertSame('enrol_self', core_component::normalize_componentname('enrol_self'));
        $this->assertSame('block_html', core_component::normalize_componentname('block_html'));
        $this->assertSame('block_mnet_hosts', core_component::normalize_componentname('block_mnet_hosts'));
        $this->assertSame('local_amos', core_component::normalize_componentname('local_amos'));
        $this->assertSame('local_admin', core_component::normalize_componentname('local_admin'));

        // Unknown words without underscore are supposed to be activity modules.
        $this->assertSame('mod_whoonearthwouldcomewithsuchastupidnameofcomponent',
            core_component::normalize_componentname('whoonearthwouldcomewithsuchastupidnameofcomponent'));
        // Module names can not contain underscores, this must be a subplugin.
        $this->assertSame('whoonearth_wouldcomewithsuchastupidnameofcomponent',
            core_component::normalize_componentname('whoonearth_wouldcomewithsuchastupidnameofcomponent'));
        $this->assertSame('whoonearth_would_come_withsuchastupidnameofcomponent',
            core_component::normalize_componentname('whoonearth_would_come_withsuchastupidnameofcomponent'));
    }

    public function test_normalize_component() {
        // Moodle core.
        $this->assertSame(array('core', null), core_component::normalize_component('core'));
        $this->assertSame(array('core', null), core_component::normalize_component('moodle'));
        $this->assertSame(array('core', null), core_component::normalize_component(''));

        // Moodle core subsystems.
        $this->assertSame(array('core', 'admin'), core_component::normalize_component('admin'));
        $this->assertSame(array('core', 'admin'), core_component::normalize_component('core_admin'));
        $this->assertSame(array('core', 'admin'), core_component::normalize_component('moodle_admin'));

        // Activity modules and their subplugins.
        $this->assertSame(array('mod', 'workshop'), core_component::normalize_component('workshop'));
        $this->assertSame(array('mod', 'workshop'), core_component::normalize_component('mod_workshop'));
        $this->assertSame(array('workshopform', 'accumulative'), core_component::normalize_component('workshopform_accumulative'));
        $this->assertSame(array('mod', 'quiz'), core_component::normalize_component('quiz'));
        $this->assertSame(array('quiz', 'grading'), core_component::normalize_component('quiz_grading'));
        $this->assertSame(array('mod', 'data'), core_component::normalize_component('data'));
        $this->assertSame(array('datafield', 'checkbox'), core_component::normalize_component('datafield_checkbox'));

        // Other plugin types.
        $this->assertSame(array('auth', 'mnet'), core_component::normalize_component('auth_mnet'));
        $this->assertSame(array('enrol', 'self'), core_component::normalize_component('enrol_self'));
        $this->assertSame(array('block', 'html'), core_component::normalize_component('block_html'));
        $this->assertSame(array('block', 'mnet_hosts'), core_component::normalize_component('block_mnet_hosts'));
        $this->assertSame(array('local', 'amos'), core_component::normalize_component('local_amos'));
        $this->assertSame(array('local', 'admin'), core_component::normalize_component('local_admin'));

        // Unknown words without underscore are supposed to be activity modules.
        $this->assertSame(array('mod', 'whoonearthwouldcomewithsuchastupidnameofcomponent'),
            core_component::normalize_component('whoonearthwouldcomewithsuchastupidnameofcomponent'));
        // Module names can not contain underscores, this must be a subplugin.
        $this->assertSame(array('whoonearth', 'wouldcomewithsuchastupidnameofcomponent'),
            core_component::normalize_component('whoonearth_wouldcomewithsuchastupidnameofcomponent'));
        $this->assertSame(array('whoonearth', 'would_come_withsuchastupidnameofcomponent'),
            core_component::normalize_component('whoonearth_would_come_withsuchastupidnameofcomponent'));
    }

    public function test_deprecated_normalize_component() {
        // Moodle core.
        $this->assertSame(array('core', null), normalize_component('core'));
        $this->assertSame(array('core', null), normalize_component(''));
        $this->assertSame(array('core', null), normalize_component('moodle'));

        // Moodle core subsystems.
        $this->assertSame(array('core', 'admin'), normalize_component('admin'));
        $this->assertSame(array('core', 'admin'), normalize_component('core_admin'));
        $this->assertSame(array('core', 'admin'), normalize_component('moodle_admin'));

        // Activity modules and their subplugins.
        $this->assertSame(array('mod', 'workshop'), normalize_component('workshop'));
        $this->assertSame(array('mod', 'workshop'), normalize_component('mod_workshop'));
        $this->assertSame(array('workshopform', 'accumulative'), normalize_component('workshopform_accumulative'));
        $this->assertSame(array('mod', 'quiz'), normalize_component('quiz'));
        $this->assertSame(array('quiz', 'grading'), normalize_component('quiz_grading'));
        $this->assertSame(array('mod', 'data'), normalize_component('data'));
        $this->assertSame(array('datafield', 'checkbox'), normalize_component('datafield_checkbox'));

        // Other plugin types.
        $this->assertSame(array('auth', 'mnet'), normalize_component('auth_mnet'));
        $this->assertSame(array('enrol', 'self'), normalize_component('enrol_self'));
        $this->assertSame(array('block', 'html'), normalize_component('block_html'));
        $this->assertSame(array('block', 'mnet_hosts'), normalize_component('block_mnet_hosts'));
        $this->assertSame(array('local', 'amos'), normalize_component('local_amos'));
        $this->assertSame(array('local', 'admin'), normalize_component('local_admin'));

        // Unknown words without underscore are supposed to be activity modules.
        $this->assertSame(array('mod', 'whoonearthwouldcomewithsuchastupidnameofcomponent'),
            normalize_component('whoonearthwouldcomewithsuchastupidnameofcomponent'));
        // Module names can not contain underscores, this must be a subplugin.
        $this->assertSame(array('whoonearth', 'wouldcomewithsuchastupidnameofcomponent'),
            normalize_component('whoonearth_wouldcomewithsuchastupidnameofcomponent'));
        $this->assertSame(array('whoonearth', 'would_come_withsuchastupidnameofcomponent'),
            normalize_component('whoonearth_would_come_withsuchastupidnameofcomponent'));
    }

    public function test_get_component_directory() {
        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame($plugindir, core_component::get_component_directory(($plugintype.'_'.$pluginname)));
            }
        }

        $subsystems = core_component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertSame($fulldir, core_component::get_component_directory(('core_'.$subsystem)));
        }
    }

    public function test_deprecated_get_component_directory() {
        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame($plugindir, get_component_directory(($plugintype.'_'.$pluginname)));
            }
        }

        $subsystems = core_component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertSame($fulldir, get_component_directory(('core_'.$subsystem)));
        }
    }

    public function test_get_subtype_parent() {
        global $CFG;

        $this->assertNull(core_component::get_subtype_parent('mod'));

        // Any plugin with more subtypes is ok here.
        $this->assertFileExists("$CFG->dirroot/mod/assign/db/subplugins.php");
        $this->assertSame('mod_assign', core_component::get_subtype_parent('assignsubmission'));
        $this->assertSame('mod_assign', core_component::get_subtype_parent('assignfeedback'));
        $this->assertNull(core_component::get_subtype_parent('assignxxxxx'));
    }

    public function test_get_subplugins() {
        global $CFG;

        // Any plugin with more subtypes is ok here.
        $this->assertFileExists("$CFG->dirroot/mod/assign/db/subplugins.php");

        $subplugins = core_component::get_subplugins('mod_assign');
        $this->assertSame(array('assignsubmission', 'assignfeedback'), array_keys($subplugins));

        $subs = core_component::get_plugin_list('assignsubmission');
        $feeds = core_component::get_plugin_list('assignfeedback');

        $this->assertSame(array_keys($subs), $subplugins['assignsubmission']);
        $this->assertSame(array_keys($feeds), $subplugins['assignfeedback']);

        // Any plugin without subtypes is ok here.
        $this->assertFileExists("$CFG->dirroot/mod/choice");
        $this->assertFileNotExists("$CFG->dirroot/mod/choice/db/subplugins.php");

        $this->assertNull(core_component::get_subplugins('mod_choice'));

        $this->assertNull(core_component::get_subplugins('xxxx_yyyy'));
    }

    public function test_get_plugin_types_with_subplugins() {
        global $CFG;

        $types = core_component::get_plugin_types_with_subplugins();

        // Hardcode it here to detect if anybody hacks the code to include more subplugin types.
        $expected = array(
            'mod' => "$CFG->dirroot/mod",
            'editor' => "$CFG->dirroot/lib/editor",
            'tool' => "$CFG->dirroot/$CFG->admin/tool",
            'local' => "$CFG->dirroot/local",
        );

        $this->assertSame($expected, $types);

    }

    public function test_get_plugin_list_with_file() {
        $this->resetAfterTest(true);

        // No extra reset here because core_component reset automatically.

        $expected = array();
        $reports = core_component::get_plugin_list('report');
        foreach ($reports as $name => $fulldir) {
            if (file_exists("$fulldir/lib.php")) {
                $expected[] = $name;
            }
        }

        // Test cold.
        $list = core_component::get_plugin_list_with_file('report', 'lib.php', false);
        $this->assertEquals($expected, array_keys($list));

        // Test hot.
        $list = core_component::get_plugin_list_with_file('report', 'lib.php', false);
        $this->assertEquals($expected, array_keys($list));

        // Test with include.
        $list = core_component::get_plugin_list_with_file('report', 'lib.php', true);
        $this->assertEquals($expected, array_keys($list));

        // Test missing.
        $list = core_component::get_plugin_list_with_file('report', 'idontexist.php', true);
        $this->assertEquals(array(), array_keys($list));
    }

    public function test_get_component_classes_int_namespace() {

        // Unexisting.
        $this->assertCount(0, core_component::get_component_classes_in_namespace('core_unexistingcomponent', 'something'));
        $this->assertCount(0, core_component::get_component_classes_in_namespace('auth_cas', 'something'));

        // Matches the last namespace level name not partials.
        $this->assertCount(0, core_component::get_component_classes_in_namespace('auth_cas', 'tas'));
        $this->assertCount(0, core_component::get_component_classes_in_namespace('core_user', 'course'));
        $this->assertCount(0, core_component::get_component_classes_in_namespace('mod_forum', 'output\\emaildigest'));
        $this->assertCount(0, core_component::get_component_classes_in_namespace('mod_forum', '\\output\\emaildigest'));
        $this->assertCount(2, core_component::get_component_classes_in_namespace('mod_forum', 'output\\email'));
        $this->assertCount(2, core_component::get_component_classes_in_namespace('mod_forum', '\\output\\email'));
        $this->assertCount(2, core_component::get_component_classes_in_namespace('mod_forum', 'output\\email\\'));
        $this->assertCount(2, core_component::get_component_classes_in_namespace('mod_forum', '\\output\\email\\'));

        // Prefix with backslash if it doesn\'t come prefixed.
        $this->assertCount(1, core_component::get_component_classes_in_namespace('auth_cas', 'task'));
        $this->assertCount(1, core_component::get_component_classes_in_namespace('auth_cas', '\\task'));

        // Core as a component works, the funcion can normalise the component name.
        $this->assertCount(7, core_component::get_component_classes_in_namespace('core', 'update'));
        $this->assertCount(7, core_component::get_component_classes_in_namespace('', 'update'));
        $this->assertCount(7, core_component::get_component_classes_in_namespace('moodle', 'update'));

        // Multiple levels.
        $this->assertCount(5, core_component::get_component_classes_in_namespace('core_user', '\\output\\myprofile\\'));
        $this->assertCount(5, core_component::get_component_classes_in_namespace('core_user', 'output\\myprofile\\'));
        $this->assertCount(5, core_component::get_component_classes_in_namespace('core_user', '\\output\\myprofile'));
        $this->assertCount(5, core_component::get_component_classes_in_namespace('core_user', 'output\\myprofile'));

        // Without namespace it returns classes/ classes.
        $this->assertCount(3, core_component::get_component_classes_in_namespace('tool_mobile', ''));
        $this->assertCount(2, core_component::get_component_classes_in_namespace('tool_filetypes'));
    }

    /**
     * Data provider for classloader test
     */
    public function classloader_provider() {
        global $CFG;

        // As part of these tests, we Check that there are no unexpected problems with overlapping PSR namespaces.
        // This is not in the spec, but may come up in some libraries using both namespaces and PEAR-style class names.
        // If problems arise we can remove this test, but will need to add a warning.
        // Normalise to forward slash for testing purposes.
        $directory = str_replace('\\', '/', $CFG->dirroot) . "/lib/tests/fixtures/component/";

        $psr0 = [
          'psr0'      => 'lib/tests/fixtures/component/psr0',
          'overlap'   => 'lib/tests/fixtures/component/overlap'
        ];
        $psr4 = [
          'psr4'      => 'lib/tests/fixtures/component/psr4',
          'overlap'   => 'lib/tests/fixtures/component/overlap'
        ];
        return [
          'PSR-0 Classloading - Root' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0_main',
              'includedfiles' => "{$directory}psr0/main.php",
          ],
          'PSR-0 Classloading - Sub namespace - underscores' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0_subnamespace_example',
              'includedfiles' => "{$directory}psr0/subnamespace/example.php",
          ],
          'PSR-0 Classloading - Sub namespace - slashes' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0\\subnamespace\\slashes',
              'includedfiles' => "{$directory}psr0/subnamespace/slashes.php",
          ],
          'PSR-4 Classloading - Root' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\main',
              'includedfiles' => "{$directory}psr4/main.php",
          ],
          'PSR-4 Classloading - Sub namespace' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\subnamespace\\example',
              'includedfiles' => "{$directory}psr4/subnamespace/example.php",
          ],
          'PSR-4 Classloading - Ensure underscores are not converted to paths' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\subnamespace\\underscore_example',
              'includedfiles' => "{$directory}psr4/subnamespace/underscore_example.php",
          ],
          'Overlap - Ensure no unexpected problems with PSR-4 when overlapping namespaces.' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'overlap\\subnamespace\\example',
              'includedfiles' => "{$directory}overlap/subnamespace/example.php",
          ],
          'Overlap - Ensure no unexpected problems with PSR-0 overlapping namespaces.' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'overlap_subnamespace_example2',
              'includedfiles' => "{$directory}overlap/subnamespace/example2.php",
          ],
        ];
    }

    /**
     * Test the classloader.
     *
     * @dataProvider classloader_provider
     * @param array $psr0 The PSR-0 namespaces to be used in the test.
     * @param array $psr4 The PSR-4 namespaces to be used in the test.
     * @param string $classname The name of the class to attempt to load.
     * @param string $includedfiles The file expected to be loaded.
     */
    public function test_classloader($psr0, $psr4, $classname, $includedfiles) {
        $psr0namespaces = new ReflectionProperty('core_component', 'psr0namespaces');
        $psr0namespaces->setAccessible(true);
        $psr0namespaces->setValue(null, $psr0);

        $psr4namespaces = new ReflectionProperty('core_component', 'psr4namespaces');
        $psr4namespaces->setAccessible(true);
        $psr4namespaces->setValue(null, $psr4);

        core_component::classloader($classname);
        if (DIRECTORY_SEPARATOR != '/') {
            // Denormalise the expected path so that we can quickly compare with get_included_files.
            $includedfiles = str_replace('/', DIRECTORY_SEPARATOR, $includedfiles);
        }
        $this->assertContains($includedfiles, get_included_files());
        $this->assertTrue(class_exists($classname, false));
    }

    /**
     * Data provider for psr_classloader test
     */
    public function psr_classloader_provider() {
        global $CFG;

        // As part of these tests, we Check that there are no unexpected problems with overlapping PSR namespaces.
        // This is not in the spec, but may come up in some libraries using both namespaces and PEAR-style class names.
        // If problems arise we can remove this test, but will need to add a warning.
        // Normalise to forward slash for testing purposes.
        $directory = str_replace('\\', '/', $CFG->dirroot) . "/lib/tests/fixtures/component/";

        $psr0 = [
          'psr0'      => 'lib/tests/fixtures/component/psr0',
          'overlap'   => 'lib/tests/fixtures/component/overlap'
        ];
        $psr4 = [
          'psr4'      => 'lib/tests/fixtures/component/psr4',
          'overlap'   => 'lib/tests/fixtures/component/overlap'
        ];
        return [
          'PSR-0 Classloading - Root' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0_main',
              'file' => "{$directory}psr0/main.php",
          ],
          'PSR-0 Classloading - Sub namespace - underscores' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0_subnamespace_example',
              'file' => "{$directory}psr0/subnamespace/example.php",
          ],
          'PSR-0 Classloading - Sub namespace - slashes' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0\\subnamespace\\slashes',
              'file' => "{$directory}psr0/subnamespace/slashes.php",
          ],
          'PSR-0 Classloading - non-existant file' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr0_subnamespace_nonexistant_file',
              'file' => false,
          ],
          'PSR-4 Classloading - Root' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\main',
              'file' => "{$directory}psr4/main.php",
          ],
          'PSR-4 Classloading - Sub namespace' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\subnamespace\\example',
              'file' => "{$directory}psr4/subnamespace/example.php",
          ],
          'PSR-4 Classloading - Ensure underscores are not converted to paths' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\subnamespace\\underscore_example',
              'file' => "{$directory}psr4/subnamespace/underscore_example.php",
          ],
          'PSR-4 Classloading - non-existant file' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'psr4\\subnamespace\\nonexistant',
              'file' => false,
          ],
          'Overlap - Ensure no unexpected problems with PSR-4 when overlapping namespaces.' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'overlap\\subnamespace\\example',
              'file' => "{$directory}overlap/subnamespace/example.php",
          ],
          'Overlap - Ensure no unexpected problems with PSR-0 overlapping namespaces.' => [
              'psr0' => $psr0,
              'psr4' => $psr4,
              'classname' => 'overlap_subnamespace_example2',
              'file' => "{$directory}overlap/subnamespace/example2.php",
          ],
        ];
    }

    /**
     * Test the PSR classloader.
     *
     * @dataProvider psr_classloader_provider
     * @param array $psr0 The PSR-0 namespaces to be used in the test.
     * @param array $psr4 The PSR-4 namespaces to be used in the test.
     * @param string $classname The name of the class to attempt to load.
     * @param string|bool $file The expected file corresponding to the class or false for nonexistant.
     */
    public function test_psr_classloader($psr0, $psr4, $classname, $file) {
        $psr0namespaces = new ReflectionProperty('core_component', 'psr0namespaces');
        $psr0namespaces->setAccessible(true);
        $psr0namespaces->setValue(null, $psr0);

        $psr4namespaces = new ReflectionProperty('core_component', 'psr4namespaces');
        $psr4namespaces->setAccessible(true);
        $oldpsr4namespaces = $psr4namespaces->getValue(null);
        $psr4namespaces->setValue(null, $psr4);

        $component = new ReflectionClass('core_component');
        $psrclassloader = $component->getMethod('psr_classloader');
        $psrclassloader->setAccessible(true);

        $returnvalue = $psrclassloader->invokeArgs(null, array($classname));
        // Normalise to forward slashes for testing comparison.
        if ($returnvalue) {
            $returnvalue = str_replace('\\', '/', $returnvalue);
        }
        $this->assertEquals($file, $returnvalue);
    }

    /**
     * Data provider for get_class_file test
     */
    public function get_class_file_provider() {
        global $CFG;

        return [
          'Getting a file with underscores' => [
              'classname' => 'Test_With_Underscores',
              'prefix' => "Test",
              'path' => 'test/src',
              'separators' => ['_'],
              'result' => $CFG->dirroot . "/test/src/With/Underscores.php",
          ],
          'Getting a file with slashes' => [
              'classname' => 'Test\\With\\Slashes',
              'prefix' => "Test",
              'path' => 'test/src',
              'separators' => ['\\'],
              'result' => $CFG->dirroot . "/test/src/With/Slashes.php",
          ],
          'Getting a file with multiple namespaces' => [
              'classname' => 'Test\\With\\Multiple\\Namespaces',
              'prefix' => "Test\\With",
              'path' => 'test/src',
              'separators' => ['\\'],
              'result' => $CFG->dirroot . "/test/src/Multiple/Namespaces.php",
          ],
          'Getting a file with multiple namespaces' => [
              'classname' => 'Nonexistant\\Namespace\\Test',
              'prefix' => "Test",
              'path' => 'test/src',
              'separators' => ['\\'],
              'result' => false,
          ],
        ];
    }

    /**
     * Test the PSR classloader.
     *
     * @dataProvider get_class_file_provider
     * @param string $classname the name of the class.
     * @param string $prefix The namespace prefix used to identify the base directory of the source files.
     * @param string $path The relative path to the base directory of the source files.
     * @param string[] $separators The characters that should be used for separating.
     * @param string|bool $result The expected result to be returned from get_class_file.
     */
    public function test_get_class_file($classname, $prefix, $path, $separators, $result) {
        $component = new ReflectionClass('core_component');
        $psrclassloader = $component->getMethod('get_class_file');
        $psrclassloader->setAccessible(true);

        $file = $psrclassloader->invokeArgs(null, array($classname, $prefix, $path, $separators));
        $this->assertEquals($result, $file);
    }

    /**
     * Confirm the get_component_list method contains an entry for every component.
     */
    public function test_get_component_list_contains_all_components() {
        global $CFG;
        $componentslist = \core_component::get_component_list();

        // We should have an entry for each plugin type, and one additional for 'core'.
        $plugintypes = \core_component::get_plugin_types();
        $numelementsexpected = count($plugintypes) + 1;
        $this->assertEquals($numelementsexpected, count($componentslist));

        // And an entry for each of the plugin types.
        foreach (array_keys($plugintypes) as $plugintype) {
            $this->assertArrayHasKey($plugintype, $componentslist);
        }

        // And finally, one for 'core'.
        $this->assertArrayHasKey('core', $componentslist);

        // Check a few of the known plugin types to confirm their presence at their respective type index.
        $this->assertEquals($componentslist['core']['core_comment'], $CFG->dirroot . '/comment');
        $this->assertEquals($componentslist['mod']['mod_forum'], $CFG->dirroot . '/mod/forum');
        $this->assertEquals($componentslist['tool']['tool_usertours'], $CFG->dirroot . '/' . $CFG->admin . '/tool/usertours');
    }
}
