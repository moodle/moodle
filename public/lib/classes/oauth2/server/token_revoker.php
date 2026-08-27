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

use core\oauth2\server\repository\access_token_repository;
use core\oauth2\server\repository\auth_code_repository;
use core\oauth2\server\repository\refresh_token_repository;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use League\OAuth2\Server\CryptTrait;

/**
 * Revokes a single credential on behalf of the client that owns it, per RFC 7009.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token_revoker {
    use CryptTrait;

    /** @var string RFC 7009 token type hint. */
    public const string HINT_ACCESS_TOKEN = 'access_token';

    /** @var string RFC 7009 token type hint. */
    public const string HINT_REFRESH_TOKEN = 'refresh_token';

    /** @var string Token type hint for an authorisation code. Not in RFC 7009. */
    public const string HINT_AUTH_CODE = 'auth_code';

    /**
     * Constructor.
     *
     * @param \moodle_database $db
     * @param access_token_repository $accesstokens
     * @param refresh_token_repository $refreshtokens
     * @param auth_code_repository $authcodes
     * @param \core\oauth2\setup $setup Supplies the keys the credentials were issued with.
     * @param Parser $parser Parses a serialised access token.
     */
    public function __construct(
        /** @var \moodle_database Used for the cascading revocation transaction. */
        protected readonly \moodle_database $db,
        /** @var access_token_repository */
        protected readonly access_token_repository $accesstokens,
        /** @var refresh_token_repository */
        protected readonly refresh_token_repository $refreshtokens,
        /** @var auth_code_repository */
        protected readonly auth_code_repository $authcodes,
        /** @var \core\oauth2\setup */
        protected readonly \core\oauth2\setup $setup,
        /** @var Parser */
        protected readonly Parser $parser,
    ) {
        $this->encryptionKey = $setup->get_encryption_key();
    }

    /**
     * Read the stored identifier out of a signed access token's jti claim.
     *
     * @param string $token The access token as the client presented it.
     * @return string|null Null if this is not a token we issued.
     */
    protected function resolve_access_token_identifier(string $token): ?string {
        try {
            $parsed = $this->parser->parse($token);

            // The jti claim is caller-supplied until the signature proves we issued the token.
            (new Validator())->assert(
                $parsed,
                new SignedWith(new Sha256(), InMemory::plainText($this->setup->get_public_key())),
            );
        } catch (\Exception $e) {
            // Unparseable, or not signed by this server. Either way it is not a token of ours.
            return null;
        }

        $identifier = $parsed->claims()->get('jti');

        return is_string($identifier) ? $identifier : null;
    }

    /**
     * Read the stored identifier out of an encrypted credential payload.
     *
     * @param string $token The credential as the client presented it.
     * @param string $field The payload field holding the identifier.
     * @return string|null Null if this is not a credential we issued.
     */
    protected function resolve_encrypted_identifier(string $token, string $field): ?string {
        try {
            $payload = json_decode($this->decrypt($token), true);
        } catch (\Exception $e) {
            // Wrong key, or the payload has been tampered with.
            return null;
        }

        return isset($payload[$field]) && is_string($payload[$field]) ? $payload[$field] : null;
    }

    /**
     * Revoke a credential, if this client owns it. Silent when it does not, per RFC 7009 section 2.2.
     *
     * @param string $token The credential as the client presented it.
     * @param string $clientidentifier The client asking for the revocation.
     * @param string|null $hint The RFC 7009 token type hint, if the client sent one.
     * @return void
     */
    public function revoke(string $token, string $clientidentifier, ?string $hint = null): void {
        $revokers = [
            self::HINT_ACCESS_TOKEN => $this->revoke_access_token(...),
            self::HINT_REFRESH_TOKEN => $this->revoke_refresh_token(...),
            self::HINT_AUTH_CODE => $this->revoke_auth_code(...),
        ];

        // The hint only decides what to try first. RFC 7009 section 2.1 requires the remaining types
        // to be searched anyway, because a client that hints wrongly must still have its token
        // revoked rather than silently left alive.
        if ($hint !== null && array_key_exists($hint, $revokers)) {
            $revokers = [$hint => $revokers[$hint]] + $revokers;
        }

        foreach ($revokers as $revoker) {
            if ($revoker($token, $clientidentifier)) {
                return;
            }
        }
    }

    /**
     * Revoke an access token, if this client owns it.
     *
     * @param string $token The access token as the client presented it.
     * @param string $clientidentifier The client asking for the revocation.
     * @return bool True if the token was found and revoked.
     */
    protected function revoke_access_token(string $token, string $clientidentifier): bool {
        $identifier = $this->resolve_access_token_identifier($token);

        if ($identifier === null) {
            return false;
        }

        if (!$this->accesstokens->is_owned_by_client($identifier, $clientidentifier)) {
            return false;
        }

        $this->accesstokens->revokeAccessToken($identifier);

        return true;
    }

    /**
     * Revoke a refresh token and the access token it was issued alongside, if this client owns it.
     *
     * @param string $token The refresh token as the client presented it.
     * @param string $clientidentifier The client asking for the revocation.
     * @return bool True if the token was found and revoked.
     */
    protected function revoke_refresh_token(string $token, string $clientidentifier): bool {
        $identifier = $this->resolve_encrypted_identifier($token, 'refresh_token_id');

        if ($identifier === null) {
            return false;
        }

        $accesstokenidentifier = $this->refreshtokens->get_owning_access_token_identifier(
            $identifier,
            $clientidentifier,
        );

        if ($accesstokenidentifier === null) {
            return false;
        }

        $transaction = $this->db->start_delegated_transaction();
        $this->refreshtokens->revokeRefreshToken($identifier);
        $this->accesstokens->revokeAccessToken($accesstokenidentifier);
        $transaction->allow_commit();

        return true;
    }

    /**
     * Revoke an authorisation code, if this client owns it.
     *
     * @param string $token The authorisation code as the client presented it.
     * @param string $clientidentifier The client asking for the revocation.
     * @return bool True if the code was found and revoked.
     */
    protected function revoke_auth_code(string $token, string $clientidentifier): bool {
        $identifier = $this->resolve_encrypted_identifier($token, 'auth_code_id');

        if ($identifier === null) {
            return false;
        }

        if (!$this->authcodes->is_owned_by_client($identifier, $clientidentifier)) {
            return false;
        }

        $this->authcodes->revokeAuthCode($identifier);

        return true;
    }
}
