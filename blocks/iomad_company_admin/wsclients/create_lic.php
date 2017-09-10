<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * XML-RPC client for Moodle 2
 *
 * @authorr Jerome Mouneyrac
 */
//error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', false);

require(dirname(__FILE__) . '/config.php');

$functionname = 'block_iomad_company_admin_create_licenses';



/// PARAMETERS

$licenses = [
    "name" => "lic-name",
    "allocation" => 7,
    "validlength" => 30,
    "expirydate" => time()+ strtotime('+1 week'),
    "used" => 0,
    "companyid" => 11,
    "parentid" => 0,
    "courses" => [ "courseid" => 4 ],
];

$params = array($licenses);
//var_dump($licenses);
//var_dump(/** @lang text */
    "Test \n");

// XML-RPC CALL
$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($params));
//var_dump($post); die;
$resp = xmlrpc_decode($curl->post($serverurl, $post));
//print_r($resp);
