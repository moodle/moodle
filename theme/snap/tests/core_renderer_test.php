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
 * Test snap core renderer page heading
 * @author    Juan Ibarra
 * @copyright Copyright (c) 2021 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;

/**
 * Class theme_snap_core_renderer_testcase
 * @author    Juan Ibarra
 * @copyright Copyright (c) 2021 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer_test extends \advanced_testcase {

    /**
     * Test heading should show only h1 tags on site level.
     */
    public function test_default_heading_on_standard_pagelayout_sitelevel() {
        global $COURSE, $PAGE;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $COURSE = $this->getDataGenerator()->create_course();
        $COURSE->id = SITEID; // Simulate site level.

        $PAGE = new \moodle_page();
        $PAGE->set_url('/user/view.php', array('id' => $user->id, 'course' => $COURSE->id));
        $PAGE->set_context(\context_user::instance($user->id));
        $PAGE->set_pagelayout('standard');

        $target = null;
        $corerenderer = new \theme_snap\output\core_renderer($PAGE, $target);
        $heading = $corerenderer->page_heading();

        $this->assertEquals("<h1></h1>", $heading);
    }

    /**
     * Test heading should not show links on sitelevel.
     */
    public function test_course_link_should_not_appear_on_sitelevel() {
        global $COURSE, $PAGE;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $COURSE = $this->getDataGenerator()->create_course();
        $COURSE->id = SITEID; // Simulate site level.

        $PAGE->force_theme('snap');
        $PAGE->set_url('/user/view.php', array('id' => $user->id, 'course' => $COURSE->id));
        $PAGE->set_context(\context_user::instance($user->id));
        $PAGE->set_pagelayout('mypublic');

        $target = null;
        $corerenderer = new \theme_snap\output\core_renderer($PAGE, $target);
        $heading = $corerenderer->page_heading();

        $url = new \moodle_url('/course/view.php', ['id' => $COURSE->id]);
        $this->assertFalse(strpos($heading, $url->out()));
    }

    /**
     * Test heading should show link to course on courselevel.
     */
    public function test_course_link_should_appear_on_courselevel() {
        global $COURSE, $PAGE;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $COURSE = $this->getDataGenerator()->create_course();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/user/view.php', array('id' => $user->id, 'course' => $COURSE->id));
        $PAGE->set_context(\context_user::instance($user->id));
        $PAGE->set_pagelayout('mypublic');
        $PAGE->set_heading($COURSE->fullname);

        $target = null;
        $corerenderer = new \theme_snap\output\core_renderer($PAGE, $target);
        $heading = $corerenderer->page_heading();

        $url = new \moodle_url('/course/view.php', ['id' => $COURSE->id]);
        $this->assertStringContainsString($url->out(), $heading);
    }

    /**
     * Provider for test_remove_duplicated_breadcrumbs.
     *
     * @return array
     */
    public function remove_duplicated_breadcrumbs_provider(): array {

        return [
            'Breadcrumb items based on user preferences view.' => [
                [
                    [
                        'text' => 'Home',
                        'key' => 'home',
                    ],
                    [
                        'text' => 'Home',
                        'key' => 'home',
                    ],
                    [
                        'text' => 'Preferences',
                        'key' => 'preferences',
                    ],
                ],
                ['Home', 'Preferences'],
            ],
            'Breadcrumb items based on course view.' => [
                [
                    [
                        'text' => 'Home',
                        'key' => 'home',
                    ],
                    [
                        'text' => 'My Courses',
                        'key' => 'my_courses',
                    ],
                    [
                        'text' => 'Course 1',
                        'key' => 'course1',
                    ],
                ],
                ['Home', 'My Courses', 'Course 1'],
            ],
        ];
    }


    /**
     * Test filter duplicated breadcrumbs.
     * @dataProvider remove_duplicated_breadcrumbs_provider
     * @param array $breadcrumbs
     * @param array $expected
     */
    public function test_remove_duplicated_breadcrumbs(array $breadcrumbs, array $expected) {
        global $PAGE;

        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');
        foreach ($breadcrumbs as $item) {
            $PAGE->navbar->add($item['text'], null, null, null, $item['key']);
        }
        // Get breadcrumbs objects from page.
        $pagebreadcrumbs = $PAGE->navbar->get_items();

        // Call method.
        $corerenderer = new \theme_snap\output\core_renderer($PAGE, null);
        $pagebreadcrumbs = $corerenderer->remove_duplicated_breadcrumbs($pagebreadcrumbs);

        // Get text value from object array.
        $pagebreadcrumbsarray = array();
        foreach ($pagebreadcrumbs as $item) {
            array_push($pagebreadcrumbsarray, $item->text);
        }
        $this->assertEquals($expected, $pagebreadcrumbsarray);
    }

    /**
     * Track changes on boost core_renderer. If this fails, please check the context_header function.
     * @return void
     */
    public function test_core_renderer_changes_boost() {
        $this->markTestSkipped('To be reviewed by INT-20687. Please review the changes on the boost file.');
        global $CFG;
        $sha1 = hash_file('sha1', $CFG->dirroot . '/theme/boost/classes/output/core_renderer.php');
        $this->assertEquals('b01e09191e7c4d6356e1eee565147e2d8a0e6127', $sha1);
    }
}
