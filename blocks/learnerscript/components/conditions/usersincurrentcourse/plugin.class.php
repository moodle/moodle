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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;

class plugin_usersincurrentcourse extends pluginbase {

    function init() {
        $this->fullname = get_string('usersincurrentcourse', 'block_learnerscript');
        $this->reporttypes = array();
        $this->form = true;
        $this->allowedops = false;
    }

    function summary($data) {
        return get_string('usersincurrentcourse_summary', 'block_learnerscript');
    }

    // data -> Plugin configuration data
    function execute($data, $user, $courseid) {
        global $DB;
        $context = (new ls)->cr_get_context(CONTEXT_COURSE, $courseid);
        if ($users = get_role_users($data->field, $context, false, 'u.id', 'u.id')) {
            return array_keys($users);
        }

        return array();
    }

    function columns(){
        global $DB;

        $roles = $DB->get_records('role');
        $userroles = array();
        foreach ($roles as $r)
            $userroles[$r->id] = $r->shortname;

        return $userroles;
    }

}
