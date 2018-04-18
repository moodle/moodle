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
 * Language file definitions for onedrive repository
 *
 * @package    repository_onedrive
 * @copyright  2012 Lancaster University Network Services Ltd
 * @author     Dan Poltawski <dan.poltawski@luns.net.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['access'] = 'Access';
$string['both'] = 'Internal and external';
$string['cachedef_folder'] = 'OneDrive file IDs for folders in the system account';
$string['configplugin'] = 'Configure OneDrive plugin';
$string['confirmimportskydrive'] = 'Are you sure you want to import all files from the Microsoft SkyDrive repository to the Microsoft OneDrive repository? The Microsoft OneDrive repository must be configured and working for imported files to continue working as before. Warning: This action cannot be undone!';
$string['defaultreturntype'] = 'Default return type';
$string['external'] = 'External (only links stored in Moodle)';
$string['fileoptions'] = 'The types and defaults for returned files is configurable here. Note that all files linked externally will be updated so that the owner is the Moodle system account.';
$string['importskydrivefiles'] = 'Import files from Microsoft SkyDrive repository';
$string['internal'] = 'Internal (files stored in Moodle)';
$string['issuer_help'] = 'Select the OAuth 2 service that is configured to talk to the OneDrive API. If the service does not exist yet, you will need to create it.';
$string['issuer'] = 'OAuth 2 service';
$string['mysitenotfound'] = 'You have never logged into OneDrive before. You must log in to OneDrive at least once before it can be used with Moodle.';
$string['oauth2serviceslink'] = '<a href="{$a}" title="Link to OAuth 2 services configuration">OAuth 2 services configuration</a>';
$string['owner'] = 'Owned by: {$a}';
$string['pluginname'] = 'Microsoft OneDrive';
$string['removetempaccesstask'] = 'Remove temporary write access from controlled links.';
$string['searchfor'] = 'Search for {$a}';
$string['servicenotenabled'] = 'Access not configured.';
$string['skydrivefilesexist'] = 'Files found in the Microsoft SkyDrive repository. This repository has been deprecated by Microsoft, however the files may be imported to the Microsoft OneDrive repository.';
$string['skydrivefilesimported'] = 'All files were imported from the Microsoft SkyDrive repository.';
$string['skydrivefilesnotimported'] = 'Some files could not be imported from the Microsoft SkyDrive repository.';
$string['onedrive:view'] = 'View OneDrive repository';
$string['supportedreturntypes'] = 'Supported files';
$string['privacy:metadata:repository_onedrive'] = 'The Microsoft OneDrive repository stores temporary access grants, and transmits user data from Moodle to the remote system.';
$string['privacy:metadata:repository_onedrive:searchtext'] = 'The Microsoft OneDrive repository user search text query.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:itemid'] = 'The Microsoft OneDrive with a temporary access grant item ID.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:permissionid'] = 'The Microsoft OneDrive temporary access grant permission ID.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:timecreated'] = 'The Microsoft OneDrive temporary access grant creation date/time.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:timemodified'] = 'The Microsoft OneDrive temporary access grant modification date/time.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:usermodified'] = 'The ID of the user modifying the Microsoft OneDrive temporary access grant.';
