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
 * Age and location verification mform.
 *
 * @package     core_auth
 * @copyright   2018 Mihail Geshoski <mihail@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_auth\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

use moodleform;

/**
 * Age and location verification mform class.
 *
 * @copyright 2018 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verify_age_location_form extends moodleform {
    /**
     * Defines the form fields.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'age', get_string('whatisyourage'), array('optional'  => false));
        $mform->setType('age', PARAM_RAW);
        $mform->addRule('age', null, 'required', null, 'client');
        $mform->addRule('age', null, 'numeric', null, 'client');

        $countries = get_string_manager()->get_list_of_countries();
        $defaultcountry[''] = get_string('selectacountry');
        $countries = array_merge($defaultcountry, $countries);
        $mform->addElement('select', 'country', get_string('wheredoyoulive'), $countries);
        $mform->addRule('country', null, 'required', null, 'client');
        $mform->setDefault('country', $CFG->country);

        $this->add_action_buttons(true, get_string('proceed'));
    }
}
