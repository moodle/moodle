<?php

class com_wiris_quizzes_impl_QuizzesComponentBuilderImpl implements com_wiris_quizzes_api_ui_QuizzesComponentBuilder{
	public function __construct() { 
	}
	public function replaceFields($question, $instance, $element) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	public function newAuxiliaryCasField($instance, $slot) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function newEmbeddedAnswersEditor($question, $instance) {
		return new com_wiris_quizzes_impl_EmbeddedAnswersEditorImpl($question, $instance);
	}
	public function newAuthoringField($question, $slot, $authorAnswer) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function newAnswerField($instance, $slot) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function newAnswerFeedback($instance, $slot, $authorAnswer) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function getMathViewer() {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
		return null;
	}
	public function setLanguage($lang) {
		com_wiris_quizzes_impl_QuizzesComponentBuilderImpl::throwNotImplementedInServerTechnology();
	}
	static function throwNotImplementedInServerTechnology() {
		throw new HException("Not implemented in server technology. This method should be called from client-side.");
	}
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesComponentBuilderImpl'; }
}
