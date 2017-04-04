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

$string['both'] = 'Internal and External';
$string['cachedef_folder'] = 'OneDrive File IDs for folders in the system account';
$string['configplugin'] = 'Configure OneDrive plugin';
$string['confirmimportskydrive'] = 'Are you sure you want to import all files from the "Microsoft SkyDrive" repository to the "Microsoft OneDrive" repository? As long as the Microsoft OneDrive repository is already configured and working - all imported files will continue working as before. There is no way to undo these changes.';
$string['defaultreturntype'] = 'Default return type';
$string['external'] = 'External (only links stored in Moodle)';
$string['fileoptions'] = 'The types and defaults for returned files is configurable here. Note that all files linked externally will be updated so that the owner is the Moodle system account.';
$string['importskydrivefiles'] = 'Import files from Microsoft SkyDrive repository';
$string['internal'] = 'Internal (files stored in Moodle)';
$string['issuer_help'] = 'Select the OAuth 2 service that is configured to talk to the OneDrive API. If the services does not exist yet, you might need to create it.';
$string['issuer'] = 'OAuth 2 service';
$string['oauth2serviceslink'] = '<a href="{$a}" title="Link to OAuth Services configuration">OAuth 2 Services Configuration</a>';
$string['owner'] = 'Owned by: {$a}';
$string['pluginname'] = 'Microsoft OneDrive';
$string['removetempaccesstask'] = 'Remove temporary write access from controlled links.';
$string['searchfor'] = 'Search for {$a}';
$string['servicenotenabled'] = 'Access not configured.';
$string['skydrivefilesexist'] = 'Files found in the Microsoft SkyDrive repository. This repository is deprecated by Microsoft - the files can be automatically imported to this Microsoft OneDrive repository.';
$string['skydrivefilesimported'] = 'All files were imported from the Microsoft SkyDrive repository.';
$string['skydrivefilesnotimported'] = 'Some files could not be imported from the Microsoft SkyDrive repository.';
$string['onedrive:view'] = 'View OneDrive repository';
$string['supportedreturntypes'] = 'Supported files';
