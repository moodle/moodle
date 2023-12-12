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
 * File deleted from draft area test events.
 *
 * @package   core
 * @category  test
 * @copyright 2023 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

/**
 * Test for draft file deleted event.
 *
 * @package   core
 * @category  test
 * @copyright 2023 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\event\draft_file_deleted
 */
class draft_file_deleted_test extends \advanced_testcase {
    /**
     * Test draft file deleted event.
     */
    public function test_event() {
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
            ],
        ];
        $event = \core\event\draft_file_deleted::create($eventdata);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals($usercontext, $event->get_context());
        $expected = "The user with id '{$user->id}' has deleted file '/test.txt' from the draft file area with item id 0. " .
            "Size: 12{$nbsp}bytes. Content hash: {$originalfile->get_contenthash()}.";
        $this->assertSame($expected, $event->get_description());
    }
}
