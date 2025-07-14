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
 * Strings for component 'mlbackend_python'
 *
 * @package   mlbackend_python
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['errornoconfigdata'] = 'The server configuration is not complete.';
$string['errorserver'] = 'Server error {$a}';
$string['host'] = 'Host';
$string['hostdesc'] = 'Host';
$string['packageinstalledshouldbe'] = 'The moodlemlbackend Python package should be updated. The required version is "{$a->required}" and the installed version is "{$a->installed}".';
$string['packageinstalledtoohigh'] = 'The moodlemlbackend Python package is not compatible with this version of Moodle. The required version is "{$a->required}" or higher as long as it is API-compatible. The installed version "{$a->installed}" is too high.';
$string['pluginname'] = 'Python machine learning backend';
$string['port'] = 'Port';
$string['portdesc'] = 'Port';
$string['privacy:metadata'] = 'The Python machine learning backend plugin does not store any personal data.';
$string['pythonpackagenotinstalled'] = 'The moodlemlbackend Python package is not installed or there is a problem with it. Please execute "{$a}" from command line interface for more info.';
$string['pythonpathnotdefined'] = 'The path to your executable Python binary has not been defined. Please visit "{$a}" to set it.';
$string['serversettingsinfo'] = 'If \'Use a server\' is enabled, the server settings will be displayed.';
$string['username'] = 'Username';
$string['usernamedesc'] = 'String of characters used as a username to communicate between the Moodle server and the Python server.';
$string['password'] = 'Password';
$string['passworddesc'] = 'String of characters used as a password to communicate between the Moodle server and the Python server.';
$string['secure'] = 'Use HTTPS';
$string['securedesc'] = 'Whether to use HTTP or HTTPS.';
$string['useserver'] = 'Use a server';
$string['useserverdesc'] = 'The machine learning backend Python package is not installed on the web server but on a different server.';
$string['tensorboardinfo'] = 'Launch TensorBoard from command line by typing tensorboard --logdir=\'{$a}\' in your web server.';