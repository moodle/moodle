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

use core\clock;
use core\composer;
use core\oauth2\server\entity\client_entity;
use dml_exception;

/**
 * Scheduled task to find all OAuth 2 client secrets that have naturally expired and process them accordingly.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_expired_oauth2_secrets_task extends scheduled_task {
    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('oauth2server_processexpiredsecretstask', 'admin');
    }

    /**
     * Constructor.
     *
     * @param clock $clock The clock.
     * @param composer $composer The composer utility.
     */
    public function __construct(
        /** @var clock The clock. */
        protected readonly clock $clock,
        /** @var composer The composer utility. */
        protected readonly composer $composer,
    ) {
    }

    /**
     * Execute the task.
     *
     * @return void
     * @throws dml_exception
     */
    public function execute(): void {
        global $DB;

        // Ensure that the 'league/oauth2-server' composer package is installed.
        // If it's not installed, OAuth2 authentication and client/secret management is not available, so there's no
        // need to execute this task further.
        $package = $this->composer->get_package_status('league/oauth2-server');

        if (!$package->installed) {
            return;
        }

        $now = $this->clock->time();

        // Fetch active secrets belonging to an active client that naturally expired and are pending processing.
        $sql = "SELECT s.id,
                       s.expirytime,
                       c.id AS clientid,
                       c.name AS clientname
                  FROM {oauth2_server_client_secrets} s
                  JOIN {oauth2_server_clients} c ON c.clientidentifier = s.clientidentifier
                 WHERE s.revoked = :secretrevoked
                   AND c.status = :clientstatus
                   AND s.expirytime <= :now
                   AND s.expiryprocessed = :secretexpiryprocessed";

        $params = [
            'secretrevoked' => client_entity::SECRET_REVOKED_NO,
            'clientstatus' => client_entity::STATUS_ACTIVE,
            'now' => $now,
            'secretexpiryprocessed' => client_entity::SECRET_EXPIRY_NOT_PROCESSED,
        ];

        $expiredsecrets = $DB->get_records_sql($sql, $params);

        // Early return if there are no unprocessed expired secrets.
        if (empty($expiredsecrets)) {
            return;
        }

        // Fetch all users that are capable to manage OAuth 2 clients and secrets. These users are eligible recipients of
        // notifications about expired secrets.
        $recipients = $this->get_client_secret_managers();

        foreach ($expiredsecrets as $secret) {
            // Wrap operations in a delegated transaction for atomic safety.
            $transaction = $DB->start_delegated_transaction();

            // Mark the expired secret as processed to avoid repeated evaluation by the cron.
            $DB->set_field(
                'oauth2_server_client_secrets',
                'expiryprocessed',
                client_entity::SECRET_EXPIRY_PROCESSED,
                ['id' => $secret->id],
            );

            $this->queue_secret_expired_notifications(
                $recipients,
                $secret->clientid,
                $secret->clientname,
                $secret->expirytime,
            );

            $transaction->allow_commit();
        }
    }

    /**
     * Queue adhoc tasks for all eligible recipient users to receive a notification for the expired secret.
     *
     * @param array $recipients List of eligible recipient users.
     * @param int $clientid The ID of the client associated to the expired secret.
     * @param string $clientname The name of the client associated to the expired secret.
     * @param int $secretexpiredtime The timestamp of when the secret expired.
     * @return void
     */
    private function queue_secret_expired_notifications(
        array $recipients,
        int $clientid,
        string $clientname,
        int $secretexpiredtime,
    ): void {
        // Early return if there are no eligible recipient users to notify.
        if (empty($recipients)) {
            return;
        }

        // Queue an adhoc task per recipient user.
        foreach ($recipients as $user) {
            $adhoctask = new \core\task\send_oauth2_secret_expired_notification_adhoc_task();
            $adhoctask->set_custom_data([
                'clientid' => $clientid,
                'clientname' => $clientname,
                'secretexpiredtime' => $secretexpiredtime,
            ]);
            // Ensures that the notification is sent in the recipient user's localized context.
            $adhoctask->set_userid($user->id);

            manager::queue_adhoc_task($adhoctask);
        }
    }

    /**
     * Return users with explicit capability to manage OAuth 2 clients and secrets.
     *
     * @return array List of users.
     */
    private function get_client_secret_managers(): array {
        $users = get_users_by_capability(
            \context_system::instance(),
            'moodle/site:manageoauth2clients',
            'u.*',
        );

        // Primary site administrators ($CFG->siteadmins) do not always have explicit system role assignments,
        // which causes get_users_by_capability() to miss them.
        $users += get_admins();

        // Suspended users cannot receive notifications, so drop them.
        return array_filter($users, fn($user) => empty($user->suspended));
    }
}
