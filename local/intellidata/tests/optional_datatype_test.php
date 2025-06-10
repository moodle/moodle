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
 * Export methods test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\persistent\datatypeconfig;
use local_intellidata\services\datatypes_service;
use local_intellidata\services\dbschema_service;
use local_intellidata\services\config_service;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');


/**
 * Export methods test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class optional_datatype_test extends \advanced_testcase {

    public function setUp(): void {
        $this->setAdminUser();

        setup_helper::setup_tests_config();
    }

    /**
     * Test get all optional tables.
     *
     * @return void
     * @covers \local_intellidata\services\dbschema_service::get_tableslist
     */
    public function test_get_tableslist() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        $userdatatype = 'user';
        $dbschemaservice = new dbschema_service();
        $alltables = $dbschemaservice->get_tableslist();

        test_helper::assert_is_array(
            $this,
            $alltables
        );

        $this->assertArrayHasKey($userdatatype, $alltables);
    }

    /**
     * Test get all optional datatypes.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_all_optional_datatypes
     */
    public function test_get_all_optional_datatype() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        test_helper::assert_is_array(
            $this,
            datatypes_service::get_all_optional_datatypes()
        );
    }

    /**
     * Test get optional table.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_optional_table
     */
    public function test_format_optional_datatypes() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }
        $dtservice = datatypes_service::class;

        $tablename = 'user';
        $datatype = $dtservice::generate_optional_datatype($tablename);
        $this->assertEquals($datatype, datatypeconfig::OPTIONAL_TABLE_PREFIX . $tablename);

        $table = $dtservice::get_optional_table($tablename);
        $this->assertEquals($tablename, $table);

        $alloptionaldatatypes = datatypes_service::get_all_optional_datatypes();

        $this->assertArrayHasKey($datatype, $alloptionaldatatypes);

        test_helper::assert_is_array(
            $this,
            $alloptionaldatatypes
        );

        $this->assertEquals($tablename, $alloptionaldatatypes[$datatype]['table']);
    }

    /**
     * Test get optional table.
     *
     * @return void
     * @covers \local_intellidata\services\datatypes_service::get_optional_table
     */
    public function test_export_datatype() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        $configservice = new config_service(datatypes_service::get_all_datatypes());
        $configservice->setup_config(false);

        $dbschemaservice = new dbschema_service();

        $exportdata = $dbschemaservice->export();

        test_helper::assert_is_array(
            $this,
            datatypes_service::get_all_optional_datatypes()
        );

        $datatype = datatypes_service::generate_optional_datatype('user');
        $this->assertArrayHasKey($datatype, $exportdata);

        $this->assertArrayHasKey('name', $exportdata[$datatype]);

        $this->assertArrayHasKey('fields', $exportdata[$datatype]);

        $this->assertFalse($dbschemaservice->table_exists($datatype));
    }
}
