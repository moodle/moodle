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
 * @package   tool_redocerts
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Site wide search-redocerts form.
 */
class tool_redocerts_form extends moodleform {
    function definition() {
        global $CFG, $DB;
        $users = $DB->get_records_sql_menu("SELECT id, concat(firstname, ' ', lastname) AS fullname
                                            FROM {user}
                                            WHERE deleted = 0
                                            ORDER BY fullname", array());
        $courses = $DB->get_records_menu('course', array(), 'fullname', 'id,fullname');
        $companies = $DB->get_records_menu('company', array(), 'name', 'id,name');
        $allusers = array(0 => get_string('all')) + $users;
        $allcourses = array(0 => get_string('all')) + $courses;
        $allcompanies = array(0 => get_string('all')) + $companies;
                                            

        $mform = $this->_form;

        $mform->addElement('header', 'searchhdr', get_string('pluginname', 'tool_redocerts'));
        $mform->setExpanded('searchhdr', true);

        $mform->addElement('autocomplete', 'user', get_string('searchusers', 'tool_redocerts'), $allusers);
        $mform->addElement('text', 'userid', get_string('userid', 'tool_redocerts'));
        $mform->addElement('autocomplete', 'course', get_string('searchcourses', 'tool_redocerts'), $allcourses);
        $mform->addElement('text', 'courseid', get_string('courseid', 'tool_redocerts'));
        $mform->addElement('autocomplete', 'company', get_string('searchcompanies', 'tool_redocerts'), $allcompanies);
        $mform->addElement('text', 'companyid', get_string('companyid', 'tool_redocerts'));
        $mform->addElement('text', 'idnumber', get_string('idnumber', 'tool_redocerts'));
        $mform->addElement('date_time_selector', 'fromdate', get_string('fromdate', 'tool_redocerts'), array('optional' => true));
        $mform->addElement('date_time_selector', 'todate', get_string('todate', 'tool_redocerts'), array('optional' => true));
        $mform->setType('idnumber', PARAM_INT);
        $mform->setType('userid', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);
        $mform->setType('companyid', PARAM_INT);

        $this->add_action_buttons(false, get_string('doit', 'tool_redocerts'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
