<?php

class com_wiris_quizzes_impl_EmbeddedAnswersEditorImpl implements com_wiris_quizzes_api_ui_EmbeddedAnswersEditor{
	public function __construct($question, $instance) {
		if(!php_Boot::$skip_constructor) {
		$this->question = $question;
		$this->instance = $instance;
	}}
	public function setReadOnly($readOnly) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAutomatedStudentGuidance($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function setStyle($key, $value) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAnswerFieldPlainText($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAnswerFieldPopupEditor($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAnswerFieldInlineEditor($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function getElement() {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function addQuizzesFieldListener($listener) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function setValue($value) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function getValue() {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function showGraphicSyntax($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showGradingFunction($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAuxiliaryTextInput($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAuxiliaryCasReplaceEditor($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAuxiliaryCas($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAuxiliarTextInput($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAuxiliarCasReplaceEditor($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showAuxiliarCas($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showCorrectAnswer($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showGradingCriteria($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showVariablesDefinition($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showPreviewTab($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showVariablesTab($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showValidationTab($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function showCorrectAnswerTab($visible) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function setConfiguration($configuration) {
	}
	public function getFieldType() {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return com_wiris_quizzes_api_ui_AuthoringFieldType::$EMBEDDED_ANSWERS_EDITOR;
	}
	public function setFieldType($type) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function setEditableElement($element) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function newEmbeddedAuthoringElement() {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function filterHTML($questionText, $mode) {
		$q = $this->question->getImpl();
		$qi = $this->instance;
		return com_wiris_quizzes_impl_EmbeddedAnswerFilter::filterHTML($questionText, $mode, $q, $qi);
	}
	public function analyzeHTML() {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public $instance;
	public $question;
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
	function __toString() { return 'com.wiris.quizzes.impl.EmbeddedAnswersEditorImpl'; }
}
