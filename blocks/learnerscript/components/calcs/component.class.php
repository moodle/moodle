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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
use block_learnerscript\local\componentbase;
use block_learnerscript\local\ls;
class component_calcs extends componentbase {

    function init() {
        $this->plugins = true;
        $this->ordering = false;
        $this->form = false;
        $this->help = true;
    }

    function add_form_elements(&$mform, $components) {
        global $DB, $CFG;

        $components = (new ls)->cr_unserialize($components);
        $options = array();

        if ($this->config->type != 'sql') {
            if (!is_array($components) || empty($components['columns']['elements']))
                print_error('nocolumns');

            $columns = $components['columns']['elements'];

            $calcs = isset($components['calcs']['elements']) ? $components['calcs']['elements'] : array();
            $columnsused = array();
            if ($calcs) {
                foreach ($calcs as $c) {
                    $columnsused[] = $c['formdata']->column;
                }
            }

            $i = 0;
            foreach ($columns as $c) {
                if (!in_array($i, $columnsused))
                    $options[$i] = $c['summary'];
                $i++;
            }
        }
        else {
            require_once($CFG->dirroot . '/blocks/learnerscript/report.class.php');
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $this->config->type . '/report.class.php');

            $reportclassname = 'block_learnerscript\lsreports\report_' . $this->config->type;
            $reportclass = new $reportclassname($this->config);

            $components = (new ls)->cr_unserialize($this->config->components);
            $config = (isset($components['customsql']['config'])) ? $components['customsql']['config'] : new stdclass;

            if (isset($config->querysql)) {

                $sql = $config->querysql;
                $sql = $reportclass->prepare_sql($sql);
                if ($rs = $reportclass->execute_query($sql)) {
                    foreach ($rs['results'] as $row) {
                        $i = 0;
                        foreach ($row as $colname => $value) {
                            $options[$i] = str_replace('_', ' ', $colname);
                            $i++;
                        }
                        break;
                    }
                   // $rs->close();
                }
            }
        }

        $mform->addElement('header', 'crformheader', get_string('coursefield', 'block_learnerscript'), '');
        $mform->addElement('select', 'column', get_string('column', 'block_learnerscript'), $options);

    }

}
