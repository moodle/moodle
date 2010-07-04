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

    $row = $tabs = array();
    $tabcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    $row[] = new tabobject('graderreport',
                           $CFG->wwwroot.'/grade/report/grader/index.php?id='.$courseid,
                           get_string('pluginname', 'gradereport_grader'));
    if (has_capability('moodle/grade:manage',$tabcontext ) ||
        has_capability('moodle/grade:edit', $tabcontext) ||
        has_capability('gradereport/grader:view', $tabcontext)) {
        $row[] = new tabobject('preferences',
                               $CFG->wwwroot.'/grade/report/grader/preferences.php?id='.$courseid,
                               get_string('myreportpreferences', 'grades'));
    }

    $tabs[] = $row;
    echo '<div class="gradedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';

