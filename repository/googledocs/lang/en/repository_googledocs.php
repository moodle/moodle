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
 * Strings for component 'repository_googledocs', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   repository_googledocs
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configplugin'] = 'Configure Google Drive plugin';
$string['docsformat'] = 'Default document import format';
$string['drawingformat'] = 'Default drawing import format';
$string['googledocs:view'] = 'View Google Drive repository';
$string['importformat'] = 'Configure the default import formats from Google';
$string['pluginname'] = 'Google Drive';
$string['presentationformat'] = 'Default presentation import format';
$string['spreadsheetformat'] = 'Default spreadsheet import format';
$string['issuer'] = 'OAuth 2 service';
$string['issuer_help'] = 'Select the OAuth 2 service that is configured to talk to the Google Drive API. If the service does not exist yet, you will need to create it.';
$string['servicenotenabled'] = 'Access not configured. Make sure the service \'Drive API\' is enabled.';
$string['oauth2serviceslink'] = '<a href="{$a}" title="Link to OAuth 2 services configuration">OAuth 2 services configuration</a>';
$string['searchfor'] = 'Search for {$a}';
$string['internal'] = 'Internal (files stored in Moodle)';
$string['external'] = 'External (only links stored in Moodle)';
$string['both'] = 'Internal and external';
$string['supportedreturntypes'] = 'Supported files';
$string['defaultreturntype'] = 'Default return type';
$string['fileoptions'] = 'The types and defaults for returned files is configurable here. Note that all files linked externally will be updated so that the owner is the Moodle system account.';
$string['owner'] = 'Owned by: {$a}';
$string['cachedef_folder'] = 'Google file IDs for folders in the system account';

// Deprecated since Moodle 3.3.
$string['oauthinfo'] = '<p>To use this plugin, you must register your site with Google, as described in the documentation <a href="{$a->docsurl}">Google OAuth 2.0 setup</a>.</p><p>As part of the registration process, you will need to enter the following URL as \'Authorized Redirect URIs\':</p><p>{$a->callbackurl}</p><p>Once registered, you will be provided with a client ID and secret which can be used to configure certain other Google Drive and Picasa plugins.</p><p>Please also note that you will have to enable the service \'Drive API\'.</p>';
$string['secret'] = 'Secret';
$string['clientid'] = 'Client ID';
