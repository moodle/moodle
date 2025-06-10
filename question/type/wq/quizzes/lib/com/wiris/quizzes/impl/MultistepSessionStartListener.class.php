<?php

class com_wiris_quizzes_impl_MultistepSessionStartListener implements com_wiris_quizzes_impl_HttpListener{
	public function __construct($qi) {
		if(!php_Boot::$skip_constructor) {
		$this->qi = $qi;
	}}
	public function onError($error) {
		throw new HException("Error starting multistep session " . $error);
	}
	public function onData($data) {
		$response = com_wiris_util_json_JSon::getHash(com_wiris_util_json_JSon::decode($data));
		$sessionId = com_wiris_util_json_JSon::getString($response->get("sessionId"));
		$this->qi->setProperty(com_wiris_quizzes_api_PropertyName::$MULTISTEP_SESSION_ID, $sessionId);
	}
	public $qi;
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
	function __toString() { return 'com.wiris.quizzes.impl.MultistepSessionStartListener'; }
}
