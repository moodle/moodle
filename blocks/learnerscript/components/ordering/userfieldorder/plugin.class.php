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

class plugin_userfieldorder extends pluginbase {

    var $sql = true;

    function init() {
        $this->fullname = get_string('userfield', 'block_learnerscript');
        $this->form = true;
        $this->unique = true;
        $this->reporttypes = array('users', 'userassignments', 'usercourses', 'userquizzes');
        $this->sql = true;
    }

    function summary($data) {
        return get_string($data->column) . ' ' . (strtoupper($data->direction));
    }

    // data -> Plugin configuration data
    function execute($data) {
        global $DB, $CFG;
        if(isset($data->direction)){
            if ($data->direction == 'asc' || $data->direction == 'desc') {
                $direction = strtoupper($data->direction);
                $columns = $DB->get_columns('user');

                $coursecolumns = array();
                foreach ($columns as $c)
                    $coursecolumns[$c->name] = $c->name;

                if (isset($coursecolumns[$data->column])) {
                    return 'u.' . $data->column . ' ' . $direction;
                }
            }
        }

        return '';
    }
    function columns() {
        global $DB;
        return $DB->get_columns('user');
    }
}
