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

namespace core\oauth2\server\entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * OAuth2 server client entity.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_entity implements ClientEntityInterface {
    use ClientTrait;
    use EntityTrait;

    /** @var int Active client status */
    public const int STATUS_ACTIVE = 1;

    /** @var int Revoked client status */
    public const int STATUS_REVOKED = 2;

    /** @var \core\context The owner context */
    protected \core\context $ownercontext;

    /** @var int The status of the client (STATUS_ACTIVE|STATUS_REVOKED) */
    protected int $status;

    /** @var string|null The description of the client */
    protected ?string $description = null;

    /**
     * Get the context of the client owner.
     *
     * @return \core\context
     */
    public function get_owner_context(): \core\context {
        return $this->ownercontext;
    }

    /**
     * Get the status of the client (STATUS_ACTIVE|STATUS_REVOKED).
     *
     * @return int
     */
    public function get_status(): int {
        return $this->status;
    }

    /**
     * Returns true if the client supports the given grant type.
     *
     * @param string $granttype The grant type to check.
     * @return bool True if the client supports the grant type, false otherwise.
     */
    public function supportsGrantType(string $granttype): bool {
        // If Client Credentials grant is requested (Machine-to-machine communication), the client must be confidential
        // and owned by system context.
        if ($granttype === 'client_credentials') {
            if (!$this->isConfidential() || $this->ownercontext->contextlevel !== CONTEXT_SYSTEM) {
                return false;
            }
        }

        // For now, all clients support all grant types.
        return true;
    }

    /**
     * Get the description of the client.
     *
     * @return string|null
     */
    public function get_description(): ?string {
        return $this->description;
    }
}
