<?php

class com_wiris_quizzes_wrap_EmbeddedAnswersEditorWrap implements com_wiris_quizzes_api_ui_EmbeddedAnswersEditor{
	public function __construct($impl) {
		if(!php_Boot::$skip_constructor) {
		$this->impl = $impl;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function setReadOnly($readOnly) {
		throw new HException("Not implemented");
	}
	public function setStyle($key, $value) {
		throw new HException("Not implemented");
	}
	public function getElement() {
		throw new HException("Not implemented");
		return null;
	}
	public function addQuizzesFieldListener($listener) {
		throw new HException("Not implemented");
	}
	public function setValue($value) {
		throw new HException("Not implemented");
	}
	public function getValue() {
		throw new HException("Not implemented");
		return null;
	}
	public function showAnswerFieldPlainText($visible) {
		throw new HException("Not implemented");
	}
	public function showAnswerFieldPopupEditor($visible) {
		throw new HException("Not implemented");
	}
	public function showAutomatedStudentGuidance($visible) {
		throw new HException("Not implemented");
	}
	public function showAnswerFieldInlineEditor($visible) {
		throw new HException("Not implemented");
	}
	public function showGraphicSyntax($visible) {
		throw new HException("Not implemented");
	}
	public function showGradingFunction($visible) {
		throw new HException("Not implemented");
	}
	public function showAuxiliaryTextInput($visible) {
		throw new HException("Not implemented");
	}
	public function showAuxiliaryCasReplaceEditor($visible) {
		throw new HException("Not implemented");
	}
	public function showAuxiliaryCas($visible) {
		throw new HException("Not implemented");
	}
	public function showAuxiliarTextInput($visible) {
		throw new HException("Not implemented");
	}
	public function showAuxiliarCasReplaceEditor($visible) {
		throw new HException("Not implemented");
	}
	public function showAuxiliarCas($visible) {
		throw new HException("Not implemented");
	}
	public function showCorrectAnswer($visible) {
		throw new HException("Not implemented");
	}
	public function showPreviewTab($visible) {
		throw new HException("Not implemented");
	}
	public function showGradingCriteria($visible) {
		throw new HException("Not implemented");
	}
	public function showVariablesDefinition($visible) {
		throw new HException("Not implemented");
	}
	public function showVariablesTab($visible) {
		throw new HException("Not implemented");
	}
	public function showValidationTab($visible) {
		throw new HException("Not implemented");
	}
	public function showCorrectAnswerTab($visible) {
		throw new HException("Not implemented");
	}
	public function setConfiguration($configuration) {
		throw new HException("Not implemented");
	}
	public function getFieldType() {
		throw new HException("Not implemented");
		return null;
	}
	public function setFieldType($type) {
		throw new HException("Not implemented");
	}
	public function setEditableElement($element) {
		throw new HException("Not implemented");
	}
	public function newEmbeddedAuthoringElement() {
		throw new HException("Not implemented");
		return null;
	}
	public function analyzeHTML() {
		throw new HException("Not implemented");
	}
	public function filterHTML($questionText, $mode) {
		try {
			$this->wrapper->start();
			$r = $this->impl->filterHTML($questionText, $mode);
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public $wrapper;
	public $impl;
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
	function __toString() { return 'com.wiris.quizzes.wrap.EmbeddedAnswersEditorWrap'; }
}
