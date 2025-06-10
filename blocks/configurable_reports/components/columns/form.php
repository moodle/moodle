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
 * Class columns_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class columns_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {

        $mform =& $this->_form;

        $mform->addElement('header', get_string('reporttable', 'block_configurable_reports'), '');

        $mform->addElement('text', 'tablewidth', get_string('tablewidth', 'block_configurable_reports'));
        $mform->setType('tablewidth', PARAM_TEXT);
        $mform->setDefault('tablewidth', '100%');
        $mform->addHelpButton('tablewidth', 'reporttable', 'block_configurable_reports');

        $options = ['center' => 'center', 'left' => 'left', 'right' => 'right'];

        $mform->addElement('SELECT', 'tablealign', get_string('tablealign', 'block_configurable_reports'), $options);
        $mform->setType('tablealign', PARAM_TEXT);
        $mform->setDefault('tablealign', 'center');

        $mform->addElement('text', 'cellspacing', get_string('tablecellspacing', 'block_configurable_reports'));
        $mform->setType('cellspacing', PARAM_INT);
        $mform->setDefault('cellspacing', '3');
        $mform->setAdvanced('cellspacing');

        $mform->addElement('text', 'cellpadding', get_string('tablecellpadding', 'block_configurable_reports'));
        $mform->setType('cellpadding', PARAM_INT);
        $mform->setDefault('cellpadding', '3');
        $mform->setAdvanced('cellpadding');

        $mform->addElement('text', 'class', get_string('tableclass', 'block_configurable_reports'));
        $mform->setType('class', PARAM_TEXT);
        $mform->setAdvanced('class');

        // Buttons.
        $this->add_action_buttons(true, get_string('update'));
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
        $errors = parent::validation($data, $files);

        if (!preg_match("/^\d+%?$/i", trim($data['tablewidth']))) {
            $errors['tablewidth'] = get_string('badtablewidth', 'block_configurable_reports');
        }

        return $errors;
    }

}
