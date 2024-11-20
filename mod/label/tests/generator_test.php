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

namespace mod_label;

/**
 * PHPUnit label generator testcase
 *
 * @package    mod_label
 * @category   phpunit
 * @copyright  2013 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {
    public function test_generator(): void {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('label'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_label_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_label');
        $this->assertInstanceOf('mod_label_generator', $generator);
        $this->assertEquals('label', $generator->get_modulename());

        $generator->create_instance(array('course'=>$course->id));
        $generator->create_instance(array('course'=>$course->id));
        $label = $generator->create_instance(array('course'=>$course->id));
        $this->assertEquals(3, $DB->count_records('label'));

        $cm = get_coursemodule_from_instance('label', $label->id);
        $this->assertEquals($label->id, $cm->instance);
        $this->assertEquals('label', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($label->cmid, $context->instanceid);
    }
}
