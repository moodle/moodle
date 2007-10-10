<?php // $Id$

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
require_once $CFG->libdir.'/formslib.php';
require_once($CFG->libdir.'/gradelib.php');

class grade_import_form extends moodleform {
    function definition (){
        $mform =& $this->_form;

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'id', optional_param('id'));
        $mform->setType('id', PARAM_INT);
        $mform->addElement('header', 'general', get_string('importfile', 'grades'));
        // file upload
        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->setType('userfile', PARAM_FILE);
        $mform->addRule('userfile', null, 'required');
        $textlib = textlib_get_instance();
        $encodings = $textlib->get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'grades'), $encodings);

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'grades'), $options); // TODO: localize
        $mform->setType('previewrows', PARAM_INT);
        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));
    }
}

class grade_import_mapping_form extends moodleform {

    function definition () {
        global $CFG;
        $mform =& $this->_form;

        // this is an array of headers
        $header = $this->_customdata['header'];
        // course id

        $mform->addElement('header', 'general', get_string('identifier', 'grades'));
        $mapfromoptions = array();

        if ($header) {
            foreach ($header as $i=>$h) {
                $mapfromoptions[$i] = s($h);
            }
        }
        $mform->addElement('select', 'mapfrom', get_string('mapfrom', 'grades'), $mapfromoptions);
        //choose_from_menu($mapfromoptions, 'mapfrom');

        $maptooptions = array('userid'=>'userid', 'username'=>'username', 'useridnumber'=>'useridnumber', 'useremail'=>'useremail', '0'=>'ignore');
        //choose_from_menu($maptooptions, 'mapto');
        $mform->addElement('select', 'mapto', get_string('mapto', 'grades'), $maptooptions);

        $mform->addElement('header', 'general', get_string('mappings', 'grades'));

        // add a comment option

        if ($gradeitems = $this->_customdata['gradeitems']) {
            $comments = array();
            foreach ($gradeitems as $itemid => $itemname) {
                $comments['feedback_'.$itemid] = 'comments for '.$itemname;
            }
        }

        if ($header) {
            $i = 0; // index
            foreach ($header as $h) {

                $h = trim($h);
                // this is what each header maps to
                $mform->addElement('selectgroups',
                                   'mapping_'.$i, s($h),
                                   array('others'=>array('0'=>'ignore', 'new'=>'new gradeitem'),
                                         'gradeitems'=>$gradeitems,
                                         'comments'=>$comments));
                $i++;
            }
        }

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'map', 1);
        $mform->setType('map', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'importcode');
        $mform->setType('importcode', PARAM_FILE);
        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));

    }
}
?>
