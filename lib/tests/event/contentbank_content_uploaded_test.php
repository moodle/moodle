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
 * Content bank uploaded event tests.
 *
 * @package core
 * @category test
 * @copyright 2020 Amaia Anabitarte <amaia@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

use core_contentbank\contentbank;

/**
 * Test for content bank uploaded event.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\event\contentbank_content_uploaded
 */
class contentbank_content_uploaded_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
    }

    /**
     * Test the content created event.
     *
     * @covers ::create_from_record
     */
    public function test_content_created() {
        global $USER, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $systemcontext = \context_system::instance();

        // Create a dummy H5P file.
        $dummyh5p = array(
            'contextid' => $systemcontext->id,
            'component' => 'contentbank',
            'filearea' => 'public',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'dummy_h5p.h5p'
        );
        $path = $CFG->dirroot . '/h5p/tests/fixtures/greeting-card.h5p';
        $dummyh5pfile = \core_h5p\helper::create_fake_stored_file_from_path($path);

        // Trigger and capture the event when creating content from a file.
        $sink = $this->redirectEvents();
        $cb = new contentbank();
        $cb->create_content_from_file($systemcontext, $USER->id, $dummyh5pfile);

        // Both uploaded and created events are raised.
        $events = $sink->get_events();
        $this->assertCount(2, $events);

        // First the created content event has been raised.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\contentbank_content_created', $event);
        $this->assertEquals($systemcontext, $event->get_context());

        // Second the uploaded content event has been raised.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\contentbank_content_uploaded', $event);
        $this->assertEquals($systemcontext, $event->get_context());
    }
}
