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
 * Class cuserfield_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class cuserfield_form extends moodleform {

    /**
     * @var string[]
     */
    public array $allowedops = [
        '=' => '=',
        '>' => '>',
        '<' => '<',
        '>=' => '>=',
        '<=' => '<=',
        '<>' => '<>',
        'LIKE' => 'LIKE',
        'NOT LIKE' => 'NOT LIKE',
        'LIKE % %' => 'LIKE % %',
    ];

    /**
     * Form definition
     */
    public function definition(): void {
        global $DB;

        $mform =& $this->_form;

        $mform->addElement('header', 'crformheader', get_string('coursefield', 'block_configurable_reports'), '');

        $columns = $DB->get_columns('user');

        $usercolumns = [];
        foreach ($columns as $c) {
            $usercolumns[$c->name] = $c->name;
        }

        if ($profile = $DB->get_records('user_info_field')) {
            foreach ($profile as $p) {
                $usercolumns['profile_' . $p->shortname] = $p->name;
            }
        }

        $mform->addElement('select', 'field', get_string('column', 'block_configurable_reports'), $usercolumns);

        $mform->addElement('select', 'operator', get_string('operator', 'block_configurable_reports'), $this->allowedops);
        $mform->addElement('text', 'value', get_string('value', 'block_configurable_reports'));
        $mform->setType('value', PARAM_RAW);
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
        global $DB, $db, $CFG;

        $errors = parent::validation($data, $files);

        if (!in_array($data['operator'], $this->allowedops)) {
            $errors['operator'] = get_string('error_operator', 'block_configurable_reports');
        }

        $columns = $DB->get_columns('user');
        $usercolumns = [];
        foreach ($columns as $c) {
            $usercolumns[$c->name] = $c->name;
        }
        if ($profile = $DB->get_records('user_info_field')) {
            foreach ($profile as $p) {
                $usercolumns['profile_' . $p->shortname] = 'profile_' . $p->shortname;
            }
        }

        if (!in_array($data['field'], $usercolumns)) {
            $errors['field'] = get_string('error_field', 'block_configurable_reports');
        }

        if (!is_numeric($data['value']) && preg_match('/^(<|>)[^(<|>)]/i', $data['operator'])) {
            $errors['value'] = get_string('error_value_expected_integer', 'block_configurable_reports');
        }

        return $errors;
    }

}
