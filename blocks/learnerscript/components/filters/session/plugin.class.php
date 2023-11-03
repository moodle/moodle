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
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use stdClass;

class plugin_session extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->fullname = get_string('filtersession', 'block_learnerscript');
        $this->reporttypes = array('');
    }

    public function summary($data) {
        return get_string('filtersession_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data, $filters) {
        $filtersession = isset($filters['filter_session']) ? $filters['filter_session'] : 0;
        if (!$filtersession) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filtersession);
        } else {
            if (preg_match("/%%FILTER_SESSION:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filtersession;
                return str_replace('%%FILTER_SESSION:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }
    public function filter_data($selectoption = true){
        global $DB, $CFG, $USER;
        $properties = new stdClass();
        $properties->courseid = SITEID;

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report, $properties);
        if (!is_siteadmin()) {
            $courseoptions = (new \block_learnerscript\local\querylib)->get_rolecourses($USER->id, $_SESSION['role'], $_SESSION['ls_contextlevel'] );
            foreach($courseoptions as $courseoption) {
                $courses[] = $courseoption->id;
            }
            $courselist = implode(',', $courses);
            $sql = "SELECT * 
                    FROM {bigbluebuttonbn}
                    WHERE course IN ($courselist) ";
            $sessionslist = array_keys($DB->get_records_sql($sql));
        } else {
            $sessionslist = array_keys($DB->get_records('bigbluebuttonbn'));            
        }
        $sessionoptions = array();
        if($selectoption && !in_array('session', $this->reportclass->basicparams[0])){
            $sessionoptions[0] = $this->singleselection ?
                get_string('filter_session', 'block_learnerscript') : get_string('select') .' '. get_string('session', 'block_learnerscript');
        }

        if (!empty($sessionslist)) {
            list($usql, $params) = $DB->get_in_or_equal($sessionslist);
            $sessions = $DB->get_records_select('bigbluebuttonbn', "id $usql", $params);

            foreach ($sessions as $s) {
                $sessionoptions[$s->id] = format_string($s->name);
            }
        } else {
            $sessionoptions[0] = $this->singleselection ?
                get_string('filter_session', 'block_learnerscript') : get_string('select') .' '. get_string('session', 'block_learnerscript');
        }
        return $sessionoptions;
    }
    public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }
    public function print_filter(&$mform) {
        $sessionoptions = $this->filter_data();
        $select = $mform->addElement('select', 'filter_session', get_string('session', 'block_learnerscript'), $sessionoptions, array('data-select2'=>1));
        $select->setHiddenLabel(true);
        $mform->setType('filter_session', PARAM_INT);
    }

}
