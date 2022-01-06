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
 * Customised file types editing form.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Form for adding a new custom file type or updating an existing custom file type.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_filetypes_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $oldextension = $this->_customdata['oldextension'];

        $mform->addElement('text', 'extension', get_string('extension', 'tool_filetypes'));
        $mform->setType('extension', PARAM_ALPHANUMEXT);
        $mform->addRule('extension', null, 'required', null, 'client');
        $mform->addHelpButton('extension', 'extension', 'tool_filetypes');

        $mform->addElement('text', 'mimetype',  get_string('mimetype', 'tool_filetypes'));
        $mform->setType('mimetype', PARAM_RAW);
        $mform->addRule('mimetype', null, 'required', null, 'client');
        $mform->addHelpButton('mimetype', 'mimetype', 'tool_filetypes');

        $fileicons = \tool_filetypes\utils::get_file_icons();
        $mform->addElement('select', 'icon',
                get_string('icon', 'tool_filetypes'), $fileicons);
        $mform->addHelpButton('icon', 'icon', 'tool_filetypes');

        $mform->addElement('text', 'groups',  get_string('groups', 'tool_filetypes'));
        $mform->setType('groups', PARAM_RAW);
        $mform->addHelpButton('groups', 'groups', 'tool_filetypes');

        $mform->addElement('select', 'descriptiontype', get_string('descriptiontype', 'tool_filetypes'),
                array('' => get_string('descriptiontype_default', 'tool_filetypes'),
                'custom' => get_string('descriptiontype_custom', 'tool_filetypes'),
                'lang' => get_string('descriptiontype_lang', 'tool_filetypes')));

        $mform->addElement('text', 'description',  get_string('description', 'tool_filetypes'));
        $mform->setType('description', PARAM_TEXT);
        $mform->addHelpButton('description', 'description', 'tool_filetypes');
        $mform->hideIf('description', 'descriptiontype', 'ne', 'custom');

        $mform->addElement('text', 'corestring',  get_string('corestring', 'tool_filetypes'));
        $mform->setType('corestring', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('corestring', 'corestring', 'tool_filetypes');
        $mform->hideIf('corestring', 'descriptiontype', 'ne', 'lang');

        $mform->addElement('checkbox', 'defaulticon',  get_string('defaulticon', 'tool_filetypes'));
        $mform->addHelpButton('defaulticon', 'defaulticon', 'tool_filetypes');

        $mform->addElement('hidden', 'oldextension', $oldextension);
        $mform->setType('oldextension', PARAM_RAW);
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function set_data($data) {
        // Set up the description type.
        if (!empty($data['corestring'])) {
            $data['descriptiontype'] = 'lang';
        } else if (!empty($data['description'])) {
            $data['descriptiontype'] = 'custom';
        } else {
            $data['descriptiontype'] = '';
        }

        // Call parent.
        parent::set_data($data);
    }

    public function get_data() {
        $data = parent::get_data();

        // Update the data to handle the descriptiontype dropdown. (The type
        // is not explicitly stored, we just set or unset relevant fields.)
        if ($data) {
            switch ($data->descriptiontype) {
                case 'lang' :
                    unset($data->description);
                    break;
                case 'custom' :
                    unset($data->corestring);
                    break;
                default:
                    unset($data->description);
                    unset($data->corestring);
                    break;
            }
            unset($data->descriptiontype);
        }
        return $data;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check the extension isn't already in use.
        $oldextension = $data['oldextension'];
        $extension = trim($data['extension']);
        if (\tool_filetypes\utils::is_extension_invalid($extension, $oldextension)) {
            $errors['extension'] = get_string('error_extension', 'tool_filetypes', $extension);
        }

        // Check the 'default icon' setting doesn't conflict with an existing one.
        if (!empty($data['defaulticon']) && !\tool_filetypes\utils::is_defaulticon_allowed(
                $data['mimetype'], $oldextension)) {
            $errors['defaulticon'] = get_string('error_defaulticon', 'tool_filetypes', $extension);
        }

        // If you choose 'lang' or 'custom' descriptiontype, you must fill something in the field.
        switch ($data['descriptiontype']) {
            case 'lang' :
                if (!trim($data['corestring'])) {
                    $errors['corestring'] = get_string('required');
                }
                break;
            case 'custom' :
                if (!trim($data['description'])) {
                    $errors['description'] = get_string('required');
                }
                break;
        }

        return $errors;
    }
}
