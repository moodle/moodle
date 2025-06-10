<?php

class com_wiris_util_telemetry_Tracker {
	public function __construct($service) {
		if(!php_Boot::$skip_constructor) {
		$this->service = $service;
	}}
	public function filterParameters($parameters, $allowedParameters) {
		if($parameters !== null && $allowedParameters !== null) {
			$keys = $parameters->keys();
			while($keys->hasNext()) {
				$key = $keys->next();
				if($allowedParameters->get($key) !== null) {
					$value = $parameters->get($key);
					$allowedValues = $allowedParameters->get($key);
					$defaultValue = $allowedValues[0];
					if(!com_wiris_util_type_Arrays::containsArray($allowedValues, $value)) {
						$parameters->set($key, $defaultValue);
					}
					unset($value,$defaultValue,$allowedValues);
				} else {
					$parameters->remove($key);
				}
				unset($key);
			}
		}
	}
	public function sendInformationImpl($topic, $parameters) {
		$message = new com_wiris_util_telemetry_Message($topic, $parameters);
		$this->service->sendMessage($message);
	}
	public $service;
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
	function __toString() { return 'com.wiris.util.telemetry.Tracker'; }
}
