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

namespace assignfeedback_file\event;

/**
 * Tests {@see feedback_downloaded} event.
 *
 * @package assignfeedback_file
 * @category test
 * @copyright 2025 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \assignfeedback_file\event\feedback_downloaded
 */
final class feedback_downloaded_test extends \advanced_testcase {
    /**
     * Tests basic usage of the event, including creation, name, and description.
     */
    public function test_feedback_downloaded_ok(): void {
        $this->resetAfterTest();

        $generator = self::getDataGenerator();
        $course = $generator->create_course();
        $assign = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $this->setUser($user);

        $fs = get_file_storage();
        // We are not creating a real grade item, so I'm using 123 for its id.
        $file = $fs->create_file_from_string([
            'contextid' => \context_module::instance($assign->cmid)->id,
            'component' => 'assignfeedback_file',
            'filearea' => 'feedback_files',
            'itemid' => 123,
            'filepath' => '/',
            'filename' => 'testfile.txt',
        ], 'hello world');
        $event = feedback_downloaded::create_for_file($file);
        $this->assertInstanceOf(feedback_downloaded::class, $event);
        $this->assertEquals(CONTEXT_MODULE, $event->get_context()->contextlevel);
        $this->assertEquals($assign->cmid, $event->get_context()->instanceid);
        $this->assertEquals(123, $event->objectid);
        $this->assertEquals($file->get_id(), $event->other['fileid']);
        $this->assertEquals('testfile.txt', $event->other['filename']);
        $this->assertEquals('Feedback file downloaded', $event->get_name());

        $this->assertEquals(
            "The user with id '{$user->id}' downloaded feeedback file 'testfile.txt'" .
            " ('{$file->get_id()}') for the assignment with course module id '{$assign->cmid}'.",
            $event->get_description(),
        );
    }

    /**
     * Tests invalid missing fileid if you create manually (which you shouldn't).
     */
    public function test_no_fileid(): void {
        $this->expectExceptionMessageMatches('~other\[\'fileid\'\] must be set~');
        feedback_downloaded::create([
            'context' => \context_system::instance(),
            'objectid' => 123,
            'other' => [
                'filename' => 'testfile.txt',
            ],
        ]);
    }

    /**
     * Tests invalid missing filename if you create manually (which you shouldn't).
     */
    public function test_no_filename(): void {
        $this->expectExceptionMessageMatches('~other\[\'filename\'\] must be set~');
        feedback_downloaded::create([
            'context' => \context_system::instance(),
            'objectid' => 123,
            'other' => [
                'fileid' => 456,
            ],
        ]);
    }
}
