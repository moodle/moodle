<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\hook;

use core\di;
use core\tests\fake_plugins_test_trait;

/**
 * Hooks tests.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\hook\manager
 */
final class manager_test extends \advanced_testcase {

    use fake_plugins_test_trait;

    /**
     * Test public factory method to get hook manager.
     */
    public function test_get_instance(): void {
        $manager = manager::get_instance();
        $this->assertInstanceOf(manager::class, $manager);

        $this->assertSame($manager, manager::get_instance());
    }

    /**
     * Test getting of manager test instance.
     */
    public function test_phpunit_get_instance(): void {
        $testmanager = manager::phpunit_get_instance([]);
        $this->assertSame([], $testmanager->get_hooks_with_callbacks());

        // We get a new instance every time.
        $this->assertNotSame($testmanager, manager::phpunit_get_instance([]));

        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_valid.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame(['test_plugin\\hook\\hook'], $testmanager->get_hooks_with_callbacks());
    }

    /**
     * Test loading and parsing of callbacks from files.
     */
    public function test_callbacks(): void {
        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_valid.php',
            'test_plugin2' => __DIR__ . '/../fixtures/hook/hooks2_valid.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame(['test_plugin\\hook\\hook'], $testmanager->get_hooks_with_callbacks());
        $callbacks = $testmanager->get_callbacks_for_hook('test_plugin\\hook\\hook');
        $this->assertCount(2, $callbacks);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test2',
            'component' => 'test_plugin2',
            'disabled' => false,
            'priority' => 200,
        ], $callbacks[0]);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test1',
            'component' => 'test_plugin1',
            'disabled' => false,
            'priority' => 100,
        ], $callbacks[1]);

        $this->assertDebuggingNotCalled();
        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_broken.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame([], $testmanager->get_hooks_with_callbacks());
        $debuggings = $this->getDebuggingMessages();
        $this->resetDebugging();
        $this->assertSame(
            'Hook callback definition requires \'hook\' name in \'test_plugin1\'',
            $debuggings[0]->message
        );
        $this->assertSame(
            'Hook callback definition requires \'callback\' callable in \'test_plugin1\'',
            $debuggings[1]->message
        );
        $this->assertSame(
            'Hook callback definition contains invalid \'callback\' static class method string in \'test_plugin1\'',
            $debuggings[2]->message
        );
        $this->assertCount(3, $debuggings);
    }

    /**
     * Test hook dispatching, that is callback execution.
     */
    public function test_dispatch(): void {
        require_once(__DIR__ . '/../fixtures/hook/hook.php');
        require_once(__DIR__ . '/../fixtures/hook/callbacks.php');

        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_valid.php',
            'test_plugin2' => __DIR__ . '/../fixtures/hook/hooks2_valid.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        \test_plugin\callbacks::$calls = [];
        $hook = new \test_plugin\hook\hook();
        $result = $testmanager->dispatch($hook);
        $this->assertSame($hook, $result);
        $this->assertSame(['test2', 'test1'], \test_plugin\callbacks::$calls);
        \test_plugin\callbacks::$calls = [];
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test hook dispatching, that is callback execution.
     */
    public function test_dispatch_with_exception(): void {
        require_once(__DIR__ . '/../fixtures/hook/hook.php');
        require_once(__DIR__ . '/../fixtures/hook/callbacks.php');

        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_exception.php',
            'test_plugin2' => __DIR__ . '/../fixtures/hook/hooks2_valid.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);

        $hook = new \test_plugin\hook\hook();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('grrr');

        $testmanager->dispatch($hook);
    }

    /**
     * Test hook dispatching, that is callback execution.
     */
    public function test_dispatch_with_invalid(): void {
        require_once(__DIR__ . '/../fixtures/hook/hook.php');
        require_once(__DIR__ . '/../fixtures/hook/callbacks.php');

        // Missing callbacks is ignored.
        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_missing.php',
            'test_plugin2' => __DIR__ . '/../fixtures/hook/hooks2_valid.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        \test_plugin\callbacks::$calls = [];

        $hook = new \test_plugin\hook\hook();

        $testmanager->dispatch($hook);
        $this->assertDebuggingCalled(
            "Hook callback definition contains invalid 'callback' method name in 'test_plugin1'. Callback method not found.",
        );
        $this->assertSame(['test2'], \test_plugin\callbacks::$calls);
    }

    /**
     * Test stoppping of hook dispatching.
     */
    public function test_dispatch_stoppable(): void {
        require_once(__DIR__ . '/../fixtures/hook/stoppablehook.php');
        require_once(__DIR__ . '/../fixtures/hook/callbacks.php');

        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_stoppable.php',
            'test_plugin2' => __DIR__ . '/../fixtures/hook/hooks2_stoppable.php',
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        \test_plugin\callbacks::$calls = [];
        $hook = new \test_plugin\hook\stoppablehook();
        $result = $testmanager->dispatch($hook);
        $this->assertSame($hook, $result);
        $this->assertSame(['stop1'], \test_plugin\callbacks::$calls);
        \test_plugin\callbacks::$calls = [];
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests callbacks can be overridden via CFG settings.
     */
    public function test_callback_overriding(): void {
        global $CFG;
        $this->resetAfterTest();

        $componentfiles = [
            'test_plugin1' => __DIR__ . '/../fixtures/hook/hooks1_valid.php',
            'test_plugin2' => __DIR__ . '/../fixtures/hook/hooks2_valid.php',
        ];

        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame(['test_plugin\\hook\\hook'], $testmanager->get_hooks_with_callbacks());
        $callbacks = $testmanager->get_callbacks_for_hook('test_plugin\\hook\\hook');
        $this->assertCount(2, $callbacks);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test2',
            'component' => 'test_plugin2',
            'disabled' => false,
            'priority' => 200,
        ], $callbacks[0]);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test1',
            'component' => 'test_plugin1',
            'disabled' => false,
            'priority' => 100,
        ], $callbacks[1]);

        $CFG->hooks_callback_overrides = [
            'test_plugin\\hook\\hook' => [
                'test_plugin\\callbacks::test2' => ['priority' => 33],
            ],
        ];

        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame(['test_plugin\\hook\\hook'], $testmanager->get_hooks_with_callbacks());
        $callbacks = $testmanager->get_callbacks_for_hook('test_plugin\\hook\\hook');
        $this->assertCount(2, $callbacks);
        $this->normalise_callbacks($callbacks);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test1',
            'component' => 'test_plugin1',
            'disabled' => false,
            'priority' => 100,
        ], $callbacks[0]);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test2',
            'component' => 'test_plugin2',
            'defaultpriority' => 200,
            'disabled' => false,
            'priority' => 33,
        ], $callbacks[1]);

        $CFG->hooks_callback_overrides = [
            'test_plugin\\hook\\hook' => [
                'test_plugin\\callbacks::test2' => ['priority' => 33, 'disabled' => true],
            ],
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame(['test_plugin\\hook\\hook'], $testmanager->get_hooks_with_callbacks());
        $callbacks = $testmanager->get_callbacks_for_hook('test_plugin\\hook\\hook');
        $this->assertCount(2, $callbacks);
        $this->normalise_callbacks($callbacks);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test1',
            'component' => 'test_plugin1',
            'disabled' => false,
            'priority' => 100,
        ], $callbacks[0]);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test2',
            'component' => 'test_plugin2',
            'defaultpriority' => 200,
            'disabled' => true,
            'priority' => 33,
        ], $callbacks[1]);

        $CFG->hooks_callback_overrides = [
            'test_plugin\\hook\\hook' => [
                'test_plugin\\callbacks::test2' => ['disabled' => true],
            ],
        ];
        $testmanager = manager::phpunit_get_instance($componentfiles);
        $this->assertSame(['test_plugin\\hook\\hook'], $testmanager->get_hooks_with_callbacks());
        $callbacks = $testmanager->get_callbacks_for_hook('test_plugin\\hook\\hook');
        $this->assertCount(2, $callbacks);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test2',
            'component' => 'test_plugin2',
            'disabled' => true,
            'priority' => 200,
        ], $callbacks[0]);
        $this->assertSame([
            'callback' => 'test_plugin\\callbacks::test1',
            'component' => 'test_plugin1',
            'disabled' => false,
            'priority' => 100,
        ], $callbacks[1]);

        require_once(__DIR__ . '/../fixtures/hook/hook.php');
        require_once(__DIR__ . '/../fixtures/hook/callbacks.php');

        \test_plugin\callbacks::$calls = [];
        $hook = new \test_plugin\hook\hook();
        $result = $testmanager->dispatch($hook);
        $this->assertSame($hook, $result);
        $this->assertSame(['test1'], \test_plugin\callbacks::$calls);
        \test_plugin\callbacks::$calls = [];
        $this->assertDebuggingNotCalled();
        $CFG->hooks_callback_overrides = [];
    }

    /**
     * Register a fake plugin called hooktest in the component manager.
     *
     * Tests consuming this helpers must run in a separate process.
     */
    protected function setup_hooktest_plugin(): void {
        global $CFG;

        $this->add_mocked_plugintype('fake', "{$CFG->dirroot}/lib/tests/fixtures/hook/fakeplugins");
        $this->add_mocked_plugin('fake', 'hooktest', "{$CFG->dirroot}/lib/tests/fixtures/hook/fakeplugins/hooktest");
    }

    /**
     * Call a plugin callback that has been replaced by a hook, but has no hook callback.
     *
     * The original callback should be called, but a debugging message should be output.
     *
     * @runInSeparateProcess
     */
    public function test_migrated_callback(): void {
        $this->resetAfterTest(true);
        // Include plugin hook discovery agent, and the hook that replaces the callback.
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hooks.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hook/hook_replacing_callback.php');
        // Register the fake plugin with the component manager.
        $this->setup_hooktest_plugin();

        // Register the fake plugin with the hook manager, but don't define any hook callbacks.
        di::set(
            manager::class,
            manager::phpunit_get_instance(
                [
                    'fake_hooktest' => __DIR__ . '/../fixtures/hook/fakeplugins/hooktest/db/hooks_nocallbacks.php',
                ],
            ),
        );

        // Confirm a non-deprecated callback is called as expected.
        $this->assertEquals('Called current callback', component_callback('fake_hooktest', 'current_callback'));

        // Confirm the deprecated callback is called as expected.
        $this->assertEquals(
            'Called deprecated callback',
            component_callback('fake_hooktest', 'old_callback', [], null, true)
        );
        $this->assertDebuggingNotCalled();

        // Forcefully modify the PHPUnit flag on the manager to ensure the debugging message is output.
        $manager = di::get(manager::class);
        $rp = new \ReflectionProperty($manager, 'phpunit');
        $rp->setValue($manager, false);

        component_callback('fake_hooktest', 'old_callback', [], null, true);

        $this->assertDebuggingCalled(
            'Callback old_callback in fake_hooktest component should be migrated to new hook ' .
                'callback for fake_hooktest\hook\hook_replacing_callback',
        );
    }

    /**
     * Call a plugin callback that has been replaced by a hook, and has a hook callback.
     *
     * The original callback should not be called, and no debugging should be output.
     *
     * @runInSeparateProcess
     */
    public function test_migrated_callback_with_replacement(): void {
        $this->resetAfterTest(true);
        // Include plugin hook discovery agent, and the hook that replaces the callback, and a hook callback for the hook.
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hooks.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hook/hook_replacing_callback.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hook_callbacks.php');
        // Register the fake plugin with the component manager.
        $this->setup_hooktest_plugin();

        // Register the fake plugin with the hook manager, including the hook callback.
        di::set(
            manager::class,
            manager::phpunit_get_instance([
                'fake_hooktest' => __DIR__ . '/../fixtures/hook/fakeplugins/hooktest/db/hooks.php',
            ]),
        );

        // Confirm a non-deprecated callback is called as expected.
        $this->assertEquals('Called current callback', component_callback('fake_hooktest', 'current_callback'));

        // Confirm the deprecated callback is not called, as expected.
        $this->assertNull(component_callback('fake_hooktest', 'old_callback', [], null, true));
        $this->assertDebuggingNotCalled();
    }

    /**
     * Call a plugin class callback that has been replaced by a hook, but has no hook callback.
     *
     * The original class callback should be called, but a debugging message should be output.
     *
     * @runInSeparateProcess
     */
    public function test_migrated_class_callback(): void {
        $this->resetAfterTest(true);
        // Include plugin hook discovery agent, the class containing callbacks, and the hook that replaces the class callback.
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/callbacks.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hooks.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hook/hook_replacing_class_callback.php');
        // Register the fake plugin with the component manager.
        $this->setup_hooktest_plugin();

        // Register the fake plugin with the hook manager, but don't define any hook callbacks.
        di::set(
            manager::class,
            manager::phpunit_get_instance([
                'fake_hooktest' => __DIR__ . '/../fixtures/hook/fakeplugins/hooktest/db/hooks_nocallbacks.php',
            ]),
        );

        // Confirm a non-deprecated class callback is called as expected.
        $this->assertEquals(
            'Called current class callback',
            component_class_callback('fake_hooktest\callbacks', 'current_class_callback', [])
        );

        // Confirm the deprecated class callback is called as expected.
        $this->assertEquals(
            'Called deprecated class callback',
            component_class_callback('fake_hooktest\callbacks', 'old_class_callback', [], null, true)
        );
        $this->assertDebuggingNotCalled();

        // Forcefully modify the PHPUnit flag on the manager to ensure the debugging message is output.
        $manager = di::get(manager::class);
        $rp = new \ReflectionProperty($manager, 'phpunit');
        $rp->setValue($manager, false);

        component_class_callback('fake_hooktest\callbacks', 'old_class_callback', [], null, true);
        $this->assertDebuggingCalled(
            'Callback callbacks::old_class_callback in fake_hooktest component should be migrated to new hook ' .
                'callback for fake_hooktest\hook\hook_replacing_class_callback',
        );
    }

    /**
     * Call a plugin class callback that has been replaced by a hook, and has a hook callback.
     *
     * The original callback should not be called, and no debugging should be output.
     *
     * @runInSeparateProcess
     */
    public function test_migrated_class_callback_with_replacement(): void {
        $this->resetAfterTest(true);
        // Include plugin hook discovery agent, the class containing callbacks, the hook that replaces the class callback,
        // and a hook callback for the new hook.
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/callbacks.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hooks.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hook/hook_replacing_class_callback.php');
        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/hooktest/classes/hook_callbacks.php');
        // Register the fake plugin with the component manager.
        $this->setup_hooktest_plugin();

        // Register the fake plugin with the hook manager, including the hook callback.
        di::set(
            manager::class,
            manager::phpunit_get_instance([
                'fake_hooktest' => __DIR__ . '/../fixtures/hook/fakeplugins/hooktest/db/hooks.php',
            ]),
        );

        // Confirm a non-deprecated class callback is called as expected.
        $this->assertEquals(
            'Called current class callback',
            component_class_callback('fake_hooktest\callbacks', 'current_class_callback', [])
        );

        // Confirm the deprecated class callback is not called, as expected.
        $this->assertNull(component_class_callback('fake_hooktest\callbacks', 'old_class_callback', [], null, true));
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test verifying that callbacks for deprecated plugins are not returned and hook dispatching won't call into these plugins.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_get_callbacks_for_hook_deprecated_plugintype(): void {
        $this->resetAfterTest();

        // Inject the fixture 'fake' plugin type into component sources, which includes a single 'fake_fullfeatured' plugin.
        // This 'fake_fullfeatured' plugin is an available plugin at this stage (not yet deprecated).
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );

        // Force reset the static instance cache \core\hook\manager::$instance so that a fresh instance is instantiated, ensuring
        // the component lists are re-run and the hook manager can see the injected mock plugin and it's callbacks.
        // Note: we can't use \core\hook\manager::phpunit_get_instance() because that doesn't load in component callbacks from disk.
        $hookmanrc = new \ReflectionClass(\core\hook\manager::class);
        $hookmanrc->setStaticPropertyValue('instance', null);
        $manager = \core\hook\manager::get_instance();

        // Get all registered callbacks for the hook listened to by the mock plugin (after_course_created).
        $listeners = $manager->get_callbacks_for_hook(\core_course\hook\after_course_created::class);
        $componentswithcallbacks = array_column($listeners, 'component');

        // Verify the available mock plugin is returned as a listener.
        $this->assertContains('fake_fullfeatured', $componentswithcallbacks);

        // Deprecate the 'fake' plugin type.
        $this->deprecate_full_mocked_plugintype('fake');

        // Force a fresh plugin manager instance, again to ensure the up-to-date component lists are used.
        $hookmanrc->setStaticPropertyValue('instance', null);
        $manager = \core\hook\manager::get_instance();

        // And verify the plugin is now not returned as a listener, since it's deprecated.
        $listeners = $manager->get_callbacks_for_hook(\core_course\hook\after_course_created::class);
        $componentswithcallbacks = array_column($listeners, 'component');
        $this->assertNotContains('fake_fullfeatured', $componentswithcallbacks);
    }

    /**
     * Normalise the sort order of callbacks to help with asserts.
     *
     * @param array $callbacks
     */
    private function normalise_callbacks(array &$callbacks): void {
        foreach ($callbacks as &$callback) {
            ksort($callback);
        }
    }
}
