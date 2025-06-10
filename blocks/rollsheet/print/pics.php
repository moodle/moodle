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

require_once("../../../config.php");

global $CFG, $DB, $COURSE;
require_login();
$cid = required_param('cid', PARAM_INT);
$context = context_course::instance($cid);
$PAGE->set_context($context);
if (has_capability('block/rollsheet:viewblock', $context)) {
    require_once('../genpics/renderrollsheet.php');
    $PAGE->set_pagelayout('print');
    $PAGE->set_url('/blocks/rollsheet/print/pics.php');
    $logoenabled = get_config('block_rollsheet', 'customlogoenabled');
    echo $OUTPUT->header();
    $usersperpage = get_config('block_rollsheet', 'usersPerPage' );

    if ($logoenabled) {
        printHeaderLogo();
    }
    $rendertype = optional_param('rendertype', '', PARAM_TEXT);
    if (isset($rendertype)) {
        if ($rendertype == 'all' || $rendertype == '') {
                    echo renderPicSheet($usersperpage);
        } else if ($rendertype == 'group') {
            echo renderPicSheet($usersperpage);
        }
    } else {
        renderPicSheet($usersperpage);
    }

    echo $OUTPUT->footer();
    echo '<script>window.print();</script>';
} else {
    header("location: " . $CFG->wwwroot . "/course/view.php?id=" . $cid);
}