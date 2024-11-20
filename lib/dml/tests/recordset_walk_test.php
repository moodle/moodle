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

/**
 * Test case for recordset_walk.
 *
 * @package    core
 * @category   test
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recordset_walk_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_no_data(): void {
        global $DB;

        $recordset = $DB->get_recordset('assign');
        $walker = new \core\dml\recordset_walk($recordset, array($this, 'simple_callback'));
        $this->assertFalse($walker->valid());

        $count = 0;
        foreach ($walker as $data) {
            // No error here.
            $count++;
        }
        $this->assertEquals(0, $count);
        $walker->close();
    }

    public function test_simple_callback(): void {
        global $DB;

        /** @var \mod_assign_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $courses = array();
        for ($i = 0; $i < 10; $i++) {
            $courses[$i] = $generator->create_instance(array('course' => SITEID));
        }

        // Simple iteration.
        $recordset = $DB->get_recordset('assign');
        $walker = new \core\dml\recordset_walk($recordset, array($this, 'simple_callback'));

        $count = 0;
        foreach ($walker as $data) {
            // Checking that the callback is being executed on each iteration.
            $this->assertEquals($data->id . ' potatoes', $data->newfield);
            $count++;
        }
        $this->assertEquals(10, $count);
        // No exception if we double-close.
        $walker->close();
    }

    public function test_extra_params_callback(): void {
        global $DB;

        /** @var \mod_assign_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $courses = array();
        for ($i = 0; $i < 10; $i++) {
            $courses[$i] = $generator->create_instance(array('course' => SITEID));
        }

        // Iteration with extra callback arguments.
        $recordset = $DB->get_recordset('assign');

        $walker = new \core\dml\recordset_walk(
            $recordset,
            array($this, 'extra_callback'),
            array('brown' => 'onions')
        );

        $count = 0;
        foreach ($walker as $data) {
            // Checking that the callback is being executed on each
            // iteration and the param is being passed.
            $this->assertEquals('onions', $data->brown);
            $count++;
        }
        $this->assertEquals(10, $count);

        $walker->close();
    }

    /**
     * Simple callback requiring 1 row fields.
     *
     * @param \stdClass $data
     * @return \Traversable
     */
    public function simple_callback($data, $nothing = 'notpassed') {
        // Confirm nothing was passed.
        $this->assertEquals('notpassed', $nothing);
        $data->newfield = $data->id . ' potatoes';
        return $data;
    }

    /**
     * Callback requiring 1 row fields + other params.
     *
     * @param \stdClass $data
     * @param mixed $extra
     * @return \Traversable
     */
    public function extra_callback($data, $extra) {
        $data->brown = $extra['brown'];
        return $data;
    }
}
