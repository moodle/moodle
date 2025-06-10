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
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\completion;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/adaptivequiz/locallib.php');

use advanced_testcase;
use cm_info;
use mod_adaptivequiz\completion\custom_completion;

/**
 * @covers \mod_adaptivequiz\completion\custom_completion
 */
class custom_completion_test extends advanced_testcase {

    public function test_it_defines_completion_state_based_on_attempt_completion():void {
        global $DB;

        $this->resetAfterTest();
        $this->setup_test_data_xml();

        $attemptuniqueid = 330;
        $adaptivequizid = 330;
        $cmid = 5;
        $userid = 2;

        $adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $adaptivequizid]);
        $context = \context_module::instance($cmid);
        $cm = get_coursemodule_from_id('adaptivequiz', $cmid);

        $cminfo = cm_info::create($cm);
        $cminfo->override_customdata('customcompletionrules',
            ['completionattemptcompleted' => $adaptivequiz->completionattemptcompleted]);

        $completion = new custom_completion($cminfo, $userid);

        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_state('completionattemptcompleted'));

        adaptivequiz_complete_attempt($attemptuniqueid, $adaptivequiz, $context, $userid, '1', 'php unit test');

        $this->assertEquals(COMPLETION_COMPLETE, $completion->get_state('completionattemptcompleted'));
    }

    private function setup_test_data_xml() {
        $this->dataset_from_files(
            [__DIR__.'/../fixtures/mod_adaptivequiz_adaptiveattempt.xml']
        )->to_database();
    }
}
