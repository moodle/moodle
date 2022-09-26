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

namespace tool_moodlenet\local;

use tool_moodlenet\local\import_info;
use tool_moodlenet\local\remote_resource;
use tool_moodlenet\local\url;

/**
 * Class tool_moodlenet_import_info_testcase, providing test cases for the import_info class.
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_info_test extends \advanced_testcase {

    /**
     * Create some test objects.
     *
     * @return array
     */
    protected function create_test_info(): array {
        $user = $this->getDataGenerator()->create_user();
        $resource = new remote_resource(new \curl(),
            new url('http://example.org'),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource summary'
            ]
        );
        $importinfo = new import_info($user->id, $resource, (object)[]);

        return [$user, $resource, $importinfo];
    }

    /**
     * Test for creation and getters.
     */
    public function test_getters() {
        $this->resetAfterTest();
        [$user, $resource, $importinfo] = $this->create_test_info();

        $this->assertEquals($resource, $importinfo->get_resource());
        $this->assertEquals(new \stdClass(), $importinfo->get_config());
        $this->assertNotEmpty($importinfo->get_id());
    }

    /**
     * Test for setters.
     */
    public function test_set_config() {
        $this->resetAfterTest();
        [$user, $resource, $importinfo] = $this->create_test_info();

        $config = $importinfo->get_config();
        $this->assertEquals(new \stdClass(), $config);
        $config->course = 3;
        $config->section = 1;
        $importinfo->set_config($config);
        $this->assertEquals((object) ['course' => 3, 'section' => 1], $importinfo->get_config());
    }

    /**
     * Verify the object can be stored and loaded.
     */
    public function test_persistence() {
        $this->resetAfterTest();
        [$user, $resource, $importinfo] = $this->create_test_info();

        // Nothing to load initially since nothing has been saved.
        $loadedinfo = import_info::load($importinfo->get_id());
        $this->assertNull($loadedinfo);

        // Now, save and confirm we can load the data into a new object.
        $importinfo->save();
        $loadedinfo2 = import_info::load($importinfo->get_id());
        $this->assertEquals($importinfo, $loadedinfo2);

        // Purge and confirm the load returns null now.
        $importinfo->purge();
        $loadedinfo3 = import_info::load($importinfo->get_id());
        $this->assertNull($loadedinfo3);
    }
}
