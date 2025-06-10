<?php

interface com_wiris_plugin_asyncapi_AsyncTextService {
	function mathml2accessible($mml, $lang, $prop, $response);
	function service($serviceName, $provider, $response);
}
