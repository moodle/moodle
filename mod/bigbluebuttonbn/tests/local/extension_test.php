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
namespace mod_bigbluebuttonbn\local;

use mod_bigbluebuttonbn\extension;
use mod_bigbluebuttonbn\local\extension\mod_instance_helper;
use mod_bigbluebuttonbn\test\subplugins_test_helper_trait;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Extension helper class test
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @coversDefaultClass \mod_bigbluebuttonbn\extension
 */
class extension_test extends \advanced_testcase {
    use subplugins_test_helper_trait;
    use testcase_helper_trait;

    /**
     * Setup our fake plugin
     *
     * @return void
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
        $this->setup_fake_plugin('simple');
    }

    /**
     * Test for the type_text provider.
     *
     * @param bool $bbbenabled
     * @param string $apiclass
     * @param array $extensionclasses
     *
     * @dataProvider classes_implementing_class
     * @covers       \mod_bigbluebuttonbn\extension::get_instances_implementing
     */
    public function test_get_class_implementing(bool $bbbenabled, string $apiclass, array $extensionclasses): void {
        $this->enable_plugins($bbbenabled);
        // Make the method public so we can test it.
        $reflectionextension = new \ReflectionClass(extension::class);
        $getclassimplementing = $reflectionextension->getMethod('get_instances_implementing');
        $getclassimplementing->setAccessible(true);
        $allfoundinstances = $getclassimplementing->invoke(null, $apiclass);
        $foundclasses = array_map(
            function($instance) {
                return get_class($instance);
            },
            $allfoundinstances
        );
        $this->assertEquals($extensionclasses, $foundclasses);
    }

    /**
     * Test the add module callback
     *
     * @return void
     * @covers \mod_bigbluebuttonbn\local\extension\mod_instance_helpe
     */
    public function test_mod_instance_helper_add() {
        global $DB;
        // Enable plugin.
        $this->enable_plugins(true);

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module(
            'bigbluebuttonbn',
            ['course' => $course->id, 'newfield' => 2]
        );
        $this->assertEquals(2, $DB->get_field('bbbext_simple', 'newfield', ['bigbluebuttonbnid' => $record->id]));
    }

    /**
     * Test the update module callback
     *
     * @return void
     * @covers \mod_bigbluebuttonbn\local\extension\mod_instance_helpe
     */
    public function test_mod_instance_helper_update() {
        global $DB;
        $this->setAdminUser();
        // Enable plugin.
        $this->enable_plugins(true);

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id, 'newfield' => 2]);
        $cm = get_fast_modinfo($course)->instances['bigbluebuttonbn'][$record->id];
        [$cm, $context, $moduleinfo, $data] = get_moduleinfo_data($cm, $course);
        $data->newfield = 3;
        bigbluebuttonbn_update_instance($data);
        $this->assertEquals(3, $DB->get_field('bbbext_simple', 'newfield', ['bigbluebuttonbnid' => $record->id]));
    }

    /**
     * Test delete module callback
     *
     * @return void
     * @covers \mod_bigbluebuttonbn\local\extension\mod_instance_helpe
     */
    public function test_mod_instance_helper_delete() {
        global $DB;
        $this->initialise_mock_server();
        // Enable plugin.
        $this->enable_plugins(true);

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id, 'newfield' => 2]);
        $cm = get_fast_modinfo($course)->instances['bigbluebuttonbn'][$record->id];
        course_delete_module($cm->id, false);
        $this->assertFalse($DB->get_field('bbbext_simple', 'newfield', ['bigbluebuttonbnid' => $record->id]));
    }

    /**
     * Test the action_url_addons with plugin enabled
     *
     * @return void
     * @covers \mod_bigbluebuttonbn\extension::action_url_addons
     */
    public function test_action_url_addons() {
        // Enable plugin.
        $this->enable_plugins(true);
        // Set a random var here.
        $var1 = [];
        $var2 = ['Test'];
        ['data' => $additionalvar1, 'metadata' => $additionalvar2] = extension::action_url_addons('create', [], ['Test']);
        $this->assertEmpty($additionalvar1);
        $this->assertCount(2, $additionalvar2);
        ['data' => $additionalvar1, 'metadata' => $additionalvar2] = extension::action_url_addons('delete');
        $this->assertNotEmpty($additionalvar1);
        $this->assertEmpty($additionalvar2);
    }

    /**
     * Data provider for testing get_class_implementing
     *
     * @return array[]
     */
    public function classes_implementing_class(): array {
        return [
            'mod_instance_helper with plugin disabled' => [
                'bbbenabled' => false,
                'apiclass' => mod_instance_helper::class,
                'result' => []
            ],
            'mod_instance_helper with plugin enabled' => [
                'bbbenabled' => true,
                'apiclass' => mod_instance_helper::class,
                'result' => [
                    'bbbext_simple\\bigbluebuttonbn\\mod_instance_helper'
                ]
            ]
        ];
    }

    /**
     * Enable plugins
     *
     * @param bool $bbbenabled
     * @return void
     */
    private function enable_plugins(bool $bbbenabled) {
        // First make sure that either BBB is enabled or not.
        set_config('bigbluebuttonbn_default_dpa_accepted', $bbbenabled);
        \core\plugininfo\mod::enable_plugin('bigbluebuttonbn', $bbbenabled ? 1 : 0);
        $plugin = extension::BBB_EXTENSION_PLUGIN_NAME . '_simple';
        if ($bbbenabled) {
            unset_config('disabled', $plugin);
        } else {
            set_config('disabled', 'disabled', $plugin);
        }
    }
}
