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

    public function cook_form_data($raw) {
        return parent::cook_form_data($raw);
    }
}


class workshop_accumulative_strategy_test extends UnitTestCase {

    /** workshop instance emulation */
    protected $workshop;

    /** instance of the strategy logic class being tested */
    protected $strategy;

    /** this emulates data returned by get_data() of a submitted strategy edit form */
    protected $rawdata;

    /** setup testing environment */
    public function setUp() {
    
        $this->workshop             = new stdClass;
        $this->workshop->id         = 42;
        $this->workshop->strategy   = 'accumulative';

        $this->strategy = new testable_workshop_accumulative_strategy($this->workshop);

        // emulate a form with 5 dimensions. The first three are already in DB, the forth is new and the
        // fifth is left empty
        $this->rawdata = new stdClass;
        $this->rawdata->workshopid          = 42;
        $this->rawdata->strategy            = 'accumulative';
        $this->rawdata->numofdimensions     = 5;
        $this->rawdata->dimensionid         = array(0 => 3,       1=> 2,         2 => 1,       3 => 0,       4 => 0);
        $this->rawdata->description         = array(0 => 'First', 1 => 'Second', 2 => 'Third', 3 => 'Forth', 4 => '');
        $this->rawdata->descriptionformat   = array(0 => 1,       1 => 1,        2 => 1,       3 => 1,       4 => 1);
        $this->rawdata->grade               = array(0 => 10,      1 => 5,        2 => 5,       3 => 2,       4 => 10);
        $this->rawdata->weight              = array(0 => 1,       1 => 1,        2 => 2,       3 => 2,       4 => 1);
    }

    public function tearDown() {
        $this->strategy = null;
    }

    public function test_cook_form_data() {

        $cooked = $this->strategy->cook_form_data($this->rawdata);
        $this->assertIsA($cooked, 'Array');
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
    
}
