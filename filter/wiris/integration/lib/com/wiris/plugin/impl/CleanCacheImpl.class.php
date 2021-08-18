<?php

class com_wiris_plugin_impl_CleanCacheImpl implements com_wiris_plugin_api_CleanCache{
	public function __construct($pb) {
		if(!php_Boot::$skip_constructor) {
		$this->plugin = $pb;
	}}
	public function isGui() {
		$wirisCacheGui = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CLEAN_CACHE_GUI, "false") === "true";
		return $wirisCacheGui && !$this->accept;
	}
	public function validateToken($md5Token, $token) {
		if($token !== null && $md5Token !== null) {
			return $md5Token === com_wiris_system_Md5Tools::encodeString($token);
		} else {
			return false;
		}
	}
	public function getContentType() {
		if(!$this->gui) {
			return "application/json";
		} else {
			return "text/html charset=UTF-8";
		}
	}
	public function getCacheOutput() {
		if($this->gui) {
			$output = "";
			$output .= "<html><head>\x0D\x0A";
			$output .= "<title>MathType integration clean cache service</title><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\x0D\x0A";
			$output .= "<link rel=stylesheet type=text/css href=" . $this->resourcePath . "?resourcefile=wirisplugin.css />";
			$output .= "</head>";
			$output .= "<div class=wirismaincontainer>";
			$output .= "<body class=wirisplugincleancache>";
			$output .= "<h2 class=wirisplugincleancache>MathType integration clean cache service</h2>\x0D\x0A";
			$output .= "<div class=wirisplugincleancacheform>";
			if($this->wirisCleanCacheToken !== null) {
				$output .= "<form action=" . $this->cleanCachePath . " method=post>";
				$output .= "<span class=wirisplugincleancachetextform> Security token </span><input type=password autocomplete=off name=token>";
				$output .= "<input type=\"submit\" value=\"Submit\">";
				$output .= "</form>";
			}
			$output .= "<form action=" . $this->cleanCachePath . " method=post>";
			$output .= "<span class=wirisplugincleancachetextform> Generate token </span> <input type=text name=newtoken>";
			$output .= "<input type=\"submit\" value=\"Submit\">";
			$output .= "</form>";
			$output .= "</div>";
			$output .= "<div class=wirisplugincleancacheresults>";
			if($this->token !== null && !$this->validToken) {
				$output .= "<span class=wirisplugincleancachewarning> Invalid Token </span>";
			} else {
				if($this->validToken && $this->token !== null) {
					$output .= "<span class=wirisplugincleancachewarning> Cache deleted successfully </span>";
				} else {
					if($this->newToken !== null) {
						$output .= " Your new token is: <br>";
						$output .= "<span class=wirisplugincleancachewarning>" . haxe_Md5::encode($this->newToken) . "</span> <br>";
						$output .= " Please copy it to your configuration.ini file <br>";
						$output .= " For more information see <a href=http://www.wiris.com/plugins/docs/resources/configuration-table style=text-decoration:none>Server configuration file documentation</a>";
					}
				}
			}
			$output .= "</div>";
			$output .= "</div>";
			return $output;
		} else {
			$jsonOutput = new Hash();
			if(!$this->validToken) {
				$jsonOutput->set("status", "error");
			} else {
				$jsonOutput->set("status", "ok");
			}
			return com_wiris_util_json_JSon::encode($jsonOutput);
		}
	}
	public function deleteCache() {
		$this->storage->deleteCache();
	}
	public function init($param) {
		$this->storage = $this->plugin->getStorageAndCache();
		$this->token = $param->getParameter("token", null);
		$this->newToken = $param->getParameter("newtoken", null);
		$this->wirisCleanCacheToken = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CLEAN_CACHE_TOKEN, null);
		$this->accept = (($param->getParameter("accept", null) !== null && $param->getParameter("accept", "") === "application/json") ? true : false);
		$this->gui = $this->isGui();
		$this->validToken = $this->validateToken($this->wirisCleanCacheToken, $this->token);
		$this->cleanCachePath = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CLEAN_CACHE_PATH, "");
		$this->resourcePath = $this->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$RESOURCE_PATH, "");
		if($this->token !== null && $this->validToken) {
			$this->deleteCache();
		}
	}
	public $resourcePath;
	public $cleanCachePath;
	public $storage;
	public $gui;
	public $accept;
	public $validToken;
	public $wirisCleanCacheToken;
	public $newToken;
	public $token;
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
	function __toString() { return 'com.wiris.plugin.impl.CleanCacheImpl'; }
}
