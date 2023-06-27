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
 * Report search form class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_configlog\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Report search form class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (paulh@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search extends \moodleform {

    /**
     * Form definition
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        // By default just show the 'setting' field.
        $mform->addElement('header', 'heading', get_string('search'));
        $mform->addElement('text', 'setting', get_string('setting', 'report_configlog'));
        $mform->setType('setting', PARAM_TEXT);

        // Rest of the search fields.
        $mform->addElement('text', 'value', get_string('value', 'report_configlog'));
        $mform->setType('value', PARAM_TEXT);
        $mform->addHelpButton('value', 'value', 'report_configlog');
        $mform->setAdvanced('value', true);

        $mform->addElement('text', 'user', get_string('user', 'report_configlog'));
        $mform->setType('user', PARAM_TEXT);
        $mform->addHelpButton('user', 'user', 'report_configlog');
        $mform->setAdvanced('user', true);

        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'report_configlog'), ['optional' => true]);
        $mform->setAdvanced('datefrom', true);

        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'report_configlog'), ['optional' => true]);
        $mform->setAdvanced('dateto', true);

        $this->add_action_buttons(false, get_string('search'));
    }
}