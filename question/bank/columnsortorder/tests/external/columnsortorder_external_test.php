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

namespace qbank_columnsortorder;

use advanced_testcase;
use qbank_columnsortorder\column_manager;
use qbank_columnsortorder\external\set_columnbank_order;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/classes/external.php');

/**
 * Unit tests for qbank_columnsortorder external API.
 *
 * @package    qbank_columnsortorder
 * @author     2021, Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class columnsortorder_external_test extends advanced_testcase {

    /**
     * Test that external call core_question_external::set_columnbank_order($oldorder) sets proper
     * data in config_plugins table.
     */
    public function test_columnorder_external(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $columnsortorder = new column_manager();
        $questionlistcolumns = $columnsortorder->get_columns();
        $columnclasses = [];
        foreach ($questionlistcolumns as $columnnobject) {
            $columnclasses[] = $columnnobject->class;
        }
        shuffle($columnclasses);
        set_columnbank_order::execute($columnclasses);

        $currentconfig = (array)get_config('qbank_columnsortorder', 'enabledcol');
        $currentconfig = explode(',', $currentconfig[0]);

        $this->assertSame($columnclasses, $currentconfig);
    }
}
