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

namespace core;

/**
 * Unit tests for our locking configuration.
 *
 * @package    core
 * @category   test
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lock_config_test extends \advanced_testcase {

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
        $originalfilelocking = null;
        if (isset($CFG->preventfilelocking)) {
            $originalfilelocking = $CFG->preventfilelocking;
        }

        // Test no configuration.
        unset($CFG->lock_factory);
        $CFG->preventfilelocking = 0;
        $factory = \core\lock\lock_config::get_lock_factory('cache');
        $this->assertNotEmpty($factory, 'Get a default factory with no configuration');

        // Test explicit broken lock.
        $CFG->lock_factory = '\core\lock\not_a_lock_factory';
        try {
            $factory = \core\lock\lock_config::get_lock_factory('cache');
            $this->fail('Exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Test explicit file locks.
        $CFG->lock_factory = '\core\lock\file_lock_factory';
        $factory = \core\lock\lock_config::get_lock_factory('cache');
        if ($factory instanceof \core\lock\timing_wrapper_lock_factory) {
            $factory = $factory->get_real_factory();
        }
        $this->assertTrue($factory instanceof \core\lock\file_lock_factory,
                'Get an explicit file lock factory');

        // Test explicit file locks but with file locks prevented.
        $CFG->preventfilelocking = 1;
        try {
            $factory = \core\lock\lock_config::get_lock_factory('cache');
            $this->fail('Exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Test explicit db locks.
        $CFG->lock_factory = '\core\lock\db_record_lock_factory';
        $factory = \core\lock\lock_config::get_lock_factory('cache');
        if ($factory instanceof \core\lock\timing_wrapper_lock_factory) {
            $factory = $factory->get_real_factory();
        }
        $this->assertTrue($factory instanceof \core\lock\db_record_lock_factory,
                'Get an explicit db record lock factory');

        if ($original) {
            $CFG->lock_factory = $original;
        } else {
            unset($CFG->lock_factory);
        }
        if ($originalfilelocking) {
            $CFG->preventfilelocking = $originalfilelocking;
        } else {
            unset($CFG->preventfilelocking);
        }
    }
}

