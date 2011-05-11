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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class grade_export_form extends moodleform {
    function definition() {
        global $CFG, $COURSE, $USER, $DB;

        $mform =& $this->_form;
        if (isset($this->_customdata)) {  // hardcoding plugin names here is hacky
            $features = $this->_customdata;
        } else {
            $features = array();
        }

        $mform->addElement('header', 'options', get_string('options', 'grades'));

        $mform->addElement('advcheckbox', 'export_feedback', get_string('exportfeedback', 'grades'));
        $mform->setDefault('export_feedback', 0);

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('previewrows', 'grades'), $options);

        if (!empty($features['updategradesonly'])) {
            $mform->addElement('advcheckbox', 'updatedgradesonly', get_string('updatedgradesonly', 'grades'));
        }
        /// selections for decimal points and format, MDL-11667, defaults to site settings, if set
        //$default_gradedisplaytype = $CFG->grade_export_displaytype;
        $options = array(GRADE_DISPLAY_TYPE_REAL       => get_string('real', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER     => get_string('letter', 'grades'));

        /*
        foreach ($options as $key=>$option) {
            if ($key == $default_gradedisplaytype) {
                $options[GRADE_DISPLAY_TYPE_DEFAULT] = get_string('defaultprev', 'grades', $option);
                break;
            }
        }
        */
        $mform->addElement('select', 'display', get_string('gradeexportdisplaytype', 'grades'), $options);
        $mform->setDefault('display', $CFG->grade_export_displaytype);

        //$default_gradedecimals = $CFG->grade_export_decimalpoints;
        $options = array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'decimals', get_string('gradeexportdecimalpoints', 'grades'), $options);
        $mform->setDefault('decimals', $CFG->grade_export_decimalpoints);
        $mform->disabledIf('decimals', 'display', 'eq', GRADE_DISPLAY_TYPE_LETTER);
        /*
        if ($default_gradedisplaytype == GRADE_DISPLAY_TYPE_LETTER) {
            $mform->disabledIf('decimals', 'display', "eq", GRADE_DISPLAY_TYPE_DEFAULT);
        }
        */

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
            $keys = $DB->get_records_select('user_private_key', "script='grade/export' AND instance=? AND userid=?",
                            array($COURSE->id, $USER->id));
            if ($keys) {
                foreach ($keys as $key) {
                    $options[$key->value] = $key->value; // TODO: add more details - ip restriction, valid until ??
                }
            }
            $mform->addElement('select', 'key', get_string('userkey', 'userkey'), $options);
            $mform->addHelpButton('key', 'userkey', 'userkey');
            $mform->addElement('static', 'keymanagerlink', get_string('keymanager', 'userkey'),
                    '<a href="'.$CFG->wwwroot.'/grade/export/keymanager.php?id='.$COURSE->id.'">'.get_string('keymanager', 'userkey').'</a>');

            $mform->addElement('text', 'iprestriction', get_string('keyiprestriction', 'userkey'), array('size'=>80));
            $mform->addHelpButton('iprestriction', 'keyiprestriction', 'userkey');
            $mform->setDefault('iprestriction', getremoteaddr()); // own IP - just in case somebody does not know what user key is

            $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil', 'userkey'), array('optional'=>true));
            $mform->addHelpButton('validuntil', 'keyvaliduntil', 'userkey');
            $mform->setDefault('validuntil', time()+3600*24*7); // only 1 week default duration - just in case somebody does not know what user key is

            $mform->disabledIf('iprestriction', 'key', 'noteq', 1);
            $mform->disabledIf('validuntil', 'key', 'noteq', 1);
        }

        $mform->addElement('header', 'gradeitems', get_string('gradeitemsinc', 'grades'));

        $switch = grade_get_setting($COURSE->id, 'aggregationposition', $CFG->grade_aggregationposition);

        // Grab the grade_seq for this course
        $gseq = new grade_seq($COURSE->id, $switch);

        if ($grade_items = $gseq->items) {
            $needs_multiselect = false;
            foreach ($grade_items as $grade_item) {
                if (!empty($features['idnumberrequired']) and empty($grade_item->idnumber)) {
                    $mform->addElement('advcheckbox', 'itemids['.$grade_item->id.']', $grade_item->get_name(), get_string('noidnumber', 'grades'));
                    $mform->hardFreeze('itemids['.$grade_item->id.']');
                } else {
                    $mform->addElement('advcheckbox', 'itemids['.$grade_item->id.']', $grade_item->get_name(), null, array('group' => 1));
                    $mform->setDefault('itemids['.$grade_item->id.']', 1);
                    $needs_multiselect = true;
                }
                }

            if ($needs_multiselect) {
                $this->add_checkbox_controller(1, null, null, 1); // 1st argument is group name, 2nd is link text, 3rd is attributes and 4th is original value
            }
        }

        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons(false, get_string('submit'));

    }
}

