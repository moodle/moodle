<?php

class com_wiris_quizzes_impl_HttpToQuizzesListener implements com_wiris_quizzes_impl_HttpListener{
	public function __construct($listener, $mqr, $service, $async) {
		if(!php_Boot::$skip_constructor) {
		$this->protocol = com_wiris_quizzes_impl_QuizzesServiceImpl::$PROTOCOL_REST;
		$this->listener = $listener;
		$this->service = $service;
		$this->mqr = $mqr;
		$this->async = $async;
	}}
	public function isCacheMiss($response) {
		return $this->isFault($response) && StringTools::startsWith($this->getFaultMessage($response), "CACHEMISS");
	}
	public function getFaultMessage($response) {
		if($this->protocol === com_wiris_quizzes_impl_QuizzesServiceImpl::$PROTOCOL_REST) {
			$start = _hx_index_of($response, "<fault>", null) + 7;
			$end = _hx_index_of($response, "</fault>", null);
			$msg = _hx_substr($response, $start, $end - $start);
			return com_wiris_util_xml_WXmlUtils::htmlUnescape($msg);
		}
		return $response;
	}
	public function isFault($response) {
		if($this->protocol === com_wiris_quizzes_impl_QuizzesServiceImpl::$PROTOCOL_REST) {
			return _hx_index_of($response, "<fault>", null) !== -1;
		}
		return false;
	}
	public function stripWebServiceEnvelope($data) {
		if($this->protocol === com_wiris_quizzes_impl_QuizzesServiceImpl::$PROTOCOL_REST) {
			$startTagName = "doProcessQuestionsResponse";
			$start = _hx_index_of($data, "<" . $startTagName . ">", null) + strlen($startTagName) + 2;
			$end = _hx_index_of($data, "</" . $startTagName . ">", null);
			$data = _hx_substr($data, $start, $end - $start);
		}
		return $data;
	}
	public function onError($msg) {
		throw new HException($msg);
	}
	public function onData($response) {
		if($this->isCacheMiss($response)) {
			$this->service->callService($this->mqr, false, $this->listener, $this->async);
			return;
		}
		if($this->isFault($response)) {
			throw new HException("Remote exception: " . $this->getFaultMessage($response));
		}
		$response = $this->stripWebServiceEnvelope($response);
		$res = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newMultipleResponseFromXml($response);
		$k = null;
		{
			$_g1 = 0; $_g = $res->questionResponses->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$results = _hx_array_get($res->questionResponses, $k1)->results;
				if($results !== null && $results->length > 0) {
					$last = $results[$results->length - 1];
					if(Std::is($last, _hx_qtype("com.wiris.quizzes.impl.ResultStoreQuestion"))) {
						$rsq = $last;
						_hx_array_get($this->mqr->questionRequests, $k1)->question->setId($rsq->id);
						$results->pop();
						unset($rsq);
					}
					unset($last);
				}
				unset($results,$k1);
			}
		}
		$this->listener->onResponse($res);
	}
	public $async;
	public $protocol;
	public $mqr;
	public $service;
	public $listener;
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
	function __toString() { return 'com.wiris.quizzes.impl.HttpToQuizzesListener'; }
}
