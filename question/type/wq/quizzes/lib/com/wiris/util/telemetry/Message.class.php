<?php

class com_wiris_util_telemetry_Message {
	public function __construct($topic, $parameters) {
		if(!php_Boot::$skip_constructor) {
		$this->topic = $topic;
		$this->parameters = $parameters;
		$this->timestamp = com_wiris_util_telemetry_Message_0($this, $parameters, $topic);
	}}
	public function getTimestamp() {
		return $this->timestamp;
	}
	public function getTopic() {
		return $this->topic;
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function toHash() {
		$hash = new Hash();
		$hash->set(com_wiris_util_telemetry_Message::$TOPIC_KEY, _hx_string_rec($this->topic, "") . "");
		$hash->set(com_wiris_util_telemetry_Message::$TIMESTAMP_KEY, $this->timestamp);
		if($this->parameters !== null) {
			com_wiris_util_type_HashUtils::putAll($this->parameters, $hash);
		}
		return $hash;
	}
	public function serialize() {
		return com_wiris_util_json_JSon::encode($this->toHash());
	}
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}
	public $timestamp;
	public $parameters;
	public $topic;
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
	static $TOPIC_KEY = "topic";
	static $TIMESTAMP_KEY = "timestamp";
	static function toHashArray($array) {
		$hashArray = new _hx_array(array());
		{
			$_g = 0;
			while($_g < $array->length) {
				$message = $array[$_g];
				++$_g;
				$hashArray->push($message->toHash());
				unset($message);
			}
		}
		return $hashArray;
	}
	function __toString() { return 'com.wiris.util.telemetry.Message'; }
}
function com_wiris_util_telemetry_Message_0(&$»this, &$parameters, &$topic) {
	{
		$time = (time()*1000) + (date('Z')*1000);
		$mili = _hx_mod($time, 1000);
		$dateTime = Date::fromTime($time);
		$date = $dateTime->toString();
		$formattedDate = str_replace(" ", "T", $date);
		$formattedDate .= "." . _hx_string_rec($mili, "") . "Z";
		return $formattedDate;
	}
}
