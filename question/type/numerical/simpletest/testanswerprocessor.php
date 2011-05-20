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
 * Unit tests for the numerical question definition class.
 *
 * @package moodlecore
 * @subpackage questiontypes
 * @copyright 2008 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');

class testable_qtype_numerical_answer_processor extends qtype_numerical_answer_processor {
    public function parse_response($response) {
        return parent::parse_response($response);
    }
}

class qtype_numerical_answer_processor_test extends UnitTestCase {
    public function test_parse_response() {
        $ap = new testable_qtype_numerical_answer_processor(
                array('m' => 1, 'cm' => 100), false, '.', ',');

        $this->assertEqual(array('3', '142', '', ''), $ap->parse_response('3.142'));
        $this->assertEqual(array('', '2', '', ''), $ap->parse_response('.2'));
        $this->assertEqual(array('1', '', '', ''), $ap->parse_response('1.'));
        $this->assertEqual(array('1', '0', '', ''), $ap->parse_response('1.0'));
        $this->assertEqual(array('-1', '', '', ''), $ap->parse_response('-1.'));
        $this->assertEqual(array('+1', '0', '', ''), $ap->parse_response('+1.0'));

        $this->assertEqual(array('1', '', '4', ''), $ap->parse_response('1e4'));
        $this->assertEqual(array('3', '142', '-4', ''), $ap->parse_response('3.142E-4'));
        $this->assertEqual(array('', '2', '+2', ''), $ap->parse_response('.2e+2'));
        $this->assertEqual(array('1', '', '-1', ''), $ap->parse_response('1.e-1'));
        $this->assertEqual(array('1', '0', '0', ''), $ap->parse_response('1.0e0'));

        $this->assertEqual(array('3', '', '8', ''), $ap->parse_response('3x10^8'));
        $this->assertEqual(array('3', '', '8', ''), $ap->parse_response('3×10^8'));
        $this->assertEqual(array('3', '0', '8', ''), $ap->parse_response('3.0*10^8'));
        $this->assertEqual(array('3', '00', '-8', ''), $ap->parse_response('3.00x10**-8'));
        $this->assertEqual(array('0', '001', '7', ''), $ap->parse_response('0.001×10**7'));

        $this->assertEqual(array('1', '', '', 'm'), $ap->parse_response('1m'));
        $this->assertEqual(array('3', '142', '', 'm'), $ap->parse_response('3.142 m'));
        $this->assertEqual(array('', '2', '', 'm'), $ap->parse_response('.2m'));
        $this->assertEqual(array('1', '', '', 'cm'), $ap->parse_response('1.cm'));
        $this->assertEqual(array('1', '0', '', 'cm'), $ap->parse_response('1.0   cm'));
        $this->assertEqual(array('-1', '', '', 'm'), $ap->parse_response('-1.m'));
        $this->assertEqual(array('+1', '0', '', 'cm'), $ap->parse_response('+1.0cm'));

        $this->assertEqual(array('1', '', '4', 'm'), $ap->parse_response('1e4 m'));
        $this->assertEqual(array('3', '142', '-4', 'cm'), $ap->parse_response('3.142E-4  cm'));
        $this->assertEqual(array('', '2', '+2', 'm'), $ap->parse_response('.2e+2m'));
        $this->assertEqual(array('1', '', '-1', 'm'), $ap->parse_response('1.e-1 m'));
        $this->assertEqual(array('1', '0', '0', 'cm'), $ap->parse_response('1.0e0cm'));

        $this->assertEqual(array('1000000', '', '', ''),
                $ap->parse_response('1,000,000'));
        $this->assertEqual(array('1000', '00', '', 'm'),
                $ap->parse_response('1,000.00 m'));

        $this->assertEqual(array(null, null, null, null), $ap->parse_response('frog'));
        $this->assertEqual(array('3', '', '', 'frogs'), $ap->parse_response('3 frogs'));
        $this->assertEqual(array(null, null, null, null), $ap->parse_response('. m'));
        $this->assertEqual(array(null, null, null, null), $ap->parse_response('.e8 m'));
        $this->assertEqual(array(null, null, null, null), $ap->parse_response(','));
    }

    protected function verify_value_and_unit($exectedval, $expectedunit,
            qtype_numerical_answer_processor $ap, $input, $separateunit = null) {
        list($val, $unit) = $ap->apply_units($input, $separateunit);
        if (is_null($exectedval)) {
            $this->assertNull($val);
        } else {
            $this->assertWithinMargin($exectedval, $val, 0.0001);
        }
        $this->assertEqual($expectedunit, $unit);
    }

    public function test_apply_units() {
        $ap = new qtype_numerical_answer_processor(
                array('m/s' => 1, 'c' => 3.3356409519815E-9,
                        'mph' => 2.2369362920544), false, '.', ',');

        $this->verify_value_and_unit(3e8, 'm/s', $ap, '3x10^8 m/s');
        $this->verify_value_and_unit(3e8, '', $ap, '3x10^8');
        $this->verify_value_and_unit(299792458, 'c', $ap, '1c');
        $this->verify_value_and_unit(0.44704, 'mph', $ap, '0001.000 mph');

        $this->verify_value_and_unit(1, 'frogs', $ap, '1 frogs');
        $this->verify_value_and_unit(null, null, $ap, '. m/s');
    }

    public function test_apply_units_separate_unit() {
        $ap = new qtype_numerical_answer_processor(
                array('m/s' => 1, 'c' => 3.3356409519815E-9,
                        'mph' => 2.2369362920544), false, '.', ',');

        $this->verify_value_and_unit(3e8, 'm/s', $ap, '3x10^8', 'm/s');
        $this->verify_value_and_unit(3e8, '', $ap, '3x10^8', '');
        $this->verify_value_and_unit(299792458, 'c', $ap, '1', 'c');
        $this->verify_value_and_unit(0.44704, 'mph', $ap, '0001.000', 'mph');

        $this->verify_value_and_unit(1, 'frogs', $ap, '1', 'frogs');
        $this->verify_value_and_unit(null, null, $ap, '.', 'm/s');
    }

    public function test_euro_style() {
        $ap = new qtype_numerical_answer_processor(array(), false, ',', ' ');

        $this->assertEqual(array(-1000, ''), $ap->apply_units('-1 000'));
        $this->assertEqual(array(3.14159, ''), $ap->apply_units('3,14159'));
    }

    public function test_percent() {
        $ap = new qtype_numerical_answer_processor(array('%' => 100), false, '.', ',');

        $this->assertEqual(array('0.03', '%'), $ap->apply_units('3%'));
        $this->assertEqual(array('1e-8', '%'), $ap->apply_units('1e-6 %'));
        $this->assertEqual(array('100', ''), $ap->apply_units('100'));
    }


    public function test_currency() {
        $ap = new qtype_numerical_answer_processor(array('$' => 1, '£' => 1), true, '.', ',');

        $this->assertEqual(array('1234.56', '£'), $ap->apply_units('£1,234.56'));
        $this->assertEqual(array('100', '$'), $ap->apply_units('$100'));
        $this->assertEqual(array('100', '$'), $ap->apply_units('$100.'));
        $this->assertEqual(array('100.00', '$'), $ap->apply_units('$100.00'));
        $this->assertEqual(array('100', ''), $ap->apply_units('100'));
        $this->assertEqual(array('100', 'frog'), $ap->apply_units('frog 100'));
    }
}
