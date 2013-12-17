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
 * lock unit tests
 *
 * @package    core
 * @category   lock
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for our locking configuration.
 *
 * @package    core
 * @category   lock
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lock_config_testcase extends advanced_testcase {

    /**
     * Tests the static parse charset method
     * @return void
     */
    public function test_lock_config() {
        global $CFG;
        $original = null;
        if (isset($CFG->lock_factory)) {
            $original = $CFG->lock_factory;
        }

        // Test no configuration.
        unset($CFG->lock_factory);

        $factory = \core\lock\lock_config::get_lock_factory('cache');

        $this->assertNotEmpty($factory, 'Get a default factory with no configuration');

        $CFG->lock_factory = '\core\lock\file_lock_factory';

        $factory = \core\lock\lock_config::get_lock_factory('cache');
        $this->assertTrue($factory instanceof \core\lock\file_lock_factory,
                          'Get a default factory with a set configuration');

        $CFG->lock_factory = '\core\lock\db_record_lock_factory';

        $factory = \core\lock\lock_config::get_lock_factory('cache');
        $this->assertTrue($factory instanceof \core\lock\db_record_lock_factory,
                          'Get a default factory with a changed configuration');

        if ($original) {
            $CFG->lock_factory = $original;
        } else {
            unset($CFG->lock_factory);
        }
    }
}

