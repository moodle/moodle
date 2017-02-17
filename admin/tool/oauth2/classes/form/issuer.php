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
 * This file contains the form add/update oauth2 issuer.
 *
 * @package   tool_oauth2
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_oauth2\form;
defined('MOODLE_INTERNAL') || die();

use stdClass;
use core\form\persistent;

/**
 * Issuer form.
 *
 * @package   tool_oauth2
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issuer extends persistent {

    protected static $persistentclass = 'core\\oauth2\\issuer';

    protected static $fieldstoremove = array('submitbutton', 'action');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;
        $provider = $this->get_persistent();

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Name.
        $mform->addElement('text', 'name', get_string('issuername', 'tool_oauth2'), 'maxlength="255"');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'issuername', 'tool_oauth2');

        // Client ID.
        $mform->addElement('text', 'clientid', get_string('issuerclientid', 'tool_oauth2'), 'maxlength="255"');
        $mform->addRule('clientid', null, 'required', null, 'client');
        $mform->addRule('clientid', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('clientid', 'issuerclientid', 'tool_oauth2');

        // Client Secret.
        $mform->addElement('text', 'clientsecret', get_string('issuerclientsecret', 'tool_oauth2'), 'maxlength="255"');
        $mform->addRule('clientsecret', null, 'required', null, 'client');
        $mform->addRule('clientsecret', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('clientsecret', 'issuerclientsecret', 'tool_oauth2');

        // Base Url.
        $mform->addElement('text', 'baseurl', get_string('issuerbaseurl', 'tool_oauth2'), 'maxlength="1024"');
        $mform->addRule('baseurl', null, 'required', null, 'client');
        $mform->addRule('baseurl', get_string('maximumchars', '', 1024), 'maxlength', 1024, 'client');
        $mform->addHelpButton('baseurl', 'issuerbaseurl', 'tool_oauth2');

        // Offline access type
        $options = $provider->get_behaviour_list();
        $mform->addElement('select', 'behaviour', get_string('issuerbehaviour', 'tool_oauth2'), $options);
        $mform->addHelpButton('behaviour', 'issuerbehaviour', 'tool_oauth2');

        // Image.
        $mform->addElement('text', 'image', get_string('issuerimage', 'tool_oauth2'), 'maxlength="1024"');
        $mform->addRule('image', get_string('maximumchars', '', 1024), 'maxlength', 1024, 'client');
        $mform->addHelpButton('image', 'issuername', 'tool_oauth2');

        // Show on login page.
        $mform->addElement('checkbox', 'showonloginpage', get_string('issuershowonloginpage', 'tool_oauth2'));
        $mform->addHelpButton('showonloginpage', 'issuershowonloginpage', 'tool_oauth2');
        

        $mform->addElement('hidden', 'sortorder');
        $mform->setType('sortorder', PARAM_INT);

        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_RAW);

        $mform->addElement('hidden', 'id', $provider->get('id'));
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges', 'tool_oauth2'));
    }

}

