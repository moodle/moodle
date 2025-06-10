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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class report_edit_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class report_edit_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        global $COURSE;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('filter', 'block_configurable_reports'));

        $this->_customdata->add_filter_elements($mform);

        $mform->addElement('hidden', 'id', $this->_customdata->config->id);
        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('id', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);

        // Buttons.
        $this->add_action_buttons(true, get_string('filter_apply', 'block_configurable_reports'));
    }

}
