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
 * RedisCluster rate limiter
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_rediscluster;

defined('MOODLE_INTERNAL') || die();

/**
 * RedisCluster ratelimiter
 *
 * Provides methods for tracking usage rate of, and limiting access to,
 * resources.
 */
class ratelimiter extends sharedconn {

    /**
     * Log a response time for this resource.
     *
     * @param string $key The key of the resource we're limiting.
     * @param int $value How long did this request take.
     * @return void
     */
    public function track($key, $value) {
        if (!$this->isready) {
            return;
        }
        $key = $this->parse_key($key);
        $now = microtime(true);
        static::$connection->command('zadd', $key, $now, "{$now}:{$value}");
    }

    /**
     * Check to see if the selected resource is exceeding its limit in the
     * current window.
     *
     * @param string $key
     * @param int $window How many seconds long is our sliding window.
     * @param int $limit
     * @param int $minimum The minimum number of samples needed before limiting.
     * @return bool
     */
    public function limited_average($key, $window, $limit, $minimum = 1) {
        if (!$this->isready) {
            return false;
        }
        $key = $this->parse_key($key);
        static::$connection->command('multi');
        static::$connection->command('zremrangebyscore', $key, 0, microtime(true) - $window);
        static::$connection->command('zrange', $key, 0, -1);
        $result = static::$connection->command('exec');
        $count = count($result[1]); // Count from zrange.

        if ($count < $minimum) {
            return false;
        }

        array_walk($result[1], function(&$value) {
            $data = explode(':', $value, 2);
            $value = $data[1];
        });

        return array_sum($result[1]) / $count > $limit;
    }

    /**
     * Check if we've exceeded a number of requests per window.
     *
     * @param string $key
     * @param int $window How many seconds long is our sliding window.
     * @param int $limit
     * @return bool
     */
    public function limited_count($key, $window, $limit) {
        if (!$this->isready) {
            return false;
        }
        $key = $this->parse_key($key);
        static::$connection->command('multi');
        static::$connection->command('zremrangebyscore', $key, 0, microtime(true) - $window);
        static::$connection->command('zcount', $key, '-inf', '+inf');
        $result = static::$connection->command('exec');
        $count = $result[1];

        if ($count == 0) {
            return false;
        }

        return $count >= $limit;
    }
}