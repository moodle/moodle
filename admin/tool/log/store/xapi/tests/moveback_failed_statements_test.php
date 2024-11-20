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

namespace logstore_xapi;

use logstore_xapi\log\moveback;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/admin/tool/log/store/xapi/tests/enchancement_jisc_skeleton.php');

/**
 * Test case for moving failed statements back to retry later.
 *
 * @package    logstore_xapi
 * @author     László Záborski <laszlo.zaborski@learningpool.com>
 * @copyright  2020 Learning Pool Ltd (http://learningpool.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moveback_failed_statements_test extends enchancement_jisc_skeleton {

    /**
     * Creating minimum a single course view event to xapi logstore.
     * Using moveback script for moving a single element
     *
     * @covers \log\moveback
     */
    public function test_single_element() {
        global $DB;

        parent::test_single_element();

        $records = $DB->get_records('logstore_xapi_failed_log');
        $this->assertCount($this->generatedxapilog, $records);

        $keys = array_keys($records);

        // Move back elements.
        $mover = new moveback($keys);
        $this->assertTrue($mover->execute());

        $expectedcount = new \stdClass();
        $expectedcount->logstore_xapi_log = $this->generatedxapilog;
        $expectedcount->logstore_xapi_failed_log = 0;
        $this->assert_store_tables($expectedcount);
    }

    /**
     * Creating multiple course view events to xapi logstore.
     * Record number depends on $multipletestnumber.
     * Using moveback script for moving multiple elements.
     *
     * @covers \log\moveback
     */
    public function test_multiple_elements() {
        global $DB;

        parent::test_multiple_elements();

        $records = $DB->get_records('logstore_xapi_failed_log');
        $this->assertCount($this->multipletestnumber * $this->generatedxapilog, $records);

        $keys = array_keys($records);

        // Move back elements.
        $mover = new moveback($keys);
        $this->assertTrue($mover->execute());

        $expectedcount = new \stdClass();
        $expectedcount->logstore_xapi_log = $this->multipletestnumber * $this->generatedxapilog;
        $expectedcount->logstore_xapi_failed_log = 0;
        $this->assert_store_tables($expectedcount);
    }
}
