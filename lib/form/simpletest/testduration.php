<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir . '/form/duration.php');

/**
 * Unit tests for (some of) ../duration.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package formslib
 */
class duration_form_element_test extends UnitTestCase {
    private $element;
    public  static $includecoverage = array('lib/form/duration.php');

    function setUp() {
        $this->element = new MoodleQuickForm_duration();
    }

    function tearDown() {
        $this->element = null;
    }

    function test_constructor() {
        // Test trying to create with an invalid unit.
        $this->expectException();
        $this->element = new MoodleQuickForm_duration('testel', null, array('defaultunit' => 123));
    }

    function test_get_units() {
        $units = $this->element->get_units();
        ksort($units);
        $this->assertEqual($units, array(1 => get_string('seconds'), 60 => get_string('minutes'),
                3600 => get_string('hours'), 86400 => get_string('days')));
    }

    function test_seconds_to_unit() {
        $this->assertEqual($this->element->seconds_to_unit(0), array(0, 60)); // Zero minutes, for a nice default unit.
        $this->assertEqual($this->element->seconds_to_unit(1), array(1, 1));
        $this->assertEqual($this->element->seconds_to_unit(3601), array(3601, 1));
        $this->assertEqual($this->element->seconds_to_unit(60), array(1, 60));
        $this->assertEqual($this->element->seconds_to_unit(180), array(3, 60));
        $this->assertEqual($this->element->seconds_to_unit(3600), array(1, 3600));
        $this->assertEqual($this->element->seconds_to_unit(7200), array(2, 3600));
        $this->assertEqual($this->element->seconds_to_unit(86400), array(1, 86400));
        $this->assertEqual($this->element->seconds_to_unit(90000), array(25, 3600));

        $this->element = new MoodleQuickForm_duration('testel', null, array('defaultunit' => 86400));
        $this->assertEqual($this->element->seconds_to_unit(0), array(0, 86400)); // Zero minutes, for a nice default unit.
    }

    function test_exportValue() {
        $el = new MoodleQuickForm_duration('testel');
        $el->_createElements();
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 10, 'timeunit' => 1))), array('testel' => 10));
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 3, 'timeunit' => 60))), array('testel' => 180));
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 1.5, 'timeunit' => 60))), array('testel' => 90));
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 2, 'timeunit' => 3600))), array('testel' => 7200));
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 1, 'timeunit' => 86400))), array('testel' => 86400));
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 0, 'timeunit' => 3600))), array('testel' => 0));

        $el = new MoodleQuickForm_duration('testel', null, array('optional' => true));
        $el->_createElements();
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 10, 'timeunit' => 1))), array('testel' => 0));
        $this->assertEqual($el->exportValue(array('testel' => array('number' => 20, 'timeunit' => 1, 'enabled' => 1))), array('testel' => 20));
    }
}
