<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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

require_once($CFG->libdir.'/formslib.php');

/**
 * First implementation of the preferences in the form of a moodleform.
 * TODO add "reset to site defaults" button
 */
class course_settings_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('settings', 'grades'));

        $options = array(-1                            => get_string('default', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL       => get_string('real', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER     => get_string('letter', 'grades'));
        $default_gradedisplaytype = $CFG->grade_displaytype;
        foreach ($options as $key=>$option) {
            if ($key == $default_gradedisplaytype) {
                $options[-1] = get_string('defaultprev', 'grades', $option);
                break;
            }
        }
        $mform->addElement('select', 'displaytype', get_string('gradedisplaytype', 'grades'), $options);
        $mform->setHelpButton('displaytype', array(false, get_string('gradedisplaytype', 'grades'),
                              false, true, false, get_string('configgradedisplaytype', 'grades')));


        $options = array(-1=> get_string('defaultprev', 'grades', $CFG->grade_decimalpoints), 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'decimalpoints', get_string('decimalpoints', 'grades'), $options);
        $mform->setHelpButton('decimalpoints', array(false, get_string('decimalpoints', 'grades'),
                              false, true, false, get_string('configdecimalpoints', 'grades')));


        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);


        $options = array(-1                                      => get_string('default', 'grades'),
                         GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
                         GRADE_REPORT_AGGREGATION_POSITION_LAST  => get_string('positionlast', 'grades'));
        $default_gradedisplaytype = $CFG->grade_aggregationposition;
        foreach ($options as $key=>$option) {
            if ($key == $default_gradedisplaytype) {
                $options[-1] = get_string('defaultprev', 'grades', $option);
                break;
            }
        }
        $mform->addElement('select', 'aggregationposition', get_string('aggregationposition', 'grades'), $options);
        $mform->setHelpButton('aggregationposition', array(false, get_string('aggregationposition', 'grades'),
                              false, true, false, get_string('configaggregationposition', 'grades')));


        $this->add_action_buttons();
    }
}
?>
