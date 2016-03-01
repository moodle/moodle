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
 * Strings for component 'antivirus_clamav', language 'en'.
 *
 * @package    antivirus_clamav
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configclamactlikevirus'] = 'Treat files like viruses';
$string['configclamdonothing'] = 'Treat files as OK';
$string['configclamfailureonupload'] = 'If you have configured clam to scan uploaded files, but it is configured incorrectly or fails to run for some unknown reason, how should it behave?  If you choose \'Treat files like viruses\', they\'ll be moved into the quarantine area, or deleted. If you choose \'Treat files as OK\', the files will be moved to the destination directory like normal. Either way, admins will be alerted that clam has failed.  If you choose \'Treat files like viruses\' and for some reason clam fails to run (usually because you have entered an invalid pathtoclam), ALL files that are uploaded will be moved to the given quarantine area, or deleted. Be careful with this setting.';
$string['configpathtoclam'] = 'Path to ClamAV.  Probably something like /usr/bin/clamscan or /usr/bin/clamdscan. You need this in order for ClamAV to run.';
$string['configquarantinedir'] = 'If you want ClamAV to move infected files to a quarantine directory, enter it here. It must be writable by the webserver.  If you leave this blank, or if you enter a directory that doesn\'t exist or isn\'t writable, infected files will be deleted.  Do not include a trailing slash.';
$string['clamfailed'] = 'ClamAV has failed to run.  The return error message was "{$a}". Here is the output from ClamAV:';
$string['clamfailureonupload'] = 'On ClamAV failure';
$string['invalidpathtoclam'] = 'Path to ClamAV, {$a}, is invalid.';
$string['pathtoclam'] = 'ClamAV path';
$string['pluginname'] = 'ClamAV antivirus';
$string['quarantinedir'] = 'Quarantine directory';
$string['unknownerror'] = 'There was an unknown error with ClamAV.';
