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
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;

class plugin_ccoursefield extends pluginbase {

    function init() {
        $this->fullname = get_string('ccoursefield', 'block_learnerscript');
        $this->reporttypes = array('courses');
        $this->form = true;
        $this->allowedops = true;
    }

    function summary($data) {
        return get_string($data->field, 'block_learnerscript') . ' ' . $data->operator . ' ' . $data->value;
    }

    // data -> Plugin configuration data
    function execute($data, $user, $courseid) {
        global $DB;

        $data->value = $data->value;
        $ilike = " LIKE "; // TODO - Use $DB->sql_like()

        switch ($data->operator) {
            case 'LIKE % %': $sql = "$data->field $ilike ?";
                $params = array("%$data->value%");
                break;
            default: $sql = "$data->field $data->operator ?";
                $params = array($data->value);
        }

        $courses = $DB->get_records_select('course', $sql, $params);

        if ($courses)
            return array_keys($courses);

        return array();
    }
    function columns(){
        global $DB;

        $columns = $DB->get_columns('course');

        $coursecolumns = array();
        foreach ($columns as $c)
            $coursecolumns[$c->name] = $c->name;

        return $coursecolumns;
    }

}
