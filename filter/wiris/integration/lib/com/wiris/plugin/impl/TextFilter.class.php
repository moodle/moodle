<?php

class com_wiris_plugin_impl_TextFilter {
	public function __construct($plugin) {
		if(!php_Boot::$skip_constructor) {
		$this->plugin = $plugin;
		$this->render = $plugin->newRender();
		$this->service = $plugin->newTextService();
		$this->fixUrl = null;
	}}
	public function save_xml_encode($str) {
		$tags = com_wiris_plugin_impl_TextFilterTags::newSafeXml();
		$str = str_replace($tags->out_double_quote, $tags->in_double_quote, $str);
		$str = str_replace($tags->out_open, $tags->in_open, $str);
		$str = str_replace($tags->out_close, $tags->in_close, $str);
		$str = str_replace($tags->out_entity, $tags->in_entity, $str);
		$str = str_replace($tags->out_quote, $tags->in_quote, $str);
		return $str;
	}
	public function html_entity_encode($str) {
		$str = str_replace("<", "&lt;", $str);
		$str = str_replace(">", "&gt;", $str);
		$str = str_replace("\"", "&quot;", $str);
		$str = str_replace("&", "&amp;", $str);
		return $str;
	}
	public function html_entity_decode($str) {
		$str = str_replace("&lt;", "<", $str);
		$str = str_replace("&gt;", ">", $str);
		$str = str_replace("&quot;", "\"", $str);
		$str = str_replace("&nbsp;", com_wiris_plugin_impl_TextFilter::$NBSP, $str);
		$str = str_replace("&amp;", "&", $str);
		return $str;
	}
	public function math2Img($str, $prop) {
		$img = "<img";
		$output = array();;
		$prop["centerbaseline"] = "false";
		$prop["accessible"] = "true";
		$prop["metrics"] = "true";
		$provider = $this->plugin->newGenericParamsProvider($prop);
		$src = null;
		$alt = null;
		$width = null;
		$height = null;
		$baseline = null;
		$role = null;
		if($this->plugin->getConfiguration()->getProperty("wirispluginperformance", "false") === "false") {
			$src = $this->render->createImage($str, $provider, $output);
			$img .= " src=\"" . $src . "\"";
			$alt = com_wiris_system_PropertiesTools::getProperty($output, "alt", null);
			$width = com_wiris_system_PropertiesTools::getProperty($output, "width", null);
			$height = com_wiris_system_PropertiesTools::getProperty($output, "height", null);
			$baseline = com_wiris_system_PropertiesTools::getProperty($output, "baseline", null);
			$role = com_wiris_system_PropertiesTools::getProperty($output, "role", null);
		} else {
			$digest = $this->render->computeDigest($str, $prop);
			$hashImage = $this->render->showImageHash($digest, com_wiris_system_PropertiesTools::getProperty($prop, "lang", null));
			if($hashImage === null) {
				$this->render->showImage(null, $str, $provider);
				$hashImage = $this->render->showImageHash($digest, com_wiris_system_PropertiesTools::getProperty($prop, "lang", null));
			}
			$content = $hashImage->get("content");
			if($this->plugin->getConfiguration()->getProperty("wirisimageformat", "png") === "png") {
				$img .= " src=\"data:image/png;base64," . $content . "\"";
			} else {
				$img .= " src=\"data:image/svg+xml;charset=utf8," . com_wiris_util_type_UrlUtils::urlComponentEncode($content) . "\"";
			}
			if($hashImage->exists("alt")) {
				$alt = $hashImage->get("alt");
			} else {
				$alt = $this->service->mathml2accessible($str, null, $prop);
			}
			$width = $hashImage->get("width");
			$height = $hashImage->get("height");
			$baseline = $hashImage->get("baseline");
			$role = $hashImage->get("role");
		}
		$dpi = Std::parseFloat($this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$WIRIS_DPI, "96"));
		if($this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMS, null) !== null) {
			$json = com_wiris_util_json_JSon::decode($this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMS, null));
			$decodedHash = $json;
			if($decodedHash->exists("dpi")) {
				$dpi = Std::parseFloat($decodedHash->get("dpi"));
			}
		}
		$mml = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$FILTER_OUTPUT_MATHML, "false") === "true";
		$f = 96 / $dpi;
		$imageFormatController = $this->plugin->getImageFormatController();
		$metricsHash = new Hash();
		$metricsHash->set("width", Std::parseInt($width));
		$metricsHash->set("height", Std::parseInt($height));
		$metricsHash->set("baseline", Std::parseInt($baseline));
		$imageFormatController->scalateMetrics($dpi, $metricsHash);
		$alt = $this->html_entity_encode($alt);
		$img .= " class=\"Wirisformula\"";
		$img .= " alt=\"" . $alt . "\"";
		$img .= " width=\"" . _hx_string_rec($metricsHash->get("width"), "") . "\"";
		$img .= " height=\"" . _hx_string_rec($metricsHash->get("height"), "") . "\"";
		$verticalAlign = $metricsHash->get("baseline") - $metricsHash->get("height");
		$img .= " style=\"vertical-align:" . _hx_string_rec($verticalAlign, "") . "px\"";
		$img .= " role=\"" . $role . "\"";
		if($mml) {
			$tag = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_MATHML_ATTRIBUTE, "data-mathml");
			$img .= " " . $tag . "='" . $this->save_xml_encode($str) . "'";
		}
		$img .= "/>";
		return $img;
	}
	public function filterApplet($tags, $text, $prop, $safeXML) {
		$n0 = null;
		$n1 = null;
		$output = null;
		$sub = null;
		$output = new StringBuf();
		$n0 = 0;
		$n1 = _hx_index_of(strtoupper($text), $tags->in_appletopen, $n0);
		while($n1 >= 0) {
			$output->add(_hx_substr($text, $n0, $n1 - $n0));
			$n0 = $n1;
			$n1 = _hx_index_of(strtoupper($text), $tags->in_appletclose, $n0);
			if($n1 >= 0) {
				$n1 = $n1 + strlen($tags->in_appletclose);
				$sub = _hx_substr($text, $n0, $n1 - $n0);
				if($safeXML) {
					if($this->fixUrl === null) {
						$this->fixUrl = new EReg("<a href=\"[^\"]*\"[^>]*>([^<]*)<\\/a>|<a href=\"[^\"]*\">", "");
					}
					$sub = $this->fixUrl->replace($sub, "\$1");
					$sub = $this->html_entity_decode($sub);
					$sub = str_replace($tags->in_double_quote, $tags->out_double_quote, $sub);
					$sub = str_replace($tags->in_open, $tags->out_open, $sub);
					$sub = str_replace($tags->in_close, $tags->out_close, $sub);
					$sub = str_replace($tags->in_entity, $tags->out_entity, $sub);
					$sub = str_replace($tags->in_quote, $tags->out_quote, $sub);
				}
				$n0 = $n1;
				$output->add($sub);
				$n1 = _hx_index_of(strtoupper($text), $tags->in_appletopen, $n0);
			}
		}
		$output->add(_hx_substr($text, $n0, null));
		return $output->b;
	}
	public function filterMath($tags, $text, $prop, $safeXML) {
		$n0 = null;
		$n1 = null;
		$m0 = null;
		$m1 = null;
		$output = null;
		$sub = null;
		$output = new StringBuf();
		$n0 = 0;
		$n1 = _hx_index_of($text, $tags->in_mathopen, $n0);
		$tag = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_MATHML_ATTRIBUTE, "data-mathml");
		$dataMathml = _hx_index_of($text, $tag, 0);
		while($n1 >= 0) {
			$m0 = $n0;
			$output->add(_hx_substr($text, $n0, $n1 - $n0));
			$n0 = $n1;
			$n1 = _hx_index_of($text, $tags->in_mathclose, $n0);
			if($n1 >= 0) {
				$n1 = $n1 + strlen($tags->in_mathclose);
				$sub = _hx_substr($text, $n0, $n1 - $n0);
				if($safeXML) {
					if($dataMathml !== -1) {
						$m1 = _hx_index_of($text, "/>", $n1);
						if($m1 === -1) {
							$m1 = _hx_index_of($text, ">", $n1);
						}
						if($m1 >= 0 && (_hx_index_of($text, "<img", $n1) === -1 || _hx_index_of($text, "<img", $n1) > $m1)) {
							$m0 = _hx_last_index_of(_hx_substr($text, $m0, $n0 - $m0), "<img", null);
							if($m0 >= 0) {
								if(_hx_index_of($text, $tag, $m0) > 0 && _hx_index_of($text, $tag, $m0) < $n1) {
									$n0 = $n1;
									$output->add($sub);
									$n1 = _hx_index_of($text, $tags->in_mathopen, $n0);
									$m0 = $m1;
									continue;
								}
							}
						}
					}
					if($this->fixUrl === null) {
						$this->fixUrl = new EReg("<a href=\"[^\"]*\"[^>]*>([^<]*)<\\/a>|<a href=\"[^\"]*\">", "");
					}
					$sub = $this->fixUrl->replace($sub, "\$1");
					$sub = $this->html_entity_decode($sub);
					$sub = str_replace($tags->in_double_quote, $tags->out_double_quote, $sub);
					$sub = str_replace($tags->in_open, $tags->out_open, $sub);
					$sub = str_replace($tags->in_close, $tags->out_close, $sub);
					$sub = str_replace($tags->in_entity, $tags->out_entity, $sub);
					$sub = str_replace($tags->in_quote, $tags->out_quote, $sub);
				}
				$subtext = null;
				try {
					$subtext = $this->math2Img($sub, $prop);
				}catch(Exception $»e) {
					$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
					$e = $_ex_;
					{
						$subtext = $sub;
					}
				}
				$sub = $subtext;
				$n0 = $n1;
				$output->add($sub);
				$n1 = _hx_index_of($text, $tags->in_mathopen, $n0);
				unset($subtext,$e);
			}
		}
		$output->add(_hx_substr($text, $n0, null));
		return $output->b;
	}
	public function filter($str, $prop) {
		$saveMode = null;
		if($prop !== null) {
			$saveMode = com_wiris_system_PropertiesTools::getProperty($prop, "savemode", null);
		}
		if($saveMode === null) {
			$saveMode = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SAVE_MODE, "xml");
		}
		$b = null;
		$b = $saveMode === "safeXml";
		$tags = null;
		$mathNamespace = null;
		$namespaceIndex = _hx_index_of($str, ":" . "math", null);
		if($namespaceIndex >= 0) {
			$mathNamespace = _hx_substr($str, _hx_last_index_of($str, "<", $namespaceIndex) + 1, $namespaceIndex - (_hx_last_index_of($str, "<", $namespaceIndex) + 1));
		}
		if($b) {
			$tags = com_wiris_plugin_impl_TextFilterTags::newSafeXml();
		} else {
			$tags = com_wiris_plugin_impl_TextFilterTags::newXml($mathNamespace);
		}
		$str = $this->filterMath($tags, $str, $prop, $b);
		$str = $this->filterApplet($tags, $str, $prop, $b);
		return $str;
	}
	public $fixUrl;
	public $service;
	public $render;
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
	static $NBSP;
	function __toString() { return 'com.wiris.plugin.impl.TextFilter'; }
}
com_wiris_plugin_impl_TextFilter::$NBSP = com_wiris_plugin_impl_TextFilter_0();
function com_wiris_plugin_impl_TextFilter_0() {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(160);
		return $s->toString();
	}
}
