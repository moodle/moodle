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
 * Contains class core_tag_collection_form
 *
 * @package   core
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing tag collection
 *
 * @package   core
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_collection_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {
        $data = fullclone($this->_customdata);
        if (isset($data->id)) {
            $data->tc = $data->id;
            $data->action = 'colledit';
        } else {
            $data = new stdClass();
            $data->action = 'colladd';
            $data->isdefault = false;
        }

        $mform = $this->_form;
        $mform->addElement('hidden', 'tc');
        $mform->setType('tc', PARAM_INT);
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        if (empty($data->isdefault)) {
            $mform->addRule('name', get_string('required'), 'required', null, 'client');
        } else {
            $mform->addElement('static', 'collnameexplained', '', get_string('collnameexplained', 'tag',
                    get_string('defautltagcoll', 'tag')));
        }

        $mform->addElement('advcheckbox', 'searchable', get_string('searchable', 'tag'));
        $mform->addHelpButton('searchable', 'searchable', 'tag');
        $mform->setDefault('searchable', 1);
        if (!empty($data->isdefault)) {
            $mform->freeze('searchable');
        }

        $this->add_action_buttons();

        $this->set_data($data);
    }
}
