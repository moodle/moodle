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
 * Class reportcolumn_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class reportcolumn_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        global $CFG;

        $mform =& $this->_form;
        $mform->addElement('header', 'crformheader', get_string('reportcolumn', 'block_configurable_reports'), '');

        $reportid = optional_param('reportid', 0, PARAM_INT);
        if ($actualrid = $this->_customdata['pluginclass']->get_current_report($this->_customdata['report'])) {
            $reportid = $actualrid;
        }

        $reports = $this->_customdata['pluginclass']->get_user_reports();
        $reportoptions = [0 => get_string('choose')];

        if ($reports) {
            foreach ($reports as $r) {
                $reportoptions[$r->id] = format_string($r->name);
            }
        }

        $furl = "$CFG->wwwroot/blocks/configurable_reports/editplugin.php?id=" . $this->_customdata['report']->id .
            "&comp=columns&pname=reportcolumn";
        $options = ['onchange' => 'location.href="' . $furl . '&reportid="+document.getElementById("id_reportid").value'];
        if ($actualrid) {
            $options['disabled'] = 'disabled';
        }

        $mform->addElement('select', 'reportid', get_string('report', 'block_configurable_reports'), $reportoptions, $options);
        $mform->setDefault('reportid', $reportid);

        $columnsoptions = $this->_customdata['pluginclass']->get_report_columns($reportid);
        $mform->addElement('select', 'column', get_string('column', 'block_configurable_reports'), $columnsoptions);

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
        $errors = parent::validation($data, $files);

        $errors = $this->_customdata['compclass']->validate_form_elements($data, $errors);

        if (!$data['reportid']) {
            $errors['reportid'] = get_string('missingcolumn', 'block_configurable_reports');
        }

        if (!isset($data['column'])) {
            $errors['column'] = get_string('missingcolumn', 'block_configurable_reports');
        }

        return $errors;
    }

}
