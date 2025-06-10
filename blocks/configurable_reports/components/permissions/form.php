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
 * Class permissions_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class permissions_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        $mform =& $this->_form;

        $mform->addElement('static', 'help', '', get_string('conditionexprhelp', 'block_configurable_reports'));
        $mform->addElement('text', 'conditionexpr', get_string('conditionexpr', 'block_configurable_reports'), 'size="50"');
        $mform->addHelpButton('conditionexpr', 'conditionexpr_permissions', 'block_configurable_reports');
        $mform->setType('conditionexpr', PARAM_RAW);

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
        // TODO - this reg expr can be improved.
        $regex = "/(\(*\s*\bc\d{1,2}\b\s*\(*\)*\s*(\(|and|or)\s*)+\(*\s*\bc\d{1,2}\b\s*\(*\)*\s*$/i";
        if (!preg_match($regex, $data['conditionexpr'])) {
            $errors['conditionexpr'] = get_string('badconditionexpr', 'block_configurable_reports');
        }

        if (substr_count($data['conditionexpr'], '(') != substr_count($data['conditionexpr'], ')')) {
            $errors['conditionexpr'] = get_string('badconditionexpr', 'block_configurable_reports');
        }

        if (isset($this->_customdata['elements']) && is_array($this->_customdata['elements'])) {
            $elements = $this->_customdata['elements'];
            $nel = count($elements);
            if (!empty($elements) && $nel > 1) {
                preg_match_all('/(\d+)/', $data['conditionexpr'], $matches, PREG_PATTERN_ORDER);
                foreach ($matches[0] as $num) {
                    if ($num > $nel) {
                        $errors['conditionexpr'] = get_string('badconditionexpr', 'block_configurable_reports');
                        break;
                    }
                }
            }
        }

        return $errors;
    }

}
