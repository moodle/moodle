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
 * Class line_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class line_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        global $CFG;

        $mform =& $this->_form;
        $options = [0 => get_string('choose')];

        $report = $this->_customdata['report'];

        if ($report->type !== 'sql') {
            $components = cr_unserialize($this->_customdata['report']->components);

            if (!is_array($components) || empty($components['columns']['elements'])) {
                throw new moodle_exception('nocolumns');
            }

            $columns = $components['columns']['elements'];
            foreach ($columns as $c) {
                $options[] = $c['summary'];
            }
        } else {

            require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
            require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

            $reportclassname = 'report_' . $report->type;
            $reportclass = new $reportclassname($report);

            $components = cr_unserialize($report->components);
            $config = (isset($components['customsql']['config'])) ? $components['customsql']['config'] : new stdclass;

            if (isset($config->querysql)) {
                $sql = $config->querysql;
                $sql = $reportclass->prepare_sql($sql);
                if ($rs = $reportclass->execute_query($sql)) {
                    foreach ($rs as $row) {
                        $i = 1;
                        foreach ($row as $colname => $value) {
                            $options[$i] = str_replace('_', ' ', $colname);
                            $i++;
                        }
                        break;
                    }
                    $rs->close();
                }
            }
        }

        $mform->addElement('header', 'crformheader', get_string('line', 'block_configurable_reports'), '');

        $mform->addElement('select', 'xaxis', get_string('xaxis', 'block_configurable_reports'), $options);
        $mform->addRule('xaxis', null, 'required', null, 'client');

        $mform->addElement('select', 'serieid', get_string('serieid', 'block_configurable_reports'), $options);
        $mform->addRule('serieid', null, 'required', null, 'client');

        $mform->addElement('select', 'yaxis', get_string('yaxis', 'block_configurable_reports'), $options);
        $mform->addRule('yaxis', null, 'required', null, 'client');

        $mform->addElement('checkbox', 'group', get_string('groupseries', 'block_configurable_reports'));

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
        $errors = parent::validation($data, $files);

        if ($data['xaxis'] == $data['yaxis']) {
            $errors['yaxis'] = get_string('xandynotequal', 'block_configurable_reports');
        }

        return $errors;
    }

}
