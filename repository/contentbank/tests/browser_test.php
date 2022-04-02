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

namespace repository_contentbank;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->dirroot/repository/lib.php");

/**
 * Tests for the content bank browser class.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class browser_test extends \advanced_testcase {

    /**
     * Test get_content() in the system context with users that have capability to access/view content bank content
     * within the system context. By default, every authenticated user should be able to access/view the content in
     * the system context.
     */
    public function test_get_content_system_context_user_has_capabilities() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $systemcontext = \context_system::instance();
        // Create a course category $coursecategory.
        $coursecategory = $this->getDataGenerator()->create_category(['name' => 'Category']);
        $coursecatcontext = \context_coursecat::instance($coursecategory->id);

        // Get the default course category.
        $defaultcat = \core_course_category::get(1);
        $defaultcatcontext = \context_coursecat::instance($defaultcat->id);

        // Create course.
        $course = $this->getDataGenerator()->create_course(['category' => $coursecategory->id]);

        $admin = get_admin();
        // Create a user (not enrolled in a course).
        $user = $this->getDataGenerator()->create_user();

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add some content bank files in the system context.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $contentbankcontents = $generator->generate_contentbank_data('contenttype_h5p', 3, $admin->id,
            $systemcontext, true, $filepath);

        // Log in as admin.
        $this->setUser($admin);
        // Get the content bank nodes displayed to the admin in the system context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_system($systemcontext);
        $repositorycontentnodes = $browser->get_content();
        // All content nodes should be available to the admin user.
        // There should be a total of 5 nodes, 3 file nodes representing the existing content bank files in the
        // system context and 2 folder nodes representing the default course category and 'Category'.
        $this->assertCount(5, $repositorycontentnodes);
        $contextfolders = [
            [
                'name' => get_string('defaultcategoryname'),
                'contextid' => $defaultcatcontext->id
            ],
            [
                'name' => 'Category',
                'contextid' => $coursecatcontext->id
            ]
        ];
        $expected = $this->generate_expected_content($contextfolders, $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontentnodes);

        // Log in as a user.
        $this->setUser($user);
        // Get the content bank nodes displayed to an authenticated user in the system context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_system($systemcontext);
        $repositorycontentnodes = $browser->get_content();
        // There should be 3 nodes representing the existing content bank files in the system context.
        // The course category context folder node should be ignored as the user does not have an access to
        // the content of the category's courses.
        $this->assertCount(3, $repositorycontentnodes);
        $expected = $this->generate_expected_content([], $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontentnodes);

        // Enrol the user as an editing teacher in the course.
        $editingteacherrole = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $editingteacherrole);

         // Get the content bank nodes displayed to the editing teacher in the system context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_system($systemcontext);
        $repositorycontentnodes = $browser->get_content();
        // All content nodes should now be available to the editing teacher.
        // There should be a total of 4 nodes, 3 file nodes representing the existing content bank files in the
        // system context and 1 folder node representing the course category 'Category' (The editing teacher is now
        // enrolled in a course from the category).
        $this->assertCount(4, $repositorycontentnodes);
        $contextfolders = [
            [
                'name' => 'Category',
                'contextid' => $coursecatcontext->id
            ]
        ];
        $expected = $this->generate_expected_content($contextfolders, $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontentnodes);
    }

    /**
     * Test get_content() in the system context with users that do not have a capability to access/view content bank
     * content within the system context. By default, every non-authenticated user should not be able to access/view
     * the content in the system context.
     */
    public function test_get_content_system_context_user_missing_capabilities() {
        $this->resetAfterTest(true);

        $systemcontext = \context_system::instance();

        $admin = get_admin();
        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add some content bank files in the system context.

        $generator->generate_contentbank_data('contenttype_h5p', 3, $admin->id, $systemcontext, true);
        // Log out.
        $this->setUser();
        // Get the content bank nodes displayed to a non-authenticated user in the system context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_system($systemcontext);
        $repositorycontents = $browser->get_content();
        // Content nodes should not be available to the non-authenticated user in the system context.
        $this->assertCount(0, $repositorycontents);
    }

    /**
     * Test get_content() in the course category context with users that have capability to access/view content
     * bank content within the course category context. By default, every authenticated user that has access to
     * any category course should be able to access/view the content in the course category context.
     */
    public function test_get_content_course_category_context_user_has_capabilities() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create a course category.
        $category = $this->getDataGenerator()->create_category(['name' => 'Category']);
        $coursecatcontext = \context_coursecat::instance($category->id);
        // Create course1.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course1', 'category' => $category->id]);
        $course1context = \context_course::instance($course1->id);
        // Create course2.
        $course2 = $this->getDataGenerator()->create_course(['fullname' => 'Course2', 'category' => $category->id]);
        $course2context = \context_course::instance($course2->id);

        $admin = get_admin();
        // Create editing teacher enrolled in course1.
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course1, 'editingteacher');

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add some content bank files in the course category context.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $contentbankcontents = $generator->generate_contentbank_data('contenttype_h5p', 3, $admin->id,
            $coursecatcontext, true, $filepath);

        $this->setUser($admin);
        // Get the content bank nodes displayed to the admin in the course category context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($coursecatcontext);
        $repositorycontents = $browser->get_content();
        // All content nodes should be available to the admin user.
        // There should be a total of 5 nodes, 3 file nodes representing the existing content bank files in the
        // course category context and 2 folder nodes representing the courses 'Course1' and 'Course2'.
        $this->assertCount(5, $repositorycontents);
        $contextfolders = [
            [
                'name' => 'Course1',
                'contextid' => $course1context->id
            ],
            [
                'name' => 'Course2',
                'contextid' => $course2context->id
            ]
        ];
        $expected = $this->generate_expected_content($contextfolders, $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontents);

        // Log in as an editing teacher enrolled in a child course.
        $this->setUser($editingteacher);
        // Get the content bank nodes displayed to the editing teacher in the course category context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($coursecatcontext);
        $repositorycontents = $browser->get_content();
        // There should be a total of 4 nodes, 3 file nodes representing the existing content bank files in the
        // course category context and 1 folder node representing the course 'Course1' (The editing teacher is only
        // enrolled in course1).
        $this->assertCount(4, $repositorycontents);
        $contextfolders = [
            [
                'name' => 'Course1',
                'contextid' => $course1context->id
            ]
        ];
        $expected = $this->generate_expected_content($contextfolders, $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontents);
    }

    /**
     * Test get_content() in the course category context with users that do not have capability to access/view content
     * bank content within the course category context. By default, every non-authenticated user or authenticated users
     * that cannot access/view course content from the course category should not be able to access/view the
     * content in the course category context.
     */
    public function test_get_content_course_category_context_user_missing_capabilities() {
        $this->resetAfterTest(true);

         // Create a course category 'Category'.
        $category = $this->getDataGenerator()->create_category(['name' => 'Category']);
        // Create course1 in 'Category'.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course1', 'category' => $category->id]);
        // Create course2 in default category by default.
        $course2 = $this->getDataGenerator()->create_course(['fullname' => 'Course2']);
        // Create a teacher enrolled in course1.
        $teacher = $this->getDataGenerator()->create_and_enrol($course1, 'teacher');
        // Create an editing teacher enrolled in course2.
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course2, 'editingteacher');

        $admin = get_admin();
        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add some content bank files in the 'Category' context.
        $coursecatcontext = \context_coursecat::instance($category->id);
        $generator->generate_contentbank_data('contenttype_h5p', 3, $admin->id,
            $coursecatcontext, true);

        // Log in as a non-editing teacher.
        $this->setUser($teacher);
        // Get the content bank nodes displayed to a non-editing teacher in the 'Category' context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($coursecatcontext);
        $repositorycontents = $browser->get_content();
        // Content nodes should not be available to a non-editing teacher in the 'Category' context.
        $this->assertCount(0, $repositorycontents);

        // Log in as an editing teacher.
        $this->setUser($editingteacher);
        // Get the content bank nodes displayed to a an editing teacher in the 'Category' context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($coursecatcontext);
        $repositorycontents = $browser->get_content();
        // Content nodes should not be available to an editing teacher in the 'Category' context.
        $this->assertCount(0, $repositorycontents);

        // Log out.
        $this->setUser();
        // Get the content bank nodes displayed to a non-authenticated user in the course category context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($coursecatcontext);
        $repositorycontents = $browser->get_content();
        // Content nodes should not be available to the non-authenticated user in the course category context.
        $this->assertCount(0, $repositorycontents);
    }

    /**
     * Test get_content() in the course context with users that have capability to access/view content
     * bank content within the course context. By default, admin, managers, course creators, editing teachers enrolled
     * in the course should be able to access/view the content.
     */
    public function test_get_content_course_context_user_has_capabilities() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create course1.
        $course = $this->getDataGenerator()->create_course(['fullname' => 'Course']);
        $coursecontext = \context_course::instance($course->id);

        $admin = get_admin();
        // Create editing teacher enrolled in course.
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add some content bank files in the course context.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $contentbankcontents = $generator->generate_contentbank_data('contenttype_h5p', 3, $admin->id,
            $coursecontext, true, $filepath);

        $this->setUser($admin);
        // Get the content bank nodes displayed to the admin in the course context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_course($coursecontext);
        $repositorycontents = $browser->get_content();
        // All content nodes should be available to the admin user.
        // There should be 3 file nodes representing the existing content bank files in the
        // course context.
        $this->assertCount(3, $repositorycontents);
        $expected = $this->generate_expected_content([], $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontents);

        // Log in as an editing teacher.
        $this->setUser($editingteacher);
        // All content nodes should also be available to the editing teacher.
        // Get the content bank nodes displayed to the editing teacher in the course context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_course($coursecontext);
        $repositorycontents = $browser->get_content();
        // There should be 3 file nodes representing the existing content bank files in the
        // course context.
        $this->assertCount(3, $repositorycontents);
        $expected = $this->generate_expected_content([], $contentbankcontents);
        $this->assertEqualsCanonicalizing($expected, $repositorycontents);
    }

    /**
     * Test get_content() in the course context with users that do not have capability to access/view content
     * bank content within the course context. By default, every user which is not an admin, manager, course creator,
     * editing teacher enrolled in the course should not be able to access/view the content.
     */
    public function test_get_content_course_context_user_missing_capabilities() {
        $this->resetAfterTest(true);

        // Create course1.
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course1']);
        $course1context = \context_course::instance($course1->id);
        // Create course2.
        $course2 = $this->getDataGenerator()->create_course(['fullname' => 'Course2']);
        $course2context = \context_course::instance($course2->id);

        $admin = get_admin();
        // Create non-editing teacher enrolled in course1.
        $teacher = $this->getDataGenerator()->create_and_enrol($course1, 'teacher');
         // Create editing teacher enrolled in course1.
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course1, 'editingteacher');

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add some content bank files in the course1 context.
        $generator->generate_contentbank_data('contenttype_h5p', 2, $admin->id,
            $course1context, true);
        // Add some content bank files in the course2 context.
        $generator->generate_contentbank_data('contenttype_h5p', 3, $admin->id,
            $course2context, true);

        // Log in as a non-editing teacher.
        $this->setUser($teacher);
        // Get the content bank nodes displayed to the non-editing teacher in the course1 context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_course($course1context);
        $repositorycontents = $browser->get_content();
        // Content nodes should not be available to the teacher in the course1 context.
        $this->assertCount(0, $repositorycontents);

        // Log in as editing teacher.
        $this->setUser($editingteacher);
        // Get the content bank nodes displayed to the editing teacher in the course2 context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_course($course2context);
        $repositorycontents = $browser->get_content();
        // Content nodes should not be available to the teacher in the course2 context. The editing teacher is not
        // enrolled in this course.
        $this->assertCount(0, $repositorycontents);
    }

    /**
     * Test get_navigation() in the system context.
     */
    public function test_get_navigation_system_context() {
        $this->resetAfterTest(true);

        $systemcontext = \context_system::instance();

        $browser = new \repository_contentbank\browser\contentbank_browser_context_system($systemcontext);
        $navigation = $browser->get_navigation();
        // The navigation array should contain only 1 element, representing the system navigation node.
        $this->assertCount(1, $navigation);
        $expected = [
            \repository_contentbank\helper::create_navigation_node($systemcontext)
        ];
        $this->assertEquals($expected, $navigation);
    }

    /**
     * Test get_navigation() in the course category context.
     */
    public function test_get_navigation_course_category_context() {
        $this->resetAfterTest(true);

        $systemcontext = \context_system::instance();
        // Create a course category.
        $category = $this->getDataGenerator()->create_category(['name' => 'category']);
        $categorycontext = \context_coursecat::instance($category->id);
        // Create a course subcategory.
        $subcategory = $this->getDataGenerator()->create_category(['name' => 'subcategory', 'parent' => $category->id]);
        $subcategorytcontext = \context_coursecat::instance($subcategory->id);

        // Get navigation nodes in the category context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($categorycontext);
        $navigation = $browser->get_navigation();
        // The navigation array should contain 2 elements, representing the system and course category
        // navigation nodes.
        $this->assertCount(2, $navigation);
        $expected = [
            \repository_contentbank\helper::create_navigation_node($systemcontext),
            \repository_contentbank\helper::create_navigation_node($categorycontext)
        ];
        $this->assertEquals($expected, $navigation);

        // Get navigation nodes in the subcategory context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_coursecat($subcategorytcontext);
        $navigation = $browser->get_navigation();
        // The navigation array should contain 3 elements, representing the system, category and subcategory
        // navigation nodes.
        $this->assertCount(3, $navigation);
        $expected = [
            \repository_contentbank\helper::create_navigation_node($systemcontext),
            \repository_contentbank\helper::create_navigation_node($categorycontext),
            \repository_contentbank\helper::create_navigation_node($subcategorytcontext)
        ];
        $this->assertEquals($expected, $navigation);
    }

    /**
     * Test get_navigation() in the course context.
     */
    public function test_get_navigation_course_context() {
        $this->resetAfterTest(true);

        $systemcontext = \context_system::instance();
        // Create a category.
        $category = $this->getDataGenerator()->create_category(['name' => 'category']);
        $categorycontext = \context_coursecat::instance($category->id);
        // Create a subcategory.
        $subcategory = $this->getDataGenerator()->create_category(['name' => 'category', 'parent' => $category->id]);
        $subcategorycontext = \context_coursecat::instance($subcategory->id);
        // Create a course in category.
        $categorycourse = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $categorycoursecontext = \context_course::instance($categorycourse->id);
        // Create a course in subcategory.
        $subcategorycourse = $this->getDataGenerator()->create_course(['category' => $subcategory->id]);
        $subcategorycoursecontext = \context_course::instance($subcategorycourse->id);

        // Get navigation nodes in the category course context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_course($categorycoursecontext);
        $navigation = $browser->get_navigation();
        // The navigation array should contain 3 elements, representing the system, category and course
        // navigation nodes.
        $this->assertCount(3, $navigation);
        $expected = [
            \repository_contentbank\helper::create_navigation_node($systemcontext),
            \repository_contentbank\helper::create_navigation_node($categorycontext),
            \repository_contentbank\helper::create_navigation_node($categorycoursecontext)
        ];
        $this->assertEquals($expected, $navigation);

        // Get navigation nodes in the subcategory course context.
        $browser = new \repository_contentbank\browser\contentbank_browser_context_course($subcategorycoursecontext);
        $navigation = $browser->get_navigation();
        // The navigation array should contain 4 elements, representing the system, category, subcategory and
        // subcategory course navigation nodes.
        $this->assertCount(4, $navigation);
        $expected = [
            \repository_contentbank\helper::create_navigation_node($systemcontext),
            \repository_contentbank\helper::create_navigation_node($categorycontext),
            \repository_contentbank\helper::create_navigation_node($subcategorycontext),
            \repository_contentbank\helper::create_navigation_node($subcategorycoursecontext)
        ];
        $this->assertEquals($expected, $navigation);
    }

    /**
     * Generate the expected array of content bank nodes.
     *
     * @param array $contextfolders The array containing the expected folder nodes
     * @param array $contentbankcontents The array containing the expected contents
     * @return array[] The expected array of content bank nodes
     */
    private function generate_expected_content(array $contextfolders = [], array $contentbankcontents = []): array {

        $expected = [];
        if (!empty($contextfolders)) {
            foreach ($contextfolders as $contextfolder) {
                $expected[] = \repository_contentbank\helper::create_context_folder_node($contextfolder['name'],
                    base64_encode(json_encode(['contextid' => $contextfolder['contextid']])));
            }
        }
        if (!empty($contentbankcontents)) {
            foreach ($contentbankcontents as $content) {
                $expected[] = \repository_contentbank\helper::create_contentbank_content_node($content);
            }
        }
        return $expected;
    }
}
