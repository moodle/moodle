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

class plugin_currentreportcourse extends pluginbase {

    function init() {
        $this->fullname = get_string('currentreportcourse', 'block_learnerscript');
        $this->form = false;
        $this->allowedops = false;
        $this->reporttypes = array('courses');
    }

    function summary($data) {
        return get_string('currentreportcourse_summary', 'block_learnerscript');
    }

    // data -> Plugin configuration data
    function execute($data, $user, $courseid) {
        global $DB;

        $finalcourses = array();
        $finalcourses[] = $courseid;

        return $finalcourses;
    }

}
