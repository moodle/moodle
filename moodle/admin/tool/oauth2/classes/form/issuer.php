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

    /** @var string $persistentclass */
    protected static $persistentclass = 'core\\oauth2\\issuer';

    /** @var array $fieldstoremove */
    protected static $fieldstoremove = array('submitbutton', 'action');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;
        $issuer = $this->get_persistent();

        $docslink = optional_param('docslink', '', PARAM_ALPHAEXT);
        if ($docslink) {
            $name = s($issuer->get('name'));
            $mform->addElement('html', $OUTPUT->doc_link($docslink, get_string('issuersetuptype', 'tool_oauth2', $name)));
        } else {
            $mform->addElement('html', $OUTPUT->page_doc_link(get_string('issuersetup', 'tool_oauth2')));
        }

        // Name.
        $mform->addElement('text', 'name', get_string('issuername', 'tool_oauth2'));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'issuername', 'tool_oauth2');

        // Client ID.
        $mform->addElement('text', 'clientid', get_string('issuerclientid', 'tool_oauth2'));
        $mform->addRule('clientid', null, 'required', null, 'client');
        $mform->addRule('clientid', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('clientid', 'issuerclientid', 'tool_oauth2');

        // Client Secret.
        $mform->addElement('text', 'clientsecret', get_string('issuerclientsecret', 'tool_oauth2'));
        $mform->addRule('clientsecret', null, 'required', null, 'client');
        $mform->addRule('clientsecret', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('clientsecret', 'issuerclientsecret', 'tool_oauth2');

        // Login scopes.
        $mform->addElement('text', 'loginscopes', get_string('issuerloginscopes', 'tool_oauth2'));
        $mform->addRule('loginscopes', null, 'required', null, 'client');
        $mform->addRule('loginscopes', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('loginscopes', 'issuerloginscopes', 'tool_oauth2');

        // Login scopes offline.
        $mform->addElement('text', 'loginscopesoffline', get_string('issuerloginscopesoffline', 'tool_oauth2'));
        $mform->addRule('loginscopesoffline', null, 'required', null, 'client');
        $mform->addRule('loginscopesoffline', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('loginscopesoffline', 'issuerloginscopesoffline', 'tool_oauth2');

        // Login params.
        $mform->addElement('text', 'loginparams', get_string('issuerloginparams', 'tool_oauth2'));
        $mform->addRule('loginparams', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('loginparams', 'issuerloginparams', 'tool_oauth2');

        // Login params offline.
        $mform->addElement('text', 'loginparamsoffline', get_string('issuerloginparamsoffline', 'tool_oauth2'));
        $mform->addRule('loginparamsoffline', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('loginparamsoffline', 'issuerloginparamsoffline', 'tool_oauth2');

        // Base Url.
        $mform->addElement('text', 'baseurl', get_string('issuerbaseurl', 'tool_oauth2'));
        $mform->addRule('baseurl', get_string('maximumchars', '', 1024), 'maxlength', 1024, 'client');
        $mform->addHelpButton('baseurl', 'issuerbaseurl', 'tool_oauth2');

        // Allowed Domains.
        $mform->addElement('text', 'alloweddomains', get_string('issueralloweddomains', 'tool_oauth2'));
        $mform->addRule('alloweddomains', get_string('maximumchars', '', 1024), 'maxlength', 1024, 'client');
        $mform->addHelpButton('alloweddomains', 'issueralloweddomains', 'tool_oauth2');

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
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'enabled', $issuer->get('enabled'));
        $mform->setType('enabled', PARAM_BOOL);

        $mform->addElement('hidden', 'id', $issuer->get('id'));
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges', 'tool_oauth2'));
    }

}

