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
 * Service provider metadata
 *
 * Unfortunately this file inside SSP couldn't be customized in any clean
 * way so it has been copied here and forked. The main differences are
 * the config lookup, but also using the proxy SP module urls.
 *
 * Original file is: /.extlib/simplesamlphp/modules/saml/www/sp/metadata.php
 *
 * @package    auth_iomadsaml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../../config.php');
// @codingStandardsIgnoreEnd
require_once('../setup.php');
require_once('../locallib.php');

$download = optional_param('download', '', PARAM_RAW);
if ($download) {
    header('Content-Disposition: attachment; filename=' . $iomadsaml2auth->spname . '.xml');
}

// Allow generating SP metadata for a different domain which can
// be useful for setting up saml prior to a DNS cutover.
// Needs to be public so an IdP can load it ahead of time.
$baseurl = optional_param('baseurl', $CFG->wwwroot, PARAM_URL);

// To keep it simple, every time you visit this page, it should rebuild the SP XML.
$file = $iomadsaml2auth->get_file_sp_metadata_file($baseurl);
@unlink($file);

$xml = auth_iomadsaml2_get_sp_metadata($baseurl);

if (array_key_exists('output', $_REQUEST) && $_REQUEST['output'] == 'xhtml') {

	$t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');

	$t->data['header'] = 'iomadsaml20-sp';
	$t->data['metadata'] = htmlspecialchars($xml);
	$t->data['metadataflat'] = '$metadata[' . var_export($entityId, TRUE) . '] = ' . var_export($metaArray20, TRUE) . ';';
	$t->data['metaurl'] = $source->getMetadataURL();
	$t->show();
} else {
	// header('Content-Type: application/samlmetadata+xml');
	header('Content-Type: text/xml');
	echo($xml);
}

