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
 * Repository generator tests
 *
 * @package   repository
 * @category  test
 * @copyright 2013 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Repository generator tests class
 *
 * @package   repository
 * @category  test
 * @copyright 2013 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_repository_generator_testcase extends advanced_testcase {

    /**
     * Basic test of creation of repository types.
     *
     * @return void
     */
    public function test_create_type() {
        global $DB;
        $this->resetAfterTest(true);

        // All the repository types.
        $all = array('boxnet', 'coursefiles', 'dropbox', 'equella', 'filesystem', 'flickr',
            'flickr_public', 'googledocs', 'local', 'merlot', 'picasa', 'recent', 's3', 'upload', 'url',
            'user', 'webdav', 'wikimedia', 'youtube');

        // The ones enabled during installation.
        $alreadyenabled = array('local', 'recent', 'upload', 'url', 'user', 'wikimedia');

        // Enable all the repositories which are not enabled yet.
        foreach ($all as $type) {
            if (in_array($type, $alreadyenabled)) {
                continue;
            }
            $repotype = $this->getDataGenerator()->create_repository_type($type);
            $this->assertEquals($repotype->type, $type, 'Unexpected name after creating repository type ' . $type);
            $this->assertTrue($DB->record_exists('repository', array('type' => $type, 'visible' => 1)));
        }

        // Check that all the repositories have been enabled.
        foreach ($all as $type) {
            $caughtexception = false;
            try {
                $this->getDataGenerator()->create_repository_type($type);
            } catch (repository_exception $e) {
                if ($e->getMessage() === 'This repository already exists') {
                    $caughtexception = true;
                }
            }
            $this->assertTrue($caughtexception, "Repository type '$type' should have already been enabled");
        }
    }

    /**
     * Ensure that the type options are properly saved.
     *
     * @return void
     */
    public function test_create_type_custom_options() {
        global $DB;
        $this->resetAfterTest(true);

        // Single instances.
        // Note: for single instances repositories enablecourseinstances and enableuserinstances are forced set to 0.
        $record = new stdClass();
        $record->pluginname = 'Custom Flickr';
        $record->api_key = '12345';
        $record->secret = '67890';
        $flickr = $this->getDataGenerator()->create_repository_type('flickr', $record);

        $config = get_config('flickr');
        $record->enableuserinstances = '0';
        $record->enablecourseinstances = '0';
        $this->assertEquals($record, $config);
        $this->assertEquals('Custom Flickr',
            $DB->get_field('repository_instances', 'name', array('typeid' => $flickr->id), MUST_EXIST));

        $record = new stdClass();
        $record->pluginname = 'Custom Dropbox';
        $record->dropbox_key = '12345';
        $record->dropbox_secret = '67890';
        $record->dropbox_cachelimit = '123';
        $dropbox = $this->getDataGenerator()->create_repository_type('dropbox', $record);

        $config = get_config('dropbox');
        $record->enableuserinstances = '0';
        $record->enablecourseinstances = '0';
        $this->assertEquals($record, $config);
        $this->assertEquals('Custom Dropbox',
            $DB->get_field('repository_instances', 'name', array('typeid' => $dropbox->id), MUST_EXIST));

        // Multiple instances.
        $record = new stdClass();
        $record->pluginname = 'Custom WebDAV';
        $record->enableuserinstances = '0';
        $record->enablecourseinstances = '0';
        $webdav = $this->getDataGenerator()->create_repository_type('webdav', $record);

        $config = get_config('webdav');
        $this->assertEquals($record, $config);
        $this->assertFalse( $DB->record_exists('repository_instances', array('typeid' => $webdav->id)));

        $record = new stdClass();
        $record->pluginname = 'Custom Equella';
        $record->enableuserinstances = '1';
        $record->enablecourseinstances = '0';
        $equella = $this->getDataGenerator()->create_repository_type('equella', $record);

        $config = get_config('equella');
        $this->assertEquals($record, $config);
        $this->assertFalse( $DB->record_exists('repository_instances', array('typeid' => $equella->id)));
    }

    /**
     * Covers basic testing of instance creation.
     *
     * @return void
     */
    public function test_create_instance() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $block = $this->getDataGenerator()->create_block('online_users');

        $type = $this->getDataGenerator()->create_repository_type('webdav');
        $record = new stdClass();
        $record->name = 'A WebDAV instance';
        $record->webdav_type = '1';
        $record->webdav_server = 'localhost';
        $record->webdav_port = '12345';
        $record->webdav_path = '/nothing';
        $record->webdav_user = 'me';
        $record->webdav_password = '\o/';
        $record->webdav_auth = 'basic';
        $instance = $this->getDataGenerator()->create_repository('webdav', $record);

        $this->assertEquals(1, $DB->count_records('repository_instances', array('typeid' => $type->id)));
        $this->assertEquals($record->name, $DB->get_field('repository_instances', 'name', array('id' => $instance->id)));
        $entries = $DB->get_records('repository_instance_config', array('instanceid' => $instance->id));
        $config = new stdClass();
        foreach ($entries as $entry) {
            $config->{$entry->name} = $entry->value;
        }
        unset($record->name);
        $this->assertEquals($config, $record);

        // Course context.
        $record = new stdClass();
        $record->contextid = context_course::instance($course->id)->id;
        $instance = $this->getDataGenerator()->create_repository('webdav', $record);
        $this->assertEquals(2, $DB->count_records('repository_instances', array('typeid' => $type->id)));
        $this->assertEquals($record->contextid, $instance->contextid);

        // User context.
        $record->contextid = context_user::instance($user->id)->id;
        $instance = $this->getDataGenerator()->create_repository('webdav', $record);
        $this->assertEquals(3, $DB->count_records('repository_instances', array('typeid' => $type->id)));
        $this->assertEquals($record->contextid, $instance->contextid);

        // Invalid context.
        $this->expectException('coding_exception');
        $record->contextid = context_block::instance($block->id)->id;
        $instance = $this->getDataGenerator()->create_repository('webdav', $record);
    }

}
