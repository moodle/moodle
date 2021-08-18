<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();
$resource = $provider->getRequiredParameter('resourcefile');

$resourceLoader = $pluginBuilder->newResourceLoader();
header('Content-Type:' . $resourceLoader->getContentType($resource));
echo $resourceLoader->getcontent($resource);
