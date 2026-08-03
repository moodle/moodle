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
     * @param string $scopes The scopes.
     * @param string|null $description The description.
     * @param int|null $expirytime The expiry timestamp.
     * @return api_token_entity
     */
    public function create_token(
        string $name,
        string $secret,
        int $userid,
        string $scopes,
        ?string $description = null,
        ?int $expirytime = null
    ): api_token_entity {
        global $DB;

        $record = new \stdClass();
        $record->name = $name;
        $record->token = password_hash($secret, PASSWORD_DEFAULT);
        $record->userid = $userid;
        $record->scopes = $scopes;
        $record->description = $description;
        $record->expirytime = $expirytime;
        $record->revoked = api_token_entity::REVOKED_NO;
        $record->timecreated = time();

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
     * Update an existing token.
     *
     * Only the following fields can be updated via this method: name, description, scopes, expirytime.
     *
     * @param int $tokenid The token ID.
     * @param array $updates The updates.
     * @return void
     */
    public function update_token(int $tokenid, array $updates): void {
        global $DB;

        $allowedfields = ['name', 'description', 'scopes', 'expirytime'];
        $filteredupdates = array_intersect_key($updates, array_flip($allowedfields));

        if (empty($filteredupdates)) {
            return;
        }

        $token = $DB->get_record('rest_api_tokens', ['id' => $tokenid], '*', MUST_EXIST);

        foreach ($filteredupdates as $field => $value) {
            $token->{$field} = $value;
        }

        $DB->update_record('rest_api_tokens', $token);
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
        $tokenentity = $this->get_by_id($tokenid);

        if (!password_verify($secret, $tokenentity->get_token())) {
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
     * Update the last accessed timestamp for a token to the current time.
     *
     * @param int $tokenid The token ID.
     * @return void
     */
    public function log_token_access(int $tokenid): void {
        global $DB;

        $DB->set_field('rest_api_tokens', 'lastaccessed', time(), ['id' => $tokenid]);
    }

    /**
     * Retrieve API tokens belonging to a specific user, with optional lifecycle filtering.
     *
     * @param int $userid The Moodle user ID.
     * @param bool $includeinactive If true, includes expired and revoked tokens.
     * @return api_token_entity[]
     */
    public function get_user_tokens(int $userid, bool $includeinactive = false): array {
        global $DB;

        $select = "userid = :userid";
        $params = ['userid' => $userid];

        if (!$includeinactive) { // Only include active tokens.
            $select .= " AND revoked = :revoked AND (expirytime IS NULL OR expirytime > :now)";
            $params += [
                'revoked' => api_token_entity::REVOKED_NO,
                'now' => time(),
            ];
        }

        $records = $DB->get_records_select('rest_api_tokens', $select, $params, 'timecreated DESC');

        if (empty($records)) {
            return [];
        }

        return array_map(function ($record) {
            return api_token_entity::create_from_record($record);
        }, $records);
    }
}
