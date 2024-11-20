<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace test_plugin;

/**
 * Fixture for testing of hooks.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class callbacks {
    /** @var string[] list of calls */
    public static $calls = [];

    /**
     * Callback tester.
     *
     * @param \test_plugin\hook\hook $hook
     * @return void
     */
    public static function test1(\test_plugin\hook\hook $hook): void {
        self::$calls[] = 'test1';
    }

    /**
     * Callback tester.
     *
     * @param \test_plugin\hook\hook $hook
     * @return void
     */
    public static function test2(\test_plugin\hook\hook $hook): void {
        self::$calls[] = 'test2';
    }

    /**
     * Callback tester.
     *
     * @param \test_plugin\hook\stoppablehook $hook
     * @return void
     */
    public static function stop1(\test_plugin\hook\stoppablehook $hook): void {
        self::$calls[] = 'stop1';
        $hook->stop();
    }

    /**
     * Callback tester.
     *
     * @param \test_plugin\hook\stoppablehook $hook
     * @return void
     */
    public static function stop2(\test_plugin\hook\stoppablehook $hook): void {
        self::$calls[] = 'stop2';
        $hook->stop();
    }

    /**
     * Callback tester for exceptions.
     *
     * @param \test_plugin\hook\hook $hook
     * @return void
     */
    public static function exception(\test_plugin\hook\hook $hook): void {
        self::$calls[] = 'exception';
        throw new \Exception('grrr');
    }
}
