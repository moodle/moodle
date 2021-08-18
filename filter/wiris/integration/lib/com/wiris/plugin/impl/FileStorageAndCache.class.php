<?php

class com_wiris_plugin_impl_FileStorageAndCache implements com_wiris_plugin_storage_StorageAndCache{
	public function __construct() {
		;
	}
	public function deleteCache() {
	}
	public function getExtension($service) {
		if($service === "png") {
			return ".png";
		}
		return "." . $service . ".txt";
	}
	public function getAndCheckFolder($key) {
		$folder = com_wiris_system_PropertiesTools::getProperty($this->config, $key, null);
		if($folder === null || strlen(trim($folder)) === 0) {
			throw new HException("Missing configuration value: " . $key);
		}
		return $folder;
	}
	public function storeData($digest, $service, $stream) {
		$formula = $this->getAndCheckFolder(com_wiris_plugin_api_ConfigurationKeys::$CACHE_FOLDER);
		$store = com_wiris_util_sys_Store::newStoreWithParent(com_wiris_util_sys_Store::newStore($formula), $digest . $this->getExtension($service));
		$store->writeBinary(haxe_io_Bytes::ofData($stream));
	}
	public function retreiveData($digest, $service) {
		$formula = $this->getAndCheckFolder(com_wiris_plugin_api_ConfigurationKeys::$CACHE_FOLDER);
		$store = com_wiris_util_sys_Store::newStoreWithParent(com_wiris_util_sys_Store::newStore($formula), $digest . $this->getExtension($service));
		if(!$store->exists()) {
			return null;
		}
		return $store->readBinary()->b;
	}
	public function decodeDigest($digest) {
		$formula = $this->getAndCheckFolder(com_wiris_plugin_api_ConfigurationKeys::$FORMULA_FOLDER);
		$store = com_wiris_util_sys_Store::newStoreWithParent(com_wiris_util_sys_Store::newStore($formula), $digest . ".ini");
		return $store->read();
	}
	public function codeDigest($content) {
		$formula = $this->getAndCheckFolder(com_wiris_plugin_api_ConfigurationKeys::$FORMULA_FOLDER);
		$digest = com_wiris_system_Md5Tools::encodeString($content);
		$store = com_wiris_util_sys_Store::newStoreWithParent(com_wiris_util_sys_Store::newStore($formula), $digest . ".ini");
		$store->write($content);
		return $digest;
	}
	public function init($obj, $config, $cache, $cacheFormula) {
		$this->config = $config;
	}
	public $config;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	function __toString() { return 'com.wiris.plugin.impl.FileStorageAndCache'; }
}
