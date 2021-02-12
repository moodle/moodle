<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/config.php');

$functionname = 'block_iomad_company_admin_get_companies';

/// PARAMETERS
$params = array();

/// XML-RPC CALL
echo "STEP1: Get the current companies\n";
//header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($params));
//var_dump($post); die;
$resp = xmlrpc_decode($curl->post($serverurl, $post));
print_r($resp);

// Change some details in one of the companies
$resp[1]['name'] = 'Globex Corporation';
$resp[1]['shortname'] = 'globex';

// Send it back
$functionname = 'block_iomad_company_admin_edit_companies';

// XML-RPC CALL
//header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($resp));
//var_dump($post); die;
$resp = xmlrpc_decode($curl->post($serverurl, $post));
print_r($resp);
