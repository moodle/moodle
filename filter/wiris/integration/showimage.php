<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

$digest = $provider->getParameter('formula', null);
$mml = $provider->getParameter('mml', null);
$render = $pluginBuilder->newRender();
$jsonformat = $provider->getParameter('jsonformat', null);
$lang = $provider->getParameter('lang', 'en');

// Backwards compatibility.
// showimage.php?formula.png --> showimage.php?formula.
// because formula is md5 string, remove all extensions.
if (!is_null($digest)) {
    $a = explode(".", $digest);
    $digest = array_shift($a);
}

// Adding - if necessary - CORS headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

if ($pluginBuilder->getConfiguration()->getProperty("wirispluginperformance", "false") == "true") {

    // Cache headers
    header("Content-type: application/json");
    header("Pragma:"); // HTTP 1.0
    header("Cache-Control: public, max-age=3600"); // HTTP 1.1
    // If digest == null formula is not in cache.
    if (is_null($digest)) {
        $render->showImage(null, $mml, $provider);
        $digest = $render->computeDigest($mml, $provider->getRenderParameters($pluginBuilder->getConfiguration()));
    }
    $r = $render->showImageJson($digest, $lang);
    // If a formula is not in server cache, this request shouldn't be cached.
    if (strpos($r, "warning" )) {
        header("Pragma: no-cache"); // HTTP 1.0
        header("Cache-Control: no-cache, no-store, must-revalidate"); //HTTP 1.1
    }
} else {
    $contentType = $pluginBuilder->getImageFormatController()->getContentType();
    header('Content-Type: ' . $contentType);
    $r = $render->showImage($digest, $mml, $provider);
}
echo $r;
