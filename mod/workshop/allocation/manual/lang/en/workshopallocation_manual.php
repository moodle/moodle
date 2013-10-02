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
 * Strings for component 'workshopallocation_manual', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    workshopallocation
 * @subpackage manual
 * @copyright  2009 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addreviewee'] = 'Add reviewee';
$string['addreviewer'] = 'Add reviewer';
$string['allocationadded'] = 'The submission has been successfully allocated';
$string['allocationexists'] = 'The allocation already exists';
$string['areyousuretodeallocate'] = 'Are you sure you want deallocate the selected assessment?';
$string['areyousuretodeallocategraded'] = 'You are going to remove the assessment that has already been graded. Are you really sure you want to do it?';
$string['pluginname'] = 'Manual allocation';
$string['showallparticipants'] = 'Show all participants';

$string['uploadform_helptext'] = <<<HTML
Use this form to <strong>upload</strong> allocations. The file format is CSV. The first field should be the <strong>participants's username</strong> and all other fields on the same row are the <strong>usernames of the reviewers</strong> of that participant. For example:

<table class="upload-form-example">
	<tr><td>aaron</td><td>beryl</td><td>carlos</td><td>dorothy</td></tr>
	<tr><td>beryl</td><td>aaron</td><td>dorothy</td></tr>
	<tr><td>dorothy</td><td>carlos</td><td>beryl</td><td>aaron</td></tr>
</table>

In this example, Aaron is reviewed by Beryl, Carlos and Dorothy; Beryl is reviewed by Aaron and Dorothy, and Dorothy is reviewed by Carlos, Beryl and Aaron.
<br/><br/>
<strong>CSV uploads do not clear your current allocations.</strong>
HTML;

$string['uploadform_teammode_helptext'] = <<<HTML
Use this form to <strong>upload</strong> allocations. The file format is CSV. The first field should be the group name (case-sensitive) and all other fields on the same row are the usernames of the reviewers of that participant. For example:

<table class="upload-form-example">
	<tr><td>Team A</td><td>beryl</td><td>carlos</td><td>dorothy</td></tr>
	<tr><td>Team B</td><td>aaron</td><td>dorothy</td></tr>
	<tr><td>Team C</td><td>carlos</td><td>beryl</td><td>aaron</td></tr>
</table>


In this example, Team A is reviewed by Beryl, Carlos and Dorothy; Team B is reviewed by Aaron and Dorothy, and Team C is reviewed by Carlos, Beryl and Aaron.
<br/><br/>
<strong>CSV uploads do not clear your current allocations.</strong>
HTML;

