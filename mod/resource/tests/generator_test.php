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

namespace mod_resource;

/**
 * PHPUnit data generator testcase.
 *
 * @package    mod_resource
 * @category phpunit
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {
    public function test_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        // Must be a non-guest user to create resources.
        $this->setAdminUser();

        // There are 0 resources initially.
        $this->assertEquals(0, $DB->count_records('resource'));

        // Create the generator object and do standard checks.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_resource');
        $this->assertInstanceOf('mod_resource_generator', $generator);
        $this->assertEquals('resource', $generator->get_modulename());

        // Create three instances in the site course.
        $generator->create_instance(array('course' => $SITE->id));
        $generator->create_instance(array('course' => $SITE->id));
        $resource = $generator->create_instance(array('course' => $SITE->id));
        $this->assertEquals(3, $DB->count_records('resource'));

        // Check the course-module is correct.
        $cm = get_coursemodule_from_instance('resource', $resource->id);
        $this->assertEquals($resource->id, $cm->instance);
        $this->assertEquals('resource', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        // Check the context is correct.
        $context = \context_module::instance($cm->id);
        $this->assertEquals($resource->cmid, $context->instanceid);

        // Check that generated resource module contains a file.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', false, '', false);
        $file = array_values($files)[0];
        $this->assertCount(1, $files);
        $this->assertEquals('resource3.txt', $file->get_filename());
        $this->assertEquals('Test resource resource3.txt file', $file->get_content());

        // Create a new resource specifying the file name.
        $resource = $generator->create_instance(['course' => $SITE->id, 'defaultfilename' => 'myfile.pdf']);

        // Check that generated resource module contains a file with the specified name.
        $cm = get_coursemodule_from_instance('resource', $resource->id);
        $context = \context_module::instance($cm->id);
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', false, '', false);
        $file = array_values($files)[0];
        $this->assertCount(1, $files);
        $this->assertEquals('myfile.pdf', $file->get_filename());
        $this->assertEquals('Test resource myfile.pdf file', $file->get_content());
    }
}
