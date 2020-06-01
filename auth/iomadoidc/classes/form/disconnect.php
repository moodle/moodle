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
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_iomadoidc\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * OIDC Disconnect Form.
 */
class disconnect extends \moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        global $USER, $DB, $CFG;

        // IOMAD
        require_once($CFG->dirroot . '/local/iomad/lib/company.php');
        $companyid = iomad::get_my_companyid(context_system::instance(), false);
        if (!empty($companyid)) {
            $postfix = "_$companyid";
        } else {
            $postfix = "";
        }

        if (!empty($this->_customdata['userid'])) {
            $userrec = $DB->get_record('user', ['id' => $this->_customdata['userid']]);
        } else {
            $userrec = $DB->get_record('user', ['id' => $USER->id]);
        }

        $authconfig = get_config('auth_iomadoidc');
        $configname = "opname$postfix";
        $opname = (!empty($authconfig->$configname)) ? $authconfig->$configname : get_string('pluginname', 'auth_iomadoidc');

        $mform =& $this->_form;
        $mform->addElement('html', \html_writer::tag('h4', get_string('ucp_disconnect_title', 'auth_iomadoidc', $opname)));
        $mform->addElement('html', \html_writer::div(get_string('ucp_disconnect_details', 'auth_iomadoidc', $opname)));
        $mform->addElement('html', '<br />');
        $mform->addElement('hidden', 'redirect', $this->_customdata['redirect']);
        $mform->setType('redirect', PARAM_URL);
        $mform->addElement('hidden', 'donotremovetokens', $this->_customdata['donotremovetokens']);
        $mform->setType('donotremovetokens', PARAM_BOOL);

        $mform->addElement('header', 'userdetails', get_string('userdetails'));

        $newmethod = [];
        $attributes = [];
        $manualenabled = (is_enabled_auth('manual') === true) ? true : false;
        if ($manualenabled === true) {
            $newmethod[] =& $mform->createElement('radio', 'newmethod', '', 'manual', 'manual', $attributes);
        }
        if (!empty($this->_customdata['prevmethod'])) {
            $prevmethod = $this->_customdata['prevmethod'];
            $newmethod[] =& $mform->createElement('radio', 'newmethod', '', $prevmethod, $prevmethod, $attributes);
        }
        $mform->addGroup($newmethod, 'newmethodar', get_string('errorauthdisconnectnewmethod', 'auth_iomadoidc'), [' '], false);
        if (!empty($this->_customdata['prevmethod'])) {
            $mform->setDefault('newmethod', $this->_customdata['prevmethod']);
        } else if ($manualenabled === true) {
            $mform->setDefault('newmethod', 'manual');
        }

        if ($manualenabled === true) {
            $mform->addElement('html', \html_writer::div(get_string('errorauthdisconnectifmanual', 'auth_iomadoidc')));
            $mform->addElement('text', 'username', get_string('username'));
            $mform->addElement('passwordunmask', 'password', get_string('password'));
            $mform->setType('username', PARAM_USERNAME);
            $mform->disabledIf('username', 'newmethod', 'neq', 'manual');
            $mform->disabledIf('password', 'newmethod', 'neq', 'manual');

            // If the user cannot choose a username, set it to their current username and freeze.
            if (isset($this->_customdata['canchooseusername']) && $this->_customdata['canchooseusername'] == false) {
                $mform->setDefault('username', $userrec->username);
                $element = $mform->getElement('username');
                $element->freeze();
            }
        }

        $this->add_action_buttons();
    }
}
