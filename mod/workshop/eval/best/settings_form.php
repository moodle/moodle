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
 * Best evaluation settings form
 *
 * @package    workshopeval
 * @subpackage best
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

class workshop_best_evaluation_settings_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $plugindefaults = get_config('workshopeval_best');
        $current        = $this->_customdata['current'];
        $workshop       = $this->_customdata['workshop'];

        $mform->addElement('header', 'general', get_string('settings', 'workshopeval_best'));

        $label = get_string('evaluationmethod', 'workshop');
        $mform->addElement('static', 'methodname', $label, get_string('pluginname', 'workshopeval_best'));
        $mform->addHelpButton('methodname', 'evaluationmethod', 'workshop');

        $options = array();
        for ($i = 9; $i >= 1; $i = $i-2) {
            $options[$i] = get_string('comparisonlevel' . $i, 'workshopeval_best');
        }
        $label = get_string('comparison', 'workshopeval_best');
        $mform->addElement('select', 'comparison', $label, $options);
        $mform->addHelpButton('comparison', 'comparison', 'workshopeval_best');
        $mform->setDefault('comparison', $plugindefaults->comparison);

        $mform->addElement('submit', 'submit', get_string('aggregategrades', 'workshop'));

        $this->set_data($current);
    }
}
