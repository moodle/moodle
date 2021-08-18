<?php

class com_wiris_plugin_impl_RenderImplIntegratedServices extends com_wiris_plugin_impl_RenderImpl {
	public function __construct($plugin) { if(!php_Boot::$skip_constructor) {
		parent::__construct($plugin);
	}}
	public function render($format, $mml, $latex, $properties, $outProperties) {
		$servicesClass = Type::resolveClass("com.wiris.editor.services.PublicServices");
		$getInstance = Reflect::field($servicesClass, "getInstance");
		$publicServices = Reflect::callMethod($servicesClass, $getInstance, null);
		$args = new _hx_array(array());
		$args->push($mml);
		$args->push($latex);
		$args->push($properties);
		$args->push($outProperties);
		try {
			if(_hx_index_of($format, "png", null) !== -1) {
				$renderPngMethod = Reflect::field($publicServices, "renderPng");
				$pngObject = Reflect::callMethod($publicServices, $renderPngMethod, $args);
				$pngBytes = $pngObject;
				return haxe_io_Bytes::ofData($pngBytes);
			} else {
				if(_hx_index_of($format, "svg", null) !== -1) {
					$renderSvgMethod = Reflect::field($publicServices, "renderSvg");
					$svgObject = Reflect::callMethod($publicServices, $renderSvgMethod, $args);
					$svgString = $svgObject;
					return haxe_io_Bytes::ofString($svgString);
				} else {
					throw new HException("Unexpected image format.");
				}
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				throw new HException($e->getMessage());
			}
		}
	}
	public function showImage($digest, $mml, $provider) {
		if($digest === null && $mml === null) {
			throw new HException("Missing parameters 'formula' or 'mml'.");
		}
		if($digest !== null && $mml !== null) {
			throw new HException("Only one parameter 'formula' or 'mml' is valid.");
		}
		$atts = false;
		if($digest === null && $mml !== null) {
			$digest = $this->computeDigest($mml, $provider->getRenderParameters($this->plugin->getConfiguration()));
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
		$ss = $this->getEditorParametersList();
		$i = null;
		if($provider !== null) {
			$renderParameters = $provider->getRenderParameters($this->plugin->getConfiguration());
			{
				$_g1 = 0; $_g = $ss->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$key = $ss[$i1];
					$value = com_wiris_system_PropertiesTools::getProperty($renderParameters, $key, null);
					if($value !== null) {
						$atts = true;
						$renderParams->set($key, $value);
					}
					unset($value,$key,$i1);
				}
			}
			if($atts) {
				if($mml !== null) {
					$digest = $this->computeDigest($mml, com_wiris_system_PropertiesTools::toProperties($renderParams));
				} else {
					$digest = $this->computeDigest($renderParams->get("mml"), com_wiris_system_PropertiesTools::toProperties($renderParams));
				}
			}
		}
		$store = $this->plugin->getStorageAndCache();
		$bs = null;
		$bs = $store->retreiveData($digest, $this->plugin->getConfiguration()->getProperty("wirisimageformat", "png"));
		if($bs === null) {
			if($this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMS, null) !== null) {
				$json = com_wiris_util_json_JSon::decode($this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMS, null));
				$decodedHash = $json;
				$keys = $decodedHash->keys();
				$notAllowedParams = _hx_explode(",", com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_NOTRENDER_LIST);
				while($keys->hasNext()) {
					$key = $keys->next();
					if(!com_wiris_system_ArrayEx::contains($notAllowedParams, $key)) {
						$renderParams->set($key, $decodedHash->get($key));
					}
					unset($key);
				}
			} else {
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
			$prop = com_wiris_system_PropertiesTools::toProperties($renderParams);
			$iter = $renderParams->keys();
			$mml = $renderParams->get("mml");
			$b = $this->render($this->plugin->getConfiguration()->getProperty("wirisimageformat", "png"), $mml, null, $prop, null);
			$store->storeData($digest, $this->plugin->getConfiguration()->getProperty("wirisimageformat", "png"), $b->b);
			$bs = $b->b;
		}
		return $bs;
	}
	function __toString() { return 'com.wiris.plugin.impl.RenderImplIntegratedServices'; }
}
