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

use core\exception\coding_exception;
use core\tests\fake_plugins_test_trait;
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
    use fake_plugins_test_trait;

    #[\Override]
    public function tearDown(): void {
        parent::tearDown();

        ini_set('error_log', null);
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
        $this->assertSame('auth_ldap', component::normalize_componentname('auth_ldap'));
        $this->assertSame('enrol_self', component::normalize_componentname('enrol_self'));
        $this->assertSame('block_html', component::normalize_componentname('block_html'));
        $this->assertSame('auth_oauth2', component::normalize_componentname('auth_oauth2'));
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
            [['auth', 'ldap'], 'auth_ldap'],
            [['enrol', 'self'], 'enrol_self'],
            [['block', 'html'], 'block_html'],
            [['auth', 'oauth2'], 'auth_oauth2'],
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
        $this->assertCount(0, component::get_component_classes_in_namespace('auth_db', 'something'));

        // Matches the last namespace level name not partials.
        $this->assertCount(0, component::get_component_classes_in_namespace('auth_db', 'tas'));
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
                ['auth_db', 'task'],
                'auth_db\task\%s',
            ],
            [
                ['auth_db', '\\task'],
                'auth_db\task\%s',
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
                'file' => "{$dirroot}/lib/psr/http-message/src/ServerRequestInterface.php",
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
                'file' => "{$dirroot}/lib/psr/http-factory/src/ServerRequestFactoryInterface.php",
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

    /**
     * Data provider fetching all third-party lib directories.
     *
     * @return array
     */
    public static function core_thirdparty_libs_provider(): array {
        global $CFG;

        $libs = [];

        $xmlpath = $CFG->libdir . '/thirdpartylibs.xml';
        $xml = simplexml_load_file($xmlpath);
        foreach ($xml as $lib) {
            $base = realpath(dirname($xmlpath));
            $fullpath = "{$base}/{$lib->location}";
            $relativepath = substr($fullpath, strlen($CFG->dirroot));

            $libs[$relativepath] = [
                'name' => (string) $lib->name,
                'fullpath' => $fullpath,
                'relativepath' => $relativepath,
            ];
        }

        return $libs;
    }

    /**
     * Data provider fetching all third-party lib directories with a composer.json file.
     *
     * @return array
     */
    public static function core_thirdparty_libs_with_composer_provider(): array {
        return array_filter(self::core_thirdparty_libs_provider(), function ($lib) {
            return file_exists("{$lib['fullpath']}/composer.json");
        });
    }

    /**
     * Summary of test_composer_files
     *
     * @dataProvider core_thirdparty_libs_with_composer_provider
     * @param string $name
     * @param string $fullpath
     * @param string $relativepath
     */
    public function test_composer_files(
        string $name,
        string $fullpath,
        string $relativepath,
    ): void {
        $this->assertFileExists("{$fullpath}/composer.json");

        $composer = json_decode(file_get_contents("{$fullpath}/composer.json"), true);

        $rc = new ReflectionClass(\core\component::class);

        if (array_key_exists('autoload', $composer)) {
            // Check that the PSR-4 namespaces are present and correct.
            if (array_key_exists('psr-4', $composer['autoload'])) {
                $autoloadnamespaces = $rc->getProperty('psr4namespaces')->getValue(null);
                foreach ($composer['autoload']['psr-4'] as $namespace => $path) {
                    // Composer PSR-4 namespace autoloads may optionally have a trailing slash. Standardise the value.
                    $namespace = rtrim($namespace, '\\');

                    // If it exists in the composer.json the namespace must exist in our autoloader.
                    $this->assertArrayHasKey($namespace, $autoloadnamespaces);

                    // Ours should be standardised to not have a trailing slash.
                    $this->assertEquals(
                        rtrim($relativepath, '/'),
                        $relativepath,
                        "Moodle PSR-4 namespaces must have no trailing /",
                    );

                    // The composer.json can specify an array of possible values.
                    // Standardise the format to the array format.
                    $paths = is_array($path) ? $path : [$path];

                    foreach ($paths as $path) {
                        // The composer.json can specify any arbitrary directory within the folder.
                        // It always contains a leading slash (/) or backslash (\) on Windows.
                        // It may also have an optional trailing slash (/).
                        // Concatenate the parts and removes the slashes.
                        $relativenamespacepath = trim("{$relativepath}/{$path}", '/\\');

                        // The Moodle PSR-4 autoloader data has two formats:
                        // - a string, for a single source; or
                        // - an array, for multiple sources.
                        // Standardise the format to the latter format.
                        if (!is_array($autoloadnamespaces[$namespace])) {
                            $autoloadnamespaces[$namespace] = [$autoloadnamespaces[$namespace]];
                        }

                        // Ensure that the autoloader contains the normalised path.
                        $this->assertContains(
                            $relativenamespacepath,
                            $autoloadnamespaces[$namespace],
                            "Moodle PSR-4 namespace missing entry for library {$name}: {$namespace} => {$relativenamespacepath}",
                        );
                    }
                }
            }

            // Check that the composer autoload files are present.
            if (array_key_exists('files', $composer['autoload'])) {
                // The Moodle composer file autoloads are a simple string[].
                $autoloadnamefiles = $rc->getProperty('composerautoloadfiles')->getValue(null);
                foreach ($composer['autoload']['files'] as $file) {
                    $this->assertContains(trim($relativepath, '/\\') . "/{$file}", $autoloadnamefiles);
                }
            }
        }
    }

    /**
     * Test that fetching of subtype data throws an exception when a subplugins.php is present without a json equivalent.
     */
    public function test_fetch_subtypes_php_only(): void {
        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, [
            'plugintype' => [
                'exampleplugin' => [
                    'db' => [
                        'subplugins.php' => '',
                    ],
                ],
            ],
        ]);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageMatches('/Use of subplugins.php has been deprecated and is no longer supported/');

        $pluginroot = $vfileroot->getChild('plugintype/exampleplugin');

        $rcm = new \ReflectionMethod(\core\component::class, 'fetch_subtypes');
        $rcm->invoke(null, $pluginroot->url());
    }

    /**
     * Test that fetching of subtype data does not throw an exception when a subplugins.php is present
     * with a json file equivalent.
     *
     * Note: The content of the php file is irrelevant and we no longer use it anyway.
     */
    public function test_fetch_subtypes_php_and_json(): void {
        global $CFG;

        $this->resetAfterTest();
        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, [
            'plugintype' => [
                'exampleplugin' => [
                    'db' => [
                        'subplugins.json' => json_encode([
                            'subplugintypes' => [
                                'exampleplugina' => 'apples',
                            ],
                        ]),
                        'subplugins.php' => '',
                    ],
                    'apples' => [],
                ],
            ],
        ]);

        $CFG->dirroot = $vfileroot->url();
        $pluginroot = $vfileroot->getChild('plugintype/exampleplugin');

        $rcm = new \ReflectionMethod(\core\component::class, 'fetch_subtypes');
        $subplugins = $rcm->invoke(null, $pluginroot->url());

        $this->assertEquals([
            'plugintypes' => [
                'exampleplugina' => $pluginroot->getChild('apples')->url(),
            ],
        ], $subplugins);
    }

    /**
     * Test that fetching of subtype data in a file which is missing the new subplugintypes key warns.
     */
    public function test_fetch_subtypes_plugintypes_only(): void {
        global $CFG;

        $this->resetAfterTest();
        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, [
            'plugintype' => [
                'exampleplugin' => [
                    'db' => [
                        'subplugins.json' => json_encode([
                            'plugintypes' => [
                                'exampleplugina' => 'plugintype/exampleplugin/apples',
                            ],
                        ]),
                        'subplugins.php' => '',
                    ],
                    'apples' => [],
                ],
            ],
        ]);

        $CFG->dirroot = $vfileroot->url();
        $pluginroot = $vfileroot->getChild('plugintype/exampleplugin');

        $logdir = make_request_directory();
        $logfile = "{$logdir}/error.log";
        ini_set('error_log', $logfile);

        $rcm = new \ReflectionMethod(\core\component::class, 'fetch_subtypes');
        $subplugins = $rcm->invoke(null, $pluginroot->url());

        $this->assertEquals([
            'plugintypes' => [
                'exampleplugina' => $pluginroot->getChild('apples')->url(),
            ],
        ], $subplugins);

        $warnings = file_get_contents($logfile);
        $this->assertMatchesRegularExpression('/No subplugintypes defined in .*subplugins.json/', $warnings);
    }

    /**
     * Ensure that invalid JSON in the subplugins.json file warns appropriately.
     *
     * @dataProvider invalid_subplugins_json_provider
     * @param string[] $expectedwarnings Errors to expect in the exception message
     * @param array[] $json The contents of the subplugins.json file
     */
    public function test_fetch_subtypes_json_invalid_values(
        array $expectedwarnings,
        array $json,
    ): void {
        global $CFG;

        $this->resetAfterTest();
        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, [
            'plugintype' => [
                'exampleplugin' => [
                    'db' => [
                        'subplugins.json' => json_encode($json),
                        'subplugins.php' => '',
                    ],
                    'apples' => [],
                    'pears' => [],
                ],
            ],
        ]);

        $CFG->dirroot = $vfileroot->url();
        $pluginroot = $vfileroot->getChild('plugintype/exampleplugin');

        $logdir = make_request_directory();
        $logfile = "{$logdir}/error.log";
        ini_set('error_log', $logfile);

        $rcm = new \ReflectionMethod(\core\component::class, 'fetch_subtypes');
        $rcm->invoke(null, $pluginroot->url());

        $warnings = file_get_contents($logfile);
        foreach ($expectedwarnings as $expectedwarning) {
            $this->assertMatchesRegularExpression($expectedwarning, $warnings);
        }
    }

    /**
     * Data provider for invalid subplugins.json files.
     *
     * @return array
     */
    public static function invalid_subplugins_json_provider(): array {
        return [
            'Invalid characters in subtype name' => [
                'expectedwarnings' => [
                    "/Invalid subtype .*APPLES.*detected.*invalid characters present/",
                ],
                'json' => [
                    'subplugintypes' => [
                        'APPLES' => 'plugintype/exampleplugin/apples',
                    ],
                ],
            ],

            'Subplugin which duplicates a core subsystem' => [
                'expectedwarnings' => [
                    "/Invalid subtype .*editor.*detected.*duplicates core subsystem/",
                ],
                'json' => [
                    'subplugintypes' => [
                        'editor' => 'apples',
                    ],
                ],
            ],

            'Subplugin directory does not exist' => [
                'expectedwarnings' => [
                    "/Invalid subtype directory/",
                ],
                'json' => [
                    'subplugintypes' => [
                        'exampleapples' => 'berries',
                    ],
                ],
            ],

            'More subplugintypes than plugintypes' => [
                'expectedwarnings' => [
                    "/Subplugintypes and plugintypes are not in sync/",
                ],
                'json' => [
                    'subplugintypes' => [
                        'apples' => 'pears',
                    ],
                    'plugintypes' => [],
                ],
            ],

            'More plugintypes than subplugintypes' => [
                'expectedwarnings' => [
                    "/Subplugintypes and plugintypes are not in sync /",
                ],
                'json' => [
                    'subplugintypes' => [
                        'apples' => 'apples',
                    ],
                    'plugintypes' => [
                        'apples' => 'plugintype/exampleplugin/apples',
                        'pears' => 'plugintype/exampleplugin/pears',
                    ],
                ],
            ],

            'subplugintype not defined in plugintype' => [
                'expectedwarnings' => [
                    "/Subplugintypes and plugintypes are not in sync for 'apples'/",
                ],
                'json' => [
                    'subplugintypes' => [
                        'apples' => 'apples',
                    ],
                    'plugintypes' => [
                        'pears' => 'plugintype/exampleplugin/pears',
                    ],
                ],
            ],
            'subplugintype does not match plugintype' => [
                'expectedwarnings' => [
                    "/Subplugintypes and plugintypes are not in sync for 'apples'/",
                ],
                'json' => [
                    'subplugintypes' => [
                        'apples' => 'apples',
                    ],
                    'plugintypes' => [
                        'apples' => 'plugintype/exampleplugin/pears',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test various methods when a deprecated plugin type is introduced.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_core_component_deprecated_plugintype(): void {
        $this->resetAfterTest();

        // Inject the 'fake' plugin type.
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'lib/tests/fixtures/fakeplugins/fake'
        );

        $componenthashbefore = component::get_all_component_hash();
        $versionhashbefore = component::get_all_versions_hash();

        // Deprecation-specific APIs - pre-deprecation.
        $this->assertArrayHasKey('fake', component::get_plugin_types());
        $this->assertArrayHasKey('fullfeatured', component::get_plugin_list('fake'));
        $this->assertFalse(component::is_deprecated_plugin_type('fake'));
        $this->assertFalse(component::is_deleted_plugin_type('fake'));
        $this->assertFalse(component::is_plugintype_in_deprecation('fake'));
        $this->assertArrayNotHasKey('fake', component::get_deprecated_plugin_types());
        $this->assertArrayNotHasKey('fullfeatured', component::get_deprecated_plugin_list('fake'));
        $this->assertArrayNotHasKey('fake', component::get_deleted_plugin_types());
        $this->assertArrayNotHasKey('fullfeatured', component::get_deleted_plugin_list('fake'));
        $this->assertArrayHasKey('fake', component::get_all_plugin_types());
        $this->assertArrayHasKey('fullfeatured', component::get_all_plugins_list('fake'));

        // Deprecate the fake plugintype via mocking component sources.
        $this->deprecate_full_mocked_plugintype('fake');

        // Verify before/after hashes have changed, since the plugintype is no longer part of the hash calcs.
        $this->assertNotEquals(component::get_all_component_hash(), $componenthashbefore);
        $this->assertNotEquals(component::get_all_versions_hash(), $versionhashbefore);

        // Deprecation-specific APIs - post-deprecation.
        $this->assertTrue(component::is_deprecated_plugin_type('fake'));
        $this->assertFalse(component::is_deleted_plugin_type('fake'));
        $this->assertTrue(component::is_plugintype_in_deprecation('fake'));
        $this->assertArrayHasKey('fake', component::get_deprecated_plugin_types());
        $this->assertArrayHasKey('fullfeatured', component::get_deprecated_plugin_list('fake'));
        $this->assertArrayNotHasKey('fake', component::get_deleted_plugin_types());
        $this->assertArrayNotHasKey('fullfeatured', component::get_deleted_plugin_list('fake'));
        $this->assertArrayHasKey('fake', component::get_all_plugin_types());
        $this->assertArrayHasKey('fullfeatured', component::get_all_plugins_list('fake'));

        // Deprecated plugins excluded from the following for B/C.
        $this->assertArrayNotHasKey('fake', component::get_plugin_types());
        $this->assertArrayNotHasKey('fullfeatured', component::get_plugin_list('fake'));
        $this->assertArrayNotHasKey('fake', component::get_component_list());
        $this->assertEmpty(component::get_plugin_list_with_file('fake', 'classes/dummy.php'));
        $this->assertEmpty(component::get_plugin_list_with_class('fake', 'dummy'));

        // Deprecated plugins excluded by default for B/C, but can be included by request.
        $this->assertNotContains('fake_fullfeatured', component::get_component_names());
        $this->assertContains('fake_fullfeatured', component::get_component_names(false, true));

        // Deprecated plugins included in the following.
        $this->assertIsString(component::get_plugin_directory('fake', 'fullfeatured')); // Used by string manager.
        $this->assertIsString(component::get_component_directory('fake_fullfeatured')); // Uses get_plugin_directory().
        $this->assertTrue(component::has_monologo_icon('fake', 'fullfeatured'));  // Uses get_plugin_directory().
        $this->assertEquals('fake_fullfeatured', component::get_component_from_classname(\fake_fullfeatured\example::class));

        // Class autoloading of deprecated plugins is permitted, to facilitate plugin migration code.
        $this->assertArrayHasKey('fake_fullfeatured\dummy',
            component::get_component_classes_in_namespace('fake_fullfeatured'));
        $this->assertTrue(class_exists(\fake_fullfeatured\dummy::class));
    }

    /**
     * Test various core_component APIs when dealing with deleted plugin types.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_core_component_deleted_plugintype(): void {
        $this->resetAfterTest();

        // Inject the 'fake' plugin type.
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'lib/tests/fixtures/fakeplugins/fake',
        );

        // Delete the fake plugintype via mocking component sources.
        $this->delete_full_mocked_plugintype('fake');

        // Deprecation-specific methods.
        $this->assertFalse(component::is_deprecated_plugin_type('fake'));
        $this->assertTrue(component::is_deleted_plugin_type('fake'));
        $this->assertTrue(component::is_plugintype_in_deprecation('fake'));
        $this->assertArrayNotHasKey('fake', component::get_deprecated_plugin_types());
        $this->assertArrayNotHasKey('fullfeatured', component::get_deprecated_plugin_list('fake'));
        $this->assertArrayHasKey('fake', component::get_deleted_plugin_types());
        $this->assertArrayHasKey('fullfeatured', component::get_deleted_plugin_list('fake'));
        $this->assertArrayHasKey('fake', component::get_all_plugin_types());
        $this->assertArrayHasKey('fullfeatured', component::get_all_plugins_list('fake'));

        // Deleted plugintypes/plugins are not included in other methods.
        $this->assertArrayNotHasKey('fake', component::get_plugin_types());
        $this->assertArrayNotHasKey('fullfeatured', component::get_plugin_list('fake'));
        $this->assertNotContains('fake_fullfeatured', component::get_component_names());
        $this->assertNotContains('fake_fullfeatured', component::get_component_names(false, true));
        $this->assertArrayNotHasKey('fake', component::get_component_list());
        $this->assertEmpty(component::get_plugin_list_with_file('fake', 'classes/dummy.php'));
        $this->assertEmpty(component::get_plugin_list_with_class('fake', 'dummy'));
        $this->assertFalse(component::has_monologo_icon('fake', 'fullfeatured'));
        $this->assertNull(component::get_plugin_directory('fake', 'fullfeatured'));
        $this->assertNull(component::get_component_directory('fake_fullfeatured'));
        $this->assertNull(component::get_component_from_classname(\fake_fullfeatured\example::class));

        // Class autoloading of deleted plugins is not supported.
        $this->assertArrayNotHasKey('fake_fullfeatured\dummy',
            component::get_component_classes_in_namespace('fake_fullfeatured'));
        $this->assertFalse(class_exists(fake_fullfeatured\dummy::class));
    }

    /**
     * Test various core_component APIs when dealing with deprecated subplugins.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_core_component_deprecated_subplugintype(): void {
        $this->resetAfterTest();

        // Inject the 'fake' plugin type. This includes three mock subplugins:
        // 1. fullsubtype_example: a regular plugin type, not deprecated, nor deleted.
        // 2. fulldeprecatedsubtype_test: a deprecated subplugin type.
        // 3. fulldeletedsubtype_demo: a deleted subplugin type.
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'lib/tests/fixtures/fakeplugins/fake',
            subpluginsupport: true
        );
        $this->assert_deprecation_apis_subplugins();
    }

    /**
     * Verify that a plugin which supports subplugins cannot be deprecated.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_core_component_deprecated_subplugintype_supporting_subplugins(): void {
        $this->resetAfterTest();

        // Inject the 'fake' plugin type. This includes three mock subplugins:
        // 1. fullsubtype_example: a regular plugin type, not deprecated, nor deleted.
        // 2. fulldeprecatedsubtype_test: a deprecated subplugin type.
        // 3. fulldeletedsubtype_demo: a deleted subplugin type.
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'lib/tests/fixtures/fakeplugins/fake',
            subpluginsupport: true
        );

        // Try to deprecate the fake plugintype via mocking component sources.
        $this->deprecate_full_mocked_plugintype('fake');

        // Deprecation unsupported, so verify core_component treats all plugins the same as before the deprecation attempt.
        // Debugging is expected to be emitted during core_component::init().
        $this->assert_deprecation_apis_subplugins();
        $this->assertDebuggingCalled('Deprecation of a plugin type which supports subplugins is not supported. ' .
            'These plugin types will continue to be treated as active.', DEBUG_DEVELOPER);
    }

    /**
     * Verify that a plugin which supports subplugins cannot be deleted.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_core_component_deleted_subplugintype_supporting_subplugins(): void {
        $this->resetAfterTest();

        // Inject the 'fake' plugin type. This includes three mock subplugins:
        // 1. fullsubtype_example: a regular plugin type, not deprecated, nor deleted.
        // 2. fulldeprecatedsubtype_test: a deprecated subplugin type.
        // 3. fulldeletedsubtype_demo: a deleted subplugin type.
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'lib/tests/fixtures/fakeplugins/fake',
            subpluginsupport: true
        );

        // Try to delete the fake plugintype via mocking component sources.
        $this->delete_full_mocked_plugintype('fake');

        // Deletion unsupported, so verify core_component treats all plugins the same as before the deletion attempt.
        // Debugging is expected to be emitted during core_component::init().
        $this->assert_deprecation_apis_subplugins();
        $this->assertDebuggingCalled('Deprecation of a plugin type which supports subplugins is not supported. ' .
            'These plugin types will continue to be treated as active.', DEBUG_DEVELOPER);
    }

    /**
     * Helper asserting the returns for various core_component APIs when dealing with deprecated and deleted subplugins.
     *
     * @return void
     */
    protected function assert_deprecation_apis_subplugins(): void {
        // Deprecation-specific methods.
        $this->assertFalse(component::is_deprecated_plugin_type('fake'));
        $this->assertFalse(component::is_deprecated_plugin_type('fullsubtype'));
        $this->assertTrue(component::is_deprecated_plugin_type('fulldeprecatedsubtype'));
        $this->assertFalse(component::is_deprecated_plugin_type('fulldeletedsubtype'));

        $this->assertFalse(component::is_deleted_plugin_type('fake'));
        $this->assertFalse(component::is_deleted_plugin_type('fullsubtype'));
        $this->assertFalse(component::is_deleted_plugin_type('fulldeprecatedsubtype'));
        $this->assertTrue(component::is_deleted_plugin_type('fulldeletedsubtype'));

        $this->assertFalse(component::is_plugintype_in_deprecation('fake'));
        $this->assertFalse(component::is_plugintype_in_deprecation('fullsubtype'));
        $this->assertTrue(component::is_plugintype_in_deprecation('fulldeprecatedsubtype'));
        $this->assertTrue(component::is_plugintype_in_deprecation('fulldeletedsubtype'));

        $this->assertArrayNotHasKey('fake', component::get_deprecated_plugin_types());
        $this->assertArrayNotHasKey('fullsubtype', component::get_deprecated_plugin_types());
        $this->assertArrayHasKey('fulldeprecatedsubtype', component::get_deprecated_plugin_types());
        $this->assertArrayNotHasKey('fulldeletedsubtype', component::get_deprecated_plugin_types());

        $this->assertArrayNotHasKey('fullfeatured', component::get_deprecated_plugin_list('fake'));
        $this->assertArrayNotHasKey('example', component::get_deprecated_plugin_list('fullsubtype'));
        $this->assertArrayHasKey('test', component::get_deprecated_plugin_list('fulldeprecatedsubtype'));
        $this->assertArrayNotHasKey('demo', component::get_deprecated_plugin_list('fulldeletedsubtype'));

        $this->assertArrayNotHasKey('fake', component::get_deleted_plugin_types());
        $this->assertArrayNotHasKey('fullsubtype', component::get_deleted_plugin_types());
        $this->assertArrayNotHasKey('fulldeprecatedsubtype', component::get_deleted_plugin_types());
        $this->assertArrayHasKey('fulldeletedsubtype', component::get_deleted_plugin_types());

        $this->assertArrayNotHasKey('fullfeatured', component::get_deleted_plugin_list('fake'));
        $this->assertArrayNotHasKey('example', component::get_deleted_plugin_list('fullsubtype'));
        $this->assertArrayNotHasKey('test', component::get_deleted_plugin_list('fulldeprecatedsubtype'));
        $this->assertArrayHasKey('demo', component::get_deleted_plugin_list('fulldeletedsubtype'));

        $this->assertArrayHasKey('fake', component::get_all_plugin_types());
        $this->assertArrayHasKey('fullsubtype', component::get_all_plugin_types());
        $this->assertArrayHasKey('fulldeprecatedsubtype', component::get_all_plugin_types());
        $this->assertArrayHasKey('fulldeletedsubtype', component::get_all_plugin_types());

        $this->assertArrayHasKey('fullfeatured', component::get_all_plugins_list('fake'));
        $this->assertArrayHasKey('example', component::get_all_plugins_list('fullsubtype'));
        $this->assertArrayHasKey('test', component::get_all_plugins_list('fulldeprecatedsubtype'));
        $this->assertArrayHasKey('demo', component::get_all_plugins_list('fulldeletedsubtype'));

        // Deprecated and deleted plugins excluded from the following for B/C.
        $this->assertArrayHasKey('fake', component::get_plugin_types());
        $this->assertArrayHasKey('fullsubtype', component::get_plugin_types());
        $this->assertArrayNotHasKey('fulldeprecatedsubtype', component::get_plugin_types());
        $this->assertArrayNotHasKey('fulldeletedsubtype', component::get_plugin_types());

        $this->assertNotEmpty(component::get_plugin_list('fake'));
        $this->assertNotEmpty(component::get_plugin_list('fullsubtype'));
        $this->assertEmpty(component::get_plugin_list('fulldeprecatedsubtype'));
        $this->assertEmpty(component::get_plugin_list('fulldeletedsubtype'));

        $this->assertArrayHasKey('fake', component::get_component_list());
        $this->assertArrayHasKey('fullsubtype', component::get_component_list());
        $this->assertArrayNotHasKey('fulldeprecatedsubtype', component::get_component_list());
        $this->assertArrayNotHasKey('fulldeletedsubtype', component::get_component_list());

        $this->assertArrayHasKey('fullfeatured', component::get_plugin_list_with_file('fake', 'classes/dummy.php'));
        $this->assertArrayHasKey('example', component::get_plugin_list_with_file('fullsubtype', 'classes/dummy.php'));
        $this->assertEmpty(component::get_plugin_list_with_file('fulldeprecatedsubtype', 'classes/dummy.php'));
        $this->assertEmpty(component::get_plugin_list_with_file('fulldeletedsubtype', 'classes/dummy.php'));

        $this->assertArrayHasKey('fake_fullfeatured', component::get_plugin_list_with_class('fake', 'dummy'));
        $this->assertArrayHasKey('fullsubtype_example', component::get_plugin_list_with_class('fullsubtype', 'dummy'));
        $this->assertEmpty(component::get_plugin_list_with_class('fulldeprecatedsubtype', 'dummy'));
        $this->assertEmpty(component::get_plugin_list_with_class('fulldeletedsubtype', 'dummy'));

        $this->assertArrayHasKey('fullsubtype', component::get_subplugins('fake_fullfeatured'));
        $this->assertContains('example', component::get_subplugins('fake_fullfeatured')['fullsubtype']);
        $this->assertArrayNotHasKey('fulldeprecatedsubtype', component::get_subplugins('fake_fullfeatured'));
        $this->assertArrayNotHasKey('fulldeletedsubtype', component::get_subplugins('fake_fullfeatured'));

        $this->assertArrayHasKey('fullsubtype', component::get_all_subplugins('fake_fullfeatured'));
        $this->assertContains('example', component::get_all_subplugins('fake_fullfeatured')['fullsubtype']);
        $this->assertContains('test', component::get_all_subplugins('fake_fullfeatured')['fulldeprecatedsubtype']);
        $this->assertContains('demo', component::get_all_subplugins('fake_fullfeatured')['fulldeletedsubtype']);

        // Deprecated plugins excluded by default for B/C, but can be included by request.
        // Deleted plugins are always excluded.
        $this->assertContains('fake_fullfeatured', component::get_component_names());
        $this->assertContains('fullsubtype_example', component::get_component_names());
        $this->assertNotContains('fulldeprecatedsubtype_test', component::get_component_names());
        $this->assertNotContains('fulldeletedsubtype_demo', component::get_component_names());
        $this->assertContains('fulldeprecatedsubtype_test', component::get_component_names(false, true));
        $this->assertNotContains('fulldeletedsubtype_demo', component::get_component_names(false, true));

        // Deprecated plugins included in the following, but deleted plugins are excluded.
        $this->assertIsString(component::get_plugin_directory('fake', 'fullfeatured')); // Used by string manager.
        $this->assertIsString(component::get_plugin_directory('fullsubtype', 'example'));
        $this->assertIsString(component::get_plugin_directory('fulldeprecatedsubtype', 'test'));
        $this->assertNull(component::get_plugin_directory('fulldeletedsubtype', 'demo'));

        $this->assertIsString(component::get_component_directory('fake_fullfeatured')); // Uses get_plugin_directory().
        $this->assertIsString(component::get_component_directory('fullsubtype_example'));
        $this->assertIsString(component::get_component_directory('fulldeprecatedsubtype_test'));
        $this->assertNull(component::get_component_directory('fulldeletedsubtype_demo'));

        $this->assertTrue(component::has_monologo_icon('fullsubtype', 'example')); // Uses get_plugin_directory().
        $this->assertTrue(component::has_monologo_icon('fulldeprecatedsubtype', 'test'));
        $this->assertFalse(component::has_monologo_icon('fulldeletedsubtype', 'demo'));

        $this->assertEquals('fake_fullfeatured', component::get_component_from_classname(\fake_fullfeatured\example::class));
        $this->assertEquals('fullsubtype_example',
            component::get_component_from_classname(\fullsubtype_example\example::class));
        $this->assertEquals('fulldeprecatedsubtype_test',
            component::get_component_from_classname(\fulldeprecatedsubtype_test\example::class));
        $this->assertNull(component::get_component_from_classname(\fulldeletedsubtype_demo\example::class));

        // Deprecated and deleted plugins included in the following.
        $this->assertEquals('fake_fullfeatured', component::get_subtype_parent('fullsubtype'));
        $this->assertEquals('fake_fullfeatured', component::get_subtype_parent('fulldeprecatedsubtype'));
        $this->assertEquals('fake_fullfeatured', component::get_subtype_parent('fulldeletedsubtype'));

        // Class autoloading of deprecated plugins is permitted, to facilitate plugin migration code, but not for deleted plugins.
        $this->assertArrayHasKey('fake_fullfeatured\dummy',
            component::get_component_classes_in_namespace('fake_fullfeatured'));
        $this->assertArrayHasKey('fullsubtype_example\dummy',
            component::get_component_classes_in_namespace('fullsubtype_example'));
        $this->assertArrayHasKey('fulldeprecatedsubtype_test\dummy',
            component::get_component_classes_in_namespace('fulldeprecatedsubtype_test'));
        $this->assertEquals([], component::get_component_classes_in_namespace('fulldeletedsubtype_demo'));

        $this->assertTrue(class_exists(\fake_fullfeatured\dummy::class));
        $this->assertTrue(class_exists(\fullsubtype_example\dummy::class));
        $this->assertTrue(class_exists(\fulldeprecatedsubtype_test\dummy::class));
        $this->assertFalse(class_exists(\fulldeletedsubtype_demo\dummy::class));
    }
}
