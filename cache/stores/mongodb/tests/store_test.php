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

namespace cachestore_mongodb;

use cache_store;
use cache_definition;
use cachestore_mongodb;

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/mongodb/lib.php');

/**
 * MongoDB unit test class.
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
class store_test extends \cachestore_tests {
    /**
     * Returns the MongoDB class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_mongodb';
    }

    /**
     * A small additional test to make sure definitions that hash a hash starting with a number work OK
     */
    public function test_collection_name() {
        // This generates a definition that has a hash starting with a number. MDL-46208.
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_mongodb', 'abc');
        $instance = new cachestore_mongodb('MongoDB_Test', cachestore_mongodb::unit_test_configuration());

        if (!$instance->is_ready()) {
            $this->markTestSkipped();
        }
        $instance->initialise($definition);

        $this->assertTrue($instance->set(1, 'alpha'));
        $this->assertTrue($instance->set(2, 'beta'));
        $this->assertEquals('alpha', $instance->get(1));
        $this->assertEquals('beta', $instance->get(2));
        $this->assertEquals(array(
            1 => 'alpha',
            2 => 'beta'
        ), $instance->get_many(array(1, 2)));
    }
}
