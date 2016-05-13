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
 * Form to edit a users editor preferences.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_edit_editor_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_edit_editor_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition () {
        global $CFG, $COURSE;

        $mform = $this->_form;

        $editors = editors_get_enabled();
        if (count($editors) > 1) {
            $choices = array('' => get_string('defaulteditor'));
            $firsteditor = '';
            foreach (array_keys($editors) as $editor) {
                if (!$firsteditor) {
                    $firsteditor = $editor;
                }
                $choices[$editor] = get_string('pluginname', 'editor_' . $editor);
            }
            $mform->addElement('select', 'preference_htmleditor', get_string('textediting'), $choices);
            $mform->addHelpButton('preference_htmleditor', 'textediting');
            $mform->setDefault('preference_htmleditor', '');
        } else {
            // Empty string means use the first chosen text editor.
            $mform->addElement('hidden', 'preference_htmleditor');
            $mform->setDefault('preference_htmleditor', '');
            $mform->setType('preference_htmleditor', PARAM_PLUGIN);
        }

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}


