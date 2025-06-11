<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

try {
    $formula = $provider->getRequiredParameter('formula');
} catch (Exception $e) {
    exit("Error: Required parameter 'formula' not found.");
}

$cas = $pluginBuilder->newCas();

// Adding - if necessary - CORS headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

$r = $cas->showCasImage($formula, null);
header('Content-Type: image/png');
echo $r;
