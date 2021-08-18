<?php

class com_wiris_plugin_impl_PluginBuilderImpl extends com_wiris_plugin_api_PluginBuilder {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->updaterChain = new _hx_array(array());
		$this->updaterChain->push(new com_wiris_plugin_impl_DefaultConfigurationUpdater());
		$ci = new com_wiris_plugin_impl_ConfigurationImpl();
		$this->configuration = $ci;
		$ci->setPluginBuilderImpl($this);
	}}
	public function newGenericParamsProvider($properties) {
		return new com_wiris_plugin_impl_GenericParamsProviderImpl($properties);
	}
	public function getImageFormatController() {
		$imageFormatController = null;
		if($this->configuration->getProperty(com_wiris_plugin_api_ConfigurationKeys::$IMAGE_FORMAT, "png") === "svg") {
			$imageFormatController = new com_wiris_plugin_impl_ImageFormatControllerSvg();
		} else {
			$imageFormatController = new com_wiris_plugin_impl_ImageFormatControllerPng();
		}
		return $imageFormatController;
	}
	public function isEditorLicensed() {
		$licenseClass = Type::resolveClass("com.wiris.util.sys.License");
		if($licenseClass !== null) {
			$init = Reflect::field($licenseClass, "init");
			$initMethodParams = new _hx_array(array());
			$initMethodParams->push($this->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_KEY, ""));
			$initMethodParams->push("");
			$initMethodParams->push(new _hx_array(array(4, 5, 9, 10)));
			Reflect::callMethod($licenseClass, $init, $initMethodParams);
			$isLicensedMethod = Reflect::field($licenseClass, "isLicensed");
			$isLicensedObject = Reflect::callMethod($licenseClass, $isLicensedMethod, null);
			$isLicensed = null;
			if(_hx_index_of(Type::getClassName(Type::getClass($isLicensedObject)), "Boolean", null) !== -1) {
				$isLicensed = _hx_string_call($isLicensedObject, "toString", array());
			} else {
				$isLicensed = $isLicensedObject;
			}
			return $isLicensed;
		}
		return false;
	}
	public function addStats($url) {
		$saveMode = $this->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SAVE_MODE, "xml");
		$externalPlugin = $this->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EXTERNAL_PLUGIN, "false");
		$version = null;
		try {
			$version = com_wiris_system_Storage::newResourceStorage("VERSION")->read();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$ex = $_ex_;
			{
				$version = "Missing version";
			}
		}
		$tech = null;
		try {
			$tech = str_replace("\x0A", "", com_wiris_system_Storage::newResourceStorage("tech.txt")->read());
			$tech = str_replace("\x0D", "", $tech);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$ex2 = $_ex_;
			{
				$tech = "MissingTech";
			}
		}
		if(_hx_index_of($url, "?", null) !== -1) {
			return $url . "&stats-mode=" . $saveMode . "&stats-version=" . $version . "&stats-scriptlang=" . $tech . "&external=" . $externalPlugin;
		} else {
			return $url . "?stats-mode=" . $saveMode . "&stats-version=" . $version . "&stats-scriptlang=" . $tech . "&external=" . $externalPlugin;
		}
	}
	public function addCorsHeaders($response, $origin) {
		$conf = $this->getConfiguration();
		if($conf->getProperty("wiriscorsenabled", "false") === "true") {
			$confDir = $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CONFIGURATION_PATH, null);
			$corsConfFile = $confDir . "/corsservers.ini";
			$s = com_wiris_system_Storage::newStorage($corsConfFile);
			if($s->exists()) {
				$dir = $s->read();
				$allowedHosts = _hx_explode("\x0A", $dir);
				if(com_wiris_system_ArrayEx::contains($allowedHosts, $origin)) {
					$response->setHeader("Access-Control-Allow-Origin", $origin);
				}
			} else {
				$response->setHeader("Access-Control-Allow-Origin", "*");
			}
		}
	}
	public function addReferer($h) {
		$conf = $this->getConfiguration();
		if($conf->getProperty("wirisexternalplugin", "false") === "true") {
			$h->setHeader("Referer", $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EXTERNAL_REFERER, "external referer not found"));
		} else {
			$h->setHeader("Referer", $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$REFERER, ""));
		}
	}
	public function addProxy($h) {
		$conf = $this->getConfiguration();
		$proxyEnabled = $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$HTTPPROXY, "false");
		if($proxyEnabled === "true") {
			$host = $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$HTTPPROXY_HOST, null);
			$port = Std::parseInt($conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$HTTPPROXY_PORT, "80"));
			if($host !== null && strlen($host) > 0) {
				$user = $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$HTTPPROXY_USER, null);
				$pass = $conf->getProperty(com_wiris_plugin_api_ConfigurationKeys::$HTTPPROXY_PASS, null);
				$h->setProxy(com_wiris_std_system_HttpProxy::newHttpProxy($host, $port, $user, $pass));
			}
		}
	}
	public function getImageServiceURL($service, $stats) {
		$protocol = null;
		$port = null;
		$url = null;
		$config = $this->getConfiguration();
		if(Type::resolveClass("com.wiris.editor.services.PublicServices") !== null) {
			if($config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SERVICE_HOST, null) === "www.wiris.net") {
				return $this->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CONTEXT_PATH, "/") . "/editor/editor";
			}
		}
		$protocol = $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SERVICE_PROTOCOL, null);
		$port = $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SERVICE_PORT, null);
		$url = $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$INTEGRATION_PATH, null);
		if($protocol === null && $url !== null) {
			if(StringTools::startsWith($url, "https")) {
				$protocol = "https";
			}
		}
		if($protocol === null) {
			$protocol = "http";
		}
		if($port !== null) {
			if($protocol === "http") {
				if(!($port === "80")) {
					$port = ":" . $port;
				} else {
					$port = "";
				}
			}
			if($protocol === "https") {
				if(!($port === "443")) {
					$port = ":" . $port;
				} else {
					$port = "";
				}
			}
		} else {
			$port = "";
		}
		$domain = $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SERVICE_HOST, null);
		$path = $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SERVICE_PATH, null);
		if($service !== null) {
			$end = _hx_last_index_of($path, "/", null);
			if($end === -1) {
				$path = $service;
			} else {
				$path = _hx_substr($path, 0, $end) . "/" . $service;
			}
		}
		if($stats) {
			$path = $this->addStats($path);
		}
		return $protocol . "://" . $domain . $port . $path;
	}
	public function newResourceLoader() {
		return new com_wiris_plugin_impl_ServiceResourceLoaderImpl();
	}
	public function newCleanCache() {
		return new com_wiris_plugin_impl_CleanCacheImpl($this);
	}
	public function setStorageAndCacheCacheFormulaObject($cacheFormula) {
		$this->storageAndCacheCacheFormulaObject = $cacheFormula;
	}
	public function setStorageAndCacheCacheObject($cache) {
		$this->storageAndCacheCacheObject = $cache;
	}
	public function setStorageAndCacheInitObject($obj) {
		$this->storageAndCacheInitObject = $obj;
	}
	public function getConfigurationUpdaterChain() {
		return $this->updaterChain;
	}
	public function initialize($sac, $conf) {
		if($this->storageAndCacheCacheObject === null) {
			$this->storageAndCacheCacheObject = new com_wiris_plugin_impl_CacheImpl($conf);
		}
		if($this->storageAndCacheCacheFormulaObject === null) {
			$this->storageAndCacheCacheFormulaObject = new com_wiris_plugin_impl_CacheFormulaImpl($conf);
		}
		$sac->init($this->storageAndCacheInitObject, $conf, $this->storageAndCacheCacheObject, $this->storageAndCacheCacheFormulaObject);
	}
	public function getStorageAndCache() {
		if($this->store === null) {
			$className = $this->configuration->getProperty(com_wiris_plugin_api_ConfigurationKeys::$STORAGE_CLASS, null);
			if($className === null || $className === "FolderTreeStorageAndCache") {
				$this->store = new com_wiris_plugin_impl_FolderTreeStorageAndCache();
			} else {
				if($className === "FileStorageAndCache") {
					$this->store = new com_wiris_plugin_impl_FileStorageAndCache();
				} else {
					$cls = Type::resolveClass($className);
					if($cls === null) {
						throw new HException("Class " . $className . " not found.");
					}
					$this->store = Type::createInstance($cls, new _hx_array(array()));
					if($this->store === null) {
						throw new HException("Instance from " . Std::string($cls) . " cannot be created.");
					}
				}
			}
			$this->initialize($this->store, $this->configuration->getFullConfiguration());
		}
		return $this->store;
	}
	public function getConfiguration() {
		return $this->configuration;
	}
	public function newAsyncTextService() {
		return new com_wiris_plugin_asyncimpl_AsyncTextServiceImpl($this);
	}
	public function newTextService() {
		if(Type::resolveClass("com.wiris.editor.services.PublicServices") !== null && $this->isEditorLicensed()) {
			return new com_wiris_plugin_impl_TextServiceImplIntegratedServices($this);
		}
		return new com_wiris_plugin_impl_TextServiceImpl($this);
	}
	public function newCas() {
		return new com_wiris_plugin_impl_CasImpl($this);
	}
	public function newEditor() {
		return new com_wiris_plugin_impl_EditorImpl($this);
	}
	public function newTest() {
		return new com_wiris_plugin_impl_TestImpl($this);
	}
	public function newAsyncRender() {
		return new com_wiris_plugin_asyncimpl_AsyncRenderImpl($this);
	}
	public function newRender() {
		if(Type::resolveClass("com.wiris.editor.services.PublicServices") !== null && $this->isEditorLicensed()) {
			return new com_wiris_plugin_impl_RenderImplIntegratedServices($this);
		}
		return new com_wiris_plugin_impl_RenderImpl($this);
	}
	public function setStorageAndCache($store) {
		$this->store = $store;
	}
	public function getAccessProvider() {
		return $this->accessProvider;
	}
	public function setAccessProvider($provider) {
		$this->accessProvider = $provider;
	}
	public function getCustomParamsProvider() {
		return $this->customParamsProvider;
	}
	public function setCustomParamsProvider($provider) {
		$this->customParamsProvider = $provider;
	}
	public function addConfigurationUpdater($conf) {
		$this->updaterChain->push($conf);
	}
	public $accessProvider = null;
	public $customParamsProvider = null;
	public $storageAndCacheCacheFormulaObject = null;
	public $storageAndCacheCacheObject = null;
	public $storageAndCacheInitObject;
	public $updaterChain;
	public $store;
	public $configuration;
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
	function __toString() { return 'com.wiris.plugin.impl.PluginBuilderImpl'; }
}
