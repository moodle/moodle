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

namespace core_my\event;

use context_system;
use context_user;

/**
 * Unit tests for the dashboard events.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {

    /** @var user cobject */
    protected $user;

    /**
     * Setup often used objects for the following tests.
     */
    protected function setUp(): void {
        global $USER;
        parent::setUp();

        $this->resetAfterTest();

        // The user we are going to test this on.
        $this->setAdminUser();
        $this->user = $USER;
    }

    /**
     * Test the dashboard viewed event.
     *
     * There is no external API for viewing the dashboard, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_dashboard_viewed(): void {

        $user = $this->user;
        // Trigger an event: dashboard viewed.
        $eventparams = array(
            'context' => $context = \context_user::instance($user->id)
        );

        $event = \core\event\dashboard_viewed::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\dashboard_viewed', $event);
        $this->assertEquals($user->id, $event->userid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the dashboard reset event.
     *
     * We will reset the user dashboard to
     * trigger the event and ensure data is returned as expected.
     *
     * @covers ::my_reset_page
     */
    public function test_dashboard_reset(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/my/lib.php');

        $user = $this->user;
        $usercontext = context_user::instance($this->user->id);

        // Create at least one dashboard.
        my_copy_page($this->user->id);
        $this->assertNotEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PRIVATE,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertNotEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        // Reset the dashboard.
        $sink = $this->redirectEvents();
        my_reset_page($user->id);

        // Assert that the page and all th blocks were deleted.
        $this->assertEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PRIVATE,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        // Trigger and capture the event.
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\dashboard_reset', $event);
        $this->assertEquals($user->id, $event->userid);
        $this->assertEquals(MY_PAGE_PRIVATE, $event->other['private']);
        $this->assertEquals('my-index', $event->other['pagetype']);
        $this->assertDebuggingNotCalled();

        // Reset the dashboard with private parameter is set to MY_PAGE_PUBLIC and pagetype set to 'user-profile'.
        $systempage = $DB->get_record('my_pages', ['userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => MY_PAGE_PUBLIC]);
        $this->getDataGenerator()->create_block('online_users', [
            'parentcontextid' => context_system::instance()->id,
            'pagetypepattern' => 'user-profile',
            'subpagepattern' => $systempage->id,
        ]);

        my_copy_page($this->user->id, MY_PAGE_PUBLIC, 'user-profile');
        $this->assertNotEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PUBLIC,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertNotEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        $sink = $this->redirectEvents();
        my_reset_page($user->id, MY_PAGE_PUBLIC, 'user-profile');
        $this->assertEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PUBLIC,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        // Trigger and capture the event.
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        $this->assertEquals(MY_PAGE_PUBLIC, $event->other['private']);
        $this->assertEquals('user-profile', $event->other['pagetype']);
    }

    /**
     * Test the dashboards reset event.
     *
     * We will reset all user dashboards to
     * trigger the event and ensure data is returned as expected.
     *
     * @covers ::my_reset_page_for_all_users
     */
    public function test_dashboards_reset(): void {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/my/lib.php');

        $usercontext = context_user::instance($this->user->id);

        // Create at least one dashboard.
        my_copy_page($this->user->id);
        $this->assertNotEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PRIVATE,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertNotEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        // Reset all dashbaords.
        $sink = $this->redirectEvents();
        my_reset_page_for_all_users();

        // Assert that the page and all th blocks were deleted.
        $this->assertEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PRIVATE,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        // Trigger and capture the event.
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\dashboards_reset', $event);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals(MY_PAGE_PRIVATE, $event->other['private']);
        $this->assertEquals('my-index', $event->other['pagetype']);
        $this->assertDebuggingNotCalled();

        // Reset the dashboards with private parameter is set to MY_PAGE_PUBLIC and pagetype set to 'user-profile'.
        $systempage = $DB->get_record('my_pages', ['userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => MY_PAGE_PUBLIC]);
        $this->getDataGenerator()->create_block('online_users', [
            'parentcontextid' => context_system::instance()->id,
            'pagetypepattern' => 'user-profile',
            'subpagepattern' => $systempage->id,
        ]);

        my_copy_page($this->user->id, MY_PAGE_PUBLIC, 'user-profile');
        $this->assertNotEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PUBLIC,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertNotEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        $sink = $this->redirectEvents();
        my_reset_page_for_all_users(MY_PAGE_PUBLIC, 'user-profile');
        $this->assertEmpty($DB->get_records('my_pages', ['userid' => $this->user->id, 'private' => MY_PAGE_PUBLIC,
            'name' => MY_PAGE_DEFAULT]));
        $this->assertEmpty($DB->get_records('block_instances', ['parentcontextid' => $usercontext->id]));

        // Trigger and capture the event.
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        $this->assertEquals(MY_PAGE_PUBLIC, $event->other['private']);
        $this->assertEquals('user-profile', $event->other['pagetype']);
    }
}
