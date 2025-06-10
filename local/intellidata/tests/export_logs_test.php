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
 * User migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\persistent\export_logs;
use local_intellidata\entities\users\user;
use local_intellidata\entities\courses\course;
use local_intellidata\entities\enrolments\enrolment;
use local_intellidata\repositories\export_log_repository;

/**
 * User migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class export_logs_test extends \advanced_testcase {

    /** @var string */
    private $userdatatype;

    /** @var string */
    private $coursedatatype;

    /** @var string */
    private $enrolmentdatatype;

    /** @var export_log_repository */
    private $exportlogrepository;

    public function setUp(): void {
        $this->userdatatype = user::TYPE;
        $this->coursedatatype = course::TYPE;
        $this->enrolmentdatatype = enrolment::TYPE;
        $this->exportlogrepository = new export_log_repository();
    }

    /**
     * Test export log table creation.
     *
     * @covers \local_intellidata\repositories\export_log_repository::save_last_processed_data
     */
    public function test_save_last_processed_data() {
        $this->resetAfterTest(false);

        // Insert first empty record.
        $this->exportlogrepository->save_last_processed_data($this->userdatatype, 0, 0);
        $this->assertEquals(true, export_logs::count_records(['datatype' => $this->userdatatype]));

        // Insert last record id.
        $lastrecord = new \stdClass();
        $lastrecord->id = 2;
        $now = time();

        $this->exportlogrepository->save_last_processed_data($this->userdatatype, $lastrecord, $now);
        $this->assertEquals(1, export_logs::count_records(['datatype' => $this->userdatatype]));

        $record = export_logs::get_record(['datatype' => $this->userdatatype]);
        $this->assertEquals($lastrecord->id, $record->get('last_exported_id'));
        $this->assertEquals($now, $record->get('last_exported_time'));

        // Create another datatype record.
        $this->exportlogrepository->save_last_processed_data($this->coursedatatype, $lastrecord, $now);
        $this->assertEquals(true, export_logs::count_records(['datatype' => $this->coursedatatype]));
    }

    /**
     * Test save migration.
     *
     * @covers \local_intellidata\repositories\export_log_repository::save_migrated
     */
    public function test_save_migrated() {
        $this->resetAfterTest(false);

        $record = export_logs::get_record(['datatype' => $this->userdatatype]);
        $this->assertEquals(0, $record->get('migrated'));

        // Set record migrated.
        $this->exportlogrepository->save_migrated($this->userdatatype);

        // Get updated record.
        $record = export_logs::get_record(['datatype' => $this->userdatatype]);
        $this->assertEquals(1, $record->get('migrated'));
    }

    /**
     * Test last processed data.
     *
     * @covers \local_intellidata\repositories\export_log_repository::save_last_processed_data
     * @covers \local_intellidata\repositories\export_log_repository::get_last_processed_data
     */
    public function test_get_last_processed_data() {
        $this->resetAfterTest(false);

        // Insert last record id.
        $lastrecord = new \stdClass();
        $lastrecord->id = 3;
        $now = time();

        $this->exportlogrepository->save_last_processed_data($this->userdatatype, $lastrecord, $now);

        list($lastexportedtime, $lastexportedid) = $this->exportlogrepository->get_last_processed_data($this->userdatatype);
        $this->assertEquals($lastrecord->id, $lastexportedid);
        $this->assertEquals($now, $lastexportedtime);

        list($lastexportedtime, $lastexportedid) = $this->exportlogrepository->get_last_processed_data($this->enrolmentdatatype);
        $this->assertEquals(0, $lastexportedid);
        $this->assertEquals(0, $lastexportedtime);
    }

    /**
     * Test migrated datatype.
     *
     * @covers \local_intellidata\repositories\export_log_repository::get_migrated_datatypes
     */
    public function test_get_migrated_datatypes() {
        $this->resetAfterTest(false);

        $migrated = $this->exportlogrepository->get_migrated_datatypes();

        $this->assertCount(1, $migrated);
        $this->assertContains($this->userdatatype, $migrated);
    }

    /**
     * Test clear migration.
     *
     * @covers \local_intellidata\repositories\export_log_repository::clear_migrated
     * @covers \local_intellidata\repositories\export_log_repository::get_migrated_datatypes
     */
    public function test_clear_migrated() {
        $this->resetAfterTest(true);

        $this->exportlogrepository->clear_migrated();

        $migrated = $this->exportlogrepository->get_migrated_datatypes();
        $this->assertCount(0, $migrated);
    }
}
