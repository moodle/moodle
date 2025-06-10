<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

$digest = $provider->getParameter('digest', null);
if ($digest == null) {
    $digest = $provider->getParameter('md5', null);
}

$latex = $provider->getParameter('latex', null);

// Adding - if necessary - CORS headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

$render = $pluginBuilder->newTextService();
echo $render->getMathML($digest, $latex);
