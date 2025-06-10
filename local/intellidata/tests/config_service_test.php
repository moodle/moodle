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

use local_intellidata\persistent\datatypeconfig;
use local_intellidata\services\config_service;
use local_intellidata\services\datatypes_service;
use local_intellidata\helpers\SettingsHelper;

/**
 * Config service test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class config_service_test extends \advanced_testcase {

    /**
     * Test setup config.
     *
     * @return void
     * @throws dml_exception
     * @covers \local_intellidata\services\config_service::setup_config
     * @covers \local_intellidata\services\config_service::apply_optional_tables_events
     */
    public function test_setup_config() {
        global $DB;

        $this->resetAfterTest(true);

        $optionalconfigwithdeletedevent = [
            'tabletype' => datatypeconfig::TABLETYPE_OPTIONAL,
            'datatype' => datatypes_service::generate_optional_datatype('competency'),
            'deletedevent' => '\core\event\competency_deleted',
            'status' => datatypeconfig::STATUS_ENABLED,
            'timemodified_field' => 'timemodified',
            'rewritable' => 0,
            'events_tracking' => 0,
        ];

        $DB->execute("DELETE FROM {" . datatypeconfig::TABLE . "}");

        // Validate config empty.
        $this->assertFalse($DB->record_exists(datatypeconfig::TABLE, $optionalconfigwithdeletedevent));

        $configservice = new config_service(datatypes_service::get_all_datatypes());
        $configservice->setup_config(true);
        $config = $configservice->get_config();

        // Validate record from DB.
        $this->assertTrue($DB->record_exists(datatypeconfig::TABLE, $optionalconfigwithdeletedevent));

        // Validate record from cache.
        $this->assertTrue(isset($config[$optionalconfigwithdeletedevent['datatype']]));

        // Validate required datatype.
        $requiredconfigwithevent = [
            'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
            'datatype' => 'users',
            'status' => datatypeconfig::STATUS_ENABLED,
            'timemodified_field' => 'timemodified',
            'rewritable' => 0,
            'filterbyid' => 0,
            'events_tracking' => 1,
        ];
        // Validate record from DB.
        $this->assertTrue($DB->record_exists(datatypeconfig::TABLE, $requiredconfigwithevent));

        // Validate record from cache.
        $this->assertTrue(isset($config[$requiredconfigwithevent['datatype']]));

        // Validate required datatype.
        $requiredconfigrewritable = [
            'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
            'datatype' => 'roles',
            'status' => datatypeconfig::STATUS_ENABLED,
            'rewritable' => 1,
            'filterbyid' => 0,
            'events_tracking' => 0,
        ];
        // Validate record from DB.
        $this->assertTrue($DB->record_exists(datatypeconfig::TABLE, $requiredconfigrewritable));

        // Validate record from cache.
        $this->assertTrue(isset($config[$requiredconfigrewritable['datatype']]));
    }

    /**
     * Test get_exportids_config_optional method with checking plugin configuration.
     *
     * @return void
     * @throws dml_exception
     * @covers \local_intellidata\services\config_service::get_exportids_config_optional
     */
    public function test_get_exportids_config_optional_exportids_config() {

        $this->resetAfterTest();

        SettingsHelper::set_setting('exportids', 0);
        $this->assertFalse(config_service::get_exportids_config_optional(null));

        SettingsHelper::set_setting('exportids', 1);
        $this->assertTrue(config_service::get_exportids_config_optional(null));
    }

    /**
     * Test get_exportids_config_optional method with checking exportdeletedrecords.
     *
     * @return void
     * @throws dml_exception
     * @covers \local_intellidata\services\config_service::get_exportids_config_optional
     */
    public function test_get_exportids_config_optional_exportdeletedrecords_enabled() {

        $this->resetAfterTest();

        $datatype = new \stdClass();
        $datatype->deletedevent = '\core\event\competency_deleted';

        SettingsHelper::set_setting('exportdeletedrecords', 0);
        $this->assertTrue(config_service::get_exportids_config_optional($datatype));

        SettingsHelper::set_setting('exportdeletedrecords', 1);
        $this->assertFalse(config_service::get_exportids_config_optional($datatype));
    }

    /**
     * Test get_exportids_config_optional method with checking rewritable.
     *
     * @return void
     * @throws dml_exception
     * @covers \local_intellidata\services\config_service::get_exportids_config_optional
     */
    public function test_get_exportids_config_optional_exportdeletedrecords_rewritable() {

        $this->resetAfterTest();

        $datatype = new \stdClass();
        $datatype->rewritable = false;
        $this->assertTrue(config_service::get_exportids_config_optional($datatype));

        $datatype->rewritable = true;
        $this->assertFalse(config_service::get_exportids_config_optional($datatype));
    }

    /**
     * Test get_exportids_config_optional method with checking rewritable.
     *
     * @return void
     * @throws dml_exception
     * @covers \local_intellidata\services\config_service::delete_missed_tables_config
     */
    public function test_delete_required_optional_datatype_delete_missed_tables_config() {
        global $DB;
        $this->resetAfterTest();

        $datatype = 'userlogins';
        $record = datatypeconfig::get_record(['datatype' => $datatype]);
        $configservice = new config_service();

        $data = new \stdClass();
        $data->tabletype = datatypeconfig::TABLETYPE_OPTIONAL;
        $configservice->save_config($record, $data);

        $configservice = new config_service(datatypes_service::get_all_optional_datatypes());
        $configservice->setup_config(false);

        // Validate record from DB.
        $this->assertTrue($DB->record_exists(datatypeconfig::TABLE, ['datatype' => $datatype]));

        // Validate record from cache.
        $config = $configservice->get_config();
        $this->assertTrue(isset($config[$datatype]));
    }

    /**
     * Test cache_config method.
     *
     * @return void
     * @covers \local_intellidata\services\config_service::cache_config
     */
    public function test_cache_config() {

        $this->resetAfterTest();

        $configservice = new config_service();
        $config = $configservice->get_config();
        $configservice->cache_config();

        $this->assertEquals($config, $configservice->get_config());
    }
}
