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

namespace core\task;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the registration_reporting_check_task scheduled task.
 *
 * @package    core
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(registration_reporting_check_task::class)]
final class registration_reporting_check_task_test extends \advanced_testcase {
    /**
     * Clear the static registration cache so each test sees the current database state.
     *
     * \core\hub\registration caches the registration record in a static property that is not
     * reset between tests, so without this a record from an earlier test leaks into this one.
     */
    protected function setUp(): void {
        parent::setUp();

        $property = new \ReflectionProperty(\core\hub\registration::class, 'registration');
        $property->setValue(null, null);
    }

    /**
     * Executing the task with a registered site that has paused reporting sends a notification.
     */
    public function test_execute_notifies_when_paused(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $sink = $this->redirectMessages();

        // Paused reporting is only detected for publicly accessible sites.
        $CFG->site_is_public = true;
        $DB->insert_record('registration_hubs', [
            'token' => 'abc123',
            'hubname' => 'Moodle.org',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => 'secret123',
            'timemodified' => time(),
        ]);
        set_config('site_regupdateversion', 0, 'hub');

        $task = new registration_reporting_check_task();
        $task->execute();

        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertSame('registrationreportingpaused', $message->eventtype);
        $this->assertSame(get_string('registrationreportingpausedsubject', 'admin'), $message->subject);
        // The body names the site itself; the registration page is linked separately as the context URL.
        $expected = get_string(
            'registrationreportingpausednewfieldsmessage',
            'admin',
            (object) ['siteurl' => $CFG->wwwroot],
        );
        $this->assertSame($expected, $message->fullmessage);
        $this->assertStringContainsString('/admin/registration/index.php', $message->contexturl);
        $sink->close();
    }

    /**
     * Executing the task with no registration does not send a notification.
     */
    public function test_execute_no_registration(): void {
        $this->resetAfterTest();
        $sink = $this->redirectMessages();

        $task = new registration_reporting_check_task();
        $task->execute();

        $this->assertCount(0, $sink->get_messages());
        $sink->close();
    }
}
