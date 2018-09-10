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

/**
 * Sets up the tabs used by the game pages based on the users capabilites.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if (empty($game)) {
    print_error('You cannot call this script in that way');
}
if (!isset($currenttab)) {
    $currenttab = '';
}
if (!isset($cm)) {
    $cm = get_coursemodule_from_instance('game', $game->id);
}
if (!isset($course)) {
    $course = $DB->get_record('course', array( 'id' => $game->course));
}

$context = game_get_context_module_instance( $cm->id);

$tabs = array();
$row  = array();
$inactive = array();
$activated = array();

global $USER;

if (has_capability('mod/game:view', $context)) {
    $row[] = new tabobject('info', "{$CFG->wwwroot}/mod/game/view.php?q=$game->id", get_string('info', 'game'));
}
if (has_capability('mod/game:viewreports', $context)) {
    $row[] = new tabobject('reports', "{$CFG->wwwroot}/mod/game/report.php?q=$game->id", get_string('results', 'game'));
}
if (has_capability('mod/game:manage', $context)) {
    $row[] = new tabobject('preview', "{$CFG->wwwroot}/mod/game/attempt.php?a=$game->id", get_string('preview', 'game'));
}
if (has_capability('mod/game:manage', $context)) {
    global $USER;
    $sesskey = $USER->sesskey;
    $url = "{$CFG->wwwroot}/course/mod.php?update=$cm->id&return=true&sesskey=$sesskey";
    $row[] = new tabobject('edit', $url, get_string('edit'));
}

if ( !($currenttab == 'info' && count($row) == 1)) {
    // Don't show only an info tab (e.g. to students).
    $tabs[] = $row;
}

if ($currenttab == 'reports' and isset($mode)) {
    $inactive[] = 'reports';
    $activated[] = 'reports';

    $allreports = get_list_of_plugins("mod/game/report");
    // Standard reports we want to show first.
    $reportlist = array ('overview');

    foreach ($allreports as $report) {
        if (!in_array($report, $reportlist)) {
            $reportlist[] = $report;
        }
    }

    $row  = array();
    $currenttab = '';
    foreach ($reportlist as $report) {
        $row[] = new tabobject($report, "{$CFG->wwwroot}/mod/game/report.php?q=$game->id&amp;mode=$report",
                                get_string($report, 'game'));
        if ($report == $mode) {
            $currenttab = $report;
        }
    }
    $tabs[] = $row;
}

if ($currenttab == 'edit' and isset($mode)) {
    $inactive[] = 'edit';
    $activated[] = 'edit';

    $row  = array();
    $currenttab = $mode;

    $strgames = get_string('modulenameplural', 'game');
    $strgame = get_string('modulename', 'game');
    $streditinggame = get_string("editinga", "moodle", $strgame);
    $strupdate = get_string('updatethis', 'moodle', $strgame);
    $row[] = new tabobject('editq', "{$CFG->wwwroot}/mod/game/edit.php?gameid=$game->id", $strgame, $streditinggame);
    questionbank_navigation_tabs($row, $context, $course->id);
    $tabs[] = $row;
}

print_tabs($tabs, $currenttab, $inactive, $activated);
