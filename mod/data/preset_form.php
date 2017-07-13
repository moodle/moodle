<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden!');
}
require_once($CFG->libdir . '/formslib.php');


class data_existing_preset_form extends moodleform {
    public function definition() {
        $this->_form->addElement('header', 'presets', get_string('usestandard', 'data'));
        $this->_form->addHelpButton('presets', 'usestandard', 'data');

        $this->_form->addElement('hidden', 'd');
        $this->_form->setType('d', PARAM_INT);
        $this->_form->addElement('hidden', 'action', 'confirmdelete');
        $this->_form->setType('action', PARAM_ALPHANUM);
        $delete = get_string('delete');
        foreach ($this->_customdata['presets'] as $preset) {
            $this->_form->addElement('radio', 'fullname', null, ' '.$preset->description, $preset->userid.'/'.$preset->shortname);
        }
        $this->_form->addElement('submit', 'importexisting', get_string('choose'));
    }
}

class data_import_preset_zip_form extends moodleform {
    public function definition() {
        $this->_form->addElement('header', 'uploadpreset', get_string('fromfile', 'data'));
        $this->_form->addHelpButton('uploadpreset', 'fromfile', 'data');

        $this->_form->addElement('hidden', 'd');
        $this->_form->setType('d', PARAM_INT);
        $this->_form->addElement('hidden', 'action', 'importzip');
        $this->_form->setType('action', PARAM_ALPHANUM);
        $this->_form->addElement('filepicker', 'importfile', get_string('chooseorupload', 'data'));
        $this->_form->addRule('importfile', null, 'required');
        $this->_form->addElement('submit', 'uploadzip', get_string('import'));
    }
}

class data_export_form extends moodleform {
    public function definition() {
        $this->_form->addElement('header', 'exportheading', get_string('exportaszip', 'data'));
        $this->_form->addElement('hidden', 'd');
        $this->_form->setType('d', PARAM_INT);
        $this->_form->addElement('hidden', 'action', 'export');
        $this->_form->setType('action', PARAM_ALPHANUM);
        $this->_form->addElement('submit', 'export', get_string('export', 'data'));
    }
}

class data_save_preset_form extends moodleform {
    public function definition() {
        $this->_form->addElement('header', 'exportheading', get_string('saveaspreset', 'data'));
        $this->_form->addElement('hidden', 'd');
        $this->_form->setType('d', PARAM_INT);
        $this->_form->addElement('hidden', 'action', 'save2');
        $this->_form->setType('action', PARAM_ALPHANUM);
        $this->_form->addElement('text', 'name', get_string('name'));
        $this->_form->setType('name', PARAM_FILE);
        $this->_form->addRule('name', null, 'required');
        $this->_form->addElement('checkbox', 'overwrite', get_string('overwrite', 'data'), get_string('overrwritedesc', 'data'));
        $this->_form->addElement('submit', 'saveaspreset', get_string('continue'));
    }
}
