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

require(dirname(__FILE__) . '/config.php');

$functionname = 'block_iomad_company_admin_create_companies';

/// PARAMETERS
$company1 = new stdClass;
$company1->name = 'Company_5';
$company1->shortname = 'C5';
$company1->city = 'Köln';
$company1->country = 'DE';

$params = array(
    $company1,
);

// XML-RPC CALL
$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($params));
echo ("\n \$post: \n");
print_r($post); //die;
$resp = xmlrpc_decode($curl->post($serverurl, $post));

var_dump($resp);
