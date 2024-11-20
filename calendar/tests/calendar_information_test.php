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

namespace core_calendar;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/helpers.php');

/**
 * Unit tests for calendar_information.
 *
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_information_test extends \advanced_testcase {

    /**
     * Helper to mock a course and category structure.
     *
     * @return array
     */
    protected function mock_structure() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $categories = [];
        $courses = [];

        $categories['A'] = $generator->create_category(['name' => 'A']);
        $courses['A.1'] = $generator->create_course(['category' => $categories['A']->id]);
        $courses['A.2'] = $generator->create_course(['category' => $categories['A']->id]);
        $categories['A1'] = $generator->create_category(['name' => 'A1', 'parent' => $categories['A']->id]);
        $courses['A1.1'] = $generator->create_course(['category' => $categories['A1']->id]);
        $courses['A1.2'] = $generator->create_course(['category' => $categories['A1']->id]);
        $categories['A1i'] = $generator->create_category(['name' => 'A1i', 'parent' => $categories['A1']->id]);
        $categories['A1ii'] = $generator->create_category(['name' => 'A1ii', 'parent' => $categories['A1']->id]);
        $categories['A2'] = $generator->create_category(['name' => 'A2', 'parent' => $categories['A']->id]);
        $courses['A2.1'] = $generator->create_course(['category' => $categories['A2']->id]);
        $courses['A2.2'] = $generator->create_course(['category' => $categories['A2']->id]);
        $categories['A2i'] = $generator->create_category(['name' => 'A2i', 'parent' => $categories['A2']->id]);
        $categories['A2ii'] = $generator->create_category(['name' => 'A2ii', 'parent' => $categories['A2']->id]);
        $categories['B'] = $generator->create_category(['name' => 'B']);
        $courses['B.1'] = $generator->create_course(['category' => $categories['B']->id]);
        $courses['B.2'] = $generator->create_course(['category' => $categories['B']->id]);
        $categories['B1'] = $generator->create_category(['name' => 'B1', 'parent' => $categories['B']->id]);
        $categories['B1i'] = $generator->create_category(['name' => 'B1i', 'parent' => $categories['B1']->id]);
        $categories['B1ii'] = $generator->create_category(['name' => 'B1ii', 'parent' => $categories['B1']->id]);
        $categories['B2'] = $generator->create_category(['name' => 'B2', 'parent' => $categories['B']->id]);
        $categories['B2i'] = $generator->create_category(['name' => 'B2i', 'parent' => $categories['B2']->id]);
        $categories['B2ii'] = $generator->create_category(['name' => 'B2ii', 'parent' => $categories['B2']->id]);
        $categories['C'] = $generator->create_category(['name' => 'C']);

        return [$courses, $categories];
    }

    /**
     * Given a user has no enrolments.
     * And I ask for the site information.
     * Then I should see the site.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_no_enrolment(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(0, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given a user has no enrolments.
     * And I ask for a category.
     * Then I should see the category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site course.
     * And I should see no other courses.
     */
    public function test_site_visibility_no_enrolment_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        $category = $categories['A1'];
        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }

    /**
     * Given a user has a role assignment to manage a category.
     * And I ask for the site information.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site course.
     * And I should see no other courses.
     */
    public function test_site_visibility_category_manager_site(): void {
        global $DB;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $category = $categories['A1'];

        $roles = $DB->get_records('role', [], '', 'shortname, id');
        $generator->role_assign($roles['manager']->id, $user->id, \context_coursecat::instance($category->id));

        $this->setUser($user);

        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given a user has a role assignment to manage a category.
     * And I ask for that category.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site course.
     * And I should see no other courses.
     */
    public function test_site_visibility_category_manager_own_category(): void {
        global $DB;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $category = $categories['A1'];

        $roles = $DB->get_records('role', [], '', 'shortname, id');
        $generator->role_assign($roles['manager']->id, $user->id, \context_coursecat::instance($category->id));

        $this->setUser($user);

        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }

    /**
     * Given a user has a role assignment to manage a category.
     * And I ask for the parent of that category.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site course.
     * And I should see no other courses.
     */
    public function test_site_visibility_category_manager_parent_category(): void {
        global $DB;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $category = $categories['A1'];

        $roles = $DB->get_records('role', [], '', 'shortname, id');
        $generator->role_assign($roles['manager']->id, $user->id, \context_coursecat::instance($category->id));

        $this->setUser($user);

        $calendar = \calendar_information::create(time(), SITEID, $category->parent);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(7, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->parent), $calendar->context);
    }

    /**
     * Given a user has a role assignment to manage a category.
     * And I ask for a child of that category.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site course.
     * And I should see no other courses.
     */
    public function test_site_visibility_category_manager_child_category(): void {
        global $DB;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $enrolledcategory = $categories['A1'];
        $category = $categories['A1i'];

        $roles = $DB->get_records('role', [], '', 'shortname, id');
        $generator->role_assign($roles['manager']->id, $user->id, \context_coursecat::instance($enrolledcategory->id));

        $this->setUser($user);

        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(3, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in a single course.
     * And I ask for the site information.
     * Then I should see the site.
     * And I should see the course I am enrolled in.
     * And I should see the category that my enrolled course is in.
     * And I should see the parents of the category that my enrolled course is in.
     */
    public function test_site_visibility_single_course_site(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $course->id);

        $this->setUser($user);

        // Viewing the site as a whole.
        // Should see all courses that this user is enrolled in, and their
        // categories, and those categories parents.
        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(2, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given a user has an enrolment in a single course.
     * And I ask for the course information.
     * Then I should see the site.
     * And I should see that course.
     * And I should see the category of that course.
     * And I should see the parents of that course category.
     */
    public function test_site_visibility_single_course_course_course(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $course->id);

        $this->setUser($user);
        $time = time();

        // Viewing the course calendar.
        // Should see just this course, and all parent categories.
        $calendar = \calendar_information::create($time, $course->id, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(2, $calendar->categories);
        $this->assertEquals($course->id, $calendar->courseid);
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_course::instance($course->id), $calendar->context);

        // Viewing the course calendar while specifying the category too.
        // The category is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $course->id, $category->id);
        $this->assertEquals($calendar, $calendarwithcategory);

        // Viewing the course calendar while specifying the wrong category.
        // The category is essentially ignored. No change expected.
        $calendarwithwrongcategory = \calendar_information::create($time, $course->id, $wrongcategory->id);
        $this->assertEquals($calendar, $calendarwithwrongcategory);
    }

    /**
     * Given a user has an enrolment in a single course.
     * And I ask for the category information for the category my course is in.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site.
     * And I should see my course.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_single_course_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $generator->enrol_user($user->id, $course->id);

        $this->setUser($user);

        // Viewing the category calendar.
        // Should see all courses that this user is enrolled in within this
        // category, plus the site course, plus the category that course is
        // in and it's parents, and it's children.
        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in a single course.
     * And I ask for the category information for the parent of the category my course is in.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site.
     * And I should see my course.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_single_course_parent_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $generator->enrol_user($user->id, $course->id);

        $this->setUser($user);

        // Viewing the category calendar.
        // Should see all courses that this user is enrolled in within this
        // category, plus the site course, plus the category that course is
        // in and it's parents, and it's children.
        $calendar = \calendar_information::create(time(), SITEID, $category->parent);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(7, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A2']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A2i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A2ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->parent), $calendar->context);
    }

    /**
     * Given a user has an enrolment in a single course.
     * And I ask for the category information for the sibling of the category my course is in.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site.
     * And I should see my course.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_single_course_sibling_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $courses['A1.1'];
        $category = $categories['A2'];
        $generator->enrol_user($user->id, $course->id);

        $this->setUser($user);

        // Viewing the category calendar.
        // Should see all courses that this user is enrolled in within this
        // category, plus the site course, plus the category that course is
        // in and it's parents, and it's children.
        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A2']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A2i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A2ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in a single course.
     * And I ask for the category information for a different category to the one my course is in.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site.
     * And I should see not see my course.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_single_course_different_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $course->id);

        $this->setUser($user);

        // Viewing the category calendar for a category the user doesn't have any enrolments in.
        // Should see that category, and all categories underneath it.
        $calendar = \calendar_information::create(time(), SITEID, $wrongcategory->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['B']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['B1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['B1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['B1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($wrongcategory->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in two courses in the same category.
     * And I ask for the site information.
     * Then I should see the site.
     * And I should see the course I am enrolled in.
     * And I should see the category that my enrolled course is in.
     * And I should see the parents of the category that my enrolled course is in.
     */
    public function test_site_visibility_two_courses_one_category_site(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['A.2'];
        $category = \core_course_category::get($coursea->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);

        // Viewing the site azs a whole.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(3, $calendar->courses);
        $this->assertCount(1, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($courseb->id, array_flip($calendar->courses));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given a user has an enrolment in two courses in the same category.
     * And I ask for the course information.
     * Then I should see the site.
     * And I should see that course.
     * And I should see the category that my enrolled courses are in.
     * And I should see the parents of the category that my enrolled course are in.
     */
    public function test_site_visibility_two_courses_one_category_course(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['A.2'];
        $category = \core_course_category::get($coursea->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);
        $time = time();

        // Viewing the course calendar.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create($time, $coursea->id, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(1, $calendar->categories);
        $this->assertEquals($coursea->id, $calendar->courseid);
        $this->assertArrayHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_course::instance($coursea->id), $calendar->context);

        // Viewing the course calendar while specifying the category too.
        // The category is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $coursea->id, $category->id);
        $this->assertEquals($calendar, $calendarwithcategory);

        // Viewing the course calendar while specifying the wrong category.
        // The category is essentially ignored. No change expected.
        $calendarwithwrongcategory = \calendar_information::create($time, $coursea->id, $wrongcategory->id);
        $this->assertEquals($calendar, $calendarwithwrongcategory);
    }

    /**
     * Given a user has an enrolment in two courses in the same category.
     * And I ask for the course information of the second course.
     * Then I should see the site.
     * And I should see that course.
     * And I should see the category that my enrolled courses are in.
     * And I should see the parents of the category that my enrolled course are in.
     */
    public function test_site_visibility_two_courses_one_category_courseb(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['A.2'];
        $category = \core_course_category::get($coursea->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);
        $time = time();

        // Viewing the other course calendar.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create($time, $courseb->id, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(1, $calendar->categories);
        $this->assertEquals($courseb->id, $calendar->courseid);
        $this->assertArrayHasKey($courseb->id, array_flip($calendar->courses));
        $this->assertArrayNotHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_course::instance($courseb->id), $calendar->context);

        // Viewing the course calendar while specifying the category too.
        // The category is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $courseb->id, $category->id);
        $this->assertEquals($calendar, $calendarwithcategory);

        // Viewing the course calendar while specifying the wrong category.
        // The category is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $courseb->id, $wrongcategory->id);
        $this->assertEquals($calendar, $calendarwithcategory);
    }

    /**
     * Given a user has an enrolment in two courses in the same category.
     * And I ask for the category information.
     * Then I should see the site.
     * And I should see that course.
     * And I should see the category that my enrolled courses are in.
     * And I should see the parents of the category that my enrolled course are in.
     */
    public function test_site_visibility_two_courses_one_category_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['A.2'];
        $category = \core_course_category::get($coursea->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);

        // Viewing the category calendar.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(3, $calendar->courses);
        $this->assertCount(7, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($courseb->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in two courses in the same category.
     * And I ask for the categoy information of a different course.
     * Then I should see the site.
     * And I should see that course.
     * And I should see the category that my enrolled courses are in.
     * And I should see the parents of the category that my enrolled course are in.
     */
    public function test_site_visibility_two_courses_one_category_othercategory(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['A.2'];
        $category = \core_course_category::get($coursea->category);
        $wrongcategory = $categories['B1'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);

        // Viewing the category calendar for a category the user doesn't have any enrolments in.
        // Should see that category, and all categories underneath it.
        $calendar = \calendar_information::create(time(), SITEID, $wrongcategory->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_coursecat::instance($wrongcategory->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in two courses in the separate category.
     * And I ask for the site informatino.
     * Then I should see the site.
     * And I should see both course.
     * And I should see the categories that my enrolled courses are in.
     * And I should see the parents of those categories.
     */
    public function test_site_visibility_two_courses_two_categories_site(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['B.1'];
        $categorya = \core_course_category::get($coursea->category);
        $categoryb = \core_course_category::get($courseb->category);
        $wrongcategory = $categories['C'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);

        // Viewing the site azs a whole.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(3, $calendar->courses);
        $this->assertCount(2, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($courseb->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given a user has an enrolment in two courses in the separate category.
     * And I ask for the course information for one of those courses.
     * Then I should see the site.
     * And I should see one of the courses.
     * And I should see the categories that my enrolled courses are in.
     * And I should see the parents of those categories.
     */
    public function test_site_visibility_two_courses_two_categories_coursea(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['B.1'];
        $categorya = \core_course_category::get($coursea->category);
        $categoryb = \core_course_category::get($courseb->category);
        $wrongcategory = $categories['C'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);
        $time = time();

        // Viewing the course calendar.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create($time, $coursea->id, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(1, $calendar->categories);
        $this->assertEquals($coursea->id, $calendar->courseid);
        $this->assertArrayHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_course::instance($coursea->id), $calendar->context);

        // Viewing the course calendar while specifying the categorya too.
        // The categorya is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $coursea->id, $categorya->id);
        $this->assertEquals($calendar, $calendarwithcategory);

        // Viewing the course calendar while specifying the wrong categorya.
        // The categorya is essentially ignored. No change expected.
        $calendarwithwrongcategory = \calendar_information::create($time, $coursea->id, $wrongcategory->id);
        $this->assertEquals($calendar, $calendarwithwrongcategory);
    }

    /**
     * Given a user has an enrolment in two courses in the separate category.
     * And I ask for the course information for the second of those courses.
     * Then I should see the site.
     * And I should see one of the courses.
     * And I should see the categories that my enrolled courses are in.
     * And I should see the parents of those categories.
     */
    public function test_site_visibility_two_courses_two_categories_courseb(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['B.1'];
        $categorya = \core_course_category::get($coursea->category);
        $categoryb = \core_course_category::get($courseb->category);
        $wrongcategory = $categories['C'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);
        $time = time();

        // Viewing the other course calendar.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create($time, $courseb->id, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(1, $calendar->categories);
        $this->assertEquals($courseb->id, $calendar->courseid);
        $this->assertArrayHasKey($courseb->id, array_flip($calendar->courses));
        $this->assertArrayNotHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_course::instance($courseb->id), $calendar->context);

        // Viewing the other course calendar while specifying the categorya too.
        // The categorya is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $courseb->id, $categoryb->id);
        $this->assertEquals($calendar, $calendarwithcategory);

        // Viewing the other course calendar while specifying the wrong categorya.
        // The categorya is essentially ignored. No change expected.
        $calendarwithwrongcategory = \calendar_information::create($time, $courseb->id, $wrongcategory->id);
        $this->assertEquals($calendar, $calendarwithwrongcategory);
    }

    /**
     * Given a user has an enrolment in two courses in separate categories.
     * And I ask for the category information.
     * Then I should see the site.
     * And I should see one of the courses.
     * And I should see the categories that my enrolled courses are in.
     * And I should see the parents of those categories.
     */
    public function test_site_visibility_two_courses_two_categories_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['B.1'];
        $categorya = \core_course_category::get($coursea->category);
        $categoryb = \core_course_category::get($courseb->category);
        $wrongcategory = $categories['C'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);

        // Viewing the categorya calendar.
        // Should see all courses that this user is enrolled in.
        $calendar = \calendar_information::create(time(), SITEID, $categorya->id);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(7, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey($coursea->id, array_flip($calendar->courses));
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayNotHasKey($courseb->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($categorya->id), $calendar->context);
    }

    /**
     * Given a user has an enrolment in two courses in the separate category.
     * And I ask for the category information of a different category.
     * Then I should see the site.
     * And I should see one of the courses.
     * And I should see the categories that my enrolled courses are in.
     * And I should see the parents of those categories.
     */
    public function test_site_visibility_two_courses_two_categories_different_category(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursea = $courses['A.1'];
        $courseb = $courses['B.1'];
        $categorya = \core_course_category::get($coursea->category);
        $categoryb = \core_course_category::get($courseb->category);
        $wrongcategory = $categories['C'];
        $generator->enrol_user($user->id, $coursea->id);
        $generator->enrol_user($user->id, $courseb->id);

        $this->setUser($user);
        // Viewing the categorya calendar for a categorya the user doesn't have any enrolments in.
        // Should see that categorya, and all categories underneath it.
        $calendar = \calendar_information::create(time(), SITEID, $wrongcategory->id);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(1, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertEquals(\context_coursecat::instance($wrongcategory->id), $calendar->context);
    }

    /**
     * Given an admin user with no enrolments.
     * And I ask for the site information.
     * Then I should see the site.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_admin_user(): void {
        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $this->setAdminUser();

        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(1, $calendar->courses);
        $this->assertCount(0, $calendar->categories);
        $this->assertEquals(SITEID, $calendar->courseid);
        $this->assertEquals(SITEID, reset($calendar->courses));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given an admin user with a single enrolments.
     * And I ask for the site information.
     * Then I should see the site.
     * And I should see the course I am enrolled in
     * And I should see the category of that course.
     * And I should see the parents of that course category.
     * And I should see no other courses.
     * And I should see no other categories.
     */
    public function test_site_visibility_admin_user_with_enrolment_site(): void {
        global $USER;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $this->setAdminUser();
        $generator->enrol_user($USER->id, $course->id);

        $calendar = \calendar_information::create(time(), SITEID, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(2, $calendar->categories);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_system::instance(), $calendar->context);
    }

    /**
     * Given an admin user with a single enrolments.
     * And I ask for the course information.
     * Then I should see the site.
     * And I should see the course I am enrolled in
     * And I should see the category of that course.
     * And I should see the parents of that course category.
     */
    public function test_site_visibility_admin_user_with_enrolment_course(): void {
        global $USER;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $wrongcategory = $categories['B1'];
        $this->setAdminUser();
        $generator->enrol_user($USER->id, $course->id);

        $time = time();

        $calendar = \calendar_information::create($time, $course->id, null);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(2, $calendar->categories);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_course::instance($course->id), $calendar->context);

        // Viewing the course calendar while specifying the category too.
        // The category is essentially ignored. No change expected.
        $calendarwithcategory = \calendar_information::create($time, $course->id, $category->id);
        $this->assertEquals($calendar, $calendarwithcategory);

        // Viewing the course calendar while specifying the wrong category.
        // The category is essentially ignored. No change expected.
        $calendarwithwrongcategory = \calendar_information::create($time, $course->id, $wrongcategory->id);
        $this->assertEquals($calendar, $calendarwithwrongcategory);
    }

    /**
     * Given an admin user with a single enrolments.
     * And I ask for the category information for the category my course is in.
     * Then I should see that category.
     * And I should see the category parents.
     * And I should see the category descendants.
     * And I should see the site.
     * And I should see my course.
     * And I should see no other courses.
     * And I should see no categories.
     */
    public function test_site_visibility_admin_user_with_enrolment_category(): void {
        global $USER;

        $this->resetAfterTest();
        list ($courses, $categories) = $this->mock_structure();

        $generator = $this->getDataGenerator();
        $course = $courses['A1.1'];
        $category = \core_course_category::get($course->category);
        $wrongcategory = $categories['B1'];
        $this->setAdminUser();
        $generator->enrol_user($USER->id, $course->id);

        $calendar = \calendar_information::create(time(), SITEID, $category->id);

        $this->assertCount(2, $calendar->courses);
        $this->assertCount(4, $calendar->categories);
        $this->assertArrayHasKey(SITEID, array_flip($calendar->courses));
        $this->assertArrayHasKey($course->id, array_flip($calendar->courses));
        $this->assertArrayHasKey($categories['A']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1i']->id, array_flip($calendar->categories));
        $this->assertArrayHasKey($categories['A1ii']->id, array_flip($calendar->categories));
        $this->assertEquals(\context_coursecat::instance($category->id), $calendar->context);
    }
}
