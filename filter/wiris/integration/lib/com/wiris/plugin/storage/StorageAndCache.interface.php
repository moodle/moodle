<?php

interface com_wiris_plugin_storage_StorageAndCache {
	function deleteCache();
	function storeData($digest, $service, $stream);
	function retreiveData($digest, $service);
	function decodeDigest($digest);
	function codeDigest($content);
	function init($obj, $config, $cache, $cacheFormula);
}
