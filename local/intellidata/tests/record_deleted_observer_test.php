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
 * Config service test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\helpers\StorageHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\persistent\datatypeconfig;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\repositories\config_repository;
use local_intellidata\repositories\file_storage_repository;
use local_intellidata\services\config_service;
use local_intellidata\services\datatypes_service;
use local_intellidata\services\export_service;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');

/**
 * Config service test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 *
 * @runTestsInSeparateProcesses
 */
class record_deleted_observer_test extends \advanced_testcase {

    /**
     * Setup test.
     *
     * @return void
     */
    public function setUp(): void {
        $this->setAdminUser();

        setup_helper::setup_tests_config();

        $configservice = new config_service(datatypes_service::get_all_datatypes());
        $configservice->setup_config(true);
    }

    /**
     * Test success tracking action.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @covers \local_intellidata\observers\record_deleted_observer::execute
     */
    public function test_execute_success() {
        $this->resetAfterTest(true);

        $datatype = datatypes_service::generate_optional_datatype('enrol');

        // Enable enrol datatype in config.
        $configrepository = new config_repository();
        $configrepository->enable($datatype);

        SettingsHelper::set_setting('trackingstorage', StorageHelper::FILE_STORAGE);

        // Enable enrol datatype in export.
        $exportlogrepository = new export_log_repository();
        $exportlogrepository->insert_datatype($datatype);

        $enrolinstance = $this->trigger_enrol_instance_deleted();

        $exportservice = new export_service();
        $enroldatatype = $exportservice->get_datatype($datatype);
        $filestoragerepository = new file_storage_repository($enroldatatype);
        $storagefile = $filestoragerepository->get_storage_file();

        // Validate temp file not exists.
        $this->assertFileExists($storagefile);

        $data = StorageHelper::get_from_file($storagefile);
        $data = reset($data);

        $this->assertEquals($enrolinstance->id, $data->id);
        $this->assertEquals('d', $data->crud);
    }

    /**
     * Test tracking action when export is disabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @covers \local_intellidata\observers\record_deleted_observer::execute
     */
    public function test_execute_export_disabled() {
        $this->resetAfterTest(true);

        $datatype = datatypes_service::generate_optional_datatype('enrol');

        // Enable enrol datatype in config.
        $configrepository = new config_repository();
        $configrepository->enable($datatype);

        $this->trigger_enrol_instance_deleted();

        $exportservice = new export_service();
        $datatypes = $exportservice->get_datatypes(false);

        // Validate datatype exists.
        $this->assertFalse(isset($datatypes[$datatype]));
    }

    /**
     * Test tracking action when config is disabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @covers \local_intellidata\observers\record_deleted_observer::execute
     */
    public function test_execute_config_disabled() {
        $this->resetAfterTest(true);

        $datatype = datatypes_service::generate_optional_datatype('enrol');

        // Enable enrol datatype in export.
        $exportlogrepository = new export_log_repository();
        $exportlogrepository->insert_datatype($datatype);

        // Disabled datatype.
        if ($record = datatypeconfig::get_record(['datatype' => $datatype])) {
            $record->set('status', datatypeconfig::STATUS_DISABLED);
            $record->save();
        }

        $this->trigger_enrol_instance_deleted();

        // Rebuild datatypes cache data.
        \local_intellidata\services\datatypes_service::get_datatypes(false, true);

        // Get datatypes with default configuration.
        $exportservice = new export_service();
        $datatypes = $exportservice->get_datatypes(false);

        // Validate datatype exists.
        $this->assertFalse(isset($datatypes[$datatype]));
    }

    /**
     * Test tracking action when setting event tracking is disabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @covers \local_intellidata\observers\record_deleted_observer::execute
     */
    public function test_execute_setting_disabled() {
        $this->resetAfterTest(true);

        $datatype = datatypes_service::generate_optional_datatype('enrol');

        SettingsHelper::set_setting('exportdeletedrecords', SettingsHelper::EXPORTDELETED_DISABLED);

        // Enable enrol datatype in config.
        $configrepository = new config_repository();
        $configrepository->enable($datatype);

        // Enable enrol datatype in export.
        $exportlogrepository = new export_log_repository();
        $exportlogrepository->insert_datatype($datatype);

        $this->trigger_enrol_instance_deleted();

        $exportservice = new export_service();
        $enroldatatype = $exportservice->get_datatype($datatype);
        $filestoragerepository = new file_storage_repository($enroldatatype);
        $storagefile = $filestoragerepository->get_storage_file();

        // Validate temp file not exists.
        $assertfiledoesnotexistmethod = test_helper::assert_file_does_not_exist_method($this);
        $this->$assertfiledoesnotexistmethod($storagefile);
    }

    /**
     * Trigger enrol instance deleted event.
     *
     * @return false|mixed|\stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function trigger_enrol_instance_deleted() {
        global $DB;

        $selfplugin = enrol_get_plugin('self');
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        $ccount = $DB->count_records('course');
        $data = [
            'fullname' => 'ibcourse1' . $ccount,
            'idnumber' => '1111111' . $ccount,
            'shortname' => 'ibscourse1' . $ccount,
        ];

        $course = generator::create_course($data);

        // Creating enrol instance.
        $instanceid = $selfplugin->add_instance($course, [
            'status' => ENROL_INSTANCE_ENABLED,
            'name' => 'Test instance 1',
            'customint6' => 1,
            'roleid' => $studentrole->id,
        ]);

        // Deleting enrol instance.
        $instance = $DB->get_record('enrol', ['id' => $instanceid]);
        $selfplugin->delete_instance($instance);

        return $instance;
    }
}
