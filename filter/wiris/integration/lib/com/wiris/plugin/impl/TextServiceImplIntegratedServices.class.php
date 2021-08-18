<?php

class com_wiris_plugin_impl_TextServiceImplIntegratedServices extends com_wiris_plugin_impl_TextServiceImpl {
	public function __construct($plugin) { if(!php_Boot::$skip_constructor) {
		parent::__construct($plugin);
	}}
	public function mathml2accessible($mml, $lang, $param) {
		$servicesClass = Type::resolveClass("com.wiris.editor.services.PublicServices");
		$getInstance = Reflect::field($servicesClass, "getInstance");
		$publicServices = Reflect::callMethod($servicesClass, $getInstance, null);
		$serviceMethod = Reflect::field($publicServices, "mathml2accessible");
		$args = new _hx_array(array());
		if($mml === null) {
			throw new HException("Missing mml");
		} else {
			$args->push($mml);
		}
		$args->push($lang);
		$args->push($param);
		$serviceText = null;
		$serviceText = Reflect::callMethod($publicServices, $serviceMethod, $args);
		return $serviceText;
	}
	public function serviceText($serviceName, $provider) {
		$servicesClass = Type::resolveClass("com.wiris.editor.services.PublicServices");
		$getInstance = Reflect::field($servicesClass, "getInstance");
		$publicServices = Reflect::callMethod($servicesClass, $getInstance, null);
		$serviceMethod = Reflect::field($publicServices, $serviceName);
		$args = new _hx_array(array());
		$jsonResponse = new com_wiris_util_json_JsonAPIResponse();
		$result = new Hash();
		$serviceText = null;
		try {
			if(_hx_index_of($serviceName, "mathml2accessible", null) !== -1) {
				$mml = $provider->getParameter("mml", null);
				if($mml === null) {
					throw new HException("Missing mml");
				} else {
					$args->push($mml);
				}
				$lang = $provider->getParameter("lang", "en");
				$args->push($lang);
				$args->push($provider->getParameters());
				$serviceText = Reflect::callMethod($publicServices, $serviceMethod, $args);
			} else {
				if(_hx_index_of($serviceName, "mathml2latex", null) !== -1) {
					$mml = $provider->getParameter("mml", null);
					if($mml === null) {
						throw new HException("Missing mml");
					} else {
						$args->push($mml);
					}
					$keepMathml = $provider->getParameter("keepMathml", "false");
					if(_hx_index_of($keepMathml, "true", null) !== -1) {
						$args->push(true);
					} else {
						$args->push(false);
					}
					$args->push($provider->getParameters());
					$serviceText = Reflect::callMethod($publicServices, $serviceMethod, $args);
				} else {
					if(_hx_index_of($serviceName, "latex2mathml", null) !== -1) {
						$latex = $provider->getParameter("latex", null);
						if($latex === null) {
							throw new HException("Missing LaTeX");
						} else {
							$args->push($latex);
						}
						$keepLatex = $provider->getParameter("saveLatex", "false");
						if(_hx_index_of($keepLatex, "false", null) !== -1) {
							$args->push(false);
						} else {
							$args->push(true);
						}
						$args->push($provider->getParameters());
						$serviceText = Reflect::callMethod($publicServices, $serviceMethod, $args);
					} else {
						throw new HException("Unknow service " . $serviceName);
					}
				}
			}
			$result->set("text", $serviceText);
			$jsonResponse->setStatus(com_wiris_util_json_JsonAPIResponse::$STATUS_OK);
			$jsonResponse->setResult($result);
			return $jsonResponse->getResponse();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				if(_hx_index_of($serviceName, "mathml2accessible", null) !== -1) {
					$result->set("text", "Error converting from MathML to accessible text");
					$jsonResponse->setResult($result);
					$jsonResponse->setStatus(com_wiris_util_json_JsonAPIResponse::$STATUS_WARNING);
					return $jsonResponse->getResponse();
				} else {
					throw new HException($e->getMessage());
				}
			}
		}
	}
	public function service($serviceName, $provider) {
		$digest = null;
		if(com_wiris_plugin_impl_TextServiceImpl::hasCache($serviceName)) {
			$digest = $this->plugin->newRender()->computeDigest(null, $provider->getRenderParameters($this->plugin->getConfiguration()));
			$store = $this->plugin->getStorageAndCache();
			$ext = com_wiris_plugin_impl_TextServiceImpl::getDigestExtension($serviceName, $provider);
			$s = $store->retreiveData($digest, $ext);
			if($s !== null) {
				return com_wiris_system_Utf8::fromBytes($s);
			}
		}
		$result = $this->serviceText($serviceName, $provider);
		if($digest !== null) {
			$store = $this->plugin->getStorageAndCache();
			$ext = com_wiris_plugin_impl_TextServiceImpl::getDigestExtension($serviceName, $provider);
			$store->storeData($digest, $ext, com_wiris_system_Utf8::toBytes($result));
		}
		return $result;
	}
	function __toString() { return 'com.wiris.plugin.impl.TextServiceImplIntegratedServices'; }
}
