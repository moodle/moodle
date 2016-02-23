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
$string['errorcreatingschema'] = 'Error creating the Solr schema: {$a}';
$string['errorvalidatingschema'] = 'Error validating Solr schema, field {$a->fieldname} does not exist. Please <a href="{$a->setupurl}">follow this link</a> to setup the fields required by Moodle.';
$string['extensionerror'] = 'The Apache Solr PHP extension is not installed. Please check the documentation.';
$string['missingconfig'] = 'Your Apache Solr server is not yet configured in Moodle.';
$string['multivaluedfield'] = 'Field "{$a}" returned an array instead of a scalar, the field is probably defined in Solr with "Multivalued" to true, this means that Solr autocreated the field for you when you indexed data because you forgot to run search/engine/solr/cli/setup_schema.php. Please delete the current index, create a new one and run setup_schema.php before indexing data in Solr.';
$string['nodatafromserver'] = 'No data from server';
$string['pluginname'] = 'Solr';
$string['schemafieldautocreated'] = 'Field "{$a}" already exists in Solr schema. You probably forgot to run this script before indexing data and fields were autocreated by Solr. Please delete the current index, create a new one and run setup_schema.php again before indexing data in Solr.';
$string['searchinfo'] = 'Search queries';
$string['searchinfo_help'] = 'Features you can use while performing search queries:

* Fields: You can specify which fields you want results from.<br/>
  * title:(moodle + perth): returns all records that contains both "moodle" and "perth" in the title. Available fields: title, content, name, intro
* Boolean Operators ("AND", "OR", "NOT"):<br/>
  * (moodle AND perth) OR (moodle AND australia)
* Wildcards ("&#42;", "?"):<br/>
  * "mo??l&#42;" returns both "moodle" and "moodledata".
* Proximity Searches ("~"):<br/>
  * mood~2 returns "moodle". (2 alphabets away from "mood").
  * moodle australia~3 returns results containing "moodle hq at perth australia" (the queried terms were within 3 words of each other)
* Boosting Terms ("^"): To boost certain words/phrases:<br/>
  * "perth australia"^5 "australia" will make results with the phrase "perth australia" more relevant.
';
$string['setupok'] = 'The schema is ready to be used.';
$string['solrauthpassword'] = 'HTTP Authentication Password';
$string['solrauthuser'] = 'HTTP Authentication username';
$string['solrindexname'] = 'Index name';
$string['solrhttpconnectionport'] = 'Port';
$string['solrhttpconnectiontimeout'] = 'Timeout';
$string['solrhttpconnectiontimeout_desc'] = 'The HTTP connection timeout is the maximum time in seconds allowed for the HTTP data transfer operation.';
$string['solrinfo'] = 'Solr';
$string['solrnotselected'] = 'Solr engine is not the configured search engine';
$string['solrserverhostname'] = 'Host Name';
$string['solrserverhostname_desc'] = 'Domain name of the Solr server.';
$string['solrsecuremode'] = 'Secure mode';
$string['solrsetting'] = 'Solr settings';
$string['solrsslcainfo'] = 'SSL CA certificates name';
$string['solrsslcainfo_desc'] = 'File name holding one or more CA certificates to verify peer with';
$string['solrsslcapath'] = 'SSL CA certificates path';
$string['solrsslcapath_desc'] = 'Directory path holding multiple CA certificates to verify peer with';
$string['solrsslcert'] = 'SSL key & certificate';
$string['solrsslcert_desc'] = 'File name to a PEM-formatted private key + private certificate (concatenated in that order)';
$string['solrsslcertonly'] = 'SSL certificate';
$string['solrsslcertonly_desc'] = 'File name to a PEM-formatted private certificate only';
$string['solrsslkey'] = 'SSL key';
$string['solrsslkey_desc'] = 'File name to a PEM-formatted private key';
$string['solrsslkeypassword'] = 'SSL Key password';
$string['solrsslkeypassword_desc'] = 'Password for PEM-formatted private key file';
