<?php
// ${license.statement}
require_once ('pluginbuilder.php');

// Adding - if necessary - CORS headers.
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

$render = $pluginBuilder->newTest();
echo $render->getTestPage();
