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
 * Regenerate the Private Key and Certificate files
 *
 * @package    auth_iomadsaml2
 * @copyright  Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2\form;

defined('MOODLE_INTERNAL') || die();

use moodleform;

require_once("$CFG->libdir/formslib.php");

/**
 * Regenerate the Private Key and Certificate files
 *
 * @copyright  Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class regenerate extends moodleform {

    /**
     * Definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'countryname', get_string('countryname', 'auth_iomadsaml2'), 'size=64');
        $mform->setType('countryname', PARAM_TEXT);
        $mform->addRule('countryname', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'stateorprovincename', get_string('stateorprovincename', 'auth_iomadsaml2'), 'size=64');
        $mform->setType('stateorprovincename', PARAM_TEXT);
        $mform->addRule('stateorprovincename', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'localityname', get_string('localityname', 'auth_iomadsaml2'), 'size=64');
        $mform->setType('localityname', PARAM_TEXT);
        $mform->addRule('localityname', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'organizationname', get_string('organizationname', 'auth_iomadsaml2'), 'size=64');
        $mform->setType('organizationname', PARAM_TEXT);
        $mform->addRule('organizationname', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'organizationalunitname', get_string('organizationalunitname', 'auth_iomadsaml2'), 'size=64');
        $mform->setType('organizationalunitname', PARAM_TEXT);
        $mform->addRule('organizationalunitname', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'commonname', get_string('commonname', 'auth_iomadsaml2'), 'size=64');
        $mform->setType('commonname', PARAM_TEXT);
        $mform->addRule('commonname', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'), 'size=64');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('required', 'auth_iomadsaml2'),
                'required', null, 'client');

        $mform->addElement('text', 'expirydays', get_string('expirydays', 'auth_iomadsaml2'), 'size=5');
        $mform->setType('expirydays', PARAM_INT);
        $mform->addRule('expirydays', get_string('requireint', 'auth_iomadsaml2'),
                'numeric', null, 'client');
        $mform->addRule('expirydays', get_string('requireint', 'auth_iomadsaml2'),
                'required', null, 'client');

        $this->add_action_buttons(true, get_string('regenerate_submit', 'auth_iomadsaml2'));

    }

}

