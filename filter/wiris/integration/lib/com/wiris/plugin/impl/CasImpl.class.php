<?php

class com_wiris_plugin_impl_CasImpl implements com_wiris_plugin_api_Cas{
	public function __construct($plugin) {
		if(!php_Boot::$skip_constructor) {
		$this->plugin = $plugin;
	}}
	public function htmlentities($input, $entQuotes) {
		$returnValue = str_replace("&", "&amp;", $input);
		$returnValue = str_replace("<", "&lt;", $returnValue);
		$returnValue = str_replace(">", "gt;", $returnValue);
		if($entQuotes) {
			$returnValue = str_replace("\"", "&quot;", $returnValue);
			return $returnValue;
		}
		return $returnValue;
	}
	public function printCASContainer($config, $availableLanguages, $lang) {
		$output = new StringBuf();
		$output->add("<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\"/><script>");
		$output->add("var lang ='");
		$output->add($lang);
		$output->add("/strings.js';");
		$output->add(" ");
		$output->add(" var scriptsrc =  window.opener.path + '/lang/' + lang;");
		$output->add(" var script = document.createElement('script'); ");
		$output->add(" script.src = scriptsrc;");
		$output->add(" document.head.appendChild(script);");
		$output->add("</script><script>");
		$output->add("var scriptsrc = window.opener.path + '/core/cas.js'; ");
		$output->add(" var script = document.createElement('script'); ");
		$output->add(" script.src = scriptsrc;");
		$output->add(" document.head.appendChild(script);");
		$output->add("</script>");
		$output->add("<title>WIRIS CAS</title><style type=\"text/css\">");
		$output->add("/*<!--*/ html, body, #optionForm { height: 100%; } body { overflow: hidden; margin: 0; } #controls { width: 100%; } /*-->*/</style></head>");
		$output->add("<body><form id=\"optionForm\"><div id=\"appletContainer\"></div><table id=\"controls\"><tr><td>Width</td><td><input name=\"width\" type=\"text\" value=\"");
		$output->add($config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CAS_WIDTH, null));
		$output->add("\"/></td><td><input name=\"executeonload\" type=\"checkbox\"/> Calculate on load");
		$output->add("</td><td><input name=\"toolbar\" type=\"checkbox\" checked /> Show toolbar</td><td>Language <select id=\"languageList\">");
		$i = null;
		{
			$_g1 = 0; $_g = $availableLanguages->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$language = $this->htmlentities($availableLanguages[$i1], true);
				$output->add("<option value=\"");
				$output->add($language);
				$output->add("\">");
				$output->add($language);
				$output->add("</option>");
				unset($language,$i1);
			}
		}
		$output->add("</select></td></tr><tr><td>Height</td><td><input name=\"height\" type=\"text\" value=\"");
		$output->add($config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CAS_HEIGHT, null));
		$output->add("\"/></td><td><input name=\"focusonload\" type=\"checkbox\"/> Focus on load</td><td><input name=\"level\" type=\"checkbox\"/>");
		$output->add("Elementary mode</td><td></td></tr><tr><td colspan=\"5\"><input id=\"submit\" value=\"Accept\" type=\"button\"/>");
		$output->add("<input id=\"cancel\" value=\"Cancel\" type=\"button\"/></td></tr></table></form></body></html>");
		return $output->b;
	}
	public function printCAS($codebase, $archive, $className) {
		$output = new StringBuf();
		$output->add("<html><head><style type=\"text/css\">/*<!--*/ html, body { height: 100%; } body { overflow: hidden; margin: 0; } applet { height: 100%; width: 100%; } /*-->*/</style></head>");
		$output->add("<body><applet id=\"applet\" alt=\"WIRIS CAS\" codebase=\"");
		$output->add($this->htmlentities($codebase, true));
		$output->add("\" archive=\"");
		$output->add($this->htmlentities($archive, true));
		$output->add("\" code=\"");
		$output->add($this->htmlentities($className, true));
		$output->add("\"><p>You need JAVA&reg; to use WIRIS tools.<br />FREE download from <a target=\"_blank\" href=\"http://www.java.com\">www.java.com</a></p></applet></body></html>");
		return $output->b;
	}
	public function getAvailableCASLanguages($languageString) {
		$elem = null;
		$langs = _hx_explode(",", $languageString);
		$availableLanguages = new _hx_array(array());
		$iter = $langs->iterator();
		while($iter->hasNext()) {
			$elem = $iter->next();
			$elem = trim($elem);
			$availableLanguages->push($elem);
		}
		if($availableLanguages->length === 0) {
			$availableLanguages = new _hx_array(array());
			$availableLanguages->push("");
		}
		return $availableLanguages;
	}
	public function cas($mode, $language) {
		$output = new StringBuf();
		$output->add("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
		$config = $this->plugin->getConfiguration();
		$availableLanguages = $this->getAvailableCASLanguages($config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CAS_LANGUAGES, null));
		if($language === null || !com_wiris_system_ArrayEx::contains($availableLanguages, $language)) {
			$language = $availableLanguages[0];
		}
		if($mode !== null && $mode === "applet") {
			$codebase = str_replace("%LANG", $language, $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CAS_CODEBASE, null));
			$archive = str_replace("%LANG", $language, $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CAS_ARCHIVE, null));
			$className = str_replace("%LANG", $language, $config->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CAS_CLASS, null));
			$output->add($this->printCAS($codebase, $archive, $className));
		} else {
			$output->add($this->printCASContainer($config, $availableLanguages, $language));
		}
		return $output->b;
	}
	public function createCasImage($imageParameter) {
		$output = "";
		$contextPath = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CONTEXT_PATH, "/");
		if($imageParameter !== null) {
			$dataDecoded = com_wiris_plugin_impl_CasImpl::decodeBase64($imageParameter);
			$digest = haxe_Md5::encode($imageParameter);
			$store = $this->plugin->getStorageAndCache();
			$store->storeData($digest, "png", $dataDecoded->b);
			$showImagePath = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SHOWCASIMAGE_PATH, null);
			$output .= com_wiris_plugin_impl_RenderImpl::concatPath($contextPath, $showImagePath) . rawurlencode($digest . ".png");
		} else {
			$output .= com_wiris_plugin_impl_RenderImpl::concatPath($contextPath, "core/cas.png");
		}
		return $output;
	}
	public function showCasImage($f, $provider) {
		$formula = $f;
		if(StringTools::endsWith($formula, ".png")) {
			$formula = _hx_substr($formula, 0, strlen($formula) - 4);
		}
		$store = $this->plugin->getStorageAndCache();
		$data = $store->retreiveData($formula, "png");
		if($data === null) {
			$data = com_wiris_system_Storage::newResourceStorage("cas.png")->readBinary();
			if($data === null) {
				throw new HException("Missing resource cas.png");
			}
		}
		return $data;
	}
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
	static function decodeBase64($imageParameter) {
		$b = new com_wiris_system_Base64();
		$dataDecoded = $b->decodeBytes(haxe_io_Bytes::ofString($imageParameter));
		return $dataDecoded;
	}
	function __toString() { return 'com.wiris.plugin.impl.CasImpl'; }
}
