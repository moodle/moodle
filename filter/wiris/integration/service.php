<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$render = $pluginBuilder->newTextService();

// Adding - if necessary - CORS headers.
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

$provider = $pluginBuilder->getCustomParamsProvider();

try {
    $service = $provider->getRequiredParameter('service');
} catch (Exception $e) {
    exit("Error: Required parameter 'service' not found.");
}

$mml = $provider->getParameter('mml', null);
$latex = $provider->getParameter('latex', null);
$lang = $provider->getParameter('lang', 'en');

$r = $render->service($service, $provider);
header('Content-Type: text/plain; charset=utf-8');
echo $r;