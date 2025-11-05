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
 * Testcase class for the tool_ally\componentsupport\page_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local_content;
use tool_ally\componentsupport\glossary_component;
use tool_ally\testing\traits\component_assertions;
use tool_ally\webservice\course_content;
use tool_ally\models\component;
use tool_ally\models\component_content;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

/**
 * Testcase class for the tool_ally\componentsupport\page_component class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class components_page_component_test extends abstract_testcase {
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
        $this->page = $gen->create_module('page',
            [
                'course' => $this->course->id,
                'introformat' => FORMAT_HTML,
                'intro' => 'Text in intro',
                'contentformat' => FORMAT_HTML,
                'content' => 'Text in content'
            ]
        );

        $this->component = local_content::component_instance('page');
    }

    public function test_list_intro_and_content() {
        $this->setAdminUser();
        $contentitems = course_content::service([$this->course->id]);
        $component = new component(0, 'page', 'page', 'intro', $this->course->id, 0, FORMAT_HTML, $this->page->name);
        $this->assert_component_is_in_array($component, $contentitems);
        $component = new component(0, 'page', 'page', 'content', $this->course->id, 0, FORMAT_HTML, $this->page->name);
        $this->assert_component_is_in_array($component, $contentitems);
    }

    public function test_get_all_html_content() {
        $items = local_content::get_all_html_content($this->page->id, 'page');
        $componentcontent = new component_content(
                $this->page->id, 'page', 'page', 'intro', $this->course->id, 0,
                FORMAT_HTML, $this->page->intro, $this->page->name);
        $this->assertTrue($this->component_content_is_in_array($componentcontent, $items));
    }

    public function test_resolve_module_instance_id() {
        $this->setAdminUser();
        $instanceid = $this->component->resolve_module_instance_id('page', $this->page->id);
        $this->assertEquals($this->page->id, $instanceid);
    }

    public function test_get_all_course_annotation_maps() {
        $cis = $this->component->get_annotation_maps($this->course->id);
        $this->assertEquals('page:page:intro:' . $this->page->id, reset($cis['intros']));
        $this->assertEquals('page:page:content:' . $this->page->id, reset($cis['content']));

        $gen = $this->getDataGenerator();
        $this->admin = get_admin();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->page = $gen->create_module('page',
                                          [
                                              'course' => $this->course->id,
                                          ]
        );

        $cis = $this->component->get_annotation_maps($this->course->id);
        $this->assertEquals([], $cis['intros']);
        $this->assertEquals([], $cis['content']);

    }

    /**
     * Test if file in use detection is working with this module.
     */
    public function test_check_file_in_use() {
        $context = \context_module::instance($this->page->cmid);

        $usedfiles = [];
        $unusedfiles = [];

        // Check the intro.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'mod_page', $this->page->id,
            'page', 'intro');

        // Check the page content.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'mod_page', $this->page->id,
            'page', 'content');

        // This will double check that file iterator is working as expected.
        $this->check_file_iterator_exclusion($context, $usedfiles, $unusedfiles);
    }
}
