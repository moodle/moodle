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

use core\oauth2\server\entity\client_entity;
use core\oauth2\server\expiry_helper;
use dml_exception;

/**
 * Scheduled task to scan and queue notifications for expiring OAuth2 client secrets.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_oauth2_secret_expiry_notifications_task extends scheduled_task {
    /**
     * Constructor.
     *
     * @param \core\clock $clock The clock.
     * @param \core\composer $composer The composer helper.
     * @param expiry_helper $expiryhelper The expiry helper.
     */
    public function __construct(
        /** @var \core\clock $clock The clock. */
        private readonly \core\clock $clock,
        /** @var \core\composer $composer The composer helper. */
        private readonly \core\composer $composer,
        /** @var expiry_helper $expiryhelper The expiry helper. */
        private readonly expiry_helper $expiryhelper,
    ) {
    }

    /**
     * Expiry notification milestones defined as non-overlapping time buckets `(lower, days]`.
     *
     * Each entry specifies the target milestone threshold (`days`) as the bucket's upper bound and
     * the lower bound (`lower`) where the preceding milestone ends. This enables catch-up capability
     * if scheduled execution is delayed.
     */
    private const EXPIRY_NOTIFICATION_MILESTONES = [
        ['days' => 1, 'lower' => 0],
        ['days' => 7, 'lower' => 1],
        ['days' => 30, 'lower' => 7],
    ];

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('oauth2server_sendsecretexpirynotifications', 'admin');
    }

    /**
     * Execute the task.
     *
     * @return void
     * @throws dml_exception
     */
    public function execute(): void {
        // Ensure that the 'league/oauth2-server' composer package is installed.
        // If it's not installed, OAuth2 authentication and client/secret management is not available, so there's no
        // need to execute this task further.
        $package = $this->composer->get_package_status('league/oauth2-server');

        if (!$package->installed) {
            return;
        }

        // Fetch users with explicit capability to manage OAuth 2 clients.
        $users = get_users_by_capability(
            \context_system::instance(),
            'moodle/site:manageoauth2clients',
            'u.*',
        );

        // Primary site administrators ($CFG->siteadmins) do not always have explicit system role assignments,
        // which causes get_users_by_capability() to miss them.
        $users += get_admins();

        // Suspended users cannot receive notifications, so drop them.
        $users = array_filter($users, fn($user) => empty($user->suspended));

        if (empty($users)) {
            return;
        }

        foreach (self::EXPIRY_NOTIFICATION_MILESTONES as $milestone) {
            $days = $milestone['days'];
            $lower = $milestone['lower'];

            $clients = $this->get_clients_with_expiring_secrets_for_milestone($days, $lower);

            foreach ($clients as $clientid => $clientinfo) {
                $this->queue_notifications_and_update_state($users, $clientid, $clientinfo);
            }
        }
    }

    /**
     * Get OAuth2 clients with secrets expiring within a specific milestone bucket.
     *
     * @param int $days Target milestone days (e.g., 30, 7, 1).
     * @param int $lower Lower bound days for this bucket (e.g., 7, 1, 0).
     * @return array
     * @throws dml_exception
     */
    private function get_clients_with_expiring_secrets_for_milestone(int $days, int $lower): array {
        global $DB;

        [$lowerbound, $upperbound] = $this->expiryhelper->get_milestone_bounds($days, $lower);

        $sql = "SELECT s.id AS secretid, c.id AS clientid, c.name AS clientname, s.expirytime, s.lastexpirynotification
                  FROM {oauth2_server_client_secrets} s
                  JOIN {oauth2_server_clients} c ON c.clientidentifier = s.clientidentifier
                 WHERE s.revoked = :revoked
                   AND c.status = :status
                   AND s.expirytime > :lowerbound
                   AND s.expirytime <= :upperbound";

        $records = $DB->get_records_sql(
            $sql,
            [
                'lowerbound' => $lowerbound,
                'upperbound' => $upperbound,
                'revoked' => client_entity::SECRET_REVOKED_NO,
                'status' => client_entity::STATUS_ACTIVE,
            ],
        );

        $clientsecrets = [];
        foreach ($records as $record) {
            $lastexpirynotification = $record->lastexpirynotification ? (int) $record->lastexpirynotification : null;

            // Skip secrets that have already received a notification for this milestone.
            if ($this->expiryhelper->has_been_notified_for_milestone($lastexpirynotification, (int) $record->expirytime, $days)) {
                continue;
            }

            if (!isset($clientsecrets[$record->clientid])) {
                $clientsecrets[$record->clientid] = [
                    'clientname' => $record->clientname,
                    'secretids' => [],
                    'minexpirytime' => (int) $record->expirytime,
                ];
            } else {
                $clientsecrets[$record->clientid]['minexpirytime'] = min(
                    $clientsecrets[$record->clientid]['minexpirytime'],
                    (int) $record->expirytime
                );
            }
            $clientsecrets[$record->clientid]['secretids'][] = (int) $record->secretid;
        }

        return $clientsecrets;
    }

    /**
     * Queue adhoc tasks for all eligible recipient users and flag secrets as notified in the DB atomically.
     *
     * @param array $users List of eligible recipient users.
     * @param int $clientid Client database ID.
     * @param array $clientinfo Array holding client details, secret IDs, and minimum expiry timestamp.
     * @return void
     */
    private function queue_notifications_and_update_state(array $users, int $clientid, array $clientinfo): void {
        global $DB;

        $secretids = $clientinfo['secretids'];
        $secretcount = count($secretids);

        // Dynamically compute exact remaining days for catch-up accuracy.
        $actualdays = $this->expiryhelper->get_remaining_calendar_days($clientinfo['minexpirytime']);

        // Wrap operations in a delegated transaction for atomic safety.
        $transaction = $DB->start_delegated_transaction();

        // Lock secrets immediately so subsequent scheduled task runs won't duplicate adhoc task queuing.
        $now = $this->clock->time();
        [$insql, $inparams] = $DB->get_in_or_equal($secretids, SQL_PARAMS_NAMED);
        $DB->set_field_select(
            'oauth2_server_client_secrets',
            'lastexpirynotification',
            $now,
            "id {$insql}",
            $inparams
        );

        // Queue an adhoc task per recipient user.
        foreach ($users as $user) {
            $adhoc = \core\di::make(send_oauth2_secret_expiry_notification_adhoc_task::class);
            $adhoc->set_userid($user->id);
            $adhoc->set_custom_data([
                'clientid' => $clientid,
                'clientname' => $clientinfo['clientname'],
                'secretcount' => $secretcount,
                'expirydays' => $actualdays,
            ]);
            manager::queue_adhoc_task($adhoc);
        }

        $transaction->allow_commit();
    }
}
