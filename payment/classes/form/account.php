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
 * Class account
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment\form;

use core\form\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Class account
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class account extends persistent {

    /** @var string The persistent class. */
    protected static $persistentclass = 'core_payment\account';

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'contextid');

        $mform->addElement('text', 'name', get_string('accountname', 'payment'), 'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'server');

        $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="100"');
        $mform->setType('idnumber', PARAM_RAW_TRIMMED);
        $mform->addRule('idnumber', get_string('maximumchars', '', 100), 'maxlength', 100, 'server');

        $mform->addElement('advcheckbox', 'enabled', get_string('enable'));
        $this->add_action_buttons();
    }
}
