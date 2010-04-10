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
 * Strings for component 'portfolio_format_leap2a', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   portfolio_format_leap2a
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['entryalreadyexists'] = 'You tried to add a Leap2A entry with an id ({$a}) that already exists in this feed';
$string['feedtitle'] = 'Leap2A export from Moodle for {$a}';
$string['invalidentryfield'] = 'You tried to set an entry field that didn\'t exist ({$a}) or you can\'t set directly';
$string['invalidentryid'] = 'You tried to access an entry by an id that didn\'t exist ({$a})';
$string['missingfield'] = 'Required Leap2A entry field {$a} missing';
$string['nonexistantlink'] = 'A Leap2A entry ({$a->from}) tried to link to a non existing entry ({$a->to}) with rel {$a->rel}';
$string['overwritingselection'] = 'Overwriting the original type of an entry ({$a}) to selection in make_selection';
$string['selflink'] = 'A Leap2A entry ({$a->id}) tried to link to itself with rel {$a->rel}';
