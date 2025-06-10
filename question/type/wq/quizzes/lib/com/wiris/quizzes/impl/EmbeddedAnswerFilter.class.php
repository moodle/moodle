<?php

class com_wiris_quizzes_impl_EmbeddedAnswerFilter {
	public function __construct(){}
	static function filterHTML($html, $mode, $q, $qi) {
		if($html === null || $html === "") {
			return "";
		}
		$regexp = new EReg("<(input|img|span)[^>]*(wrsUI_quizzesEmbeddedAuthoringField|wirisauthoringfield|wirisembeddedauthoringfield|wirisanswerfield)[^>]*(\\/>|>[^<]*<\\/(input|img|span)>)", "gm");
		$html = $regexp->replace($html, "<<wirisembeddedanswerfield>>");
		$i = 0;
		$start = 0;
		$pos = null;
		$sb = new StringBuf();
		while(($pos = _hx_index_of($html, "<<wirisembeddedanswerfield>>", $start)) !== -1) {
			$sb->add(_hx_substr($html, $start, $pos - $start));
			if($mode === com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode::$AUTHORING) {
				$value = $q->getCorrectAnswer($i);
				$sb->add("<input type=\"hidden\" class=\"wirisembeddedauthoringfield\" value=\"" . com_wiris_util_xml_WXmlUtils::htmlEscape($value) . "\" data-answer-index=\"" . _hx_string_rec($i, "") . "\" />");
				unset($value);
			} else {
				if($mode === com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode::$DELIVERY) {
					$sb->add("<input type=\"hidden\" class=\"wirisanswerfield wirisembedded\" value=\"\" />");
				} else {
					if($mode === com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode::$REVIEW) {
						$value = $qi->getStudentAnswer($i);
						if($value === null) {
							$value = "";
						}
						$sb->add("<input type=\"hidden\" class=\"wirisanswerfield wirisembedded wirisembeddedfeedback wirisassertionsfeedback wiriscorrectfeedback\" value=\"" . com_wiris_util_xml_WXmlUtils::htmlEscape($value) . "\" />");
						unset($value);
					}
				}
			}
			$i++;
			$start = $pos + 28;
		}
		$sb->add(_hx_substr($html, $start, null));
		return $sb->b;
	}
	function __toString() { return 'com.wiris.quizzes.impl.EmbeddedAnswerFilter'; }
}
