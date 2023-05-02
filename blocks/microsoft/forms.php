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
 * Define forms used by the plugin.
 *
 * @package block_microsoft
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Form to configure course sync options.
 */
class block_microsoft_course_sync_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);

        // Sync options.
        $syncoptions = [
            MICROSOFT365_COURSE_SYNC_DISABLED => get_string('course_sync_option_disabled', 'block_microsoft'),
            MICROSOFT365_COURSE_SYNC_ENABLED => get_string('course_sync_option_enabled', 'block_microsoft'),
        ];
        $mform->addElement('select', 'sync', get_string('course_sync_option', 'block_microsoft'), $syncoptions);
        $mform->setDefault('sync', MICROSOFT365_COURSE_SYNC_DISABLED);

        $this->add_action_buttons();
    }
}

/**
 * Form to configure course reset actions.
 */
class block_microsoft_course_configure_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);

        $resetoptions = [];
        $resetoptions[] = $mform->createElement('radio', 'reset_setting', '',
            get_string('reset_option_do_nothing', 'block_microsoft'), COURSE_SYNC_RESET_COURSE_SETTING_DO_NOTHING);
        $coursesyncsetting = get_config('local_o365', 'coursesync');
        if ($coursesyncsetting == 'oncustom') {
            $resetoptions[] = $mform->createElement('radio', 'reset_setting', '',
                get_string('reset_option_disconnect_only', 'block_microsoft'),
                COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_ONLY);
        }
        $resetoptions[] = $mform->createElement('radio', 'reset_setting', '',
            get_string('reset_option_disconnect_and_create_new', 'block_microsoft'),
            COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_AND_CREATE_NEW);
        $mform->addGroup($resetoptions, 'resetsettinggroup', get_string('course_reset_option', 'block_microsoft'),
            '<br/>', false);
        $mform->setDefault('reset_setting', COURSE_SYNC_RESET_COURSE_SETTING_DO_NOTHING);

        $this->add_action_buttons();
    }
}
