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

namespace core\authentication;

use core\di;
use core\exception\coding_exception;
use core\exception\moodle_exception;

/**
 * Tests for \core\authentication\password.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(password::class)]
final class password_test extends \advanced_testcase {
    /**
     * Get the password service via DI.
     *
     * @return password
     */
    private function get_service(): password {
        return di::get(password::class);
    }

    public function test_is_legacy_hash_with_bcrypt(): void {
        $service = $this->get_service();

        // Well-formed bcrypt hashes should be detected as legacy.
        foreach (['some', 'strings', 'to_check!'] as $pw) {
            $bcrypt = password_hash($pw, PASSWORD_BCRYPT);
            $this->assertTrue($service->is_legacy_hash($bcrypt), "bcrypt hash of '$pw' should be legacy");
        }
    }

    public function test_is_legacy_hash_with_sha512(): void {
        $service = $this->get_service();

        $sha512 = '$6$rounds=5000$somesalt$9nEA35u5h4oDrUdcVFUwXDSwIBiZtuKDHiaI/kxnBSslH4wVXeAhVsDn1UFxBxrnRJva/8dZ8IouaijJdd4cF';
        $this->assertFalse($service->is_legacy_hash($sha512));
    }

    public function test_is_legacy_hash_with_not_cached(): void {
        $service = $this->get_service();

        $this->assertFalse($service->is_legacy_hash(AUTH_PASSWORD_NOT_CACHED));
    }

    public function test_is_legacy_hash_with_empty_string(): void {
        $service = $this->get_service();

        $this->assertFalse($service->is_legacy_hash(''));
    }

    public function test_get_peppers_empty_when_not_configured(): void {
        global $CFG;
        $this->resetAfterTest();

        unset($CFG->passwordpeppers);
        $service = $this->get_service();

        $this->assertEquals([], $service->get_peppers());
    }

    public function test_get_peppers_returns_configured_peppers(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
        ];
        $service = $this->get_service();

        $peppers = $service->get_peppers();
        $this->assertCount(2, $peppers);
    }

    public function test_get_peppers_sorted_descending_by_key(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
        ];
        $service = $this->get_service();

        $peppers = $service->get_peppers();
        $keys = array_keys($peppers);
        $this->assertEquals(2, $keys[0], 'Highest key should be first');
        $this->assertEquals(1, $keys[1]);
    }

    public function test_get_peppers_allows_empty_latest_pepper(): void {
        global $CFG;
        $this->resetAfterTest();

        // An empty latest pepper allows migration off peppers.
        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
            3 => '',
        ];
        $service = $this->get_service();

        $peppers = $service->get_peppers();
        $this->assertCount(3, $peppers);
        $this->assertEquals('', reset($peppers));
    }

    public function test_get_peppers_throws_on_low_entropy(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->passwordpeppers = [
            1 => 'foo',
            2 => 'bar',
        ];
        $service = $this->get_service();

        $this->expectException(coding_exception::class);
        $service->get_peppers();
    }

    public function test_get_peppers_non_array_config(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->passwordpeppers = 'notanarray';
        $service = $this->get_service();

        $this->assertEquals([], $service->get_peppers());
    }

    public function test_exceeds_max_length_short_password(): void {
        $service = $this->get_service();

        $this->assertFalse($service->exceeds_max_length('test'));
    }

    public function test_exceeds_max_length_at_limit(): void {
        $service = $this->get_service();

        $password = str_repeat('a', MAX_PASSWORD_CHARACTERS);
        $this->assertFalse($service->exceeds_max_length($password));
    }

    public function test_exceeds_max_length_over_limit(): void {
        $service = $this->get_service();

        $password = str_repeat('a', MAX_PASSWORD_CHARACTERS + 1);
        $this->assertTrue($service->exceeds_max_length($password));
    }

    public function test_exceeds_max_length_with_pepperlength(): void {
        $service = $this->get_service();

        // Password at MAX_PASSWORD_CHARACTERS + 10 should not exceed if pepperlength is 10.
        $password = str_repeat('a', MAX_PASSWORD_CHARACTERS + 10);
        $this->assertFalse($service->exceeds_max_length($password, 10));

        // But it should exceed if pepperlength is 9.
        $this->assertTrue($service->exceeds_max_length($password, 9));
    }

    public function test_hash_produces_sha512_format(): void {
        $service = $this->get_service();

        $hash = $service->hash('testpassword');

        // SHA512 crypt format: $6$rounds=NNNN$salt$hash.
        $this->assertMatchesRegularExpression('/^\$6\$rounds=10000\$.{103}$/', $hash);
    }

    public function test_hash_fast_uses_fewer_rounds(): void {
        $service = $this->get_service();

        $fasthash = $service->hash('testpassword', fasthash: true);

        $this->assertMatchesRegularExpression('/^\$6\$rounds=5000\$.{103}$/', $fasthash);
    }

    public function test_hash_not_legacy(): void {
        $service = $this->get_service();

        $hash = $service->hash('testpassword');
        $this->assertFalse($service->is_legacy_hash($hash));
    }

    public function test_hash_different_passwords_produce_different_hashes(): void {
        $service = $this->get_service();

        $hash1 = $service->hash('password1');
        $hash2 = $service->hash('password2');

        $this->assertNotEquals($hash1, $hash2);
    }

    public function test_hash_same_password_produces_different_hashes(): void {
        $service = $this->get_service();

        // Different salts should produce different hashes.
        $hash1 = $service->hash('samepassword');
        $hash2 = $service->hash('samepassword');

        $this->assertNotEquals($hash1, $hash2);
    }

    public function test_hash_throws_on_exceeding_length(): void {
        $service = $this->get_service();

        $password = str_repeat('a', MAX_PASSWORD_CHARACTERS + 1);

        $this->expectException(moodle_exception::class);
        $service->hash($password);
    }

    public function test_hash_verifiable_with_crypt(): void {
        $service = $this->get_service();

        $password = 'testpassword';
        $hash = $service->hash($password);

        // The hash should be verifiable with password_verify.
        $this->assertTrue(password_verify($password, $hash));
    }

    public function test_hash_international_characters(): void {
        $service = $this->get_service();

        $password = 'ĩńťėŕňăţĩōŋāĹ';
        $hash = $service->hash($password);

        $this->assertMatchesRegularExpression('/^\$6\$rounds=10000\$/', $hash);
        $this->assertTrue(password_verify($password, $hash));
    }

    public function test_hash_complex_characters(): void {
        $service = $this->get_service();

        // phpcs:ignore moodle.Strings.ForbiddenStrings.Found
        $password = 'C0mP1eX_&}<?@*&%` |"';
        $hash = $service->hash($password);

        $this->assertTrue(password_verify($password, $hash));
    }

    public function test_validate_correct_password_sha512(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $password = 'correctpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);

        $this->assertTrue($service->validate($user, $password));
    }

    public function test_validate_incorrect_password(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => 'realpassword']);

        $this->assertFalse($service->validate($user, 'wrongpassword'));
    }

    public function test_validate_rejects_too_long_password(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => 'test']);

        $longpassword = str_repeat('a', MAX_PASSWORD_CHARACTERS + 1);
        $this->assertFalse($service->validate($user, $longpassword));
    }

    public function test_validate_rejects_not_cached(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        $user->password = AUTH_PASSWORD_NOT_CACHED;

        $this->assertFalse($service->validate($user, 'anypassword'));
    }

    public function test_validate_upgrades_legacy_bcrypt(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();
        $password = 'testpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        // Set a bcrypt hash directly.
        $bcrypthash = password_hash($password, PASSWORD_BCRYPT);
        $DB->set_field('user', 'password', $bcrypthash, ['id' => $user->id]);
        $user->password = $bcrypthash;

        $this->assertTrue($service->is_legacy_hash($user->password));

        // Validation should succeed and upgrade the hash.
        $this->assertTrue($service->validate($user, $password));

        // After validation, the password should have been upgraded to SHA512.
        $updatedpassword = $DB->get_field('user', 'password', ['id' => $user->id]);
        $this->assertFalse($service->is_legacy_hash($updatedpassword));
    }

    public function test_validate_with_pepper(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
        ];
        $service = $this->get_service();

        $password = 'pepperedpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);

        $this->assertTrue($service->validate($user, $password));
        $this->assertFalse($service->validate($user, 'wrongpassword'));
    }

    public function test_validate_fails_after_pepper_removed(): void {
        global $CFG;
        $this->resetAfterTest();

        // Set a pepper and create a user with a peppered password.
        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
        ];
        $service = $this->get_service();
        $password = 'testpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);

        $this->assertTrue($service->validate($user, $password));

        // Now remove the peppers — password should no longer validate.
        unset($CFG->passwordpeppers);
        $this->assertFalse($service->validate($user, $password));
    }

    public function test_validate_upgrades_to_latest_pepper(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Start with one pepper.
        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
        ];
        $service = $this->get_service();
        $password = 'testpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);
        $oldhash = $DB->get_field('user', 'password', ['id' => $user->id]);

        // Add a new pepper.
        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
        ];

        // Validation should succeed and update to the new pepper.
        $this->assertTrue($service->validate($user, $password));

        $newhash = $DB->get_field('user', 'password', ['id' => $user->id]);
        $this->assertNotEquals($oldhash, $newhash);
    }

    public function test_validate_bcrypt_hashes(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();

        $bcrypthashes = [
            'pw' => '$2y$10$LOSDi5eaQJhutSRun.OVJ.ZSxQZabCMay7TO1KmzMkDMPvU40zGXK',
            'abc' => '$2y$10$VWTOhVdsBbWwtdWNDRHSpewjd3aXBQlBQf5rBY/hVhw8hciarFhXa',
            // phpcs:ignore moodle.Strings.ForbiddenStrings.Found
            'C0mP1eX_&}<?@*&%` |\"' => '$2y$10$3PJf.q.9ywNJlsInPbqc8.IFeSsvXrGvQLKRFBIhVu1h1I3vpIry6',
            'ĩńťėŕňăţĩōŋāĹ' => '$2y$10$3A2Y8WpfRAnP3czJiSv6N.6Xp0T8hW3QZz2hUCYhzyWr1kGP1yUve',
        ];

        foreach ($bcrypthashes as $password => $hash) {
            $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);
            $DB->set_field('user', 'password', $hash, ['id' => $user->id]);
            $user->password = $hash;

            $this->assertTrue($service->validate($user, $password));
            $this->assertFalse($service->validate($user, 'badpw'));
        }
    }

    public function test_validate_sha512_hashes(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();

        $sha512hashes = [
            'pw2' =>
                '$6$rounds=10000$0rDIzh/4.MMf9Dm8$Zrj6Ulc1JFj0RFXwMJFsngRSNGlqkPlV1wwRVv7wBLrMeQeMZrwsBO62zy63D' .
                '//6R5sNGVYQwPB0K8jPCScxB/',
            'abc2' =>
                '$6$rounds=10000$t0L6PklgpijV4tMB$1vpCRKCImsVqTPMiZTi6zLGbs.hpAU8I2BhD/IFliBnHJkFZCWEBfTCq6pEzo' .
                '0Q8nXsryrgeZ.qngcW.eifuW.',
            // phpcs:ignore moodle.Strings.ForbiddenStrings.Found
            'C0mP1eX_&}<?@*&%` |\"2' =>
                '$6$rounds=10000$3TAyVAXN0zmFZ4il$KF8YzduX6Gu0C2xHsY83zoqQ/rLVsb9mLe417wDObo9tO00qeUC68/y2tMq4F' .
                'L2ixnMPH3OMwzGYo8VJrm8Eq1',
            'ĩńťėŕňăţĩōŋāĹ2' =>
                '$6$rounds=10000$SHR/6ctTkfXOy5NP$YPv42hjDjohVWD3B0boyEYTnLcBXBKO933ijHmkPXNL7BpqAcbYMLfTl9rjsP' .
                'mCt.1GZvEJZ8ikkCPYBC5Sdp.',
        ];

        foreach ($sha512hashes as $password => $hash) {
            $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);
            $DB->set_field('user', 'password', $hash, ['id' => $user->id]);
            $user->password = $hash;

            $this->assertTrue($service->validate($user, $password));
            $this->assertFalse($service->validate($user, 'badpw'));
        }
    }

    public function test_update_sets_password_in_db(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();
        $password = 'newpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        $service->update($user, $password);

        // The database should have the updated hash.
        $dbhash = $DB->get_field('user', 'password', ['id' => $user->id]);
        $this->assertEquals($user->password, $dbhash);
        $this->assertTrue($service->validate($user, $password));
    }

    public function test_update_fires_event(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        $sink = $this->redirectEvents();
        $service->update($user, 'newpassword');
        $events = $sink->get_events();
        $sink->close();

        $event = array_pop($events);
        $this->assertInstanceOf(\core\event\user_password_updated::class, $event);
        $this->assertEquals($user->id, $event->relateduserid);
    }

    public function test_update_no_event_when_unchanged(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $password = 'samepassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual', 'password' => $password]);

        // Updating to the same password should not fire an event (hash already matches).
        $sink = $this->redirectEvents();
        $service->update($user, $password);
        $events = $sink->get_events();
        $sink->close();

        // Filter for password update events only.
        $pwevents = array_filter($events, fn($e) => $e instanceof \core\event\user_password_updated);
        $this->assertEmpty($pwevents);
    }

    public function test_update_prevents_local_passwords(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();
        // The 'db' auth prevents local passwords.
        $user = $this->getDataGenerator()->create_user(['auth' => 'db']);

        $service->update($user, 'anypassword');

        $this->assertEquals(AUTH_PASSWORD_NOT_CACHED, $user->password);
        $this->assertEquals(AUTH_PASSWORD_NOT_CACHED, $DB->get_field('user', 'password', ['id' => $user->id]));
    }

    public function test_update_no_event_when_already_not_cached(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'db']);

        // First update to NOT_CACHED.
        $service->update($user, 'something');
        $this->assertEquals(AUTH_PASSWORD_NOT_CACHED, $user->password);

        // Second update should not fire an event since it's already NOT_CACHED.
        $sink = $this->redirectEvents();
        $service->update($user, 'somethingelse');
        $events = $sink->get_events();
        $sink->close();

        $pwevents = array_filter($events, fn($e) => $e instanceof \core\event\user_password_updated);
        $this->assertEmpty($pwevents);
    }

    public function test_update_upgrades_bcrypt_to_sha512(): void {
        global $DB;
        $this->resetAfterTest();

        $service = $this->get_service();
        $password = 'testpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        // Set a bcrypt hash.
        $bcrypthash = password_hash($password, PASSWORD_BCRYPT);
        $DB->set_field('user', 'password', $bcrypthash, ['id' => $user->id]);
        $user->password = $bcrypthash;

        $this->assertTrue($service->is_legacy_hash($user->password));

        $service->update($user, $password);

        $this->assertFalse($service->is_legacy_hash($user->password));
    }

    public function test_update_with_pepper(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->passwordpeppers = [
            1 => '#GV]NLie|x$H9[$rW%94bXZvJHa%z',
            2 => '#GV]NLie|x$H9[$rW%94bXZvJHa%$',
        ];
        $service = $this->get_service();
        $password = 'pepperedpassword';
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        $service->update($user, $password);

        // The hash should verify with the peppered password.
        $latestpepper = '#GV]NLie|x$H9[$rW%94bXZvJHa%$';
        $this->assertTrue(password_verify($password . $latestpepper, $user->password));
    }

    public function test_update_deletes_ws_tokens_when_configured(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $CFG->passwordchangetokendeletion = 1;
        $service = $this->get_service();

        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        // Create a token for the user.
        $tokenrecord = new \stdClass();
        $tokenrecord->token = md5(uniqid(rand(), true));
        $tokenrecord->userid = $user->id;
        $tokenrecord->tokentype = EXTERNAL_TOKEN_PERMANENT;
        $tokenrecord->externalserviceid = 1;
        $tokenrecord->contextid = \context_system::instance()->id;
        $tokenrecord->creatorid = $user->id;
        $tokenrecord->timecreated = time();
        $DB->insert_record('external_tokens', $tokenrecord);

        $this->assertTrue($DB->record_exists('external_tokens', ['userid' => $user->id]));

        $service->update($user, 'differentpassword');

        $this->assertFalse($DB->record_exists('external_tokens', ['userid' => $user->id]));
    }

    public function test_update_missing_auth_field_triggers_debugging(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        unset($user->auth);

        $service->update($user, 'newpassword');

        $this->assertDebuggingCalled(
            'User record in \\core\\authentication\\password::update() must include field auth',
            DEBUG_DEVELOPER,
        );
        $this->assertEquals('manual', $user->auth);
    }

    public function test_update_returns_true(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        $this->assertTrue($service->update($user, 'anypassword'));
    }

    public function test_update_with_null_password(): void {
        $this->resetAfterTest();

        $service = $this->get_service();
        // OAuth2 auth prevents local passwords.
        $user = $this->getDataGenerator()->create_user(['auth' => 'oauth2']);

        // Null password should result in AUTH_PASSWORD_NOT_CACHED.
        $service->update($user, null);
        $this->assertEquals(AUTH_PASSWORD_NOT_CACHED, $user->password);
    }

    public function test_di_resolution(): void {
        $instance = di::get(password::class);
        $this->assertInstanceOf(password::class, $instance);
    }
}
