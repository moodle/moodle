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
 * Unit tests for (some of) question/type/numerical/edit_numerical_form.php.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/edit_numerical_form.php');


/**
 * Test sub-class, so we can force the locale.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_qtype_numerical_edit_form extends qtype_numerical_edit_form {
    public function __construct() {
        // Warning, avoid running the parent constructor. That means the form is
        // not properly tested but for now that is OK, we are only testing a few
        // methods.
        $this->ap = new qtype_numerical_answer_processor(array(), false, ',', ' ');
    }
    public function is_valid_number($x) {
        return parent::is_valid_number($x);
    }
}


/**
 * Unit tests for question/type/numerical/edit_numerical_form.php.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_form_test extends UnitTestCase {
    public static $includecoverage = array(
        'question/type/numerical/edit_numerical_form.php'
    );

    protected $form;

    public function setUp() {
        $this->form = new test_qtype_numerical_edit_form();
    }

    public function tearDown() {
        $this->form = null;
    }

    public function test_is_valid_number() {
        $this->assertTrue($this->form->is_valid_number('1,001'));
        $this->assertTrue($this->form->is_valid_number('1.001'));
        $this->assertTrue($this->form->is_valid_number('1'));
        $this->assertTrue($this->form->is_valid_number('1,e8'));
        $this->assertFalse($this->form->is_valid_number('1001 xxx'));
        $this->assertTrue($this->form->is_valid_number('1.e8'));
    }
}
