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

namespace core\hook\navigation;

/**
 * Test hook for primary navigation.
 *
 * @coversDefaultClass \core\hook\navigation\primary_extend
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2023 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_extend_test extends \advanced_testcase {
    /**
     * Test stoppable_trait.
     * @covers ::stop_propagation
     */
    public function test_stop_propagation() {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');
        $primarynav = new \core\navigation\views\primary($PAGE);

        $hook = new primary_extend($primarynav);
        $this->assertInstanceOf('Psr\EventDispatcher\StoppableEventInterface', $hook);
        $this->assertFalse($hook->isPropagationStopped());

        $hook->stop_propagation();
        $this->assertTrue($hook->isPropagationStopped());
    }

    /**
     * Test hook is triggered when initialising primary navigation menu.
     * @covers \core\navigation\views\primary::initialise
     */
    public function test_trigggering() {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');

        $count = 0;
        $receivedhook = null;
        $testcallback = function(primary_extend $hook) use (&$receivedhook, &$count): void {
            $count++;
            $receivedhook = $hook;
        };
        $this->redirectHook(primary_extend::class, $testcallback);

        $primarynav = new \core\navigation\views\primary($PAGE);
        $this->assertSame(0, $count);
        $this->assertNull($receivedhook);

        $primarynav->initialise();
        $this->assertSame(1, $count);
        $this->assertInstanceOf(primary_extend::class, $receivedhook);
    }

    /**
     * Verify that nothing except this hook modifies the primary menu.
     * @covers \core\navigation\views\primary::initialise
     */
    public function test_unsupported_hacks() {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');

        $testcallback = function(primary_extend $hook): void {
            // Nothing to do, propagation is stopped by hook redirection.
        };
        $this->redirectHook(primary_extend::class, $testcallback);

        $primarynav = new \core\navigation\views\primary($PAGE);
        $primarynav->initialise();
        $this->assertSame(['home'], $primarynav->get_children_key_list(),
            'Unsupported primary menu modification detected, use new primary_extend hook instead.');

        $this->setAdminUser();
        $primarynav = new \core\navigation\views\primary($PAGE);
        $primarynav->initialise();
        $this->assertSame(['home', 'myhome', 'mycourses'], $primarynav->get_children_key_list(),
            'Unsupported primary menu modification detected, use new primary_extend hook instead.');
    }

    /**
     * Test adding of primary menu items via hook.
     * @covers \core\navigation\views\primary::initialise
     */
    public function test_primary_menu_extending() {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');

        $testcallback = function(primary_extend $hook): void {
            $primaryview = $hook->get_primaryview();
            $primaryview->add('Pokus', null);
        };
        $this->redirectHook(primary_extend::class, $testcallback);

        $primarynav = new \core\navigation\views\primary($PAGE);
        $primarynav->initialise();
        $keys = $primarynav->get_children_key_list();
        $this->assertCount(2, $keys);
        $firstkey = array_shift($keys);
        $this->assertSame('home', $firstkey);
        $secondkey = array_shift($keys);
        /** @var \navigation_node $pokus */
        $pokus = $primarynav->get($secondkey);
        $this->assertInstanceOf(\navigation_node::class, $pokus);
        $this->assertSame('Pokus', $pokus->text);
    }

    /**
     * Test replacing of the whole primary menu.
     * @covers \core\navigation\views\primary::initialise
     */
    public function test_primary_menu_replacing() {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');

        $testcallback = function(primary_extend $hook): void {
            $primaryview = $hook->get_primaryview();
            $keys = $primaryview->get_children_key_list();
            foreach ($keys as $key) {
                $item = $primaryview->get($key);
                $item->remove();
            }
            $primaryview->add('Pokus', null);
            // Technically we do not need to stop because observers are overridden,
            // but this can be used as an example for plugin that wants to stop
            // adding of primary menu items from plugins.
            $hook->stop_propagation();
        };
        $this->redirectHook(primary_extend::class, $testcallback);

        $primarynav = new \core\navigation\views\primary($PAGE);
        $primarynav->initialise();
        $keys = $primarynav->get_children_key_list();
        $this->assertCount(1, $keys);
        $firstkey = array_shift($keys);
        /** @var \navigation_node $pokus */
        $pokus = $primarynav->get($firstkey);
        $this->assertInstanceOf(\navigation_node::class, $pokus);
        $this->assertSame('Pokus', $pokus->text);
    }
}
