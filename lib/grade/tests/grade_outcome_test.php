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
 * @package    core_grades
 * @category   phpunit
 * @copyright  nicolas@moodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/lib.php');


class grade_outcome_testcase extends grade_base_testcase {

    public function test_grade_outcome() {
        $this->sub_test_grade_outcome_construct();
        $this->sub_test_grade_outcome_insert();
        $this->sub_test_grade_outcome_update();
        $this->sub_test_grade_outcome_delete();
        //$this->sub_test_grade_outcome_fetch();
        $this->sub_test_grade_outcome_fetch_all();
    }

    protected function sub_test_grade_outcome_construct() {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->shortname = 'Team work';

        $grade_outcome = new grade_outcome($params, false);
        $this->assertEquals($params->courseid, $grade_outcome->courseid);
        $this->assertEquals($params->shortname, $grade_outcome->shortname);
    }

    protected function sub_test_grade_outcome_insert() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'insert'));

        $grade_outcome->courseid = $this->courseid;
        $grade_outcome->shortname = 'tw';
        $grade_outcome->fullname = 'Team work';

        $grade_outcome->insert();

        $last_grade_outcome = end($this->grade_outcomes);

        $this->assertEquals($grade_outcome->id, $last_grade_outcome->id + 1);
        $this->assertFalse(empty($grade_outcome->timecreated));
        $this->assertFalse(empty($grade_outcome->timemodified));
    }

    protected function sub_test_grade_outcome_update() {
        global $DB;
        $grade_outcome = new grade_outcome($this->grade_outcomes[0], false);
        $this->assertTrue(method_exists($grade_outcome, 'update'));
        $grade_outcome->shortname = 'Team work';
        $this->assertTrue($grade_outcome->update());
        $shortname = $DB->get_field('grade_outcomes', 'shortname', array('id' => $this->grade_outcomes[0]->id));
        $this->assertEquals($grade_outcome->shortname, $shortname);
    }

    protected function sub_test_grade_outcome_delete() {
        global $DB;
        $grade_outcome = new grade_outcome($this->grade_outcomes[0], false);
        $this->assertTrue(method_exists($grade_outcome, 'delete'));

        $this->assertTrue($grade_outcome->delete());
        $this->assertFalse($DB->get_record('grade_outcomes', array('id' => $grade_outcome->id)));
    }

    protected function sub_test_grade_outcome_fetch() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'fetch'));

        $grade_outcome = grade_outcome::fetch(array('id'=>$this->grade_outcomes[0]->id));
        $grade_outcome->load_scale();
        $this->assertEquals($this->grade_outcomes[0]->id, $grade_outcome->id);
        $this->assertEquals($this->grade_outcomes[0]->shortname, $grade_outcome->shortname);

        $this->assertEquals($this->scale[2]->id, $grade_outcome->scale->id);
    }

    protected function sub_test_grade_outcome_fetch_all() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'fetch_all'));

        $grade_outcomes = grade_outcome::fetch_all(array());
        $this->assertEquals(count($this->grade_outcomes), count($grade_outcomes));
    }
}
