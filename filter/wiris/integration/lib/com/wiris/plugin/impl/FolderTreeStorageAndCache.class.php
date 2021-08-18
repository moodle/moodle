<?php

class com_wiris_plugin_impl_FolderTreeStorageAndCache implements com_wiris_plugin_storage_StorageAndCache{
	public function __construct() {
		;
	}
	public function deleteCache() {
		$cache = new com_wiris_plugin_impl_CacheImpl($this->config);
		try {
			$cache->deleteAll();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				throw new HException("Error: can't delete cache: " . Std::string($e->getMessage()));
			}
		}
	}
	public function getExtension($service) {
		if($service === "png") {
			return "png";
		}
		if($service === "svg") {
			return "svg";
		}
		return $service . ".txt";
	}
	public function storeData($digest, $service, $stream) {
		try {
			$this->cache->set($digest . "." . $this->getExtension($service), haxe_io_Bytes::ofData($stream));
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				throw new HException("Error: can't write on cache: " . Std::string($e->getMessage()));
			}
		}
	}
	public function retreiveData($digest, $service) {
		$data = $this->cache->get($digest . "." . $this->getExtension($service));
		if($data !== null) {
			return $data->b;
		} else {
			return null;
		}
	}
	public function decodeDigest($digest) {
		$data = $this->cacheFormula->get($digest . ".ini");
		if($data !== null) {
			return com_wiris_system_Utf8::fromBytes($data->b);
		} else {
			return null;
		}
	}
	public function codeDigest($content) {
		$digest = com_wiris_system_Md5Tools::encodeString($content);
		try {
			$this->cacheFormula->set($digest . ".ini", haxe_io_Bytes::ofData(com_wiris_system_Utf8::toBytes($content)));
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				throw new HException("Error: can't write on cache: " . Std::string($e->getMessage()));
			}
		}
		return $digest;
	}
	public function init($obj, $config, $cache, $cacheFormula) {
		$this->config = $config;
		$this->cache = $cache;
		$this->cacheFormula = $cacheFormula;
	}
	public $cacheFormula;
	public $cache;
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
	static $backwards_compat = true;
	function __toString() { return 'com.wiris.plugin.impl.FolderTreeStorageAndCache'; }
}
