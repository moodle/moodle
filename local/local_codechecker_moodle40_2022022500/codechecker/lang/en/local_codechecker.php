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
 * Language strings
 *
 * @package    local_codechecker
 * @category   string
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['check'] = 'Check code';
$string['clihelp'] = 'Check some PHP code against the Moodle coding guidelines.
Options:
  --interactive, -i Stops after every file allowing to continue with the next
                    file or to repeat the check against the same one.
  --exclude, -e     Exclude files with a comma separated list of substr matching paths,
                    Asterisks are allowed as wildchars at any place.
  --help, -h        Shows this information
Example:
  php run.php local/codechecker';
$string['error_find'] = 'Folder search failed';
$string['exclude'] = 'Exclude';
$string['other_eol'] = 'Whitespace at end of line';
$string['other_tab'] = 'Tab character not permitted';
$string['other_toolong'] = 'Line longer than maximum 180 characters';
$string['other_ratherlong'] = 'Line longer than recommended 132 characters';
$string['other_crlf'] = 'Windows (CRLF) line ending instead of just LF (reporting only first occurrence)';
$string['other_missinglf'] = 'Missing LF at end of file (use exactly one)';
$string['other_extralfs'] = 'Extra blank line(s) at end of file (use exactly one)';
$string['filesfound'] = 'Files found: {$a}';
$string['filesummary'] = '{$a->path} - {$a->count}';
$string['info'] = '<p>Checks code against some aspects of the {$a->link}.</p>
<p>Enter a path relative to the Moodle code root, for example: {$a->path}.</p>
<p>You can enter either a specific PHP file, or to a folder to check all the files it contains.
Multiple entries are supported (files or folders), one per line.</p>
<p>To exclude files, a comma separated list of substr matching paths can be used, for example: {$a->excludeexample}. Asterisks are allowed as wildchars at any place.</p>';
$string['includewarnings'] = 'Include warnings';
$string['invalidpath'] = 'Invalid path {$a}';
$string['moodlecodingguidelines'] = 'Moodle coding guidelines';
$string['numerrorswarnings'] = '{$a->errors} error(s) and {$a->warnings} warning(s)';
$string['path'] = 'Path(s) to check';
$string['privacy:metadata'] = 'The Code checker plugin does not store any personal data.';
$string['pluginname'] = 'Code checker';
$string['recheckfile'] = 'Re-check just this file';
$string['showstandard'] = 'Display phpcs standard associated with a problem';
$string['success'] = 'Well done!';
$string['summary'] = 'Total: {$a}';
$string['wholefile'] = 'File';
