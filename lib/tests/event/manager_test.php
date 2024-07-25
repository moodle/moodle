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

namespace core\event;

use core\tests\fake_plugins_test_trait;

/**
 * Tests for the \core\event\manager class.
 *
 * @package   core
 * @category  test
 * @copyright 2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\event\manager
 */
final class manager_test extends \advanced_testcase {

    use fake_plugins_test_trait;

    /**
     * Test verifying that observers are not returned for deprecated plugin types.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function test_get_all_observers_deprecated_plugintype(): void {
        $this->resetAfterTest();

        // Inject the fixture 'fake' plugin type into component sources, which includes a single 'fake_fullfeatured' plugin.
        // This 'fake_fullfeatured' plugin is an available plugin at this stage (not yet deprecated).
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'lib/tests/fixtures/fakeplugins/fake',
        );

        $observers = array_filter(
            \core\event\manager::get_all_observers()['\core\event\course_created'],
            fn($observer) => $observer->plugintype == 'fake'
        );
        $this->assertEquals('\fake_fullfeatured\event_listener::event_handler', $observers[0]->callable);

        // Now, deprecate the plugin type, verifying the event observer isn't visible.
        // Note: \core\event\manager::$allobservers static cache must be reset first, to force a reload.

        $this->deprecate_full_mocked_plugintype('fake');
        $eventmanrc = new \ReflectionClass(\core\event\manager::class);
        $eventmanrc->setStaticPropertyValue('allobservers', null);

        $observers = array_filter(
            \core\event\manager::get_all_observers()['\core\event\course_created'],
            fn($observer) => $observer->plugintype == 'fake'
        );
        $this->assertEmpty($observers);
    }
}
