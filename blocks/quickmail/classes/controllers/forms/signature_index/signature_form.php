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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\controllers\forms\signature_index;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_quickmail\controllers\support\controller_form;
use block_quickmail_string;
use block_quickmail_config;

class signature_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        // Select_signature_id (select).
        $mform->addElement(
            'select',
            'select_signature_id',
            block_quickmail_string::get('select_signature_for_edit'),
            $this->get_user_signature_options()
        );
        $mform->setType(
            'select_signature_id',
            PARAM_INT
        );
        $mform->setDefault(
            'select_signature_id',
            $this->get_selected_signature('id') ?: 0
        );

        $mform->addElement('html', '<hr>');

        $mform->addElement('hidden', 'signature_id');
        $mform->setType('signature_id', PARAM_TEXT);
        $mform->setDefault('signature_id', $this->get_selected_signature('id') ?: 0);

        // Title (text).
        $mform->addElement(
            'text',
            'title',
            block_quickmail_string::get('title')
        );
        $mform->setType(
            'title',
            PARAM_TEXT
        );
        $mform->setDefault(
            'title',
            $this->get_selected_signature('title')
        );

        $mform->addRule('title', get_string('required'), 'required', '', 'server');

        // Signature_editor (editor).
        $mform->addElement(
            'editor',
            'signature_editor',
            block_quickmail_string::get('signature'),
            null,
            $this->get_editor_options()
        )->setValue([
            'text' => $this->get_selected_signature('signature')
        ]);

        $mform->setType(
            'signature_editor',
            PARAM_RAW
        );

        $mform->addRule('signature_editor', get_string('required'), 'required', '', 'server');

        // Default_flag (checkbox).
        $mform->addElement(
            'checkbox',
            'default_flag',
            get_string('default')
        );
        $mform->setType(
            'default_flag',
            PARAM_BOOL
        );
        $mform->setDefault(
            'default_flag',
            $this->get_selected_signature('default_flag')
        );

        // Buttons!
        $buttons = [
            $mform->createElement('cancel', 'cancelbutton', get_string('back')),
        ];

        if ($this->get_selected_signature('id')) {
            $buttons = array_merge($buttons, [
                $mform->createElement('submit', 'update', get_string('update')),
                $mform->createElement('submit', 'delete', get_string('delete')),
            ]);
        } else {
            $buttons = array_merge($buttons, [
                $mform->createElement('submit', 'save', get_string('save', 'block_quickmail')),
            ]);
        }

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Returns the current user's signatures for selection with a prepended "new signature" option
     *
     * @return array
     */
    private function get_user_signature_options() {
        return [0 => 'Create New'] + $this->get_custom_data('user_signature_array');
    }

    /**
     * Returns the given param for the currently selected signature, if any, defaulting to empty string
     *
     * @param  mixed  $attr
     * @return mixed
     */
    private function get_selected_signature($attr) {
        return ! empty($this->get_custom_data('selected_signature'))
            ? $this->get_custom_data('selected_signature')->get($attr)
            : '';
    }

    /**
     * Returns an array of text editor master options
     *
     * @return array
     */
    private function get_editor_options() {
        return block_quickmail_config::get_editor_options($this->get_custom_data('context'));
    }

}
