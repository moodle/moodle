<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

try {
    $mml = $provider->getRequiredParameter('mml');
} catch (Exception $e) {
    exit("Error: Required parameter 'mml' not found.");
}

$render = $pluginBuilder->newRender();

$outp = null;

// Adding - if necessary - CORS headers.
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

echo $render->createImage($mml, $provider, $outp);
