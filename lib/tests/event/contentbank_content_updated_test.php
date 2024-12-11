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
 * Content bank updated event tests.
 *
 * @package core
 * @category test
 * @copyright 2020 Amaia Anabitarte <amaia@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

/**
 * Test for content bank updated event.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\event\contentbank_content_updated
 */
final class contentbank_content_updated_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test the content updated event.
     *
     * @covers ::create_from_record
     */
    public function test_content_updated(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Save the system context.
        $systemcontext = \context_system::instance();

        // Create a content bank content.
        /** @var \core_contentbank_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 1);
        $content = array_shift($contents);

        // Store the name before we change it.
        $oldname = $content->get_name();

        // Trigger and capture the event when renaming a content.
        $sink = $this->redirectEvents();

        $newname = "New name";
        $content->set_name($newname);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\contentbank_content_updated', $event);
        $this->assertEquals($systemcontext, $event->get_context());
    }
}
