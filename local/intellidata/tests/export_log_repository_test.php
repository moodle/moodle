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
 * Export_log_repository test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');

use local_intellidata\repositories\export_log_repository;
use local_intellidata\persistent\export_logs;

/**
 * Export_logs_repository test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class export_log_repository_test extends \advanced_testcase {

    /**
     * Test save() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\export_log_repository::get_datatype
     */
    public function test_get_datatype() {
        $this->resetAfterTest(true);

        $exportlogrepository = new export_log_repository();
        $this->assertInstanceOf('local_intellidata\repositories\export_log_repository', $exportlogrepository);

        $datatypename = 'users';
        $exportlogrepository->reset_datatype($datatypename, export_logs::TABLE_TYPE_UNIFIED);

        // Validate existing datatype.
        $datatype = $exportlogrepository->get_datatype($datatypename);

        $this->assertInstanceOf('local_intellidata\persistent\export_logs', $datatype);

        $this->assertEquals($datatypename, $datatype->get('datatype'));
        $this->assertEquals(export_logs::TABLE_TYPE_UNIFIED, $datatype->get('tabletype'));

        // Validate not existing datatype.
        $this->assertFalse(
            $exportlogrepository->get_datatype('notexistingdatatype')
        );
    }
}
