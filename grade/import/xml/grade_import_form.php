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

class grade_import_form extends moodleform {
    function definition () {
        global $COURSE, $USER, $CFG, $DB;

        $mform =& $this->_form;

        if (isset($this->_customdata)) {
            $features = $this->_customdata;
        } else {
            $features = array();
        }

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'id', optional_param('id', 0, PARAM_INT));
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'general', get_string('importfile', 'grades'));

        $mform->addElement('advcheckbox', 'feedback', get_string('importfeedback', 'grades'));
        $mform->setDefault('feedback', 0);

        // Restrict the possible upload file types.
        if (!empty($features['acceptedtypes'])) {
            $acceptedtypes = $features['acceptedtypes'];
        } else {
            $acceptedtypes = '*';
        }

        // File upload.
        $mform->addElement('filepicker', 'userfile', get_string('file'), null, array('accepted_types' => $acceptedtypes));
        $mform->disabledIf('userfile', 'url', 'noteq', '');

        $mform->addElement('text', 'url', get_string('fileurl', 'gradeimport_xml'), 'size="80"');
        $mform->setType('url', PARAM_URL);
        $mform->disabledIf('url', 'userfile', 'noteq', '');

        if (!empty($CFG->gradepublishing)) {
            $mform->addElement('header', 'publishing', get_string('publishing', 'grades'));
            $options = array(get_string('nopublish', 'grades'), get_string('createnewkey', 'userkey'));
            $keys = $DB->get_records_select('user_private_key',
                            "script='grade/import' AND instance=? AND userid=?",
                            array($COURSE->id, $USER->id));
            if ($keys) {
                foreach ($keys as $key) {
                    $options[$key->value] = $key->value; // TODO: add more details - ip restriction, valid until ??
                }
            }
            $mform->addElement('select', 'key', get_string('userkey', 'userkey'), $options);
            $mform->addHelpButton('key', 'userkey', 'userkey');
            $mform->addElement('static', 'keymanagerlink', get_string('keymanager', 'userkey'),
                    '<a href="'.$CFG->wwwroot.'/grade/import/keymanager.php?id='.$COURSE->id.'">'.get_string('keymanager', 'userkey').'</a>');

            $mform->addElement('text', 'iprestriction', get_string('keyiprestriction', 'userkey'), array('size'=>80));
            $mform->addHelpButton('iprestriction', 'keyiprestriction', 'userkey');
            $mform->setDefault('iprestriction', getremoteaddr()); // own IP - just in case somebody does not know what user key is

            $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil', 'userkey'), array('optional'=>true));
            $mform->addHelpButton('validuntil', 'keyvaliduntil', 'userkey');
            $mform->setDefault('validuntil', time()+3600*24*7); // only 1 week default duration - just in case somebody does not know what user key is

            $mform->disabledIf('iprestriction', 'key', 'noteq', 1);
            $mform->disabledIf('validuntil', 'key', 'noteq', 1);

            $mform->disabledIf('iprestriction', 'url', 'eq', '');
            $mform->disabledIf('validuntil', 'url', 'eq', '');
            $mform->disabledIf('key', 'url', 'eq', '');
        }

        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));
    }

    function validation($data, $files) {
        $err = parent::validation($data, $files);
        if (empty($data['url']) and empty($data['userfile'])) {
            if (array_key_exists('url', $data)) {
                $err['url'] = get_string('required');
            }
            if (array_key_exists('userfile', $data)) {
                $err['userfile'] = get_string('required');
            }

        } else if (array_key_exists('url', $data) and $data['url'] != clean_param($data['url'], PARAM_URL)) {
            $err['url'] = get_string('error');
        }

        return $err;
    }
}

