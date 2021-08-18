<?php

class com_wiris_system_service_HttpResponse {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->headers = new Hash();
	}}
	public function setReturnCode($r) {
		$code = null;
		switch($r) {
		case 100:{
			$code = "100 Continue";
		}break;
		case 101:{
			$code = "101 Switching Protocols";
		}break;
		case 200:{
			$code = "200 Continue";
		}break;
		case 201:{
			$code = "201 Created";
		}break;
		case 202:{
			$code = "202 Accepted";
		}break;
		case 203:{
			$code = "203 Non-Authoritative Information";
		}break;
		case 204:{
			$code = "204 No Content";
		}break;
		case 205:{
			$code = "205 Reset Content";
		}break;
		case 206:{
			$code = "206 Partial Content";
		}break;
		case 300:{
			$code = "300 Multiple Choices";
		}break;
		case 301:{
			$code = "301 Moved Permanently";
		}break;
		case 302:{
			$code = "302 Found";
		}break;
		case 303:{
			$code = "303 See Other";
		}break;
		case 304:{
			$code = "304 Not Modified";
		}break;
		case 305:{
			$code = "305 Use Proxy";
		}break;
		case 307:{
			$code = "307 Temporary Redirect";
		}break;
		case 400:{
			$code = "400 Bad Request";
		}break;
		case 401:{
			$code = "401 Unauthorized";
		}break;
		case 402:{
			$code = "402 Payment Required";
		}break;
		case 403:{
			$code = "403 Forbidden";
		}break;
		case 404:{
			$code = "404 Not Found";
		}break;
		case 405:{
			$code = "405 Method Not Allowed";
		}break;
		case 406:{
			$code = "406 Not Acceptable";
		}break;
		case 407:{
			$code = "407 Proxy Authentication Required";
		}break;
		case 408:{
			$code = "408 Request Timeout";
		}break;
		case 409:{
			$code = "409 Conflict";
		}break;
		case 410:{
			$code = "410 Gone";
		}break;
		case 411:{
			$code = "411 Length Required";
		}break;
		case 412:{
			$code = "412 Precondition Failed";
		}break;
		case 413:{
			$code = "413 Request Entity Too Large";
		}break;
		case 414:{
			$code = "414 Request-URI Too Long";
		}break;
		case 415:{
			$code = "415 Unsupported Media Type";
		}break;
		case 416:{
			$code = "416 Requested Range Not Satisfiable";
		}break;
		case 417:{
			$code = "417 Expectation Failed";
		}break;
		case 500:{
			$code = "500 Internal Server Error";
		}break;
		case 501:{
			$code = "501 Not Implemented";
		}break;
		case 502:{
			$code = "502 Bad Gateway";
		}break;
		case 503:{
			$code = "503 Service Unavailable";
		}break;
		case 504:{
			$code = "504 Gateway Timeout";
		}break;
		case 505:{
			$code = "505 HTTP Version Not Supported";
		}break;
		default:{
			$code = "";
		}break;
		}
		$code = "HTTP/1.1 " . $code;
		header($code, true, $r);;
	}
	public function getHeader($name) {
		return $this->headers->get($name);
	}
	public function close() {
		flush();;
	}
	public function writeString($s) {
		echo($s);
	}
	public function writeBinary($data) {
		$this->writeString($data->toString());
	}
	public function sendError($num, $message) {
		$this->setReturnCode($num);
		$this->writeString($message);
		$this->close();
	}
	public function setHeader($name, $value) {
		$this->headers->set($name, $value);
		header($name . ": " . $value);
	}
	public $headers;
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
	function __toString() { return 'com.wiris.system.service.HttpResponse'; }
}
