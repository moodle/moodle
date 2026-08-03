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

namespace core\api\entity;

/**
 * Entity representing a REST API token.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_token_entity {
    /** @var int The token is not revoked. */
    public const int REVOKED_NO = 0;

    /** @var int The token is revoked. */
    public const int REVOKED_YES = 1;

    /** @var int The token ID. */
    protected int $id;

    /** @var string The human-readable name of the token. */
    protected string $name;

    /** @var string|null The optional description of the token. */
    protected ?string $description;

    /** @var string The authentication token string. */
    protected string $token;

    /** @var int The user ID of the token owner. */
    protected int $userid;

    /** @var string The space-separated list of scopes. */
    protected string $scopes;

    /** @var int|null The token expiry timestamp. */
    protected ?int $expirytime;

    /** @var int Whether the token is revoked. */
    protected int $revoked;

    /** @var int The token creation timestamp. */
    protected int $timecreated;

    /** @var int|null The timestamp when the token was last accessed. */
    protected ?int $lastaccessed;

    /**
     * Create an API token entity from a database record.
     *
     * @param \stdClass $record The database record.
     * @return self
     */
    public static function create_from_record(\stdClass $record): self {
        $token = new self();
        $token->id = (int) $record->id;
        $token->name = $record->name;
        $token->token = $record->token;
        $token->userid = (int) $record->userid;
        $token->scopes = $record->scopes;
        $token->timecreated = (int) $record->timecreated;
        $token->description = $record->description ?? null;
        $token->expirytime = isset($record->expirytime) ? (int) $record->expirytime : null;
        $token->revoked = (int) $record->revoked;
        $token->lastaccessed = isset($record->lastaccessed) ? (int) $record->lastaccessed : null;

        return $token;
    }

    /**
     * Get the token ID.
     *
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Get the human-readable name.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get the optional description.
     *
     * @return string|null
     */
    public function get_description(): ?string {
        return $this->description;
    }

    /**
     * Get the token string.
     *
     * @return string
     */
    public function get_token(): string {
        return $this->token;
    }

    /**
     * Get the user ID of the token owner.
     *
     * @return int
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * Get the space-separated list of scopes.
     *
     * @return string
     */
    public function get_scopes(): string {
        return $this->scopes;
    }

    /**
     * Get the expiry timestamp.
     *
     * @return int|null
     */
    public function get_expirytime(): ?int {
        return $this->expirytime;
    }

    /**
     * Get the token creation timestamp.
     *
     * @return int
     */
    public function get_timecreated(): int {
        return $this->timecreated;
    }

    /**
     * Get the revoked status.
     *
     * @return int
     */
    public function get_revoked(): int {
        return $this->revoked;
    }

    /**
     * Get the timestamp when the token was last accessed.
     *
     * @return int|null
     */
    public function get_lastaccessed(): ?int {
        return $this->lastaccessed;
    }

    /**
     * Check if the token is revoked.
     *
     * @return bool
     */
    public function is_revoked(): bool {
        return $this->revoked === self::REVOKED_YES;
    }

    /**
     * Check if the token has expired.
     *
     * @return bool
     */
    public function has_expired(): bool {
        if ($this->expirytime === null) {
            return false;
        }

        return $this->expirytime < time();
    }

    /**
     * Check if the token is active (not revoked and not expired).
     *
     * @return bool
     */
    public function is_active(): bool {
        return !$this->is_revoked() && !$this->has_expired();
    }
}
