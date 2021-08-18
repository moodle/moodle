<?php

interface com_wiris_plugin_api_TextService {
	function filter($str, $prop);
	function getMathML($digest, $latex);
	function latex2mathml($mml);
	function mathml2latex($mml);
	function mathml2accessible($mml, $lang, $prop);
	function service($serviceName, $provider);
}
