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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;

class plugin_fcoursefield extends pluginbase {

    public function init() {
        $this->form = true;
        $this->unique = true;
        $this->singleselection = true;
        $this->fullname = get_string('fcoursefield', 'block_learnerscript');
        $this->reporttypes = array('courses');
    }

    public function summary($data) {
        return $data->field;
    }

    public function execute($finalelements, $data) {
        global $DB;
        $filterfcoursefield = optional_param('filter_fcoursefield_' . $data->field, 0, PARAM_RAW);
        if ($filterfcoursefield) {
            // Function addslashes is done in clean param.
            $filter = clean_param(base64_decode($filterfcoursefield), PARAM_CLEAN);
            list($usql, $params) = $DB->get_in_or_equal($finalelements);
            $sql = "$data->field = ? AND id $usql";
            $params = array_merge(array($filter), $params);
            if ($elements = $DB->get_records_select('course', $sql, $params)) {
                $finalelements = array_keys($elements);
            }
        }
        return $finalelements;
    }

    public function print_filter(&$mform, $data) {
        global $DB, $CFG;

        $columns = $DB->get_columns('course');
        $filteroptions = array();
        $filteroptions[''] =$this->singleselection ?
                        get_string('filter_all', 'block_learnerscript') : get_string('select') .' '. get_string($data->field);

        $coursecolumns = array();
        foreach ($columns as $c) {
            $coursecolumns[$c->name] = $c->name;
        }

        if (!isset($coursecolumns[$data->field])) {
            print_error('nosuchcolumn');
        }

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        $components = (new block_learnerscript\local\ls)->cr_unserialize($this->report->components);
        $conditions = $components['conditions'];
        $courselist = $reportclass->elements_by_conditions($conditions);

        if (!empty($courselist)) {
           $sql = 'SELECT DISTINCT(' . $data->field . ') as ufield FROM {course} WHERE ' . $data->field . ' <> :datafield ORDER BY ufield ASC';
            if ($rs = $DB->get_recordset_sql($sql, ['datafield' => ""])) {
                foreach ($rs as $u) {
                    $filteroptions[base64_encode($u->ufield)] = $u->ufield;
                }
                $rs->close();
            }
        }

        $select = $mform->addElement('select', 'filter_fcoursefield_' . $data->field, get_string($data->field), $filteroptions,array('data-select2'=>1));
        $select->setHiddenLabel(true);
        $mform->setType('filter_courses', PARAM_BASE64);
    }

}
