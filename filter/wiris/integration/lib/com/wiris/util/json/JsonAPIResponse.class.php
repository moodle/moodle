<?php

class com_wiris_util_json_JsonAPIResponse {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->result = new Hash();
		$this->errors = new _hx_array(array());
		$this->warnings = new _hx_array(array());
	}}
	public function toString() {
		return $this->getResponse();
	}
	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		if($status !== com_wiris_util_json_JsonAPIResponse::$STATUS_OK && $status !== com_wiris_util_json_JsonAPIResponse::$STATUS_WARNING && $status !== com_wiris_util_json_JsonAPIResponse::$STATUS_ERROR) {
			throw new HException("Invalid status code");
		}
		$this->status = $status;
	}
	public function addError($error) {
		$this->errors->push($error);
	}
	public function addWarning($warning) {
		$this->warnings->push($warning);
	}
	public function getResult() {
		if($this->status === com_wiris_util_json_JsonAPIResponse::$STATUS_ERROR) {
			return null;
		} else {
			return $this->result;
		}
	}
	public function setResult($obj) {
		$this->result = $obj;
	}
	public function addResult($key, $value) {
		$this->result->set($key, $value);
	}
	public function getResponse() {
		$response = new Hash();
		if($this->status === com_wiris_util_json_JsonAPIResponse::$STATUS_ERROR) {
			$response->set("errors", $this->errors);
			$response->set("status", "error");
		}
		if($this->status === com_wiris_util_json_JsonAPIResponse::$STATUS_WARNING) {
			$response->set("warnings", $this->warnings);
			$response->set("result", $this->result);
			$response->set("status", "warning");
		}
		if($this->status === com_wiris_util_json_JsonAPIResponse::$STATUS_OK) {
			$response->set("result", $this->result);
			$response->set("status", "ok");
		}
		return com_wiris_util_json_JSon::encode($response);
	}
	public $warnings;
	public $errors;
	public $result;
	public $status;
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
	static $STATUS_OK = 0;
	static $STATUS_WARNING = 1;
	static $STATUS_ERROR = -1;
	function __toString() { return $this->toString(); }
}
