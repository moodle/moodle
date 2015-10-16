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
 * This file contains the form add/update a competency framework.
 *
 * @package   tool_lp
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\form;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

use moodleform;
use tool_lp\api;

require_once($CFG->libdir.'/formslib.php');

/**
 * Learning plan template form.
 *
 * @package   tool_lp
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        $context = $this->_customdata['context'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('text', 'shortname',
                           get_string('shortname', 'tool_lp'));
        $mform->setType('shortname', PARAM_TEXT);
        $mform->addRule('shortname', null, 'required', null, 'client');
        $mform->addElement('editor', 'description',
                           get_string('description', 'tool_lp'), array('rows' => 4));
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('selectyesno', 'visible',
                           get_string('visible', 'tool_lp'));
        $mform->addElement('date_time_selector',
                           'duedate',
                           get_string('duedate', 'tool_lp'),
                           array('optional' => true));
        $mform->addHelpButton('duedate', 'duedate', 'tool_lp');

        $mform->setDefault('visible', true);
        $mform->addHelpButton('visible', 'visible', 'tool_lp');

        $mform->addElement('static', 'context', get_string('context', 'core_role'));
        $mform->setDefault('context', $context->get_context_name());

        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));

        if (!empty($id)) {
            if (!$this->is_submitted()) {
                $template = api::read_template($id);
                $record = $template->to_record();
                // Massage for editor API.
                $record->description = array('text' => $record->description, 'format' => $record->descriptionformat);
                $this->set_data($record);
            }
        }

    }

    /**
     * Get form data.
     * Conveniently removes non-desired properties.
     * @return object
     */
    public function get_data() {
        $data = parent::get_data();
        if (is_object($data)) {
            unset($data->submitbutton);
        }
        return $data;
    }

    /**
     * Extra the form.
     *
     * @param  array $data
     * @param  array $files
     * @return array
     */
    public function validation($data, $files) {
        $data = $this->get_submitted_data();        // To remove extra fields (sesskey, __qf_, ...).
        unset($data->submitbutton);

        $data->descriptionformat = $data->description['format'];
        $data->description = $data->description['text'];

        $template = new \tool_lp\template(0, $data);
        $errors = $template->get_errors();

        // The context ID is not submitted via this form.
        if (isset($errors['contextid'])) {
            unset($errors['contextid']);
        }

        return $errors;
    }

}
