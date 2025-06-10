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
 * File contains the unit tests for the element helper class.
 *
 * @package    mod_customcert
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

use grade_item;
use grade_grade;
use context_module;
use context_system;
use advanced_testcase;

/**
 * Unit tests for the element helper class.
 *
 * @package    mod_customcert
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element_helper_test extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Tests we are returning the correct course id for an element in a course customcert activity.
     *
     * @covers \element_helper::get_courseid
     */
    public function test_get_courseid_element_in_course_certificate() {
        global $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a custom certificate in the course.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Get the template to add elements to.
        $template = $DB->get_record('customcert_templates', ['contextid' => context_module::instance($customcert->cmid)->id]);
        $template = new template($template);

        // Add a page to the template.
        $pageid = $template->add_page();

        // Add an element to this page.
        $element = new \stdClass();
        $element->name = 'Test element';
        $element->element = 'testelement';
        $element->pageid = $pageid;
        $element->sequence = element_helper::get_element_sequence($element->pageid);
        $element->timecreated = time();
        $element->id = $DB->insert_record('customcert_elements', $element);

        // Confirm the correct course id is returned.
        $this->assertEquals($course->id, element_helper::get_courseid($element->id));
    }

    /**
     * Tests we are returning the correct course id for an element in a site template.
     *
     * @covers \element_helper::get_courseid
     */
    public function test_get_courseid_element_in_site_template() {
        global $DB, $SITE;

        // Add a template to the site.
        $template = template::create('Site template', context_system::instance()->id);

        // Add a page to the template.
        $pageid = $template->add_page();

        // Add an element to this page.
        $element = new \stdClass();
        $element->name = 'Test element';
        $element->element = 'testelement';
        $element->pageid = $pageid;
        $element->sequence = element_helper::get_element_sequence($element->pageid);
        $element->timecreated = time();
        $element->id = $DB->insert_record('customcert_elements', $element);

        // Confirm the correct course id is returned.
        $this->assertEquals($SITE->id, element_helper::get_courseid($element->id));
    }

    /**
     * Tests we are returning the correct course module id for an element in a course customcert activity.
     *
     * @covers \element_helper::get_context
     */
    public function test_get_context_element_in_course_certificate() {
        global $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a custom certificate in the course.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Get the template to add elements to.
        $template = $DB->get_record('customcert_templates', ['contextid' => context_module::instance($customcert->cmid)->id]);
        $template = new template($template);

        // Add a page to the template.
        $pageid = $template->add_page();

        // Add an element to this page.
        $element = new \stdClass();
        $element->name = 'Test element';
        $element->element = 'testelement';
        $element->pageid = $pageid;
        $element->sequence = element_helper::get_element_sequence($element->pageid);
        $element->timecreated = time();
        $element->id = $DB->insert_record('customcert_elements', $element);

        // Confirm the correct course module id is returned.
        $this->assertEquals(context_module::instance($customcert->cmid),
            element_helper::get_context($element->id));
    }

    /**
     * Tests we are returning the correct course module id for an element in a site template.
     *
     * @covers \element_helper::get_context
     */
    public function test_get_context_element_in_site_template() {
        global $DB;

        // Add a template to the site.
        $template = template::create('Site template', context_system::instance()->id);

        // Add a page to the template.
        $pageid = $template->add_page();

        // Add an element to this page.
        $element = new \stdClass();
        $element->name = 'Test element';
        $element->element = 'testelement';
        $element->pageid = $pageid;
        $element->sequence = element_helper::get_element_sequence($element->pageid);
        $element->timecreated = time();
        $element->id = $DB->insert_record('customcert_elements', $element);

        // Confirm the correct course module id is returned.
        $this->assertEquals(context_system::instance(), element_helper::get_context($element->id));
    }

    /**
     * Test we return the correct grade items in a course.
     *
     * @covers \element_helper::get_grade_items
     */
    public function test_get_grade_items() {
        global $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a few gradeable items.
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign3 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Create a manual grade item.
        $gi = $this->getDataGenerator()->create_grade_item(['courseid' => $course->id]);

        // Create a category grade item.
        $gc = $this->getDataGenerator()->create_grade_category(['courseid' => $course->id]);
        $gc = $DB->get_record('grade_items', ['itemtype' => 'category', 'iteminstance' => $gc->id]);

        // Create an item attached to an outcome.
        $outcome = $this->getDataGenerator()->create_grade_outcome(['courseid' => $course->id, 'shortname' => 'outcome']);
        $go = $this->getDataGenerator()->create_grade_item(
            [
                'courseid' => $course->id,
                'outcomeid' => $outcome->id,
            ]
        );

        // Confirm the function returns the correct number of grade items.
        $gradeitems = element_helper::get_grade_items($course);
        $this->assertCount(6, $gradeitems);
        $this->assertArrayHasKey($assign1->cmid, $gradeitems);
        $this->assertArrayHasKey($assign2->cmid, $gradeitems);
        $this->assertArrayHasKey($assign3->cmid, $gradeitems);
        $this->assertArrayHasKey('gradeitem:' . $gi->id, $gradeitems);
        $this->assertArrayHasKey('gradeitem:' . $gc->id, $gradeitems);
        $this->assertArrayHasKey('gradeitem:' . $go->id, $gradeitems);
    }

    /**
     * Test we return the correct grade information for an activity.
     *
     * @covers \element_helper::get_mod_grade_info
     */
    public function test_get_mod_grade_info() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Set that we want 3 decimals to display.
        grade_set_setting($course->id, 'decimalpoints', 3);

        // Create two users.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();

        // Enrol them into the course.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id);

        // Create a gradeable item.
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Give a grade to the student.
        $gi = grade_item::fetch(
            [
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $assign->id,
                'courseid' => $course->id,
            ]
        );
        $datagrade = 50;
        $time = time();
        $grade = new grade_grade();
        $grade->itemid = $gi->id;
        $grade->userid = $student1->id;
        $grade->rawgrade = $datagrade;
        $grade->finalgrade = $datagrade;
        $grade->rawgrademax = 100;
        $grade->rawgrademin = 0;
        $grade->timecreated = $time;
        $grade->timemodified = $time;
        $grade->insert();

        // Check that the user received the grade.
        $grade = element_helper::get_mod_grade_info(
            $assign->cmid,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $student1->id
        );

        $this->assertEquals($assign->name, $grade->get_name());
        $this->assertEquals('50.00000', $grade->get_grade());
        $this->assertEquals('50.000 %', $grade->get_displaygrade());
        $this->assertEquals($time, $grade->get_dategraded());

        // Check that the user we did not grade has no grade.
        $grade = element_helper::get_mod_grade_info(
            $assign->cmid,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $student2->id
        );
        $this->assertEquals($assign->name, $grade->get_name());
        $this->assertEquals(null, $grade->get_grade());
        $this->assertEquals('-', $grade->get_displaygrade());
        $this->assertEquals(null, $grade->get_dategraded());

        grade_get_setting($course->id, null, null, true);
    }

    /**
     * Test we return the correct grade information for a course.
     *
     * @covers \element_helper::get_course_grade_info
     */
    public function test_get_course_grade_info() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Set that we want 3 decimals to display.
        grade_set_setting($course->id, 'decimalpoints', 3);

        // Create two users.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();

        // Enrol them into the course.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id);

        // Get the course item.
        $coursegradeitem = grade_item::fetch_course_item($course->id);

        $datagrade = 50;
        $time = time();
        $grade = new grade_grade();
        $grade->itemid = $coursegradeitem->id;
        $grade->userid = $student1->id;
        $grade->rawgrade = $datagrade;
        $grade->finalgrade = $datagrade;
        $grade->rawgrademax = 100;
        $grade->rawgrademin = 0;
        $grade->timecreated = $time;
        $grade->timemodified = $time;
        $grade->insert();

        // Check that the user received the grade.
        $grade = element_helper::get_course_grade_info(
            $course->id,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $student1->id
        );

        $this->assertEquals(get_string('coursetotal', 'grades'), $grade->get_name());
        $this->assertEquals('50.00000', $grade->get_grade());
        $this->assertEquals('50.000 %', $grade->get_displaygrade());
        $this->assertEquals($time, $grade->get_dategraded());

        // Check that the user we did not grade has no grade.
        $grade = element_helper::get_course_grade_info(
            $course->id,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $student2->id
        );
        $this->assertEquals(get_string('coursetotal', 'grades'), $grade->get_name());
        $this->assertEquals(null, $grade->get_grade());
        $this->assertEquals('-', $grade->get_displaygrade());
        $this->assertEquals(null, $grade->get_dategraded());

        grade_get_setting($course->id, null, null, true);
    }

    /**
     * Test we return the correct grade information for a grade item.
     *
     * @covers \element_helper::get_grade_item_info
     */
    public function test_get_grade_item_info() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Set that we want 3 decimals to display.
        grade_set_setting($course->id, 'decimalpoints', 3);

        // Create two users.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();

        // Enrol them into the course.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id);

        // Create a manual grade item.
        $gi = $this->getDataGenerator()->create_grade_item(['itemname' => 'Grade item yo', 'courseid' => $course->id]);

        // Give a grade to the student.
        $gi = grade_item::fetch(['id' => $gi->id]);
        $datagrade = 50;
        $time = time();
        $grade = new grade_grade();
        $grade->itemid = $gi->id;
        $grade->userid = $student1->id;
        $grade->rawgrade = $datagrade;
        $grade->finalgrade = $datagrade;
        $grade->rawgrademax = 100;
        $grade->rawgrademin = 0;
        $grade->timecreated = $time;
        $grade->timemodified = $time;
        $grade->insert();

        // Check that the user received the grade.
        $grade = element_helper::get_grade_item_info(
            $gi->id,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $student1->id
        );

        $this->assertEquals('Grade item yo', $grade->get_name());
        $this->assertEquals('50.00000', $grade->get_grade());
        $this->assertEquals('50.000 %', $grade->get_displaygrade());
        $this->assertEquals($time, $grade->get_dategraded());

        // Check that the user we did not grade has no grade.
        $grade = element_helper::get_grade_item_info(
            $gi->id,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $student2->id
        );
        $this->assertEquals('Grade item yo', $grade->get_name());
        $this->assertEquals(null, $grade->get_grade());
        $this->assertEquals('-', $grade->get_displaygrade());
        $this->assertEquals(null, $grade->get_dategraded());

        grade_get_setting($course->id, null, null, true);
    }
}
