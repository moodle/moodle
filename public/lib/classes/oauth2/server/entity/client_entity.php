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

use League\OAuth2\Server\Entities\ScopeEntityInterface;
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
class client_entity implements client_entity_interface {
    use ClientTrait;
    use EntityTrait;

    /** @var int Active client status */
    public const int STATUS_ACTIVE = 1;

    /** @var int Disabled client status */
    public const int STATUS_DISABLED = 2;

    /** @var int Client secret is not revoked */
    public const int SECRET_REVOKED_NO = 0;

    /** @var int Client secret is revoked */
    public const int SECRET_REVOKED_YES = 1;

    /** @var int Client secret has not yet naturally expired, or its expiry is awaiting processing */
    public const int SECRET_EXPIRY_NOT_PROCESSED = 0;

    /** @var int Client secret has naturally expired and the expiry was processed (notifications queued) */
    public const int SECRET_EXPIRY_PROCESSED = 1;

    /** @var int Public client type */
    public const int TYPE_PUBLIC = 0;

    /** @var int Confidential client type */
    public const int TYPE_CONFIDENTIAL = 1;

    /** @var string Authorization code grant type */
    public const string GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

    /** @var string Refresh token grant type */
    public const string GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';

    /** @var string Client credentials grant type */
    public const string GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

    /** @var string Password grant type */
    public const string GRANT_TYPE_PASSWORD = 'password';

    /** @var int The ID of the client */
    protected int $id;

    /** @var \core\context The owner context */
    protected \core\context $ownercontext;

    /** @var int The status of the client (STATUS_ACTIVE|STATUS_DISABLED) */
    protected int $status;

    /** @var string|null The description of the client */
    protected ?string $description = null;

    /** @var array The grant types supported by the client */
    protected array $granttypes;

    /** @var bool Whether PKCE is required for the client */
    protected bool $ispkcerequired;

    /** @var string[] The list of scopes approved for use by this client */
    protected array $scopes;

    /**
     * Get the ID of the client.
     *
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Get the context of the client owner.
     *
     * @return \core\context
     */
    public function get_owner_context(): \core\context {
        return $this->ownercontext;
    }

    /**
     * Get the status of the client (STATUS_ACTIVE|STATUS_DISABLED).
     *
     * @return int
     */
    public function get_status(): int {
        return $this->status;
    }

    /**
     * Get the grant types supported by the client.
     *
     * @return array
     */
    public function get_grant_types(): array {
        return $this->granttypes;
    }

    /**
     * Whether PKCE is required for the client.
     *
     * @return bool
     */
    public function is_pkce_required(): bool {
        if (!$this->isConfidential()) {
            // Public clients must always use PKCE, so we return true here regardless of the stored value.
            return true;
        }

        return $this->ispkcerequired;
    }

    /**
     * The list of scope identifiers allowed for use by this OAuth2 Client.
     *
     * @return string[]
     */
    public function get_scopes(): array {
        return $this->scopes;
    }

    /**
     * Whether a scope requested by the user has been allowed for this client.
     *
     * @param ScopeEntityInterface|null $scope
     * @return bool
     */
    public function is_scope_approved(?ScopeEntityInterface $scope): bool {
        if ($scope === null) {
            return false;
        }

        return in_array($scope->getIdentifier(), $this->scopes, true);
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
        if ($granttype === self::GRANT_TYPE_CLIENT_CREDENTIALS) {
            if (!$this->isConfidential() || $this->ownercontext->contextlevel !== CONTEXT_SYSTEM) {
                return false;
            }
        }

        // The Resource Owner Password Credentials grant is deprecated and no longer supported.
        if ($granttype === self::GRANT_TYPE_PASSWORD) {
            return false;
        }

        // Finally, check if the grant type is in the list of supported grant types for this client.
        return in_array($granttype, $this->granttypes, true);
    }

    /**
     * Get the description of the client.
     *
     * @return string|null
     */
    public function get_description(): ?string {
        return $this->description;
    }

    /**
     * Create a client_entity from a database record.
     *
     * @param \stdClass $clientrecord The client database record.
     * @param array $redirecturis Array of redirect URI records.
     * @return self The client entity.
     */
    public static function create_from_record(\stdClass $clientrecord, array $redirecturis): self {
        $client = new self();
        $client->id = (int) $clientrecord->id;
        $client->setIdentifier($clientrecord->clientidentifier);
        $client->name = $clientrecord->name;
        $client->description = $clientrecord->description;
        $client->ownercontext = \core\context::instance_by_id($clientrecord->ownercontext);
        $client->redirectUri = array_map(function ($redirecturi) {
            return $redirecturi->uri;
        }, $redirecturis);
        $client->status = (int) $clientrecord->status;
        $client->isConfidential = (bool) $clientrecord->isconfidential;
        $client->granttypes = !empty($clientrecord->granttypes) ? explode(',', $clientrecord->granttypes) : [];
        $client->ispkcerequired = (bool) $clientrecord->ispkcerequired;
        $client->scopes = explode(' ', $clientrecord->scopes);

        return $client;
    }
}
