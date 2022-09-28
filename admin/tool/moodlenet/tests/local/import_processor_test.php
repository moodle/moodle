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

namespace tool_moodlenet\local;

use tool_moodlenet\local\import_handler_registry;
use tool_moodlenet\local\import_processor;
use tool_moodlenet\local\import_strategy_file;
use tool_moodlenet\local\import_strategy_link;
use tool_moodlenet\local\remote_resource;
use tool_moodlenet\local\url;

/**
 * Class tool_moodlenet_import_processor_testcase, providing test cases for the import_processor class.
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_processor_test extends \advanced_testcase {

    /**
     * An integration test, this confirms the ability to construct an import processor and run the import for the current user.
     */
    public function test_process_valid_resource() {
        $this->resetAfterTest();

        // Set up a user as a teacher in a course.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $section = 0;
        $this->setUser($teacher);

        // Set up the import, using a mod_resource handler for the html extension.
        $resourceurl = $this->getExternalTestFileUrl('/test.html');
        $remoteresource = new remote_resource(
            new \curl(),
            new url($resourceurl),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource description'
            ]
        );
        $handlerregistry = new import_handler_registry($course, $teacher);
        $handlerinfo = $handlerregistry->get_resource_handler_for_mod_and_strategy($remoteresource, 'resource',
            new import_strategy_file());
        $importproc = new import_processor($course, $section, $remoteresource, $handlerinfo, $handlerregistry);

        // Import the file.
        $importproc->process();

        // Verify there is a new mod_resource created with correct name, description and containing the test.html file.
        $modinfo = get_fast_modinfo($course, $teacher->id);
        $cms = $modinfo->get_instances();
        $this->assertArrayHasKey('resource', $cms);
        $cminfo = array_shift($cms['resource']);
        $this->assertEquals('Resource name', $cminfo->get_formatted_name());
        $cm = get_coursemodule_from_id('', $cminfo->id, 0, false, MUST_EXIST);
        list($cm, $context, $module, $data, $cw) = get_moduleinfo_data($cminfo, $course);
        $this->assertEquals($remoteresource->get_description(), $data->intro);
        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_module::instance($cminfo->id)->id, 'mod_resource', 'content', false,
            'sortorder DESC, id ASC', false);
        $file = reset($files);
        $this->assertEquals('test.html', $file->get_filename());
        $this->assertEquals('text/html', $file->get_mimetype());
    }

    /**
     * Test confirming that an exception is thrown when trying to process a resource which does not exist.
     */
    public function test_process_invalid_resource() {
        $this->resetAfterTest();

        // Set up a user as a teacher in a course.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $section = 0;
        $this->setUser($teacher);

        // Set up the import, using a mod_resource handler for the html extension.
        $resourceurl = $this->getExternalTestFileUrl('/test.htmlzz');
        $remoteresource = new remote_resource(
            new \curl(),
            new url($resourceurl),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource description'
            ]
        );
        $handlerregistry = new import_handler_registry($course, $teacher);
        $handlerinfo = $handlerregistry->get_resource_handler_for_mod_and_strategy($remoteresource, 'resource',
            new import_strategy_file());
        $importproc = new import_processor($course, $section, $remoteresource, $handlerinfo, $handlerregistry);

        // Import the file.
        $this->expectException(\coding_exception::class);
        $importproc->process();
    }

    /**
     * Test confirming that imports can be completed using alternative import strategies.
     */
    public function test_process_alternative_import_strategies() {
        $this->resetAfterTest();

        // Set up a user as a teacher in a course.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $section = 0;
        $this->setUser($teacher);

        // Set up the import, using a mod_url handler and the link import strategy.
        $remoteresource = new remote_resource(
            new \curl(),
            new url('http://example.com/cats.pdf'),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource description'
            ]
        );
        $handlerregistry = new import_handler_registry($course, $teacher);
        $handlerinfo = $handlerregistry->get_resource_handler_for_mod_and_strategy($remoteresource, 'url',
            new import_strategy_link());
        $importproc = new import_processor($course, $section, $remoteresource, $handlerinfo, $handlerregistry);

        // Import the resource as a link.
        $importproc->process();

        // Verify there is a new mod_url created with name 'cats' and containing the URL of the resource.
        $modinfo = get_fast_modinfo($course, $teacher->id);
        $cms = $modinfo->get_instances();
        $this->assertArrayHasKey('url', $cms);
        $cminfo = array_shift($cms['url']);
        $this->assertEquals('Resource name', $cminfo->get_formatted_name());
    }
}
