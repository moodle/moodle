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

use core\composer;
use core\composer\package_status;
use core\context\system;
use core\oauth2\server\client_manager;
use core\oauth2\server\entity\client_entity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see send_oauth2_secret_expiry_notifications_task}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(send_oauth2_secret_expiry_notifications_task::class)]
final class send_oauth2_secret_expiry_notifications_task_test extends \advanced_testcase {
    /**
     * Freeze the clock at a fixed, non-midnight instant, in the server timezone, on a fixed reference
     * date.
     *
     * Every test measures secret expiry windows relative to this instant, so the task's internal
     * "today at midnight" and "now" calculations are deterministic no matter what timezone or time of
     * day the test host runs at. The clock is frozen a couple of hours after midnight, mirroring a
     * scheduled task that runs some time after midnight rather than at the exact instant of midnight.
     *
     * @return \DateTimeImmutable Midnight on the reference date, in the server timezone.
     */
    private function freeze_clock(): \DateTimeImmutable {
        $timezone = new \DateTimeZone(\core_date::get_server_timezone());
        $now = new \DateTimeImmutable('2026-03-01 02:00:00', $timezone);

        $this->mock_clock_with_frozen_datetime($now);

        return $now->setTime(0, 0, 0);
    }

    /**
     * Advance the frozen clock by a given relative modifier (e.g. '+3 days').
     *
     * @param \DateTimeImmutable $current The instant the clock is currently frozen at.
     * @param string $modifier A DateTimeImmutable::modify() compatible relative modifier.
     * @return \DateTimeImmutable The new instant the clock is now frozen at.
     */
    private function advance_clock(\DateTimeImmutable $current, string $modifier): \DateTimeImmutable {
        $new = $current->modify($modifier);
        // Move the existing clock, as services already resolved from the container hold a reference to it.
        \core\di::get(\core\clock::class)->set_to($new->getTimestamp());

        return $new;
    }

    /**
     * Get a timestamp safely inside (i.e. not at either boundary of) the milestone bucket for a given
     * day count, so that fixtures used by wording/behaviour tests can't accidentally straddle a bucket
     * boundary.
     *
     * @param \DateTimeImmutable $today Midnight today, as returned by {@see self::freeze_clock()}.
     * @param int $days The milestone's day count (1, 7 or 30).
     * @return int
     */
    private function inside_bucket_timestamp(\DateTimeImmutable $today, int $days): int {
        return match ($days) {
            1 => $today->modify('+18 hours')->getTimestamp(), // Within (now, midnight+1 day].
            7 => $today->modify('+4 days')->getTimestamp(), // Within (midnight+1 day, midnight+7 days].
            30 => $today->modify('+18 days')->getTimestamp(), // Within (midnight+7 days, midnight+30 days].
            default => throw new \InvalidArgumentException("Unsupported milestone: {$days}"),
        };
    }

    /**
     * Create an active, confidential OAuth2 client owned by the system context.
     *
     * @param string $name The client name.
     * @return client_entity The created client.
     */
    private function create_client(string $name = 'Test client'): client_entity {
        return \core\di::get(client_manager::class)->create_client(
            name: $name,
            ownercontext: system::instance(),
            granttypes: [client_entity::GRANT_TYPE_CLIENT_CREDENTIALS],
        );
    }

    /**
     * Create a non-revoked secret for a client with a specific expiry time.
     *
     * @param client_entity $client The client to attach the secret to.
     * @param int $expirytime The expiry timestamp for the secret.
     * @return int The ID of the created secret record.
     */
    private function create_secret(client_entity $client, int $expirytime): int {
        global $DB;

        \core\di::get(client_manager::class)->create_secret($client->get_id(), $expirytime);

        return (int) $DB->get_field_sql(
            'SELECT MAX(id) FROM {oauth2_server_client_secrets} WHERE clientidentifier = ?',
            [$client->getIdentifier()],
        );
    }

    /**
     * Create a user who holds the capability required to manage OAuth2 clients (and therefore is
     * eligible to receive expiry notifications), by assigning them the manager role at system context.
     *
     * This exercises the get_users_by_capability() path in execute(), independently of the
     * get_admins() fallback for primary site administrators.
     *
     * @param array $userdata Additional user fields (e.g. ['suspended' => 1]).
     * @return \stdClass The created user.
     */
    private function create_eligible_user(array $userdata = []): \stdClass {
        global $DB;

        $user = $this->getDataGenerator()->create_user($userdata);
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST);
        role_assign($managerroleid, $user->id, system::instance()->id);

        return $user;
    }

    /**
     * Make a user THE (sole) primary site administrator (i.e. present in $CFG->siteadmins), without
     * granting them any capability via role assignment. This replaces whatever site admin(s) were
     * previously configured, but always leaves a valid one in place (never empty), so that
     * \core\cron::setup_user() (invoked by the adhoc task) never fails.
     *
     * This exercises the get_admins() fallback path in execute(), which exists because primary site
     * administrators do not always hold an explicit role assignment granting the capability.
     *
     * @param \stdClass $user The user to promote to (sole) site administrator.
     */
    private function make_site_admin(\stdClass $user): void {
        set_config('siteadmins', (string) $user->id);
    }

    /**
     * Remove every primary site administrator.
     *
     * Only safe to call in tests where no adhoc task will actually be queued/executed afterwards (e.g.
     * asserting a total absence of notifications), since an empty $CFG->siteadmins causes
     * \core\cron::setup_user() to fail with a TypeError as soon as it is invoked.
     */
    private function clear_site_admins(): void {
        set_config('siteadmins', '');
    }

    /**
     * Execute the scheduled task once, then drain (execute) every adhoc task it queued.
     *
     * manager::queue_adhoc_task() only inserts a database row; the queued
     * send_oauth2_secret_expiry_notification_adhoc_task instances - which are the ones which actually call
     * message_send() - are never run unless explicitly processed. Any test asserting on delivered
     * messages must call this rather than the scheduled task's execute() alone.
     */
    private function run_task_and_drain_adhoc_queue(): void {
        \core\di::make(send_oauth2_secret_expiry_notifications_task::class)->execute();
        $this->runAdhocTasks(send_oauth2_secret_expiry_notification_adhoc_task::class);
    }

    /**
     * Filter out any message addressed to the given (site admin) user id.
     *
     * Used by tests that leave the default site administrator in place (to avoid the
     * \core\cron::setup_user() crash) but want to assert only
     * on messages received by their own, explicitly-created, eligible user(s).
     *
     * @param array $messages Messages, as returned by a message sink's get_messages().
     * @param int $adminid The site administrator's user id to exclude.
     * @return array The filtered messages, re-indexed.
     */
    private function messages_excluding_admin(array $messages, int $adminid): array {
        return array_values(array_filter(
            $messages,
            static fn($message) => (int) $message->useridto !== $adminid,
        ));
    }

    /**
     * No notification is sent when the 'league/oauth2-server' composer package is not installed, even
     * if there are secrets expiring soon and eligible users to notify.
     */
    public function test_execute_no_composer_package_installed(): void {
        $this->resetAfterTest();
        $composer = $this->createMock(composer::class);
        $composer->method('get_package_status')
            ->with('league/oauth2-server')
            ->willReturn(new package_status(false, true, '1.0.0', null));
        \core\di::set(composer::class, $composer);
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertSame(0, $sink->count());
    }

    /**
     * No notification is sent when there are no secrets expiring within any milestone bucket (0-30
     * days).
     */
    public function test_execute_no_secrets_expiring(): void {
        $this->resetAfterTest();
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client();
        // Expires far outside the entire 0-30 day catch-up range.
        $this->create_secret($client, $today->modify('+1 year')->getTimestamp());

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertSame(0, $sink->count());
    }

    /**
     * A secret that already expired (in the past relative to "now") does not trigger a notification:
     * the innermost bucket's lower bound is the current instant, not the start of today.
     */
    public function test_execute_already_expired_secret_excluded(): void {
        $this->resetAfterTest();
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client();
        $this->create_secret($client, $today->modify('+1 hour')->getTimestamp());

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertSame(0, $sink->count());
    }

    /**
     * No notification is sent when there are secrets expiring soon, but nobody holds the capability to
     * manage OAuth2 clients and there are no primary site administrators.
     *
     * This is the only scenario in this suite where it is safe to empty $CFG->siteadmins: with no
     * eligible recipients at all, no adhoc task is ever queued (or executed), so
     * \core\cron::setup_user() is never invoked.
     */
    public function test_execute_no_eligible_users(): void {
        $this->resetAfterTest();
        $this->clear_site_admins();
        $today = $this->freeze_clock();

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertSame(0, $sink->count());
    }

    /**
     * A user who holds the 'moodle/site:manageoauth2clients' capability via an explicit role
     * assignment (but who is not a primary site administrator) receives the notification.
     */
    public function test_notification_sent_to_user_with_explicit_capability(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $user = $this->create_eligible_user();

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(1, $messages);
        $this->assertEquals($user->id, $messages[0]->useridto);
    }

    /**
     * A primary site administrator (present in $CFG->siteadmins) receives the notification even
     * without an explicit role assignment granting the capability, via the get_admins() fallback.
     */
    public function test_notification_sent_to_site_admin_without_explicit_capability(): void {
        $this->resetAfterTest();
        $today = $this->freeze_clock();

        $user = $this->getDataGenerator()->create_user();
        $this->make_site_admin($user);

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $this->assertEquals($user->id, $messages[0]->useridto);
    }

    /**
     * Data provider for singular/plural notification-bucket tests: one entry per milestone bucket.
     *
     * @return array[]
     */
    public static function notification_day_provider(): array {
        return [
            '1 day bucket' => [1],
            '7 day bucket' => [7],
            '30 day bucket' => [30],
        ];
    }

    /**
     * A single secret expiring within a given milestone bucket produces a notification using the
     * singular ("1 secret ...") wording, with the correct subject and smallmessage.
     *
     * @param int $days The milestone bucket being tested (1, 7 or 30).
     */
    #[DataProvider('notification_day_provider')]
    public function test_singular_warning_for_notification_bucket(int $days): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client('Singular client');
        $expirytime = $this->inside_bucket_timestamp($today, $days);
        $this->create_secret($client, $expirytime);

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(1, $messages);

        $expirydays = \core\di::get(\core\oauth2\server\expiry_helper::class)->get_remaining_calendar_days($expirytime);

        if ($days > 1) {
            $expectedsubject = get_string(
                'oauth2server_secretexpireindaysnotificationsubject',
                'admin',
                (object) ['clientname' => 'Singular client', 'expirydays' => $expirydays],
            );
            $expectedwarning = get_string(
                'oauth2server_secretexpiresindays',
                'admin',
                (object) ['clientname' => 'Singular client', 'expirydays' => $expirydays],
            );
        } else {
            $expectedsubject = get_string('oauth2server_secretexpireindaynotificationsubject', 'admin', 'Singular client');
            $expectedwarning = get_string('oauth2server_secretexpiresinday', 'admin', 'Singular client');
        }

        $this->assertSame($expectedsubject, $messages[0]->subject);
        $this->assertSame($expectedwarning, $messages[0]->smallmessage);
        // The rendered HTML body should include the call-to-action link text.
        $this->assertStringContainsString(get_string('oauth2server_managesecrets', 'admin'), $messages[0]->fullmessagehtml);
    }

    /**
     * Two secrets expiring within the same milestone bucket, for the same client, produce a single
     * notification using the plural ("N secrets ...") wording.
     *
     * @param int $days The milestone bucket being tested (1, 7 or 30).
     */
    #[DataProvider('notification_day_provider')]
    public function test_plural_warning_for_notification_bucket(int $days): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client('Plural client');
        $expirytime = $this->inside_bucket_timestamp($today, $days);
        $this->create_secret($client, $expirytime);
        // A second secret, a little earlier but still solidly inside the same bucket.
        $this->create_secret($client, $expirytime - HOURSECS);

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(1, $messages);

        // The reported day count is based on the earliest (soonest-expiring) of the two secrets.
        $expirydays = \core\di::get(\core\oauth2\server\expiry_helper::class)->get_remaining_calendar_days($expirytime - HOURSECS);

        if ($days > 1) {
            $expectedwarning = get_string(
                'oauth2server_secretsexpireindays',
                'admin',
                (object) ['clientname' => 'Plural client', 'secretcount' => 2, 'expirydays' => $expirydays],
            );
        } else {
            $expectedwarning = get_string(
                'oauth2server_secretsexpireinday',
                'admin',
                (object) ['clientname' => 'Plural client', 'secretcount' => 2],
            );
        }

        $this->assertSame($expectedwarning, $messages[0]->smallmessage);
    }

    /**
     * A client with secrets expiring in two different milestone buckets (e.g. one in the 1-day bucket
     * and another in the 30-day bucket) receives a separate notification per bucket, each scoped to
     * that bucket's own secret count, rather than one aggregated notification.
     */
    public function test_client_with_secrets_in_multiple_buckets_sends_separate_notifications(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client('Multi-bucket client');
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 30));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(2, $messages);

        $smallmessages = array_map(static fn($message) => $message->smallmessage, $messages);
        sort($smallmessages);

        $expected30days = \core\di::get(\core\oauth2\server\expiry_helper::class)->get_remaining_calendar_days(
            $this->inside_bucket_timestamp($today, 30),
        );
        $expected = [
            get_string('oauth2server_secretexpiresinday', 'admin', 'Multi-bucket client'),
            get_string(
                'oauth2server_secretexpiresindays',
                'admin',
                (object) ['clientname' => 'Multi-bucket client', 'expirydays' => $expected30days],
            ),
        ];
        sort($expected);

        $this->assertSame($expected, $smallmessages);
    }

    /**
     * A revoked secret is never included in the expiry count, even if its expiry time falls within a
     * milestone bucket.
     */
    public function test_revoked_secret_excluded(): void {
        global $DB;

        $this->resetAfterTest();
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client();
        $secretid = $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));
        $DB->set_field('oauth2_server_client_secrets', 'revoked', client_entity::SECRET_REVOKED_YES, ['id' => $secretid]);

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertSame(0, $sink->count());
    }

    /**
     * A secret belonging to a disabled client is never included, even if it is not itself revoked and
     * its expiry time falls within a milestone bucket.
     */
    public function test_disabled_client_secret_excluded(): void {
        $this->resetAfterTest();
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));
        \core\di::get(client_manager::class)->disable_client($client->get_id());

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertSame(0, $sink->count());
    }

    /**
     * A secret expiring exactly at the shared boundary between two adjacent buckets (i.e. the wider
     * bucket's lower bound, which equals the narrower bucket's upper bound) is captured exactly once,
     * by the narrower (inner) bucket - not twice, and not missed entirely. This confirms the buckets
     * are contiguous and non-overlapping, per the milestone-bucket "catch-up" design.
     */
    public function test_secret_at_shared_bucket_boundary_is_captured_once_by_inner_bucket(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $this->freeze_clock();
        $this->create_eligible_user();

        [$lowerbound7] = \core\di::get(\core\oauth2\server\expiry_helper::class)->get_milestone_bounds(7, 1);
        [, $upperbound1] = \core\di::get(\core\oauth2\server\expiry_helper::class)->get_milestone_bounds(1, 0);
        $this->assertSame($upperbound1, $lowerbound7, 'Buckets are expected to be contiguous.');

        $client = $this->create_client();
        $this->create_secret($client, $lowerbound7);

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(1, $messages);
        // Captured via the 1-day bucket's inclusive upper bound, not the 7-day bucket.
        $this->assertSame(
            get_string('oauth2server_secretexpiresinday', 'admin', 'Test client'),
            $messages[0]->smallmessage,
        );
    }

    /**
     * A secret expiring exactly at a bucket's upper bound is included (the upper bound is inclusive:
     * `expirytime <= upperbound`).
     */
    public function test_secret_at_bucket_upperbound_is_included(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $this->freeze_clock();
        $this->create_eligible_user();

        [, $upperbound] = \core\di::get(\core\oauth2\server\expiry_helper::class)->get_milestone_bounds(7, 1);
        $client = $this->create_client();
        $this->create_secret($client, $upperbound);

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(1, $messages);
    }

    /**
     * A suspended user is skipped, even if they otherwise hold the capability to manage OAuth2
     * clients.
     */
    public function test_suspended_user_skipped(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user(['suspended' => 1]);

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(0, $messages);
    }

    /**
     * Every eligible user is notified about the same client, not just the first one found.
     */
    public function test_multiple_eligible_users_all_notified(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $user1 = $this->create_eligible_user();
        $user2 = $this->create_eligible_user();

        $client = $this->create_client();
        $this->create_secret($client, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $recipients = array_map(static fn($message) => (int) $message->useridto, $messages);
        sort($recipients);

        $this->assertSame([(int) $user1->id, (int) $user2->id], $recipients);
    }

    /**
     * Multiple clients with secrets expiring in the same bucket are each notified independently.
     */
    public function test_multiple_clients_notified_independently(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $clienta = $this->create_client('Client A');
        $clientb = $this->create_client('Client B');
        $this->create_secret($clienta, $this->inside_bucket_timestamp($today, 1));
        $this->create_secret($clientb, $this->inside_bucket_timestamp($today, 1));

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(2, $messages);

        $subjects = array_map(static fn($message) => $message->subject, $messages);
        sort($subjects);

        $expected = [
            get_string('oauth2server_secretexpireindaynotificationsubject', 'admin', 'Client A'),
            get_string('oauth2server_secretexpireindaynotificationsubject', 'admin', 'Client B'),
        ];
        sort($expected);

        $this->assertSame($expected, $subjects);
    }

    /**
     * A secret that has just entered a milestone bucket for the first time is notified once, using the
     * *actual* number of calendar days remaining (which need not equal the bucket's nominal 1/7/30 day
     * threshold) - this is the "catch-up" behaviour that lets the task recover from a missed run.
     */
    public function test_catchup_notification_uses_actual_remaining_days(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        // 5 days left: inside the (1, 7] bucket, but not equal to its nominal 7-day threshold - as if
        // the task had failed to run for a couple of days and only now "catches up".
        $client = $this->create_client('Catch-up client');
        $expirytime = $today->modify('+5 days')->getTimestamp();
        $this->create_secret($client, $expirytime);

        $sink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $messages = $this->messages_excluding_admin($sink->get_messages(), $adminid);
        $this->assertCount(1, $messages);

        $expectedwarning = get_string(
            'oauth2server_secretexpiresindays',
            'admin',
            (object) ['clientname' => 'Catch-up client', 'expirydays' => 5],
        );
        $this->assertSame($expectedwarning, $messages[0]->smallmessage);
    }

    /**
     * Once a secret has been notified for a milestone, running the task again immediately afterwards
     * (same bucket, no time elapsed) does not send a duplicate notification.
     */
    public function test_no_duplicate_notification_on_immediate_rerun(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $today = $this->freeze_clock();
        $this->create_eligible_user();

        $client = $this->create_client();
        $this->create_secret($client, $today->modify('+5 days')->getTimestamp());

        $firstsink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();
        $this->assertCount(1, $this->messages_excluding_admin($firstsink->get_messages(), $adminid));
        $firstsink->close();

        $secondsink = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();

        $this->assertCount(0, $this->messages_excluding_admin($secondsink->get_messages(), $adminid));
    }

    /**
     * A secret is silent on every subsequent run while it remains within the bucket it was already
     * notified for, and only produces a new notification once it progresses into the next, tighter
     * bucket - matching the intended "notify once per milestone, catch up only on anomaly" design.
     */
    public function test_silent_until_next_milestone_then_notifies_again(): void {
        $this->resetAfterTest();
        $adminid = (int) get_admin()->id;
        $now = $this->freeze_clock();
        $this->create_eligible_user();

        // Starts with 5 days left: inside the (1, 7] bucket.
        $client = $this->create_client('Lifecycle client');
        $expirytime = $now->modify('+5 days')->getTimestamp();
        $this->create_secret($client, $expirytime);

        // Initial catch-up notification for the 7-day bucket.
        $sink1 = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();
        $this->assertCount(1, $this->messages_excluding_admin($sink1->get_messages(), $adminid));
        $sink1->close();

        // 3 days later: 2 days left, still inside the same (1, 7] bucket - must stay silent.
        $now = $this->advance_clock($now, '+3 days');
        $sink2 = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();
        $this->assertCount(0, $this->messages_excluding_admin($sink2->get_messages(), $adminid));
        $sink2->close();

        // A further day and a half later: under 1 day left, now inside the (0, 1] bucket - a fresh
        // notification for this tighter milestone must be sent.
        $now = $this->advance_clock($now, '+36 hours');
        $sink3 = $this->redirectMessages();
        $this->run_task_and_drain_adhoc_queue();
        $messages = $this->messages_excluding_admin($sink3->get_messages(), $adminid);
        $this->assertCount(1, $messages);
        $this->assertSame(
            get_string('oauth2server_secretexpiresinday', 'admin', 'Lifecycle client'),
            $messages[0]->smallmessage,
        );
        $sink3->close();
    }

    /**
     * get_name() returns the human-readable task name used in the scheduled task admin UI.
     */
    public function test_get_name(): void {
        $this->resetAfterTest();

        $task = \core\di::make(send_oauth2_secret_expiry_notifications_task::class);

        $this->assertSame(get_string('oauth2server_sendsecretexpirynotifications', 'admin'), $task->get_name());
    }
}
