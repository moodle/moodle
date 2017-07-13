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
 * Strings for component 'search_solr'.
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['connectionerror'] = 'The specified Solr server is not available or the specified index does not exist';
$string['connectionsettings'] = 'Connection settings';
$string['errorcreatingschema'] = 'Error creating the Solr schema: {$a}';
$string['errorvalidatingschema'] = 'Error validating Solr schema: field {$a->fieldname} does not exist. Please <a href="{$a->setupurl}">follow this link</a> to set up the required fields.';
$string['extensionerror'] = 'The Apache Solr PHP extension is not installed. Please check the documentation.';
$string['fileindexing'] = 'Enable file indexing';
$string['fileindexing_help'] = 'If your Solr install supports it, this feature allows Moodle to send files to be indexed.';
$string['fileindexsettings'] = 'File indexing settings';
$string['maxindexfilekb'] = 'Maximum file size to index (kB)';
$string['maxindexfilekb_help'] = 'Files larger than this number of kilobytes will not be included in search indexing. If set to zero, files of any size will be indexed.';
$string['minimumsolr4'] = 'Solr 4.0 is the minimum version required for Moodle';
$string['missingconfig'] = 'Your Apache Solr server is not yet configured in Moodle.';
$string['multivaluedfield'] = 'Field "{$a}" returned an array instead of a scalar. Please delete the current index, create a new one and run setup_schema.php before indexing data in Solr.';
$string['nodatafromserver'] = 'No data from server';
$string['pluginname'] = 'Solr';
$string['schemafieldautocreated'] = 'Field "{$a}" already exists in Solr schema. You probably forgot to run this script before indexing data and fields were autocreated by Solr. Please delete the current index, create a new one and run setup_schema.php again before indexing data in Solr.';
$string['schemasetupfromsolr5'] = 'Your Solr server version is lower than 5.0. This script can only set your schema if your Solr version is 5.0 or higher. You need to manually set the fields in your schema according to \\search_solr\\document::get_default_fields_definition().';
$string['searchinfo'] = 'Search queries';
$string['searchinfo_help'] = 'The field to be searched may be specified by prefixing the search query with \'title:\', \'content:\', \'name:\', or \'intro:\'. For example, searching for \'title:news\' would return results with the word \'news\' in the title.

Boolean operators (\'AND\', \'OR\', \'NOT\') may be used to combine or exclude keywords.

Wildcard characters (\'*\' or \'?\' ) may be used to represent characters in the search query.';
$string['setupok'] = 'The schema is ready to be used.';
$string['solrauthpassword'] = 'HTTP authentication password';
$string['solrauthuser'] = 'HTTP authentication username';
$string['solrindexname'] = 'Index name';
$string['solrhttpconnectionport'] = 'Port';
$string['solrhttpconnectiontimeout'] = 'Timeout';
$string['solrhttpconnectiontimeout_desc'] = 'The HTTP connection timeout is the maximum time in seconds allowed for the HTTP data transfer operation.';
$string['solrinfo'] = 'Solr';
$string['solrnotselected'] = 'Solr engine is not the configured search engine';
$string['solrserverhostname'] = 'Host name';
$string['solrserverhostname_desc'] = 'Domain name of the Solr server.';
$string['solrsecuremode'] = 'Secure mode';
$string['solrsetting'] = 'Solr settings';
$string['solrsslcainfo'] = 'SSL CA certificates name';
$string['solrsslcainfo_desc'] = 'File name holding one or more CA certificates to verify peer with';
$string['solrsslcapath'] = 'SSL CA certificates path';
$string['solrsslcapath_desc'] = 'Directory path holding multiple CA certificates to verify peer with';
$string['solrsslcert'] = 'SSL certificate';
$string['solrsslcert_desc'] = 'File name to a PEM-formatted private certificate';
$string['solrsslkey'] = 'SSL key';
$string['solrsslkey_desc'] = 'File name to a PEM-formatted private key';
$string['solrsslkeypassword'] = 'SSL key password';
$string['solrsslkeypassword_desc'] = 'Password for PEM-formatted private key file';
