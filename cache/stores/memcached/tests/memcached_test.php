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
 * Memcached unit tests
 *
 * @package    cachestore_memcached
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/memcached/lib.php');

/**
 * Memcached unit test class.
 *
 * @package    cachestore_memcached
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_memcached_test extends cachestore_tests {
    /**
     * Prepare to run tests.
     */
    public function setUp() {
        if (defined('TEST_CACHESTORE_MEMCACHED_TESTSERVERS')) {
            set_config('testservers', TEST_CACHESTORE_MEMCACHED_TESTSERVERS, 'cachestore_memcached');
            $this->resetAfterTest();
        }
        parent::setUp();
    }
    /**
     * Returns the memcached class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_memcached';
    }
}