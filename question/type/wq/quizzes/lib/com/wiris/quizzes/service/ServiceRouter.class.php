<?php

class com_wiris_quizzes_service_ServiceRouter {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		if(com_wiris_quizzes_service_ServiceRouter::$router === null) {
			com_wiris_quizzes_service_ServiceRouter::$router = $this->getRouter();
		}
		if(com_wiris_quizzes_service_ServiceRouter::$serviceMimes === null) {
			com_wiris_quizzes_service_ServiceRouter::$serviceMimes = $this->getMimes();
		}
	}}
	public function sendFile($data, $res) {
		try {
			$res->setHeader("Content-Length", "" . _hx_string_rec($data->length, ""));
			$res->writeBinary($data);
			$res->close();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$t = $_ex_;
			{
				$res->sendError(500, "Unable to read file.");
			}
		}
	}
	public function getQuizzesJS($s) {
		$sb = new StringBuf();
		$js = $s->read();
		$sb->add("(function(){\x0A");
		$sb->add($js);
		$sb->add("\x0A");
		$sb->add(com_wiris_quizzes_service_ServiceTools::appendQuizzesJS());
		$sb->add("})();");
		return $sb->b;
	}
	public function sendQuizzesJS($s, $res) {
		$res->writeString($this->getQuizzesJS($s));
		$res->close();
	}
	public function service($request, $res) {
		$accessProvider = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getAccessProvider();
		if($accessProvider !== null) {
			if($accessProvider->isEnabled()) {
				if(!$accessProvider->requireAccess()) {
					$res->sendError(403, "Forbidden");
					return;
				}
			}
		}
		if($request->getParameter("service") === null) {
			$res->sendError(400, "Missing \"service\" parameter.");
			return;
		}
		$service = $request->getParameter("service");
		if($service === "resource" || $service === "cache") {
			if($request->getParameter("name") === null) {
				$res->sendError(400, "Missing \"name\" parameter.");
			}
			$name = $request->getParameter("name");
			$res->setHeader("Content-Type", com_wiris_quizzes_service_ServiceTools::getContentType($name));
			$res->setHeader("Cache-Control", "max-age=1800");
			$b = null;
			if($service === "resource") {
				$s = com_wiris_system_Storage::newResourceStorage($name);
				if($name === "quizzes.js") {
					$this->sendQuizzesJS($s, $res);
				} else {
					$b = haxe_io_Bytes::ofData($s->readBinary());
				}
			} else {
				$cache = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getImagesCache();
				$b = $cache->get($name);
			}
			if($b !== null) {
				$this->sendFile($b, $res);
			}
		} else {
			if($service === "echo") {
				if($request->getParameter("data") === null) {
					$res->sendError(400, "Missing \"data\" parameter.");
					return;
				}
				$data = $request->getParameter("data");
				if($request->getParameter("filename") !== null) {
					$filename = $request->getParameter("filename");
					$res->setHeader("Content-Type", com_wiris_quizzes_service_ServiceTools::getContentType($filename));
					$res->setHeader("Content-Disposition", "attachment; filename=\"" . $filename . "\"");
				} else {
					$res->setHeader("Content-Type", "text/plain");
				}
				$res->writeString($data);
				$res->close();
			} else {
				$url = null;
				$post = null;
				$postdata = null;
				$mime = null;
				$http = null;
				if($service === "url") {
					$url = $request->getParameter("url");
					$url = $this->allowedURL($url);
					if($url === null) {
						$res->sendError(400, "URL not allowed.");
					}
					$http = new com_wiris_quizzes_impl_MaxConnectionsHttpImpl($url, new com_wiris_quizzes_service_ServiceRouterListener($res));
					$res->setHeader("Content-Type", $this->getUrlMime($url));
					$post = false;
				} else {
					if(!com_wiris_quizzes_service_ServiceRouter::$router->exists($service)) {
						$res->sendError(400, "Service \"" . $service . "\" not found.");
						return;
					} else {
						$url = com_wiris_quizzes_service_ServiceRouter::$router->get($service);
						if($request->getParameter("path") !== null) {
							$url .= "/" . $request->getParameter("path");
						}
						$post = true;
						$rawpostdata = $request->getParameter("rawpostdata") !== null && $request->getParameter("rawpostdata") === "true";
						$http = new com_wiris_quizzes_impl_MaxConnectionsHttpImpl($url, new com_wiris_quizzes_service_ServiceRouterListener($res));
						$res->setHeader("Content-Type", com_wiris_quizzes_service_ServiceRouter::$serviceMimes->get($service));
						if($rawpostdata) {
							$postdata = $request->getParameter("postdata");
							$http->setPostData($postdata);
							$mime = (($request->getParameter("contenttype") !== null) ? $request->getParameter("contenttype") : "text/plain");
						} else {
							$mime = "application/x-www-form-urlencoded";
							$keys = $request->getParameterNames();
							$i = 0;
							{
								$_g1 = 0; $_g = $keys->length;
								while($_g1 < $_g) {
									$i1 = $_g1++;
									$key = $keys[$i1];
									if(!($key === "service") && !($key === "rawpostdata") && !($key === "path")) {
										$http->setParameter($key, $request->getParameter($key));
									}
									unset($key,$i1);
								}
							}
						}
						if(StringTools::endsWith($service, "base64")) {
							$http->setHeader("Accept", "text/plain");
						}
					}
				}
				$http->setHeader("Referer", com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL));
				$http->setHeader("Accept-Charset", "utf-8");
				if($post) {
					$http->setHeader("Content-Type", $mime . ";charset=utf-8");
				}
				$http->request($post);
			}
		}
	}
	public function getUrlMime($url) {
		$it = com_wiris_quizzes_service_ServiceRouter::$router->keys();
		while($it->hasNext()) {
			$service = $it->next();
			if(StringTools::startsWith($url, com_wiris_quizzes_service_ServiceRouter::$router->get($service))) {
				if($service === "grammar" && _hx_index_of($url, "json=true", null) !== -1) {
					return "application/json";
				} else {
					return com_wiris_quizzes_service_ServiceRouter::$serviceMimes->get($service);
				}
			}
			unset($service);
		}
		return null;
	}
	public function allowedURL($url) {
		$it = com_wiris_quizzes_service_ServiceRouter::$router->keys();
		while($it->hasNext()) {
			$routerurl = com_wiris_quizzes_service_ServiceRouter::$router->get($it->next());
			if(StringTools::startsWith($routerurl, "https://") && StringTools::startsWith($url, "http://")) {
				$url = "https://" . _hx_substr($url, 7, null);
			} else {
				if(StringTools::startsWith($routerurl, "http://") && StringTools::startsWith($url, "https://")) {
					$url = "http://" . _hx_substr($url, 8, null);
				}
			}
			if(StringTools::startsWith($url, $routerurl)) {
				return $url;
			}
			unset($routerurl);
		}
		return null;
	}
	public function getMimes() {
		$mimes = new Hash();
		$mimes->set("render", "image/png");
		$mimes->set("plot.png", "image/png");
		$mimes->set("plot.png.base64", "text/plain");
		$mimes->set("quizzes", "application/xml");
		$mimes->set("grammar", "text/plain");
		$mimes->set("wirislauncher", "application/json");
		$mimes->set("mathml2accessible", "text/plain");
		$mimes->set("api", "application/json");
		return $mimes;
	}
	public function getRouter() {
		$cfg = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration();
		$router = new Hash();
		$router->set("render", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL) . "/render");
		$router->set("quizzes", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL) . "/rest");
		$router->set("grammar", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL) . "/grammar");
		$router->set("wirislauncher", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$WIRISLAUNCHER_URL));
		$router->set("mathml2accessible", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$EDITOR_URL) . "/mathml2accessible");
		$router->set("plot.png", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_URL) . "/plot.png");
		$router->set("plot.png.base64", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$GRAPH_URL) . "/plot.png");
		$router->set("api", $cfg->get(com_wiris_quizzes_api_ConfigurationKeys::$API_URL));
		return $router;
	}
	static $router = null;
	static $serviceMimes = null;
	static $MAX_UPLOAD_SIZE = 1048576;
	function __toString() { return 'com.wiris.quizzes.service.ServiceRouter'; }
}
