<?php

class com_wiris_plugin_impl_EditorImpl implements com_wiris_plugin_api_Editor{
	public function __construct($plugin) {
		if(!php_Boot::$skip_constructor) {
		$this->plugin = $plugin;
	}}
	public function addLine($output, $s) {
		$output->add($s);
		$output->add("\x0D\x0A");
	}
	public function editor($language, $provider) {
		$output = new StringBuf();
		if($language === null || strlen($language) === 0) {
			$language = "en";
		}
		$language = strtolower($language);
		str_replace("-", "_", $language);
		$store = com_wiris_system_Storage::newResourceStorage("lang/" . $language . "/strings.js");
		if(!$store->exists()) {
			$store = com_wiris_system_Storage::newResourceStorage("lang/" . _hx_substr($language, 0, 2) . "/strings.js");
			$language = _hx_substr($language, 0, 2);
			if(!$store->exists()) {
				$language = "en";
			}
		}
		$attributes = new StringBuf();
		$attributes->add("");
		$confVal = "";
		$i = 0;
		$config = $this->plugin->getConfiguration();
		$h = com_wiris_plugin_api_ConfigurationKeys::$imageConfigPropertiesInv;
		$it = $h->keys();
		$value = null;
		while($it->hasNext()) {
			$value = $it->next();
			if($config->getProperty($value, null) !== null) {
				if($i !== 0) {
					$attributes->add(",");
				}
				$i++;
				$confVal = $config->getProperty($value, null);
				str_replace("-", "_", $confVal);
				str_replace("-", "_", $confVal);
				$attributes->add("'");
				$attributes->add(com_wiris_plugin_api_ConfigurationKeys::$imageConfigPropertiesInv->get($value));
				$attributes->add("' : '");
				$attributes->add($confVal);
				$attributes->add("'");
			}
		}
		$script = new StringBuf();
		if($i > 0) {
			$script->add("<script type=\"text/javascript\">window.wrs_attributes = {");
			$script->add($attributes);
			$script->add("};</script>");
		}
		$editorUrl = $this->plugin->getImageServiceURL("editor", false);
		$isSegure = $provider->getParameter("secure", "false") === "true";
		if(StringTools::startsWith($editorUrl, "http:") && $isSegure) {
			$editorUrl = "https:" . _hx_substr($editorUrl, 5, null);
		}
		$this->addLine($output, "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
		$this->addLine($output, "<html><head>");
		$this->addLine($output, "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\"/>");
		$this->addLine($output, $script->b);
		$this->addLine($output, "<script type=\"text/javascript\" src=\"" . $editorUrl . "?lang=" . rawurlencode($language) . "\"></script>");
		$this->addLine($output, "<script type=\"text/javascript\" src=\"../core/editor.js\"></script>");
		$this->addLine($output, "<script type=\"text/javascript\" src=\"../lang/" . rawurlencode($language) . "/strings.js\"></script>");
		$this->addLine($output, "<title>MathType</title>");
		$this->addLine($output, "<style type=\"text/css\">/*<!--*/html, body, #container { height: 100%; } body { margin: 0; }");
		$this->addLine($output, "#links { text-align: right; margin-right: 20px; } #links_rtl {text-align: left; margin-left: 20px;} #controls { float: left; } #controls_rtl {float: right;}/*-->*/</style>");
		$this->addLine($output, "</head><body topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">");
		$this->addLine($output, "<div id=\"container\"><div id=\"editorContainer\"></div><div id=\"controls\"></div>");
		$this->addLine($output, "<div id=\"links\"><a href=\"http://www.wiris.com/editor3/docs/manual/latex-support\" id=\"a_latex\" target=\"_blank\">LaTeX</a> | ");
		$this->addLine($output, "<a href=\"http://www.wiris.com/editor3/docs/manual\" target=\"_blank\" id=\"a_manual\">Manual</a></div></div></body>");
		return $output->b;
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
	function __toString() { return 'com.wiris.plugin.impl.EditorImpl'; }
}
