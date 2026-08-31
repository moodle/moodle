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
use core\clock;
use core\di;
use core\exception\moodle_exception;
use core\oauth2\server\repository\scope_repository;

/**
 * Mints personal access tokens, holding the policy the repository layer leaves open.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token_manager {
    // Fixed so a secret scanner can recognise a leaked token, though not vendor-specific: a
    // scanner cannot tell a Moodle token from another product's by this prefix alone.
    /** @var string Prefix identifying a personal access token. */
    public const string TOKEN_PREFIX = 'pat_';

    // A ceiling, not a default: an unbounded token is one nobody ever revokes.
    /** @var int The longest a token may live, in seconds. */
    public const int MAX_LIFETIME = YEARSECS;

    // A short list makes a short-lived token the easy choice; someone asked to pick a date
    // reaches for the furthest one allowed. The longest offered is MAX_LIFETIME itself.
    /** @var string[] The expiry periods offered, as days mapped to the string naming each. */
    public const array EXPIRY_PRESETS = [
        7 => 'pat_expiry7days',
        30 => 'pat_expiry1month',
        60 => 'pat_expiry2months',
        90 => 'pat_expiry3months',
        365 => 'pat_expiry1year',
    ];

    /** @var int The expiry period chosen by default, in days. */
    public const int DEFAULT_EXPIRY_PRESET = 30;

    // Deliberately shorter than the shortest period a token can be given, so the flag always
    // means "sooner than you chose" rather than sitting there from the moment it is created.
    /** @var int How close to expiry a token is flagged as expiring soon, in days. */
    public const int EXPIRY_IMMINENT_DAYS = 3;

    /** @var int The length of the generated secret, in characters. */
    protected const int SECRET_LENGTH = 32;

    /**
     * Constructor.
     *
     * @param api_token_repository $repository The repository which persists tokens.
     * @param clock $clock The clock used to judge expiry dates.
     * @param scope_repository $scoperepository The repository used to resolve available scopes.
     */
    public function __construct(
        /** @var api_token_repository The repository which persists tokens. */
        protected readonly api_token_repository $repository,
        /** @var clock The clock used to judge expiry dates. */
        protected readonly clock $clock,
        /** @var scope_repository The repository used to resolve available scopes. */
        protected readonly scope_repository $scoperepository,
    ) {
    }

    /**
     * Mint a token for a user and return the secret to show them, once.
     *
     * @param string $name The human-readable name of the token.
     * @param int $userid The user who will own the token.
     * @param string[] $scopes The scope identifiers to grant. At least one is required.
     * @param string|null $description An optional human-readable description.
     * @param int $expirytime The timestamp at which the token lapses.
     * @return string The token to present to the user, as {@see self::TOKEN_PREFIX}<id>_<secret>.
     * @throws moodle_exception If the expiry is out of range, or a scope is missing or unknown.
     */
    public function create_token(
        string $name,
        int $userid,
        array $scopes,
        ?string $description,
        int $expirytime,
    ): string {
        $this->validate_expiry($expirytime);
        $this->validate_scopes($scopes);

        $secret = random_string(self::SECRET_LENGTH);

        $token = $this->repository->create_token(
            $name,
            $secret,
            $userid,
            implode(' ', $scopes),
            $description,
            $expirytime,
        );

        // The id is what makes the secret findable again: the stored hash is salted per row, so it
        // cannot be searched for, and validation needs the row before it can verify the secret.
        // This is also the only time the secret exists readable, so the caller must not keep it.
        return self::TOKEN_PREFIX . $token->get_id() . '_' . $secret;
    }

    /**
     * Get every scope a token may be granted, keyed by identifier.
     *
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface[]
     */
    public function get_available_scopes(): array {
        return $this->scoperepository->get_all_scopes();
    }

    /**
     * Whether a token is close enough to expiry to be worth flagging.
     *
     * @param int|null $expirytime The token's expiry, or null if it never expires.
     * @return bool
     */
    public static function is_expiring_soon(?int $expirytime): bool {
        if ($expirytime === null) {
            return false;
        }

        $now = self::now();

        // A token which has already lapsed is not "expiring soon": its status says so already.
        return $expirytime > $now && $expirytime <= $now + (self::EXPIRY_IMMINENT_DAYS * DAYSECS);
    }

    /**
     * The current time, for the static judgements above.
     *
     * @return int
     */
    protected static function now(): int {
        // The same clock the instance methods read, so freezing it reaches these too.
        return di::get(clock::class)->time();
    }

    /**
     * Format a token's expiry for display, the same way on every screen and notification.
     *
     * @param int $timestamp The timestamp to format.
     * @return string
     */
    public static function format_datetime(int $timestamp): string {
        // An exact moment rather than a whole day, so the time is shown too. Not userdate()'s
        // default, which prefixes the weekday: it is three words of column width that nobody
        // reads off a token, and these dates sit in a table beside four other columns.
        return userdate($timestamp, get_string('strftimedatetime', 'langconfig'));
    }

    /**
     * Get the display name of every available scope, keyed by identifier.
     *
     * @return string[]
     */
    public function get_scope_names(): array {
        $names = [];

        foreach ($this->get_available_scopes() as $identifier => $scope) {
            $names[$identifier] = $scope::get_summary();
        }

        return $names;
    }

    /**
     * The latest expiry a token minted right now may carry.
     *
     * @return int
     */
    public function get_maximum_expiry(): int {
        return $this->get_current_time() + self::MAX_LIFETIME;
    }

    /**
     * Get the expiry timestamp each preset resolves to, keyed by its period in days.
     *
     * @return int[]
     */
    public function get_expiry_presets(): array {
        $now = $this->get_current_time();
        $presets = [];

        foreach (array_keys(self::EXPIRY_PRESETS) as $days) {
            $presets[$days] = $now + ($days * DAYSECS);
        }

        return $presets;
    }

    /**
     * Get the label for each offered period, keyed by its number of days.
     *
     * @return string[]
     */
    public function get_expiry_choices(): array {
        $choices = [];

        foreach (self::EXPIRY_PRESETS as $days => $identifier) {
            $choices[$days] = get_string($identifier);
        }

        return $choices;
    }

    /**
     * The current time according to the clock this manager judges expiry dates against.
     *
     * @return int
     */
    public function get_current_time(): int {
        // Callers validating a date must use this rather than time(), or their idea of "now"
        // can differ from the one the token is actually checked against.
        return $this->clock->time();
    }

    /**
     * Check that an expiry timestamp is in the future and within the maximum lifetime.
     *
     * @param int $expirytime The timestamp to check.
     * @return void
     * @throws moodle_exception If the expiry is in the past or too far ahead.
     */
    protected function validate_expiry(int $expirytime): void {
        if ($expirytime <= $this->get_current_time()) {
            throw new moodle_exception('apitokenexpiryinpast', 'error');
        }

        if ($expirytime > $this->get_maximum_expiry()) {
            throw new moodle_exception('apitokenexpirytoofar', 'error');
        }
    }

    /**
     * Check that at least one scope was chosen and that every one of them exists.
     *
     * @param string[] $scopes The scope identifiers to check.
     * @return void
     * @throws moodle_exception If no scope was given, or one is not a known scope.
     */
    protected function validate_scopes(array $scopes): void {
        if (empty($scopes)) {
            throw new moodle_exception('apitokennoscopes', 'error');
        }

        $available = $this->get_available_scopes();

        foreach ($scopes as $scope) {
            if (!isset($available[$scope])) {
                throw new moodle_exception('apitokenunknownscope', 'error', '', $scope);
            }
        }
    }
}
