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
$string['clamfailed'] = 'ClamAV has failed to run.  The return error message was "{$a}". Here is the output from ClamAV:';
$string['clamfailureonupload'] = 'On ClamAV failure';
$string['errorcantopensocket'] = 'Connecting to Unix domain socket resulted in error {$a}';
$string['errorclamavnoresponse'] = 'ClamAV does not respond; check daemon running state.';
$string['errornounixsocketssupported'] = 'Unix domain socket transport is not supported on this system. Please use the command line option instead.';
$string['invalidpathtoclam'] = 'Path to ClamAV, {$a}, is invalid.';
$string['pathtoclam'] = 'Command line';
$string['pathtoclamdesc'] = 'If the running method is set to "command line", enter the path to ClamAV here. On Linux this will be /usr/bin/clamscan or /usr/bin/clamdscan.';
$string['pathtounixsocket'] = 'Unix domain socket';
$string['pathtounixsocketdesc'] = 'If the running method is set to "Unix domain socket", enter the path to ClamAV Unix socket here. On Debian Linux this will be /var/run/clamav/clamd.ctl. Please make sure that clamav daemon has read access to uploaded files, the easiest way to ensure that is to add \'clamav\' user to your webserver group (\'www-data\' on Debian Linux).';
$string['pluginname'] = 'ClamAV antivirus';
$string['quarantinedir'] = 'Quarantine directory';
$string['runningmethod'] = 'Running method';
$string['runningmethoddesc'] = 'Method of running ClamAV. Command line is used by default, however on Unix systems better performance can be obtained by using system sockets.';
$string['runningmethodcommandline'] = 'Command line';
$string['runningmethodunixsocket'] = 'Unix domain socket';
$string['unknownerror'] = 'There was an unknown error with ClamAV.';
