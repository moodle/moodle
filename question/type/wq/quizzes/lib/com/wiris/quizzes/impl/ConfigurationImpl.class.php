<?php

class com_wiris_quizzes_impl_ConfigurationImpl implements com_wiris_quizzes_api_Configuration{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->properties = new Hash();
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$WIRIS_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_WIRIS_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$CALC_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CALC_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_EDITOR_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HAND_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HAND_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_SERVICE_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$API_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_API_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_PROXY_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$CACHE_DIR, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CACHE_DIR);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$MAXCONNECTIONS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_MAXCONNECTIONS);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$CONFIG_FILE, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CONFIG_FILE);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$CONFIG_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CONFIG_CLASS);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$CONFIG_CLASSPATH, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CONFIG_CLASSPATH);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$IMAGESCACHE_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_IMAGESCACHE_CLASS);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$VARIABLESCACHE_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_VARIABLESCACHE_CLASS);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$LOCKPROVIDER_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_LOCKPROVIDER_CLASS);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$ACCESSPROVIDER_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_ACCESSPROVIDER_CLASS);
		$this->set(com_wiris_quizzes_impl_ConfigurationImpl::$ACCESSPROVIDER_CLASSPATH, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_ACCESSPROVIDER_CLASSPATH);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_HOST, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HTTPPROXY_HOST);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_PORT, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HTTPPROXY_PORT);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_USER, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HTTPPROXY_USER);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_PASS, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HTTPPROXY_PASS);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_REFERER_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HAND_ENABLED, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HAND_ENABLED);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$CALC_ENABLED, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CALC_ENABLED);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_OFFLINE, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_SERVICE_OFFLINE);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$HAND_LOGTRACES, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_HAND_LOGTRACES);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$WIRISLAUNCHER_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_WIRISLAUNCHER_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$CROSSORIGINCALLS_ENABLED, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_CROSSORIGINCALLS_ENABLED);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_STATIC, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_RESOURCES_STATIC);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_GRAPH_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$VERSION, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_VERSION);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$DEPLOYMENT_ID, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_DEPLOYMENT_ID);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$LICENSE_ID, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_LICENSE_ID);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$TELEMETRY_URL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_TELEMETRY_URL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$TELEMETRY_TOKEN, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_TELEMETRY_TOKEN);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$QUIZZES_LOGGING_LEVEL, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_QUIZZES_LOGGING_LEVEL);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$QUIZZES_TRACKING_ENABLED, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_QUIZZES_TRACKING_ENABLED);
		$this->set(com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_TRACK_INSTANCES, com_wiris_quizzes_impl_ConfigurationImpl::$DEF_GRAPH_TRACK_INSTANCES);
		if(!com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT) {
			try {
				$s = com_wiris_system_Storage::newStorage(com_wiris_quizzes_impl_ConfigurationImpl::$DEF_DIST_CONFIG_FILE);
				if(!$s->exists()) {
					$s = com_wiris_system_Storage::newResourceStorage(com_wiris_quizzes_impl_ConfigurationImpl::$DEF_DIST_CONFIG_FILE);
				}
				$content = $s->read();
				$ini = com_wiris_util_sys_IniFile::newIniFileFromString($content);
				$this->setAll($ini->getProperties());
			}catch(Exception $»e) {
				$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
				$e = $_ex_;
				{
					throw new HException("Could not read the configuration file \"" . com_wiris_quizzes_impl_ConfigurationImpl::$DEF_DIST_CONFIG_FILE . "\".");
				}
			}
			$classpath = $this->get(com_wiris_quizzes_impl_ConfigurationImpl::$CONFIG_CLASSPATH);
			if(!($classpath === "")) {
				com_wiris_quizzes_impl_ClasspathLoader::load($classpath);
			}
			$className = $this->get(com_wiris_quizzes_impl_ConfigurationImpl::$CONFIG_CLASS);
			if(!($className === "")) {
				try {
					$config = Type::createInstance(Type::resolveClass($className), new _hx_array(array()));
					$keys = new _hx_array(array(com_wiris_quizzes_api_ConfigurationKeys::$WIRIS_URL, com_wiris_quizzes_api_ConfigurationKeys::$WIRISLAUNCHER_URL, com_wiris_quizzes_api_ConfigurationKeys::$CALC_URL, com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL, com_wiris_quizzes_api_ConfigurationKeys::$HAND_URL, com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL, com_wiris_quizzes_api_ConfigurationKeys::$API_URL, com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL, com_wiris_quizzes_api_ConfigurationKeys::$CACHE_DIR, com_wiris_quizzes_api_ConfigurationKeys::$MAXCONNECTIONS, com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_HOST, com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_PORT, com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_USER, com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_PASS, com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL, com_wiris_quizzes_api_ConfigurationKeys::$HAND_ENABLED, com_wiris_quizzes_api_ConfigurationKeys::$CALC_ENABLED, com_wiris_quizzes_api_ConfigurationKeys::$HAND_LOGTRACES, com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_OFFLINE, com_wiris_quizzes_api_ConfigurationKeys::$CROSSORIGINCALLS_ENABLED, com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_STATIC, com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_URL, com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_URL, com_wiris_quizzes_api_ConfigurationKeys::$DEPLOYMENT_ID, com_wiris_quizzes_api_ConfigurationKeys::$LICENSE_ID, com_wiris_quizzes_impl_ConfigurationImpl::$IMAGESCACHE_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$VARIABLESCACHE_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$LOCKPROVIDER_CLASS, com_wiris_quizzes_impl_ConfigurationImpl::$ACCESSPROVIDER_CLASS));
					$i = null;
					{
						$_g1 = 0; $_g = $keys->length;
						while($_g1 < $_g) {
							$i1 = $_g1++;
							$value = $config->get($keys[$i1]);
							if($value !== null) {
								$this->set($keys[$i1], $value);
							}
							unset($value,$i1);
						}
					}
				}catch(Exception $»e) {
					$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
					$e2 = $_ex_;
					{
						throw new HException("Could not find the Configuration class \"" . $className . "\".");
					}
				}
			}
			$file = $this->properties->get(com_wiris_quizzes_impl_ConfigurationImpl::$CONFIG_FILE);
			if(com_wiris_system_Storage::newStorage($file)->exists() || com_wiris_system_Storage::newResourceStorage($file)->exists()) {
				try {
					$ini = com_wiris_util_sys_IniFile::newIniFileFromFilename($file);
					$this->setAll($ini->getProperties());
				}catch(Exception $»e) {
					$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
					$e2 = $_ex_;
					{
						throw new HException("Could not read configuration file \"" . $file . "\".");
					}
				}
			}
			$vs = com_wiris_system_Storage::newResourceStorage(com_wiris_quizzes_impl_ConfigurationImpl::$DEF_VERSION_FILE);
			if($vs->exists()) {
				$version = $vs->read();
				$this->set(com_wiris_quizzes_api_ConfigurationKeys::$VERSION, $version);
			}
		}
	}}
	public function setHttps($url) {
		$newUrl = $url;
		if(StringTools::startsWith($url, "http://")) {
			$newUrl = "https://" . _hx_substr($url, 7, null);
		}
		return $newUrl;
	}
	public function jsEscape($text) {
		$text = str_replace("\\", "\\\\", $text);
		$text = str_replace("\"", "\\\"", $text);
		$text = str_replace("\x0A", "\\n", $text);
		$text = str_replace("\x0D", "\\r", $text);
		$text = str_replace("\x09", "\\t", $text);
		return $text;
	}
	public function getJSConfig() {
		$sb = new StringBuf();
		$prefix = "com.wiris.quizzes.impl.ConfigurationImpl.";
		$sb->add($prefix . "DEF_WIRIS_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$WIRIS_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_CALC_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$CALC_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_EDITOR_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_HAND_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$HAND_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_SERVICE_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_API_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$API_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_PROXY_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_CACHE_DIR" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$CACHE_DIR)) . "\";\x0A");
		$sb->add($prefix . "DEF_MAXCONNECTIONS" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$MAXCONNECTIONS)) . "\";\x0A");
		$sb->add($prefix . "DEF_HAND_ENABLED" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$HAND_ENABLED)) . "\";\x0A");
		$sb->add($prefix . "DEF_CALC_ENABLED" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$CALC_ENABLED)) . "\";\x0A");
		$sb->add($prefix . "DEF_SERVICE_OFFLINE" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_OFFLINE)) . "\";\x0A");
		$sb->add($prefix . "DEF_WIRISLAUNCHER_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$WIRISLAUNCHER_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_CROSSORIGINCALLS_ENABLED" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$CROSSORIGINCALLS_ENABLED)) . "\";\x0A");
		$sb->add($prefix . "DEF_RESOURCES_STATIC" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_STATIC)) . "\";\x0A");
		$sb->add($prefix . "DEF_HAND_LOGTRACES" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$HAND_LOGTRACES)) . "\";\x0A");
		$sb->add($prefix . "DEF_GRAPH_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_VERSION" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$VERSION)) . "\";\x0A");
		$sb->add($prefix . "DEF_DEPLOYMENT_ID" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$DEPLOYMENT_ID)) . "\";\x0A");
		$sb->add($prefix . "DEF_LICENSE_ID" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$LICENSE_ID)) . "\";\x0A");
		$sb->add($prefix . "DEF_TELEMETRY_URL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$TELEMETRY_URL)) . "\";\x0A");
		$sb->add($prefix . "DEF_TELEMETRY_TOKEN" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$TELEMETRY_TOKEN)) . "\";\x0A");
		$sb->add($prefix . "DEF_QUIZZES_LOGGING_LEVEL" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$QUIZZES_LOGGING_LEVEL)) . "\";\x0A");
		$sb->add($prefix . "DEF_QUIZZES_TRACKING_ENABLED" . " = \"" . $this->jsEscape($this->get(com_wiris_quizzes_api_ConfigurationKeys::$QUIZZES_TRACKING_ENABLED)) . "\";\x0A");
		return $sb->b;
	}
	public function set($key, $value) {
		if(com_wiris_quizzes_impl_ConfigurationImpl::$isHttps) {
			$urls = com_wiris_quizzes_impl_ConfigurationImpl::getUrlKeys();
			if(com_wiris_util_type_Arrays::containsArray($urls, $key)) {
				$value = $this->setHttps($value);
			}
		}
		$this->properties->set($key, $value);
	}
	public function get($key) {
		return $this->properties->get($key);
	}
	public function setAll($props) {
		$it = $props->keys();
		while($it->hasNext()) {
			$key = $it->next();
			$this->set($key, $props->get($key));
			unset($key);
		}
	}
	public function loadFile($file) {
		$ini = com_wiris_util_sys_IniFile::newIniFileFromFilename($file);
		$this->setAll($ini->getProperties());
	}
	public function load($text) {
		$ini = com_wiris_util_sys_IniFile::newIniFileFromString($text);
		$this->setAll($ini->getProperties());
	}
	public $properties;
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
	static $CONFIG_FILE = "quizzes.configuration.file";
	static $DEF_CONFIG_FILE = "configuration.ini";
	static $DEF_DIST_CONFIG_FILE = "integration.ini";
	static $DEF_VERSION_FILE = "version.txt";
	static $CONFIG_CLASS = "quizzes.configuration.class";
	static $DEF_CONFIG_CLASS = "";
	static $CONFIG_CLASSPATH = "quizzes.configuration.classpath";
	static $DEF_CONFIG_CLASSPATH = "";
	static $IMAGESCACHE_CLASS = "quizzes.imagescache.class";
	static $DEF_IMAGESCACHE_CLASS = "";
	static $VARIABLESCACHE_CLASS = "quizzes.variablescache.class";
	static $DEF_VARIABLESCACHE_CLASS = "";
	static $LOCKPROVIDER_CLASS = "quizzes.lockprovider.class";
	static $DEF_LOCKPROVIDER_CLASS = "";
	static $ACCESSPROVIDER_CLASS = "quizzes.accessprovider.class";
	static $DEF_ACCESSPROVIDER_CLASS = "";
	static $ACCESSPROVIDER_CLASSPATH = "quizzes.accessprovider.classpath";
	static $DEF_ACCESSPROVIDER_CLASSPATH = "";
	static $DEF_WIRIS_URL = "http://www.wiris.net/demo/wiris";
	static $DEF_WIRISLAUNCHER_URL = "http://stateful.wiris.net/demo/wiris";
	static $DEF_CALC_URL = "https://calcme.com";
	static $DEF_CALC_ENABLED = "true";
	static $DEF_EDITOR_URL = "http://www.wiris.net/demo/editor";
	static $DEF_HAND_URL = "http://www.wiris.net/demo/hand";
	static $DEF_SERVICE_URL = "http://www.wiris.net/demo/quizzes";
	static $DEF_API_URL = "http://www.wiris.net/demo/quizzes/api";
	static $DEF_PROXY_URL = "quizzes/service";
	static $DEF_CACHE_DIR = "/var/wiris/cache";
	static $DEF_MAXCONNECTIONS = "20";
	static $DEF_HTTPPROXY_HOST = "";
	static $DEF_HTTPPROXY_PORT = "8080";
	static $DEF_HTTPPROXY_USER = "";
	static $DEF_HTTPPROXY_PASS = "";
	static $DEF_REFERER_URL = "";
	static $DEF_HAND_ENABLED = "true";
	static $DEF_HAND_LOGTRACES = "false";
	static $DEF_SERVICE_OFFLINE = "false";
	static $DEF_CROSSORIGINCALLS_ENABLED = "false";
	static $DEF_RESOURCES_STATIC = "false";
	static $DEF_RESOURCES_URL = "quizzes/resources";
	static $DEF_GRAPH_URL = "http://www.wiris.net/demo/graph";
	static $DEF_VERSION = "";
	static $DEF_DEPLOYMENT_ID = "quizzes-unknown";
	static $DEF_LICENSE_ID = "";
	static $DEF_TELEMETRY_URL = "https://telemetry.wiris.net";
	static $DEF_TELEMETRY_TOKEN = "1lt1OnlX3898VauysJ1nr5ODR8CNfVmB80KGxSSt";
	static $DEF_QUIZZES_LOGGING_LEVEL = "WARNING";
	static $DEF_QUIZZES_TRACKING_ENABLED = "true";
	static $DEF_GRAPH_TRACK_INSTANCES = "true";
	static $config = null;
	static function thisLock() { $»args = func_get_args(); return call_user_func_array(self::$thisLock, $»args); }
	static $thisLock;
	static $isHttps = false;
	static function getInstance() {
		if(com_wiris_quizzes_impl_ConfigurationImpl::$config === null) {
			com_wiris_quizzes_impl_ConfigurationImpl::$config = new com_wiris_quizzes_impl_ConfigurationImpl();
		}
		return com_wiris_quizzes_impl_ConfigurationImpl::$config;
	}
	static function getUrlKeys() {
		$urls = new _hx_array(array(com_wiris_quizzes_api_ConfigurationKeys::$WIRIS_URL, com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL, com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL, com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL, com_wiris_quizzes_api_ConfigurationKeys::$HAND_URL, com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_URL, com_wiris_quizzes_api_ConfigurationKeys::$WIRISLAUNCHER_URL, com_wiris_quizzes_api_ConfigurationKeys::$CALC_URL, com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_URL));
		return $urls;
	}
	static function setIsHttps($https) {
		com_wiris_quizzes_impl_ConfigurationImpl::$isHttps = $https;
	}
	function __toString() { return 'com.wiris.quizzes.impl.ConfigurationImpl'; }
}
com_wiris_quizzes_impl_ConfigurationImpl::$thisLock = _hx_anonymous(array());
