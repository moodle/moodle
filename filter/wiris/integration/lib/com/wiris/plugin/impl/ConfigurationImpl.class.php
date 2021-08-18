<?php

class com_wiris_plugin_impl_ConfigurationImpl implements com_wiris_plugin_api_Configuration{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->props = array();;
	}}
	public function setConfigurations($configurationKeys, $configurationValues) {
		$configurationKeysArray = _hx_explode(",", $configurationKeys);
		$configurationValuesArray = _hx_explode(",", $configurationValues);
		$keysIterator = $configurationKeysArray->iterator();
		$valuesIterator = $configurationValuesArray->iterator();
		while($keysIterator->hasNext() && $valuesIterator->hasNext()) {
			$key = $keysIterator->next();
			$value = $valuesIterator->next();
			if($this->getProperty($key, null) !== null) {
				$this->setProperty($key, $value);
			}
			unset($value,$key);
		}
	}
	public function getJsonConfiguration($configurationKeys) {
		$configurationKeysArray = _hx_explode(",", $configurationKeys);
		$iterator = $configurationKeysArray->iterator();
		$jsonOutput = new Hash();
		$jsonVariables = new Hash();
		$thereIsNullValue = false;
		while($iterator->hasNext()) {
			$key = $iterator->next();
			$value = $this->getProperty($key, "null");
			if($value === "null") {
				$thereIsNullValue = true;
			}
			$jsonVariables->set($key, $value);
			unset($value,$key);
		}
		if(!$thereIsNullValue) {
			$jsonOutput->set("status", "ok");
		} else {
			$jsonOutput->set("status", "warning");
		}
		$jsonOutput->set("result", $jsonVariables);
		return com_wiris_util_json_JSon::encode($jsonOutput);
	}
	public function getJavaScriptConfigurationJson() {
		$javaScriptHash = $this->getJavaScriptHash();
		return com_wiris_util_json_JSon::encode($javaScriptHash);
	}
	public function getJavaScriptHash() {
		$javaScriptHash = new Hash();
		$javaScriptHash->set("editorEnabled", $this->getProperty("wiriseditorenabled", null) === "true");
		$javaScriptHash->set("imageMathmlAttribute", $this->getProperty("wiriseditormathmlattribute", null));
		$javaScriptHash->set("saveMode", $this->getProperty("wiriseditorsavemode", null));
		$javaScriptHash->set("base64savemode", $this->getProperty("wiriseditorbase64savemode", null));
		$javaScriptHash->set("saveHandTraces", $this->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SAVE_MATHML_SEMANTICS, null) === "true");
		$parseLatexElements = new _hx_array(array());
		if($this->getProperty("wiriseditorparselatex", null) === "true") {
			$parseLatexElements->push("latex");
		}
		if($this->getProperty("wiriseditorparsexml", null) === "true") {
			$parseLatexElements->push("xml");
		}
		$javaScriptHash->set("parseModes", $parseLatexElements);
		$javaScriptHash->set("editorAttributes", $this->getProperty("wiriseditorwindowattributes", null));
		$javaScriptHash->set("editorUrl", $this->plugin->getImageServiceURL("editor", false));
		$javaScriptHash->set("modalWindow", $this->getProperty("wiriseditormodalwindow", null) === "true");
		$javaScriptHash->set("modalWindowFullScreen", $this->getProperty("wiriseditormodalwindowfullscreen", null) === "true");
		$javaScriptHash->set("CASEnabled", $this->getProperty("wiriscasenabled", null) === "true");
		$javaScriptHash->set("CASMathmlAttribute", $this->getProperty("wiriscasmathmlattribute", null));
		$javaScriptHash->set("CASAttributes", $this->getProperty("wiriscaswindowattributes", null));
		$javaScriptHash->set("hostPlatform", $this->getProperty("wirishostplatform", null));
		$javaScriptHash->set("versionPlatform", $this->getProperty("wirisversionplatform", "unknown"));
		$javaScriptHash->set("enableAccessibility", $this->getProperty("wirisaccessibilityenabled", null) === "true");
		$javaScriptHash->set("editorToolbar", $this->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_TOOLBAR, null));
		$javaScriptHash->set("chemEnabled", $this->getProperty("wirischemeditorenabled", null) === "true");
		$javaScriptHash->set("imageFormat", $this->getProperty("wirisimageformat", "png"));
		if($this->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMS, null) !== null) {
			$javaScriptHash->set("editorParameters", com_wiris_util_json_JSon::decode($this->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMS, null)));
		} else {
			$h = com_wiris_plugin_api_ConfigurationKeys::$imageConfigPropertiesInv;
			$attributes = new Hash();
			$confVal = "";
			$i = 0;
			$it = $h->keys();
			$value = null;
			while($it->hasNext()) {
				$value = $it->next();
				if($this->getProperty($value, null) !== null) {
					$confVal = $this->getProperty($value, null);
					str_replace("-", "_", $confVal);
					str_replace("-", "_", $confVal);
					$attributes->set($confVal, $value);
				}
			}
			$javaScriptHash->set("editorParameters", $attributes);
		}
		$javaScriptHash->set("wirisPluginPerformance", $this->getProperty("wirispluginperformance", null) === "true");
		$version = null;
		try {
			$version = com_wiris_system_Storage::newResourceStorage("VERSION")->read();
			if($version === null) {
				$version = "Missing version";
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$ex = $_ex_;
			{
				$version = "Missing version";
			}
		}
		$javaScriptHash->set("version", $version);
		return $javaScriptHash;
	}
	public function appendElement2JavascriptArray($array, $value) {
		$arrayOpen = _hx_index_of($array, "[", null);
		$arrayClose = _hx_index_of($array, "]", null);
		if($arrayOpen === -1 || $arrayClose === -1) {
			throw new HException("Array not valid");
		}
		return "[" . "'" . $value . "'" . (com_wiris_plugin_impl_ConfigurationImpl_0($this, $array, $arrayClose, $arrayOpen, $value));
	}
	public function appendVarJs($sb, $varName, $value, $comment) {
		$sb->add("var ");
		$sb->add($varName);
		$sb->add(" = ");
		$sb->add($value);
		$sb->add(";");
		if($comment !== null && strlen($comment) > 0) {
			$sb->add("// ");
			$sb->add($comment);
		}
		$sb->add("\x0D\x0A");
	}
	public function setPluginBuilderImpl($plugin) {
		$this->plugin = $plugin;
	}
	public function initialize($cu) {
		$cu->init($this->initObject);
	}
	public function initialize0() {
		if($this->initialized) {
			return;
		}
		$this->initialized = true;
		$this->plugin->addConfigurationUpdater(new com_wiris_plugin_impl_FileConfigurationUpdater());
		$this->plugin->addConfigurationUpdater(new com_wiris_plugin_impl_CustomConfigurationUpdater($this));
		$a = $this->plugin->getConfigurationUpdaterChain();
		$iter = $a->iterator();
		while($iter->hasNext()) {
			$cu = $iter->next();
			$this->initialize($cu);
			$cu->updateConfiguration($this->props);
			unset($cu);
		}
	}
	public function setInitObject($context) {
		$this->initObject = $context;
	}
	public function setProperty($key, $value) {
		$this->props[$key] = $value;
	}
	public function getProperty($key, $dflt) {
		$this->initialize0();
		return com_wiris_system_PropertiesTools::getProperty($this->props, $key, $dflt);
	}
	public function getFullConfiguration() {
		$this->initialize0();
		return $this->props;
	}
	public $initialized;
	public $props;
	public $initObject;
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
	function __toString() { return 'com.wiris.plugin.impl.ConfigurationImpl'; }
}
function com_wiris_plugin_impl_ConfigurationImpl_0(&$»this, &$array, &$arrayClose, &$arrayOpen, &$value) {
	if(strlen($array) === 2) {
		return "]";
	} else {
		return "," . _hx_substr($array, $arrayOpen + 1, $arrayClose - $arrayOpen);
	}
}
