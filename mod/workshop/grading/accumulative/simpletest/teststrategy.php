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
 * Unit tests for (some of) mod/workshop/grading/accumulative/strategy.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the code to test
require_once($CFG->dirroot . '/mod/workshop/grading/accumulative/strategy.php');

/**
 * Test subclass that makes all the protected methods we want to test public
 */
class testable_workshop_accumulative_strategy extends workshop_accumulative_strategy {
    public function _cook_dimension_records(array $raw) {
        return parent::_cook_dimension_records($raw);
    }
    public function _cook_edit_form_data(stdClass $raw) {
        return parent::_cook_edit_form_data($raw);
    }
    public function _cook_assessment_form_data(stdClass $assessment, stdClass $raw) {
        return parent::_cook_assessment_form_data($assessment, $raw);
    }
}

class workshop_accumulative_strategy_test extends UnitTestCase {

    /** workshop instance emulation */
    protected $workshop;

    /** instance of the strategy logic class being tested */
    protected $strategy;

    /** this emulates dimensions data returned by get_data() of a submitted strategy edit form */
    protected $rawform;

    /** this emulates dimensions data stored in database to be loaded into strategy edit form */
    protected $rawdb;

    /** setup testing environment */
    public function setUp() {
        $cm             = (object)array('id' => 3);
        $course         = (object)array('id' => 11);
        $workshop       = (object)array('id' => 42, 'strategy' => 'accumulative');
        $this->workshop = new workshop($workshop, $cm, $course);
        $this->strategy = new testable_workshop_accumulative_strategy($this->workshop);

        // emulate a form with 5 dimensions. The first three are already in DB, the forth is new and the
        // fifth is left empty
        $this->rawform = new stdClass;
        $this->rawform->workshopid          = 42;
        $this->rawform->strategy            = 'accumulative';
        $this->rawform->norepeats           = 5;
        $this->rawform->dimensionid__idx_0  = 3;
        $this->rawform->dimensionid__idx_1  = 2;
        $this->rawform->dimensionid__idx_2  = 1;
        $this->rawform->dimensionid__idx_3  = 0;
        $this->rawform->dimensionid__idx_4  = 0;
        $this->rawform->description__idx_0_editor = array('text' => 'First', 'format' =>1, 'itemid' => 123456789);
        $this->rawform->description__idx_1_editor = array('text' => 'Second', 'format' =>1, 'itemid' => 123456788);
        $this->rawform->description__idx_2_editor = array('text' => 'Third', 'format' =>1, 'itemid' => 123456787);
        $this->rawform->description__idx_3_editor = array('text' => 'Forth', 'format' =>1, 'itemid' => 123456786);
        $this->rawform->description__idx_4_editor = array('text' => '', 'format' =>1, 'itemid' => 123456785);
        $this->rawform->grade__idx_0  = 10;
        $this->rawform->grade__idx_1  = 5;
        $this->rawform->grade__idx_2  = 5;
        $this->rawform->grade__idx_3  = 2;
        $this->rawform->grade__idx_4  = 10;
        $this->rawform->weight__idx_0 = 1;
        $this->rawform->weight__idx_1 = 1;
        $this->rawform->weight__idx_2 = 2;
        $this->rawform->weight__idx_3 = 2;
        $this->rawform->weight__idx_4 = 1;

        // emulate two assessment dimensions being stored in database
        $this->rawdb = array();
        $this->rawdb[3] = new stdClass;
        $this->rawdb[3]->id                 = 3;
        $this->rawdb[3]->workshopid         = 42;
        $this->rawdb[3]->sort               = 1;
        $this->rawdb[3]->description        = 'First';
        $this->rawdb[3]->descriptionformat  = 1;
        $this->rawdb[3]->grade              = 20;
        $this->rawdb[3]->weight             = 16;

        $this->rawdb[7] = new stdClass;
        $this->rawdb[7]->id                 = 7;
        $this->rawdb[7]->workshopid         = 42;
        $this->rawdb[7]->sort               = 2;
        $this->rawdb[7]->description        = 'Second';
        $this->rawdb[7]->descriptionformat  = 1;
        $this->rawdb[7]->grade              = 10;
        $this->rawdb[7]->weight             = 1;

        // emulate the filled assessment form
        $this->rawass->nodims               = 5;
        $this->rawass->strategyname         = 'accumulative';
        $this->rawass->dimensionid__idx_0   = 3;
        $this->rawass->grade__idx_0         = 10;
        $this->rawass->peercomment__idx_0   = 'Great';
        $this->rawass->dimensionid__idx_1   = 2;
        $this->rawass->grade__idx_1         = 4;
        $this->rawass->peercomment__idx_1   = 'Hmm';
        $this->rawass->dimensionid__idx_2   = 1;
        $this->rawass->grade__idx_2         = 1;
        $this->rawass->peercomment__idx_2   = 'Uch';
        $this->rawass->dimensionid__idx_3   = 4;
        $this->rawass->grade__idx_3         = 0;
        $this->rawass->peercomment__idx_3   = 'Grrr';
        $this->rawass->dimensionid__idx_4   = 5;
        $this->rawass->grade__idx_4         = 7;
        $this->rawass->peercomment__idx_4   = 'Bye';
    }

    public function tearDown() {
        $this->workshop = null;
        $this->strategy = null;
        $this->rawform  = null;
        $this->rawdb    = null;
    }

    public function test_cook_dimension_records() {
        // excersise SUT
        $cooked = $this->strategy->_cook_dimension_records($this->rawdb);
        // verify
        $this->assertIsA($cooked, 'stdClass');

        $this->assertEqual($cooked->dimensionid__idx_0, 3);
        $this->assertEqual($cooked->description__idx_0, 'First');
        $this->assertEqual($cooked->descriptionformat__idx_0, 1);
        $this->assertEqual($cooked->grade__idx_0, 20);
        $this->assertEqual($cooked->weight__idx_0, 16);

        $this->assertEqual($cooked->dimensionid__idx_1, 7);
        $this->assertEqual($cooked->description__idx_1, 'Second');
        $this->assertEqual($cooked->descriptionformat__idx_1, 1);
        $this->assertEqual($cooked->grade__idx_1, 10);
        $this->assertEqual($cooked->weight__idx_1, 1);
    }

    public function test_cook_edit_form_data() {

        $cooked = $this->strategy->_cook_edit_form_data($this->rawform);
        $this->assertIsA($cooked, 'Array');
        $this->assertEqual(count($cooked), 5);
        $this->assertEqual($cooked[2], (object)array(
                            'id'                => 1,
                            'workshopid'        => 42,
                            'sort'              => 3,
                            'description'       => 'Third',
                            'descriptionformat' => 1,
                            'grade'             => 5,
                            'weight'            => 2,
                            ));
        $this->assertEqual($cooked[3], (object)array(
                            'id'                => 0,
                            'workshopid'        => 42,
                            'sort'              => 4,
                            'description'       => 'Forth',
                            'descriptionformat' => 1,
                            'grade'             => 2,
                            'weight'            => 2,
                            ));
        $this->assertEqual($cooked[4], (object)array(
                            'id'                => 0,
                            'workshopid'        => 42,
                            'sort'              => 5,
                            'description'       => '',
                            'descriptionformat' => 1,
                            'grade'             => 10,
                            'weight'            => 1,
                            ));
    }

    public function test__cook_assessment_form_data() {
        // fixture set-up
        $assessment = new stdClass();
        $assessment->id = 90;

        // exercise SUT
        $cooked = $this->strategy->_cook_assessment_form_data($assessment, $this->rawass);

        // verify
        $this->assertIsA($cooked, 'Array');
        $this->assertEqual(count($cooked), 5);
        $this->assertEqual($cooked[0], (object)array(
                            'assessmentid'  => 90,
                            'strategy'      => 'accumulative',
                            'dimensionid'   => 3,
                            'grade'         => 10,
                            'peercomment'   => 'Great',
                            'peercommentformat' => FORMAT_HTML,
                        ));
        $this->assertEqual($cooked[2], (object)array(
                            'assessmentid'  => 90,
                            'strategy'      => 'accumulative',
                            'dimensionid'   => 1,
                            'grade'         => 1,
                            'peercomment'   => 'Uch',
                            'peercommentformat' => FORMAT_HTML,
                        ));
        $this->assertEqual($cooked[3], (object)array(
                            'assessmentid'  => 90,
                            'strategy'      => 'accumulative',
                            'dimensionid'   => 4,
                            'grade'         => 0,
                            'peercomment'   => 'Grrr',
                            'peercommentformat' => FORMAT_HTML,
                        ));
    }
}
