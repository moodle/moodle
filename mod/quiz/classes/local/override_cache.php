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

namespace mod_quiz\local;

/**
 * Cache manager for quiz overrides
 *
 * Override cache data is set via its data source, {@see \mod_quiz\cache\overrides}
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override_cache {
    /** @var string invalidation event used to purge data when reset_userdata is called, {@see \cache_helper::purge_by_event()} **/
    public const INVALIDATION_USERDATARESET = 'userdatareset';

    /**
     * Create override_cache object and link to quiz
     *
     * @param int $quizid The quiz to link this cache to
     */
    public function __construct(
        /** @var int $quizid ID of quiz cache is being operated on **/
        protected readonly int $quizid
    ) {
    }

    /**
     * Returns the override cache
     *
     * @return \cache
     */
    protected function get_cache(): \cache {
        return \cache::make('mod_quiz', 'overrides');
    }

    /**
     * Returns group cache key
     *
     * @param int $groupid
     * @return string the group cache key
     */
    protected function get_group_cache_key(int $groupid): string {
        return "{$this->quizid}_g_{$groupid}";
    }

    /**
     * Returns user cache key
     *
     * @param int $userid
     * @return string the user cache key
     */
    protected function get_user_cache_key(int $userid): string {
        return "{$this->quizid}_u_{$userid}";
    }

    /**
     * Returns the override value in the cache for the given group
     *
     * @param int $groupid group to get cached override data for
     * @return ?\stdClass override value in the cache for the given group, or null if there is none.
     */
    public function get_cached_group_override(int $groupid): ?\stdClass {
        $raw = $this->get_cache()->get($this->get_group_cache_key($groupid));
        return empty($raw) || !is_object($raw) ? null : (object) $raw;
    }

    /**
     * Returns the override value in the cache for the given user
     *
     * @param int $userid user to get cached override data for
     * @return ?\stdClass the override value in the cache for the given user, or null if there is none.
     */
    public function get_cached_user_override(int $userid): ?\stdClass {
        $raw = $this->get_cache()->get($this->get_user_cache_key($userid));
        return empty($raw) || !is_object($raw) ? null : (object) $raw;
    }

    /**
     * Deletes the cached override data for a given group
     *
     * @param int $groupid group to delete data for
     */
    public function clear_for_group(int $groupid): void {
        $this->get_cache()->delete($this->get_group_cache_key($groupid));
    }

    /**
     * Deletes the cached override data for the given user
     *
     * @param int $userid user to delete data for
     */
    public function clear_for_user(int $userid): void {
        $this->get_cache()->delete($this->get_user_cache_key($userid));
    }

    /**
     * Clears the cache for the given user and/or group.
     *
     * @param ?int $userid user to delete data for, or null.
     * @param ?int $groupid group to delete data for, or null.
     */
    public function clear_for(?int $userid = null, ?int $groupid = null): void {
        if (!empty($userid)) {
            $this->clear_for_user($userid);
        }

        if (!empty($groupid)) {
            $this->clear_for_group($groupid);
        }
    }
}
