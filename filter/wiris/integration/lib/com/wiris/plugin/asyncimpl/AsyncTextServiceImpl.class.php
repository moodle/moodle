<?php

class com_wiris_plugin_asyncimpl_AsyncTextServiceImpl implements com_wiris_plugin_impl_HttpListener, com_wiris_plugin_asyncapi_AsyncTextService{
	public function __construct($plugin) {
		if(!php_Boot::$skip_constructor) {
		$this->plugin = $plugin;
	}}
	public function onError($msg) {
		$this->response->error($msg);
	}
	public function onData($data) {
		if($this->digest !== null) {
			$store = $this->plugin->getStorageAndCache();
			$ext = com_wiris_plugin_impl_TextServiceImpl::getDigestExtension($this->serviceName, $this->provider);
			$store->storeData($this->digest, $ext, com_wiris_system_Utf8::toBytes($data));
		}
		$this->response->returnString($data);
	}
	public function mathml2accessible($mml, $lang, $param, $response) {
		if($lang !== null) {
			$param["lang"] = $lang;
		}
		$param["mml"] = $mml;
		$provider = $this->plugin->newGenericParamsProvider($param);
		$this->service("mathml2accessible", $provider, $response);
	}
	public function service($serviceName, $provider, $response) {
		$this->serviceName = $serviceName;
		$this->provider = $provider;
		$this->response = $response;
		$this->digest = null;
		if(com_wiris_plugin_impl_TextServiceImpl::hasCache($serviceName)) {
			$this->digest = $this->plugin->newRender()->computeDigest(null, $provider->getRenderParameters($this->plugin->getConfiguration()));
			$store = $this->plugin->getStorageAndCache();
			$ext = com_wiris_plugin_impl_TextServiceImpl::getDigestExtension($serviceName, $this->provider);
			$s = $store->retreiveData($this->digest, $ext);
			if($s !== null) {
				$response->returnString(com_wiris_system_Utf8::fromBytes($s));
				return;
			}
		}
		$url = $this->plugin->getImageServiceURL($serviceName, true);
		$h = new com_wiris_plugin_impl_HttpImpl($url, $this);
		$this->plugin->addReferer($h);
		$this->plugin->addProxy($h);
		if($this->param !== null) {
			$ha = com_wiris_system_PropertiesTools::fromProperties($this->param);
			$iter = $ha->keys();
			while($iter->hasNext()) {
				$k = $iter->next();
				$h->setParameter($k, $ha->get($k));
				unset($k);
			}
		}
		$h->request(true);
	}
	public $response;
	public $provider;
	public $param;
	public $serviceName;
	public $digest;
	public $plugin;
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
	function __toString() { return 'com.wiris.plugin.asyncimpl.AsyncTextServiceImpl'; }
}
