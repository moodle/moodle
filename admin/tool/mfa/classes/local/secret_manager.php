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

namespace tool_mfa\local;

/**
 * MFA secret management class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secret_manager {

    /** @var string */
    const REVOKED = 'revoked';

    /** @var string */
    const VALID = 'valid';

    /** @var string */
    const NONVALID = 'nonvalid';

    /** @var string */
    private $factor;

    /** @var string|false */
    private $sessionid;

    /**
     * Initialises a secret manager instance
     *
     * @param   string $factor
     */
    public function __construct(string $factor) {
        $this->factor = $factor;
        $this->sessionid = session_id();
    }

    /**
     * This function creates or takes a secret, and stores it in the database or session.
     *
     * @param int $expires the length of time the secret is valid. e.g. 1 min = 60
     * @param bool $session whether this secret should be linked to the session.
     * @param string $secret an optional provided secret
     * @return string the secret code, or 0 if no new code created.
     */
    public function create_secret(int $expires, bool $session, ?string $secret = null): string {
        // Check if there already an active secret, unless we are forcibly given a code.
        if ($this->has_active_secret($session) && empty($secret)) {
            return '';
        }

        // Setup a secret if not provided.
        if (empty($secret)) {
            $secret = random_int(100000, 999999);
        }

        // Now pass the code where it needs to go.
        if ($session) {
            $this->add_secret_to_db($secret, $expires, $this->sessionid);
        } else {
            $this->add_secret_to_db($secret, $expires);
        }

        return $secret;
    }

    /**
     * Inserts the provided secret into the database with a given expiry duration.
     *
     * @param string $secret the secret to store
     * @param int $expires expiry duration in seconds
     * @param string $sessionid an optional sessionID to tie this record to
     * @return void
     */
    private function add_secret_to_db(string $secret, int $expires, ?string $sessionid = null): void {
        global $DB, $USER;
        $expirytime = time() + $expires;

        $data = [
            'userid' => $USER->id,
            'factor' => $this->factor,
            'secret' => $secret,
            'timecreated' => time(),
            'expiry' => $expirytime,
            'revoked' => 0,
        ];
        if (!empty($sessionid)) {
            $data['sessionid'] = $sessionid;
        }
        $DB->insert_record('tool_mfa_secrets', $data);
    }

    /**
     * Validates whether the provided secret is currently valid.
     *
     * @param string $secret the secret to check
     * @param bool $keep should the secret be kept for reuse until expiry?
     * @return string a secret manager state constant
     */
    public function validate_secret(string $secret, bool $keep = false): string {
        global $DB, $USER;
        $status = $this->check_secret_against_db($secret, $this->sessionid);
        if ($status !== self::NONVALID) {
            if ($status === self::VALID && !$keep) {
                // Cleanup DB $record.
                $DB->delete_records('tool_mfa_secrets', ['userid' => $USER->id, 'factor' => $this->factor]);
            }
            return $status;
        }
        // This is always nonvalid.
        return $status;
    }

    /**
     * Checks if a given secret is valid from the Database.
     *
     * @param string $secret the secret to check.
     * @param string $sessionid the session id to check for.
     * @return string a secret manager state constant.
     */
    private function check_secret_against_db(string $secret, string $sessionid): string {
        global $DB, $USER;

        $sql = "SELECT *
                  FROM {tool_mfa_secrets}
                 WHERE secret = :secret
                   AND expiry > :now
                   AND userid = :userid
                   AND factor = :factor";

        $params = [
            'secret' => $secret,
            'now' => time(),
            'userid' => $USER->id,
            'factor' => $this->factor,
        ];

        $record = $DB->get_record_sql($sql, $params);

        if (!empty($record)) {
            // If revoked it should always be revoked status.
            if ($record->revoked) {
                return self::REVOKED;
            }

            // Check if this is valid in only one session.
            if (!empty($record->sessionid)) {
                if ($record->sessionid === $sessionid) {
                    return self::VALID;
                }
                return self::NONVALID;
            }
            return self::VALID;
        }
        return self::NONVALID;
    }

    /**
     * Revokes the provided secret code for the user.
     *
     * @param string $secret the secret to revoke.
     * @param int $userid the userid to revoke the secret for.
     * @return void
     */
    public function revoke_secret(string $secret, $userid = null): void {
        global $DB, $USER;

        $userid = $userid ?? $USER->id;

        // We do not need to worry about session vs global here.
        // A factor should only ever use one.
        // We know this secret is valid, so we don't need to check expiry.
        $DB->set_field('tool_mfa_secrets', 'revoked', 1, ['userid' => $userid, 'factor' => $this->factor, 'secret' => $secret]);
    }

    /**
     * Checks whether this factor currently has an active secret, and should not add another.
     *
     * @param bool $checksession should we only check if a current session secret is active?
     * @return bool
     */
    private function has_active_secret(bool $checksession = false): bool {
        global $DB, $USER;

        $sql = "SELECT *
                  FROM {tool_mfa_secrets}
                 WHERE expiry > :now
                   AND userid = :userid
                   AND factor = :factor
                   AND revoked = 0";

        $params = [
            'now' => time(),
            'userid' => $USER->id,
            'factor' => $this->factor,
        ];

        if ($checksession) {
            $sql .= ' AND sessionid = :sessionid';
            $params['sessionid'] = $this->sessionid;
        }

        if ($DB->record_exists_sql($sql, $params)) {
            return true;
        }

        return false;
    }

    /**
     * Deletes any user secrets hanging around in the database.
     *
     * @param int $userid the userid to cleanup temp secrets for.
     * @return void
     */
    public function cleanup_temp_secrets($userid = null): void {
        global $DB, $USER;
        // Session records are autocleaned up.
        // Only DB cleanup required.

        $userid = $userid ?? $USER->id;
        $sql = 'DELETE FROM {tool_mfa_secrets}
                      WHERE userid = :userid
                        AND factor = :factor';

        $DB->execute($sql, ['userid' => $userid, 'factor' => $this->factor]);
    }
}
