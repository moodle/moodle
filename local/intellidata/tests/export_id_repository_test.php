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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');

use local_intellidata\repositories\export_id_repository;
use local_intellidata\persistent\export_ids;

/**
 * Export_id_repository migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class export_id_repository_test extends \advanced_testcase {

    /**
     * Test save() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\export_id_repository::save
     */
    public function test_save() {
        global $DB;

        $DB->delete_records(export_ids::TABLE);

        $this->resetAfterTest(true);

        $exportidrepository = new export_id_repository();
        $this->assertInstanceOf('local_intellidata\repositories\export_id_repository', $exportidrepository);

        // Validate empty table.
        $this->assertEquals(0, export_ids::count_records());

        $exportidrepository->save([]);
        $this->assertEquals(0, export_ids::count_records());

        // Validate record creation.
        $records = [];
        $records[] = [
            'datatype' => 'tracking',
            'dataid' => 1,
            'timecreated' => time(),
        ];
        $records[] = [
            'datatype' => 'tracking',
            'dataid' => 2,
            'timecreated' => time(),
        ];
        $exportidrepository->save($records);
        $this->assertEquals(count($records), export_ids::count_records());
    }

    /**
     * Test for clean_deleted_ids() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\export_id_repository::save
     * @covers \local_intellidata\repositories\export_id_repository::clean_deleted_ids
     */
    public function test_clean_deleted_ids() {
        global $DB;

        $DB->delete_records(export_ids::TABLE);
        $this->resetAfterTest(true);

        $exportidrepository = new export_id_repository();
        $this->assertInstanceOf('local_intellidata\repositories\export_id_repository', $exportidrepository);

        // Validate empty table.
        $this->assertEquals(0, export_ids::count_records());

        // Validate record creation.
        $records = []; $recordsnum = 10; $datatype = 'tracking';
        for ($i = 1; $i <= $recordsnum; $i++) {
            $records[] = [
                'datatype' => $datatype,
                'dataid' => $i,
                'timecreated' => time(),
            ];
        }

        $exportidrepository->save($records);
        $this->assertEquals($recordsnum, export_ids::count_records(['datatype' => $datatype]));

        // Validate other datatype deletion.
        $exportidrepository->clean_deleted_ids('users', []);
        $this->assertEquals($recordsnum, export_ids::count_records(['datatype' => $datatype]));

        // Validate deletion.
        $ids = [];

        $exportidrepository->clean_deleted_ids($datatype, $ids);
        $this->assertEquals($recordsnum, export_ids::count_records());

        $exportidsrecords = export_ids::get_records(['datatype' => $datatype]);
        foreach ($exportidsrecords as $exportids) {
            $ids[] = $exportids->get('dataid');
        }

        $exportidrepository->clean_deleted_ids($datatype, $ids);
        $this->assertEquals(0, export_ids::count_records());
    }

    /**
     * Test for get_deleted_ids() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\export_id_repository::save
     * @covers \local_intellidata\repositories\export_id_repository::get_deleted_ids
     */
    public function test_get_deleted_ids() {
        global $DB;

        $DB->delete_records(export_ids::TABLE);
        $this->resetAfterTest(true);

        $exportidrepository = new export_id_repository();
        $this->assertInstanceOf('local_intellidata\repositories\export_id_repository', $exportidrepository);

        // Validate empty table.
        $this->assertEquals(0, export_ids::count_records());

        // Validate record creation.
        $datatype = 'tracking';
        $records = []; $recordsnum = 5;
        for ($i = 1; $i <= $recordsnum; $i++) {
            $records[] = [
                'datatype' => $datatype,
                'dataid' => $i,
                'timecreated' => time(),
            ];
        }

        // Validate not existing deleted IDs.
        $deletedids = $exportidrepository->get_deleted_ids($datatype, 'local_intellidata_tracking');
        $this->assertFalse($deletedids->valid());

        // Validate deleted IDs.
        $exportidrepository->save($records);
        $this->assertEquals($recordsnum, export_ids::count_records(['datatype' => $datatype]));

        $deletedrecords = $exportidrepository->get_deleted_ids($datatype, 'local_intellidata_tracking');
        $this->assertTrue($deletedrecords->valid());

        $deletedidscount = 0;
        foreach ($deletedrecords as $noneed) {
            $deletedidscount++;
        }
        $this->assertEquals($deletedidscount, $recordsnum);
    }
}
