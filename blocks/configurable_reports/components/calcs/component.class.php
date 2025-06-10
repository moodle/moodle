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

/**
 * Class component_calcs
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class component_calcs extends component_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->plugins = true;
        $this->ordering = false;
        $this->form = false;
        $this->help = true;
    }

    /**
     * add_form_elements
     *
     * @param MoodleQuickForm $mform
     * @param string|object $components
     * @return void
     */
    public function add_form_elements(MoodleQuickForm $mform, $components): void {
        global $CFG;

        $components = cr_unserialize($components);
        $options = [];

        if ($this->config->type !== 'sql') {
            if (!is_array($components) || empty($components['columns']['elements'])) {
                throw new moodle_exception('nocolumns');
            }

            $columns = $components['columns']['elements'];

            $calcs = $components['calcs']['elements'] ?? [];
            $columnsused = [];
            if ($calcs) {
                foreach ($calcs as $c) {
                    $columnsused[] = $c['formdata']->column;
                }
            }

            $i = 0;
            foreach ($columns as $c) {
                if (!in_array($i, $columnsused)) {
                    $options[$i] = $c['summary'];
                }
                $i++;
            }
        } else {
            require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
            require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $this->config->type . '/report.class.php');

            $reportclassname = 'report_' . $this->config->type;
            $reportclass = new $reportclassname($this->config);

            $components = cr_unserialize($this->config->components);
            $config = $components['customsql']['config'] ?? (object) [];

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
        $mform->addElement('select', 'column', get_string('column', 'block_configurable_reports'), $options);
    }

}
