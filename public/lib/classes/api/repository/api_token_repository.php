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

namespace core\api\repository;

use core\api\entity\api_token_entity;
use core\api\token_manager;
use core\clock;
use core\di;

/**
 * Repository for REST API tokens.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_token_repository {
    /**
     * Create a new REST API token.
     *
     * @param string $name The human-readable name.
     * @param string $secret The raw secret.
     * @param int $userid The user ID.
     * @param string[] $scopes The scopes.
     * @param string|null $description The description.
     * @param int|null $expirytime The expiry timestamp.
     * @return api_token_entity
     */
    public function create_token(
        string $name,
        #[\SensitiveParameter]
        string $secret,
        int $userid,
        array $scopes,
        ?string $description = null,
        ?int $expirytime = null
    ): api_token_entity {
        global $DB;

        $record = new \stdClass();
        $record->name = $name;

        // Ensure that scopes are sorted for consistency.
        sort($scopes);

        // When we hash the secret we include a checksum of the granted scopes and expiry to ensure that these
        // are not changed in the database.
        $secret .= $this->calculate_token_checksum($userid, $scopes, $expirytime);

        $record->token = password_hash($this->prehash_secret($secret), PASSWORD_DEFAULT);
        $record->userid = $userid;
        $record->scopes = implode(' ', $scopes);
        $record->description = $description;
        $record->expirytime = $expirytime;
        $record->revoked = api_token_entity::REVOKED_NO;
        $record->timecreated = di::get(clock::class)->time();

        $record->id = $DB->insert_record('rest_api_tokens', $record);

        return api_token_entity::create_from_record($record);
    }

    /**
     * Get a token entity by its ID.
     *
     * @param int $id The token ID.
     * @return api_token_entity
     * @throws \dml_missing_record_exception If the token does not exist.
     */
    public function get_by_id(int $id): api_token_entity {
        global $DB;

        $record = $DB->get_record('rest_api_tokens', ['id' => $id], '*', MUST_EXIST);

        return api_token_entity::create_from_record($record);
    }

    /**
     * Get a token entity from a provided token.
     *
     * @param string $token
     * @return api_token_entity
     * @throws \core\exception\invalid_api_token_exception If the token is invalid.
     * @throws \core\exception\expired_api_token_exception If the token has expired.
     * @throws \core\exception\revoked_api_token_exception If the token has been revoked.
     */
    public function get_from_token(
        #[\SensitiveParameter]
        string $token,
    ): api_token_entity {
        if (!str_starts_with($token, token_manager::TOKEN_PREFIX)) {
            throw new \core\exception\invalid_api_token_exception();
        }

        // Tokens are a base64 encoded string of "tokenid/secret" prefixed with the token manager's prefix.
        // The secret is hashed in the database using `password_hash` so is not reversible.
        // The base64 encoding makes it URL safe and allows us to include the token ID and secret for verification.
        $tokendata = base64_decode(substr($token, strlen(token_manager::TOKEN_PREFIX)));

        if (!str_contains($tokendata, '/')) {
            // After base64 decoding, the token should contain a '/' separating the token ID and secret.
            throw new \core\exception\invalid_api_token_exception();
        }

        [$tokenid, $secret] = explode('/', $tokendata, 2);
        if (!is_numeric($tokenid)) {
            // The TokenID should be an integer.
            throw new \core\exception\invalid_api_token_exception();
        }
        $tokenid = (int) $tokenid;

        // Validate the token and secret, throwing exceptions if invalid, expired, or revoked.
        return $this->validate_token($tokenid, $secret);
    }

    /**
     * Revoke a token.
     *
     * @param int $tokenid The token ID.
     * @return void
     */
    public function revoke_token(int $tokenid): void {
        global $DB;

        $DB->set_field(
            'rest_api_tokens',
            'revoked',
            api_token_entity::REVOKED_YES,
            ['id' => $tokenid]
        );
    }

    /**
     * Delete a token.
     *
     * @param int $tokenid The token ID.
     * @return void
     */
    public function delete_token(int $tokenid): void {
        global $DB;

        $DB->delete_records('rest_api_tokens', ['id' => $tokenid]);
    }

    /**
     * A pre-computed bcrypt hash, used only to burn an equivalent amount of CPU time to a real
     * password_verify() call when no matching token row exists to check against.
     *
     * @var string
     */
    protected const string DUMMY_HASH = '$2y$10$VeFTuEKh7qoWSrbPxWsnVeyLNgcZCrakBQAvqKQcVbaro8Jj2smvu';

    /**
     * Validate a token.
     *
     * @param int $tokenid The token ID.
     * @param string $secret The raw secret.
     * @return api_token_entity
     * @throws \core\exception\invalid_api_token_exception If the token is invalid.
     * @throws \core\exception\expired_api_token_exception If the token has expired.
     * @throws \core\exception\revoked_api_token_exception If the token has been revoked.
     */
    public function validate_token(int $tokenid, string $secret): api_token_entity {
        try {
            $tokenentity = $this->get_by_id($tokenid);
        } catch (\dml_missing_record_exception $e) {
            // Without this, a request for a token ID that does not exist returns immediately with a
            // different exception type to one for an ID that exists but has the wrong secret (which
            // only fails after the cost of a bcrypt verify). Both the timing difference and the
            // distinct exception would let an attacker enumerate valid token IDs by probing every
            // integer. Performing a dummy verify here keeps both the timing and the exception thrown
            // the same in either case.
            password_verify($this->prehash_secret($secret), self::DUMMY_HASH);
            throw new \core\exception\invalid_api_token_exception();
        }

        $userid = $tokenentity->get_userid();
        $checksum = $this->calculate_token_checksum(
            $userid,
            $tokenentity->get_scopes(),
            $tokenentity->get_expirytime(),
        );

        if (!password_verify($this->prehash_secret($secret . $checksum), $tokenentity->get_token())) {
            throw new \core\exception\invalid_api_token_exception();
        }

        if ($tokenentity->has_expired()) {
            throw new \core\exception\expired_api_token_exception();
        }

        if ($tokenentity->is_revoked()) {
            throw new \core\exception\revoked_api_token_exception();
        }

        return $tokenentity;
    }

    /**
     * Record that a token was just used, and where from.
     *
     * @param int $tokenid The token ID.
     * @return void
     */
    public function log_token_access(int $tokenid): void {
        global $DB;

        // The address is stored alongside the time because "last used yesterday" on its own does
        // not tell the owner whether it was them.
        $DB->update_record('rest_api_tokens', (object) [
            'id' => $tokenid,
            'lastaccessed' => di::get(clock::class)->time(),
            'lastaccessip' => getremoteaddr(null),
        ]);
    }

    /**
     * Retrieve API tokens belonging to a specific user, with optional lifecycle filtering.
     *
     * @param int $userid The Moodle user ID.
     * @param bool $includeinactive If true, includes expired and revoked tokens.
     * @return api_token_entity[]
     */
    public function get_user_tokens(int $userid, bool $includeinactive = false): array {
        $select = "userid = :userid";
        $params = ['userid' => $userid];

        if (!$includeinactive) { // Only include active tokens.
            $select .= " AND revoked = :revoked AND (expirytime IS NULL OR expirytime > :now)";
            $params += [
                'revoked' => api_token_entity::REVOKED_NO,
                'now' => di::get(clock::class)->time(),
            ];
        }

        return $this->get_tokens_select($select, $params, sort: 'timecreated DESC');
    }

    /**
     * Get the tokens whose owners are due a reminder that they are about to expire.
     *
     * @param int|null $userid Only this owner's tokens, or everyone's if null.
     * @return api_token_entity[]
     */
    public function get_tokens_to_warn(?int $userid = null): array {
        $now = di::get(clock::class)->time();
        $select = "revoked = :revoked AND expirytime > :now AND expirytime <= :horizon AND timeexpirednotified IS NULL";
        $params = [
            'revoked' => api_token_entity::REVOKED_NO,
            'now' => $now,
            'horizon' => $now + (max(token_manager::REMINDER_DAYS) * DAYSECS),
        ];

        return array_filter(
            $this->get_tokens_select($select, $params, $userid),
            fn(api_token_entity $token) => token_manager::is_reminder_due(
                $token->get_timecreated(),
                $token->get_expirytime(),
                $token->get_timeexpirywarned(),
            ),
        );
    }

    /**
     * Get the recently expired tokens whose owners have not been told.
     *
     * @param int|null $userid Only this owner's tokens, or everyone's if null.
     * @return api_token_entity[]
     */
    public function get_expired_tokens_to_notify(?int $userid = null): array {
        $now = di::get(clock::class)->time();
        $select = "revoked = :revoked AND expirytime <= :now AND expirytime > :cutoff AND timeexpirednotified IS NULL";
        $params = [
            'revoked' => api_token_entity::REVOKED_NO,
            'now' => $now,
            'cutoff' => $now - (token_manager::EXPIRED_NOTICE_DAYS * DAYSECS),
        ];

        return $this->get_tokens_select($select, $params, $userid);
    }

    /**
     * Record that a token's owner has just been reminded it is about to expire.
     *
     * @param int $tokenid The token ID.
     * @return void
     */
    public function mark_expiry_warned(int $tokenid): void {
        global $DB;

        $DB->set_field('rest_api_tokens', 'timeexpirywarned', di::get(clock::class)->time(), ['id' => $tokenid]);
    }

    /**
     * Record that a token's owner has been told it has expired.
     *
     * @param int $tokenid The token ID.
     * @return void
     */
    public function mark_expired_notified(int $tokenid): void {
        global $DB;

        $DB->set_field('rest_api_tokens', 'timeexpirednotified', di::get(clock::class)->time(), ['id' => $tokenid]);
    }

    /**
     * Get the tokens matching a WHERE clause, optionally for one owner only.
     *
     * @param string $select The WHERE clause.
     * @param array $params The clause's parameters.
     * @param int|null $userid Only this owner's tokens, or everyone's if null.
     * @param string $sort The ORDER BY clause.
     * @return api_token_entity[]
     */
    protected function get_tokens_select(string $select, array $params, ?int $userid = null, string $sort = 'id'): array {
        global $DB;

        if ($userid !== null) {
            $select .= " AND userid = :ownerid";
            $params['ownerid'] = $userid;
        }

        return array_map(
            fn($record) => api_token_entity::create_from_record($record),
            $DB->get_records_select('rest_api_tokens', $select, $params, $sort),
        );
    }

    /**
     * Pre-hash the secret and its appended checksum.
     *
     * The default password_hash() algorithm is bcrypt, which silently truncates its input to 72 bytes. Our
     * secret is already 64 bytes (see {@see token_manager::SECRET_LENGTH}), leaving very little room for the
     * scopes/expirytime checksum appended in {@see self::create_token()} and {@see self::validate_token()}.
     * Without pre-hashing, two tokens whose secret + checksum share the same first ~72 bytes would produce
     * the same bcrypt hash, defeating the checksum's purpose of detecting tampering with stored scopes or
     * expiry time.
     *
     * Hashing first collapses the input to a fixed-length 64-character SHA-256 digest, well under the bcrypt
     * limit, regardless of how many scopes are granted or how long their names are.
     *
     * @param string $secret The raw secret, with the checksum already appended.
     * @return string The pre-hashed digest to pass to password_hash()/password_verify().
     */
    protected function prehash_secret(
        #[\SensitiveParameter]
        string $secret,
    ): string {
        return hash('sha256', $secret);
    }

    /**
     * Calculate the checksum used as an addendum to the token secret.
     *
     * Note: We do not hash the checksum in any way. It is used as-is appended to the token secret.
     * If we were to shasum it then we would open it to collision attacks.
     *
     * @param array $scopes
     * @param int|null $expirytime
     * @return string
     */
    protected function calculate_token_checksum(
        int $userid,
        array $scopes,
        ?int $expirytime,
    ): string {
        sort($scopes);
        return $userid . '|' . implode(' ', $scopes) . '|' . ($expirytime ?? '');
    }
}
