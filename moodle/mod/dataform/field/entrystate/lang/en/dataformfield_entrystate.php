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
 * @package dataformfield_entrystate
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Entry state';
$string['entrystate:addinstance'] = 'Add a new Entry state dataformfield';
$string['state'] = 'State';
$string['states'] = 'States';
$string['states_help'] = 'State names, one per line. Example:<p>Draft<br />Submitted<br />Approved</p>The list of states should be saved before transitions can added.';
$string['transition'] = 'Transition';
$string['transitions'] = 'Transitions';
$string['allowedto'] = 'Allowed to';
$string['allowedto_help'] = 'Allowed to';
$string['notify'] = 'Notify';
$string['notify_help'] = 'Notify';
$string['stateicon'] = 'State Icon';
$string['stateicon_help'] = 'State Icon';
$string['transition'] = 'Transition';
$string['transition_help'] = 'A list of states that can be advanced to from this state. Each state in a new line.';
$string['incorrectstate'] = 'The requested state {$a} could not be found.';
$string['alreadyinstate'] = 'The entry ({$a->entryid}) is already in the requested state {$a->newstate}.';
$string['instatingdenied'] = 'You are not permitted to change the state of this entry.';
$string['statechanged'] = 'The state of entry id {$a->id} has changed from {$a->old} to {$a->new}.';
