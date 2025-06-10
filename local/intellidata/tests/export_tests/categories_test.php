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
 * Categories migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\setup_helper;
use local_intellidata\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Categories migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class categories_test extends custom_db_client_testcase {

    /**
     * Test create category.
     *
     * @covers \local_intellidata\entities\categories\category
     * @covers \local_intellidata\entities\categories\migration
     * @covers \local_intellidata\entities\categories\observer::course_category_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_category_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_category_test(0);
    }

    /**
     * Test update category.
     *
     * @covers \local_intellidata\entities\categories\category
     * @covers \local_intellidata\entities\categories\migration
     * @covers \local_intellidata\entities\categories\observer::course_category_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_category_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_category_test(0);
    }

    /**
     * Test delete category.
     *
     * @covers \local_intellidata\entities\categories\category
     * @covers \local_intellidata\entities\categories\migration
     * @covers \local_intellidata\entities\categories\observer::course_category_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        } else {
            $this->test_create();
        }

        $this->delete_category_test(0);
    }

    /**
     * Delete category test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_category_test($tracking) {
        global $DB;

        $data = [
            'name' => 'ibcategory1' . $tracking,
        ];

        $category = $DB->get_record('course_categories', $data);

        $coursecat = generator::get_category($category->id);
        $coursecat->delete_full();

        $entity = new \local_intellidata\entities\categories\category($category);
        $entitydata = $entity->export();

        $storage = StorageHelper::get_storage_service(['name' => 'categories']);
        $datarecord = $storage->get_log_entity_data('d', ['id' => $category->id]);

        $datarecorddata = json_decode($datarecord->data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }

    /**
     * Update category test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_category_test($tracking) {
        global $DB;

        $data = [
            'name' => 'ibcategory1' . $tracking,
        ];

        $category = $DB->get_record('course_categories', $data);
        $category->idnumber = '2222222' . $tracking;

        $data['idnumber'] = $category->idnumber;

        $coursecat = generator::get_category($category->id);
        $coursecat->update($category);

        $entity = new \local_intellidata\entities\categories\category($category);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'categories']);
        $datarecord = $storage->get_log_entity_data('u', ['id' => $category->id, 'idnumber' => $data['idnumber']]);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create category test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_category_test($tracking) {
        $data = [
            'name' => 'ibcategory1' . $tracking,
            'idnumber' => '1111111' . $tracking,
        ];

        // Create category.
        $category = generator::create_category($data);

        $entity = new \local_intellidata\entities\categories\category($category);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'categories']);
        $datarecord = $storage->get_log_entity_data('c', ['id' => $category->id, 'idnumber' => $data['idnumber']]);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}
