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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see client_entity}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(client_entity::class)]
final class client_entity_test extends \advanced_testcase {
    /**
     * Test the client identifier getter and setter.
     *
     * @return void
     */
    public function test_identifier_getter_and_setter(): void {
        $client = new client_entity();
        $client->setIdentifier('client-id');

        $this->assertSame('client-id', $client->getIdentifier());
    }

    /**
     * Test the client name getter.
     *
     * @return void
     */
    public function test_name_getter(): void {
        $client = new client_entity();
        $this->set_protected_property($client, 'name', 'Example client');

        $this->assertSame('Example client', $client->getName());
    }

    /**
     * Test the owner context getter.
     *
     * @return void
     */
    public function test_owner_context_getter(): void {
        $client = new client_entity();
        $systemcontext = \context_system::instance();
        $this->set_protected_property($client, 'ownercontext', $systemcontext);

        $this->assertSame($systemcontext, $client->get_owner_context());
    }

    /**
     * Test the client status getter.
     *
     * @return void
     */
    public function test_status_getter(): void {
        $client = new client_entity();
        $this->set_protected_property($client, 'status', client_entity::STATUS_ACTIVE);

        $this->assertSame(client_entity::STATUS_ACTIVE, $client->get_status());
    }

    /**
     * Test the client description getter.
     *
     * @return void
     */
    public function test_description_getter(): void {
        $client = new client_entity();
        $this->set_protected_property($client, 'description', 'Client description');

        $this->assertSame('Client description', $client->get_description());
    }

    /**
     * Test the redirect URI getter.
     *
     * @param string|array $redirecturi The redirect URI to set.
     * @return void
     */
    #[DataProvider('redirect_uri_provider')]
    public function test_redirect_uri_getter(string|array $redirecturi): void {
        $client = new client_entity();
        $this->set_protected_property($client, 'redirectUri', $redirecturi);

        $this->assertSame($redirecturi, $client->getRedirectUri());
    }

    /**
     * Data provider for redirect URI tests.
     *
     * @return array Data for redirect URI tests.
     */
    public static function redirect_uri_provider(): array {
        return [
            'single uri' => ['https://example.test/callback'],
            'multiple uris' => [['https://example.test/callback', 'https://example.test/alt']],
        ];
    }

    /**
     * Test getting the confidential state.
     *
     * @param bool $value The confidential state to set.
     * @return void
     */
    #[DataProvider('confidential_state_provider')]
    public function test_confidential_state(bool $value): void {
        $client = new client_entity();
        $this->set_protected_property($client, 'isConfidential', $value);

        $this->assertSame($value, $client->isConfidential());
    }

    /**
     * Data provider for confidential state tests.
     *
     * @return array Data for confidential state tests.
     */
    public static function confidential_state_provider(): array {
        return [
            'confidential' => [true],
            'public' => [false],
        ];
    }

    /**
     * Test grant type support.
     *
     * @param string $granttype The grant type to test.
     * @param bool $isconfidential Whether the client is confidential.
     * @param bool $issystemcontext Whether the client is in the system context.
     * @param bool $expected The expected result.
     * @return void
     */
    #[DataProvider('grant_type_provider')]
    public function test_supports_grant_type(
        string $granttype,
        bool $isconfidential,
        bool $issystemcontext,
        bool $expected
    ): void {
        $client = new client_entity();
        $this->set_protected_property($client, 'isConfidential', $isconfidential);

        if ($granttype === 'client_credentials') {
            $ownercontext = $issystemcontext ? \context_system::instance() : \context_course::instance(SITEID);
            $this->set_protected_property($client, 'ownercontext', $ownercontext);
        }

        $this->assertSame($expected, $client->supportsGrantType($granttype));
    }

    /**
     * Data provider for grant type support tests.
     *
     * @return array Data for grant type support tests.
     */
    public static function grant_type_provider(): array {
        return [
            'authorization code' => ['authorization_code', false, false, true],
            'client credentials allowed' => ['client_credentials', true, true, true],
            'client credentials confidential only' => ['client_credentials', false, true, false],
            'client credentials system context only' => ['client_credentials', true, false, false],
        ];
    }

    /**
     * Helper method to set protected properties using reflection.
     *
     * @param object $object The object to set the property on.
     * @param string $property The name of the property to set.
     * @param mixed $value The value to set the property to.
     * @return void
     * @throws \ReflectionException If the property does not exist.
     */
    protected function set_protected_property(object $object, string $property, mixed $value): void {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
