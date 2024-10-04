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
 * Code quality unit tests that are fast enough to run each time.
 *
 * @package    core
 * @category   test
 * @copyright  (C) 2013 onwards Remote Learner.net Inc (http://www.remote-learner.net)
 * @author     Brent Boghosian (brent.boghosian@remote-learner.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use context;
use context_helper;

defined('MOODLE_INTERNAL') || die();


/**
 * Code quality unit tests that are fast enough to run each time.
 *
 * @package    core
 * @category   test
 * @copyright  (C) 2013 onwards Remote Learner.net Inc (http://www.remote-learner.net)
 * @author     Brent Boghosian (brent.boghosian@remote-learner.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class customcontext_test extends \advanced_testcase {

    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test case for custom context classes
     */
    public function test_customcontexts(): void {
        global $CFG;
        static $customcontexts = array(
            11 => 'context_bogus1',
            12 => 'context_bogus2',
            13 => 'context_bogus3'
        );

        // save any existing custom contexts
        $existingcustomcontexts = get_config(null, 'custom_context_classes');

        set_config('custom_context_classes', serialize($customcontexts));
        initialise_cfg();
        context_helper::reset_levels();
        $alllevels = context_helper::get_all_levels();
        $this->assertEquals($alllevels[11], 'context_bogus1');
        $this->assertEquals($alllevels[12], 'context_bogus2');
        $this->assertEquals($alllevels[13], 'context_bogus3');

        // clean-up & restore any custom contexts
        set_config('custom_context_classes', ($existingcustomcontexts === false) ? null : $existingcustomcontexts);
        initialise_cfg();
        context_helper::reset_levels();
    }
}

/**
 * Bogus custom context class for testing
 */
class context_bogus1 extends context {
    /**
     * Returns context shortname.
     *
     * @return string
     */
    public static function get_short_name(): string {
        return 'bogus1';
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return \moodle_url
     */
    public function get_url() {
        global $ME;
        return $ME;
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        return array();
    }
}

/**
 * Bogus custom context class for testing
 */
class context_bogus2 extends context {
    /**
     * Returns context shortname.
     *
     * @return string
     */
    public static function get_short_name(): string {
        return 'bogus2';
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return \moodle_url
     */
    public function get_url() {
        global $ME;
        return $ME;
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        return array();
    }
}

/**
 * Bogus custom context class for testing
 */
class context_bogus3 extends context {
    /**
     * Returns context shortname.
     *
     * @return string
     */
    public static function get_short_name(): string {
        return 'bogus3';
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return \moodle_url
     */
    public function get_url() {
        global $ME;
        return $ME;
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        return array();
    }
}
