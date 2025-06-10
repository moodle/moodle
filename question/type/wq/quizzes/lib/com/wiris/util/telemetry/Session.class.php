<?php

class com_wiris_util_telemetry_Session {
	public function __construct($UUID) {
		if(!php_Boot::$skip_constructor) {
		if($UUID === null) {
			$this->id = com_wiris_system_UUIDUtils::generateV4(null, null);
		} else {
			$this->id = $UUID;
		}
	}}
	public function toHash() {
		$hash = new Hash();
		$hash->set(com_wiris_util_telemetry_Session::$ID_KEY, $this->id);
		$hash->set(com_wiris_util_telemetry_Session::$PAGE_KEY, _hx_string_rec($this->page, "") . "");
		return $hash;
	}
	public function serialize() {
		return com_wiris_util_json_JSon::encode($this->toHash());
	}
	public function incrementPageBy($n) {
		$this->page += $n;
	}
	public function setPage($page) {
		$this->page = $page;
	}
	public function getPage() {
		return $this->page;
	}
	public function getId() {
		return $this->id;
	}
	public $page = 0;
	public $id;
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
	static $ID_KEY = "id";
	static $PAGE_KEY = "page";
	function __toString() { return 'com.wiris.util.telemetry.Session'; }
}
