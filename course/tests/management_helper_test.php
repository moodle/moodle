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

namespace core_course;

use core_course_category;
use core_course_list_element;
use course_capability_assignment;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/tests/fixtures/course_capability_assignment.php');

/**
 * Course and category management helper class tests.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class management_helper_test extends \advanced_testcase {

    /** Category management capability: moodle/category:manage */
    const CATEGORY_MANAGE = 'moodle/category:manage';
    /** View hidden category capability: moodle/category:viewhiddencategories */
    const CATEGORY_VIEWHIDDEN = 'moodle/category:viewhiddencategories';
    /** View course capability: moodle/course:visibility */
    const COURSE_VIEW = 'moodle/course:visibility';
    /** View hidden course capability: moodle/course:viewhiddencourses */
    const COURSE_VIEWHIDDEN = 'moodle/course:viewhiddencourses';

    /**
     * Returns a user object and its assigned new role.
     *
     * @param testing_data_generator $generator
     * @param $contextid
     * @return array The user object and the role ID
     */
    protected function get_user_objects(\testing_data_generator $generator, $contextid) {
        global $USER;

        if (empty($USER->id)) {
            $user  = $generator->create_user();
            $this->setUser($user);
        }
        $roleid = create_role('Test role', 'testrole', 'Test role description');
        if (!is_array($contextid)) {
            $contextid = array($contextid);
        }
        foreach ($contextid as $cid) {
            $assignid = role_assign($roleid, $user->id, $cid);
        }
        return array($user, $roleid);
    }

    /**
     * Tests:
     *   - action_category_hide
     *   - action_category_show
     *
     * In order to show/hide the user must have moodle/category:manage on the parent context.
     * In order to view hidden categories the user must have moodle/category:viewhiddencategories
     */
    public function test_action_category_hide_and_show(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $subcategory = $generator->create_category(array('parent' => $category->id));
        $course = $generator->create_course(array('category' => $subcategory->id));
        $context = $category->get_context();
        $subcontext = $subcategory->get_context();
        $parentcontext = $context->get_parent_context();
        list($user, $roleid) = $this->get_user_objects($generator, $parentcontext->id);

        $this->assertEquals(1, $category->visible);

        $parentassignment = course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $parentcontext->id);
        course_capability_assignment::allow(self::CATEGORY_VIEWHIDDEN, $roleid, $parentcontext->id);
        course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $context->id);
        course_capability_assignment::allow(array(self::COURSE_VIEW, self::COURSE_VIEWHIDDEN), $roleid, $subcontext->id);

        $this->assertTrue(\core_course\management\helper::action_category_hide($category));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);
        // This doesn't change anything but should succeed still.
        $this->assertTrue(\core_course\management\helper::action_category_hide($category));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $this->assertTrue(\core_course\management\helper::action_category_show($category));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(1, $cat->visible);
        $this->assertEquals(1, $cat->visibleold);
        $this->assertEquals(1, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(1, $course->visible);
        $this->assertEquals(1, $course->visibleold);
        // This doesn't change anything but should succeed still.
        $this->assertTrue(\core_course\management\helper::action_category_show($category));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(1, $cat->visible);
        $this->assertEquals(1, $cat->visibleold);
        $this->assertEquals(1, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(1, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $parentassignment->assign(CAP_PROHIBIT);

        try {
            \core_course\management\helper::action_category_hide($category);
            $this->fail('Expected exception did not occur when trying to hide a category without permission.');
        } catch (\moodle_exception $ex) {
            // The category must still be visible.
            $cat = core_course_category::get($category->id);
            $subcat = core_course_category::get($subcategory->id);
            $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
            $this->assertEquals(1, $cat->visible);
            $this->assertEquals(1, $cat->visibleold);
            $this->assertEquals(1, $subcat->visible);
            $this->assertEquals(1, $subcat->visibleold);
            $this->assertEquals(1, $course->visible);
            $this->assertEquals(1, $course->visibleold);
        }

        // Hide the category so that we can test helper::show.
        $parentassignment->assign(CAP_ALLOW);
        \core_course\management\helper::action_category_hide($category);
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $parentassignment->assign(CAP_PROHIBIT);

        try {
            \core_course\management\helper::action_category_show($category);
            $this->fail('Expected exception did not occur when trying to show a category without permission.');
        } catch (\moodle_exception $ex) {
            // The category must still be hidden.
            $cat = core_course_category::get($category->id);
            $subcat = core_course_category::get($subcategory->id);
            $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
            $this->assertEquals(0, $cat->visible);
            $this->assertEquals(0, $cat->visibleold);
            $this->assertEquals(0, $subcat->visible);
            $this->assertEquals(1, $subcat->visibleold);
            $this->assertEquals(0, $course->visible);
            $this->assertEquals(1, $course->visibleold);
        }

        $parentassignment->assign(CAP_PREVENT);
        // Now we have capability on the category and subcategory but not the parent.
        // Try to mark the subcategory as visible. This should be possible although its parent is set to hidden.
        $this->assertTrue(\core_course\management\helper::action_category_show($subcategory));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        // Now make the parent visible for the next test.
        $parentassignment->assign(CAP_ALLOW);
        $this->assertTrue(\core_course\management\helper::action_category_show($category));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(1, $cat->visible);
        $this->assertEquals(1, $cat->visibleold);
        $this->assertEquals(1, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(1, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $parentassignment->assign(CAP_PREVENT);
        // Make sure we can change the subcategory visibility.
        $this->assertTrue(\core_course\management\helper::action_category_hide($subcategory));
        // But not the category visibility.
        try {
            \core_course\management\helper::action_category_hide($category);
            $this->fail('Expected exception did not occur when trying to hide a category without permission.');
        } catch (\moodle_exception $ex) {
            // The category must still be visible.
            $this->assertEquals(1, core_course_category::get($category->id)->visible);
        }
    }

    /**
     * Tests hiding and showing of a category by its ID.
     *
     * This mimics the logic of {@link test_action_category_hide_and_show()}
     */
    public function test_action_category_hide_and_show_by_id(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $subcategory = $generator->create_category(array('parent' => $category->id));
        $course = $generator->create_course(array('category' => $subcategory->id));
        $context = $category->get_context();
        $parentcontext = $context->get_parent_context();
        $subcontext = $subcategory->get_context();
        list($user, $roleid) = $this->get_user_objects($generator, $parentcontext->id);

        $this->assertEquals(1, $category->visible);

        $parentassignment = course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $parentcontext->id);
        course_capability_assignment::allow(self::CATEGORY_VIEWHIDDEN, $roleid, $parentcontext->id);
        course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $context->id);
        course_capability_assignment::allow(array(self::COURSE_VIEW, self::COURSE_VIEWHIDDEN), $roleid, $subcontext->id);

        $this->assertTrue(\core_course\management\helper::action_category_hide_by_id($category->id));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);
        // This doesn't change anything but should succeed still.
        $this->assertTrue(\core_course\management\helper::action_category_hide_by_id($category->id));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $this->assertTrue(\core_course\management\helper::action_category_show_by_id($category->id));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(1, $cat->visible);
        $this->assertEquals(1, $cat->visibleold);
        $this->assertEquals(1, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(1, $course->visible);
        $this->assertEquals(1, $course->visibleold);
        // This doesn't change anything but should succeed still.
        $this->assertTrue(\core_course\management\helper::action_category_show_by_id($category->id));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(1, $cat->visible);
        $this->assertEquals(1, $cat->visibleold);
        $this->assertEquals(1, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(1, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $parentassignment->assign(CAP_PROHIBIT);

        try {
            \core_course\management\helper::action_category_hide_by_id($category->id);
            $this->fail('Expected exception did not occur when trying to hide a category without permission.');
        } catch (\moodle_exception $ex) {
            // The category must still be visible.
            $cat = core_course_category::get($category->id);
            $subcat = core_course_category::get($subcategory->id);
            $this->assertEquals(1, $cat->visible);
            $this->assertEquals(1, $cat->visibleold);
            $this->assertEquals(1, $subcat->visible);
            $this->assertEquals(1, $subcat->visibleold);
            $this->assertEquals(1, $course->visible);
            $this->assertEquals(1, $course->visibleold);
        }

        // Hide the category so that we can test helper::show.
        $parentassignment->assign(CAP_ALLOW);
        \core_course\management\helper::action_category_hide_by_id($category->id);
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $parentassignment->assign(CAP_PROHIBIT);

        try {
            \core_course\management\helper::action_category_show_by_id($category->id);
            $this->fail('Expected exception did not occur when trying to show a category without permission.');
        } catch (\moodle_exception $ex) {
            // The category must still be hidden.
            $cat = core_course_category::get($category->id);
            $subcat = core_course_category::get($subcategory->id);
            $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
            $this->assertEquals(0, $cat->visible);
            $this->assertEquals(0, $cat->visibleold);
            $this->assertEquals(0, $subcat->visible);
            $this->assertEquals(1, $subcat->visibleold);
            $this->assertEquals(0, $course->visible);
            $this->assertEquals(1, $course->visibleold);
        }

        $parentassignment->assign(CAP_PREVENT);
        // Now we have capability on the category and subcategory but not the parent.
        // Try to mark the subcategory as visible. This should be possible although its parent is set to hidden.
        $this->assertTrue(\core_course\management\helper::action_category_show($subcategory));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(0, $cat->visible);
        $this->assertEquals(0, $cat->visibleold);
        $this->assertEquals(0, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(0, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        // Now make the parent visible for the next test.
        $parentassignment->assign(CAP_ALLOW);
        $this->assertTrue(\core_course\management\helper::action_category_show_by_id($category->id));
        $cat = core_course_category::get($category->id);
        $subcat = core_course_category::get($subcategory->id);
        $course = $DB->get_record('course', array('id' => $course->id), 'id, visible, visibleold', MUST_EXIST);
        $this->assertEquals(1, $cat->visible);
        $this->assertEquals(1, $cat->visibleold);
        $this->assertEquals(1, $subcat->visible);
        $this->assertEquals(1, $subcat->visibleold);
        $this->assertEquals(1, $course->visible);
        $this->assertEquals(1, $course->visibleold);

        $parentassignment->assign(CAP_PREVENT);
        // Make sure we can change the subcategory visibility.
        $this->assertTrue(\core_course\management\helper::action_category_hide($subcategory));
        // But not the category visibility.
        try {
            \core_course\management\helper::action_category_hide_by_id($category->id);
            $this->fail('Expected exception did not occur when trying to hide a category without permission.');
        } catch (\moodle_exception $ex) {
            // The category must still be visible.
            $this->assertEquals(1, core_course_category::get($category->id)->visible);
        }
    }

    /**
     * Test moving courses between categories.
     */
    public function test_action_category_move_courses_into(): void {
        global $DB, $CFG;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category();
        $sub1 = $generator->create_category(array('parent' => $cat1->id));
        $sub2 = $generator->create_category(array('parent' => $cat1->id));
        $course1 = $generator->create_course(array('category' => $cat1->id));
        $course2 = $generator->create_course(array('category' => $sub1->id));
        $course3 = $generator->create_course(array('category' => $sub1->id));
        $course4 = $generator->create_course(array('category' => $cat2->id));

        $syscontext = \context_system::instance();

        list($user, $roleid) = $this->get_user_objects($generator, $syscontext->id);

        course_capability_assignment::allow(array(self::CATEGORY_MANAGE, self::CATEGORY_VIEWHIDDEN), $roleid, $syscontext->id);

        // Check they are where we think they are.
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(1, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(0, $sub2->get_courses_count());

        // Move the courses in sub category 1 to sub category 2.
        $this->assertTrue(
            \core_course\management\helper::action_category_move_courses_into($sub1, $sub2, array($course2->id, $course3->id))
        );

        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(1, $cat2->get_courses_count());
        $this->assertEquals(0, $sub1->get_courses_count());
        $this->assertEquals(2, $sub2->get_courses_count());

        $courses = $DB->get_records('course', array('category' => $sub2->id), 'id');
        $this->assertEquals(array((int)$course2->id, (int)$course3->id), array_keys($courses));

        // Move the courses in sub category 2 back into to sub category 1.
        $this->assertTrue(
            \core_course\management\helper::action_category_move_courses_into($sub2, $sub1, array($course2->id, $course3->id))
        );

        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(1, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(0, $sub2->get_courses_count());

        $courses = $DB->get_records('course', array('category' => $sub1->id), 'id');
        $this->assertEquals(array((int)$course2->id, (int)$course3->id), array_keys($courses));

        // Try moving just one course.
        $this->assertTrue(
            \core_course\management\helper::action_category_move_courses_into($cat2, $sub2, array($course4->id))
        );
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(0, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(1, $sub2->get_courses_count());
        $courses = $DB->get_records('course', array('category' => $sub2->id), 'id');
        $this->assertEquals(array((int)$course4->id), array_keys($courses));

        // Try moving a course from a category its not part of.
        try {
            \core_course\management\helper::action_category_move_courses_into($cat2, $sub2, array($course4->id));
            $this->fail('Moved a course from a category it wasn\'t within');
        } catch (\moodle_exception $exception) {
            // Check that everything is as it was.
            $this->assertEquals(1, $cat1->get_courses_count());
            $this->assertEquals(0, $cat2->get_courses_count());
            $this->assertEquals(2, $sub1->get_courses_count());
            $this->assertEquals(1, $sub2->get_courses_count());
        }

        // Now try that again with two courses, one of which is in the right place.
        try {
            \core_course\management\helper::action_category_move_courses_into($cat2, $sub2, array($course4->id, $course1->id));
            $this->fail('Moved a course from a category it wasn\'t within');
        } catch (\moodle_exception $exception) {
            // Check that everything is as it was. Nothing should have been moved.
            $this->assertEquals(1, $cat1->get_courses_count());
            $this->assertEquals(0, $cat2->get_courses_count());
            $this->assertEquals(2, $sub1->get_courses_count());
            $this->assertEquals(1, $sub2->get_courses_count());
        }

        // Current state:
        // * $cat1 => $course1
        //    * $sub1 => $course2, $course3
        //    * $sub2 => $course4
        // * $cat2 =>.

        // Prevent the user from being able to move into $sub2.
        $sub2cap = course_capability_assignment::prohibit(self::CATEGORY_MANAGE, $roleid, $sub2->get_context()->id);
        $sub2 = core_course_category::get($sub2->id);
        // Suppress debugging messages for a moment.
        $olddebug = $CFG->debug;
        $CFG->debug = 0;

        // Try to move a course into sub2. This shouldn't be possible because you should always be able to undo what you've done.
        // Try moving just one course.
        try {
            \core_course\management\helper::action_category_move_courses_into($sub1, $sub2, array($course2->id));
            $this->fail('Invalid move of course between categories, action can\'t be undone.');
        } catch (\moodle_exception $ex) {
            $this->assertEquals(get_string('cannotmovecourses', 'error'), $ex->getMessage());
        }
        // Nothing should have changed.
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(0, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(1, $sub2->get_courses_count());

        // Now try moving a course out of sub2. Again should not be possible.
        // Try to move a course into sub2. This shouldn't be possible because you should always be able to undo what you've done.
        // Try moving just one course.
        try {
            \core_course\management\helper::action_category_move_courses_into($sub2, $cat2, array($course4->id));
            $this->fail('Invalid move of course between categories, action can\'t be undone.');
        } catch (\moodle_exception $ex) {
            $this->assertEquals(get_string('cannotmovecourses', 'error'), $ex->getMessage());
        }
        // Nothing should have changed.
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(0, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(1, $sub2->get_courses_count());

        $CFG->debug = $olddebug;
    }

    /**
     * Test moving a categories up and down.
     */
    public function test_action_category_movedown_and_moveup(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $parent = $generator->create_category();
        $cat1 = $generator->create_category(array('parent' => $parent->id, 'name' => 'One'));
        $cat2 = $generator->create_category(array('parent' => $parent->id, 'name' => 'Two'));
        $cat3 = $generator->create_category(array('parent' => $parent->id, 'name' => 'Three'));

        $syscontext = \context_system::instance();
        list($user, $roleid) = $this->get_user_objects($generator, $syscontext->id);
        course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $syscontext->id);

        // Check everything is where we expect it to be.
        $this->assertEquals(
            array('One', 'Two', 'Three'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Move the top category down one.
        $this->assertTrue(\core_course\management\helper::action_category_change_sortorder_down_one($cat1));
        // Reload out objects.
        $cat1 = core_course_category::get($cat1->id);
        $cat2 = core_course_category::get($cat2->id);
        $cat3 = core_course_category::get($cat3->id);
        // Verify that caches were cleared.
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat1->id)), $cat1->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat2->id)), $cat2->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat3->id)), $cat3->sortorder);
        // Verify sorting.
        $this->assertEquals(
            array('Two', 'One', 'Three'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Move the bottom category up one.
        $this->assertTrue(\core_course\management\helper::action_category_change_sortorder_up_one($cat3));
        // Reload out objects.
        $cat1 = core_course_category::get($cat1->id);
        $cat2 = core_course_category::get($cat2->id);
        $cat3 = core_course_category::get($cat3->id);
        // Verify that caches were cleared.
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat1->id)), $cat1->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat2->id)), $cat2->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat3->id)), $cat3->sortorder);
        // Verify sorting.
        $this->assertEquals(
            array('Two', 'Three', 'One'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Move the top category down one.
        $this->assertTrue(\core_course\management\helper::action_category_change_sortorder_down_one_by_id($cat2->id));
        $this->assertEquals(
            array('Three', 'Two', 'One'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Move the top category down one.
        $this->assertTrue(\core_course\management\helper::action_category_change_sortorder_up_one_by_id($cat1->id));
        $this->assertEquals(
            array('Three', 'One', 'Two'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Reload out objects the above actions will have caused the objects to become stale.
        $cat1 = core_course_category::get($cat1->id);
        $cat2 = core_course_category::get($cat2->id);
        $cat3 = core_course_category::get($cat3->id);
        // Verify that caches were cleared.
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat1->id)), $cat1->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat2->id)), $cat2->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat3->id)), $cat3->sortorder);
        // Verify sorting.

        // Test moving the top category up one. Nothing should change but it should return false.
        $this->assertFalse(\core_course\management\helper::action_category_change_sortorder_up_one($cat3));
        // Reload out objects.
        $cat1 = core_course_category::get($cat1->id);
        $cat2 = core_course_category::get($cat2->id);
        $cat3 = core_course_category::get($cat3->id);
        // Verify that caches were cleared.
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat1->id)), $cat1->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat2->id)), $cat2->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat3->id)), $cat3->sortorder);
        // Verify sorting.
        $this->assertEquals(
            array('Three', 'One', 'Two'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Test moving the bottom category down one. Nothing should change but it should return false.
        $this->assertFalse(\core_course\management\helper::action_category_change_sortorder_down_one($cat2));
        // Reload out objects.
        $cat1 = core_course_category::get($cat1->id);
        $cat2 = core_course_category::get($cat2->id);
        $cat3 = core_course_category::get($cat3->id);
        // Verify that caches were cleared.
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat1->id)), $cat1->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat2->id)), $cat2->sortorder);
        $this->assertEquals($DB->get_field('course_categories', 'sortorder', array('id' => $cat3->id)), $cat3->sortorder);
        // Verify sorting.
        $this->assertEquals(
            array('Three', 'One', 'Two'),
            array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
        );

        // Prevent moving on the parent.
        course_capability_assignment::prevent(self::CATEGORY_MANAGE, $roleid, $parent->get_context()->id);
        try {
            \core_course\management\helper::action_category_change_sortorder_up_one($cat1);
        } catch (\moodle_exception $exception) {
            // Check everything is still where it should be.
            $this->assertEquals(
                array('Three', 'One', 'Two'),
                array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
            );
        }
        try {
            \core_course\management\helper::action_category_change_sortorder_down_one($cat3);
        } catch (\moodle_exception $exception) {
            // Check everything is still where it should be.
            $this->assertEquals(
                array('Three', 'One', 'Two'),
                array_keys($DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder', 'name'))
            );
        }
    }

    /**
     * Test resorting of courses within a category.
     *
     * \core_course\management\helper::action_category_resort_courses
     */
    public function test_action_category_resort_courses(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course1 = $generator->create_course(array('category' => $category->id, 'fullname' => 'Experimental Chemistry',
            'shortname' => 'Course A', 'idnumber' => '10001'));
        $course2 = $generator->create_course(array('category' => $category->id, 'fullname' => 'Learn to program: Jade',
            'shortname' => 'Beginning Jade', 'idnumber' => '10003'));
        $course3 = $generator->create_course(array('category' => $category->id, 'fullname' => 'Advanced algebra',
            'shortname' => 'Advanced algebra', 'idnumber' => '10002'));
        $syscontext = \context_system::instance();

        // Update category object from DB so the course count is correct.
        $category = core_course_category::get($category->id);

        list($user, $roleid) = $this->get_user_objects($generator, $syscontext->id);
        $caps = course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $syscontext->id);

        // Check that sort order in the DB matches what we've got in the cache.
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Resort by fullname.
        \core_course\management\helper::action_category_resort_courses($category, 'fullname');
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course3->id, $course1->id, $course2->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Resort by shortname.
        \core_course\management\helper::action_category_resort_courses($category, 'shortname');
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course3->id, $course2->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Resort by idnumber.
        \core_course\management\helper::action_category_resort_courses($category, 'idnumber');
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course3->id, $course2->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Try with a field that cannot be sorted on.
        try {
            \core_course\management\helper::action_category_resort_courses($category, 'category');
            $this->fail('Category courses resorted by invalid sort field.');
        } catch (\coding_exception $exception) {
            // Test things are as they were before.
            $courses = $category->get_courses();
            $this->assertIsArray($courses);
            $this->assertEquals(array($course1->id, $course3->id, $course2->id), array_keys($courses));
            $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
            $this->assertEquals(array_keys($dbcourses), array_keys($courses));
        }

        // Try with a completely bogus field.
        try {
            \core_course\management\helper::action_category_resort_courses($category, 'monkeys');
            $this->fail('Category courses resorted by completely ridiculous field.');
        } catch (\coding_exception $exception) {
            // Test things are as they were before.
            $courses = $category->get_courses();
            $this->assertIsArray($courses);
            $this->assertEquals(array($course1->id, $course3->id, $course2->id), array_keys($courses));
            $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
            $this->assertEquals(array_keys($dbcourses), array_keys($courses));
        }

        // Prohibit resorting.
        $caps->assign(CAP_PROHIBIT);
        // Refresh our coursecat object.
        $category = core_course_category::get($category->id);

        // We should no longer have permission to do this. Test it out!
        try {
            \core_course\management\helper::action_category_resort_courses($category, 'shortname');
            $this->fail('Courses sorted without having the required permission.');
        } catch (\moodle_exception $exception) {
            // Check its the right exception.
            $this->assertEquals('core_course_category::can_resort', $exception->debuginfo);
            // Test things are as they were before.
            $courses = $category->get_courses();
            $this->assertIsArray($courses);
            $this->assertEquals(array($course1->id, $course3->id, $course2->id), array_keys($courses));
            $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder');
            $this->assertEquals(array_keys($dbcourses), array_keys($courses));
        }
    }

    /**
     * Tests resorting sub categories of a course.
     *
     * \core_course\management\helper::action_category_resort_courses
     */
    public function test_action_category_resort_subcategories(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $parent = $generator->create_category();
        $cat1 = $generator->create_category(array('parent' => $parent->id, 'name' => 'School of Science', 'idnumber' => '10001'));
        $cat2 = $generator->create_category(array('parent' => $parent->id, 'name' => 'School of Commerce', 'idnumber' => '10003'));
        $cat3 = $generator->create_category(array('parent' => $parent->id, 'name' => 'School of Arts', 'idnumber' => '10002'));

        $syscontext = \context_system::instance();
        list($user, $roleid) = $this->get_user_objects($generator, $syscontext->id);
        $caps = course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $syscontext->id);

        $categories = $parent->get_children();
        $this->assertIsArray($categories);
        $dbcategories = $DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder');
        $this->assertEquals(array_keys($dbcategories), array_keys($categories));

        // Test sorting by name.
        \core_course\management\helper::action_category_resort_subcategories($parent, 'name');
        $categories = $parent->get_children();
        $this->assertIsArray($categories);
        $this->assertEquals(array($cat3->id, $cat2->id, $cat1->id), array_keys($categories));
        $dbcategories = $DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder');
        $this->assertEquals(array_keys($dbcategories), array_keys($categories));

        // Test sorting by idnumber.
        \core_course\management\helper::action_category_resort_subcategories($parent, 'idnumber');
        $categories = $parent->get_children();
        $this->assertIsArray($categories);
        $this->assertEquals(array($cat1->id, $cat3->id, $cat2->id), array_keys($categories));
        $dbcategories = $DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder');
        $this->assertEquals(array_keys($dbcategories), array_keys($categories));

        // Try with an invalid field.
        try {
            \core_course\management\helper::action_category_resort_subcategories($parent, 'summary');
            $this->fail('Categories resorted by invalid field.');
        } catch (\coding_exception $exception) {
            // Check that nothing was changed.
            $categories = $parent->get_children();
            $this->assertIsArray($categories);
            $this->assertEquals(array($cat1->id, $cat3->id, $cat2->id), array_keys($categories));
            $dbcategories = $DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder');
            $this->assertEquals(array_keys($dbcategories), array_keys($categories));
        }

        // Try with a completely bogus field.
        try {
            \core_course\management\helper::action_category_resort_subcategories($parent, 'monkeys');
            $this->fail('Categories resorted by completely bogus field.');
        } catch (\coding_exception $exception) {
            // Check that nothing was changed.
            $categories = $parent->get_children();
            $this->assertIsArray($categories);
            $this->assertEquals(array($cat1->id, $cat3->id, $cat2->id), array_keys($categories));
            $dbcategories = $DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder');
            $this->assertEquals(array_keys($dbcategories), array_keys($categories));
        }

        // Test resorting the top level category (puke).
        $topcat = core_course_category::get(0);
        \core_course\management\helper::action_category_resort_subcategories($topcat, 'name');
        $categories = $topcat->get_children();
        $this->assertIsArray($categories);
        $dbcategories = $DB->get_records('course_categories', array('parent' => '0'), 'sortorder');
        $this->assertEquals(array_keys($dbcategories), array_keys($categories));

        // Prohibit resorting.
        $caps->assign(CAP_PROHIBIT);
        // Refresh our coursecat object.
        $parent = core_course_category::get($parent->id);

        // We should no longer have permission to do this. Test it out!
        try {
            \core_course\management\helper::action_category_resort_subcategories($parent, 'idnumber');
            $this->fail('Categories sorted without having the required permission.');
        } catch (\moodle_exception $exception) {
            // Check its the right exception.
            $this->assertEquals('core_course_category::can_resort', $exception->debuginfo);
            // Test things are as they were before.
            $categories = $parent->get_children();
            $this->assertIsArray($categories);
            $this->assertEquals(array($cat1->id, $cat3->id, $cat2->id), array_keys($categories));
            $dbcategories = $DB->get_records('course_categories', array('parent' => $parent->id), 'sortorder');
            $this->assertEquals(array_keys($dbcategories), array_keys($categories));
        }
    }

    /**
     * Test hiding and showing of a course.
     *
     * @see \core_course\management\helper::action_course_hide
     * @see \core_course\management\helper::action_course_show
     */
    public function test_action_course_hide_show(): void {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course = $generator->create_course();

        $coursecontext = \context_course::instance($course->id);

        list($user, $roleid) = $this->get_user_objects($generator, $coursecontext->id);
        $caps = array(self::COURSE_VIEW, self::COURSE_VIEWHIDDEN);
        $assignment = course_capability_assignment::allow($caps, $roleid, $coursecontext->id);

        $course = new core_course_list_element(get_course($course->id));

        // Check it is set to what we think it is.
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        // Test hiding the course.
        $this->assertTrue(\core_course\management\helper::action_course_hide($course));
        // Refresh the course.
        $course = new core_course_list_element(get_course($course->id));
        $this->assertEquals('0', $course->visible);
        $this->assertEquals('0', $course->visibleold);

        // Test hiding the course again.
        $this->assertTrue(\core_course\management\helper::action_course_hide($course));
        // Refresh the course.
        $course = new core_course_list_element(get_course($course->id));
        $this->assertEquals('0', $course->visible);
        $this->assertEquals('0', $course->visibleold);

        // Test showing the course.
        $this->assertTrue(\core_course\management\helper::action_course_show($course));
        // Refresh the course.
        $course = new core_course_list_element(get_course($course->id));
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        // Test showing the course again. Shouldn't change anything.
        $this->assertTrue(\core_course\management\helper::action_course_show($course));
        // Refresh the course.
        $course = new core_course_list_element(get_course($course->id));
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        // Revoke the permissions.
        $assignment->revoke();
        $course = new core_course_list_element(get_course($course->id));

        try {
            \core_course\management\helper::action_course_show($course);
        } catch (\moodle_exception $exception) {
            $this->assertEquals('core_course_list_element::can_change_visbility', $exception->debuginfo);
        }
    }

    /**
     * Test hiding and showing of a course.
     *
     * @see \core_course\management\helper::action_course_hide_by_record
     * @see \core_course\management\helper::action_course_show_by_record
     */
    public function test_action_course_hide_show_by_record(): void {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course = $generator->create_course();

        $coursecontext = \context_course::instance($course->id);

        list($user, $roleid) = $this->get_user_objects($generator, $coursecontext->id);
        $caps = array(self::COURSE_VIEW, self::COURSE_VIEWHIDDEN);
        $assignment = course_capability_assignment::allow($caps, $roleid, $coursecontext->id);

        $course = get_course($course->id);

        // Check it is set to what we think it is.
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        // Test hiding the course.
        $this->assertTrue(\core_course\management\helper::action_course_hide_by_record($course));
        // Refresh the course.
        $course = get_course($course->id);
        $this->assertEquals('0', $course->visible);
        $this->assertEquals('0', $course->visibleold);

        // Test hiding the course again. Shouldn't change anything.
        $this->assertTrue(\core_course\management\helper::action_course_hide_by_record($course));
        // Refresh the course.
        $course = get_course($course->id);
        $this->assertEquals('0', $course->visible);
        $this->assertEquals('0', $course->visibleold);

        // Test showing the course.
        $this->assertTrue(\core_course\management\helper::action_course_show_by_record($course));
        // Refresh the course.
        $course = get_course($course->id);
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        // Test showing the course again. Shouldn't change anything.
        $this->assertTrue(\core_course\management\helper::action_course_show_by_record($course));
        // Refresh the course.
        $course = get_course($course->id);
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        // Revoke the permissions.
        $assignment->revoke();
        $course = get_course($course->id);

        try {
            \core_course\management\helper::action_course_show_by_record($course);
        } catch (\moodle_exception $exception) {
            $this->assertEquals('core_course_list_element::can_change_visbility', $exception->debuginfo);
        }
    }

    /**
     * Tests moving a course up and down by one.
     */
    public function test_action_course_movedown_and_moveup(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course3 = $generator->create_course(array('category' => $category->id));
        $course2 = $generator->create_course(array('category' => $category->id));
        $course1 = $generator->create_course(array('category' => $category->id));
        $context = $category->get_context();

        // Update category object from DB so the course count is correct.
        $category = core_course_category::get($category->id);

        list($user, $roleid) = $this->get_user_objects($generator, $context->id);
        $caps = course_capability_assignment::allow(self::CATEGORY_MANAGE, $roleid, $context->id);

        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Move a course down.
        $this->assertTrue(
            \core_course\management\helper::action_course_change_sortorder_down_one(
                new core_course_list_element(get_course($course1->id)), $category)
        );
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Move a course up.
        $this->assertTrue(
            \core_course\management\helper::action_course_change_sortorder_up_one(
                new core_course_list_element(get_course($course3->id)), $category)
        );
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Move a course down by record.
        $this->assertTrue(
            \core_course\management\helper::action_course_change_sortorder_down_one_by_record(get_course($course2->id), $category)
        );
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course3->id, $course2->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Move a course up by record.
        $this->assertTrue(
            \core_course\management\helper::action_course_change_sortorder_up_one_by_record(get_course($course2->id), $category)
        );
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Try move the bottom course down. This should return false and nothing changes.
        $this->assertFalse(
            \core_course\management\helper::action_course_change_sortorder_down_one(
                new core_course_list_element(get_course($course1->id)), $category)
        );
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Try move the top course up. This should return false and nothing changes.
        $this->assertFalse(
            \core_course\management\helper::action_course_change_sortorder_up_one(
                new core_course_list_element(get_course($course2->id)), $category)
        );
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Prohibit the ability to move.
        $caps->assign(CAP_PROHIBIT);
        // Reload the category.
        $category = core_course_category::get($category->id);

        try {
            \core_course\management\helper::action_course_change_sortorder_down_one(
                new core_course_list_element(get_course($course2->id)), $category);
            $this->fail('Course moved without having the required permissions.');
        } catch (\moodle_exception $exception) {
            // Check nothing has changed.
            $courses = $category->get_courses();
            $this->assertIsArray($courses);
            $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
            $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
            $this->assertEquals(array_keys($dbcourses), array_keys($courses));
        }
    }

    /**
     * Tests the fetching of actions for a category.
     */
    public function test_get_category_listitem_actions(): void {
        global $PAGE;
        $this->resetAfterTest(true);

        $PAGE->set_url(new \moodle_url('/course/management.php'));

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $context = \context_system::instance();
        list($user, $roleid) = $this->get_user_objects($generator, $context->id);
        course_capability_assignment::allow(array(
            self::CATEGORY_MANAGE,
            self::CATEGORY_VIEWHIDDEN,
            'moodle/role:assign',
            'moodle/cohort:view',
            'moodle/filter:manage'
        ), $roleid, $context->id);

        $actions = \core_course\management\helper::get_category_listitem_actions($category);
        $this->assertIsArray($actions);
        $this->assertArrayHasKey('edit', $actions);
        $this->assertArrayHasKey('hide', $actions);
        $this->assertArrayHasKey('show', $actions);
        $this->assertArrayHasKey('moveup', $actions);
        $this->assertArrayHasKey('movedown', $actions);
        $this->assertArrayHasKey('delete', $actions);
        $this->assertArrayHasKey('permissions', $actions);
        $this->assertArrayHasKey('cohorts', $actions);
        $this->assertArrayHasKey('filters', $actions);
    }

    /**
     * Tests fetching the course actions.
     */
    public function test_get_course_detail_actions(): void {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course = $generator->create_course();
        $context = \context_system::instance();
        list($user, $roleid) = $this->get_user_objects($generator, $context->id);
        $generator->enrol_user($user->id, $course->id, $roleid);
        course_capability_assignment::allow(array(
            self::COURSE_VIEW,
            self::COURSE_VIEWHIDDEN,
            'moodle/course:update',
            'moodle/course:enrolreview',
            'moodle/course:delete',
            'moodle/backup:backupcourse',
            'moodle/restore:restorecourse'
        ), $roleid, $context->id);

        $actions = \core_course\management\helper::get_course_detail_actions(new core_course_list_element($course));
        $this->assertIsArray($actions);
        $this->assertArrayHasKey('view', $actions);
        $this->assertArrayHasKey('edit', $actions);
        $this->assertArrayHasKey('enrolledusers', $actions);
        $this->assertArrayHasKey('delete', $actions);
        $this->assertArrayHasKey('hide', $actions);
        $this->assertArrayHasKey('backup', $actions);
        $this->assertArrayHasKey('restore', $actions);
    }

    /**
     * Test fetching course details.
     */
    public function test_get_course_detail_array(): void {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course = $generator->create_course();
        $context = \context_system::instance();
        list($user, $roleid) = $this->get_user_objects($generator, $context->id);
        $generator->enrol_user($user->id, $course->id, $roleid);
        course_capability_assignment::allow(array(
            self::COURSE_VIEW,
            self::COURSE_VIEWHIDDEN,
            'moodle/course:update',
            'moodle/course:enrolreview',
            'moodle/course:delete',
            'moodle/backup:backupcourse',
            'moodle/restore:restorecourse',
            'moodle/site:accessallgroups'
        ), $roleid, $context->id);

        $details = \core_course\management\helper::get_course_detail_array(new core_course_list_element($course));
        $this->assertIsArray($details);
        $this->assertArrayHasKey('format', $details);
        $this->assertArrayHasKey('fullname', $details);
        $this->assertArrayHasKey('shortname', $details);
        $this->assertArrayHasKey('idnumber', $details);
        $this->assertArrayHasKey('category', $details);
        $this->assertArrayHasKey('groupings', $details);
        $this->assertArrayHasKey('groups', $details);
        $this->assertArrayHasKey('roleassignments', $details);
        $this->assertArrayHasKey('enrolmentmethods', $details);
        $this->assertArrayHasKey('sections', $details);
        $this->assertArrayHasKey('modulesused', $details);
    }

    public function test_move_courses_into_category(): void {
        global $DB, $CFG;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category();
        $sub1 = $generator->create_category(array('parent' => $cat1->id));
        $sub2 = $generator->create_category(array('parent' => $cat1->id));
        $course1 = $generator->create_course(array('category' => $cat1->id));
        $course2 = $generator->create_course(array('category' => $sub1->id));
        $course3 = $generator->create_course(array('category' => $sub1->id));
        $course4 = $generator->create_course(array('category' => $cat2->id));

        $syscontext = \context_system::instance();

        list($user, $roleid) = $this->get_user_objects($generator, $syscontext->id);

        course_capability_assignment::allow(array(self::CATEGORY_MANAGE, self::CATEGORY_VIEWHIDDEN), $roleid, $syscontext->id);

        // Check they are where we think they are.
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(1, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(0, $sub2->get_courses_count());

        // Move the courses in sub category 1 to sub category 2.
        $this->assertTrue(
            \core_course\management\helper::move_courses_into_category($sub2->id, array($course2->id, $course3->id))
        );

        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(1, $cat2->get_courses_count());
        $this->assertEquals(0, $sub1->get_courses_count());
        $this->assertEquals(2, $sub2->get_courses_count());

        $courses = $DB->get_records('course', array('category' => $sub2->id), 'id');
        $this->assertEquals(array((int)$course2->id, (int)$course3->id), array_keys($courses));

        // Move the courses in sub category 2 back into to sub category 1.
        $this->assertTrue(
            \core_course\management\helper::move_courses_into_category($sub1->id, array($course2->id, $course3->id))
        );

        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(1, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(0, $sub2->get_courses_count());

        $courses = $DB->get_records('course', array('category' => $sub1->id), 'id');
        $this->assertEquals(array((int)$course2->id, (int)$course3->id), array_keys($courses));

        // Try moving just one course.
        $this->assertTrue(
            \core_course\management\helper::move_courses_into_category($sub2->id, $course4->id)
        );
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(0, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(1, $sub2->get_courses_count());
        $courses = $DB->get_records('course', array('category' => $sub2->id), 'id');
        $this->assertEquals(array((int)$course4->id), array_keys($courses));

        // Current state:
        // * $cat1 => $course1
        //    * $sub1 => $course2, $course3
        //    * $sub2 => $course4
        // * $cat2 =>.

        // Prevent the user from being able to move into $sub2.
        $sub2cap = course_capability_assignment::prohibit(self::CATEGORY_MANAGE, $roleid, $sub2->get_context()->id);
        $sub2 = core_course_category::get($sub2->id);
        // Suppress debugging messages for a moment.
        $olddebug = $CFG->debug;
        $CFG->debug = 0;

        // Try to move a course into sub2. This shouldn't be possible because you should always be able to undo what you've done.
        // Try moving just one course.
        try {
            \core_course\management\helper::move_courses_into_category($sub2->id, array($course2->id));
            $this->fail('Invalid move of course between categories, action can\'t be undone.');
        } catch (\moodle_exception $ex) {
            $this->assertEquals(get_string('cannotmovecourses', 'error'), $ex->getMessage());
        }
        // Nothing should have changed.
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(0, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(1, $sub2->get_courses_count());

        // Now try moving a course out of sub2. Again should not be possible.
        // Try to move a course into sub2. This shouldn't be possible because you should always be able to undo what you've done.
        // Try moving just one course.
        try {
            \core_course\management\helper::move_courses_into_category($cat2->id, array($course4->id));
            $this->fail('Invalid move of course between categories, action can\'t be undone.');
        } catch (\moodle_exception $ex) {
            $this->assertEquals(get_string('cannotmovecourses', 'error'), $ex->getMessage());
        }
        // Nothing should have changed.
        $this->assertEquals(1, $cat1->get_courses_count());
        $this->assertEquals(0, $cat2->get_courses_count());
        $this->assertEquals(2, $sub1->get_courses_count());
        $this->assertEquals(1, $sub2->get_courses_count());

        $CFG->debug = $olddebug;
    }

}
