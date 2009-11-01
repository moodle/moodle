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

    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);

    $row[] = new tabobject('courseoutcomes',
                           $CFG->wwwroot.'/grade/edit/outcome/course.php?id='.$courseid,
                           get_string('outcomescourse', 'grades'));

    if (has_capability('moodle/grade:manage', $context)) {
        $row[] = new tabobject('outcomes',
                               $CFG->wwwroot.'/grade/edit/outcome/index.php?id='.$courseid,
                               get_string('editoutcomes', 'grades'));
    }

    $tabs[] = $row;

    echo '<div class="outcomedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';


