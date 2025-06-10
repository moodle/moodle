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
 * User info data migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\userinfodatas\userinfodata;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\setup_helper;
use local_intellidata\test_helper;
use profile_define_base;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/user/profile/definelib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * User info data migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class userinfodatas_test extends custom_db_client_testcase {

    /**
     * Test user info data create.
     *
     * @covers \local_intellidata\entities\userinfocategories\userinfocategory
     * @covers \local_intellidata\entities\userinfocategories\migration
     * @covers \local_intellidata\entities\userinfocategories\observer::user_info_category_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_user_info_data_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_user_info_data_test(0);
    }

    /**
     * Test user info data update.
     *
     * @covers \local_intellidata\entities\userinfocategories\userinfocategory
     * @covers \local_intellidata\entities\userinfocategories\migration
     * @covers \local_intellidata\entities\userinfocategories\observer::user_info_category_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_user_info_data_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_user_info_data_test(0);
    }

    /**
     * Test user info data update.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_user_info_data_test($tracking) {
        global $DB;

        $userinfofield = $DB->get_record('user_info_field', ['shortname' => 'example_uid' . $tracking]);

        $data = [
            'username' => 'unittest_create_user' . $tracking,
        ];

        $user = $DB->get_record('user', $data);
        $user->{'profile_field_' . $userinfofield->shortname} = 'test2';

        // Save custom profile fields data.
        profile_save_data($user);
        if ($tracking == 0) {
            \core\event\user_updated::create_from_userid($user->id)->trigger();
        }

        $data = [
            'userid' => $user->id,
            'fieldid' => $userinfofield->id,
        ];
        $userinfodata = $DB->get_record('user_info_data', $data);

        $data['id'] = $userinfodata->id;
        $data['data'] = 'test2';

        $entity = new userinfodata($userinfodata);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'userinfodatas']);

        $datarecord = $storage->get_log_entity_data('u', ['id' => $userinfodata->id, 'data' => $data['data']]);

        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create user info data test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_user_info_data_test($tracking) {
        global $DB;

        $userinfofield = $this->createfield($tracking);

        $data = [
            'firstname' => 'unit test create user',
            'username' => 'unittest_create_user' . $tracking,
            'password' => 'Unittest_User1!',
            'profile_field_' . $userinfofield->shortname => 'test',
        ];

        // Create user.
        $user = generator::create_user($data);

        $user->{'profile_field_' . $userinfofield->shortname} = 'test';
        if ($this->release < 3.7) {
            profile_save_data($user);
            \core\event\user_created::create_from_userid($user->id)->trigger();
        }
        $data = [
            'userid' => $user->id,
            'fieldid' => $userinfofield->id,
        ];
        $userinfodata = $DB->get_record('user_info_data', $data);

        $data = [
            'id' => $userinfodata->id,
            'userid' => $userinfodata->userid,
            'fieldid' => $userinfodata->fieldid,
            'data' => $userinfodata->data,
            'dataformat' => $userinfodata->dataformat,
        ];

        $entity = new userinfodata($userinfodata);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'userinfodatas']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $userinfodata->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create user info field.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function createfield($tracking) {
        global $DB;

        // Create a new profile category.
        $cat1 = generator::create_profile_field_category(['name' => 'Example category']);

        // Create a new profile field.
        $data = new \stdClass();
        $data->datatype = 'text';
        $data->shortname = 'example_uid' . $tracking;
        $data->name = 'Example field' . $tracking;
        $data->description = 'Hello this is an example' . $tracking;
        $data->required = false;
        $data->locked = false;
        $data->forceunique = false;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = $cat1->id;

        $field = new profile_define_base();
        $field->define_save($data);

        return $DB->get_record('user_info_field', ['shortname' => $data->shortname]);
    }
}
