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
 * Database module preset forms.
 *
 * @package   mod_data
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
            $userid = $preset instanceof \mod_data\preset ? $preset->get_userid() : $preset->userid;
            $this->_form->addElement('radio', 'fullname', null, ' '.$preset->description, $userid.'/'.$preset->shortname);
        }
        $this->_form->addElement('submit', 'importexisting', get_string('choose'));
    }
}

/**
 * Import preset class
 *
 *
 * @package   mod_data
 * @deprecated since 4.1 This is deprecated since MDL-75188, please use the dynamic_form
 *             form (\mod_data\form\import_presets)
 * @todo MDL-75189 This will be deleted in Moodle 4.5.
 */
class data_import_preset_zip_form extends moodleform {
    /**
     * Form definition
     *
     * @return void
     * @throws coding_exception
     */
    public function definition() {
        $this->_form->addElement('header', 'uploadpreset', get_string('fromfile', 'data'));
        $this->_form->addHelpButton('uploadpreset', 'fromfile', 'data');

        $this->_form->addElement('hidden', 'd');
        $this->_form->setType('d', PARAM_INT);
        $this->_form->addElement('hidden', 'mode', 'import');
        $this->_form->setType('mode', PARAM_ALPHANUM);
        $this->_form->addElement('hidden', 'action', 'importzip');
        $this->_form->setType('action', PARAM_ALPHANUM);
        $this->_form->addElement('filepicker', 'importfile', get_string('chooseorupload', 'data'));
        $this->_form->addRule('importfile', null, 'required');
        $buttons = [
            $this->_form->createElement('submit', 'submitbutton', get_string('save')),
            $this->_form->createElement('cancel'),
        ];
        $this->_form->addGroup($buttons, 'buttonar', '', [' '], false);
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
