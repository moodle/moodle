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
 * This file contains the form add/update oauth2 user_field_mapping.
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
class user_field_mapping extends persistent {

    /** @var string $persistentclass */
    protected static $persistentclass = 'core\\oauth2\\user_field_mapping';

    /** @var array $fieldstoremove */
    protected static $fieldstoremove = array('submitbutton', 'action');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;
        $userfieldmapping = $this->get_persistent();

        // External.
        $mform->addElement('text', 'externalfield', get_string('userfieldexternalfield', 'tool_oauth2'));
        $mform->addRule('externalfield', null, 'required', null, 'client');
        $mform->addRule('externalfield', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('externalfield', 'userfieldexternalfield', 'tool_oauth2');

        // Internal.
        $choices = $userfieldmapping->get_internalfield_list();
        $mform->addElement('selectgroups', 'internalfield', get_string('userfieldinternalfield', 'tool_oauth2'), $choices);
        $mform->addHelpButton('internalfield', 'userfieldinternalfield', 'tool_oauth2');

        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'issuerid', $userfieldmapping->get('issuerid'));
        $mform->setConstant('issuerid', $this->_customdata['issuerid']);
        $mform->setType('issuerid', PARAM_INT);

        $mform->addElement('hidden', 'id', $userfieldmapping->get('id'));
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges', 'tool_oauth2'));
    }

}

