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
 * Defines and prints the workshop navigation tabs
 *
 * Can be included from within a workshop script only
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (empty($workshop) or !is_a($workshop, 'workshop')) {
    print_error('cannotcallscript');
}
if (!isset($currenttab)) {
    $currenttab = 'info';
}

$tabs       = array();
$row        = array();
$inactive   = array();
$activated  = array();

// top level tabs
if (has_capability('mod/workshop:view', $PAGE->context)) {
    $row[] = new tabobject('info', $workshop->view_url()->out(), get_string('info', 'workshop'));
}
if (has_capability('mod/workshop:editdimensions', $PAGE->context)) {
    $row[] = new tabobject('editform', $workshop->editform_url()->out(), get_string('editassessmentform', 'workshop'));
}
if ($currenttab == 'example' and has_any_capability(array('mod/workshop:submit', 'mod/workshop:manageexamples'), $PAGE->context)) {
    $row[] = new tabobject('example', '', get_string('example', 'workshop'));
}
if (has_capability('mod/workshop:submit', $PAGE->context)) {
    $row[] = new tabobject('submission', $workshop->submission_url()->out(), get_string('submission', 'workshop'));
}
if (has_capability('mod/workshop:allocate', $PAGE->context)) {
    $row[] = new tabobject('allocation', $workshop->allocation_url()->out(), get_string('allocate', 'workshop'));
}
$tabs[] = $row;

if ($currenttab == 'allocation' and !empty($allocators)) {
    // this is included from allocation.php so we rely of some variables defined there
    $activated[] = 'allocation';
    $row = array();
    foreach ($allocators as $methodid => $methodname) {
        $row[] = new tabobject($methodid, "allocation.php?cmid={$cmid}&amp;method={$methodid}", $methodname);
        if ($methodid == $method) {
            $currenttab = $methodid;
        }
    }
    $tabs[] = $row;
}


print_tabs($tabs, $currenttab, $inactive, $activated);
