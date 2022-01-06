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
 * This file contains form for bulk changing user enrolments.
 *
 * @package    core_enrol
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * A base class that can be used to easily construct a form for use with bulk operations
 *
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class enrol_bulk_enrolment_change_form extends moodleform {

    /**
     * Defines the standard structure of the form
     */
    protected function definition() {
        $form = $this->_form;
        $users = $this->_customdata['users'];

        $statusoptions = $this->get_status_options();
        $form->addElement('html', $this->get_users_table($users, $statusoptions));
        $form->addElement('select', 'status', get_string('alterstatus', 'enrol_manual'), $statusoptions, array('optional' => true));
        $form->addElement('date_time_selector', 'timestart', get_string('altertimestart', 'enrol_manual'), array('optional' => true));
        $form->addElement('date_time_selector', 'timeend', get_string('altertimeend', 'enrol_manual'), array('optional' => true));

        $this->add_action_buttons();
    }

    /**
     * Returns an array of status options
     * @return array
     */
    protected function get_status_options() {
        return array(-1                   => get_string('nochange', 'enrol'),
                     ENROL_USER_ACTIVE    => get_string('participationactive', 'enrol'),
                     ENROL_USER_SUSPENDED => get_string('participationsuspended', 'enrol'));
    }

    /**
     * Generates an HTML table to display the users being affected by the bulk change.
     *
     * @param array $users
     * @param array $statusoptions
     * @return string
     */
    protected function get_users_table(array $users, array $statusoptions) {
        $table = new html_table();
        $table->head = array(
            get_string('name'),
            get_string('participationstatus', 'enrol'),
            get_string('enroltimestart', 'enrol'),
            get_string('enroltimeend', 'enrol'),
        );
        $table->data = array();
        foreach ($users as $user) {
            foreach ($user->enrolments as $enrolment) {
                $input = html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'bulkuser[]', 'value' => $user->id));
                $table->data[] = array(
                    fullname($user).$input,
                    $statusoptions[$enrolment->status],
                    (!empty($enrolment->timestart))?userdate($enrolment->timestart):'',
                    (!empty($enrolment->timeend))?userdate($enrolment->timeend):'',
                );
            }
        }
        return html_writer::table($table);
    }
}

/**
 * A convenience class to allow the quick generation of a confirmation form for a bulk operation.
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class enrol_bulk_enrolment_confirm_form extends enrol_bulk_enrolment_change_form {

    /**
     * Defines the standard structure of the form
     */
    protected function definition() {
        $form = $this->_form;
        $users = $this->_customdata['users'];
        $title = $this->_customdata['title'];
        $message = $this->_customdata['message'];
        $button = $this->_customdata['button'];

        $form->addElement('html', $this->get_users_table($users, $this->get_status_options()));
        $form->addElement('header', 'ebecf_header', $title);
        $form->addElement('html', html_writer::tag('p', $message));
        $this->add_action_buttons(true, $button);
    }
}