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
 * Strings for component 'portfolio_boxnet', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   portfolio_boxnet
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['apikey'] = 'API key';
$string['apikeyhelp'] = 'You need to get this by signing up to enabled.box.net and adding an application.  The callback url must be yourwwwroot/portfolio/add.php?postcontrol=1&type=boxnet';
$string['apikeyinlinehelp'] = '<p>To configure Box.net, visit <a href="http://enabled.box.net/my-projects">enabled.box.net</a> and log in.</p><p>Under My Projects you will need to create one new project for each Moodle site.</p><p>The only setting that matters is the callback url, which should be {$a}. You can put anything you like for the other settings. Save it and you\'re done!';
$string['err_noapikey'] = 'There is no API Key configured for this plugin.  You can get one of these from http://enabled.box.net';
$string['existingfolder'] = 'Exiting folder to put file(s) into';
$string['folderclash'] = 'The folder you asked to create already exists!';
$string['foldercreatefailed'] = 'Failed to create your target folder on box.net';
$string['folderlistfailed'] = 'Failed to retrieve a folder listing from box.net';
$string['newfolder'] = 'New folder to put file(s) into';
$string['noauthtoken'] = 'Could not retrieve an authentication token for use in this session';
$string['notarget'] = 'You must specify either an existing folder or a new folder to upload into';
$string['noticket'] = 'Could not retrieve a ticket from box.net to begin the authentication session';
$string['password'] = 'Your box.net password (will not be stored)';
$string['pluginname'] = 'Box.net internet storage';
$string['sendfailed'] = 'Failed to send content to box.net: {$a}';
$string['sharedfolder'] = 'Shared';
$string['sharefile'] = 'Share this file?';
$string['sharefolder'] = 'Share this new folder?';
$string['targetfolder'] = 'Target folder';
$string['tobecreated'] = 'To be created';
$string['username'] = 'Your box.net username (will not be stored)';
