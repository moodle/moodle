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
     * @param bool $ispkceenabled Whether PKCE is enabled for this client.
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
        bool $ispkceenabled = true,
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
            'ispkceenabled' => (int) $ispkceenabled,
            'timecreated' => $now,
            'timemodified' => $now,
        ];

        $transaction = $this->db->start_delegated_transaction();
        $record->id = $this->db->insert_record('oauth2_server_clients', $record);
        $this->insert_redirect_uris($record->clientidentifier, $redirecturis);
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
     * Only the name and the description can be changed. Any other key in $updates is ignored, so
     * that security-relevant properties such as the client identifier or the owner context can
     * never be altered through this method.
     *
     * @param int $clientid The client ID.
     * @param array $updates The new values, keyed by field name.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function update_client(int $clientid, array $updates): void {
        $allowedfields = ['name', 'description'];
        $filteredupdates = array_intersect_key($updates, array_flip($allowedfields));

        if (empty($filteredupdates)) {
            return;
        }

        $client = $this->get_client_record($clientid);

        foreach ($filteredupdates as $field => $value) {
            $client->{$field} = $value;
        }
        $client->timemodified = $this->clock->time();

        $this->db->update_record('oauth2_server_clients', $client);
    }

    /**
     * Revoke a client, cutting off all of its existing access immediately.
     *
     * Revoking marks the client as revoked and cascades to every credential it holds: its secrets,
     * its access tokens, its refresh tokens and its outstanding authorisation codes. Access is
     * therefore withdrawn straight away rather than merely being blocked for future requests.
     *
     * @param int $clientid The client ID.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function revoke_client(int $clientid): void {
        $client = $this->get_client_record($clientid);
        $params = ['clientidentifier' => $client->clientidentifier];

        $transaction = $this->db->start_delegated_transaction();

        $client->status = client_entity::STATUS_REVOKED;
        $client->timemodified = $this->clock->time();
        $this->db->update_record('oauth2_server_clients', $client);

        $this->db->set_field_select(
            'oauth2_server_client_secrets',
            'revoked',
            client_entity::SECRET_REVOKED_YES,
            'clientidentifier = :clientidentifier',
            $params,
        );

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

        $transaction->allow_commit();
    }

    /**
     * Reactivate a revoked client.
     *
     * Only the client record itself is restored. Secrets and tokens revoked when the client was
     * revoked stay revoked, so the client must be issued a new secret and must be authorised again
     * before it can obtain new tokens.
     *
     * @param int $clientid The client ID.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     */
    public function reactivate_client(int $clientid): void {
        $client = $this->get_client_record($clientid);

        $client->status = client_entity::STATUS_ACTIVE;
        $client->timemodified = $this->clock->time();

        $this->db->update_record('oauth2_server_clients', $client);
    }

    /**
     * Permanently delete a client and everything belonging to it.
     *
     * The client must already be revoked. Requiring revocation first guards against destroying a
     * live integration in a single step.
     *
     * @param int $clientid The client ID.
     * @return void
     * @throws \dml_missing_record_exception If the client does not exist.
     * @throws moodle_exception If the client has not been revoked yet.
     */
    public function delete_client(int $clientid): void {
        $client = $this->get_client_record($clientid);

        if ((int) $client->status !== client_entity::STATUS_REVOKED) {
            throw new moodle_exception('oauth2clientnotrevoked', 'error', '', $client->clientidentifier);
        }

        $params = ['clientidentifier' => $client->clientidentifier];

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
     * @throws moodle_exception If the client is public or revoked, or already holds the maximum
     *      number of active secrets.
     */
    public function create_secret(int $clientid, ?int $expirytime = null): string {
        $client = $this->get_client_record($clientid);

        if ((int) $client->status !== client_entity::STATUS_ACTIVE) {
            throw new moodle_exception('oauth2clientrevoked', 'error', '', $client->clientidentifier);
        }

        // A public client cannot keep a secret confidential, so it is never issued one.
        if (!(int) $client->isconfidential) {
            throw new moodle_exception('oauth2clientnotconfidential', 'error', '', $client->clientidentifier);
        }

        if (count($this->get_secrets_by_identifier($client->clientidentifier)) >= self::MAX_ACTIVE_SECRETS) {
            throw new moodle_exception('oauth2clientsecretlimitreached', 'error', '', self::MAX_ACTIVE_SECRETS);
        }

        $now = $this->clock->time();
        $secret = bin2hex(random_bytes(32));

        $this->db->insert_record('oauth2_server_client_secrets', (object) [
            'clientidentifier' => $client->clientidentifier,
            'secret' => password_hash($secret, PASSWORD_DEFAULT),
            'expirytime' => $expirytime ?? $now + self::SECRET_LIFETIME,
            'revoked' => client_entity::SECRET_REVOKED_NO,
            'timecreated' => $now,
        ]);

        return $secret;
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
        $this->db->set_field(
            'oauth2_server_client_secrets',
            'revoked',
            client_entity::SECRET_REVOKED_YES,
            ['id' => $secretid],
        );
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

        $this->insert_redirect_uris($client->clientidentifier, [$uri]);
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

        [$insql, $params] = $this->db->get_in_or_equal($matches, SQL_PARAMS_NAMED);
        $this->db->delete_records_select('oauth2_server_client_redirect_uris', "id {$insql}", $params);
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
    protected function validate_redirect_uri_format(string $uri): void {
        $parts = parse_url($uri);

        // A redirect URI must be absolute and must not carry a fragment. See RFC 6749, section 3.1.2.
        $isabsolute = is_array($parts)
            && !empty($parts['scheme'])
            && !empty($parts['host'])
            && !isset($parts['fragment']);

        // HTTPS is required, except on the loopback interface, which native apps rely on during
        // development and cannot serve over HTTPS. See RFC 8252, section 7.3.
        $scheme = $isabsolute ? strtolower($parts['scheme']) : '';
        $isallowedscheme = $scheme === 'https'
            || ($scheme === 'http' && $this->is_loopback_host($parts['host']));

        if (!$isabsolute || !$isallowedscheme) {
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
        $validgrants = ['authorization_code', 'client_credentials', 'refresh_token'];

        foreach ($granttypes as $grant) {
            if (!in_array($grant, $validgrants, true)) {
                throw new \coding_exception("Unsupported grant type specified: {$grant}");
            }
        }

        // Public clients cannot use Client Credential flows.
        if (!$isconfidential && in_array('client_credentials', $granttypes, true)) {
            throw new \coding_exception('Public clients cannot support the client_credentials grant type.');
        }

        // Client Credentials grant is restricted strictly to the system context.
        if (in_array('client_credentials', $granttypes, true)) {
            if ($ownercontext->contextlevel !== CONTEXT_SYSTEM) {
                throw new \coding_exception('The client_credentials grant type is only allowed for system-owned clients.');
            }
        }

        // Authorization code and Refresh tokens grants must be supported together.
        if (in_array('authorization_code', $granttypes, true) !== in_array('refresh_token', $granttypes, true)) {
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
