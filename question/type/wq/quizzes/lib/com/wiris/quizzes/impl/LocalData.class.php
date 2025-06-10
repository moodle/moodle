<?php

class com_wiris_quizzes_impl_LocalData extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function replaceDeprecatedNames($name) {
		if($name !== null && $name === "auxiliarTextInput") {
			return com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_AUXILIARY_TEXT_INPUT;
		}
		return $name;
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_LocalData();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_LocalData::$TAGNAME);
		$this->name = $this->replaceDeprecatedNames($s->attributeString("name", $this->name, null));
		$this->value = $s->textContent($this->value);
		$s->endTag();
	}
	public $value;
	public $name;
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
	static $TAGNAME = "data";
	static $KEY_OPENANSWER_COMPOUND_ANSWER;
	static $KEY_OPENANSWER_INPUT_FIELD;
	static $KEY_SHOW_CAS;
	static $KEY_CAS_INITIAL_SESSION;
	static $KEY_CAS_SESSION;
	static $KEY_SHOW_AUXILIARY_TEXT_INPUT;
	static $KEY_AUXILIARY_TEXT;
	static $KEY_OPENANSWER_COMPOUND_ANSWER_GRADE;
	static $KEY_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTION;
	static $KEY_OPENANSWER_GRAPH_TOOLBAR;
	static $KEY_OPENANSWER_HANDWRITING_CONSTRAINTS = "handwritingConstraints";
	static $KEY_ITEM_SEPARATOR = "itemSeparator";
	static $KEY_AUXILIARY_CAS_HIDE_FILE_MENU = "auxiliaryCasHideFileMenu";
	static $VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE;
	static $VALUE_OPENANSWER_COMPOUND_ANSWER_FALSE;
	static $VALUE_OPENANSWER_INPUT_FIELD_INLINE_EDITOR;
	static $VALUE_OPENANSWER_INPUT_FIELD_POPUP_EDITOR;
	static $VALUE_OPENANSWER_INPUT_FIELD_PLAIN_TEXT;
	static $VALUE_OPENANSWER_INPUT_FIELD_INLINE_HAND = "inlineHand";
	static $VALUE_OPENANSWER_INPUT_FIELD_INLINE_GRAPH = "inlineGraph";
	static $VALUE_OPENANSWER_INPUT_FIELD_MULTISTEP_EDITOR = "multiStepEditor";
	static $VALUE_SHOW_CAS_FALSE;
	static $VALUE_SHOW_CAS_ADD;
	static $VALUE_SHOW_CAS_REPLACE_INPUT;
	static $VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_AND;
	static $VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTE;
	static $VALUE_AUXILIARY_CAS_HIDE_FILE_MENU_TRUE = "true";
	static $VALUE_AUXILIARY_CAS_HIDE_FILE_MENU_FALSE = "false";
	static $KEY_ELEMENTS_TO_HANDWRITE = "elementsToHandwrite";
	static $KEY_GRAPH_LOCK_INITIAL_CONTENT = "graphLockInitialContent";
	static $KEY_GRAPH_SHOW_NAME_IN_LABEL = "graphShowNameInLabel";
	static $KEY_GRAPH_SHOW_VALUE_IN_LABEL = "graphShowValueInLabel";
	static $VALUE_NEVER = "never";
	static $VALUE_ALWAYS = "always";
	static $VALUE_FOCUS = "focus";
	static $KEY_GRAPH_MAGNETIC_GRID = "graphMagneticGrid";
	static $VALUE_SNAP = "snap";
	static $KEY_MULTISTEP_SESSION_ID = "multistepSessionId";
	static $keys;
	function __toString() { return 'com.wiris.quizzes.impl.LocalData'; }
}
com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER;
com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_INPUT_FIELD = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_ANSWER_FIELD_TYPE;
com_wiris_quizzes_impl_LocalData::$KEY_SHOW_CAS = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS;
com_wiris_quizzes_impl_LocalData::$KEY_CAS_INITIAL_SESSION = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_CAS_INITIAL_SESSION;
com_wiris_quizzes_impl_LocalData::$KEY_CAS_SESSION = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_CAS_SESSION;
com_wiris_quizzes_impl_LocalData::$KEY_SHOW_AUXILIARY_TEXT_INPUT = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_AUXILIARY_TEXT_INPUT;
com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_TEXT = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_AUXILIARY_TEXT;
com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE;
com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTION = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE_DISTRIBUTION;
com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_GRAPH_TOOLBAR = com_wiris_quizzes_api_QuizzesConstants::$GRAPH_TOOLBAR;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_FALSE = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_FALSE;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_INLINE_EDITOR = com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_INLINE_EDITOR;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_POPUP_EDITOR = com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_POPUP_EDITOR;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_PLAIN_TEXT = com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_TEXT;
com_wiris_quizzes_impl_LocalData::$VALUE_SHOW_CAS_FALSE = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_FALSE;
com_wiris_quizzes_impl_LocalData::$VALUE_SHOW_CAS_ADD = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_ADD;
com_wiris_quizzes_impl_LocalData::$VALUE_SHOW_CAS_REPLACE_INPUT = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_REPLACE;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_AND = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_AND;
com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTE = com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_DISTRIBUTE;
com_wiris_quizzes_impl_LocalData::$keys = new _hx_array(array(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER, com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_INPUT_FIELD, com_wiris_quizzes_impl_LocalData::$KEY_SHOW_CAS, com_wiris_quizzes_impl_LocalData::$KEY_CAS_INITIAL_SESSION, com_wiris_quizzes_impl_LocalData::$KEY_CAS_SESSION, com_wiris_quizzes_impl_LocalData::$KEY_SHOW_AUXILIARY_TEXT_INPUT, com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_TEXT, com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE, com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTION, com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_GRAPH_TOOLBAR, com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_CAS_HIDE_FILE_MENU, com_wiris_quizzes_impl_LocalData::$KEY_ELEMENTS_TO_HANDWRITE, com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_LOCK_INITIAL_CONTENT, com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_SHOW_NAME_IN_LABEL, com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_SHOW_VALUE_IN_LABEL, com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_MAGNETIC_GRID));
