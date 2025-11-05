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

/**
 * Provides an abstraction to store the pair of users to merge from the web administration.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

use stdClass;

/**
 * Provides an abstraction to store the pair of users to merge from the web administration.
 *
 * This implementation uses a backed $SESSION attribute to keep the pair of selected users.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class selected_users_to_merge {
    /** @var selected_users_to_merge singleton instance to use during the web session. */
    private static self $instance;
    /** @var string name of the SESSION attribute where to store the users selection. */
    private static string $attributename = 'toolmergeusers';
    /** @var string name of the attribute to store the user selected to remove. */
    private static string $fromusername = 'fromuser';
    /** @var string name of the attribute to store the user selected to keep. */
    private static string $tousername = 'touser';

    /**
     * Provides a singleton instance of the users selection.
     *
     * @return self
     */
    public static function instance(): self {
        if (!isset(self::$instance) || defined('PHPUNIT_TEST')) {
            // During testing, $SESSION is reset, but not this singleton.
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Builds the users selection instance.
     */
    private function __construct() {
        $this->init_attribute();
    }

    /**
     * Informs the current selected user to be removed (fromid).
     *
     * @return mixed|null null when not selected any yet; the user record otherwise.
     */
    public function from_user(): ?object {
        global $SESSION;
        return $SESSION->{self::$attributename}->{self::$fromusername};
    }

    /**
     * Tells whether the user to remove (fromid) is already set.
     *
     * @return bool
     */
    public function from_user_is_set(): bool {
        global $SESSION;
        return !empty($SESSION->{self::$attributename}->{self::$fromusername});
    }

    /**
     * Informs the current selected user to be kept (toid).
     *
     * @return mixed|null null when not selected any yet; the user record otherwise.
     */
    public function to_user(): ?object {
        global $SESSION;
        return $SESSION->{self::$attributename}->{self::$tousername};
    }

    /**
     * Tells whether the user to keep (toid) is already set.
     *
     * @return bool
     */
    public function to_user_is_set(): bool {
        global $SESSION;
        return !empty($SESSION->{self::$attributename}->{self::$tousername});
    }

    /**
     * Informs whether both users to merge are already selected.
     *
     * @return bool true only when both users are set already; false otherwise.
     */
    public function both_are_selected(): bool {
        global $SESSION;
        return !empty($SESSION->{self::$attributename}->{self::$fromusername}) &&
            !empty($SESSION->{self::$attributename}->{self::$tousername});
    }

    /**
     * Sets the user to use to remove (fromid) only if it is passed a non-null value.
     *
     * It is meant to help client-side to make things easier.
     *
     * @param object|null $fromuser only not null values are set.
     * @return void
     */
    public function set_from_user(?object $fromuser): void {
        global $SESSION;
        if (!empty($fromuser)) {
            $SESSION->{self::$attributename}->{self::$fromusername} = $fromuser;
        }
    }

    /**
     * Sets the user to use to keep (toid) only if it is passed a non-null value.
     *
     * It is meant to help client-side to make things easier.
     *
     * @param object|null $touser only not null values are set.
     * @return void
     */
    public function set_to_user(?object $touser): void {
        global $SESSION;
        if (!empty($touser)) {
            $SESSION->{self::$attributename}->{self::$tousername} = $touser;
        }
    }

    /**
     * Forces to clear the selected user to keep.
     *
     * @return void
     */
    public function unset_to_user(): void {
        global $SESSION;
        $SESSION->{self::$attributename}->{self::$tousername} = null;
    }

    /**
     * Forces to clear the selected user to remove.
     *
     * @return void
     */
    public function unset_from_user(): void {
        global $SESSION;
        $SESSION->{self::$attributename}->{self::$fromusername} = null;
    }

    /**
     * Unsets any user selected to merge.
     *
     * @return void
     */
    public function clear_users_selection(): void {
        $this->init_attribute(true);
    }

    /**
     * Initializes the $SESSION object with the attribute to use by this plugin.
     *
     * @param bool $force default to false, for not to override the attribute if it exists;
     * when true, it will override any existing value and will remove any selected user.
     *
     * @return void
     */
    private function init_attribute(bool $force = false): void {
        global $SESSION;
        if (isset($SESSION->{self::$attributename}) && !$force) {
            return;
        }
        $SESSION->{self::$attributename} = (object)[self::$fromusername => null, self::$tousername => null];
    }
}
