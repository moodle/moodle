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

class plugin_categoryfieldorder extends pluginbase {

    var $sql = true;

    function init() {
        $this->fullname = get_string('categoryfield', 'block_learnerscript');
        $this->form = true;
        $this->unique = true;
        $this->reporttypes = array('categories');
        $this->sql = true;
    }

    function summary($data) {
        return $data->column . ' ' . (strtoupper($data->direction));
    }

    // data -> Plugin configuration data
    function execute($data) {
        global $DB, $CFG;

        if ($data->direction == 'asc' || $data->direction == 'desc') {
            $direction = strtoupper($data->direction);
            $columns = $DB->get_columns('course_categories');

            $categorycolumns = array();
            foreach ($columns as $c)
                $categorycolumns[$c->name] = $c->name;

            if (isset($categorycolumns[$data->column])) {
                return 'ca.' . $data->column . ' ' . $direction;
            }
        }

        return '';
    }

    function columns() {
        global $DB;
        return $DB->get_columns('course_categories');
    }

}
