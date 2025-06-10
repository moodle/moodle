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
 * Config migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');

use local_intellidata\persistent\datatypeconfig;
use local_intellidata\services\config_service;
use local_intellidata\services\datatypes_service;
use local_intellidata\task\export_adhoc_task;

/**
 * Config migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class config_test extends \advanced_testcase {

    /**
     * Setup test.
     *
     * @return void
     */
    public function setUp(): void {
        $this->setAdminUser();

        setup_helper::setup_tests_config();
    }

    /**
     * Test required datatype.
     *
     * @return void
     * @throws \coding_exception
     * @covers \local_intellidata\services\datatypes_service::get_required_datatypes
     */
    public function test_required_datatype() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }
        $reqdatatype = key(datatypes_service::get_required_datatypes());
        if ($record = datatypeconfig::get_record(['datatype' => $reqdatatype])) {
            $this->assertTrue($record->is_required_by_default());
        }

        $reqdatatype = key(datatypes_service::get_all_optional_datatypes());
        if ($record = datatypeconfig::get_record(['datatype' => $reqdatatype])) {
            $this->assertFalse($record->is_required_by_default());
        }
    }

    /**
     * Test config save required.
     *
     * @return void
     * @throws \coding_exception
     * @covers \local_intellidata\services\config_service::save_config
     */
    public function test_save_config_required() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }
        $configservice = new config_service();

        $reqdatatype = key(datatypes_service::get_required_datatypes());
        if ($record = datatypeconfig::get_record(['datatype' => $reqdatatype])) {
            $data = [
                'timemodified_field' => 'test_field',
                'tabletype' => datatypeconfig::TABLETYPE_OPTIONAL,
            ];
            $configservice->save_config($record, (object)$data);

            // Validate record from DB.
            $record = datatypeconfig::get_record(['datatype' => $reqdatatype]);
            $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $record->get('tabletype'));

            $this->assertFalse(($record->get('timemodified_field') == 'test_field') || ($record->get('timemodified_field') == ''));

            // Validate record from cache.
            $config = $configservice->get_config();
            $this->assertTrue(isset($config[$reqdatatype]));

            $record = $config[$reqdatatype];
            $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $record->tabletype);
            $this->assertFalse(($record->timemodified_field === 'test_field') || ($record->timemodified_field === ''));
        }
    }

    /**
     * Test config save optional.
     *
     * @return void
     * @throws \coding_exception
     * @covers \local_intellidata\services\config_service::save_config
     */
    public function test_save_config_optional() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }
        $configservice = new config_service();

        $optdatatype = key(datatypes_service::get_all_optional_datatypes());
        if ($record = datatypeconfig::get_record(['datatype' => $optdatatype])) {
            $data = [
                'timemodified_field' => 'test_field',
                'tabletype' => datatypeconfig::TABLETYPE_REQUIRED,
                'events_tracking' => $record->get('events_tracking'),
                'filterbyid' => false,
                'rewritable' => false,
                'status' => $record->get('status'),
                'tableindex' => $record->get('tableindex'),
                'enableexport' => false,
            ];
            $configservice->save_config($record, (object)$data);

            // Validate record from DB.
            $record = datatypeconfig::get_record(['datatype' => $optdatatype]);
            $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $record->get('tabletype'));
            $this->assertTrue($record->get('timemodified_field') === '');

            // Validate record from cache.
            $config = $configservice->get_config();
            $this->assertTrue(isset($config[$optdatatype]));

            $record = $config[$optdatatype];
            $this->assertEquals(datatypeconfig::TABLETYPE_OPTIONAL, $record->tabletype);
            $this->assertTrue($record->timemodified_field === '');
        }
    }

    /**
     * Test config reset to default.
     *
     * @return void
     * @throws \coding_exception
     * @covers \local_intellidata\services\config_service::save_config
     * @covers \local_intellidata\services\config_service::create_config
     */
    public function test_reset_to_default() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        $configservice = new config_service();

        $reqdatatype = key(datatypes_service::get_required_datatypes());
        if ($record = datatypeconfig::get_record(['datatype' => $reqdatatype])) {
            $data = [
                'tabletype' => datatypeconfig::TABLETYPE_OPTIONAL,
            ];
            // Change datatype paramert - tabletype.
            $configservice->save_config($record, (object)$data);

            $datatypeconfig = datatypes_service::get_datatype($reqdatatype);
            $datatypeconfig['timemodifiedfields'] = config_service::get_available_timemodified_fields($datatypeconfig['table']);
            $configservice->create_config($reqdatatype, $datatypeconfig);

            // Validate record from DB.
            $record = datatypeconfig::get_record(['datatype' => $reqdatatype]);
            $this->assertEquals(datatypeconfig::TABLETYPE_REQUIRED, $record->get('tabletype'));

            // Validate record from cache.
            $config = $configservice->get_config();
            $this->assertTrue(isset($config[$reqdatatype]));

            $record = $config[$reqdatatype];
            $this->assertEquals(datatypeconfig::TABLETYPE_REQUIRED, $record->tabletype);
        }
    }

    /**
     * Test config reset.
     *
     * @return void
     * @throws \coding_exception
     * @covers \local_intellidata\services\config_service::reset_config_datatype
     */
    public function test_reset_config() {
        global $DB;
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        $configservice = new config_service();

        $reqdatatype = key(datatypes_service::get_required_datatypes());
        if ($record = datatypeconfig::get_record(['datatype' => $reqdatatype])) {
            $data = [
                'tabletype' => datatypeconfig::TABLETYPE_OPTIONAL,
            ];
            // Change datatype paramert - tabletype.
            $configservice->save_config($record, (object)$data);
            $record = datatypeconfig::get_record(['datatype' => $reqdatatype]);

            $configservice->reset_config_datatype($record);

            // Check сreation task after reset.
            $this->assertTrue($DB->record_exists('task_adhoc', ['classname' => "\\" . export_adhoc_task::class]));
        }
    }
}
