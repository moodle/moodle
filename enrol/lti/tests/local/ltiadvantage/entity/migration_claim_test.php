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

namespace enrol_lti\local\ltiadvantage\entity;

use enrol_lti\local\ltiadvantage\repository\legacy_consumer_repository;

/**
 * Tests for migration_claim.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\migration_claim
 */
class migration_claim_test extends \advanced_testcase {
    /**
     * Setup run for each test case.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Returns a stub legacy_consumer_repository, allowing tests to verify claims using a predefined secret.
     */
    protected function get_stub_legacy_consumer_repo() {
        $mockedlegacyconsumerrepo = $this->createStub(legacy_consumer_repository::class);
        $mockedlegacyconsumerrepo->method('get_consumer_secrets')
            ->willReturn(['consumer_secret']);
        return $mockedlegacyconsumerrepo;
    }

    /**
     * Test instantiation and getters of the migration_claim.
     *
     * @dataProvider migration_claim_provider
     * @param array $migrationclaimdata the lti1p1 migration claim.
     * @param string $deploymentid string id of the tool deployment.
     * @param string $platform string url of the issuer.
     * @param string $clientid string id of the client.
     * @param string $exp expiry time.
     * @param string $nonce nonce.
     * @param bool $stublegacyconsumerrepo Whether the legacy consumer repo is a stub
     * @param array $expected array containing expectation data.
     * @covers ::__construct
     */
    public function test_migration_claim(
        array $migrationclaimdata,
        string $deploymentid,
        string $platform,
        string $clientid,
        string $exp,
        string $nonce,
        bool $stublegacyconsumerrepo,
        array $expected,
    ): void {
        if ($stublegacyconsumerrepo) {
            $legacyconsumerrepo = $this->get_stub_legacy_consumer_repo();
        } else {
            $legacyconsumerrepo = new legacy_consumer_repository();
        }

        if (!empty($expected['exception'])) {
            $this->expectException($expected['exception']);
            $this->expectExceptionMessage($expected['exceptionmessage']);
            new migration_claim(
                $migrationclaimdata,
                $deploymentid,
                $platform,
                $clientid,
                $exp,
                $nonce,
                $legacyconsumerrepo,
            );
        } else {
            $migrationclaim = new migration_claim(
                $migrationclaimdata,
                $deploymentid,
                $platform,
                $clientid,
                $exp,
                $nonce,
                $legacyconsumerrepo,
            );
            $this->assertInstanceOf(migration_claim::class, $migrationclaim);
            $this->assertEquals($expected['user_id'], $migrationclaim->get_user_id());
            $this->assertEquals($expected['context_id'], $migrationclaim->get_context_id());
            $this->assertEquals(
                $expected['tool_consumer_instance_guid'],
                $migrationclaim->get_tool_consumer_instance_guid(),
            );
            $this->assertEquals($expected['resource_link_id'], $migrationclaim->get_resource_link_id());
        }
    }

    /**
     * Data provider testing migration_claim instantiation.
     *
     * @return array[] the test case data.
     */
    public static function migration_claim_provider(): array {
        // Note: See https://www.imsglobal.org/spec/lti/v1p3/migr#lti-1-1-migration-claim for details regarding the
        // correct generation of oauth_consumer_key_sign signature.
        return [
            'Invalid - missing oauth_consumer_key' => [
                'lti1p1migrationclaim' => [
                    'oauth_consumer_key' => '',
                    'oauth_consumer_key_sign' => 'abcd',
                ],
                'deploymentid' => 'D12345',
                'platform' => 'https://lms.example.org/',
                'clientid' => 'a1b2c3d4',
                'exp' => '1622612930',
                'nonce' => 'j45j2j5nnjn24544',
                'stublegacyconsumerrepo' => false,
                'expected' => [
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Missing 'oauth_consumer_key' property in lti1p1 migration claim."
                ]
            ],
            'Invalid - missing oauth_consumer_key_sign' => [
                'lti1p1migrationclaim' => [
                    'oauth_consumer_key' => 'CONSUMER_1',
                    'oauth_consumer_key_sign' => '',
                ],
                'deploymentid' => 'D12345',
                'platform' => 'https://lms.example.org/',
                'clientid' => 'a1b2c3d4',
                'exp' => '1622612930',
                'nonce' => 'j45j2j5nnjn24544',
                'stublegacyconsumerrepo' => false,
                'expected' => [
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Missing 'oauth_consumer_key_sign' property in lti1p1 migration claim."
                ]
            ],
            'Invalid - incorrect oauth_consumer_key_sign' => [
                'lti1p1migrationclaim' => [
                    'oauth_consumer_key' => 'CONSUMER_1',
                    'oauth_consumer_key_sign' => 'badsignature',
                ],
                'deploymentid' => 'D12345',
                'platform' => 'https://lms.example.org/',
                'clientid' => 'a1b2c3d4',
                'exp' => '1622612930',
                'nonce' => 'j45j2j5nnjn24544',
                'stublegacyconsumerrepo' => false,
                'expected' => [
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'oauth_consumer_key_sign' signature in lti1p1 claim."
                ]
            ],
            'Valid - signature valid, map properties not provided' => [
                'lti1p1migrationclaim' => [
                    'oauth_consumer_key' => 'CONSUMER_1',
                    'oauth_consumer_key_sign' => base64_encode(
                        hash_hmac(
                            'sha256',
                            'CONSUMER_1&D12345&https://lms.example.org/&a1b2c3d4&1622612930&j45j2j5nnjn24544',
                            'consumer_secret'
                        )
                    ),
                ],
                'deploymentid' => 'D12345',
                'platform' => 'https://lms.example.org/',
                'clientid' => 'a1b2c3d4',
                'exp' => '1622612930',
                'nonce' => 'j45j2j5nnjn24544',
                'stublegacyconsumerrepo' => true,
                'expected' => [
                    'user_id' => null,
                    'context_id' => null,
                    'tool_consumer_instance_guid' => null,
                    'resource_link_id' => null
                ]
            ],
            'Valid - signature valid, map properties are provided' => [
                'lti1p1migrationclaim' => [
                    'oauth_consumer_key' => 'CONSUMER_1',
                    'oauth_consumer_key_sign' => base64_encode(
                        hash_hmac(
                            'sha256',
                            'CONSUMER_1&D12345&https://lms.example.org/&a1b2c3d4&1622612930&j45j2j5nnjn24544',
                            'consumer_secret'
                        )
                    ),
                    'user_id' => '24',
                    'context_id' => 'd345b',
                    'tool_consumer_instance_guid' => '12345-123',
                    'resource_link_id' => '4b6fa'
                ],
                'deploymentid' => 'D12345',
                'platform' => 'https://lms.example.org/',
                'clientid' => 'a1b2c3d4',
                'exp' => '1622612930',
                'nonce' => 'j45j2j5nnjn24544',
                'stublegacyconsumerrepo' => true,
                'expected' => [
                    'user_id' => '24',
                    'context_id' => 'd345b',
                    'tool_consumer_instance_guid' => '12345-123',
                    'resource_link_id' => '4b6fa'
                ]
            ]
        ];
    }
}
