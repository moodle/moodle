<?php

class com_wiris_util_telemetry_Sender {
	public function __construct($deployment, $parameters, $id) {
		if(!php_Boot::$skip_constructor) {
		$this->id = $id;
		$this->deployment = $deployment;
		$this->parameters = $parameters;
	}}
	public function toHash() {
		$hash = new Hash();
		$hash->set(com_wiris_util_telemetry_Sender::$ID_KEY, $this->id);
		$hash->set(com_wiris_util_telemetry_Sender::$DEPLOYMENT_KEY, $this->deployment);
		if($this->parameters !== null) {
			com_wiris_util_type_HashUtils::putAll($this->parameters, $hash);
		}
		return $hash;
	}
	public function serialize() {
		return com_wiris_util_json_JSon::encode($this->toHash());
	}
	public function addParameter($key, $value) {
		$this->parameters->set($key, $value);
	}
	public function setParameters($parameters) {
		$this->parameters = $parameters;
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function getDeployment() {
		return $this->deployment;
	}
	public function getId() {
		return $this->id;
	}
	public $parameters;
	public $deployment;
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
	static $PRODUCT_VERSION_KEY = "product_version";
	static $PRODUCT_KEY_KEY = "product_key";
	static $ID_KEY = "id";
	static $DEPLOYMENT_KEY = "deployment";
	static $DOMAIN_KEY = "domain";
	static $USER_AGENT_KEY = "user_agent";
	static $OS_KEY = "os";
	static $IS_TRIAL_KEY = "is_trial";
	static $IP_KEY = "ip";
	static $MAC_ADDRESS_KEY = "mac_address";
	static $BACKEND_KEY = "backend";
	static $FRAMEWORK_KEY = "framework";
	static $PLATFORM_KEY = "platform";
	static $LANGUAGE_KEY = "language";
	static function newWithRandomId($deployment, $parameters) {
		return new com_wiris_util_telemetry_Sender($deployment, $parameters, com_wiris_system_UUIDUtils::generateV4(null, null));
	}
	function __toString() { return 'com.wiris.util.telemetry.Sender'; }
}
