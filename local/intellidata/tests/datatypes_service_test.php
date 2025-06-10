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
 * Database repository migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\config_repository;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\repositories\required_tables_repository;
use local_intellidata\services\datatypes_service;
use local_intellidata\persistent\datatypeconfig;

/**
 * Export_id_repository migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class datatypes_service_test extends \advanced_testcase {

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        setup_helper::setup_tests_config();
    }

    /**
     * Test init_migration method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::init_migration
     */
    public function test_init_migration_with_existing_class() {
        $datatype = ['migration' => 'users\migration'];
        $migration = datatypes_service::init_migration($datatype);
        $this->assertInstanceOf(\local_intellidata\entities\users\migration::class, $migration);
    }

    /**
     * Test init_migration method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::init_migration
     */
    public function test_init_migration_with_not_existing_class() {
        $datatype = ['migration' => 'NonExistingClass'];
        $migration = datatypes_service::init_migration($datatype);
        $this->assertNull($migration);
    }

    /**
     * Test get_datatype_entity_path method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_datatype_entity_path
     */
    public function test_get_datatype_entity_path_with_existing_entity() {
        $datatype = ['entity' => 'users\user'];
        $entitypath = datatypes_service::get_datatype_entity_path($datatype);
        $this->assertEquals('users\user', $entitypath);
    }

    /**
     * Test get_datatype_entity_path method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_datatype_entity_path
     */
    public function test_get_datatype_entity_path_with_empty_entity() {
        $entitypath = datatypes_service::get_datatype_entity_path([]);
        $this->assertEquals('', $entitypath);
    }

    /**
     * Test get_datatype_entity_class method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_datatype_entity_class
     */
    public function test_get_datatype_entity_class_with_existing_entity() {
        $entitypath = 'users\user';
        $entityclass = datatypes_service::get_datatype_entity_class($entitypath);
        $entity = new $entityclass();
        $this->assertInstanceOf(\local_intellidata\entities\users\user::class, $entity);
    }

    /**
     * Test get_datatype_entity_class method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_datatype_entity_class
     */
    public function test_get_datatype_entity_class_with_empty_entity() {
        $entityclass = datatypes_service::get_datatype_entity_class();
        $this->assertEquals('\local_intellidata\entities\\', $entityclass);
    }

    /**
     * Test init_entity method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::init_entity
     */
    public function test_init_entity_with_existing_class() {
        $datatype = ['entity' => 'users\user', 'name' => 'users'];
        $entity = datatypes_service::init_entity($datatype, []);
        $this->assertInstanceOf(\local_intellidata\entities\users\user::class, $entity);
    }

    /**
     * Test init_entity method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::init_entity
     */
    public function test_init_entity_with_not_existing_class() {
        $datatype = ['entity' => 'notExitsts', 'name' => 'users'];
        $entity = datatypes_service::init_entity($datatype, []);
        $this->assertInstanceOf(\local_intellidata\entities\custom\entity::class, $entity);
    }

    /**
     * Test get_required_datatypes method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_required_datatypes
     */
    public function test_get_required_datatypes() {

        $datatypes = datatypes_service::get_required_datatypes();

        $defaultdatatypes = [
            'users', 'categories', 'courses', 'enrolments', 'roleassignments', 'cohorts',
            'coursegroups', 'coursegroupmembers', 'cohortmembers', 'coursecompletions', 'activities',
            'activitycompletions', 'usergrades', 'gradeitems', 'roles', 'modules', 'forumdiscussions',
            'forumposts', 'quizattempts', 'quizquestions', 'quizquestionrelations', 'quizquestionattempts',
            'quizquestionattemptsteps', 'quizquestionattemptstepsdata', 'assignmentsubmissions',
            'ltisubmittion', 'coursesections', 'ltitypes', 'survey', 'surveyanswers', 'tracking',
            'trackinglog', 'trackinglogdetail', 'userinfocategories', 'userinfofields',
            'userinfodatas', 'participation', 'userlogins',
        ];
        $this->assertEquals($defaultdatatypes, array_keys($datatypes));

        foreach ($datatypes as $datatype) {
            $this->assertTrue(!empty($datatype['name']));
            $this->assertEquals(datatypeconfig::TABLETYPE_REQUIRED, $datatype['tabletype']);
            $this->assertTrue(!empty($datatype['migration']));
            $this->assertTrue(!empty($datatype['entity']));

            // Validate datatype should have observer if table is empty.
            if (empty($datatype['table'])) {
                $this->assertTrue(!empty($datatype['observer']));
            }

            // Validate datatype should have timemodified_field if observer and rewritable are empty.
            if (empty($datatype['observer']) && empty($datatype['rewritable']) && empty($datatype['filterbyid'])) {
                $this->assertTrue(!empty($datatype['timemodified_field']));
            }

            // Validate datatype should have timemodified_field if observer and rewritable are empty.
            if (empty($datatype['observer']) && empty($datatype['rewritable']) && empty($datatype['filterbyid'])) {
                $this->assertTrue(!empty($datatype['timemodified_field']));
            }

            // Validate datatype should have timemodified_field if observer and rewritable are empty.
            if (empty($datatype['observer']) && empty($datatype['rewritable']) && empty($datatype['timemodified_field'])) {
                $this->assertTrue(!empty($datatype['filterbyid']));
            }

            // Validate datatype should have timemodified_field if observer and rewritable are empty.
            if (empty($datatype['observer']) && empty($datatype['filterbyid']) && empty($datatype['timemodified_field'])) {
                $this->assertTrue(!empty($datatype['rewritable']));
            }
        }
    }

    /**
     * Test format_required_datatypes method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::format_required_datatypes
     */
    public function test_format_required_datatypes_eventstracking_disabled() {

        SettingsHelper::set_setting('eventstracking', 0);

        $datatypes = [
            'users' => [
                'name' => 'users',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'user',
                'migration' => 'users\migration',
                'entity' => 'users\user',
                'observer' => 'users\observer',
                'rewritable' => false,
                'timemodified_field' => 'timemodified',
                'filterbyid' => false,
                'databaseexport' => false,
            ],
            'coursecompletions' => [
                'name' => 'coursecompletions',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'migration' => 'coursecompletions\migration',
                'entity' => 'coursecompletions\coursecompletion',
                'observer' => 'coursecompletions\observer',
                'rewritable' => false,
                'timemodified_field' => false,
                'filterbyid' => false,
                'databaseexport' => false,
            ],
            'roles' => [
                'name' => 'roles',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'role',
                'migration' => 'roles\migration',
                'entity' => 'roles\role',
                'observer' => false,
                'rewritable' => true,
                'timemodified_field' => false,
                'filterbyid' => false,
                'databaseexport' => true,
                'exportids' => false,
            ],
            'quizquestionrelations' => [
                'name' => 'quizquestionrelations',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'quiz_slots',
                'migration' => 'quizquestionrelations\migration',
                'entity' => 'quizquestionrelations\quizquestionrelation',
                'observer' => false,
                'rewritable' => false,
                'timemodified_field' => false,
                'filterbyid' => true,
                'databaseexport' => true,
                'exportids' => true,
            ],
            'quizquestionattempts' => [
                'name' => 'quizquestionattempts',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'question_attempts',
                'migration' => 'quizquestionanswers\quamigration',
                'entity' => 'quizquestionanswers\quizquestionattempts',
                'observer' => false,
                'rewritable' => false,
                'timemodified_field' => 'timemodified',
                'filterbyid' => false,
                'databaseexport' => true,
                'exportids' => false,
            ],
            'userinfofields' => [
                'name' => 'userinfofields',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'migration' => 'userinfofields\migration',
                'entity' => 'userinfofields\userinfofield',
                'observer' => false,
                'rewritable' => false,
                'timemodified_field' => 'timemodified',
                'filterbyid' => false,
                'databaseexport' => false,
            ],
        ];
        $datatype = datatypes_service::format_required_datatypes($datatypes);

        // Validate users datatype will be exported with databaseexport.
        $this->assertEquals(true, $datatype['users']['databaseexport']);

        // Validate coursecompletions will be exported with events only.
        $this->assertEquals(false, $datatype['coursecompletions']['databaseexport']);

        // Validate users datatype will not be exported ids.
        $this->assertTrue(empty($datatype['users']['exportids']));

        // Validate roles will not be exported ids.
        $this->assertEquals(false, $datatype['roles']['exportids']);

        // Validate quizquestionrelations will be exported ids.
        $this->assertEquals(true, $datatype['quizquestionrelations']['exportids']);

        // Validate quizquestionattempts will not be exported ids.
        $this->assertEquals(false, $datatype['quizquestionattempts']['exportids']);

        // Validate userinfofields will be exported ids.
        $this->assertEquals(true, $datatype['userinfofields']['exportids']);
    }

    /**
     * Test format_required_datatypes method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::format_required_datatypes
     */
    public function test_format_required_datatypes_eventstracking_enabled() {

        SettingsHelper::set_setting('eventstracking', 1);

        $datatypes = [
            'users' => [
                'name' => 'users',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'user',
                'migration' => 'users\migration',
                'entity' => 'users\user',
                'observer' => 'users\observer',
                'rewritable' => false,
                'timemodified_field' => 'timemodified',
                'filterbyid' => false,
                'databaseexport' => false,
            ],
            'coursecompletions' => [
                'name' => 'coursecompletions',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'migration' => 'coursecompletions\migration',
                'entity' => 'coursecompletions\coursecompletion',
                'observer' => 'coursecompletions\observer',
                'rewritable' => false,
                'timemodified_field' => false,
                'filterbyid' => false,
                'databaseexport' => false,
            ],
            'roles' => [
                'name' => 'roles',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'role',
                'migration' => 'roles\migration',
                'entity' => 'roles\role',
                'observer' => false,
                'rewritable' => true,
                'timemodified_field' => false,
                'filterbyid' => false,
                'databaseexport' => true,
                'exportids' => false,
            ],
            'quizquestionrelations' => [
                'name' => 'quizquestionrelations',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'quiz_slots',
                'migration' => 'quizquestionrelations\migration',
                'entity' => 'quizquestionrelations\quizquestionrelation',
                'observer' => false,
                'rewritable' => false,
                'timemodified_field' => false,
                'filterbyid' => true,
                'databaseexport' => true,
                'exportids' => true,
            ],
            'quizquestionattempts' => [
                'name' => 'quizquestionattempts',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'table' => 'question_attempts',
                'migration' => 'quizquestionanswers\quamigration',
                'entity' => 'quizquestionanswers\quizquestionattempts',
                'observer' => false,
                'rewritable' => false,
                'timemodified_field' => 'timemodified',
                'filterbyid' => false,
                'databaseexport' => true,
                'exportids' => false,
            ],
            'userinfofields' => [
                'name' => 'userinfofields',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'migration' => 'userinfofields\migration',
                'entity' => 'userinfofields\userinfofield',
                'observer' => false,
                'rewritable' => false,
                'timemodified_field' => 'timemodified',
                'filterbyid' => false,
                'databaseexport' => false,
            ],
        ];
        $datatype = datatypes_service::format_required_datatypes($datatypes);

        // Validate users datatype will not be exported with databaseexport.
        $this->assertEquals(false, $datatype['users']['databaseexport']);

        // Validate coursecompletions will be exported with events.
        $this->assertEquals(false, $datatype['coursecompletions']['databaseexport']);

        // Validate users datatype will not export ids.
        $this->assertTrue(empty($datatype['users']['exportids']));

        // Validate roles will not export ids.
        $this->assertEquals(false, $datatype['roles']['exportids']);

        // Validate quizquestionrelations will export ids.
        $this->assertEquals(true, $datatype['quizquestionrelations']['exportids']);

        // Validate quizquestionattempts will not export ids.
        $this->assertEquals(false, $datatype['quizquestionattempts']['exportids']);

        // Validate userinfofields will not export ids.
        $this->assertTrue(empty($datatype['userinfofields']['exportids']));
    }

    /**
     * Test get_optional_datatypes_for_export method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_optional_datatypes_for_export
     */
    public function test_get_optional_datatypes_for_export_with_timemodified() {

        $datatypename = datatypes_service::generate_optional_datatype('enrol');

        // Enable enrol datatype in config.
        $configrepository = new config_repository();
        $configrepository->enable($datatypename);

        // Enable enrol datatype in export.
        $exportlogrepository = new export_log_repository();
        $exportlogrepository->insert_datatype($datatypename);

        $datatypes = datatypes_service::get_optional_datatypes_for_export();

        // Validate datatype exists.
        $this->assertTrue(isset($datatypes[$datatypename]));

        $datatype = $datatypes[$datatypename];

        $this->assertEquals($datatypename, $datatype['name']);
        $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $datatype['tabletype']);
        $this->assertEquals(datatypes_service::get_optional_table($datatypename), $datatype['table']);
        $this->assertFalse($datatype['migration']);
        $this->assertFalse($datatype['entity']);
        $this->assertFalse($datatype['observer']);
        $this->assertEquals('timemodified', $datatype['timemodified_field']);
        $this->assertEquals(false, $datatype['filterbyid']);
        $this->assertEquals(false, $datatype['rewritable']);
        $this->assertTrue($datatype['databaseexport']);
        $this->assertFalse($datatype['exportids']);
    }

    /**
     * Test get_optional_datatypes_for_export method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_optional_datatypes_for_export
     */
    public function test_get_optional_datatypes_for_export_with_filterbyid() {

        $datatypename = datatypes_service::generate_optional_datatype('course_format_options');

        // Enable enrol datatype in config.
        $configrepository = new config_repository();
        $configrepository->enable($datatypename);

        // Enable enrol datatype in export.
        $exportlogrepository = new export_log_repository();
        $exportlogrepository->insert_datatype($datatypename);

        $datatypes = datatypes_service::get_optional_datatypes_for_export();

        // Validate datatype exists.
        $this->assertTrue(isset($datatypes[$datatypename]));

        $datatype = $datatypes[$datatypename];
        $this->assertEquals($datatypename, $datatype['name']);
        $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $datatype['tabletype']);
        $this->assertEquals(datatypes_service::get_optional_table($datatypename), $datatype['table']);
        $this->assertFalse($datatype['migration']);
        $this->assertFalse($datatype['entity']);
        $this->assertFalse($datatype['observer']);
        $this->assertNull($datatype['timemodified_field']);
        $this->assertEquals(true, $datatype['filterbyid']);
        $this->assertEquals(false, $datatype['rewritable']);
        $this->assertTrue($datatype['databaseexport']);
        $this->assertTrue($datatype['exportids']);
    }

    /**
     * Test get_optional_datatypes_for_export method.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_optional_datatypes_for_export
     */
    public function test_get_optional_datatypes_for_export_with_rewritable() {

        $datatypename = datatypes_service::generate_optional_datatype('lesson_attempts');

        // Enable enrol datatype in config.
        $configrepository = new config_repository();
        $configrepository->enable($datatypename);

        // Enable enrol datatype in export.
        $exportlogrepository = new export_log_repository();
        $exportlogrepository->insert_datatype($datatypename);

        $datatypes = datatypes_service::get_optional_datatypes_for_export();

        // Validate datatype exists.
        $this->assertTrue(isset($datatypes[$datatypename]));

        $datatype = $datatypes[$datatypename];
        $this->assertEquals($datatypename, $datatype['name']);
        $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $datatype['tabletype']);
        $this->assertEquals(datatypes_service::get_optional_table($datatypename), $datatype['table']);
        $this->assertFalse($datatype['migration']);
        $this->assertFalse($datatype['entity']);
        $this->assertFalse($datatype['observer']);
        $this->assertNull($datatype['timemodified_field']);
        $this->assertFalse($datatype['filterbyid']);
        $this->assertTrue($datatype['rewritable']);
        $this->assertTrue($datatype['databaseexport']);
        $this->assertFalse($datatype['exportids']);
    }

    /**
     * Test enabling required native tables.
     *
     * @return void
     * @throws \coding_exception
     * @covers \local_intellidata\services\datatypes_service::enable_required_native_datatypes
     */
    public function test_enable_required_native_datatypes() {

        $exportlogrepository = new export_log_repository();
        $requirednativetables = required_tables_repository::get_required_native_tables();

        $configrepository = new config_repository();
        $config = $configrepository->get_config();

        // Initial plugin installation validation.
        foreach ($requirednativetables as $table) {

            $datatypename = datatypes_service::generate_optional_datatype($table);
            $datatype = $exportlogrepository->get_datatype($datatypename);

            if (isset($config[$datatypename])) {
                $this->assertEquals($datatypename, $datatype->get('datatype'));
            } else {
                $this->assertFalse($datatype);
            }
        }

        // Remove datatypes from export.
        foreach ($requirednativetables as $table) {
            $datatypename = datatypes_service::generate_optional_datatype($table);
            $exportlogrepository->remove_datatype($datatypename);

            $this->assertFalse($exportlogrepository->get_datatype($datatypename));
        }

        datatypes_service::enable_required_native_datatypes();

        // Validate datatypes are enabled.
        foreach ($requirednativetables as $table) {
            $datatypename = datatypes_service::generate_optional_datatype($table);
            $datatype = $exportlogrepository->get_datatype($datatypename);

            if (isset($config[$datatypename])) {
                $this->assertEquals($datatypename, $datatype->get('datatype'));
            } else {
                $this->assertFalse($datatype);
            }
        }
    }

}
