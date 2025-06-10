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
 * Class pie_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class pie_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        global $CFG;

        $mform =& $this->_form;
        $options = [];

        $report = $this->_customdata['report'];

        if ($report->type !== 'sql') {
            $components = cr_unserialize($this->_customdata['report']->components);

            if (!is_array($components) || empty($components['columns']['elements'])) {
                throw new moodle_exception('nocolumns');
            }

            $columns = $components['columns']['elements'];
            foreach ($columns as $c) {
                if (!empty($c['summary'])) {
                    $options[] = $c['summary'];
                }
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
                        $i = 0;
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

        $mform->addElement('header', 'crformheader', get_string('coursefield', 'block_configurable_reports'), '');

        $mform->addElement('select', 'areaname', get_string('pieareaname', 'block_configurable_reports'), $options);
        $mform->addElement('select', 'areavalue', get_string('pieareavalue', 'block_configurable_reports'), $options);
        $mform->addElement('checkbox', 'group', get_string('groupvalues', 'block_configurable_reports'));

        $mform->addElement('header', 'legendheader', get_string('legendheader', 'block_configurable_reports'), '');
        $mform->addElement(
            'static',
            'legendheaderdesc',
            get_string('description', 'block_configurable_reports'),
            get_string('legendheaderdesc', 'block_configurable_reports')
        );

        $repeatarray = [];
        $repeatarray[] =
            $mform->createElement('text', 'piechart_label', get_string('piechart_label', 'block_configurable_reports', '{no}'));
        $repeatarray[] = $mform->createElement(
            'text',
            'piechart_label_color',
            get_string('piechart_label_color', 'block_configurable_reports', '{no}')
        );
        $mform->setType('piechart_label', PARAM_TEXT);
        $mform->setType('piechart_label_color', PARAM_TEXT);

        $repeatno = 3;
        $repeateloptions = [];

        $this->repeat_elements(
            $repeatarray,
            $repeatno,
            $repeateloptions,
            'piechart_label_repeats',
            'piechart_add_colors',
            1,
            get_string('piechart_add_colors', 'block_configurable_reports'),
            true
        );

        $mform->addElement(
            'header',
            'generalcolorpaletteheader',
            get_string('generalcolorpalette', 'block_configurable_reports'),
            ''
        );
        $mform->addElement(
            'textarea',
            'generalcolorpalette',
            get_string('generalcolorpalette', 'block_configurable_reports'),
            'rows="10" cols="7"'
        );
        $mform->addHelpButton('generalcolorpalette', 'generalcolorpalette', 'block_configurable_reports');

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
        $errors = [];

        $length = count($data['piechart_label']);
        for ($i = 0; $i < $length; $i++) {
            if (!empty($data['piechart_label'][$i])) {
                if (empty($data['piechart_label_color'][$i]) ||
                    !preg_match('/^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $data['piechart_label_color'][$i])) {
                    $errors["piechart_label_color[$i]"] = get_string('invalidcolorcode', 'block_configurable_reports');
                }
            }
        }

        if (!empty($data['generalcolorpalette'])) {
            $colors = explode(PHP_EOL, $data['generalcolorpalette']);
            foreach ($colors as $color) {
                if (!empty($color) && !preg_match('/^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', trim($color))) {
                    $errors['generalcolorpalette'] = get_string('invalidcolorcode', 'block_configurable_reports');
                }
            }
        }

        return $errors;
    }

}
