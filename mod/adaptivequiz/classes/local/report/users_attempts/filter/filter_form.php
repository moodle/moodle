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

namespace mod_adaptivequiz\local\report\users_attempts\filter;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use html_writer;
use moodle_url;
use moodleform;

final class filter_form extends moodleform {

    /**
     * Overrides the parent method to remove mandatory closing of fieldset before the submit button.
     * Ignores the arguments, as it contains its own logic to display the button.
     *
     * @inheritDoc
     */
    public function add_action_buttons($cancel = true, $submitlabel=null) {
        $form =& $this->_form;
        $form->addElement('submit', 'prefssubmit', get_string('reportattemptsfilterformsubmit', 'adaptivequiz'));
    }

    protected function definition() {
        $form = $this->_form;

        $form->addElement('header', 'filterheader', get_string('reportattemptsfilterformheader', 'adaptivequiz'));

        $enrolmentoptions = [
            filter_options::ENROLLED_USERS_WITH_NO_ATTEMPTS
                => get_string('reportattemptsenrolledwithnoattempts', 'adaptivequiz'),
            filter_options::ENROLLED_USERS_WITH_ATTEMPTS
                => get_string('reportattemptsenrolledwithattempts', 'adaptivequiz'),
            filter_options::BOTH_ENROLLED_AND_NOT_ENROLLED_USERS_WITH_ATTEMPTS
                => get_string('reportattemptsbothenrolledandnotenrolled', 'adaptivequiz'),
            filter_options::NOT_ENROLLED_USERS_WITH_ATTEMPTS
                => get_string('reportattemptsnotenrolled', 'adaptivequiz')
        ];
        $form->addElement('select', 'users', get_string('reportattemptsfilterusers', 'adaptivequiz'),
            $enrolmentoptions);
        $form->setDefault('users', filter_options::users_option_default());

        $form->addElement('advcheckbox', 'includeinactiveenrolments',
            get_string('reportattemptsfilterincludeinactiveenrolments', 'adaptivequiz'), '&nbsp;', null, [0, 1]);
        $form->setDefault('includeinactiveenrolments', filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT);
        $form->addHelpButton('includeinactiveenrolments', 'reportattemptsfilterincludeinactiveenrolments',
            'adaptivequiz');
        $form->disabledIf('includeinactiveenrolments', 'users', 'eq',
            filter_options::BOTH_ENROLLED_AND_NOT_ENROLLED_USERS_WITH_ATTEMPTS);

        $this->add_action_buttons();
    }
}
