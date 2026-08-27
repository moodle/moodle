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

namespace core\tests\oauth2;

use core\oauth2\server\entity\access_token_entity;
use core\oauth2\server\entity\client_entity;
use core\oauth2\setup;
use Defuse\Crypto\Crypto;
use League\OAuth2\Server\CryptKey;

/**
 * Serialises OAuth2 credentials the way the authorisation server issues them.
 *
 * Tests must submit these forms rather than the stored identifier, which no client ever holds.
 *
 * @package    core
 * @category   test
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait credential_issuer {
    /**
     * Get the keys the authorisation server issues credentials with.
     *
     * @return setup
     */
    protected function get_oauth2_setup(): setup {
        return \core\di::get(setup::class);
    }

    /**
     * Build the JWT for an access token, the way the authorisation server does.
     *
     * @param string $clientidentifier
     * @param string $identifier Becomes the jti claim.
     * @param int $userid
     * @param string|null $privatekey Defaults to this server's key.
     * @return string
     */
    protected function serialise_access_token(
        string $clientidentifier,
        string $identifier,
        int $userid,
        ?string $privatekey = null,
    ): string {
        $client = new client_entity();
        $client->setIdentifier($clientidentifier);

        $token = new access_token_entity();
        $token->setIdentifier($identifier);
        $token->setClient($client);
        $token->setUserIdentifier((string) $userid);
        $token->setExpiryDateTime(new \DateTimeImmutable('@' . (time() + HOURSECS)));
        $token->setPrivateKey(new CryptKey($privatekey ?? $this->get_oauth2_setup()->get_private_key(), null, false));

        return $token->toString();
    }

    /**
     * Encrypt a credential payload, as used for refresh tokens and authorisation codes.
     *
     * @param array $payload
     * @return string
     */
    protected function encrypt_credential_payload(array $payload): string {
        return Crypto::encryptWithPassword(
            json_encode($payload),
            $this->get_oauth2_setup()->get_encryption_key(),
        );
    }
}
