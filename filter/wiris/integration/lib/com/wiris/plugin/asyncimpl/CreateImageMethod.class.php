<?php

class com_wiris_plugin_asyncimpl_CreateImageMethod {
	public function __construct($render, $mml, $param, &$output, $call) {
		if(!php_Boot::$skip_constructor) {
		$this->output = $output;
		$this->step1_getMetrics_ = com_wiris_plugin_asyncimpl_CallbackImpl::newBytes($this, "step1_getMetrics", "step1_getMetrics_error");
		$this->step2_getAccessibility_step2_ = com_wiris_plugin_asyncimpl_CallbackImpl::newString($this, "step2_getAccessibility_step2", "step2_getAccessibility_step2_error");
		if($mml === null) {
			throw new HException("Missing parameter 'mml'.");
		}
		$this->render = $render;
		$this->digest = $render->render->computeDigest($mml, $param);
		$this->metrics = "";
		$this->accessibility = "";
		$this->mml = $mml;
		$this->param = $param;
		$this->call = $call;
		$this->step1();
	}}
	public function step3() {
		$contextPath = $this->render->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$CONTEXT_PATH, "/");
		$showImagePath = $this->render->plugin->getConfiguration()->getProperty(com_wiris_plugin_api_ConfigurationKeys::$SHOWIMAGE_PATH, null);
		$this->call->returnString(com_wiris_plugin_impl_RenderImpl::concatPath($contextPath, $showImagePath) . rawurlencode($this->digest) . $this->metrics . $this->accessibility);
	}
	public function step2_getAccessibility_step2_error($msg) {
		$this->step3();
	}
	public function step2_getAccessibility_step2($text) {
		if($this->output === null) {
			$this->accessibility = "&text=" . rawurlencode($text);
		} else {
			$this->output["alt"] = $text;
		}
		$this->step3();
	}
	public function step2_getAccessibility_step1() {
		$lang = com_wiris_system_PropertiesTools::getProperty($this->param, "lang", "en");
		try {
			$this->render->plugin->newAsyncTextService()->mathml2accessible($this->mml, $lang, $this->param, $this->step2_getAccessibility_step2_);
		}catch(Exception $�e) {
			$_ex_ = ($�e instanceof HException) ? $�e->e : $�e;
			$ex = $_ex_;
			{
				$this->step3();
			}
		}
	}
	public function step2() {
		if($this->param !== null && com_wiris_system_PropertiesTools::getProperty($this->param, "accessible", "false") === "true") {
			$this->step2_getAccessibility_step1();
		} else {
			$this->step3();
		}
	}
	public function step1_getMetrics_error($msg) {
		$this->step2();
	}
	public function step1_getMetrics($bs) {
		$this->metrics = $this->render->render->getMetricsFromBytes($bs, $this->output);
		$this->step2();
	}
	public function step1() {
		if($this->param !== null && com_wiris_system_PropertiesTools::getProperty($this->param, "metrics", "false") === "true") {
			$this->render->showImage($this->digest, null, null, $this->step1_getMetrics_);
		} else {
			$this->step2();
		}
	}
	public $step2_getAccessibility_step2_;
	public $step1_getMetrics_;
	public $output;
	public $param;
	public $mml;
	public $render;
	public $accessibility;
	public $metrics;
	public $call;
	public $digest;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->�dynamics[$m]) && is_callable($this->�dynamics[$m]))
			return call_user_func_array($this->�dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call �'.$m.'�');
	}
	function __toString() { return 'com.wiris.plugin.asyncimpl.CreateImageMethod'; }
}
