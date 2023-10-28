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

declare(strict_types = 1);

namespace mod_forum\h5p;

use stdClass;

/**
 * Test class covering the H5P canedit class.
 *
 * @package    mod_forum
 * @copyright  2021 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_forum\h5p\canedit
 */
class canedit_test extends \advanced_testcase {

    /**
     * Test the behaviour of can_edit_content().
     *
     * @covers ::can_edit_content
     * @dataProvider can_edit_content_provider
     *
     * @param string $currentuser User who will call the method.
     * @param string $fileauthor Author of the file to check.
     * @param string $filecomponent Component of the file to check.
     * @param bool $expected Expected result after calling the can_edit_content method.
     * @param string $filearea Area of the file to check.
     *
     * @return void
     */
    public function test_can_edit_content(string $currentuser, string $fileauthor, string $filecomponent, bool $expected,
            $filearea = 'unittest'): void {
        global $USER, $DB;

        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();

        // Create course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create some users.
        $this->setAdminUser();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $users = [
            'admin' => $USER,
            'teacher' => $teacher,
            'student' => $student,
        ];

        // Set current user.
        if ($currentuser !== 'admin') {
            $this->setUser($users[$currentuser]);
        }

        $itemid = rand();
        if ($filearea === 'post') {
            // Create a forum and add a discussion.
            $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

            $record = new stdClass();
            $record->course = $course->id;
            $record->userid = $users[$fileauthor]->id;
            $record->forum = $forum->id;
            $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
            $post = $DB->get_record('forum_posts', ['discussion' => $discussion->id]);
            $itemid = $post->id;
        }

        // Create the file.
        $filename = 'greeting-card.h5p';
        $path = __DIR__ . '/../../../../h5p/tests/fixtures/' . $filename;
        if ($filecomponent === 'contentbank') {
            $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
            $contents = $generator->generate_contentbank_data(
                'contenttype_h5p',
                1,
                (int)$users[$fileauthor]->id,
                $context,
                true,
                $path
            );
            $content = array_shift($contents);
            $file = $content->get_file();
        } else {
            $filerecord = [
                'contextid' => $context->id,
                'component' => $filecomponent,
                'filearea'  => $filearea,
                'itemid'    => $itemid,
                'filepath'  => '/',
                'filename'  => basename($path),
                'userid'    => $users[$fileauthor]->id,
            ];
            $fs = get_file_storage();
            $file = $fs->create_file_from_pathname($filerecord, $path);
        }

        // Check if the currentuser can edit the file.
        $result = canedit::can_edit_content($file);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_can_edit_content().
     *
     * @return array
     */
    public function can_edit_content_provider(): array {
        return [
            // Component = mod_forum.
            'mod_forum: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
            ],
            'mod_forum: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_forum',
                'expected' => true,
            ],
            'mod_forum: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
            ],
            'mod_forum: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_forum',
                'expected' => false,
            ],
            'mod_forum/post: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
                'filearea' => 'post',
            ],
            'mod_forum/post: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
                'filearea' => 'post',
            ],
            'mod_forum/post: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_forum',
                'expected' => false,
                'filearea' => 'post',
            ],

            // Component <> mod_forum.
            'mod_page: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_page',
                'expected' => false,
            ],

            // Unexisting components.
            'Unexisting component' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'unexisting_component',
                'expected' => false,
            ],
            'Unexisting module activity' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_unexisting',
                'expected' => false,
            ],
            'Unexisting block' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'block_unexisting',
                'expected' => false,
            ],
        ];
    }
}
