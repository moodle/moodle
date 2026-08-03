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

use League\OAuth2\Server\CryptKeyInterface;
use Lcobucci\JWT\Configuration;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see access_token_entity}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(access_token_entity::class)]
final class access_token_entity_test extends \advanced_testcase {
    /**
     * Test the identifier getter and setter.
     *
     * @return void
     */
    public function test_identifier_getter_and_setter(): void {
        $token = new access_token_entity();
        $token->setIdentifier('access-token-id');

        $this->assertSame('access-token-id', $token->getIdentifier());
    }

    /**
     * Test the expiry date getter and setter.
     *
     * @return void
     */
    public function test_expiry_date_time_setter_and_getter(): void {
        $token = new access_token_entity();
        $expiry = new \DateTimeImmutable('+1 hour');
        $token->setExpiryDateTime($expiry);

        $this->assertEquals($expiry, $token->getExpiryDateTime());
    }

    /**
     * Test the client getter and setter.
     *
     * @return void
     */
    public function test_client_setter_and_getter(): void {
        $token = new access_token_entity();
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
        $token = new access_token_entity();
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
        $token = new access_token_entity();
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
     * Test JWT string generation.
     *
     * @return void
     */
    public function test_to_string(): void {
        $token = new access_token_entity();
        $client = new client_entity();
        $client->setIdentifier('client-id');

        $scope = $this->createMock(ScopeEntityInterface::class);
        $scope->method('getIdentifier')->willReturn('profile');

        $cryptkey = $this->getMockBuilder(CryptKeyInterface::class)->getMock();
        $cryptkey->method('getKeyContents')->willReturn($this->generate_private_key());
        $cryptkey->method('getPassPhrase')->willReturn(null);

        $token->setPrivateKey($cryptkey);
        $token->setIdentifier('access-token-id');
        $token->setExpiryDateTime(new \DateTimeImmutable('+1 hour'));
        $token->setClient($client);
        $token->setUserIdentifier('user-id');
        $token->addScope($scope);

        $jwt = $token->toString();
        $this->assertCount(3, explode('.', $jwt));
        $this->assertNotEmpty($jwt);
    }

    /**
     * Test the private key setter.
     *
     * @return void
     */
    public function test_set_private_key(): void {
        $token = new access_token_entity();
        $cryptkey = $this->getMockBuilder(CryptKeyInterface::class)->getMock();

        $token->setPrivateKey($cryptkey);

        $reflection = new \ReflectionProperty($token, 'privateKey');
        $reflection->setAccessible(true);

        $this->assertSame($cryptkey, $reflection->getValue($token));
    }

    /**
     * Test JWT configuration initialisation.
     *
     * @return void
     */
    public function test_init_jwt_configuration(): void {
        $token = new access_token_entity();
        $cryptkey = $this->getMockBuilder(CryptKeyInterface::class)->getMock();
        $cryptkey->method('getKeyContents')->willReturn($this->generate_private_key());
        $cryptkey->method('getPassPhrase')->willReturn(null);

        $token->setPrivateKey($cryptkey);
        $token->initJwtConfiguration();

        $reflection = new \ReflectionProperty($token, 'jwtConfiguration');
        $reflection->setAccessible(true);

        $this->assertInstanceOf(Configuration::class, $reflection->getValue($token));
    }

    /**
     * Generate a private key for testing.
     *
     * @return string The private key as a string.
     */
    private function generate_private_key(): string {
        $privatekeyresource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $privatekey = '';
        openssl_pkey_export($privatekeyresource, $privatekey);

        return $privatekey;
    }
}
