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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see auth_code_entity}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(auth_code_entity::class)]
final class auth_code_entity_test extends \advanced_testcase {
    /**
     * Test the identifier getter and setter.
     *
     * @return void
     */
    public function test_identifier_setter_and_getter(): void {
        $token = new auth_code_entity();
        $token->setIdentifier('auth-code-id');

        $this->assertSame('auth-code-id', $token->getIdentifier());
    }

    /**
     * Test the expiry date getter and setter.
     *
     * @return void
     */
    public function test_expiry_date_time_setter_and_getter(): void {
        $token = new auth_code_entity();
        $expiry = new \DateTimeImmutable('+10 minutes');
        $token->setExpiryDateTime($expiry);

        $this->assertEquals($expiry, $token->getExpiryDateTime());
    }

    /**
     * Test the client getter and setter.
     *
     * @return void
     */
    public function test_client_setter_and_getter(): void {
        $token = new auth_code_entity();
        $client = new client_entity();
        $client->setIdentifier('client-id');

        $token->setClient($client);

        $this->assertSame($client, $token->getClient());
    }

    /**
     * Test the user identifier getter and setter.
     *
     * @return void
     */
    public function test_user_identifier_setter_and_getter(): void {
        $token = new auth_code_entity();
        $token->setUserIdentifier('user-id');

        $this->assertSame('user-id', $token->getUserIdentifier());
    }

    /**
     * Test the scope adder and getter.
     *
     * @param array $scopes The scopes to add.
     * @return void
     */
    #[DataProvider('scope_setter_and_getter_provider')]
    public function test_scope_setter_and_getter(array $scopes): void {
        $token = new auth_code_entity();
        $expectedscopes = [];

        // Loop through each provided identifier string from the provider dataset.
        foreach ($scopes as $identifier) {
            $scope = $this->createMock(ScopeEntityInterface::class);
            $scope->method('getIdentifier')->willReturn($identifier);

            $token->addScope($scope);
            $expectedscopes[] = $scope;
        }

        // Verify that the retrieved array matches the exact objects added, in order.
        $this->assertSame($expectedscopes, $token->getScopes());
    }

    /**
     * Data provider for scope setter and getter tests.
     *
     * @return array Data for scope tests.
     */
    public static function scope_setter_and_getter_provider(): array {
        return [
            'no scopes' => [
                [],
            ],
            'single scope' => [
                ['a'],
            ],
            'multiple scopes' => [
                [ 'a', 'b', 'c'],
            ],
        ];
    }

    /**
     * Test the redirect URI getter and setter.
     *
     * @return void
     */
    public function test_redirect_uri_setter_and_getter(): void {
        $token = new auth_code_entity();
        $token->setRedirectUri('https://example.test/callback');

        $this->assertSame('https://example.test/callback', $token->getRedirectUri());
    }
}
