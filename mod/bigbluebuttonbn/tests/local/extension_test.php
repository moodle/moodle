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

use backup;
use backup_controller;
use mod_bigbluebuttonbn\extension;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\extension\mod_instance_helper;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\test\subplugins_test_helper_trait;
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use restore_controller;
use restore_dbops;

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
        $this->resetDebugging(); // We might have debugging messages issued from setup_fake_plugin here that we need to get rid of.
    }


    /**
     * Setup our fake plugin
     *
     * @return void
     */
    public function tearDown(): void {
        $this->uninstall_fake_plugin('simple');
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
     * @covers \mod_bigbluebuttonbn\local\extension\mod_instance_helper
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
     * @covers \mod_bigbluebuttonbn\local\extension\mod_instance_helper
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
     * @covers \mod_bigbluebuttonbn\local\extension\mod_instance_helper
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
        $course = $this->get_course();
        [$cm, $cminfo, $bbactivity] = $this->create_instance($course);
        $bbactivity->newfield = 4;
        extension::update_instance($bbactivity);
        ['data' => $additionalvar1, 'metadata' => $additionalvar2] =
            extension::action_url_addons('create', [], ['bbb-meta' => 'Test'], $bbactivity->id);
        $this->assertEmpty($additionalvar1);
        $this->assertCount(2, $additionalvar2);
        $this->assertEquals($additionalvar2['newfield'], 4);
        ['data' => $additionalvar1, 'metadata' => $additionalvar2] = extension::action_url_addons('delete');
        $this->assertEmpty($additionalvar1);
        $this->assertEmpty($additionalvar2);
    }

    /**
     * Test the action_url_addons with plugin enabled
     *
     * @return void
     * @covers \mod_bigbluebuttonbn\extension::action_url_addons
     */
    public function test_join_url_with_additional_field() {
        $this->initialise_mock_server();
        // Enable plugin.
        $this->enable_plugins(true);
        $course = $this->get_course();
        [$cm, $cminfo, $bbactivity] = $this->create_instance($course);
        $bbactivity->newfield = 4;
        extension::update_instance($bbactivity);
        $instance = instance::get_from_instanceid($bbactivity->id);
        $meeting = new meeting($instance);
        $meetingjoinurl = $meeting->get_join_url();
        $this->assertStringContainsString('newfield=4', $meetingjoinurl);
    }

    /**
     * Test backup restore (with extension)
     *
     * @covers       \backup_bigbluebuttonbn_activity_task
     */
    public function test_backup_restore(): void {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        $this->resetAfterTest();
        $course = $this->get_course();
        [$cm, $cminfo, $bbactivity] = $this->create_instance($course);
        $bbactivity->newfield = 4;
        extension::update_instance($bbactivity);

        $this->setAdminUser();

        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings.
        set_config('backup_general_users', 1, 'backup');
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
            backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_GENERAL,
            $USER->id);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Do restore to new course with default settings.
        $newcourseid = restore_dbops::create_new_course(
            $course->fullname, $course->shortname . '_2', $course->category);
        $rc = new restore_controller('test-restore-course', $newcourseid,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
            backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();
        $cms = get_fast_modinfo($newcourseid)->get_cms();
        $newmoduleid = null;
        foreach ($cms as $id => $cm) {
            if ($cm->modname == 'bigbluebuttonbn') {
                $newmoduleid = $cm->instance;
            }
        }
        // Check instance has been copied.
        $this->assertNotNull($newmoduleid);

        // Change the original instance value (this makes sure we are not looking at the same value in the
        // original and copy).
        $bbactivity->newfield = 5;
        extension::update_instance($bbactivity);

        // Now check the copied instance.
        $newfieldrecord = $DB->get_record('bbbext_simple', [
            'bigbluebuttonbnid' => $newmoduleid,
        ]);
        $this->assertNotNull($newfieldrecord);
        $this->assertEquals(4, $newfieldrecord->newfield);
        // And the original.
        $oldfieldrecord = $DB->get_record('bbbext_simple', [
            'bigbluebuttonbnid' => $bbactivity->id,
        ]);
        $this->assertNotNull($oldfieldrecord);
        $this->assertEquals(5, $oldfieldrecord->newfield);
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
