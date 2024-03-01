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

use DirectoryIterator;
use ReflectionClass;
use ReflectionProperty;

/**
 * component related tests.
 *
 * @package    core
 * @category   test
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \core\component
 */
final class component_test extends \advanced_testcase {
    #[\Override]
    public function tearDown(): void {
        parent::tearDown();

        component::reset();
    }

    /**
     * To be changed if number of subsystems increases/decreases,
     * this is defined here to annoy devs that try to add more without any thinking,
     * always verify that it does not collide with any existing add-on modules and subplugins!!!
     */
    const SUBSYSTEMCOUNT = 80;

    public function test_get_core_subsystems(): void {
        global $CFG;

        $subsystems = component::get_core_subsystems();

        $this->assertCount(
            self::SUBSYSTEMCOUNT,
            $subsystems,
            'Oh, somebody added or removed a core subsystem, think twice before doing that!',
        );

        // Make sure all paths are full/null, exist and are inside dirroot.
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertFalse(strpos($subsystem, '_'), 'Core subsystems must be one work without underscores');
            if ($fulldir === null) {
                if ($subsystem === 'filepicker' || $subsystem === 'help') { // phpcs:ignore
                    // Arrgghh, let's not introduce more subsystems for no real reason...
                } else {
                    // Lang strings.
                    $this->assertFileExists(
                        "$CFG->dirroot/lang/en/$subsystem.php",
                        'Core subsystems without fulldir are usually used for lang strings.',
                    );
                }
                continue;
            }
            $this->assertFileExists($fulldir);
            // Check that base uses realpath() separators and "/" in the subdirs.
            $this->assertStringStartsWith($CFG->dirroot . '/', $fulldir);
            $reldir = substr($fulldir, strlen($CFG->dirroot) + 1);
            $this->assertFalse(strpos($reldir, '\\'));
        }

        // Make sure all core language files are also subsystems!
        $items = new DirectoryIterator("$CFG->dirroot/lang/en");
        foreach ($items as $item) {
            if ($item->isDot() || $item->isDir()) {
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
            $file = substr($file, 0, strlen($file) - 4);
            $this->assertArrayHasKey(
                $file,
                $subsystems,
                'All core lang files should be subsystems, think twice before adding anything!',
            );
        }
        unset($item);
        unset($items);
    }

    public function test_deprecated_get_core_subsystems(): void {
        global $CFG;

        $subsystems = component::get_core_subsystems();

        $this->assertSame($subsystems, get_core_subsystems(true));
        $this->assertDebuggingCalled();
        $this->resetDebugging();

        $realsubsystems = get_core_subsystems();
        $this->assertdebuggingcalledcount(2);
        $this->resetDebugging();

        $this->assertSame($realsubsystems, get_core_subsystems(false));
        $this->assertdebuggingcalledcount(2);
        $this->resetDebugging();

        $this->assertEquals(count($subsystems), count($realsubsystems));

        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertArrayHasKey($subsystem, $realsubsystems);
            if ($fulldir === null) {
                $this->assertNull($realsubsystems[$subsystem]);
                continue;
            }
            $this->assertSame($fulldir, $CFG->dirroot . '/' . $realsubsystems[$subsystem]);
        }
    }

    public function test_get_plugin_types(): void {
        global $CFG;

        $this->assertTrue(
            empty($CFG->themedir),
            'Non-empty $CFG->themedir is not covered by any tests yet, you need to disable it.',
        );

        $plugintypes = component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $this->assertStringStartsWith("$CFG->dirroot/", $fulldir);
        }
    }

    public function test_deprecated_get_plugin_types(): void {
        global $CFG;

        $plugintypes = component::get_plugin_types();

        $this->assertSame($plugintypes, get_plugin_types());
        $this->assertDebuggingCalled();
        $this->resetDebugging();

        $this->assertSame($plugintypes, get_plugin_types(true));
        $this->assertDebuggingCalled();
        $this->resetDebugging();

        $realplugintypes = get_plugin_types(false);
        $this->assertdebuggingcalledcount(2);
        $this->resetDebugging();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $this->assertSame($fulldir, $CFG->dirroot . '/' . $realplugintypes[$plugintype]);
        }
    }

    public function test_get_plugin_list(): void {
        global $CFG;

        $plugintypes = component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertStringStartsWith("$CFG->dirroot/", $plugindir);
            }
            if ($plugintype !== 'auth') {
                // Let's crosscheck it with independent implementation (auth/db is an exception).
                $reldir = substr($fulldir, strlen($CFG->dirroot) + 1);
                $dirs = get_list_of_plugins($reldir);
                $dirs = array_values($dirs);
                $this->assertDebuggingCalled();
                $this->assertSame($dirs, array_keys($plugins));
            }
        }
    }

    public function test_deprecated_get_plugin_list(): void {
        $plugintypes = component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = component::get_plugin_list($plugintype);
            $this->assertSame($plugins, get_plugin_list($plugintype));
            $this->assertDebuggingCalled();
            $this->resetDebugging();
        }
    }

    public function test_get_plugin_directory(): void {
        $plugintypes = component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame($plugindir, component::get_plugin_directory($plugintype, $pluginname));
            }
        }
    }

    public function test_deprecated_get_plugin_directory(): void {
        $plugintypes = component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame(
                    component::get_plugin_directory($plugintype, $pluginname),
                    get_plugin_directory($plugintype, $pluginname),
                );
                $this->assertDebuggingCalled();
                $this->resetDebugging();
            }
        }
    }

    public function test_get_subsystem_directory(): void {
        $subsystems = component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertSame($fulldir, component::get_subsystem_directory($subsystem));
        }
    }

    /**
     * Test that the get_plugin_list_with_file() function returns the correct list of plugins.
     *
     * @covers \core\component::is_valid_plugin_name
     * @dataProvider is_valid_plugin_name_provider
     * @param array $arguments
     * @param bool $expected
     */
    public function test_is_valid_plugin_name(array $arguments, bool $expected): void {
        $this->assertEquals($expected, component::is_valid_plugin_name(...$arguments));
    }

    /**
     * Data provider for the is_valid_plugin_name function.
     *
     * @return array
     */
    public static function is_valid_plugin_name_provider(): array {
        return [
            [['mod', 'example1'], true],
            [['mod', 'feedback360'], true],
            [['mod', 'feedback_360'], false],
            [['mod', '2feedback'], false],
            [['mod', '1example'], false],
            [['mod', 'example.xx'], false],
            [['mod', '.example'], false],
            [['mod', '_example'], false],
            [['mod', 'example_'], false],
            [['mod', 'example_x1'], false],
            [['mod', 'example-x1'], false],
            [['mod', 'role'], false],

            [['tool', 'example1'], true],
            [['tool', 'example_x1'], true],
            [['tool', 'example_x1_xxx'], true],
            [['tool', 'feedback360'], true],
            [['tool', 'feed_back360'], true],
            [['tool', 'role'], true],
            [['tool', '1example'], false],
            [['tool', 'example.xx'], false],
            [['tool', 'example-xx'], false],
            [['tool', '.example'], false],
            [['tool', '_example'], false],
            [['tool', 'example_'], false],
            [['tool', 'example__x1'], false],

            // Some invalid cases.
            [['mod', null], false],
            [['mod', ''], false],
            [['tool', null], false],
            [['tool', ''], false],
        ];
    }

    public function test_normalize_componentname(): void {
        // Moodle core.
        $this->assertSame('core', component::normalize_componentname('core'));
        $this->assertSame('core', component::normalize_componentname('moodle'));
        $this->assertSame('core', component::normalize_componentname(''));

        // Moodle core subsystems.
        $this->assertSame('core_admin', component::normalize_componentname('admin'));
        $this->assertSame('core_admin', component::normalize_componentname('core_admin'));
        $this->assertSame('core_admin', component::normalize_componentname('moodle_admin'));

        // Activity modules and their subplugins.
        $this->assertSame('mod_workshop', component::normalize_componentname('workshop'));
        $this->assertSame('mod_workshop', component::normalize_componentname('mod_workshop'));
        $this->assertSame('workshopform_accumulative', component::normalize_componentname('workshopform_accumulative'));
        $this->assertSame('mod_quiz', component::normalize_componentname('quiz'));
        $this->assertSame('quiz_grading', component::normalize_componentname('quiz_grading'));
        $this->assertSame('mod_data', component::normalize_componentname('data'));
        $this->assertSame('datafield_checkbox', component::normalize_componentname('datafield_checkbox'));

        // Other plugin types.
        $this->assertSame('auth_mnet', component::normalize_componentname('auth_mnet'));
        $this->assertSame('enrol_self', component::normalize_componentname('enrol_self'));
        $this->assertSame('block_html', component::normalize_componentname('block_html'));
        $this->assertSame('block_mnet_hosts', component::normalize_componentname('block_mnet_hosts'));
        $this->assertSame('local_amos', component::normalize_componentname('local_amos'));
        $this->assertSame('local_admin', component::normalize_componentname('local_admin'));

        // Unknown words without underscore are supposed to be activity modules.
        $this->assertSame(
            'mod_whoonearthwouldcomewithsuchastupidnameofcomponent',
            component::normalize_componentname('whoonearthwouldcomewithsuchastupidnameofcomponent')
        );
        // Module names can not contain underscores, this must be a subplugin.
        $this->assertSame(
            'whoonearth_wouldcomewithsuchastupidnameofcomponent',
            component::normalize_componentname('whoonearth_wouldcomewithsuchastupidnameofcomponent')
        );
        $this->assertSame(
            'whoonearth_would_come_withsuchastupidnameofcomponent',
            component::normalize_componentname('whoonearth_would_come_withsuchastupidnameofcomponent')
        );
    }

    /**
     * Test \core_component::normalize_component function.
     *
     * @dataProvider normalise_component_provider
     * @param array $expected
     * @param string $args
     */
    public function test_normalize_component(array $expected, string $args): void {
        $this->assertSame(
            $expected,
            component::normalize_component($args),
        );
    }

    /**
     * Test the deprecated normalize_component function.
     *
     * @dataProvider normalise_component_provider
     * @param array $expected
     * @param string $args
     */
    public function test_deprecated_normalize_component(array $expected, string $args): void {
        $this->assertSame(
            $expected,
            normalize_component($args),
        );

        $this->assertDebuggingCalled();
    }

    /**
     * Data provider for the normalize_component function.
     */
    public static function normalise_component_provider(): array {
        return [
            // Moodle core.
            [['core', null], 'core'],
            [['core', null], ''],
            [['core', null], 'moodle'],

            // Moodle core subsystems.
            [['core', 'admin'], 'admin'],
            [['core', 'admin'], 'core_admin'],
            [['core', 'admin'], 'moodle_admin'],

            // Activity modules and their subplugins.
            [['mod', 'workshop'], 'workshop'],
            [['mod', 'workshop'], 'mod_workshop'],
            [['workshopform', 'accumulative'], 'workshopform_accumulative'],
            [['mod', 'quiz'], 'quiz'],
            [['quiz', 'grading'], 'quiz_grading'],
            [['mod', 'data'], 'data'],
            [['datafield', 'checkbox'], 'datafield_checkbox'],

            // Other plugin types.
            [['auth', 'mnet'], 'auth_mnet'],
            [['enrol', 'self'], 'enrol_self'],
            [['block', 'html'], 'block_html'],
            [['block', 'mnet_hosts'], 'block_mnet_hosts'],
            [['local', 'amos'], 'local_amos'],
            [['local', 'admin'], 'local_admin'],

            // Unknown words without underscore are supposed to be activity modules.
            [
                ['mod', 'whoonearthwouldcomewithsuchastupidnameofcomponent'],
                'whoonearthwouldcomewithsuchastupidnameofcomponent',
            ],
            // Module names can not contain underscores, this must be a subplugin.
            [
                ['whoonearth', 'wouldcomewithsuchastupidnameofcomponent'],
                'whoonearth_wouldcomewithsuchastupidnameofcomponent',
            ],
            [
                ['whoonearth', 'would_come_withsuchastupidnameofcomponent'],
                'whoonearth_would_come_withsuchastupidnameofcomponent',
            ],
        ];
    }

    public function test_get_component_directory(): void {
        $plugintypes = component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame($plugindir, component::get_component_directory(($plugintype . '_' . $pluginname)));
            }
        }

        $subsystems = component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertSame($fulldir, component::get_component_directory(('core_' . $subsystem)));
        }
    }

    /**
     * Unit tests for get_component_from_classname.
     *
     * @dataProvider get_component_from_classname_provider
     * @param string $classname The class name to test
     * @param string|null $expected The expected component
     * @covers \core\component::get_component_from_classname
     */
    public function test_get_component_from_classname(
        string $classname,
        string|null $expected,
    ): void {
        $this->assertEquals(
            $expected,
            component::get_component_from_classname($classname),
        );
    }

    /**
     * Data provider for get_component_from_classname tests.
     *
     * @return array
     */
    public static function get_component_from_classname_provider(): array {
        // Start off with testcases which have the leading \.
        $testcases = [
            // Core.
            [\core\example::class, 'core'],

            // A core subsystem.
            [\core_message\example::class, 'core_message'],

            // A fake core subsystem.
            [\core_fake\example::class, null],

            // A plugin.
            [\mod_forum\example::class, 'mod_forum'],

            // A plugin in the old style is not supported.
            [\mod_forum_example::class, null],

            // A fake plugin.
            [\mod_fake\example::class, null],

            // A subplugin.
            [\tiny_link\example::class, 'tiny_link'],
        ];

        // Duplicate the testcases, adding a nested namespace.
        $testcases = array_merge(
            $testcases,
            array_map(
                fn ($testcase) => [$testcase[0] . '\\in\\sub\\directory', $testcase[1]],
                $testcases,
            ),
        );

        // Duplicate the testcases, removing the leading \.
        return array_merge(
            $testcases,
            array_map(
                fn ($testcase) => [ltrim($testcase[0], '\\'), $testcase[1]],
                $testcases,
            ),
        );
    }

    public function test_deprecated_get_component_directory(): void {
        $plugintypes = component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugins = component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                $this->assertSame($plugindir, get_component_directory(($plugintype . '_' . $pluginname)));
                $this->assertDebuggingCalled();
                $this->resetDebugging();
            }
        }

        $subsystems = component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $fulldir) {
            $this->assertSame($fulldir, get_component_directory(('core_' . $subsystem)));
            $this->assertDebuggingCalled();
            $this->resetDebugging();
        }
    }

    public function test_get_subtype_parent(): void {
        global $CFG;

        $this->assertNull(component::get_subtype_parent('mod'));

        // Any plugin with more subtypes is ok here.
        $this->assertFileExists("$CFG->dirroot/mod/assign/db/subplugins.json");
        $this->assertSame('mod_assign', component::get_subtype_parent('assignsubmission'));
        $this->assertSame('mod_assign', component::get_subtype_parent('assignfeedback'));
        $this->assertNull(component::get_subtype_parent('assignxxxxx'));
    }

    public function test_get_subplugins(): void {
        global $CFG;

        // Any plugin with more subtypes is ok here.
        $this->assertFileExists("$CFG->dirroot/mod/assign/db/subplugins.json");

        $subplugins = component::get_subplugins('mod_assign');
        $this->assertSame(['assignsubmission', 'assignfeedback'], array_keys($subplugins));

        $subs = component::get_plugin_list('assignsubmission');
        $feeds = component::get_plugin_list('assignfeedback');

        $this->assertSame(array_keys($subs), $subplugins['assignsubmission']);
        $this->assertSame(array_keys($feeds), $subplugins['assignfeedback']);

        // Any plugin without subtypes is ok here.
        $this->assertFileExists("$CFG->dirroot/mod/choice");
        $this->assertFileDoesNotExist("$CFG->dirroot/mod/choice/db/subplugins.json");

        $this->assertNull(component::get_subplugins('mod_choice'));

        $this->assertNull(component::get_subplugins('xxxx_yyyy'));
    }

    public function test_get_plugin_types_with_subplugins(): void {
        global $CFG;

        $types = component::get_plugin_types_with_subplugins();

        // Hardcode it here to detect if anybody hacks the code to include more subplugin types.
        $expected = [
            'mod' => "$CFG->dirroot/mod",
            'editor' => "$CFG->dirroot/lib/editor",
            'tool' => "$CFG->dirroot/$CFG->admin/tool",
            'local' => "$CFG->dirroot/local",
        ];

        $this->assertSame($expected, $types);
    }

    public function test_get_plugin_list_with_file(): void {
        $this->resetAfterTest(true);

        // No extra reset here because component reset automatically.

        $expected = [];
        $reports = component::get_plugin_list('report');
        foreach ($reports as $name => $fulldir) {
            if (file_exists("$fulldir/lib.php")) {
                $expected[] = $name;
            }
        }

        // Test cold.
        $list = component::get_plugin_list_with_file('report', 'lib.php', false);
        $this->assertEquals($expected, array_keys($list));

        // Test hot.
        $list = component::get_plugin_list_with_file('report', 'lib.php', false);
        $this->assertEquals($expected, array_keys($list));

        // Test with include.
        $list = component::get_plugin_list_with_file('report', 'lib.php', true);
        $this->assertEquals($expected, array_keys($list));

        // Test missing.
        $list = component::get_plugin_list_with_file('report', 'idontexist.php', true);
        $this->assertEquals([], array_keys($list));
    }

    /**
     * Tests for get_component_classes_in_namespace.
     */
    public function test_get_component_classes_in_namespace(): void {
        // Unexisting.
        $this->assertCount(0, component::get_component_classes_in_namespace('core_unexistingcomponent', 'something'));
        $this->assertCount(0, component::get_component_classes_in_namespace('auth_cas', 'something'));

        // Matches the last namespace level name not partials.
        $this->assertCount(0, component::get_component_classes_in_namespace('auth_cas', 'tas'));
        $this->assertCount(0, component::get_component_classes_in_namespace('core_user', 'course'));
        $this->assertCount(0, component::get_component_classes_in_namespace('mod_forum', 'output\\emaildigest'));
        $this->assertCount(0, component::get_component_classes_in_namespace('mod_forum', '\\output\\emaildigest'));

        // Without either a component or namespace it returns an empty array.
        $this->assertEmpty(component::get_component_classes_in_namespace());
        $this->assertEmpty(component::get_component_classes_in_namespace(null));
        $this->assertEmpty(component::get_component_classes_in_namespace(null, ''));
    }

    /**
     * Test that the get_component_classes_in_namespace() function returns classes in the correct namespace.
     *
     * @dataProvider get_component_classes_in_namespace_provider
     * @param array $methodargs
     * @param string $expectedclassnameformat
     */
    public function test_get_component_classes_in_namespace_provider(
        array $methodargs,
        string $expectedclassnameformat,
    ): void {
        $classlist = component::get_component_classes_in_namespace(...$methodargs);
        $this->assertGreaterThan(0, count($classlist));

        foreach (array_keys($classlist) as $classname) {
            $this->assertStringMatchesFormat($expectedclassnameformat, $classname);
        }
    }

    /**
     * Data provider for get_component_classes_in_namespace tests.
     *
     * @return array
     */
    public static function get_component_classes_in_namespace_provider(): array {
        return [
            // Matches the last namespace level name not partials.
            [
                ['mod_forum', 'output\\email'],
                'mod_forum\output\email\%s',
            ],
            [
                ['mod_forum', '\\output\\email'],
                'mod_forum\output\email\%s',
            ],
            [
                ['mod_forum', 'output\\email\\'],
                'mod_forum\output\email\%s',
            ],
            [
                ['mod_forum', '\\output\\email\\'],
                'mod_forum\output\email\%s',
            ],
            // Prefix with backslash if it doesn\'t come prefixed.
            [
                ['auth_cas', 'task'],
                'auth_cas\task\%s',
            ],
            [
                ['auth_cas', '\\task'],
                'auth_cas\task\%s',
            ],

            // Core as a component works, the function can normalise the component name.
            [
                ['core', 'update'],
                'core\update\%s',
            ],
            [
                ['', 'update'],
                'core\update\%s',
            ],
            [
                ['moodle', 'update'],
                'core\update\%s',
            ],

            // Multiple levels.
            [
                ['core_user', '\\output\\myprofile\\'],
                'core_user\output\myprofile\%s',
            ],
            [
                ['core_user', 'output\\myprofile\\'],
                'core_user\output\myprofile\%s',
            ],
            [
                ['core_user', '\\output\\myprofile'],
                'core_user\output\myprofile\%s',
            ],
            [
                ['core_user', 'output\\myprofile'],
                'core_user\output\myprofile\%s',
            ],

            // Without namespace it returns classes/ classes.
            [
                ['tool_mobile', ''],
                'tool_mobile\%s',
            ],
            [
                ['tool_filetypes'],
                'tool_filetypes\%s',
            ],

            // Multiple levels.
            [
                ['core_user', '\\output\\myprofile\\'],
                'core_user\output\myprofile\%s',
            ],

            // When no component is specified, classes are returned for the namespace in all components.
            // (We don't assert exact amounts here as the count of `output` classes will change depending on plugins installed).
            [
                ['core', 'output'],
                'core\%s',
            ],
            [
                [null, 'output'],
                '%s',
            ],
        ];
    }

    /**
     * Data provider for classloader test
     */
    public static function classloader_provider(): array {
        global $CFG;

        // As part of these tests, we Check that there are no unexpected problems with overlapping PSR namespaces.
        // This is not in the spec, but may come up in some libraries using both namespaces and PEAR-style class names.
        // If problems arise we can remove this test, but will need to add a warning.
        // Normalise to forward slash for testing purposes.
        $directory = str_replace('\\', '/', $CFG->dirroot) . "/lib/tests/fixtures/component/";

        $psr0 = [
          'psr0'      => 'lib/tests/fixtures/component/psr0',
          'overlap'   => 'lib/tests/fixtures/component/overlap',
        ];
        $psr4 = [
          'psr4'      => 'lib/tests/fixtures/component/psr4',
          'overlap'   => 'lib/tests/fixtures/component/overlap',
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
     * @runInSeparateProcess
     */
    public function test_classloader($psr0, $psr4, $classname, $includedfiles): void {
        $psr0namespaces = new ReflectionProperty(component::class, 'psr0namespaces');
        $psr0namespaces->setValue(null, $psr0);

        $psr4namespaces = new ReflectionProperty(component::class, 'psr4namespaces');
        $psr4namespaces->setValue(null, $psr4);

        component::classloader($classname);
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
    public static function psr_classloader_provider(): array {
        global $CFG;

        // As part of these tests, we Check that there are no unexpected problems with overlapping PSR namespaces.
        // This is not in the spec, but may come up in some libraries using both namespaces and PEAR-style class names.
        // If problems arise we can remove this test, but will need to add a warning.
        // Normalise to forward slash for testing purposes.
        $dirroot = str_replace('\\', '/', $CFG->dirroot);
        $directory = "{$dirroot}/lib/tests/fixtures/component/";

        $psr0 = [
          'psr0'      => 'lib/tests/fixtures/component/psr0',
          'overlap'   => 'lib/tests/fixtures/component/overlap',
        ];
        $psr4 = [
          'psr4'      => 'lib/tests/fixtures/component/psr4',
          'overlap'   => 'lib/tests/fixtures/component/overlap',
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
            'PSR-0 Classloading - non-existent file' => [
                'psr0' => $psr0,
                'psr4' => $psr4,
                'classname' => 'psr0_subnamespace_nonexistent_file',
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
            'PSR-4 Classloading - non-existent file' => [
                'psr0' => $psr0,
                'psr4' => $psr4,
                'classname' => 'psr4\\subnamespace\\nonexistent',
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
            'PSR-4 namespaces can come from multiple sources - first source' => [
                'psr0' => $psr0,
                'psr4' => [
                    'Psr\\Http\\Message' => [
                        'lib/psr/http-message/src',
                        'lib/psr/http-factory/src',
                    ],
                ],
                'classname' => 'Psr\Http\Message\ServerRequestInterface',
                'includedfiles' => "{$dirroot}/lib/psr/http-message/src/ServerRequestInterface.php",
            ],
            'PSR-4 namespaces can come from multiple sources - second source' => [
                'psr0' => [],
                'psr4' => [
                    'Psr\\Http\\Message' => [
                        'lib/psr/http-message/src',
                        'lib/psr/http-factory/src',
                    ],
                ],
                'classname' => 'Psr\Http\Message\ServerRequestFactoryInterface',
                'includedfiles' => "{$dirroot}/lib/psr/http-factory/src/ServerRequestFactoryInterface.php",
            ],
        ];
    }

    /**
     * Test that the classloader can load from the test namespaces.
     */
    public function test_classloader_tests_namespace(): void {
        global $CFG;

        $this->resetAfterTest();

        $getclassfilecontent = function (string $classname, ?string $namespace): string {
            if ($namespace) {
                $content = "<?php\nnamespace $namespace;\nclass $classname {}";
            } else {
                $content = "<?php\nclass $classname {}";
            }
            return $content;
        };

        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, [
            'lib' => [
                'classes' => [
                    'example.php' => $getclassfilecontent('example', 'core'),
                ],
                'tests' => [
                    'classes' => [
                        'example_classname.php' => $getclassfilecontent('example_classname', \core\tests::class),
                    ],
                    'behat' => [
                        'example_classname.php' => $getclassfilecontent('example_classname', \core\behat::class),
                    ],
                ],
            ],
        ]);

        // Note: This is pretty hacky, but it's the only way to test the classloader.
        // We have to override the dirroot and libdir, and then reset the plugintypes property.
        $CFG->dirroot = $vfileroot->url();
        $CFG->libdir = $vfileroot->url() . '/lib';
        component::reset();

        // Existing classes do not break.
        $this->assertTrue(
            class_exists(\core\example::class),
        );

        // Test and behat classes work.
        $this->assertTrue(
            class_exists(\core\tests\example_classname::class),
        );
        $this->assertTrue(
            class_exists(\core\behat\example_classname::class),
        );

        // Non-existent classes do not do anything.
        $this->assertFalse(
            class_exists(\core\tests\example_classname_not_found::class),
        );
    }

    /**
     * Test the PSR classloader.
     *
     * @dataProvider psr_classloader_provider
     * @param array $psr0 The PSR-0 namespaces to be used in the test.
     * @param array $psr4 The PSR-4 namespaces to be used in the test.
     * @param string $classname The name of the class to attempt to load.
     * @param string|bool $file The expected file corresponding to the class or false for nonexistant.
     * @runInSeparateProcess
     */
    public function test_psr_classloader($psr0, $psr4, $classname, $file): void {
        return;
        $psr0namespaces = new ReflectionProperty(component::class, 'psr0namespaces');
        $psr0namespaces->setValue(null, $psr0);

        $psr4namespaces = new ReflectionProperty(component::class, 'psr4namespaces');
        $psr4namespaces->setValue(null, $psr4);

        $component = new ReflectionClass(component::class);
        $psrclassloader = $component->getMethod('psr_classloader');

        $returnvalue = $psrclassloader->invokeArgs(null, [$classname]);
        // Normalise to forward slashes for testing comparison.
        if ($returnvalue) {
            $returnvalue = str_replace('\\', '/', $returnvalue);
        }
        $this->assertEquals($file, $returnvalue);
    }

    /**
     * Data provider for get_class_file test
     */
    public static function get_class_file_provider(): array {
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
          'Getting a file with multiple namespaces (non-existent)' => [
              'classname' => 'Nonexistent\\Namespace\\Test',
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
    public function test_get_class_file($classname, $prefix, $path, $separators, $result): void {
        $component = new ReflectionClass(component::class);
        $psrclassloader = $component->getMethod('get_class_file');

        $file = $psrclassloader->invokeArgs(null, [$classname, $prefix, $path, $separators]);
        $this->assertEquals($result, $file);
    }

    /**
     * Confirm the get_component_list method contains an entry for every component.
     */
    public function test_get_component_list_contains_all_components(): void {
        global $CFG;
        $componentslist = component::get_component_list();

        // We should have an entry for each plugin type, and one additional for 'core'.
        $plugintypes = component::get_plugin_types();
        $numelementsexpected = count($plugintypes) + 1;
        $this->assertEquals($numelementsexpected, count($componentslist));

        // And an entry for each of the plugin types.
        foreach (array_keys($plugintypes) as $plugintype) {
            $this->assertArrayHasKey($plugintype, $componentslist);
        }

        // And one for 'core'.
        $this->assertArrayHasKey('core', $componentslist);

        // Check a few of the known plugin types to confirm their presence at their respective type index.
        $this->assertEquals($componentslist['core']['core_comment'], $CFG->dirroot . '/comment');
        $this->assertEquals($componentslist['mod']['mod_forum'], $CFG->dirroot . '/mod/forum');
        $this->assertEquals($componentslist['tool']['tool_usertours'], $CFG->dirroot . '/' . $CFG->admin . '/tool/usertours');
    }

    /**
     * Test the get_component_names() method.
     *
     * @dataProvider get_component_names_provider
     * @param bool $includecore Whether to include core in the list.
     * @param bool $coreexpected Whether core is expected to be in the list.
     */
    public function test_get_component_names(
        bool $includecore,
        bool $coreexpected,
    ): void {
        global $CFG;
        $componentnames = component::get_component_names($includecore);

        // We should have an entry for each plugin type.
        $plugintypes = component::get_plugin_types();
        $numplugintypes = 0;
        foreach (array_keys($plugintypes) as $type) {
            $numplugintypes += count(component::get_plugin_list($type));
        }
        // And an entry for each core subsystem.
        $numcomponents = $numplugintypes + count(component::get_core_subsystems());

        if ($coreexpected) {
            // Add one for core.
            $numcomponents++;
        }
        $this->assertEquals($numcomponents, count($componentnames));

        // Check a few of the known plugin types to confirm their presence at their respective type index.
        $this->assertContains('core_comment', $componentnames);
        $this->assertContains('mod_forum', $componentnames);
        $this->assertContains('tool_usertours', $componentnames);
        $this->assertContains('core_favourites', $componentnames);
        if ($coreexpected) {
            $this->assertContains('core', $componentnames);
        } else {
            $this->assertNotContains('core', $componentnames);
        }
    }

    /**
     * Data provider for get_component_names() test.
     *
     * @return array
     */
    public static function get_component_names_provider(): array {
        return [
            [false, false],
            [true, true],
        ];
    }

    /**
     * Basic tests for APIs related functions in the component class.
     */
    public function test_apis_methods(): void {
        $apis = component::get_core_apis();
        $this->assertIsArray($apis);

        $apinames = component::get_core_api_names();
        $this->assertIsArray($apis);

        // Both should return the very same APIs.
        $this->assertEquals($apinames, array_keys($apis));

        $this->assertFalse(component::is_core_api('lalala'));
        $this->assertTrue(component::is_core_api('privacy'));
    }

    /**
     * Test that the apis.json structure matches expectations
     *
     * While we include an apis.schema.json file in core, there isn't any PHP built-in allowing us
     * to validate it (3rd part libraries needed). Plus the schema doesn't allow to validate things
     * like uniqueness or sorting. We are going to do all that here.
     */
    public function test_apis_json_validation(): void {
        $apis = $sortedapis = component::get_core_apis();
        ksort($sortedapis); // We'll need this later.

        $subsystems = component::get_core_subsystems(); // To verify all apis are pointing to valid subsystems.
        $subsystems['core'] = 'anything'; // Let's add 'core' because it's a valid component for apis.

        // General structure validations.
        $this->assertIsArray($apis);
        $this->assertGreaterThan(25, count($apis));
        $this->assertArrayHasKey('privacy', $apis); // Verify a few.
        $this->assertArrayHasKey('external', $apis);
        $this->assertArrayHasKey('search', $apis);
        $this->assertEquals(array_keys($sortedapis), array_keys($apis)); // Verify json is sorted alphabetically.

        // Iterate over all apis and perform more validations.
        foreach ($apis as $apiname => $attributes) {
            // Message, to be used later and easier finding the problem.
            $message = "Validation problem found with API: {$apiname}";

            $this->assertIsObject($attributes, $message);
            $this->assertMatchesRegularExpression('/^[a-z][a-z0-9]+$/', $apiname, $message);
            $this->assertEquals(['component', 'allowedlevel2', 'allowedspread'], array_keys((array)$attributes), $message);

            // Verify attributes.
            if ($apiname !== 'core') { // Exception for core api, it doesn't have component.
                // Check that component attribute looks correct.
                $this->assertMatchesRegularExpression('/^(core|[a-z][a-z0-9_]+)$/', $attributes->component, $message);
                // Ensure that the api component (without the core_ prefix) is a correct subsystem.
                $this->assertArrayHasKey(str_replace('core_', '', $attributes->component), $subsystems, $message);
            } else {
                $this->assertNull($attributes->component, $message);
            }


            // Now check for the rest of attributes.
            $this->assertIsBool($attributes->allowedlevel2, $message);
            $this->assertIsBool($attributes->allowedspread, $message);

            // Cannot spread if level2 is not allowed.
            $this->assertLessThanOrEqual($attributes->allowedlevel2, $attributes->allowedspread, $message);
        }
    }

    /**
     * Test for monologo icons check in plugins.
     */
    public function test_has_monologo_icon(): void {
        // The Forum activity plugin has monologo icons.
        $this->assertTrue(component::has_monologo_icon('mod', 'forum'));
        // The core H5P subsystem doesn't have monologo icons.
        $this->assertFalse(component::has_monologo_icon('core', 'h5p'));
        // The function will return false for a non-existent component.
        $this->assertFalse(component::has_monologo_icon('randomcomponent', 'h5p'));
    }

    /*
     * Tests the getter for the db directory summary hash.
     *
     * @covers \core\component::get_all_directory_hashes
     */
    public function test_get_db_directories_hash(): void {
        $initial = component::get_all_component_hash();

        $dir = make_request_directory();
        $hashes = component::get_all_directory_hashes([$dir]);
        $emptydirhash = component::get_all_component_hash([$hashes]);

        // Confirm that a single empty directory is a different hash to the core hash.
        $this->assertNotEquals($initial, $emptydirhash);

        // Now lets add something to the dir, and check the hash is different.
        $file = fopen($dir . '/test.php', 'w');
        fwrite($file, 'sometestdata');
        fclose($file);

        $hashes = component::get_all_directory_hashes([$dir]);
        $onefiledirhash = component::get_all_component_hash([$hashes]);
        $this->assertNotEquals($emptydirhash, $onefiledirhash);

        // Now add a subdirectory inside the request dir. This should not affect the hash.
        mkdir($dir . '/subdir');
        $hashes = component::get_all_directory_hashes([$dir]);
        $finalhash = component::get_all_component_hash([$hashes]);
        $this->assertEquals($onefiledirhash, $finalhash);
    }
}
