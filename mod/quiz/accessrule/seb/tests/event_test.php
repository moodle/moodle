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
 * PHPUnit tests for all plugin events.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_helper_trait.php');

/**
 * PHPUnit tests for all plugin events.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_seb_event_testcase extends advanced_testcase {
    use quizaccess_seb_test_helper_trait;

    /**
     * Called before every test.
     */
    public function setUp(): void {
        parent::setUp();

        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
    }

    /**
     * Test creating the access_prevented event.
     */
    public function test_event_access_prevented() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $quiz = $this->create_test_quiz($this->course, \quizaccess_seb\settings_provider::USE_SEB_CONFIG_MANUALLY);
        $accessmanager = new \quizaccess_seb\access_manager(new quiz($quiz,
            get_coursemodule_from_id('quiz', $quiz->cmid), $this->course));

        // Set up event with data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $_SERVER['HTTP_X_SAFEEXAMBROWSER_CONFIGKEYHASH'] = 'configkey';
        $_SERVER['HTTP_X_SAFEEXAMBROWSER_REQUESTHASH'] = 'browserexamkey';

        $event = \quizaccess_seb\event\access_prevented::create_strict($accessmanager, 'Because I said so.');

        // Create an event sink, trigger event and retrieve event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertEquals(1, count($events));
        $event = reset($events);

        $expectedconfigkey = $accessmanager->get_valid_config_key();

        // Test that the event data is as expected.
        $this->assertInstanceOf('\quizaccess_seb\event\access_prevented', $event);
        $this->assertEquals('Quiz access was prevented', $event->get_name());
        $this->assertEquals(
            "The user with id '$user->id' has been prevented from accessing quiz with id '$quiz->id' by the "
            . "Safe Exam Browser access plugin. The reason was 'Because I said so.'. "
            . "Expected config key: '$expectedconfigkey'. "
            . "Received config key: 'configkey'. Received browser exam key: 'browserexamkey'.",
            $event->get_description());
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEquals($user->id, $event->userid);
        $this->assertEquals($quiz->id, $event->objectid);
        $this->assertEquals($this->course->id, $event->courseid);
        $this->assertEquals('Because I said so.', $event->other['reason']);
        $this->assertEquals($expectedconfigkey, $event->other['savedconfigkey']);
        $this->assertEquals('configkey', $event->other['receivedconfigkey']);
        $this->assertEquals('browserexamkey', $event->other['receivedbrowserexamkey']);
    }

    /**
     * Test creating the template_created event.
     */
    public function test_event_create_template() {
        $this->resetAfterTest();
        // Set up event with data.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $template = $this->create_template();

        $event = \quizaccess_seb\event\template_created::create_strict(
            $template,
            context_system::instance());

        // Create an event sink, trigger event and retrieve event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertEquals(1, count($events));
        $event = reset($events);

        // Test that the event data is as expected.
        $this->assertInstanceOf('\quizaccess_seb\event\template_created', $event);
        $this->assertEquals('SEB template was created', $event->get_name());
        $this->assertEquals(
            "The user with id '$user->id' has created a template with id '{$template->get('id')}'.",
            $event->get_description()
        );
        $this->assertEquals(context_system::instance(), $event->get_context());
        $this->assertEquals($user->id, $event->userid);
        $this->assertEquals($template->get('id'), $event->objectid);
    }
}
