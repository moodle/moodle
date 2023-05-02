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
 * Manual user match form.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Single, manual user match form.
 */
class manualusermatch extends \moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        global $USER, $DB;

        if (!empty($this->_customdata['userid'])) {
            $userrec = $DB->get_record('user', ['id' => $this->_customdata['userid']]);
        } else {
            $userrec = $DB->get_record('user', ['id' => $USER->id]);
        }

        $authconfig = get_config('auth_oidc');
        $opname = (!empty($authconfig->opname)) ? $authconfig->opname : get_string('pluginname', 'auth_oidc');

        $mform =& $this->_form;
        $mform->addElement('html', \html_writer::tag('h4', get_string('acp_userconnections_manualmatch_title', 'local_o365')));
        $mform->addElement('html', \html_writer::div(get_string('acp_userconnections_manualmatch_details', 'local_o365')));
        $mform->addElement('html', '<br />');

        $mform->addElement('header', 'userdetails', get_string('userdetails'));

        $musernametext = fullname($userrec).' ('.$userrec->username.')';
        $label = get_string('acp_userconnections_manualmatch_musername', 'local_o365');
        $mform->addElement('static', 'musername', $label, $musernametext);
        $mform->addElement('text', 'o365username', get_string('acp_userconnections_manualmatch_o365username', 'local_o365'));
        $mform->addElement('checkbox', 'uselogin', get_string('acp_userconnections_manualmatch_uselogin', 'local_o365'));
        $mform->setType('o365username', PARAM_TEXT);

        $this->add_action_buttons();
    }
}
