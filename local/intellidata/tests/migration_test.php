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
 * Export_id_repository migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\helpers\MigrationHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\services\export_service;
use local_intellidata\services\migration_service;
use local_intellidata\repositories\file_storage_repository;
use local_intellidata\task\migration_task;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');

/**
 * Migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 *
 * @runTestsInSeparateProcesses
 */
class migration_test extends \advanced_testcase {

    /** @var int */
    private $migrationrecordslimit = 20;

    /** @var array */
    private $recordsnum = [
        'users' => 100,
        'categories' => 10,
        'courses' => 45,
        'cohorts' => 22,
    ];

    /**
     * Setup test.
     *
     * @return void
     */
    public function setUp(): void {
        $this->setAdminUser();

        setup_helper::setup_tests_config();
        setup_helper::disable_eventstracking();
        setup_helper::set_migration_limit($this->migrationrecordslimit);
    }

    /**
     * Test for enabled migration task.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     */
    public function test_migration_task_enabled() {
        global $DB;

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        MigrationHelper::enabled_migration_task();

        // Check disabling the task - export_files_task.
        $this->assertTrue(
            $DB->record_exists('task_scheduled', ['classname' => '\local_intellidata\task\export_files_task', 'disabled' => 1])
        );

        // Check disabling the task - export_data_task.
        $this->assertTrue(
            $DB->record_exists('task_scheduled', ['classname' => '\local_intellidata\task\export_data_task', 'disabled' => 1])
        );

        // Check enablement the task - migration_task.
        $this->assertTrue(
            $DB->record_exists('task_scheduled', ['classname' => MigrationHelper::MIGRATIONS_TASK_CLASS, 'disabled' => 0])
        );
    }

    /**
     * Test for migration cli.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     */
    public function test_migration_cli() {

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        // Disable exporting files.
        setup_helper::disable_exportfilesduringmigration();

        $exportservice = new export_service();
        $exportservice->set_migration_mode();

        $usersdatatype = 'users';
        $datatype = $exportservice->get_datatype($usersdatatype);

        $datatype['name'] = $exportservice->get_migration_name($datatype);
        $filestoragerepository = new file_storage_repository($datatype);
        $storagefile = $filestoragerepository->get_storage_file();

        // Validate temp file not exists.
        $assertfiledoesnotexistmethod = test_helper::assert_file_does_not_exist_method($this);
        $this->$assertfiledoesnotexistmethod($storagefile);

        // Generate users.
        generator::create_users($this->recordsnum['users']);

        $migrationservice = new migration_service(null, $exportservice);

        ob_start();
        $migrationservice->process();
        ob_get_clean();

        // Validate temp file created.
        $this->assertFileExists($storagefile);

        $data = StorageHelper::get_from_file($storagefile);

        $this->validate_users($data);
    }

    /**
     * Test for migration task.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     * @covers \local_intellidata\services\export_service
     * @covers \local_intellidata\helpers\StorageHelper
     */
    public function test_migration_task() {

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        if (!test_helper::is_get_local_path_from_storedfile_callable()) {
            return $this->assertTrue(true, 'Skipping test_migration_task test.');
        }

        // Enable exporting files.
        setup_helper::enable_exportfilesduringmigration();

        // Generate data.
        generator::create_users($this->recordsnum['users']);
        generator::create_categories($this->recordsnum['categories']);
        generator::create_courses($this->recordsnum['courses']);
        generator::create_cohorts($this->recordsnum['cohorts']);

        $exportservice = new export_service();
        $exportservice->set_migration_mode();
        $migrationservice = new migration_service(null, $exportservice);

        // Run first cron run.
        $migrationtask = new migration_task();
        ob_start();
        $migrationtask->execute();
        ob_get_clean();

        // Process full migration.
        $taskruns = count($this->recordsnum) * (array_sum($this->recordsnum) / $this->migrationrecordslimit) +
            count($migrationservice->get_tables());
        for ($i = 0; $i < (int)$taskruns; $i++) {
            ob_start();
            $migrationtask->execute();
            ob_get_clean();
        }

        $usersdatatype = $exportservice->get_datatype('users');
        $exportfiles = $exportservice->get_files(['datatype' => $usersdatatype['name']]);
        $storagefolder = (new file_storage_repository($usersdatatype))->storagefolder;

        // Validate users.
        $data = StorageHelper::get_data_from_exportfiles(
            $exportservice->get_migration_name($usersdatatype),
            $storagefolder,
            $exportfiles['migration_users']
        );
        $this->validate_users($data);

        // Validate categories.
        $exportfiles = $exportservice->get_files(['datatype' => 'categories']);
        $data = StorageHelper::get_data_from_exportfiles(
            'migration_categories',
            $storagefolder,
            $exportfiles['migration_categories']
        );
        $this->validate_categories($data);

        // Validate courses.
        $exportfiles = $exportservice->get_files(['datatype' => 'courses']);
        $data = StorageHelper::get_data_from_exportfiles(
            'migration_courses',
            $storagefolder,
            $exportfiles['migration_courses']
        );
        $this->validate_courses($data);

        // Validate cohorts.
        $exportfiles = $exportservice->get_files(['datatype' => 'cohorts']);
        $data = StorageHelper::get_data_from_exportfiles(
            'migration_cohorts',
            $storagefolder,
            $exportfiles['migration_cohorts']
        );
        $this->validate_cohorts($data);
    }

    /**
     * Test migration task with plugin enabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     */
    public function test_migration_task_with_plugin_enabled() {

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        SettingsHelper::set_setting('enabled', 0);
        $migrationtask = new migration_task();
        ob_start();
        $migrationtask->execute();
        $output = ob_get_contents();
        ob_get_clean();

        $this->assertTrue((bool)stristr($output, get_string('pluginnotconfigured', 'local_intellidata')));
    }

    /**
     * Test migration task with plugin disabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     */
    public function test_migration_task_with_plugin_disabled() {

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        SettingsHelper::set_setting('enabled', 1);
        $migrationtask = new migration_task();
        ob_start();
        $migrationtask->execute();
        $output = ob_get_contents();
        ob_get_clean();

        $this->assertFalse((bool)stristr($output, get_string('pluginnotconfigured', 'local_intellidata')));
    }

    /**
     * Test migration task with force migration setting enabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     */
    public function test_migration_task_force_migration_disabled_setting_enabled() {

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        SettingsHelper::set_setting('forcedisablemigration', 1);
        $migrationtask = new migration_task();
        ob_start();
        $migrationtask->execute();
        $output = ob_get_contents();
        ob_get_clean();

        $this->assertTrue((bool)stristr($output, get_string('migrationdisabled', 'local_intellidata')));
    }

    /**
     * Test migration task with force migration setting disabled.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\services\migration_service
     */
    public function test_migration_task_force_migration_disabled_setting_disabled() {

        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(true);
        }

        SettingsHelper::set_setting('forcedisablemigration', 0);
        $migrationtask = new migration_task();
        ob_start();
        $migrationtask->execute();
        $output = ob_get_contents();
        ob_get_clean();

        $this->assertFalse((bool)stristr($output, get_string('migrationdisabled', 'local_intellidata')));
    }

    /**
     * Validate migrated users.
     *
     * @param $data
     * @return void
     * @throws \dml_exception
     */
    private function validate_users($data) {
        global $DB;

        // Validate users count.
        $dbusers = $DB->get_records('user');
        $this->assertEquals(count($dbusers), count($data));

        // Validate users data.
        $fields = [
            'id' => 'id',
            'fullname' => 'fullname',
            'username' => 'username',
            'email' => 'email',
            'timecreated' => 'timecreated',
            'lang' => 'lang',
            'country' => 'country',
        ];

        foreach ($data as $datarow) {
            $user = $dbusers[$datarow->id];
            $user->fullname = fullname($user);

            $this->assertEquals(
                test_helper::filter_fields($datarow, $fields),
                test_helper::filter_fields($user, $fields)
            );
        }
    }

    /**
     * Validate migrated categories.
     *
     * @param $data
     * @return void
     * @throws \dml_exception
     */
    private function validate_categories($data) {
        global $DB;

        // Validate categories count.
        $dbcategories = $DB->get_records('course_categories');
        $this->assertEquals(count($dbcategories), count($data));

        // Validate categories data.
        $fields = [
            'id' => 'id',
            'parent' => 'parent',
            'name' => 'name',
            'path' => 'path',
            'visible' => 'visible',
            'timecreated' => 'timecreated',
        ];

        foreach ($data as $datarow) {
            $category = $dbcategories[$datarow->id];

            $this->assertEquals(
                test_helper::filter_fields($datarow, $fields),
                test_helper::filter_fields($category, $fields)
            );
        }
    }

    /**
     * Validate migrated courses.
     *
     * @param $data
     * @return void
     * @throws \dml_exception
     */
    private function validate_courses($data) {
        global $DB;

        // Validate courses count.
        $dbcourses = $DB->get_records_sql("SELECT * FROM {course} WHERE id > :id", ['id' => 1]);
        $this->assertEquals(count($dbcourses), count($data));

        // Validate courses data.
        $fields = [
            'id' => 'id',
            'fullname' => 'fullname',
            'startdate' => 'startdate',
            'enddate' => 'enddate',
            'timecreated' => 'timecreated',
            'visible' => 'visible',
        ];

        foreach ($data as $datarow) {
            $course = $dbcourses[$datarow->id];

            $this->assertEquals(
                test_helper::filter_fields($datarow, $fields),
                test_helper::filter_fields($course, $fields)
            );
        }
    }

    /**
     * Validate migrated cohorts.
     *
     * @param $data
     * @return void
     * @throws \dml_exception
     */
    private function validate_cohorts($data) {
        global $DB;

        // Validate cohorts count.
        $dbcohorts = $DB->get_records('cohort');
        $this->assertEquals(count($dbcohorts), count($data));

        // Validate cohorts data.
        $fields = [
            'id' => 'id',
            'idnumber' => 'idnumber',
            'name' => 'name',
            'visible' => 'visible',
            'timecreated' => 'timecreated',
            'timemodified' => 'timemodified',
        ];

        foreach ($data as $datarow) {
            $cohort = $dbcohorts[$datarow->id];

            $this->assertEquals(
                test_helper::filter_fields($datarow, $fields),
                test_helper::filter_fields($cohort, $fields)
            );
        }
    }
}
