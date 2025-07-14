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
 * File added to draft area test events.
 *
 * @package   core
 * @category  test
 * @copyright 2023 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

use context_system;

/**
 * Test for draft file added event.
 *
 * @package   core
 * @category  test
 * @copyright 2023 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\event\draft_file_added
 */
final class draft_file_added_test extends \advanced_testcase {
    /**
     * Test draft file added event.
     */
    public function test_event(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $usercontext = \context_user::instance($user->id);

        $sink = $this->redirectEvents();
        $fs = get_file_storage();

        $filerecord = [
                'contextid' => $usercontext->id,
                'component' => 'core',
                'filearea' => 'unittest',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'test.txt',
                'source' => 'Copyright stuff',
        ];
        $originalfile = $fs->create_file_from_string($filerecord, 'Test content');
        $nbsp = "\xc2\xa0";

        // Event data for logging.
        $eventdata = [
            'objectid' => $originalfile->get_id(),
            'context' => $usercontext,
            'other' => [
                'itemid' => $originalfile->get_itemid(),
                'filename' => $originalfile->get_filename(),
                'filesize' => $originalfile->get_filesize(),
                'filepath' => $originalfile->get_filepath(),
                'contenthash' => $originalfile->get_contenthash(),
                'avscantime' => '1.234',
            ],
        ];
        $event = draft_file_added::create($eventdata);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals($usercontext, $event->get_context());
        $expected = "The user with id '{$user->id}' has uploaded file '/test.txt' to the draft file area with item id 0. ".
            "Size: 12{$nbsp}bytes. Content hash: {$originalfile->get_contenthash()}.";
        $this->assertSame($expected, $event->get_description());
        $this->assertSame(1.234, $event->other['avscantime']);
    }

    public function test_avscantime_optional(): void {
        $eventdata = [
            'objectid' => 123,
            'context' => context_system::instance(),
            'other' => [
                'itemid' => 789,
                'filename' => 'test.txt',
                'filesize' => 42,
                'filepath' => '/',
                'contenthash' => 'a2653cc92420a875358f09942e4b4665351fa49b',
            ],
        ];
        $event = draft_file_added::create($eventdata);
        $event->trigger();

        // Mainly, we are asserting that creating the event does not throw an exception.
        $this->assertInstanceOf(draft_file_added::class, $event);
    }

    public function test_avscantime_must_be_float(): void {
        $this->expectException(\coding_exception::class);

        $eventdata = [
            'objectid' => 123,
            'context' => context_system::instance(),
            'other' => [
                'itemid' => 789,
                'filename' => 'test.txt',
                'filesize' => 42,
                'filepath' => '/',
                'contenthash' => 'a2653cc92420a875358f09942e4b4665351fa49b',
                'avscantime' => 'frog',
            ],
        ];
        draft_file_added::create($eventdata);
    }
}
