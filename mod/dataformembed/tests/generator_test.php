<?php
// This file is part of Moodle - http://moodle.org/.
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

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit dataform generator testcase
 *
 * @package    mod_dataformembed
 * @category   phpunit
 * @group      mod_dataformembed
 * @group      mod_dataform
 * @copyright  2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_dataformembed_generator_testcase extends advanced_testcase {

    public function test_generator() {
        global $DB;

        $this->resetAfterTest();

        $this->assertEquals(0, $DB->count_records('dataformembed'));

        $course = $this->getDataGenerator()->create_course();

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_dataformembed');
        $this->assertInstanceOf('mod_dataformembed_generator', $generator);
        $this->assertEquals('dataformembed', $generator->get_modulename());

        $generator->create_instance(array('course' => $course->id));
        $generator->create_instance(array('course' => $course->id));
        $dataformembed = $generator->create_instance(array('course' => $course->id));
        $this->assertEquals(3, $DB->count_records('dataformembed'));

        $cm = get_coursemodule_from_instance('dataformembed', $dataformembed->id);
        $this->assertEquals($dataformembed->id, $cm->instance);
        $this->assertEquals('dataformembed', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($dataformembed->cmid, $context->instanceid);
    }
}
