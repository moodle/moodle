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

use core\api\repository\api_token_repository;
use core\api\token_manager;
use core\url;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for {@see personal_access_token_expiry_task} and the notices it queues.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(personal_access_token_expiry_task::class)]
#[CoversClass(send_personal_access_token_expiry_notices::class)]
final class personal_access_token_expiry_task_test extends \advanced_testcase {
    /** @var int The fixed 'now' every token is minted at. */
    private const NOW = 1786000000;

    /** @var \frozen_clock The clock the task and the token code both read. */
    private \frozen_clock $clock;

    #[\Override]
    protected function setUp(): void {
        global $CFG;

        parent::setUp();
        $this->resetAfterTest();
        $this->clock = $this->mock_clock_with_frozen(self::NOW);

        // Owners hold the capability to create tokens, and the message provider requires it.
        assign_capability('moodle/api:createtoken', CAP_ALLOW, $CFG->defaultuserroleid, \context_system::instance());
    }

    /**
     * Mint a token through the real manager, as the management page does.
     *
     * @param int $userid The owner.
     * @param string $name The token name.
     * @param int $lifetime How long the token lasts, in seconds.
     * @return int The token id.
     */
    private function issue_token(int $userid, string $name = 'Attendance export', int $lifetime = WEEKSECS): int {
        $manager = \core\di::make(token_manager::class);
        $token = $manager->issue_token($name, $userid, ['core_grades:grade:read'], null, self::NOW + $lifetime);

        return \core\di::get(api_token_repository::class)->get_from_token($token)->get_id();
    }

    /**
     * Run the task and the notices it queues, and return what was sent.
     *
     * @return \stdClass[]
     */
    private function run_task(): array {
        $sink = $this->redirectMessages();
        \core\di::make(personal_access_token_expiry_task::class)->execute();
        ob_start();
        try {
            $this->runAdhocTasks(send_personal_access_token_expiry_notices::class);
        } finally {
            ob_end_clean();
        }
        $messages = $sink->get_messages_by_component_and_type('moodle', 'personalaccesstokenexpiry');
        $sink->close();

        return $messages;
    }

    /**
     * Each owner's notices are sent by a task of their own, running as them, queued once.
     */
    public function test_queues_one_task_per_owner(): void {
        $first = $this->getDataGenerator()->create_user();
        $second = $this->getDataGenerator()->create_user();
        $this->issue_token($first->id, 'One');
        $this->issue_token($first->id, 'Two');
        $this->issue_token($second->id, 'Three');

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $sink = $this->redirectMessages();
        \core\di::make(personal_access_token_expiry_task::class)->execute();
        \core\di::make(personal_access_token_expiry_task::class)->execute();

        $queued = manager::get_adhoc_tasks(send_personal_access_token_expiry_notices::class);
        $this->assertEqualsCanonicalizing([$first->id, $second->id], array_map(fn($task) => $task->get_userid(), $queued));
        $this->assertSame(0, $sink->count());
        $sink->close();
    }

    /**
     * The notice runs as its owner, so the expiry is given in the owner's own time zone.
     */
    public function test_expiry_is_in_owner_time_zone(): void {
        $this->setTimezone('Europe/London');
        $user = $this->getDataGenerator()->create_user(['timezone' => 'Pacific/Auckland']);
        $this->issue_token($user->id);

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $message = current($this->run_task());

        $format = get_string('strftimedatetime', 'langconfig');
        $this->assertStringContainsString(userdate(self::NOW + WEEKSECS, $format, 'Pacific/Auckland'), $message->fullmessagehtml);
        $this->assertStringNotContainsString(userdate(self::NOW + WEEKSECS, $format, 'Europe/London'), $message->fullmessagehtml);
    }

    /**
     * A week-long token is only reminded a day out: the earlier reminders would come at creation.
     */
    public function test_no_notice_before_window(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);

        $this->assertEmpty($this->run_task());

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS - 1);
        $this->assertEmpty($this->run_task());
    }

    /**
     * A long-lived token is reminded 30, 7 and 1 days before it expires, once each.
     */
    public function test_long_lived_token_is_reminded_three_times(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id, lifetime: YEARSECS);
        $expiry = self::NOW + YEARSECS;

        $sent = [];
        foreach ([31, 30, 29, 7, 6, 1, 0.5] as $daysleft) {
            $this->clock->set_to($expiry - (int) ($daysleft * DAYSECS));
            $sent[(string) $daysleft] = count($this->run_task());
        }

        $this->assertSame(['31' => 0, '30' => 1, '29' => 0, '7' => 1, '6' => 0, '1' => 1, '0.5' => 0], $sent);
    }

    /**
     * Reminders missed while the task did not run are not all sent at once.
     */
    public function test_missed_reminders_send_one_notice(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id, lifetime: YEARSECS);

        $this->clock->set_to(self::NOW + YEARSECS - DAYSECS);

        $this->assertCount(1, $this->run_task());
    }

    /**
     * A token inside the window warns its owner once, naming it and linking to the management page.
     */
    public function test_warns_once_inside_window(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $messages = $this->run_task();

        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertEquals($user->id, $message->useridto);
        $this->assertStringContainsString('Attendance export', $message->subject);
        $this->assertStringContainsString(token_manager::format_datetime(self::NOW + WEEKSECS), $message->fullmessagehtml);
        $this->assertEquals((new url('/user/personalaccesstokens.php'))->out(false), $message->contexturl);

        $this->assertEmpty($this->run_task());
    }

    /**
     * Once a token has expired its owner is told once, with a different message to the warning.
     */
    public function test_expired_notice_sent_once_after_warning(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $warning = $this->run_task();

        $this->clock->set_to(self::NOW + WEEKSECS);
        $expired = $this->run_task();

        $this->assertCount(1, $expired);
        $this->assertNotEquals(reset($warning)->subject, reset($expired)->subject);
        $this->assertStringContainsString('Attendance export', reset($expired)->subject);

        $this->assertEmpty($this->run_task());
    }

    /**
     * A token that lapses between two runs, never having been warned, gets only the expired notice.
     */
    public function test_expired_without_warning_sends_only_expired_notice(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);

        $this->clock->set_to(self::NOW + WEEKSECS + DAYSECS);
        $messages = $this->run_task();

        $this->assertCount(1, $messages);
        $this->assertEmpty($this->run_task());
    }

    /**
     * Each token is its own notice, so two tokens expiring together send two.
     */
    public function test_one_notice_per_token(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id, 'First');
        $this->issue_token($user->id, 'Second');

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $subjects = array_column($this->run_task(), 'subject');

        $this->assertCount(2, $subjects);
        $this->assertCount(1, array_filter($subjects, fn($subject) => str_contains($subject, 'First')));
        $this->assertCount(1, array_filter($subjects, fn($subject) => str_contains($subject, 'Second')));
    }

    /**
     * The token name is the owner's own text, so it is escaped in the HTML body.
     */
    public function test_token_name_is_escaped_in_html(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id, '<b>Nightly</b>');

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $message = current($this->run_task());

        $this->assertStringContainsString('&lt;b&gt;Nightly&lt;/b&gt;', $message->fullmessagehtml);
        $this->assertStringNotContainsString('<b>Nightly</b>', $message->fullmessagehtml);
    }

    /**
     * A revoked token has already been dealt with by its owner, so it is never mentioned again.
     */
    public function test_revoked_token_is_not_notified(): void {
        $user = $this->getDataGenerator()->create_user();
        $id = $this->issue_token($user->id);
        \core\di::get(api_token_repository::class)->revoke_token($id);

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $this->assertEmpty($this->run_task());

        $this->clock->set_to(self::NOW + WEEKSECS);
        $this->assertEmpty($this->run_task());
    }

    /**
     * An owner who can no longer create tokens cannot act on a notice, so none is sent.
     */
    public function test_owner_without_capability_is_not_notified(): void {
        global $CFG;

        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);
        unassign_capability('moodle/api:createtoken', $CFG->defaultuserroleid, \context_system::instance());

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);

        $this->assertEmpty($this->run_task());
        // Skipped by the task, rather than refused by the message API with a debugging notice.
        $this->assertDebuggingNotCalled();
    }

    /**
     * An owner who could not be told is warned once they can be, as the token was left unmarked.
     */
    public function test_owner_regaining_capability_is_warned(): void {
        global $CFG;

        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);
        $systemcontext = \context_system::instance();
        unassign_capability('moodle/api:createtoken', $CFG->defaultuserroleid, $systemcontext);
        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);
        $this->run_task();

        assign_capability('moodle/api:createtoken', CAP_ALLOW, $CFG->defaultuserroleid, $systemcontext);
        $messages = $this->run_task();

        $this->assertCount(1, $messages);
    }

    /**
     * An expiry long past is not announced, however late the owner becomes reachable.
     */
    public function test_no_expired_notice_long_after_expiry(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->issue_token($user->id);

        $this->clock->set_to(self::NOW + WEEKSECS + (token_manager::EXPIRED_NOTICE_DAYS * DAYSECS) + 1);

        $this->assertEmpty($this->run_task());
    }

    /**
     * Suspended and deleted owners are not sent anything.
     */
    public function test_inactive_owner_is_not_notified(): void {
        $suspended = $this->getDataGenerator()->create_user(['suspended' => 1]);
        $deleted = $this->getDataGenerator()->create_user();
        $this->issue_token($suspended->id);
        $this->issue_token($deleted->id);
        delete_user($deleted);

        $this->clock->set_to(self::NOW + WEEKSECS - DAYSECS);

        $this->assertEmpty($this->run_task());
    }
}
