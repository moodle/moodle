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

class grade_export_form extends moodleform {
    function definition() {
        global $CFG, $COURSE, $USER;

        $mform =& $this->_form;
        if (isset($this->_customdata)) {  // hardcoding plugin names here is hacky
            $features = $this->_customdata;
        } else {
            $features = array();
        }

        $mform->addElement('header', 'options', get_string('options', 'grades'));

        $mform->addElement('advcheckbox', 'export_letters', get_string('exportletters', 'grades'));
        $mform->setDefault('export_letters', 0);
        $mform->setHelpButton('export_letters', array(false, get_string('exportletters', 'grades'),
                false, true, false, get_string("exportlettershelp", 'grades')));

        $mform->addElement('advcheckbox', 'export_feedback', get_string('exportfeedback', 'grades'));
        $mform->setDefault('export_feedback', 0);

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('previewrows', 'grades'), $options); 

        if (!empty($features['updatedgrades'])) {
            $mform->addElement('advcheckbox', 'updatedgradesonly', get_string('updatedgradesonly', 'grades'));
        }
    
        if (!empty($features['includeseparator'])) {
            $radio = array();
            $radio[] = &MoodleQuickForm::createElement('radio', 'separator', null, get_string('septab', 'grades'), 'tab');
            $radio[] = &MoodleQuickForm::createElement('radio', 'separator', null, get_string('sepcomma', 'grades'), 'comma');
            $mform->addGroup($radio, 'separator', get_string('separator', 'grades'), ' ', false);
            $mform->setDefault('separator', 'comma');
        }

        if (!empty($CFG->gradepublishing) and !empty($features['publishing'])) {
            $mform->addElement('header', 'publishing', get_string('publishing', 'grades'));
            $options = array(get_string('nopublish', 'grades'), get_string('createnewkey', 'userkey'));
            if ($keys = get_records_select('user_private_key', "script='grade/export' AND instance={$COURSE->id} AND userid={$USER->id}")) {
                foreach ($keys as $key) {
                    $options[$key->value] = $key->value; // TODO: add more details - ip restriction, valid until ??
                }
            }
            $mform->addElement('select', 'key', get_string('userkey', 'userkey'), $options);
            $mform->setHelpButton('key', array(false, get_string('userkey', 'userkey'),
                    false, true, false, get_string("userkeyhelp", 'grades')));
            $mform->addElement('static', 'keymanagerlink', get_string('keymanager', 'userkey'),
                    '<a href="'.$CFG->wwwroot.'/grade/export/keymanager.php?id='.$COURSE->id.'">'.get_string('keymanager', 'userkey').'</a>');

            $mform->addElement('text', 'iprestriction', get_string('keyiprestriction', 'userkey'), array('size'=>80));
            $mform->setHelpButton('iprestriction', array(false, get_string('keyiprestriction', 'userkey'),
                    false, true, false, get_string("keyiprestrictionhelp", 'userkey')));
            $mform->setDefault('iprestriction', getremoteaddr()); // own IP - just in case somebody does not know what user key is

            $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil', 'userkey'), array('optional'=>true));
            $mform->setHelpButton('validuntil', array(false, get_string('keyvaliduntil', 'userkey'),
                    false, true, false, get_string("keyvaliduntilhelp", 'userkey')));
            $mform->setDefault('validuntil', time()+3600*24*7); // only 1 week default duration - just in case somebody does not know what user key is

            $mform->disabledIf('iprestriction', 'key', 'noteq', 1);
            $mform->disabledIf('validuntil', 'key', 'noteq', 1);
        }

        $mform->addElement('header', 'gradeitems', get_string('gradeitemsinc', 'grades'));

        if ($grade_items = grade_item::fetch_all(array('courseid'=>$COURSE->id))) {
            foreach ($grade_items as $grade_item) {
                if (!empty($features['idnumberrequired']) and empty($grade_item->idnumber)) {
                    $mform->addElement('advcheckbox', 'itemids['.$grade_item->id.']', $grade_item->get_name(), get_string('noidnumber', 'grades'));
                    $mform->hardFreeze('itemids['.$grade_item->id.']');

                } else {
                    $mform->addElement('advcheckbox', 'itemids['.$grade_item->id.']', $grade_item->get_name());
                    $mform->setDefault('itemids['.$grade_item->id.']', 1);
                }
            }
        }

        $mform->addElement('hidden', 'id', $COURSE->id);

        $this->add_action_buttons(false, get_string('submit'));
    }
}
?>
