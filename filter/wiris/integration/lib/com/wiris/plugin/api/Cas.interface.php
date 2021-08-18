<?php

interface com_wiris_plugin_api_Cas {
	function cas($mode, $language);
	function createCasImage($imageParameter);
	function showCasImage($formula, $provider);
}
