<?php

// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Certificate module data generator.
 *
 * @package    mod_certificateuv
 * @category   test
 * @author     Russell England <russell.england@catalyst-eu.net>
 * @copyright  Catalyst IT Ltd 2013 <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

class mod_certificateuv_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('certificateuv'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_certificate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_certificateuv');
        $this->assertInstanceOf('mod_certificateuv_generator', $generator);
        $this->assertEquals('certificateuv', $generator->get_modulename());

        $generator->create_instance(array('course' => $course->id));
        $generator->create_instance(array('course' => $course->id));
        $certificate = $generator->create_instance(array('course' => $course->id));
        $this->assertEquals(3, $DB->count_records('certificateuv'));

        $cm = get_coursemodule_from_instance('certificateuv', $certificate->id);
        $this->assertEquals($certificate->id, $cm->instance);
        $this->assertEquals('certificateuv', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($certificate->cmid, $context->instanceid);
    }
}
