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

declare(strict_types=1);

namespace core\task;

use core_user;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see send_oauth2_secret_expiry_notification_adhoc_task}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(send_oauth2_secret_expiry_notification_adhoc_task::class)]
final class send_oauth2_secret_expiry_notification_adhoc_task_test extends \advanced_testcase {
    /**
     * Create a user who holds the 'moodle/site:manageoauth2clients' capability.
     *
     * message_send() only actually delivers a notification to a recipient who is a permitted
     * recipient of its message provider; the 'oauth2_secret_expiry_warning' provider is restricted to
     * users holding this capability (see lib/db/messages.php), so an arbitrary user without it would
     * silently receive nothing, regardless of what the adhoc task itself does.
     *
     * @param array $userdata Additional user fields (e.g. ['suspended' => 1]).
     * @return \stdClass
     */
    private function create_recipient_user(array $userdata = []): \stdClass {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user($userdata);
        $generator->role_assign('manager', $user->id);

        return $user;
    }

    /**
     * Build and execute an adhoc task with the given custom data.
     *
     * @param int $userid The recipient user the task runs as.
     * @param array $customdata Custom data, merged over sensible defaults.
     * @return void
     */
    private function execute_task(int $userid, array $customdata = []): void {
        $customdata += [
            'clientid' => 1,
            'clientname' => 'Test client',
            'secretcount' => 1,
            'expirydays' => 1,
        ];

        $task = \core\di::make(send_oauth2_secret_expiry_notification_adhoc_task::class);
        $task->set_userid($userid);
        $task->set_custom_data($customdata);
        manager::queue_adhoc_task($task);

        $this->runAdhocTasks(send_oauth2_secret_expiry_notification_adhoc_task::class);
    }

    /**
     * The task cannot run as a deleted user, so no message is sent.
     */
    public function test_execute_deleted_user_is_not_run(): void {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $DB->set_field('user', 'deleted', 1, ['id' => $user->id]);

        $sink = $this->redirectMessages();
        try {
            $this->execute_task((int) $user->id);
            $this->fail('Expected an exception for a deleted user');
        } catch (\moodle_exception $e) {
            $this->assertSame('userdeleted', $e->errorcode);
        }

        $this->assertSame(0, $sink->count());
    }

    /**
     * The task cannot run as a suspended user, so no message is sent.
     */
    public function test_execute_suspended_user_is_not_run(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user(['suspended' => 1]);

        $sink = $this->redirectMessages();
        try {
            $this->execute_task((int) $user->id);
            $this->fail('Expected an exception for a suspended user');
        } catch (\moodle_exception $e) {
            $this->assertSame('suspended', $e->errorcode);
        }

        $this->assertSame(0, $sink->count());
    }

    /**
     * A valid, active user is sent exactly one message, addressed to them.
     */
    public function test_execute_sends_message_to_valid_user(): void {
        $this->resetAfterTest();
        $user = $this->create_recipient_user();

        $sink = $this->redirectMessages();
        $this->execute_task((int) $user->id);

        $this->assertSame(1, $sink->count());
        $messages = $sink->get_messages();
        $this->assertEquals($user->id, $messages[0]->useridto);
    }

    /**
     * The message envelope (component, message provider name, sender, notification flag and format) is
     * set correctly, regardless of wording.
     */
    public function test_execute_sets_message_envelope_fields(): void {
        $this->resetAfterTest();
        $user = $this->create_recipient_user();

        $sink = $this->redirectMessages();
        $this->execute_task((int) $user->id);

        $message = $sink->get_messages()[0];
        $this->assertSame('moodle', $message->component);
        $this->assertSame('oauth2_secret_expiry_warning', $message->eventtype);
        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals(1, $message->notification);
        $this->assertEquals(FORMAT_HTML, $message->fullmessageformat);
    }

    /**
     * Data provider of (expirydays, secretcount) combinations exercising every branch of the singular
     * vs. plural, and "1 day" vs. "N days", wording logic.
     *
     * @return array[]
     */
    public static function wording_provider(): array {
        return [
            '1 day, 1 secret' => [1, 1],
            '1 day, many secrets' => [1, 3],
            'many days, 1 secret' => [15, 1],
            'many days, many secrets' => [15, 4],
        ];
    }

    /**
     * The subject line uses the singular "1 day" wording when expirydays === 1, and the plural "N
     * days" wording (with the client name and day count interpolated) otherwise - independent of the
     * secret count, since the subject never mentions the secret count.
     *
     * @param int $expirydays
     * @param int $secretcount
     */
    #[DataProvider('wording_provider')]
    public function test_execute_subject_wording(int $expirydays, int $secretcount): void {
        $this->resetAfterTest();
        $user = $this->create_recipient_user();

        $sink = $this->redirectMessages();
        $this->execute_task((int) $user->id, [
            'clientname' => 'Acme client',
            'expirydays' => $expirydays,
            'secretcount' => $secretcount,
        ]);

        $expected = $expirydays > 1
            ? get_string(
                'oauth2server_secretexpireindaysnotificationsubject',
                'admin',
                (object) ['clientname' => 'Acme client', 'expirydays' => $expirydays],
            )
            : get_string('oauth2server_secretexpireindaynotificationsubject', 'admin', 'Acme client');

        $this->assertSame($expected, $sink->get_messages()[0]->subject);
    }

    /**
     * The warning text (smallmessage) is worded according to all four combinations of singular/plural
     * day count and secret count.
     *
     * @param int $expirydays
     * @param int $secretcount
     */
    #[DataProvider('wording_provider')]
    public function test_execute_warning_text_wording(int $expirydays, int $secretcount): void {
        $this->resetAfterTest();
        $user = $this->create_recipient_user();

        $sink = $this->redirectMessages();
        $this->execute_task((int) $user->id, [
            'clientname' => 'Acme client',
            'expirydays' => $expirydays,
            'secretcount' => $secretcount,
        ]);

        if ($expirydays > 1) {
            $expected = $secretcount > 1
                ? get_string(
                    'oauth2server_secretsexpireindays',
                    'admin',
                    (object) ['clientname' => 'Acme client', 'secretcount' => $secretcount, 'expirydays' => $expirydays],
                )
                : get_string(
                    'oauth2server_secretexpiresindays',
                    'admin',
                    (object) ['clientname' => 'Acme client', 'expirydays' => $expirydays],
                );
        } else {
            $expected = $secretcount > 1
                ? get_string(
                    'oauth2server_secretsexpireinday',
                    'admin',
                    (object) ['clientname' => 'Acme client', 'secretcount' => $secretcount],
                )
                : get_string('oauth2server_secretexpiresinday', 'admin', 'Acme client');
        }

        $message = $sink->get_messages()[0];
        $this->assertSame($expected, $message->smallmessage);
        // The plain-text body is derived from the rendered HTML, so it should contain the same
        // wording somewhere in it.
        $this->assertStringContainsString($expected, $message->fullmessage);
    }

    /**
     * The rendered HTML body includes a working link to the "Manage secrets" page for the correct
     * client id, and the call-to-action text.
     */
    public function test_execute_html_body_includes_manage_secrets_link(): void {
        $this->resetAfterTest();
        $user = $this->create_recipient_user();

        $sink = $this->redirectMessages();
        $this->execute_task((int) $user->id, ['clientid' => 42]);

        $expectedurl = \core\router\util::get_path_for_callable(
            [\core_admin\route\controller\oauth2\server\client_management::class, 'manage_client_secrets'],
            ['client' => 42],
        )->out(false);

        $message = $sink->get_messages()[0];
        $this->assertStringContainsString(get_string('oauth2server_managesecrets', 'admin'), $message->fullmessagehtml);
        $this->assertStringContainsString($expectedurl, $message->fullmessagehtml);
    }
}
