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
 * User info fields migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\userinfocategories\userinfocategory;
use local_intellidata\entities\userinfofields\userinfofield;
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
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * User info fields migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class userinfofields_test extends custom_db_client_testcase {

    /**
     * Test create user info field.
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
            $this->create_user_info_field_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_user_info_field_test(0);
    }

    /**
     * Test update user info field.
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
            $this->update_user_info_field_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_user_info_field_test(0);
    }

    /**
     * Test delete user info field.
     *
     * @covers \local_intellidata\entities\userinfocategories\userinfocategory
     * @covers \local_intellidata\entities\userinfocategories\migration
     * @covers \local_intellidata\entities\userinfocategories\observer::user_info_category_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_user_info_field_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_user_info_field_test(0);
    }

    /**
     * Delete user info field test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_user_info_field_test($tracking) {
        global $DB;

        $userinfofield = $DB->get_record('user_info_field', ['shortname' => 'example_uif' . $tracking]);

        profile_delete_field($userinfofield->id);

        $storage = StorageHelper::get_storage_service(['name' => 'userinfofields']);
        $datarecord = $storage->get_log_entity_data('d', ['id' => $userinfofield->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = json_decode($datarecord->data);
        $this->assertEquals($userinfofield->id, $datarecorddata->id);
    }

    /**
     * Update user info field test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function update_user_info_field_test($tracking) {
        global $DB;

        $userinfofield = $DB->get_record('user_info_field', ['shortname' => 'example_uif' . $tracking]);
        $userinfofield->name = 'Example field update ' . $tracking;

        $field = new profile_define_base();
        $field->define_save($userinfofield);

        $data = [
            'id' => $userinfofield->id,
            'shortname' => $userinfofield->shortname,
            'name' => $userinfofield->name,
        ];

        $entity = new userinfofield($userinfofield);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'userinfofields']);

        $datarecord = $storage->get_log_entity_data('u', ['id' => $userinfofield->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create user info field test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \moodle_exception
     */
    private function create_user_info_field_test($tracking) {
        global $DB;

        // Create a new profile category.
        $cat1 = generator::create_profile_field_category(['name' => 'Example category']);

        // Create a new profile field.
        $data = new \stdClass();
        $data->datatype = 'text';
        $data->shortname = 'example_uif' . $tracking;
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

        $userinfofield = $DB->get_record('user_info_field', ['shortname' => $data->shortname]);

        $data = [
            'id' => $userinfofield->id,
            'shortname' => $userinfofield->shortname,
            'name' => $userinfofield->name,
            'categoryid' => $userinfofield->categoryid,
            'visible' => $userinfofield->visible,
            'datatype' => $userinfofield->datatype,
        ];

        $entity = new userinfofield($userinfofield);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'userinfofields']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $userinfofield->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
