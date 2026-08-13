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

use core\exception\moodle_exception;
use core\oauth2\server\entity\access_token_entity;
use core\oauth2\server\entity\auth_code_entity;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\refresh_token_entity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see client_manager}.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(client_manager::class)]
final class client_manager_test extends \advanced_testcase {
    /** @var int The frozen time used by every test in this class. */
    private const int NOW = 1750000000;

    /**
     * Freeze the clock and return a manager resolved from the DI container.
     *
     * The clock is mocked before the manager is resolved so that the manager is constructed with
     * the frozen clock, which every timestamp assertion below relies on.
     *
     * @return client_manager
     */
    private function get_manager(): client_manager {
        $this->mock_clock_with_frozen(self::NOW);

        return \core\di::get(client_manager::class);
    }

    /**
     * Create a client for use as a fixture.
     *
     * @param client_manager $manager The manager to create the client with.
     * @param array $redirecturis The redirect URIs to register.
     * @return \stdClass The client record.
     */
    private function create_fixture_client(
        client_manager $manager,
        array $redirecturis = [],
        array $supportedgrants = ['authorization_code', 'client_credentials', 'refresh_token'],
    ): \stdClass {
        global $DB;

        $client = $manager->create_client(
            name: 'Test client',
            ownercontext: \core\context\system::instance(),
            granttypes: $supportedgrants,
            redirecturis: $redirecturis,
        );

        return $DB->get_record(
            'oauth2_server_clients',
            ['clientidentifier' => $client->getIdentifier()],
            '*',
            MUST_EXIST,
        );
    }

    /**
     * Issue an access token, a refresh token, an authorisation code and a granted scope to a client.
     *
     * @param string $clientidentifier The client identifier to issue the credentials to.
     * @return void
     */
    private function issue_credentials(string $clientidentifier): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        $DB->insert_record('oauth2_server_client_access_tokens', (object) [
            'identifier' => 'accesstoken-' . $clientidentifier,
            'userid' => $user->id,
            'clientidentifier' => $clientidentifier,
            'scopes' => 'user/read',
            'expirytime' => self::NOW + HOURSECS,
            'revoked' => access_token_entity::REVOKED_NO,
            'timecreated' => self::NOW,
        ]);

        $DB->insert_record('oauth2_server_client_refresh_tokens', (object) [
            'identifier' => 'refreshtoken-' . $clientidentifier,
            'accesstokenidentifier' => 'accesstoken-' . $clientidentifier,
            'expirytime' => self::NOW + DAYSECS,
            'revoked' => refresh_token_entity::REVOKED_NO,
            'timecreated' => self::NOW,
        ]);

        $DB->insert_record('oauth2_server_client_auth_codes', (object) [
            'identifier' => 'authcode-' . $clientidentifier,
            'userid' => $user->id,
            'clientidentifier' => $clientidentifier,
            'redirecturi' => 'https://example.com/callback',
            'scopes' => 'user/read',
            'expirytime' => self::NOW + MINSECS,
            'revoked' => auth_code_entity::REVOKED_NO,
            'timecreated' => self::NOW,
        ]);

        $DB->insert_record('oauth2_server_client_granted_scopes', (object) [
            'clientidentifier' => $clientidentifier,
            'userid' => $user->id,
            'scope' => 'user/read',
            'timecreated' => self::NOW,
        ]);
    }

    /**
     * Test that the manager can be resolved from the dependency injection container.
     *
     * @return void
     */
    public function test_resolvable_via_dependency_injection(): void {
        $this->resetAfterTest();

        $this->assertInstanceOf(client_manager::class, \core\di::get(client_manager::class));
    }

    /**
     * Test creating a client.
     *
     * @return void
     */
    public function test_create_client(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $context = \core\context\system::instance();

        $client = $manager->create_client(
            name: 'My integration',
            ownercontext: $context,
            granttypes: ['authorization_code', 'client_credentials', 'refresh_token'],
            redirecturis: ['https://example.com/callback'],
            description: 'Does something useful',
        );

        $this->assertInstanceOf(client_entity::class, $client);
        $this->assertSame('My integration', $client->getName());
        $this->assertSame('Does something useful', $client->get_description());
        $this->assertSame(client_entity::STATUS_ACTIVE, $client->get_status());
        $this->assertTrue($client->isConfidential());
        $this->assertSame($context->id, $client->get_owner_context()->id);
        $this->assertSame(['https://example.com/callback'], array_values((array) $client->getRedirectUri()));
        $this->assertEqualsCanonicalizing(
            ['authorization_code', 'client_credentials', 'refresh_token'],
            $client->get_grant_types(),
        );
        $this->assertTrue($client->is_pkce_enabled());

        $record = $DB->get_record(
            'oauth2_server_clients',
            ['clientidentifier' => $client->getIdentifier()],
            '*',
            MUST_EXIST,
        );
        $this->assertSame((int) $record->timecreated, self::NOW);
        $this->assertSame((int) $record->timemodified, self::NOW);
    }

    /**
     * Test that a created client can be read back identically through the manager.
     *
     * @return void
     */
    public function test_create_client_is_readable_through_the_repository(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $created = $manager->create_client(
            name: 'My integration',
            ownercontext: \core\context\system::instance(),
            granttypes: ['authorization_code', 'client_credentials', 'refresh_token'],
            redirecturis: ['https://example.com/callback', 'https://example.com/other'],
        );

        $fetched = $manager->get_client($created->getIdentifier());

        $this->assertInstanceOf(client_entity::class, $fetched);
        $this->assertSame($created->getName(), $fetched->getName());
        $this->assertSame($created->get_status(), $fetched->get_status());
        $this->assertEqualsCanonicalizing(
            array_values((array) $created->getRedirectUri()),
            array_values((array) $fetched->getRedirectUri()),
        );
        $this->assertEqualsCanonicalizing(
            $created->get_grant_types(),
            $fetched->get_grant_types(),
        );
    }

    /**
     * Test that duplicate redirect URIs given at creation are stored only once.
     *
     * @return void
     */
    public function test_create_client_deduplicates_redirect_uris(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager, [
            'https://example.com/callback',
            'https://example.com/callback',
        ]);

        $this->assertSame(
            ['https://example.com/callback'],
            array_values($manager->get_redirect_uris((int) $record->id)),
        );
    }

    /**
     * Test that creating a client with an unusable redirect URI is rejected.
     *
     * @return void
     */
    public function test_create_client_rejects_invalid_redirect_uri(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();

        try {
            $manager->create_client(
                name: 'Test client',
                ownercontext: \core\context\system::instance(),
                granttypes: ['authorization_code', 'client_credentials', 'refresh_token'],
                redirecturis: ['http://example.com/callback'],
            );
            $this->fail('A moodle_exception was expected.');
        } catch (moodle_exception $e) {
            $this->assertSame('oauth2clientinvalidredirecturi', $e->errorcode);
        }

        // The URIs are checked before anything is written, so no client may be left behind.
        $this->assertSame(0, $DB->count_records('oauth2_server_clients'));
    }

    /**
     * Test fetching a client by its database ID.
     *
     * @return void
     */
    public function test_get_client_by_id(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $client = $manager->get_client_by_id((int) $record->id);

        $this->assertInstanceOf(client_entity::class, $client);
        $this->assertSame($record->clientidentifier, $client->getIdentifier());
    }

    /**
     * Test that fetching an unknown client returns null rather than throwing.
     *
     * @return void
     */
    public function test_get_unknown_client_returns_null(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();

        $this->assertNull($manager->get_client('no-such-client'));
        $this->assertNull($manager->get_client_by_id(-1));
    }

    /**
     * Test updating the administrative metadata of a client.
     *
     * @return void
     */
    public function test_update_client(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $manager->update_client((int) $record->id, [
            'name' => 'Renamed client',
            'description' => 'A new description',
        ]);

        $updated = $DB->get_record('oauth2_server_clients', ['id' => $record->id], '*', MUST_EXIST);
        $this->assertSame('Renamed client', $updated->name);
        $this->assertSame('A new description', $updated->description);
        $this->assertSame(self::NOW, (int) $updated->timemodified);
    }

    /**
     * Test that update_client refuses to change anything but the name and the description.
     *
     * @return void
     */
    public function test_update_client_ignores_disallowed_fields(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $manager->update_client((int) $record->id, [
            'clientidentifier' => 'hijacked',
            'ownercontext' => 1,
            'status' => client_entity::STATUS_REVOKED,
            'isconfidential' => 0,
        ]);

        $updated = $DB->get_record('oauth2_server_clients', ['id' => $record->id], '*', MUST_EXIST);
        $this->assertSame($record->clientidentifier, $updated->clientidentifier);
        $this->assertSame($record->ownercontext, $updated->ownercontext);
        $this->assertSame(client_entity::STATUS_ACTIVE, (int) $updated->status);
        $this->assertSame($record->isconfidential, $updated->isconfidential);
    }

    /**
     * Test that revoking a client cascades to every credential it holds.
     *
     * @return void
     */
    public function test_revoke_client_cascades_to_secrets_and_tokens(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);
        $manager->create_secret((int) $record->id);
        $this->issue_credentials($record->clientidentifier);

        // A second client's credentials must be left alone.
        $other = $this->create_fixture_client($manager);
        $manager->create_secret((int) $other->id);
        $this->issue_credentials($other->clientidentifier);

        $manager->revoke_client((int) $record->id);

        $this->assertSame(
            client_entity::STATUS_REVOKED,
            (int) $DB->get_field('oauth2_server_clients', 'status', ['id' => $record->id]),
        );
        $this->assertSame(1, $DB->count_records('oauth2_server_client_secrets', [
            'clientidentifier' => $record->clientidentifier,
            'revoked' => client_entity::SECRET_REVOKED_YES,
        ]));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_access_tokens', [
            'clientidentifier' => $record->clientidentifier,
            'revoked' => access_token_entity::REVOKED_YES,
        ]));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_auth_codes', [
            'clientidentifier' => $record->clientidentifier,
            'revoked' => auth_code_entity::REVOKED_YES,
        ]));
        $this->assertSame(refresh_token_entity::REVOKED_YES, (int) $DB->get_field(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            ['identifier' => 'refreshtoken-' . $record->clientidentifier],
        ));

        // The untouched client keeps everything.
        $this->assertSame(client_entity::STATUS_ACTIVE, (int) $DB->get_field(
            'oauth2_server_clients',
            'status',
            ['id' => $other->id],
        ));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_secrets', [
            'clientidentifier' => $other->clientidentifier,
            'revoked' => client_entity::SECRET_REVOKED_YES,
        ]));
        $this->assertSame(refresh_token_entity::REVOKED_NO, (int) $DB->get_field(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            ['identifier' => 'refreshtoken-' . $other->clientidentifier],
        ));
    }

    /**
     * Test that reactivating a client restores the client record and nothing else.
     *
     * @return void
     */
    public function test_reactivate_client_does_not_restore_credentials(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);
        $manager->create_secret((int) $record->id);
        $this->issue_credentials($record->clientidentifier);

        $manager->revoke_client((int) $record->id);
        $manager->reactivate_client((int) $record->id);

        $this->assertSame(client_entity::STATUS_ACTIVE, (int) $DB->get_field(
            'oauth2_server_clients',
            'status',
            ['id' => $record->id],
        ));

        // Every credential revoked alongside the client stays revoked.
        $this->assertEmpty($manager->get_secrets((int) $record->id));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_access_tokens', [
            'clientidentifier' => $record->clientidentifier,
            'revoked' => access_token_entity::REVOKED_NO,
        ]));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_auth_codes', [
            'clientidentifier' => $record->clientidentifier,
            'revoked' => auth_code_entity::REVOKED_NO,
        ]));
        $this->assertSame(refresh_token_entity::REVOKED_YES, (int) $DB->get_field(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            ['identifier' => 'refreshtoken-' . $record->clientidentifier],
        ));
    }

    /**
     * Test that an active client cannot be deleted before it is revoked.
     *
     * @return void
     */
    public function test_delete_client_requires_revocation_first(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        try {
            $manager->delete_client((int) $record->id);
            $this->fail('A moodle_exception was expected.');
        } catch (moodle_exception $e) {
            $this->assertSame('oauth2clientnotrevoked', $e->errorcode);
        }

        $this->assertTrue($DB->record_exists('oauth2_server_clients', ['id' => $record->id]));
    }

    /**
     * Test that deleting a revoked client removes it and all of its child records.
     *
     * @return void
     */
    public function test_delete_client_removes_all_child_records(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager, ['https://example.com/callback']);
        $manager->create_secret((int) $record->id);
        $this->issue_credentials($record->clientidentifier);

        $other = $this->create_fixture_client($manager, ['https://example.com/other']);
        $manager->create_secret((int) $other->id);
        $this->issue_credentials($other->clientidentifier);

        $manager->revoke_client((int) $record->id);
        $manager->delete_client((int) $record->id);

        $params = ['clientidentifier' => $record->clientidentifier];
        $this->assertFalse($DB->record_exists('oauth2_server_clients', ['id' => $record->id]));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_secrets', $params));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_redirect_uris', $params));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_access_tokens', $params));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_auth_codes', $params));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_granted_scopes', $params));
        $this->assertSame(0, $DB->count_records('oauth2_server_client_refresh_tokens', [
            'identifier' => 'refreshtoken-' . $record->clientidentifier,
        ]));

        // The second client is entirely unaffected.
        $otherparams = ['clientidentifier' => $other->clientidentifier];
        $this->assertTrue($DB->record_exists('oauth2_server_clients', ['id' => $other->id]));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_secrets', $otherparams));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_redirect_uris', $otherparams));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_access_tokens', $otherparams));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_auth_codes', $otherparams));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_granted_scopes', $otherparams));
        $this->assertSame(1, $DB->count_records('oauth2_server_client_refresh_tokens', [
            'identifier' => 'refreshtoken-' . $other->clientidentifier,
        ]));
    }

    /**
     * Test generating a client secret.
     *
     * @return void
     */
    public function test_create_secret(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $secret = $manager->create_secret((int) $record->id);

        $stored = $DB->get_record(
            'oauth2_server_client_secrets',
            ['clientidentifier' => $record->clientidentifier],
            '*',
            MUST_EXIST,
        );

        // The plain text secret is returned to the caller, but only its hash is stored.
        $this->assertNotSame($secret, $stored->secret);
        $this->assertTrue(password_verify($secret, $stored->secret));
        $this->assertSame(client_entity::SECRET_REVOKED_NO, (int) $stored->revoked);
        $this->assertSame(self::NOW, (int) $stored->timecreated);
        $this->assertSame(self::NOW + client_manager::SECRET_LIFETIME, (int) $stored->expirytime);
    }

    /**
     * Test that a caller-supplied expiry time is honoured.
     *
     * @return void
     */
    public function test_create_secret_with_explicit_expiry(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $manager->create_secret((int) $record->id, self::NOW + DAYSECS);

        $this->assertSame(self::NOW + DAYSECS, (int) $DB->get_field(
            'oauth2_server_client_secrets',
            'expirytime',
            ['clientidentifier' => $record->clientidentifier],
        ));
    }

    /**
     * Test that a client cannot hold more than the maximum number of active secrets.
     *
     * @return void
     */
    public function test_create_secret_enforces_active_secret_limit(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        for ($i = 0; $i < client_manager::MAX_ACTIVE_SECRETS; $i++) {
            $manager->create_secret((int) $record->id);
        }

        try {
            $manager->create_secret((int) $record->id);
            $this->fail('A moodle_exception was expected.');
        } catch (moodle_exception $e) {
            $this->assertSame('oauth2clientsecretlimitreached', $e->errorcode);
        }

        $this->assertSame(
            client_manager::MAX_ACTIVE_SECRETS,
            $DB->count_records('oauth2_server_client_secrets', ['clientidentifier' => $record->clientidentifier]),
        );
    }

    /**
     * Test that the limit counts only active secrets, so revoking one frees a slot.
     *
     * @return void
     */
    public function test_create_secret_limit_frees_up_when_a_secret_is_revoked(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        for ($i = 0; $i < client_manager::MAX_ACTIVE_SECRETS; $i++) {
            $manager->create_secret((int) $record->id);
        }

        $secrets = $manager->get_secrets((int) $record->id);
        $manager->revoke_secret((int) reset($secrets)->id);

        // A slot is now free, so this must not throw.
        $manager->create_secret((int) $record->id);

        $this->assertCount(client_manager::MAX_ACTIVE_SECRETS, $manager->get_secrets((int) $record->id));
    }

    /**
     * Test that an expired secret does not occupy one of the active secret slots.
     *
     * @return void
     */
    public function test_create_secret_limit_ignores_expired_secrets(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        for ($i = 0; $i < client_manager::MAX_ACTIVE_SECRETS; $i++) {
            $manager->create_secret((int) $record->id);
        }

        $secrets = $manager->get_secrets((int) $record->id);
        $DB->set_field(
            'oauth2_server_client_secrets',
            'expirytime',
            self::NOW - 1,
            ['id' => reset($secrets)->id],
        );

        $manager->create_secret((int) $record->id);

        $this->assertCount(client_manager::MAX_ACTIVE_SECRETS, $manager->get_secrets((int) $record->id));
        $this->assertSame(
            client_manager::MAX_ACTIVE_SECRETS + 1,
            $DB->count_records('oauth2_server_client_secrets', ['clientidentifier' => $record->clientidentifier]),
        );
    }

    /**
     * Test that a revoked client cannot be issued a new secret.
     *
     * @return void
     */
    public function test_create_secret_rejects_revoked_client(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);
        $manager->revoke_client((int) $record->id);

        try {
            $manager->create_secret((int) $record->id);
            $this->fail('A moodle_exception was expected.');
        } catch (moodle_exception $e) {
            $this->assertSame('oauth2clientrevoked', $e->errorcode);
        }

        $this->assertEmpty($manager->get_secrets((int) $record->id, true));
    }

    /**
     * Test that a public client cannot be issued a secret.
     *
     * @return void
     */
    public function test_create_secret_rejects_public_client(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $client = $manager->create_client(
            name: 'Public client',
            granttypes: ['authorization_code', 'refresh_token'],
            ownercontext: \core\context\system::instance(),
            isconfidential: false,
        );
        $clientid = (int) $DB->get_field(
            'oauth2_server_clients',
            'id',
            ['clientidentifier' => $client->getIdentifier()],
        );

        $this->assertFalse($client->isConfidential());

        try {
            $manager->create_secret($clientid);
            $this->fail('A moodle_exception was expected.');
        } catch (moodle_exception $e) {
            $this->assertSame('oauth2clientnotconfidential', $e->errorcode);
        }

        $this->assertEmpty($manager->get_secrets($clientid, true));
    }

    /**
     * Test fetching secrets with and without the inactive ones.
     *
     * @return void
     */
    public function test_get_secrets(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $manager->create_secret((int) $record->id);
        $manager->create_secret((int) $record->id);

        $secrets = $manager->get_secrets((int) $record->id);
        $manager->revoke_secret((int) reset($secrets)->id);

        $this->assertCount(1, $manager->get_secrets((int) $record->id));
        $this->assertCount(2, $manager->get_secrets((int) $record->id, true));
    }

    /**
     * Test that a revoked secret is reported as revoked.
     *
     * @return void
     */
    public function test_revoke_secret(): void {
        global $DB;

        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);
        $manager->create_secret((int) $record->id);

        $secrets = $manager->get_secrets((int) $record->id);
        $secretid = (int) reset($secrets)->id;

        $manager->revoke_secret($secretid);

        $this->assertSame(client_entity::SECRET_REVOKED_YES, (int) $DB->get_field(
            'oauth2_server_client_secrets',
            'revoked',
            ['id' => $secretid],
        ));
    }

    /**
     * Test that operating on a client which does not exist throws.
     *
     * @return void
     */
    public function test_missing_client_throws(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();

        $this->expectException(\dml_missing_record_exception::class);
        $manager->create_secret(-1);
    }

    /**
     * Test adding, listing and removing redirect URIs.
     *
     * @return void
     */
    public function test_redirect_uri_lifecycle(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager, ['https://example.com/first']);
        $clientid = (int) $record->id;

        $manager->add_redirect_uri($clientid, 'https://example.com/second');
        $this->assertEqualsCanonicalizing(
            ['https://example.com/first', 'https://example.com/second'],
            array_values($manager->get_redirect_uris($clientid)),
        );

        $manager->remove_redirect_uri($clientid, 'https://example.com/first');
        $this->assertSame(
            ['https://example.com/second'],
            array_values($manager->get_redirect_uris($clientid)),
        );

        // Removing a URI which is not registered is a no-op.
        $manager->remove_redirect_uri($clientid, 'https://example.com/never-registered');
        $this->assertCount(1, $manager->get_redirect_uris($clientid));
    }

    /**
     * Test that registering the same redirect URI twice stores it only once.
     *
     * @return void
     */
    public function test_add_redirect_uri_is_idempotent(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        $manager->add_redirect_uri((int) $record->id, 'https://example.com/callback');
        $manager->add_redirect_uri((int) $record->id, 'https://example.com/callback');

        $this->assertCount(1, $manager->get_redirect_uris((int) $record->id));
    }

    /**
     * Test that redirect URIs are accepted or rejected according to the scheme rules.
     *
     * @param string $uri The redirect URI under test.
     * @param bool $expectedvalid Whether the URI should be accepted.
     * @return void
     */
    #[DataProvider('redirect_uri_format_provider')]
    public function test_add_redirect_uri_validates_the_uri(string $uri, bool $expectedvalid): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager);

        if (!$expectedvalid) {
            try {
                $manager->add_redirect_uri((int) $record->id, $uri);
                $this->fail('The redirect URI should have been rejected.');
            } catch (moodle_exception $e) {
                $this->assertSame('oauth2clientinvalidredirecturi', $e->errorcode);
            }

            $this->assertEmpty($manager->get_redirect_uris((int) $record->id));
            return;
        }

        $manager->add_redirect_uri((int) $record->id, $uri);

        $this->assertSame([$uri], array_values($manager->get_redirect_uris((int) $record->id)));
    }

    /**
     * Data provider for redirect URI format validation.
     *
     * @return array The datasets.
     */
    public static function redirect_uri_format_provider(): array {
        return [
            'https' => ['https://example.com/callback', true],
            'https with port and query' => ['https://example.com:8443/callback?state=1', true],
            'http on localhost' => ['http://localhost/callback', true],
            'http on localhost with port' => ['http://localhost:8080/callback', true],
            'http on IPv4 loopback' => ['http://127.0.0.1:8080/callback', true],
            'http on the wider IPv4 loopback range' => ['http://127.1.2.3/callback', true],
            'http on IPv6 loopback' => ['http://[::1]:8080/callback', true],
            'http on a public host' => ['http://example.com/callback', false],
            'http on a host merely containing localhost' => ['http://localhost.example.com/callback', false],
            'http on a host merely starting with 127' => ['http://127.example.com/callback', false],
            'ftp' => ['ftp://example.com/callback', false],
            'javascript' => ['javascript:alert(1)', false],
            'relative path' => ['/callback', false],
            'scheme with no host' => ['https:///callback', false],
            'https carrying a fragment' => ['https://example.com/callback#token', false],
            'empty string' => ['', false],
        ];
    }

    /**
     * Test that a redirect URI must match a registered URI exactly.
     *
     * @param string $candidate The URI presented for validation.
     * @param bool $expectedmatch Whether it should be accepted.
     * @return void
     */
    #[DataProvider('redirect_uri_match_provider')]
    public function test_validate_redirect_uri_requires_an_exact_match(string $candidate, bool $expectedmatch): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $record = $this->create_fixture_client($manager, ['https://example.com/callback']);

        $this->assertSame(
            $expectedmatch,
            $manager->validate_redirect_uri($record->clientidentifier, $candidate),
        );
    }

    /**
     * Data provider for exact redirect URI matching.
     *
     * @return array The datasets.
     */
    public static function redirect_uri_match_provider(): array {
        return [
            'exact match' => ['https://example.com/callback', true],
            'trailing slash' => ['https://example.com/callback/', false],
            'prefix of the registered URI' => ['https://example.com/', false],
            'registered URI as a prefix' => ['https://example.com/callback/evil', false],
            'added query string' => ['https://example.com/callback?code=1', false],
            'different case in the path' => ['https://example.com/Callback', false],
            'different host' => ['https://evil.example.com/callback', false],
            'wildcard' => ['https://example.com/*', false],
            'unregistered' => ['https://example.com/other', false],
        ];
    }

    /**
     * Test that redirect URI validation is scoped to a single client.
     *
     * @return void
     */
    public function test_validate_redirect_uri_is_scoped_to_the_client(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();
        $first = $this->create_fixture_client($manager, ['https://example.com/first']);
        $second = $this->create_fixture_client($manager, ['https://example.com/second']);

        $this->assertTrue($manager->validate_redirect_uri($first->clientidentifier, 'https://example.com/first'));
        $this->assertFalse($manager->validate_redirect_uri($first->clientidentifier, 'https://example.com/second'));
        $this->assertTrue($manager->validate_redirect_uri($second->clientidentifier, 'https://example.com/second'));
        $this->assertFalse($manager->validate_redirect_uri($second->clientidentifier, 'https://example.com/first'));
    }

    /**
     * Test that an unknown client has no registered redirect URIs.
     *
     * @return void
     */
    public function test_validate_redirect_uri_for_unknown_client(): void {
        $this->resetAfterTest();

        $manager = $this->get_manager();

        $this->assertFalse($manager->validate_redirect_uri('no-such-client', 'https://example.com/callback'));
    }
}
