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

    $row[] = new tabobject('lettersview',
                           $CFG->wwwroot.'/grade/edit/letter/index.php?id='.$COURSE->id,
                           get_string('letters', 'grades'));

    if (has_capability('moodle/grade:manageletters', $context)) {
        $row[] = new tabobject('lettersedit',
                               $CFG->wwwroot.'/grade/edit/letter/edit.php?id='.$context->id,
                               get_string('edit'));
    }

    $tabs[] = $row;

    echo '<div class="letterdisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';


