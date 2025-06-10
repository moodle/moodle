<?php

interface com_wiris_plugin_api_CleanCache {
	function getContentType();
	function getCacheOutput();
	function init($provider);
}
