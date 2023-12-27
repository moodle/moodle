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

/**
 * Hooks tests.
 *
 * @coversDefaultClass \core\hook\manager
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_test extends \advanced_testcase {
    /**
     * Test public factory method to get hook manager.
     * @covers ::get_instance
     */
    public function test_get_instance() {
        $manager = manager::get_instance();
        $this->assertInstanceOf(manager::class, $manager);

        $this->assertSame($manager, manager::get_instance());
    }

    /**
     * Test getting of manager test instance.
     * @covers ::phpunit_get_instance
     */
    public function test_phpunit_get_instance() {
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
     *
     * @covers ::get_callbacks_for_hook
     * @covers ::get_hooks_with_callbacks
     * @covers ::load_callbacks
     * @covers ::add_component_callbacks
     */
    public function test_callbacks() {
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
        $this->assertSame('Hook callback definition requires \'hook\' name in \'test_plugin1\'',
            $debuggings[0]->message);
        $this->assertSame('Hook callback definition requires \'callback\' callable in \'test_plugin1\'',
            $debuggings[1]->message);
        $this->assertSame('Hook callback definition contains invalid \'callback\' static class method string in \'test_plugin1\'',
            $debuggings[2]->message);
        $this->assertCount(3, $debuggings);
    }

    /**
     * Test hook dispatching, that is callback execution.
     * @covers ::dispatch
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
     * @covers ::dispatch
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
     * @covers ::dispatch
     */
    public function test_dispatch_with_invalid(): void {
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
     * @covers ::dispatch
     */
    public function test_dispatch_stoppable() {
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
     * @covers ::load_callbacks
     * @covers ::dispatch
     */
    public function test_callback_overriding() {
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
                'test_plugin\\callbacks::test2' => ['priority' => 33]
            ]
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
                'test_plugin\\callbacks::test2' => ['priority' => 33, 'disabled' => true]
            ]
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
        ],
        $callbacks[0]);
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
            ]
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
    }

    /**
     * Normalise the sort order of callbacks to help with asserts.
     *
     * @param array $callbacks
     * @return void
     */
    private function normalise_callbacks(array &$callbacks): void {
        foreach ($callbacks as &$callback) {
            ksort($callback);
        }
    }
}
