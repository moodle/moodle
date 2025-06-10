<?php

class com_wiris_quizzes_service_ServiceRouterListener implements com_wiris_quizzes_impl_HttpListener{
	public function __construct($res) {
		if(!php_Boot::$skip_constructor) {
		$this->res = $res;
	}}
	public function onError($error) {
		$this->res->sendError(500, $error);
	}
	public function onData($data) {
		$type = $this->res->getHeader("Content-Type");
		if($type !== null && (StringTools::startsWith($type, "image/") || $type === "application/octet-stream")) {
			$b = haxe_io_Bytes::ofString($data);
			$this->res->writeBinary($b);
		} else {
			$this->res->writeString($data);
		}
		$this->res->close();
	}
	public $mime;
	public $res;
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
	function __toString() { return 'com.wiris.quizzes.service.ServiceRouterListener'; }
}
