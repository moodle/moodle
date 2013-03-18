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
 * Strings for the tool_installaddon component.
 *
 * @package     tool_installaddon
 * @category    string
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['acknowledgement'] = 'Acknowledgement';
$string['acknowledgementmust'] = 'You must acknowledge this';
$string['acknowledgementtext'] = 'I understand that it is my responsibility to have full backups of this site prior to installing add-ons. I accept and understand that add-ons (especially but not only those originating in unofficial sources) may contain security holes, can make the site unavailable, or cause private data leaks or loss.';
$string['featuredisabled'] = 'Add-on installer is disabled at this site.';
$string['installaddons'] = 'Install add-ons';
$string['installfromrepo'] = 'Install add-ons from Moodle plugins directory';
$string['installfromrepo_help'] = 'You will be redirected to the Moodle plugins directory to search for and install an add-on. Note that your site fullname, URL and major version will be sent as well, to make the installation process easier for you.';
$string['installfromzipfile'] = 'ZIP package';
$string['installfromzipfile_help'] = 'The plugin ZIP package must contain just one directory with the name of the plugin. The ZIP will be extracted into the appropriate location for the given plugin type. Packages downloaded from the Moodle plugins directory have this format.';
$string['installfromzipsubmit'] = 'Install add-on from the ZIP file';
$string['installfromziptype'] = 'Plugin type';
$string['installfromziptype_help'] = 'Choose the correct type of plugin you are about to install. The installation procedure may fail badly when incorrect plugin type is provided.';
$string['permcheck'] = 'Make sure the plugin type root location is writable by the web server process';
$string['permcheckerror'] = 'Error while checking for write permission';
$string['permcheckprogress'] = 'Checking for write permission ...';
$string['permcheckresultno'] = 'Plugin type location <em>{$a->path}</em> not writable';
$string['permcheckresultyes'] = 'Plugin type location <em>{$a->path}</em> is writable';
$string['pluginname'] = 'Add-on installer';
