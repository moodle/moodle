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

namespace core\oauth2;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

/**
 * Class hook_callbacks
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Provide DI Configuration for the Router system.
     *
     * @param \core\hook\di_configuration $hook
     * @codeCoverageIgnore
     */
    public static function provide_di_configuration(
        \core\hook\di_configuration $hook,
    ): void {
        if (!class_exists(\League\OAuth2\Server\Grant\AuthCodeGrant::class)) {
            // At the moment running Composer is optional, so if the OAuth2 library is not installed
            // we will skip the DI configuration for it.
            // This will be removed in Moodle 6.0 when Composer is required.
            return;
        }

        $hook->add_definition(
            'oauth2.server.private_key',
            \DI\factory(fn (\core\oauth2\setup $setup): string => $setup->get_private_key()),
        );

        $hook->add_definition(
            'oauth2.server.public_key',
            \DI\factory(fn (\core\oauth2\setup $setup): string => $setup->get_public_key()),
        );

        $hook->add_definition(
            'oauth2.server.encryption_key',
            \DI\factory(fn (\core\oauth2\setup $setup): string => $setup->get_encryption_key()),
        );

        $hook->add_definition(
            'oauth2.server.authCodeTTL',
            \DI\create(\DateInterval::class)->constructor('PT10M'),
        );
        $hook->add_definition(
            'oauth2.server.refreshTokenTTL',
            \DI\create(\DateInterval::class)->constructor('P1M'),
        );

        $hook->add_definition(
            AuthorizationServer::class,
            \DI\autowire()
                ->constructorParameter(
                    'privateKey',
                    \DI\get('oauth2.server.private_key'),
                )
                ->constructorParameter(
                    'encryptionKey',
                    \DI\get('oauth2.server.encryption_key'),
                )
                ->method(
                    'enableGrantType',
                    \DI\get(\League\OAuth2\Server\Grant\AuthCodeGrant::class),
                    \DI\create(\DateInterval::class)->constructor('PT1H'),
                )
                ->method(
                    'enableGrantType',
                    \DI\get(\League\OAuth2\Server\Grant\RefreshTokenGrant::class),
                    \DI\create(\DateInterval::class)->constructor('PT1H'),
                ),
        );

        $hook->add_definition(
            \League\OAuth2\Server\ResourceServer::class,
            \DI\autowire()
                ->constructorParameter(
                    'publicKey',
                    \DI\get('oauth2.server.public_key'),
                ),
        );

        $hook->add_definition(
            \League\OAuth2\Server\Grant\AuthCodeGrant::class,
            \DI\autowire()
                ->constructorParameter(
                    'authCodeTTL',
                    \DI\get('oauth2.server.authCodeTTL'),
                )
                ->method('disableRequireCodeChallengeForPublicClients')
                ->method('setRefreshTokenTTL', \DI\get('oauth2.server.refreshTokenTTL')),
        );

        $hook->add_definition(
            \League\OAuth2\Server\Grant\RefreshTokenGrant::class,
            \DI\autowire()
                ->method('setRefreshTokenTTL', \DI\get('oauth2.server.refreshTokenTTL')),
        );

        $hook->add_definition(
            ClientRepositoryInterface::class,
            \DI\get(\core\oauth2\server\repository\client_repository::class),
        );
        $hook->add_definition(
            AccessTokenRepositoryInterface::class,
            \DI\get(\core\oauth2\server\repository\access_token_repository::class),
        );
        $hook->add_definition(
            ScopeRepositoryInterface::class,
            \DI\get(\core\oauth2\server\repository\scope_repository::class),
        );
        $hook->add_definition(
            AuthCodeRepositoryInterface::class,
            \DI\get(\core\oauth2\server\repository\auth_code_repository::class),
        );
        $hook->add_definition(
            RefreshTokenRepositoryInterface::class,
            \DI\get(\core\oauth2\server\repository\refresh_token_repository::class),
        );
        $hook->add_definition(
            UserRepositoryInterface::class,
            \DI\get(\core\oauth2\server\repository\user_repository::class),
        );

        $hook->add_definition(
            \core\oauth2\server\token_revoker::class,
            \DI\autowire()
                ->constructorParameter(
                    'parser',
                    \DI\factory(
                        fn (): \Lcobucci\JWT\Token\Parser => new \Lcobucci\JWT\Token\Parser(
                            new \Lcobucci\JWT\Encoding\JoseEncoder()
                        ),
                    ),
                ),
        );
    }
}
