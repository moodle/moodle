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
 * Content bank repository search unit tests.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->dirroot/repository/lib.php");

/**
 * Tests for the content bank search class.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_contentbank_search_testcase extends advanced_testcase {

    /**
     * Test get_search_contents() by searching through some existing content using different search terms.
     *
     * @dataProvider get_search_contents_provider
     * @param array $contentnames The array containing the names of the content that needs to be generated
     * @param string $search The search string
     * @param array $expected The array containing the expected content names that should be returned by the search
     */
    public function test_get_search_contents(array $contentnames, string $search, array $expected) {
        $this->resetAfterTest();

        $admin = get_admin();
        $systemcontext = \context_system::instance();
        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        foreach ($contentnames as $contentname) {
            $generator->generate_contentbank_data('contenttype_h5p', 1, $admin->id,
                $systemcontext, true, 'file.h5p', $contentname);
        }
        // Log in as admin.
        $this->setUser($admin);
        // Search for content bank content items which have the search pattern within the name.
        $searchcontentnodes = \repository_contentbank\contentbank_search::get_search_contents($search);
        // Get the content name of the each content returned after performing the search.
        $actual = array_map(function($searchcontentnode) {
            return $searchcontentnode['shorttitle'];
        }, $searchcontentnodes);

        $this->assertEquals($expected, $actual, '', 0.0, 10, true);
    }

    /**
     * Data provider for test_get_search_contents().
     *
     * @return array
     */
    public function get_search_contents_provider(): array {
        return [
            'Search for existing pattern found within the name of content items' => [
                [
                    'systemcontentfile1',
                    'systemcontentfile2',
                    'somesystemfile'
                ],
                'content',
                [
                    'systemcontentfile1',
                    'systemcontentfile2'
                ]
            ],
            'Search for existing pattern found at the beginning of the name of content items' => [
                [
                    'systemcontentfile1',
                    'systemcontentfile2',
                    'somesystemfile'
                ],
                'some',
                [
                    'somesystemfile',
                ]
            ],
            'Search for existing pattern found at the end of the name of content items' => [
                [
                    'systemcontentfile1',
                    'systemcontentfile2',
                    'somesystemfile'
                ],
                'file2',
                [
                    'systemcontentfile2',
                ]
            ],
            'Search for a pattern which does not exist within the name of any content item' => [
                [
                    'systemcontentfile1',
                    'somesystemfile'
                ],
                'somefile',
                []
            ],
            'Case-insensitive search for a pattern which exists within the name of content items' => [
                [
                    'systemcontentfile1',
                    'systemcontentfile2',
                    'somesystemfile'
                ],
                'CONTENT',
                [
                    'systemcontentfile1',
                    'systemcontentfile2'
                ]
            ]
        ];
    }

    /**
     * Test get_search_contents() by searching for content with users that have capability to access/view
     * all existing content bank content. By default, admins, managers should be able to view every existing content
     * that matches the search criteria.
     */
    public function test_get_search_contents_user_can_access_all_content() {
        $this->resetAfterTest(true);

        // Create a course in 'Miscellaneous' category by default.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        // Create a course category without a course.
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = \context_coursecat::instance($category->id);

        $admin = get_admin();
        // Add some content to the content bank in different contexts.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add a content bank file in the category context.
        $categorycontents = $generator->generate_contentbank_data('contenttype_h5p', 1, $admin->id,
            $categorycontext, true, 'file.h5p', 'categorycontentfile');
        $categorycontent = reset($categorycontents);
        // Add a content bank file in the course context.
        $coursecontents = $generator->generate_contentbank_data('contenttype_h5p', 1, $admin->id,
            $coursecontext, true, 'file.h5p', 'coursecontentfile');
        $coursecontent = reset($coursecontents);

        // Log in as admin.
        $this->setUser($admin);

        // Search for content bank content items which have the pattern 'contentfile' within the name.
        $search = 'contentfile';
        $searchcontentnodes = \repository_contentbank\contentbank_search::get_search_contents($search);
        // All content files which name matches the search criteria should be available to the admin user.
        // The search should return 2 file nodes.
        $this->assertCount(2, $searchcontentnodes);
        $expected = [
            \repository_contentbank\helper::create_contentbank_content_node($categorycontent),
            \repository_contentbank\helper::create_contentbank_content_node($coursecontent),
        ];
        $this->assertEquals($expected, $searchcontentnodes, '', 0.0, 10, true);
    }

    /**
     * Test get_search_contents() by searching for content with users that have capability to access/view only
     * certain existing content bank content. By default, editing teachers should be able to view content that matches
     * the search criteria AND is in the courses they are enrolled, course categories of the enrolled courses
     * and system content. Other authenticated users should be able to access only the system content.
     */
    public function test_get_search_contents_user_can_access_certain_content() {
        $this->resetAfterTest(true);

        $systemcontext = \context_system::instance();
        // Create course1.
        $course1 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        // Create course2.
        $course2 = $this->getDataGenerator()->create_course();
        $course2context = \context_course::instance($course2->id);

        $admin = get_admin();
        // Create and enrol an editing teacher in course1.
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course1, 'editingteacher');
        // Create and enrol a teacher in course2.
        $teacher = $this->getDataGenerator()->create_and_enrol($course2, 'teacher');

        // Add some content to the content bank in different contexts.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        // Add a content bank file in the system context.
        $systemcontents = $generator->generate_contentbank_data('contenttype_h5p', 1, $admin->id,
            $systemcontext, true, 'file.h5p', 'systemcontentfile');
        $systemcontent = reset($systemcontents);
        // Add a content bank file in the course1 context.
        $course1contents = $generator->generate_contentbank_data('contenttype_h5p', 1, $admin->id,
            $course1context, true, 'file.h5p', 'coursecontentfile1');
        $course1content = reset($course1contents);
        // Add a content bank file in the course2 context.
        $generator->generate_contentbank_data('contenttype_h5p', 1, $admin->id,
            $course2context, true, 'file.h5p', 'coursecontentfile2');

        // Log in as an editing teacher.
        $this->setUser($editingteacher);
        // Search for content bank content items which have the pattern 'contentfile' within the name.
        $search = 'contentfile';
        $searchcontentnodes = \repository_contentbank\contentbank_search::get_search_contents($search);
        // The search should return 2 file nodes. The editing teacher does not have access to the content of course2
        // and therefore, the course2 content should be skipped.
        $this->assertCount(2, $searchcontentnodes);
        $expected = [
            \repository_contentbank\helper::create_contentbank_content_node($systemcontent),
            \repository_contentbank\helper::create_contentbank_content_node($course1content),
        ];
        $this->assertEquals($expected, $searchcontentnodes, '', 0.0, 10, true);

        // Log in as a teacher.
        $this->setUser($teacher);
        // Search for content bank content items which have the pattern 'contentfile' within the name.
        $search = 'contentfile';
        $searchcontentnodes = \repository_contentbank\contentbank_search::get_search_contents($search);
        // The search should return 1 file node. The teacher should only be able to view system content.
        $this->assertCount(1, $searchcontentnodes);
        $expected = [
            \repository_contentbank\helper::create_contentbank_content_node($systemcontent),
        ];
        $this->assertEquals($expected, $searchcontentnodes, '', 0.0, 10, true);
    }
}
