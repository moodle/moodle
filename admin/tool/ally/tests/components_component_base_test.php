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
 * Testcase class for the tool_ally\componentsupport\component_base class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\local_content;
use tool_ally\componentsupport\glossary_component; // Note this could be any component that extends component_base.
use tool_ally\testing\traits\component_assertions;

/**
 * Testcase class for the tool_ally\componentsupport\component_base class.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class components_component_base_test extends \advanced_testcase {
    use component_assertions;

    /**
     * @var stdClass
     */
    private $student;

    /**
     * @var stdClass
     */
    private $teacher;

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
     * @var glossary_component
     */
    private $component;

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->student = $gen->create_user();
        $this->teacher = $gen->create_user();
        $this->admin = get_admin();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $gen->enrol_user($this->student->id, $this->course->id, 'student');
        $gen->enrol_user($this->teacher->id, $this->course->id, 'editingteacher');

        $this->component = local_content::component_instance('glossary');

    }

    public function test_get_approved_author_ids_for_context() {
        $authorids = $this->component->get_approved_author_ids_for_context($this->coursecontext);
        $this->assertTrue(in_array($this->teacher->id, $authorids),
                'Teacher id '.$this->teacher->id.' should be in list of author ids.');
        $this->assertTrue(in_array($this->admin->id, $authorids),
                'Admin id '.$this->admin->id.' should be in list of author ids.');
        $this->assertFalse(in_array($this->student->id, $authorids),
                'Student id '.$this->student->id.' should NOT be in list of author ids.');
    }

    public function test_user_is_approved_author_type() {
        $this->assertFalse($this->component->user_is_approved_author_type($this->student->id, $this->coursecontext),
            'Student should not be approved author type');
        $this->assertTrue($this->component->user_is_approved_author_type($this->teacher->id, $this->coursecontext),
            'Teacher should be approved author type');
        $this->assertTrue($this->component->user_is_approved_author_type($this->admin->id, $this->coursecontext),
            'Admin should be approved author type');
    }

}
