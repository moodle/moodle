<?php
// ${license.statement}

// Please, set if called from the command line.
// $_SERVER['SCRIPT_NAME'] = "/generic/integration/filter.php";
require_once ('pluginbuilder.php');
$text = $pluginBuilder->newTextService();
$input = "<html><body><b>Formula: </b><math><mfrac><mi>x</mi><mn>1000</mn></mfrac></math></body></html>";
$params = null;
$output = $text->filter($input, $params);

// Adding - if necessary - CORS headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

header('Content-Type: text/html;charset=UTF-8');
echo $output;
