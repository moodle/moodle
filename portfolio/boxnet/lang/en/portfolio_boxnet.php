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

$string['apiv1migration_message_content'] = 'As part of the recent Moodle upgrade to 2.5.3, the Box.net portfolio plugin has been disabled. To re-enable it, you need to re-configure it as described in the documentation {$a->docsurl}.';
$string['apiv1migration_message_small'] = 'This plugin has been disabled, as it requires configuration as described in the documentation Box.net APIv1 migration.';
$string['apiv1migration_message_subject'] = 'Important information regarding Box.net portfolio plugin';
$string['clientid'] = 'Client ID';
$string['clientsecret'] = 'Client secret';
$string['existingfolder'] = 'Existing folder to put file(s) into';
$string['folderclash'] = 'The folder you asked to create already exists!';
$string['foldercreatefailed'] = 'Failed to create your target folder on box.net';
$string['folderlistfailed'] = 'Failed to retrieve a folder listing from box.net';
$string['missinghttps'] = 'HTTPS required';
$string['missinghttps_help'] = 'Box.net will only work with an HTTPS enabled website.';
$string['missingoauthkeys'] = 'Missing client ID and secret';
$string['missingoauthkeys_help'] = 'There is no client ID or secret configured for this plugin. You can get one of these from Box.net development page.';
$string['newfolder'] = 'New folder to put file(s) into';
$string['noauthtoken'] = 'Could not retrieve an authentication token for use in this session';
$string['notarget'] = 'You must specify either an existing folder or a new folder to upload into';
$string['noticket'] = 'Could not retrieve a ticket from box.net to begin the authentication session';
$string['password'] = 'Your box.net password (will not be stored)';
$string['pluginname'] = 'Box.net';
$string['sendfailed'] = 'Failed to send content to box.net: {$a}';
$string['setupinfo'] = 'Setup instructions';
$string['setupinfodetails'] = 'To obtain a client ID and secret, log in to Box.net and visit their <a href="{$a->servicesurl}">developers page</a>. Follow \'Create new application\' and create new application for your Moodle site. The client ID ans secret are displayed in \'OAuth2 parameters\' section of the application edit form. Optionally, you can also provide other information about your Moodle site.';
$string['sharedfolder'] = 'Shared';
$string['sharefile'] = 'Share this file?';
$string['sharefolder'] = 'Share this new folder?';
$string['targetfolder'] = 'Target folder';
$string['tobecreated'] = 'To be created';
$string['username'] = 'Your box.net username (will not be stored)';
$string['warninghttps'] = 'Box.net requires your website to be using HTTPS in order for the portfolio to work.';
