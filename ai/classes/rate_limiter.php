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

namespace core_ai;

use Psr\Clock\ClockInterface;

/**
 * Rate limiting functionality that can be used by AI providers.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rate_limiter {
    /** @var int TIME_WINDOW Time window in seconds (1 hour). */
    public const TIME_WINDOW = HOURSECS;

    /** @var null|rate_limiter Singleton instance of the rate limiter. */
    private static ?rate_limiter $instance = null;

    /** @var \cache_application Cache instance for rate limiter. */
    private \cache_application $cache;

    /**
     * Constructor.
     *
     * @param ClockInterface $clock Clock instance for time management.
     */
    public function __construct(
        /** @var ClockInterface Clock instance for time management. */
        private ClockInterface $clock,
    ) {
        $this->cache = \cache::make('core', 'ai_ratelimit');
    }

    /**
     * Check global rate limit for a component.
     *
     * @param string $component Name of the component.
     * @param int $ratelimit Number of requests per time window.
     * @return bool True if request is allowed, false otherwise.
     */
    public function check_global_rate_limit(string $component, int $ratelimit): bool {
        $currenttime = $this->clock->now()->getTimestamp();
        return $this->check_limit("global_{$component}", $ratelimit, $currenttime);
    }

    /**
     * Check user rate limit for a component.
     *
     * @param string $component Name of the component.
     * @param int $ratelimit Number of requests per time window.
     * @param int $userid User ID for user-specific rate limit.
     * @return bool True if request is allowed, false otherwise.
     */
    public function check_user_rate_limit(string $component, int $ratelimit, int $userid): bool {
        $currenttime = $this->clock->now()->getTimestamp();

        // Check and update user limit.
        return $this->check_limit("user_{$component}_{$userid}", $ratelimit, $currenttime);
    }

    /**
     * Helper function to check limit in cache.
     *
     * @param string $key Cache key.
     * @param int $ratelimit Number of requests per time window.
     * @param int $currenttime Current timestamp.
     * @return bool True if request is allowed, false otherwise.
     */
    private function check_limit(string $key, int $ratelimit, int $currenttime): bool {
        $ratedata = $this->cache->get($key);

        if ($ratedata === false) {
            // No data found, initialize rate data.
            $ratedata = ['count' => 0, 'start_time' => $currenttime];
        }

        // Remove expired rate data.
        if ($currenttime - $ratedata['start_time'] > self::TIME_WINDOW) {
            $ratedata['count'] = 0;
            $ratedata['start_time'] = $currenttime;
        }

        // Check rate limit.
        if ($ratedata['count'] < $ratelimit) {
            $ratedata['count']++;
            $this->cache->set($key, $ratedata);
            return true;
        }

        // Rate limit exceeded.
        return false;
    }
}
