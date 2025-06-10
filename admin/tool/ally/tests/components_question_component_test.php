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

/**
 * Testcase class for the tool_ally\componentsupport\question_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local_content;
use tool_ally\testing\traits\component_assertions;
use tool_ally\webservice\course_content;
use tool_ally\componentsupport\question_component;
use tool_ally\componentsupport\component_base;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

/**
 * Testcase class for the tool_ally\componentsupport\page_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class components_question_component_test extends abstract_testcase {
    use component_assertions;

    /**
     * @var stdClass
     */
    private $admin;

    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var context_course
     */
    private $coursecontext;

    /**
     * @var stdClass
     */
    private $page;

    /**
     * @var glossary_component
     */
    private $component;

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->admin = get_admin();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $qcat1 = $generator->create_question_category(array(
            'name' => 'My category', 'sortorder' => 1, 'idnumber' => 'myqcat'));
        $this->quest1 = $generator->create_question('shortanswer', null,
            ['name' => 'sa1', 'category' => $qcat1->id, 'idnumber' => 'myquest_3']);

        $this->component = local_content::component_instance('question');
    }

    public function test_component_type() {
        $type = question_component::component_type();
        $this->assertEquals(component_base::TYPE_CORE, $type);
    }

    public function test_fileurlproperties() {
        $pluginfileurl = 'http://moodle.test/pluginfile.php/16/question/questiontext/1/1/1/test.odt';
        $urlprops = question_component::fileurlproperties($pluginfileurl);

        $this->assertEquals(16, $urlprops->contextid);
        $this->assertEquals('question', $urlprops->component);
        $this->assertEquals('questiontext', $urlprops->filearea);
        $this->assertEquals('1', $urlprops->itemid);
        $this->assertEquals('test.odt', $urlprops->filename);
    }

    public function test_get_question() {
        $quest = \phpunit_util::call_internal_method(
            $this->component,
            'get_question',
            [$this->quest1->id],
            question_component::class
        );
        $this->assertEquals((int) $this->quest1->id, (int) $quest->id);
        $this->assertEquals($this->quest1->name, $quest->name);
        $this->assertEquals($this->quest1->idnumber, $quest->idnumber);
    }

    public function test_list_intro_and_content() {
        $this->markTestSkipped('HTML content not yet supported');
    }

    public function test_get_all_html_content() {
        $this->markTestSkipped('HTML content not yet supported');
    }

    public function test_get_all_course_annotation_maps() {
        $this->markTestSkipped('HTML content not yet supported');
    }
}
