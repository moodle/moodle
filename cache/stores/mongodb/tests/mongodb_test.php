<?php
// This mongodb is part of Moodle - http://moodle.org/
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
 * MongoDB unit tests.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_MONGODB_TESTSERVER', 'mongodb://localhost:27017');
 *
 * @package    cachestore_mongodb
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/mongodb/lib.php');

/**
 * MongoDB unit test class.
 *
 * @package    cachestore_mongodb
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_mongodb_test extends cachestore_tests {
    /**
     * Prepare to run tests.
     */
    public function setUp() {
        if (defined('TEST_CACHESTORE_MONGODB_TESTSERVER')) {
            set_config('testserver', TEST_CACHESTORE_MONGODB_TESTSERVER, 'cachestore_mongodb');
            $this->resetAfterTest();
        }
        parent::setUp();
    }
    /**
     * Returns the MongoDB class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_mongodb';
    }
}