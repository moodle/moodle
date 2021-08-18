<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

$service = $provider->getRequiredParameter('service');
$mml = $provider->getParameter('mml', null);
$latex = $provider->getParameter('latex', null);
$lang = $provider->getParameter('lang', 'en');

$render = $pluginBuilder->newTextService();

// Adding - if necessary - CORS headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

$r = $render->service($service, $provider);
header('Content-Type: text/plain; charset=utf-8');
echo $r;
