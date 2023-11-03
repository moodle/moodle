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

class plugin_cuserfield extends pluginbase {

    function init() {
        $this->fullname = get_string('cuserfield', 'block_learnerscript');
        $this->reporttypes = array('users', 'grades');
        $this->form = true;
        $this->allowedops = true;
    }

    function summary($data) {
        global $DB;

        if (strpos($data->field, 'profile_') === 0) {
            $name = $DB->get_field('user_info_field', 'name', array('shortname' => str_replace('profile_', '', $data->field)));
            return $name . ' ' . $data->operator . ' ' . $data->value;
        }
        return get_string($data->field) . ' ' . $data->operator . ' ' . $data->value;
    }

    // data -> Plugin configuration data
    function execute($data, $user, $courseid) {
        global $DB;

        $ilike = " LIKE "; // TODO - Use $DB->sql_like()

        if (strpos($data->field, 'profile_') === 0) {

            if ($data->value == "%%CURRENTUSER%%") {
                $pfname = str_replace('profile_', '', $data->field);
                $data->value = $user->profile[$pfname];
            }

            if ($fieldid = $DB->get_field('user_info_field', 'id', array('shortname' => str_replace('profile_', '', $data->field)))) {

                switch ($data->operator) {
                    case 'LIKE % %': $sql = "fieldid = $fieldid AND data $ilike ?";
                        $params = array("%$data->value%");
                        break;
                    default: $sql = "fieldid = $fieldid AND data $data->operator ?";
                        $params = array($data->value);
                }

                if ($infodata = $DB->get_records_select('user_info_data', $sql, $params)) {
                    $finalusersid = array();
                    foreach ($infodata as $d) {
                        $finalusersid[] = $d->userid;
                    }
                    return $finalusersid;
                }
            }
        } else {

            if ($data->value == "%%CURRENTUSER%%") {
                $data->value = $user->{$data->field};
            }

            switch ($data->operator) {
                case 'LIKE % %': $sql = "$data->field $ilike ?";
                    $params = array("%$data->value%");
                    break;
                default: $sql = "$data->field $data->operator ?";
                    $params = array($data->value);
            }

            $users = $DB->get_records_select('user', $sql, $params);
            if ($users)
                return array_keys($users);
        }

        return array();
    }

    function columns(){
        global $DB;

        $columns = $DB->get_columns('user');

        $usercolumns = array();
        foreach ($columns as $c)
            $usercolumns[$c->name] = $c->name;

        if ($profile = $DB->get_records('user_info_field'))
            foreach ($profile as $p)
                $usercolumns['profile_' . $p->shortname] = $p->name;

        unset($usercolumns['password']);
        unset($usercolumns['sesskey']);

        return $usercolumns;
    }

}
