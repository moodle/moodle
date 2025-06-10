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

require_once($CFG->libdir . '/formslib.php');

/**
 * Class userstats_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class userstats_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {

        $mform =& $this->_form;

        $mform->addElement('header', 'crformheader', get_string('userstats', 'block_configurable_reports'), '');

        $userstats = [
            'logins' => get_string('statslogins', 'block_configurable_reports'),
            'activityview' => get_string('activityview', 'block_configurable_reports'),
            'activitypost' => get_string('activitypost', 'block_configurable_reports'),
        ];
        $userstats['coursededicationtime'] = get_string('coursededicationtime', 'block_configurable_reports');

        $mform->addElement('select', 'stat', get_string('stat', 'block_configurable_reports'), $userstats);

        $this->_customdata['compclass']->add_form_elements($mform, $this);

        // Buttons.
        $this->add_action_buttons(true, get_string('add'));
    }

    /**
     * Server side rules do not work for uploaded files, implement serverside rules here if needed.
     *
     * @param array $data  array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *                     or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files): array {
        global $CFG;
        $errors = parent::validation($data, $files);
        $errors = $this->_customdata['compclass']->validate_form_elements($data, $errors);
        if ($data['stat'] !== 'coursededicationtime' && (!isset($CFG->enablestats) || !$CFG->enablestats)) {
            $errors['stat'] = get_string('globalstatsshouldbeenabled', 'block_configurable_reports');
        }

        return $errors;
    }

}
