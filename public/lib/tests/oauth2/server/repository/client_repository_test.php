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

namespace core\oauth2\server\repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use core\oauth2\server\entity\client_entity;

/**
 * Tests for {@see client_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(client_repository::class)]
final class client_repository_test extends \advanced_testcase {
    /**
     * Test getClientEntity under different database record scenarios.
     *
     * @param array $clientdata The data to insert into the clients table.
     * @param string $clientidentifier The client identifier to check.
     * @param bool $expectednull Whether the result should be null.
     * @param array|null $expectedproperties The expected entity properties if not null.
     * @return void
     */
    #[DataProvider('get_client_entity_provider')]
    public function test_get_client_entity(
        array $clientdata,
        string $clientidentifier,
        bool $expectednull,
        ?array $expectedproperties
    ): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new client_repository();

        if (!empty($clientdata)) {
            // Ensure valid system context is set.
            $clientdata['ownercontext'] = \context_system::instance()->id;
            $DB->insert_record('oauth2_server_clients', (object) $clientdata);

            foreach ($clientdata['redirecturis'] as $uri) {
                $DB->insert_record('oauth2_server_client_redirect_uris', (object) [
                    'clientidentifier' => $clientdata['clientidentifier'],
                    'uri' => $uri,
                ]);
            }
        }

        $client = $repository->getClientEntity($clientidentifier);

        if ($expectednull) {
            $this->assertNull($client);
        } else {
            $this->assertInstanceOf(client_entity::class, $client);
            $this->assertSame($expectedproperties['identifier'], $client->getIdentifier());
            $this->assertSame($expectedproperties['name'], $client->getName());
            $this->assertSame($expectedproperties['description'], $client->get_description());
            $this->assertSame($expectedproperties['status'], $client->get_status());
            $this->assertSame($expectedproperties['isconfidential'], $client->isConfidential());
            $this->assertEqualsCanonicalizing($expectedproperties['granttypes'], $client->get_grant_types());
            $this->assertEquals($expectedproperties['ispkceenabled'], $client->is_pkce_enabled());
            $this->assertEqualsCanonicalizing($expectedproperties['redirecturi'], array_values($client->getRedirectUri()));
        }
    }

    /**
     * Data provider for getClientEntity tests.
     *
     * @return array The datasets.
     */
    public static function get_client_entity_provider(): array {
        return [
            'non-existent client' => [
                [],
                'client-identifier-1',
                true,
                null,
            ],
            'active, confidential client with single redirect uri' => [
                [
                    'name' => 'Active Client',
                    'clientidentifier' => 'client-identifier-1',
                    'description' => 'A test description',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => 1,
                    'granttypes' => client_entity::GRANT_TYPE_CLIENT_CREDENTIALS,
                    'ispkceenabled' => false,
                    'timecreated' => time(),
                    'redirecturis' => ['https://example.test/callback'],
                ],
                'client-identifier-1',
                false,
                [
                    'identifier' => 'client-identifier-1',
                    'name' => 'Active Client',
                    'description' => 'A test description',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => true,
                    'granttypes' => [client_entity::GRANT_TYPE_CLIENT_CREDENTIALS],
                    'ispkceenabled' => false,
                    'redirecturi' => ['https://example.test/callback'],
                ],
            ],
            'revoked public client with multiple redirect uris' => [
                [
                    'name' => 'Revoked Client',
                    'clientidentifier' => 'client-identifier-1',
                    'description' => null,
                    'status' => client_entity::STATUS_REVOKED,
                    'isconfidential' => 0,
                    'granttypes' => implode(
                        ',' ,
                        [
                            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                            client_entity::GRANT_TYPE_REFRESH_TOKEN,
                        ]
                    ),
                    'ispkceenabled' => true,
                    'timecreated' => time(),
                    'redirecturis' => ['https://example.test/uri1', 'https://example.test/uri2'],
                ],
                'client-identifier-1',
                false,
                [
                    'identifier' => 'client-identifier-1',
                    'name' => 'Revoked Client',
                    'description' => null,
                    'status' => client_entity::STATUS_REVOKED,
                    'isconfidential' => false,
                    'granttypes' => [
                        client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                        client_entity::GRANT_TYPE_REFRESH_TOKEN,
                    ],
                    'ispkceenabled' => true,
                    'redirecturi' => ['https://example.test/uri1', 'https://example.test/uri2'],
                ],
            ],
        ];
    }

    /**
     * Test validateClient under different scenarios using data providers.
     *
     * @param array $clientdata The data to insert into the clients table.
     * @param string|null $plaintextsecret The plain secret to insert (hashed) into secrets table.
     * @param int $secretrevoked Whether the secret is marked revoked in db.
     * @param int $secretexpirytime The timestamp when the secret expires.
     * @param string $checkidentifier The client identifier passed to validateClient.
     * @param string|null $checksecret The secret passed to validateClient.
     * @param string|null $checkgranttype The grant type passed to validateClient.
     * @param bool $expectedresult The expected boolean return.
     * @return void
     */
    #[DataProvider('validate_client_provider')]
    public function test_validate_client(
        array $clientdata,
        ?string $plaintextsecret,
        int $secretrevoked,
        int $secretexpirytime,
        string $checkidentifier,
        ?string $checksecret,
        ?string $checkgranttype,
        bool $expectedresult
    ): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new client_repository();

        if (!empty($clientdata)) {
            $clientdata['ownercontext'] = \context_system::instance()->id;
            $DB->insert_record('oauth2_server_clients', (object) $clientdata);

            if ($plaintextsecret !== null) {
                $DB->insert_record('oauth2_server_client_secrets', (object) [
                    'clientidentifier' => $clientdata['clientidentifier'],
                    'secret' => password_hash($plaintextsecret, PASSWORD_DEFAULT),
                    'revoked' => $secretrevoked,
                    'expirytime' => $secretexpirytime,
                    'timecreated' => time(),
                ]);
            }
        }

        $this->assertSame(
            $expectedresult,
            $repository->validateClient($checkidentifier, $checksecret, $checkgranttype)
        );
    }

    /**
     * Data provider for validateClient tests.
     *
     * @return array The datasets.
     */
    public static function validate_client_provider(): array {
        return [
            'non-existent client' => [
                [],
                null,
                client_entity::SECRET_REVOKED_NO,
                time() + 3600,
                'non-existent',
                'secret',
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                false,
            ],
            'confidential client with correct secret in active state' => [
                [
                    'name' => 'Confidential Client',
                    'clientidentifier' => 'client-conf',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => 1,
                    'granttypes' => implode(
                        ',',
                        [
                            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                            client_entity::GRANT_TYPE_REFRESH_TOKEN,
                        ],
                    ),
                    'timecreated' => time(),
                ],
                'supersecret',
                client_entity::SECRET_REVOKED_NO,
                time() + 3600,
                'client-conf',
                'supersecret',
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                true,
            ],
            'confidential client with wrong secret' => [
                [
                    'name' => 'Confidential Client',
                    'clientidentifier' => 'client-conf',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => 1,
                    'granttypes' => implode(
                        ',',
                        [
                            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                            client_entity::GRANT_TYPE_REFRESH_TOKEN,
                        ],
                    ),
                    'timecreated' => time(),
                ],
                'supersecret',
                client_entity::SECRET_REVOKED_NO,
                time() + 3600,
                'client-conf',
                'wrongsecret',
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                false,
            ],
            'confidential client with revoked secret' => [
                [
                    'name' => 'Confidential Client',
                    'clientidentifier' => 'client-conf',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => 1,
                    'granttypes' => implode(
                        ',',
                        [
                            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                            client_entity::GRANT_TYPE_REFRESH_TOKEN,
                        ],
                    ),
                    'timecreated' => time(),
                ],
                'supersecret',
                client_entity::SECRET_REVOKED_YES,
                time() + 3600,
                'client-conf',
                'supersecret',
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                false,
            ],
            'confidential client with empty secret input' => [
                [
                    'name' => 'Confidential Client',
                    'clientidentifier' => 'client-conf',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => 1,
                    'granttypes' => implode(
                        ',',
                        [
                            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                            client_entity::GRANT_TYPE_REFRESH_TOKEN,
                        ],
                    ),
                    'timecreated' => time(),
                ],
                'supersecret',
                client_entity::SECRET_REVOKED_NO,
                time() + 3600,
                'client-conf',
                null,
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                false,
            ],
            'confidential client with expired secret' => [
                [
                    'name' => 'Confidential Client',
                    'clientidentifier' => 'client-conf',
                    'status' => client_entity::STATUS_ACTIVE,
                    'isconfidential' => 1,
                    'granttypes' => implode(
                        ',',
                        [
                            client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                            client_entity::GRANT_TYPE_REFRESH_TOKEN,
                        ],
                    ),
                    'timecreated' => time() - 7200,
                ],
                'supersecret',
                client_entity::SECRET_REVOKED_NO,
                time() - 3600,
                'client-conf',
                'supersecret',
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                false,
            ],
        ];
    }
}
