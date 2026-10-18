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

namespace core\oauth2\server;

use core\exception\moodle_exception;
use core\oauth2\server\entity\access_token_entity;
use core\oauth2\server\entity\auth_code_entity;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\refresh_token_entity;
use core\oauth2\server\repository\client_repository;

/**
 * Manages the lifecycle of OAuth2 server clients, their secrets and their redirect URIs.
 *
 * This is the single entry point for administering clients of the internal OAuth2 authorisation
 * server. The repository classes in {@see \core\oauth2\server\repository} serve the League OAuth2
 * server during a grant flow and are deliberately not responsible for administration.
 *
 * Resolve it through the dependency injection container rather than instantiating it directly:
 *
 *     $manager = \core\di::get(\core\oauth2\server\client_manager::class);
 *
 * All child records are keyed on the client identifier rather than on the client ID, because that
 * is how the database relationships are defined.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_manager {
    /**
     * The number of live secrets a single client may hold at any one time.
     *
     * A security constraint, not a quota: two slots is what rotation needs and no more. A client
     * runs on one secret, generates a second to migrate over to, then revokes the old one, which
     * frees the slot again. Only live secrets count, so revoked and expired ones do not.
     *
     * @var int
     */
    public const int MAX_ACTIVE_SECRETS = 2;

    /** @var int How long a generated client secret remains valid for, in seconds. */
    public const int SECRET_LIFETIME = YEARSECS;

    /**
     * Select fragment matching the refresh tokens issued to one client.
     *
     * Refresh tokens carry no client identifier of their own, so they can only be reached through
     * the access token they were issued alongside. Expects a :clientidentifier parameter.
     *
     * @var string
     */
    protected const string REFRESH_TOKENS_FOR_CLIENT = 'accesstokenidentifier IN (
                 SELECT identifier
                   FROM {oauth2_server_client_access_tokens}
                  WHERE clientidentifier = :clientidentifier)';

    /** @var string The config log name for changes to a client and its redirect URIs. */
    protected const string CONFIG_LOG_CLIENT = 'oauth2serverclient';

    /** @var string The config log name for changes to a client secret. */
    protected const string CONFIG_LOG_SECRET = 'oauth2serverclientsecret';

    /**
     * Constructor.
     *
     * @param \moodle_database $db The database connection.
     * @param \core\clock $clock The clock used for every timestamp this class writes.
     * @param client_repository $clientrepository The repository used to hydrate client entities.
     */
    public function __construct(
        /** @var \moodle_database The database connection. */
        protected readonly \moodle_database $db,
        /** @var \core\clock The clock used for every timestamp this class writes. */
        protected readonly \core\clock $clock,
        /** @var client_repository The repository used to hydrate client entities. */
        protected readonly client_repository $clientrepository,
    ) {
    }

    /**
     * Create a new, active OAuth2 client.
     *
     * @param string $name The human-readable name of the client.
     * @param \core\context $ownercontext The context which owns the client.
     * @param array $granttypes The grant types supported by the client.
     * @param array $redirecturis The redirect URIs to register, as strings. Duplicates are ignored.
     * @param string|null $description An optional human-readable description.
     * @param bool $isconfidential Whether the client can keep a secret confidential.
     * @param bool $ispkcerequired Whether PKCE is required for this client.
     * @return client_entity The client that was created.
     * @throws moodle_exception If any of the redirect URIs is not usable.
     */
    public function create_client(
        string $name,
        \core\context $ownercontext,
        array $granttypes,
        array $redirecturis = [],
        ?string $description = null,
        bool $isconfidential = true,
        bool $ispkcerequired = true,
        array $scopes = [],
    ): client_entity {
        $redirecturis = array_values(array_unique($redirecturis));

        foreach ($redirecturis as $uri) {
            $this->validate_redirect_uri_format($uri);
        }

        $granttypes = $this->validate_grant_types($granttypes, $isconfidential, $ownercontext);

        $now = $this->clock->time();
        $record = (object) [
            'name' => $name,
            'description' => $description,
            'clientidentifier' => bin2hex(random_bytes(16)),
            'ownercontext' => $ownercontext->id,
            'status' => client_entity::STATUS_ACTIVE,
            'isconfidential' => (int) $isconfidential,
            'granttypes' => implode(',', $granttypes),
            'ispkcerequired' => (int) $ispkcerequired,
            'timecreated' => $now,
            'timemodified' => $now,
            'scopes' => implode(' ', $scopes),
        ];

        $transaction = $this->db->start_delegated_transaction();
        $record->id = $this->db->insert_record('oauth2_server_clients', $record);
        $this->insert_redirect_uris($record->clientidentifier, $redirecturis);
        $this->log_change(self::CONFIG_LOG_CLIENT, null, $this->get_client_snapshot($record, $redirecturis));
        $transaction->allow_commit();

        // Everything the entity needs is already in hand, so build it rather than reading it back.
        $urirecords = array_map(fn(string $uri): \stdClass => (object) ['uri' => $uri], $redirecturis);

        return client_entity::create_from_record($record, $urirecords);
    }

    /**
     * Get a client by its client identifier.
     *
     * @param string $clientidentifier The public client identifier.
     * @return client_entity|null The client, or null if no such client exists.
     */
    public function get_client(string $clientidentifier): ?client_entity {
        return $this->clientrepository->getClientEntity($clientidentifier);
    }

    /**
     * Get a client by its database ID.
     *
     * @param int $clientid The client ID.
     * @return client_entity|null The client, or null if no such client exists.
     */
    public function get_client_by_id(int $clientid): ?client_entity {
        $record = $this->db->get_record('oauth2_server_clients', ['id' => $clientid]);

        if (!$record) {
            return null;
        }

        $urirecords = $this->db->get_records(
            'oauth2_server_client_redirect_uris',
            ['clientidentifier' => $record->clientidentifier],
        );

        return client_entity::create_from_record($record, $urirecords);
    }

    /**
     * Update the administrative metadata of a client.
     *
     * Only the name, description, PKCE requirement and scopes can be changed. Any other key in
     * $updates is ignored, so that security-relevant properties such as the client identifier or
     * the owner context can never be altered through this method.
     *
     * @param int $clientid The client ID.
     * @param array $updates The new values, keyed by field name.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function update_client(int $clientid, array $updates): void {
        $allowedfields = ['name', 'description'];
        $filteredupdates = array_intersect_key($updates, array_flip($allowedfields));

        $haspkceupdate = array_key_exists('ispkcerequired', $updates);
        $hasscopesupdate = array_key_exists('scopes', $updates);

        if (empty($filteredupdates) && !$haspkceupdate && !$hasscopesupdate) {
            return;
        }

        $client = $this->get_client_record($clientid);
        $uris = $this->get_uris($client->clientidentifier);
        $oldsnapshot = $this->get_client_snapshot($client, $uris);

        foreach ($filteredupdates as $field => $value) {
            $client->{$field} = $value;
        }

        if ($client->isconfidential && $haspkceupdate) {
            $client->ispkcerequired = (int) $updates['ispkcerequired'];
        }

        if ($hasscopesupdate) {
            $client->scopes = implode(' ', $updates['scopes']);
        }

        $client->timemodified = $this->clock->time();

        $this->db->update_record('oauth2_server_clients', $client);
        $this->log_change(self::CONFIG_LOG_CLIENT, $oldsnapshot, $this->get_client_snapshot($client, $uris));
    }

    /**
     * Disable a client, cutting off all of its existing access immediately.
     *
     * Disabling marks the client as disabled and cascades to its access tokens, its refresh tokens and its outstanding
     * authorisation codes. Access is therefore withdrawn straight away rather than merely being blocked for future
     * requests.
     *
     * Client secrets are not explicitly revoked as part of this action, but will remain invalid for use
     * as long as the client is disabled. The reason why explicit secret revocation is skipped is to allow existing
     * secrets to still be used in the case of re-enabling the client.
     *
     * @param int $clientid The client ID.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function disable_client(int $clientid): void {
        $client = $this->get_client_record($clientid);
        $params = ['clientidentifier' => $client->clientidentifier];
        $uris = $this->get_uris($client->clientidentifier);
        $oldsnapshot = $this->get_client_snapshot($client, $uris);

        $transaction = $this->db->start_delegated_transaction();

        $client->status = client_entity::STATUS_DISABLED;
        $client->timemodified = $this->clock->time();
        $this->db->update_record('oauth2_server_clients', $client);

        $this->db->set_field_select(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            refresh_token_entity::REVOKED_YES,
            self::REFRESH_TOKENS_FOR_CLIENT,
            $params,
        );

        $this->db->set_field_select(
            'oauth2_server_client_access_tokens',
            'revoked',
            access_token_entity::REVOKED_YES,
            'clientidentifier = :clientidentifier',
            $params,
        );

        $this->db->set_field_select(
            'oauth2_server_client_auth_codes',
            'revoked',
            auth_code_entity::REVOKED_YES,
            'clientidentifier = :clientidentifier',
            $params,
        );

        $this->log_change(self::CONFIG_LOG_CLIENT, $oldsnapshot, $this->get_client_snapshot($client, $uris));
        $transaction->allow_commit();
    }

    /**
     * Reactivate a disabled client.
     *
     * Only the client record itself is restored. Tokens that were revoked when the client was disabled remain revoked,
     * so the client must be authorized again before it can obtain new tokens.
     *
     * @param int $clientid The client ID.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function reactivate_client(int $clientid): void {
        $client = $this->get_client_record($clientid);
        $uris = $this->get_uris($client->clientidentifier);
        $oldsnapshot = $this->get_client_snapshot($client, $uris);

        $client->status = client_entity::STATUS_ACTIVE;
        $client->timemodified = $this->clock->time();

        $this->db->update_record('oauth2_server_clients', $client);
        $this->log_change(self::CONFIG_LOG_CLIENT, $oldsnapshot, $this->get_client_snapshot($client, $uris));
    }

    /**
     * Permanently delete a client and everything belonging to it.
     *
     * The client must already be disabled. Requiring disabling first guards against destroying a
     * live integration in a single step.
     *
     * @param int $clientid The client ID.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     * @throws moodle_exception If the client has not been disabled yet.
     */
    public function delete_client(int $clientid): void {
        $client = $this->get_client_record($clientid);

        if ((int) $client->status !== client_entity::STATUS_DISABLED) {
            throw new moodle_exception('oauth2clientnotdisabled', 'error', '', $client->clientidentifier);
        }

        $params = ['clientidentifier' => $client->clientidentifier];
        $oldsnapshot = $this->get_client_snapshot($client);

        $transaction = $this->db->start_delegated_transaction();

        // Refresh tokens reference the access tokens, so they have to go first.
        $this->db->delete_records_select(
            'oauth2_server_client_refresh_tokens',
            self::REFRESH_TOKENS_FOR_CLIENT,
            $params,
        );
        $this->db->delete_records('oauth2_server_client_access_tokens', $params);
        $this->db->delete_records('oauth2_server_client_auth_codes', $params);
        $this->db->delete_records('oauth2_server_client_granted_scopes', $params);
        $this->db->delete_records('oauth2_server_client_secrets', $params);
        $this->db->delete_records('oauth2_server_client_redirect_uris', $params);
        $this->db->delete_records('oauth2_server_clients', ['id' => $clientid]);

        $this->log_change(self::CONFIG_LOG_CLIENT, $oldsnapshot, null);
        $transaction->allow_commit();
    }

    /**
     * Generate a new secret for a client.
     *
     * The plain text secret is returned once and never stored, so the caller must pass it on to the
     * client owner immediately. Only the hash is kept.
     *
     * @param int $clientid The client ID.
     * @param int|null $expirytime When the secret expires. Defaults to self::SECRET_LIFETIME from now.
     * @return string The plain text secret.
     * @throws \dml_missing_record_exception If the client does not exist.
     * @throws moodle_exception If the client is public or already holds the maximum
     *                          number of active secrets.
     */
    public function create_secret(int $clientid, ?int $expirytime = null): string {
        global $USER;

        $client = $this->get_client_record($clientid);

        // A public client cannot keep a secret confidential, so it is never issued one.
        if (!(int) $client->isconfidential) {
            throw new moodle_exception('oauth2clientnotconfidential', 'error', '', $client->clientidentifier);
        }

        if (count($this->get_secrets_by_identifier($client->clientidentifier)) >= self::MAX_ACTIVE_SECRETS) {
            throw new moodle_exception('oauth2clientsecretlimitreached', 'error', '', self::MAX_ACTIVE_SECRETS);
        }

        $now = $this->clock->time();
        $expirytime ??= $now + self::SECRET_LIFETIME;
        $secret = bin2hex(random_bytes(32));

        $secretrecord = (object) [
            'clientidentifier' => $client->clientidentifier,
            'secret' => password_hash($secret, PASSWORD_DEFAULT),
            'expirytime' => $expirytime,
            'revoked' => client_entity::SECRET_REVOKED_NO,
            'timecreated' => $now,
        ];
        $secretrecord->id = $this->db->insert_record('oauth2_server_client_secrets', $secretrecord);
        $this->log_change(self::CONFIG_LOG_SECRET, null, $this->get_secret_snapshot($secretrecord));

        // Sent from ad hoc tasks rather than now: anything message output prints would corrupt
        // the JSON response of the route that creates secrets.
        $this->queue_secret_created_notices([
            'creatorid' => $USER->id,
            'name' => $client->name,
            'expirytime' => $expirytime,
            'url' => \core\router\util::get_path_for_callable(
                [\core_admin\route\controller\oauth2\server\client_management::class, 'manage_client_secrets'],
                ['client' => $clientid],
            )->out(false),
        ]);

        return $secret;
    }

    /**
     * Queue a notice of a new secret to each person who can manage OAuth 2 clients.
     *
     * @param array $notice The task custom data describing the secret.
     * @return void
     */
    protected function queue_secret_created_notices(array $notice): void {
        // Site admins pass every capability check without holding a role, so they are not in the
        // capability query and have to be added to it.
        $recipients = get_admins() + get_users_by_capability(
            \core\context\system::instance(),
            'moodle/site:manageoauth2clients',
            'u.id',
        );

        foreach (array_keys($recipients) as $userid) {
            $task = new \core\task\oauth2_secret_created_task();
            $task->set_custom_data($notice);
            $task->set_userid($userid);

            try {
                \core\task\manager::queue_adhoc_task($task);
            } catch (moodle_exception $e) {
                // Queueing refuses a user cron could not run the task as, such as a suspended one.
                // Only that recipient is skipped, not everyone after them.
                continue;
            }
        }
    }

    /**
     * Get the secrets belonging to a client.
     *
     * @param int $clientid The client ID.
     * @param bool $includeinactive Whether to include revoked and expired secrets.
     * @return \stdClass[] The secret records, newest first, keyed by secret ID.
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function get_secrets(int $clientid, bool $includeinactive = false): array {
        $client = $this->get_client_record($clientid);

        return $this->get_secrets_by_identifier($client->clientidentifier, $includeinactive);
    }

    /**
     * Get the secrets registered against a client identifier.
     *
     * @param string $clientidentifier The public client identifier.
     * @param bool $includeinactive Whether to include revoked and expired secrets.
     * @return \stdClass[] The secret records, newest first, keyed by secret ID.
     */
    protected function get_secrets_by_identifier(string $clientidentifier, bool $includeinactive = false): array {
        $select = 'clientidentifier = :clientidentifier';
        $params = ['clientidentifier' => $clientidentifier];

        if (!$includeinactive) {
            $select .= ' AND revoked = :revoked AND expirytime > :now';
            $params += [
                'revoked' => client_entity::SECRET_REVOKED_NO,
                'now' => $this->clock->time(),
            ];
        }

        return $this->db->get_records_select(
            'oauth2_server_client_secrets',
            $select,
            $params,
            'timecreated DESC, id DESC',
        );
    }

    /**
     * Revoke a single client secret.
     *
     * @param int $secretid The secret ID.
     * @return void
     */
    public function revoke_secret(int $secretid): void {
        $secretrecord = $this->db->get_record('oauth2_server_client_secrets', ['id' => $secretid]);

        // Revoking a secret which does not exist does nothing.
        if (!$secretrecord) {
            return;
        }

        $oldsnapshot = $this->get_secret_snapshot($secretrecord);
        $secretrecord->revoked = client_entity::SECRET_REVOKED_YES;

        $this->db->set_field(
            'oauth2_server_client_secrets',
            'revoked',
            $secretrecord->revoked,
            ['id' => $secretid],
        );
        $this->log_change(self::CONFIG_LOG_SECRET, $oldsnapshot, $this->get_secret_snapshot($secretrecord));
    }

    /**
     * Get the redirect URIs registered for a client.
     *
     * @param int $clientid The client ID.
     * @return string[] The redirect URIs, keyed by redirect URI ID.
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function get_redirect_uris(int $clientid): array {
        $client = $this->get_client_record($clientid);

        return $this->get_uris($client->clientidentifier);
    }

    /**
     * Register a redirect URI for a client.
     *
     * Registering a URI which is already registered does nothing. There is no limit on how many
     * redirect URIs a client may have.
     *
     * @param int $clientid The client ID.
     * @param string $uri The redirect URI to register.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     * @throws moodle_exception If the URI is not usable as a redirect URI.
     */
    public function add_redirect_uri(int $clientid, string $uri): void {
        $client = $this->get_client_record($clientid);

        $this->validate_redirect_uri_format($uri);

        if ($this->validate_redirect_uri($client->clientidentifier, $uri)) {
            return;
        }

        $oldsnapshot = $this->get_client_snapshot($client);
        $this->insert_redirect_uris($client->clientidentifier, [$uri]);
        $this->log_change(self::CONFIG_LOG_CLIENT, $oldsnapshot, $this->get_client_snapshot($client));
    }

    /**
     * Remove a redirect URI from a client.
     *
     * Removing a URI which is not registered does nothing.
     *
     * @param int $clientid The client ID.
     * @param string $uri The redirect URI to remove.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function remove_redirect_uri(int $clientid, string $uri): void {
        $client = $this->get_client_record($clientid);

        $matches = array_keys($this->get_uris($client->clientidentifier), $uri, true);

        if (empty($matches)) {
            return;
        }

        $oldsnapshot = $this->get_client_snapshot($client);
        [$insql, $params] = $this->db->get_in_or_equal($matches, SQL_PARAMS_NAMED);
        $this->db->delete_records_select('oauth2_server_client_redirect_uris', "id {$insql}", $params);
        $this->log_change(self::CONFIG_LOG_CLIENT, $oldsnapshot, $this->get_client_snapshot($client));
    }

    /**
     * Check whether a redirect URI is registered for a client.
     *
     * The comparison is exact: neither prefix nor wildcard matching is permitted, because a looser
     * comparison would let an attacker redirect an authorisation code to a URI the client owner
     * never registered.
     *
     * @param string $clientidentifier The public client identifier.
     * @param string $uri The redirect URI to check.
     * @return bool True if the URI is registered for this client.
     */
    public function validate_redirect_uri(string $clientidentifier, string $uri): bool {
        return in_array($uri, $this->get_uris($clientidentifier), true);
    }

    /**
     * Get a client record by ID.
     *
     * @param int $clientid The client ID.
     * @return \stdClass The client record.
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    protected function get_client_record(int $clientid): \stdClass {
        return $this->db->get_record('oauth2_server_clients', ['id' => $clientid], '*', MUST_EXIST);
    }

    /**
     * Describe a client and its redirect URIs for the config log.
     *
     * @param \stdClass $client The client record.
     * @param string[]|null $uris The client's redirect URIs, if already known. Read from the database if null.
     * @return string The client state, as JSON.
     */
    protected function get_client_snapshot(\stdClass $client, ?array $uris = null): string {
        return json_encode([
            'clientidentifier' => $client->clientidentifier,
            'name' => $client->name,
            'description' => $client->description,
            'status' => (int) $client->status,
            'isconfidential' => (int) $client->isconfidential,
            'ispkcerequired' => (int) $client->ispkcerequired,
            'granttypes' => $client->granttypes,
            'scopes' => $client->scopes,
            'redirecturis' => array_values($uris ?? $this->get_uris($client->clientidentifier)),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Describe a client secret for the config log, leaving out the secret hash.
     *
     * @param \stdClass $secret The secret record.
     * @return string The secret state, as JSON.
     */
    protected function get_secret_snapshot(\stdClass $secret): string {
        return json_encode([
            'clientidentifier' => $secret->clientidentifier,
            'secretid' => (int) $secret->id,
            'expirytime' => (int) $secret->expirytime,
            'revoked' => (int) $secret->revoked,
        ]);
    }

    /**
     * Record a change in the config log, unless nothing actually changed.
     *
     * @param string $name The config log name.
     * @param string|null $oldvalue The state before the change, or null if it was created.
     * @param string|null $newvalue The state after the change, or null if it was deleted.
     * @return void
     */
    protected function log_change(string $name, ?string $oldvalue, ?string $newvalue): void {
        if ($oldvalue !== $newvalue) {
            add_to_config_log($name, $oldvalue, $newvalue, 'core');
        }
    }

    /**
     * Get the redirect URIs registered against a client identifier.
     *
     * @param string $clientidentifier The public client identifier.
     * @return string[] The redirect URIs, keyed by redirect URI ID.
     */
    protected function get_uris(string $clientidentifier): array {
        $records = $this->db->get_records(
            'oauth2_server_client_redirect_uris',
            ['clientidentifier' => $clientidentifier],
            'id ASC',
            'id, uri',
        );

        return array_map(fn(\stdClass $record): string => $record->uri, $records);
    }

    /**
     * Insert redirect URI records for a client.
     *
     * @param string $clientidentifier The public client identifier.
     * @param string[] $uris The redirect URIs to insert.
     * @return void
     */
    protected function insert_redirect_uris(string $clientidentifier, array $uris): void {
        if (empty($uris)) {
            return;
        }

        $records = array_map(fn(string $uri): array => [
            'clientidentifier' => $clientidentifier,
            'uri' => $uri,
        ], $uris);

        $this->db->insert_records('oauth2_server_client_redirect_uris', $records);
    }

    /**
     * Check that a URI can be used as a redirect URI, throwing if it cannot.
     *
     * @param string $uri The redirect URI to check.
     * @return void
     * @throws moodle_exception If the URI is not usable as a redirect URI.
     */
    public function validate_redirect_uri_format(string $uri): void {
        $parts = parse_url($uri);

        // A redirect URI must be absolute and must not carry a fragment. See RFC 6749, section 3.1.2.
        $isabsolute = is_array($parts)
            && !empty($parts['scheme'])
            && !empty($parts['host'])
            && !isset($parts['fragment']);

        if (!$isabsolute) {
            throw new moodle_exception('oauth2clientinvalidredirecturi', 'error', '', $uri);
        }

        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);

        // HTTPS is required, except on the loopback interface, which native apps rely on during
        // development and cannot serve over HTTPS. See RFC 8252, section 7.3.
        $islocal = $this->is_loopback_host($host);

        if ($scheme !== 'https' && !($scheme === 'http' && $islocal)) {
            throw new moodle_exception('oauth2clientinvalidredirecturi', 'error', '', $uri);
        }
    }

    /**
     * Validate that the requested grant types are allowed based on the client settings.
     *
     * @param array $granttypes The list of requested grant types.
     * @param bool $isconfidential Whether the client is confidential or public.
     * @param \core\context $ownercontext The context owning this client.
     * @return array The sanitized and normalized list of grant types.
     * @throws \coding_exception If any validation rule is violated.
     */
    private function validate_grant_types(array $granttypes, bool $isconfidential, \core\context $ownercontext): array {
        // Clean up the array (remove duplicates and empty values).
        $granttypes = array_values(array_unique(array_filter($granttypes)));

        // Define all valid grant types allowed.
        $validgrants = [
            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
            client_entity::GRANT_TYPE_CLIENT_CREDENTIALS,
            client_entity::GRANT_TYPE_REFRESH_TOKEN,
        ];

        foreach ($granttypes as $grant) {
            if (!in_array($grant, $validgrants, true)) {
                throw new \coding_exception("Unsupported grant type specified: {$grant}");
            }
        }

        // Public clients cannot use Client Credential flows.
        if (!$isconfidential && in_array(client_entity::GRANT_TYPE_CLIENT_CREDENTIALS, $granttypes, true)) {
            throw new \coding_exception('Public clients cannot support the client_credentials grant type.');
        }

        // Client Credentials grant is restricted strictly to the system context.
        if (in_array(client_entity::GRANT_TYPE_CLIENT_CREDENTIALS, $granttypes, true)) {
            if ($ownercontext->contextlevel !== CONTEXT_SYSTEM) {
                throw new \coding_exception('The client_credentials grant type is only allowed for system-owned clients.');
            }
        }

        $isauthorizationcodesupported = in_array(client_entity::GRANT_TYPE_AUTHORIZATION_CODE, $granttypes, true);
        $isrefreshtokensupported = in_array(client_entity::GRANT_TYPE_REFRESH_TOKEN, $granttypes, true);

        // Authorization code and Refresh tokens grants must be supported together.
        if ($isauthorizationcodesupported !== $isrefreshtokensupported) {
            throw new \coding_exception('The authorization_code and refresh_token grants must be supported together.');
        }

        return $granttypes;
    }

    /**
     * Check whether a host refers to the loopback interface.
     *
     * @param string $host The host portion of a URI, which may be a bracketed IPv6 literal.
     * @return bool True if the host is a loopback host.
     */
    protected function is_loopback_host(string $host): bool {
        $host = strtolower(trim($host, '[]'));

        if ($host === 'localhost' || $host === '::1') {
            return true;
        }

        // The whole of 127.0.0.0/8 is reserved for loopback, not just 127.0.0.1.
        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            && str_starts_with($host, '127.');
    }
}
