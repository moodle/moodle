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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../fixtures/event_fixtures.php');

/**
 * Tests for event manager, base event and observers.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unknown_logged_test extends \advanced_testcase {

    public function test_restore_event(): void {
        $event1 = \core_tests\event\unittest_executed::create(array('context' => \context_system::instance(), 'other' => array('sample' => 1, 'xx' => 10)));
        $data1 = $event1->get_data();

        $data1['eventname'] = '\mod_xx\event\xx_yy';
        $data1['component'] = 'mod_xx';
        $data1['action'] = 'yy';
        $data1['target'] = 'xx';
        $extra1 = array('origin' => 'cli');

        $event2 = \core\event\base::restore($data1, $extra1);
        $data2 = $event2->get_data();
        $extra2 = $event2->get_logextra();

        $this->assertInstanceOf('core\event\unknown_logged', $event2);
        $this->assertTrue($event2->is_triggered());
        $this->assertTrue($event2->is_restored());
        $this->assertNull($event2->get_url());
        $this->assertEquals($data1, $data2);
        $this->assertEquals($extra1, $extra2);
    }
}
