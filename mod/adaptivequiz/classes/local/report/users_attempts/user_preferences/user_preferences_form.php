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
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\user_preferences;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use moodleform;

class user_preferences_form extends moodleform {

    /**
     * Overrides the parent method to remove mandatory closing of fieldset before the submit button.
     * Ignores the arguments, as it contains its own logic to display the button.
     *
     * @inheritDoc
     */
    public function add_action_buttons($cancel = true, $submitlabel=null) {
        $form =& $this->_form;
        $form->addElement('submit', 'prefssubmit', get_string('reportattemptsprefsformsubmit', 'adaptivequiz'));
    }

    protected function definition() {
        $form = $this->_form;

        $form->addElement('header', 'prefsheader', get_string('reportattemptsprefsformheader', 'adaptivequiz'));

        $form->addElement('select', 'perpage', get_string('reportattemptsusersperpage', 'adaptivequiz'),
            array_combine(user_preferences::PER_PAGE_OPTIONS, user_preferences::PER_PAGE_OPTIONS));
        $form->setDefault('perpage', user_preferences::PER_PAGE_DEFAULT);

        $form->addElement('advcheckbox', 'showinitialsbar',
            get_string('reportattemptsshowinitialbars', 'adaptivequiz'), '&nbsp;', null, [0, 1]);
        $form->setDefault('showinitialsbar', user_preferences::SHOW_INITIALS_BAR_DEFAULT);

        $form->addElement('advcheckbox', 'persistentfilter',
            get_string('reportattemptspersistentfilter', 'adaptivequiz'), '&nbsp;', null, [0, 1]);
        $form->setDefault('persistentfilter', user_preferences::PERSISTENT_FILTER_DEFAULT);
        $form->addHelpButton('persistentfilter', 'reportattemptspersistentfilter', 'adaptivequiz');

        $this->add_action_buttons();
    }
}
