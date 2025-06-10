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
 * Config repository test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata;

use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\config_repository;
use local_intellidata\persistent\datatypeconfig;
use local_intellidata\services\datatypes_service;
use stdClass;

/**
 * Config repository test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class config_repository_test extends \advanced_testcase {

    /**
     * Test class construct.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::__construct
     */
    public function test_init_cache() {
        $this->resetAfterTest();

        // Validate enabled cache.
        $configrepository = new config_repository();
        $this->assertInstanceOf(
            'local_intellidata\repositories\config\cache_config_repository',
            $configrepository->repo
        );
    }

    /**
     * Test class construct.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::__construct
     */
    public function test_init_db() {
        $this->resetAfterTest();

        // Validate enabled cache.
        SettingsHelper::set_setting('cacheconfig', 0);
        $configrepository = new config_repository();
        $this->assertInstanceOf(
            'local_intellidata\repositories\config\database_config_repository',
            $configrepository->repo
        );
    }

    /**
     * Test get_repository() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_repository
     */
    public function test_get_repository_cache() {
        $this->resetAfterTest();

        // Validate enabled cache.
        $this->assertInstanceOf(
            'local_intellidata\repositories\config\cache_config_repository',
            config_repository::get_repository()
        );
    }

    /**
     * Test get_repository() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_repository
     */
    public function test_get_repository_db() {
        $this->resetAfterTest();

        // Validate enabled cache.
        SettingsHelper::set_setting('cacheconfig', 0);
        $this->assertInstanceOf(
            'local_intellidata\repositories\config\database_config_repository',
            config_repository::get_repository()
        );
    }

    /**
     * Test get_config() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_config
     * @covers \local_intellidata\repositories\config\cache_config_repository::get_config
     */
    public function test_get_config_required_with_cache() {
        $this->resetAfterTest();

        $datatype = 'users';

        // Validate config from cache.
        $configrepository = new config_repository();
        $config = $configrepository->get_config();

        $this->assertTrue(isset($config[$datatype]));
        $this->assertEquals(datatypeconfig::TABLETYPE_REQUIRED, $config[$datatype]->tabletype);
        $this->assertEquals(1, $config[$datatype]->status);
        $this->assertEquals('timemodified', $config[$datatype]->timemodified_field);
        $this->assertEquals(0, $config[$datatype]->rewritable);
        $this->assertEquals(0, $config[$datatype]->filterbyid);
        $this->assertEquals(1, $config[$datatype]->events_tracking);
        $this->assertEquals([], $config[$datatype]->params);
    }

    /**
     * Test get_config() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_config
     * @covers \local_intellidata\repositories\config\database_config_repository::get_config
     */
    public function test_get_config_required_wuth_db() {
        $this->resetAfterTest();

        $datatype = 'users';

        // Validate config from DB.
        SettingsHelper::set_setting('cacheconfig', 0);
        $configrepository = new config_repository();
        $config = $configrepository->get_config();

        $this->assertTrue(isset($config[$datatype]));
        $this->assertEquals(datatypeconfig::TABLETYPE_REQUIRED, $config[$datatype]->tabletype);
        $this->assertEquals(1, $config[$datatype]->status);
        $this->assertEquals('timemodified', $config[$datatype]->timemodified_field);
        $this->assertEquals(0, $config[$datatype]->rewritable);
        $this->assertEquals(0, $config[$datatype]->filterbyid);
        $this->assertEquals(1, $config[$datatype]->events_tracking);
        $this->assertEquals([], $config[$datatype]->params);
    }

    /**
     * Test get_optional_datatypes() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_optional_datatypes
     * @covers \local_intellidata\repositories\config\cache_config_repository::get_optional_datatypes
     */
    public function test_get_optional_datatypes_from_cache() {
        $this->resetAfterTest();

        // Validate config from cache.
        $config = config_repository::get_optional_datatypes();

        $excludeddatatype = datatypes_service::generate_optional_datatype('local_intellidata_config');
        $enableddatatype = datatypes_service::generate_optional_datatype('user_enrolments');

        $this->assertFalse(isset($config[$excludeddatatype]));

        // Validate config status enabled.
        $config = config_repository::get_optional_datatypes(datatypeconfig::STATUS_ENABLED);
        $this->assertTrue(isset($config[$enableddatatype]));
        $this->assertEquals('\core\event\user_enrolment_deleted', $config[$enableddatatype]->deletedevent);

        // Validate config status disabled.
        $config = config_repository::get_optional_datatypes(datatypeconfig::STATUS_DISABLED);
        $this->assertFalse(isset($config[$enableddatatype]));
    }

    /**
     * Test get_optional_datatypes() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_optional_datatypes
     * @covers \local_intellidata\repositories\config\database_config_repository::get_optional_datatypes
     */
    public function test_get_optional_datatypes_from_db() {
        $this->resetAfterTest();

        // Validate config from cache.
        $config = config_repository::get_optional_datatypes();

        $excludeddatatype = datatypes_service::generate_optional_datatype('local_intellidata_config');
        $enableddatatype = datatypes_service::generate_optional_datatype('user_enrolments');

        $this->assertFalse(isset($config[$excludeddatatype]));

        // Validate config status enabled.
        $config = config_repository::get_optional_datatypes(datatypeconfig::STATUS_ENABLED);
        $this->assertTrue(isset($config[$enableddatatype]));
        $this->assertEquals('\core\event\user_enrolment_deleted', $config[$enableddatatype]->deletedevent);

        // Validate config status disabled.
        $config = config_repository::get_optional_datatypes(datatypeconfig::STATUS_DISABLED);
        $this->assertFalse(isset($config[$enableddatatype]));
    }

    /**
     * Test get_logs_datatypes() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_logs_datatypes
     * @covers \local_intellidata\repositories\config_repository::save
     * @covers \local_intellidata\repositories\config\cache_config_repository::get_logs_datatypes
     * @covers \local_intellidata\repositories\config\database_config_repository::get_logs_datatypes
     */
    public function test_get_logs_datatypes() {
        $this->resetAfterTest();

        $config = config_repository::get_logs_datatypes();
        $this->assertEquals([], $config);

        $configrepository = new config_repository();

        // Validate config from cache.
        $data = new stdClass();
        $data->datatype = 'course_viewed';
        $data->timemodified_field = 'timecreated';
        $data->rewritable = datatypeconfig::STATUS_DISABLED;
        $data->filterbyid = datatypeconfig::STATUS_DISABLED;
        $data->tabletype = datatypeconfig::TABLETYPE_LOGS;
        $data->events_tracking = datatypeconfig::STATUS_ENABLED;
        $data->status = datatypeconfig::STATUS_ENABLED;

        // Save config.
        $configrepository->save($data->datatype, $data);

        $config = config_repository::get_logs_datatypes();
        $this->assertTrue(isset($config[$data->datatype]));
        $this->assertEquals($data->timemodified_field, $config[$data->datatype]->timemodified_field);
        $this->assertEquals($data->rewritable, $config[$data->datatype]->rewritable);
        $this->assertEquals($data->filterbyid, $config[$data->datatype]->filterbyid);
        $this->assertEquals($data->events_tracking, $config[$data->datatype]->events_tracking);
    }

    /**
     * Test get_record() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::get_record
     */
    public function test_get_record() {
        $this->resetAfterTest();

        $configrepository = new config_repository();

        $this->assertInstanceOf(
            'local_intellidata\persistent\datatypeconfig',
            $configrepository->get_record(['datatype' => 'users'])
        );

        $this->assertFalse($configrepository->get_record(['datatype' => 'notexists']));
    }

    /**
     * Test delete() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::delete
     */
    public function test_delete() {
        $this->resetAfterTest();

        $configrepository = new config_repository();

        // Validate config from cache.
        $data = new stdClass();
        $data->datatype = 'course_module_viewed';
        $data->tabletype = datatypeconfig::TABLETYPE_LOGS;
        $data->status = datatypeconfig::STATUS_ENABLED;

        // Save config.
        $configrepository->save($data->datatype, $data);

        $this->assertInstanceOf(
            'local_intellidata\persistent\datatypeconfig',
            $configrepository->get_record(['datatype' => $data->datatype])
        );

        // Delete config.
        $this->assertTrue($configrepository->delete($data->datatype));
        $this->assertFalse($configrepository->get_record(['datatype' => $data->datatype]));

        $this->assertFalse($configrepository->delete('notexists'));
    }

    /**
     * Test cache_config() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::cache_config
     * @covers \local_intellidata\repositories\config\cache_config_repository::get_logs_datatypes
     */
    public function test_cache_config_from_cache() {
        $this->resetAfterTest();

        $configrepository = new config_repository();

        $this->assertEquals($configrepository->get_config(), $configrepository->cache_config());
    }

    /**
     * Test cache_config() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\config_repository::cache_config
     * @covers \local_intellidata\repositories\config\database_config_repository::get_logs_datatypes
     */
    public function test_cache_config_from_db() {
        $this->resetAfterTest();

        SettingsHelper::set_setting('cacheconfig', 0);

        $configrepository = new config_repository();

        $this->assertEquals([], $configrepository->cache_config());
    }

}
