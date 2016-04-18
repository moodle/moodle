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
 * Tests redis session handler
 *
 * @package    core
 * @copyright  2016 Nicholas Hoobin (nicholashoobin@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Tests redis session handler class
 *
 * @package    core
 * @copyright  2016 Nicholas Hoobin (nicholashoobin@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redis_session_testcase extends advanced_testcase {

    /**
     * Test test_redis_connection_parser()
     * @param string $constring The connection string, 'savepath'.
     * @param array $expected An array of expected results.
     * @param int $count Number of valid connections.
     * @dataProvider connectionprovider
     */
    public function test_redis_connection_parser($constring, $expected, $debug) {
        $handler = new \core\session\redis;

        $servers = $handler->connection_string_to_redis_servers($constring);

        if ($debug == true) {
            $this->assertDebuggingCalled();
        }

        $this->assertEquals($expected, $servers);
    }

    /**
     * Provides data for test_redis_connection_parser().
     * @return array array of connection results
     */
    public function connectionprovider() {
        return array(
            array(
                "tcp://127.0.0.1, unix:///var/run/redis/redis.sock",
                array(
                    array(
                        'database' => 0,
                        'timeout' => 86400,
                        'port' => 6379,
                        'scheme' => 'tcp',
                        'prefix' => 'PHPREDIS_SESSION:',
                        'host' => '127.0.0.1'
                    ),
                    array(
                        'database' => 0,
                        'timeout' => 86400,
                        'scheme' => 'unix',
                        'prefix' => 'PHPREDIS_SESSION:',
                        'path' => '/var/run/redis/redis.sock'
                    )
                ),
                false
            ),
            array(
                "tcp://127.0.0.1?database=2&timeout=2.5&port=54428",
                array(
                    array(
                        'database' => '2',
                        'timeout' => '2.5',
                        'port' => '54428',
                        'scheme' => 'tcp',
                        'prefix' => 'PHPREDIS_SESSION:',
                        'host' => '127.0.0.1',
                        'query' => 'database=2&timeout=2.5&port=54428'
                    ),
                ),
                false
            ),
            array(
                "127.0.0.1",
                array(),
                true
            ),
            array(
                "tcp:sdgf243@Q#t23",
                array(),
                true
            ),
            array(
                "/var/run/redis/redis.sock",
                array(),
                true
            )
        );
    }
}
