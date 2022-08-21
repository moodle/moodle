<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_plugnmeet configuration form.
 *
 * @package     mod_plugnmeet
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

if (!class_exists("PlugNmeetHelper")) {
    require($CFG->dirroot . '/mod/plugnmeet/helpers/helper.php');

}

/**
 * Module instance settings form.
 *
 * @package     mod_plugnmeet
 * @copyright   2022 mynaparrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_plugnmeet_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('roomtitle', 'mod_plugnmeet'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        $mform->addElement(
            'textarea',
            'welcomemessage',
            get_string("welcome_message", "mod_plugnmeet"),
            'wrap="virtual" rows="5" cols="50"'
        );
        $mform->setType('welcomemessage', PARAM_CLEANHTML);

        $mform->addElement('text', 'maxparticipants', get_string("max_participants", "mod_plugnmeet"));
        $mform->setType('maxparticipants', PARAM_INT);

        $roommetadata = array();
        if (isset($this->get_current()->roommetadata)) {
            $roommetadata = json_decode($this->get_current()->roommetadata, true);
        }

        $mform->addElement('header', 'roomfeatures', get_string('room_features', 'mod_plugnmeet'));
        PlugNmeetHelper::get_room_features($roommetadata, $mform);

        $mform->addElement('header', 'other_features', get_string('other_features', 'mod_plugnmeet'));

        PlugNmeetHelper::get_chat_features($roommetadata, $mform);
        $mform->addElement('html', '<hr />');
        PlugNmeetHelper::get_shared_note_pad_features($roommetadata, $mform);

        $mform->addElement('html', '<hr />');
        PlugNmeetHelper::get_whiteboard_features($roommetadata, $mform);

        $mform->addElement('html', '<hr />');
        PlugNmeetHelper::get_external_media_player_features($roommetadata, $mform);

        $mform->addElement('html', '<hr />');
        PlugNmeetHelper::get_waiting_room_features($roommetadata, $mform);

        $mform->addElement('html', '<hr />');
        PlugNmeetHelper::get_breakout_room_features($roommetadata, $mform);

        $mform->addElement('html', '<hr />');
        PlugNmeetHelper::get_display_external_link_features($roommetadata, $mform);

        $mform->addElement('header', 'defaultlock', get_string('defaultlock', 'mod_plugnmeet'));
        PlugNmeetHelper::get_default_lock_settings($roommetadata, $mform);

        // Availability.
        $mform->addElement('header', 'availabilityhdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'available', get_string('available', 'plugnmeet'), array('optional' => true));
        $mform->setDefault('available', 0);

        $mform->addElement('date_time_selector', 'deadline', get_string('deadline', 'plugnmeet'), array('optional' => true));
        $mform->setDefault('deadline', 0);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
