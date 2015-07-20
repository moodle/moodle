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
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->libdir.'/formslib.php');

require_once($CFG->dirroot . '/blocks/mediasearch/locallib.php');

class mediasearch_uploaddata_form1 extends moodleform {
    public function definition () {
        global $CFG, $USER;

        $mform =& $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = array('10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploaduser'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $choices = array(UU_ADDNEW    => get_string('uuoptype_addnew', 'block_mediasearch'),
                         UU_ADD_UPDATE => get_string('uuoptype_addupdate', 'block_mediasearch'),
                         UU_UPDATE     => get_string('uuoptype_update', 'block_mediasearch'));
        $mform->addElement('select', 'uutype', get_string('uuoptype', 'tool_uploaduser'), $choices);

        $this->add_action_buttons(false, get_string('uploadentries', 'block_mediasearch'));
    }
}

class mediasearch_uploaddata_form2 extends moodleform {
    protected $courseselector = null;

    public function definition () {
        global $CFG, $USER, $SESSION;

        $mform   =& $this->_form;
        $columns =& $this->_customdata;

        // I am the template user, why should it be the administrator? we have roles now, other ppl may use this script ;-).
        $templateuser = $USER;

        // Upload settings and file.
        $mform->addElement('header', 'settingsheader', get_string('settings'));

        $mform->addElement('static', 'uutypelabel', get_string('uuoptype', 'tool_uploaduser'));

        $choices = array(0 => get_string('nochanges', 'tool_uploaduser'),
                         1 => get_string('uuupdatefromfile', 'tool_uploaduser'),
                         2 => get_string('uuupdateall', 'tool_uploaduser'),
                         3 => get_string('uuupdatemissing', 'tool_uploaduser'));
        $mform->addElement('select', 'uuupdatetype', get_string('uuupdatetype', 'block_mediasearch'), $choices);
        $mform->setDefault('uuupdatetype', 0);
        $mform->disabledIf('uuupdatetype', 'uutype', 'eq', UU_ADDNEW);
        $mform->disabledIf('uuupdatetype', 'uutype', 'eq', UU_ADDINC);

        $mform->addElement('selectyesno', 'uuallowdeletes', get_string('allowdeletes', 'tool_uploaduser'));
        $mform->setDefault('uuallowdeletes', 0);
        $mform->disabledIf('uuallowdeletes', 'uutype', 'eq', UU_ADDNEW);
        $mform->disabledIf('uuallowdeletes', 'uutype', 'eq', UU_ADDINC);
        
        // Hidden fields.
        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_INT);

        $mform->addElement('hidden', 'auth');
        $mform->setDefault('auth', '');
        $mform->setType('auth', PARAM_TEXT);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);

        $mform->addElement('hidden', 'readcount');
        $mform->setType('readcount', PARAM_INT);

        $mform->addElement('hidden', 'uutype');
        $mform->setType('uutype', PARAM_INT);

    }

    /**
     * Form tweaks that depend on current data.
     */
    public function definition_after_data() {
        global $USER, $SESSION;

        $mform   =& $this->_form;
        $columns =& $this->_customdata;

        foreach ($columns as $column) {
            if ($mform->elementExists($column)) {
                $mform->removeElement($column);
            }
        }

        $this->add_action_buttons(true, get_string('uploadentries', 'block_mediasearch'));
    }

    /**
     * Server side validation.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $columns =& $this->_customdata;
        $optype  = $data['uutype'];

        return $errors;
    }
}

class mediasearch_uploaddata_form3 extends moodleform {
    public function definition () {
        global $CFG, $USER;
        $mform =& $this->_form;
        $this->add_action_buttons(false, get_string('uploadnewfile'));
    }
}
