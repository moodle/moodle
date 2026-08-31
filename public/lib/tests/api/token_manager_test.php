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

namespace core\api;

use core\api\repository\api_token_repository;
use core\exception\moodle_exception;
use core\oauth2\server\repository\scope_repository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see token_manager}.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(token_manager::class)]
final class token_manager_test extends \advanced_testcase {
    /** @var int The fixed 'now' used by every test, so expiry boundaries are deterministic. */
    private const NOW = 1786000000;

    /**
     * Build a manager whose clock is frozen at {@see self::NOW}.
     *
     * @return token_manager
     */
    private function get_manager(): token_manager {
        return new token_manager(
            new api_token_repository(),
            $this->mock_clock_with_frozen(self::NOW),
            new scope_repository(),
        );
    }

    /**
     * The returned string carries the token id, so the secret can later be matched against a row.
     */
    public function test_create_token_returns_identifiable_secret(): void {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $token = $this->get_manager()->create_token(
            'Attendance export',
            $user->id,
            ['core_grades:grade:read'],
            'Used by the faculty office weekly attendance report.',
            self::NOW + WEEKSECS,
        );

        $this->assertMatchesRegularExpression('/^pat_\d+_[A-Za-z0-9]{32}$/', $token);

        // The id in the string must address the row that was just written.
        [, $id, $secret] = explode('_', $token, 3);
        $record = $DB->get_record('rest_api_tokens', ['id' => (int) $id], '*', MUST_EXIST);

        $this->assertEquals('Attendance export', $record->name);
        $this->assertEquals($user->id, $record->userid);
        $this->assertEquals('core_grades:grade:read', $record->scopes);
        // The repository stamps this from the injected clock, so the frozen time reaches it.
        $this->assertEquals(self::NOW, $record->timecreated);

        // Only a hash is stored, and it must verify against the secret half of the string.
        $this->assertNotEquals($secret, $record->token);
        $this->assertTrue(password_verify($secret, $record->token));
    }

    /**
     * Every generated secret differs, even for identical input.
     */
    public function test_create_token_secrets_are_unique(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $manager = $this->get_manager();

        $first = $manager->create_token('One', $user->id, ['core_admin:config:read'], null, self::NOW + DAYSECS);
        $second = $manager->create_token('Two', $user->id, ['core_admin:config:read'], null, self::NOW + DAYSECS);

        $this->assertNotEquals($first, $second);
    }

    /**
     * Multiple scopes are stored space separated, as the repository expects.
     */
    public function test_create_token_joins_scopes(): void {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $token = $this->get_manager()->create_token(
            'Multi',
            $user->id,
            ['core_grades:grade:read', 'core_course:course:read'],
            null,
            self::NOW + DAYSECS,
        );

        [, $id] = explode('_', $token, 3);

        $this->assertEquals(
            'core_grades:grade:read core_course:course:read',
            $DB->get_field('rest_api_tokens', 'scopes', ['id' => (int) $id]),
        );
    }

    /**
     * An expiry beyond the maximum lifetime is refused.
     */
    public function test_create_token_rejects_expiry_beyond_maximum(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageMatches('/expiry/i');

        $this->get_manager()->create_token(
            'Too far',
            $user->id,
            ['core_admin:config:read'],
            null,
            self::NOW + token_manager::MAX_LIFETIME + DAYSECS,
        );
    }

    /**
     * An expiry on the maximum boundary is accepted.
     */
    public function test_create_token_accepts_expiry_on_boundary(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $token = $this->get_manager()->create_token(
            'Exactly a year',
            $user->id,
            ['core_admin:config:read'],
            null,
            self::NOW + token_manager::MAX_LIFETIME,
        );

        $this->assertStringStartsWith('pat_', $token);
    }

    /**
     * An expiry in the past is refused.
     */
    public function test_create_token_rejects_expiry_in_the_past(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageMatches('/expiry/i');

        $this->get_manager()->create_token(
            'Already gone',
            $user->id,
            ['core_admin:config:read'],
            null,
            self::NOW - DAYSECS,
        );
    }

    /**
     * A token must carry at least one scope, or it could do nothing at all.
     */
    public function test_create_token_rejects_empty_scopes(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageMatches('/scope/i');

        $this->get_manager()->create_token('No scopes', $user->id, [], null, self::NOW + DAYSECS);
    }

    /**
     * Unknown scope identifiers are refused, so a token cannot be minted against a typo.
     */
    public function test_create_token_rejects_unknown_scope(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageMatches('/scope/i');

        $this->get_manager()->create_token(
            'Bogus',
            $user->id,
            ['core:not:a:real:scope'],
            null,
            self::NOW + DAYSECS,
        );
    }

    /**
     * Each preset resolves to a timestamp that many days from now.
     */
    public function test_get_expiry_presets(): void {
        $presets = $this->get_manager()->get_expiry_presets();

        $this->assertSame(array_keys(token_manager::EXPIRY_PRESETS), array_keys($presets));

        foreach ($presets as $days => $timestamp) {
            // Exactly that many days from now, to the second, so the expiry shown is the moment
            // the token stops working.
            $this->assertEquals(self::NOW + ($days * DAYSECS), $timestamp);
        }
    }

    /**
     * No preset may offer an expiry the manager would then refuse.
     */
    public function test_expiry_presets_are_within_the_maximum_lifetime(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $manager = $this->get_manager();

        foreach ($manager->get_expiry_presets() as $days => $timestamp) {
            $token = $manager->create_token("Preset {$days}", $user->id, ['core_admin:config:read'], null, $timestamp);
            $this->assertStringStartsWith('pat_', $token);
        }
    }

    /**
     * Every offered period has a label, and the default is one of them.
     */
    public function test_default_expiry_preset_is_offered(): void {
        $this->assertArrayHasKey(token_manager::DEFAULT_EXPIRY_PRESET, token_manager::EXPIRY_PRESETS);

        $choices = $this->get_manager()->get_expiry_choices();
        $this->assertSame(array_keys(token_manager::EXPIRY_PRESETS), array_keys($choices));
        foreach ($choices as $label) {
            $this->assertNotEmpty($label);
        }
    }

    /**
     * A token is flagged as expiring soon only inside the window, and never once it has lapsed.
     *
     * @param int|null $offset Seconds from now until expiry, or null for a token that never expires.
     * @param bool $expected Whether it should be flagged.
     */
    #[DataProvider('expiring_soon_provider')]
    public function test_is_expiring_soon(?int $offset, bool $expected): void {
        // Frozen, so the window is measured against the same clock a token is minted by.
        $this->mock_clock_with_frozen(self::NOW);
        $expirytime = $offset === null ? null : self::NOW + $offset;

        $this->assertSame($expected, token_manager::is_expiring_soon($expirytime));
    }

    /**
     * Cases for {@see test_is_expiring_soon}.
     *
     * @return array
     */
    public static function expiring_soon_provider(): array {
        $window = token_manager::EXPIRY_IMMINENT_DAYS * DAYSECS;

        return [
            'never expires' => [null, false],
            'already lapsed' => [-DAYSECS, false],
            'lapsing this minute' => [-1, false],
            'due in an hour' => [HOURSECS, true],
            'just inside the window' => [$window - MINSECS, true],
            'just outside the window' => [$window + MINSECS, false],
            'far away' => [30 * DAYSECS, false],
        ];
    }

    /**
     * Every scope the site declares is offered, keyed by the identifier a token stores.
     */
    public function test_get_available_scopes(): void {
        $scopes = $this->get_manager()->get_available_scopes();

        // Compared against the repository the manager delegates to, rather than a list written
        // here: a hard coded expectation would only assert that this test was updated too.
        $this->assertSame(array_keys((new scope_repository())->get_all_scopes()), array_keys($scopes));
        $this->assertNotEmpty($scopes);

        foreach ($scopes as $identifier => $scope) {
            $this->assertMatchesRegularExpression('/^[a-z0-9_]+(:[a-z0-9_]+)+$/', $identifier);
            $this->assertInstanceOf(\core\router\scope\abstract_scope::class, $scope);
        }
    }

    /**
     * Each scope is named by its own summary, under the identifier a token stores.
     */
    public function test_get_scope_names(): void {
        $manager = $this->get_manager();
        $names = $manager->get_scope_names();
        $scopes = $manager->get_available_scopes();

        $this->assertSame(array_keys($scopes), array_keys($names));

        foreach ($names as $identifier => $name) {
            // The name a scope reports for itself, so a renamed scope cannot silently keep an
            // old label in the list.
            $this->assertSame($scopes[$identifier]::get_summary(), $name);
            $this->assertNotEmpty($name);
        }
    }

    /**
     * The furthest expiry on offer is the maximum lifetime from now, and it is accepted.
     */
    public function test_get_maximum_expiry(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $manager = $this->get_manager();

        $this->assertSame(self::NOW + token_manager::MAX_LIFETIME, $manager->get_maximum_expiry());

        // The boundary it reports must be one it would then mint a token for.
        $token = $manager->create_token('At the ceiling', $user->id, ['core_admin:config:read'], null, $manager->get_maximum_expiry());
        $this->assertStringStartsWith(token_manager::TOKEN_PREFIX, $token);
    }

    /**
     * Dates are shown without the weekday that userdate() prefixes by default.
     */
    public function test_format_datetime(): void {
        $timestamp = 1787000000;

        $formatted = token_manager::format_datetime($timestamp);

        $this->assertSame(userdate($timestamp, get_string('strftimedatetime', 'langconfig')), $formatted);
        // The weekday is what userdate() adds by default, and what this must not carry.
        $this->assertStringNotContainsString(userdate($timestamp, '%A'), $formatted);
        $this->assertNotSame(userdate($timestamp), $formatted);
    }

    /**
     * The time the manager judges expiry against is the clock it was given, not the wall clock.
     */
    public function test_get_current_time(): void {
        $this->assertSame(self::NOW, $this->get_manager()->get_current_time());
    }

    /**
     * The manager is resolvable through the DI container, which is how callers should obtain it.
     */
    public function test_resolvable_through_di(): void {
        $this->assertInstanceOf(token_manager::class, \core\di::get(token_manager::class));
    }
}
