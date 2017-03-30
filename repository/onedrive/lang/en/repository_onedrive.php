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
 * Language file definitions for skydrive repository
 *
 * @package    repository_skydrive
 * @copyright  2012 Lancaster University Network Services Ltd
 * @author     Dan Poltawski <dan.poltawski@luns.net.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['configplugin'] = 'Configure OneDrive plugin';
$string['skydrive:view'] = 'View OneDrive repository';
$string['pluginname'] = 'Microsoft OneDrive';
$string['issuer'] = 'OAuth 2 service';
$string['issuer_help'] = 'Select the OAuth 2 service that is configured to talk to the OneDrive API. If the services does not exist yet, you might need to create it.';
$string['servicenotenabled'] = 'Access not configured.';
$string['oauth2serviceslink'] = '<a href="{$a}" title="Link to OAuth Services configuration">OAuth 2 Services Configuration</a>';
$string['searchfor'] = 'Search for {$a}';
$string['internal'] = 'Internal (files stored in Moodle)';
$string['external'] = 'External (only links stored in Moodle)';
$string['both'] = 'Internal and External';
$string['supportedreturntypes'] = 'Supported files';
$string['defaultreturntype'] = 'Default return type';
$string['fileoptions'] = 'The types and defaults for returned files is configurable here. Note that all files linked externally will be updated so that the owner is the Moodle system account.';
$string['owner'] = 'Owned by: {$a}';
$string['cachedef_folder'] = 'OneDrive File IDs for folders in the system account';

