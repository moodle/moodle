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
 * Tests for {@see send_oauth2_secret_expired_notification_adhoc_task}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(send_oauth2_secret_expired_notification_adhoc_task::class)]
final class send_oauth2_secret_expired_notification_adhoc_task_test extends \advanced_testcase {
    /** @var int The frozen time used by every test in this class. */
    private const int NOW = 1750000000;

    /**
     * Grant the given user the capability to manage OAuth 2 clients and secrets at the system context.
     *
     * The 'oauth2_secret_expired' message provider is gated behind this capability, so the recipient
     * must hold it to actually be allowed to receive the notification.
     *
     * @param \stdClass $user The user to grant the capability to.
     * @return void
     */
    private function allow_user_to_receive_notification(\stdClass $user): void {
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('moodle/site:manageoauth2clients', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $user->id, \context_system::instance());
    }

    /**
     * Queue the adhoc task for the given recipient with the supplied custom data.
     *
     * @param int $userid The recipient user ID.
     * @param int $clientid The client ID.
     * @param string $clientname The client name.
     * @param int $secretexpiredtime The timestamp the secret expired at.
     * @return void
     */
    private function queue_task(
        int $userid,
        int $clientid = 1,
        string $clientname = 'Test client',
        int $secretexpiredtime = self::NOW,
    ): void {
        $task = new send_oauth2_secret_expired_notification_adhoc_task();
        $task->set_custom_data([
            'clientid' => $clientid,
            'clientname' => $clientname,
            'secretexpiredtime' => $secretexpiredtime,
        ]);
        $task->set_userid((int) $userid);

        manager::queue_adhoc_task($task);
    }

    /**
     * Executing the task sends a notification to the recipient user, with the expected subject, body
     * content and metadata.
     *
     * @return void
     */
    public function test_execute_sends_notification_to_recipient(): void {
        $this->resetAfterTest();
        $this->mock_clock_with_frozen(self::NOW);

        $recipient = $this->getDataGenerator()->create_user();
        $this->allow_user_to_receive_notification($recipient);
        $clientid = 42;
        $clientname = 'My Confidential Client';
        $secretexpiredtime = self::NOW - DAYSECS;

        $this->queue_task((int) $recipient->id, $clientid, $clientname, $secretexpiredtime);

        $sink = $this->redirectMessages();
        $this->runAdhocTasks(send_oauth2_secret_expired_notification_adhoc_task::class);
        $messages = $sink->get_messages();
        $sink->close();

        $this->assertCount(1, $messages);
        $message = $messages[0];

        $this->assertEquals($recipient->id, $message->useridto);
        $this->assertEquals(\core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals('moodle', $message->component);
        $this->assertEquals('oauth2_secret_expired', $message->eventtype);
        $this->assertEquals(1, $message->notification);

        $expecteddatetime = userdate($secretexpiredtime, get_string('strftimedatetime', 'langconfig'));

        $this->assertEquals(
            get_string('oauth2server_secretexpirednotificationsubject', 'admin', $clientname),
            $message->subject,
        );
        $this->assertStringContainsString($clientname, $message->smallmessage);
        $this->assertStringContainsString($expecteddatetime, $message->smallmessage);
        $this->assertStringContainsString($clientname, $message->fullmessagehtml);
        $this->assertStringContainsString($expecteddatetime, $message->fullmessagehtml);
        $this->assertStringContainsString($clientname, $message->fullmessage);
    }

    /**
     * The rendered notification includes a link to the manage secrets page for the affected client.
     *
     * @return void
     */
    public function test_execute_includes_manage_secrets_link(): void {
        $this->resetAfterTest();
        $this->mock_clock_with_frozen(self::NOW);

        $recipient = $this->getDataGenerator()->create_user();
        $this->allow_user_to_receive_notification($recipient);
        $clientid = 123;

        $this->queue_task((int) $recipient->id, $clientid);

        $expectedurl = \core\router\util::get_path_for_callable(
            [\core_admin\route\controller\oauth2\server\client_management::class, 'manage_client_secrets'],
            ['client' => $clientid],
        )->out();

        $sink = $this->redirectMessages();
        $this->runAdhocTasks(send_oauth2_secret_expired_notification_adhoc_task::class);
        $messages = $sink->get_messages();
        $sink->close();

        $this->assertCount(1, $messages);
        $this->assertStringContainsString($expectedurl, $messages[0]->fullmessagehtml);
    }

    /**
     * The notification is sent in the recipient's own localized context, reflecting their profile timezone
     * when formatting the secret expiry datetime.
     *
     * @return void
     */
    public function test_execute_formats_datetime_in_recipient_timezone(): void {
        $this->resetAfterTest();
        $this->mock_clock_with_frozen(self::NOW);

        $recipient = $this->getDataGenerator()->create_user(['timezone' => 'Pacific/Auckland']);
        $this->allow_user_to_receive_notification($recipient);
        $secretexpiredtime = self::NOW - HOURSECS;

        $this->queue_task((int) $recipient->id, secretexpiredtime: $secretexpiredtime);

        $expecteddatetime = userdate(
            $secretexpiredtime,
            get_string('strftimedatetime', 'langconfig'),
            'Pacific/Auckland',
        );

        $sink = $this->redirectMessages();
        $this->runAdhocTasks(send_oauth2_secret_expired_notification_adhoc_task::class);
        $messages = $sink->get_messages();
        $sink->close();

        $this->assertCount(1, $messages);
        $this->assertStringContainsString($expecteddatetime, $messages[0]->smallmessage);
    }
}
