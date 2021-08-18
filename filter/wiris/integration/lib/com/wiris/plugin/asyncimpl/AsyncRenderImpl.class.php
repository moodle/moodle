<?php

class com_wiris_plugin_asyncimpl_AsyncRenderImpl implements com_wiris_plugin_asyncapi_AsyncRender{
	public function __construct($plugin) {
		if(!php_Boot::$skip_constructor) {
		$this->render = new com_wiris_plugin_impl_RenderImpl($plugin);
		$this->plugin = $plugin;
	}}
	public function getMathml($digest, $call) {
		$s = $this->render->getMathml($digest);
		$call->returnString($s);
	}
	public function onError($msg) {
		$this->call->error($msg);
	}
	public function onData($data) {
		$b = com_wiris_system_TypeTools::string2ByteData_iso8859_1($data);
		$store = $this->plugin->getStorageAndCache();
		$store->storeData($this->digest, "png", $b->b);
		$bs = $b->b;
		$this->call->returnBytes($bs);
	}
	public function showImage($digest, $mml, $param, $call) {
		if($digest === null && $mml === null) {
			throw new HException("Missing parameters 'formula' or 'mml'.");
		}
		if($digest !== null && $mml !== null) {
			throw new HException("Only one parameter 'formula' or 'mml' is valid.");
		}
		$atts = false;
		if($digest === null && $mml !== null) {
			$digest = $this->render->computeDigest($mml, $param);
		}
		$formula = $this->plugin->getStorageAndCache()->decodeDigest($digest);
		if($formula === null) {
			throw new HException("Formula associated to digest not found.");
		}
		if(StringTools::startsWith($formula, "<")) {
			throw new HException("Not implemented.");
		}
		$iniFile = com_wiris_util_sys_IniFile::newIniFileFromString($formula);
		$renderParams = $iniFile->getProperties();
		$i = null;
		$ss = $this->render->getEditorParametersList();
		if($param !== null) {
			$_g1 = 0; $_g = $ss->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$key = $ss[$i1];
				$value = com_wiris_system_PropertiesTools::getProperty($param, $key, null);
				if($value !== null) {
					$atts = true;
					$renderParams->set($key, $value);
				}
				unset($value,$key,$i1);
			}
		}
		if($atts) {
			if($mml !== null) {
				$digest = $this->render->computeDigest($mml, com_wiris_system_PropertiesTools::toProperties($renderParams));
			} else {
				$digest = $this->render->computeDigest($renderParams->get("mml"), com_wiris_system_PropertiesTools::toProperties($renderParams));
			}
		}
		$store = $this->plugin->getStorageAndCache();
		$bs = null;
		$bs = $store->retreiveData($digest, "png");
		if($bs === null) {
			{
				$_g1 = 0; $_g = $ss->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$key = $ss[$i1];
					if(!$renderParams->exists($key)) {
						$confKey = com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->get($key);
						if($confKey !== null) {
							$value = $this->plugin->getConfiguration()->getProperty($confKey, null);
							if($value !== null) {
								$renderParams->set($key, $value);
							}
							unset($value);
						}
						unset($confKey);
					}
					unset($key,$i1);
				}
			}
			$h = new com_wiris_plugin_impl_HttpImpl($this->plugin->getImageServiceURL(null, true), null);
			$h->setHeader("Content-Type", "application/x-www-form-urlencoded");
			$this->plugin->addReferer($h);
			$this->plugin->addProxy($h);
			$iter = $renderParams->keys();
			while($iter->hasNext()) {
				$key = $iter->next();
				$h->setParameter($key, $renderParams->get($key));
				unset($key);
			}
			$this->digest = $digest;
			$this->call = $call;
			com_wiris_plugin_asyncimpl_HttpPostAndContinue::doPost($h, $this, "onData");
		} else {
			$call->returnBytes($bs);
		}
	}
	public function createImage($mml, $param, &$output, $call) {
		$output = $output;
		new com_wiris_plugin_asyncimpl_CreateImageMethod($this, $mml, $param, $output, $call);
	}
	public $call;
	public $digest;
	public $plugin;
	public $render;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->�dynamics[$m]) && is_callable($this->�dynamics[$m]))
			return call_user_func_array($this->�dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call �'.$m.'�');
	}
	function __toString() { return 'com.wiris.plugin.asyncimpl.AsyncRenderImpl'; }
}
