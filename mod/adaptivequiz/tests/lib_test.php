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
 * Adaptive lib.php PHPUnit tests
 *
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/adaptivequiz/lib.php');

use advanced_testcase;
use mod_adaptivequiz\local\attempt\attempt_state;
use stdClass;

/**
 * @group mod_adaptivequiz
 */
class lib_test extends advanced_testcase {
    /**
     * This functions loads data via the tests/fixtures/mod_adaptivequiz.xml file
     * @return void
     */
    protected function setup_test_data_xml() {
        $this->dataset_from_files(
            [__DIR__.'/fixtures/mod_adaptivequiz.xml']
        )->to_database();
    }

    /**
     * Provide input data to the parameters of the test_questioncat_association_insert() method.
     */
    public function questioncat_association_records() {
        $data = array();

        $adaptivequiz = new stdClass();
        $adaptivequiz->questionpool = array(1, 2, 3, 4);
        $data[] = array(1, $adaptivequiz);

        $adaptivequiz = new stdClass();
        $adaptivequiz->questionpool = array(1, 2);
        $data[] = array(2, $adaptivequiz);

        $adaptivequiz = new stdClass();
        $adaptivequiz->questionpool = array(1, 2, 4);
        $data[] = array(3, $adaptivequiz);

        return $data;
    }

    /**
     * Test insertion of question category association records.
     *
     * @dataProvider questioncat_association_records
     * @param int $instance: activity instance id
     * @param object $adaptivequiz: An object from the form in mod_form.php
     * @group adaptivequiz_lib_test
     * @covers ::adaptivequiz_add_questcat_association
     */
    public function test_questioncat_association_insert($instance, stdClass $adaptivequiz) {
        global $DB;

        $this->resetAfterTest(true);

        adaptivequiz_add_questcat_association($instance, $adaptivequiz);

        if (1 == $instance) {
            $this->assertEquals(4, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        }

        if (2 == $instance) {
            $this->assertEquals(2, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        }

        if (3 == $instance) {
            $this->assertEquals(3, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        }
    }

    /**
     * Test update of question category associations records.
     *
     * @dataProvider questioncat_association_records
     * @param int $instance: activity instance id
     * @param object $adaptivequiz: An object from the form in mod_form.php
     * @group adaptivequiz_lib_test
     * @covers ::adaptivequiz_update_questcat_association
     */
    public function test_questioncat_association_update($instance, stdClass $adaptivequiz) {
        global $DB;

        $this->resetAfterTest(true);

        adaptivequiz_add_questcat_association($instance, $adaptivequiz);

        if (1 == $instance) {
            $adaptivequizupdate = new stdClass();
            $adaptivequizupdate->questionpool = array(111, 222, 333, 444, 555, 122, 133, 144, 155, 166);

            adaptivequiz_update_questcat_association($instance, $adaptivequizupdate);
            $this->assertEquals(10, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        }

        if (2 == $instance) {
            $adaptivequizupdate = new stdClass();
            $adaptivequizupdate->questionpool = array(4);

            adaptivequiz_update_questcat_association($instance, $adaptivequizupdate);
            $this->assertEquals(1, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        }

        if (3 == $instance) {
            $adaptivequizupdate = new stdClass();
            $adaptivequizupdate->questionpool = array(4, 10, 20, 30, 40, 100, 333);

            adaptivequiz_update_questcat_association($instance, $adaptivequizupdate);
            $this->assertEquals(7, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        }
    }

    /**
     * This function tests the removal of an activity instance and all related data.
     *
     * @covers ::adaptivequiz_delete_instance
     */
    public function test_adaptivequiz_delete_instance() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setup_test_data_xml();

        $instance = 330;
        adaptivequiz_delete_instance($instance);

        $this->assertEquals(0, $DB->count_records('adaptivequiz', array('id' => $instance)));
        $this->assertEquals(0, $DB->count_records('adaptivequiz_question', array('instance' => $instance)));
        $this->assertEquals(0, $DB->count_records('adaptivequiz_attempt', array('instance' => $instance)));
        $this->assertEquals(0, $DB->count_records('question_usages', array('id' => $instance)));
    }

    /**
     * This function tests the output from adaptivequiz_print_recent_mod_activity().
     *
     * @covers ::adaptivequiz_print_recent_mod_activity
     */
    public function test_adaptivequiz_print_recent_mod_activity_details_true() {
        $this->resetAfterTest(true);

        $dummy = new stdClass();
        $dummy->user = new stdClass();
        $dummy->user->id = 2;
        $dummy->user->fullname = 'user-phpunit';
        $dummy->user->alternatename = 'user-phpunit';
        $dummy->user->picture = '';
        $dummy->user->firstname = 'user';
        $dummy->user->middlename = '-';
        $dummy->user->lastname = 'phpunit';
        $dummy->user->imagealt = '';
        $dummy->user->email = 'a@a.com';
        $dummy->user->firstnamephonetic = 'user';
        $dummy->user->lastnamephonetic = 'phpunit';
        $dummy->content = new stdClass();
        $dummy->content->attemptstate = attempt_state::IN_PROGRESS;
        $dummy->content->questionsattempted = '12';
        $dummy->timestamp = 1234;
        $dummy->type = 'mod_adaptivequiz';
        $dummy->name = 'my-phpunit-test';
        $dummy->cmid = 99;

        $output = adaptivequiz_print_recent_mod_activity($dummy, 1, true, array('mod_adaptivequiz' => 'adaptivequiz'), true, true);
        $this->assertStringContainsString('<table', $output);
        $this->assertStringContainsString('<tr>', $output);
        $this->assertStringContainsString('<td', $output);
        $this->assertStringContainsString('mod/adaptivequiz/view.php?id=99', $output);
        $this->assertStringContainsString('/user/view.php?id=2', $output);
        $this->assertStringContainsString('user phpunit', $output);
        $this->assertStringContainsString('my-phpunit-test', $output);
    }

    /**
     * This function tests the output from adaptivequiz_print_recent_mod_activity().
     *
     * @covers ::adaptivequiz_print_recent_mod_activity
     */
    public function test_adaptivequiz_print_recent_mod_activity_details_false() {
        $this->resetAfterTest(true);

        $dummy = new stdClass();
        $dummy->user = new stdClass();
        $dummy->user->id = 2;
        $dummy->user->fullname = 'user-phpunit';
        $dummy->user->alternatename = 'user-phpunit';
        $dummy->user->picture = '';
        $dummy->user->firstname = 'user';
        $dummy->user->middlename = '-';
        $dummy->user->lastname = 'phpunit';
        $dummy->user->imagealt = '';
        $dummy->user->email = 'a@a.com';
        $dummy->user->firstnamephonetic = 'user';
        $dummy->user->lastnamephonetic = 'phpunit';
        $dummy->content = new stdClass();
        $dummy->content->attemptstate = attempt_state::IN_PROGRESS;
        $dummy->content->questionsattempted = '12';
        $dummy->timestamp = 1234;
        $dummy->type = 'mod_adaptivequiz';
        $dummy->name = 'my-phpunit-test';
        $dummy->cmid = 99;

        $output = adaptivequiz_print_recent_mod_activity($dummy, 1, false, array('mod_adaptivequiz' => 'adaptivequiz'), true, true);

        $this->assertStringContainsString('<table', $output);
        $this->assertStringContainsString('<tr>', $output);
        $this->assertStringContainsString('<td', $output);
        $this->assertStringContainsString('/user/view.php?id=2', $output);
        $this->assertStringContainsString('user phpunit', $output);
    }
}
