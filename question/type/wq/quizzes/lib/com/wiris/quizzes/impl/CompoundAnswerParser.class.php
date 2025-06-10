<?php

class com_wiris_quizzes_impl_CompoundAnswerParser {
	public function __construct(){}
	static function parseCompoundAnswer($math) {
		if($math === null || $math->content === null) {
			return new _hx_array(array());
		}
		if("text" === $math->type) {
			return com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswerText($math->content);
		} else {
			if("mathml" === $math->type) {
				return com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswerMathML($math->content);
			} else {
				return new _hx_array(array());
			}
		}
	}
	static function parseCompoundAnswerText($correctAnswer) {
		$answers = new _hx_array(array());
		$lines = _hx_explode("\x0A", $correctAnswer);
		$i = null;
		{
			$_g1 = 0; $_g = $lines->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$line = $lines[$i1];
				$p = _hx_index_of($line, "=", null);
				if($p !== -1) {
					$label = _hx_substr($line, 0, $p + 1);
					$value = trim(_hx_substr($line, $p + 1, null));
					$answers->push(new _hx_array(array($label, $value)));
					unset($value,$label);
				}
				unset($p,$line,$i1);
			}
		}
		return $answers;
	}
	static function parseCompoundAnswerMathML($correctAnswer) {
		$answers = new _hx_array(array());
		$newline = "<mspace linebreak=\"newline\"/>";
		$equal = "<mo>=</mo>";
		$mml = com_wiris_util_xml_MathMLUtils::convertEditor2Newlines($correctAnswer);
		$s = com_wiris_util_xml_MathMLUtils::splitRootTag($mml, "math");
		$mml = com_wiris_util_xml_MathMLUtils::stripRootTag($s[1], "mrow");
		$lines = new _hx_array(array());
		$start = 0;
		$end = 0;
		do {
			$end = _hx_index_of($mml, $newline, $start);
			$line = (($end > -1) ? _hx_substr($mml, $start, $end - $start) : _hx_substr($mml, $start, null));
			if($lines->length > 0 && _hx_index_of($line, "<mo>=</mo>", null) === -1) {
				$lastElem = $lines[$lines->length - 1] . $newline . $line;
				$lines[$lines->length - 1] = $lastElem;
				unset($lastElem);
			} else {
				$lines->push($line);
			}
			$start = $end + strlen($newline);
			unset($line);
		} while($end !== -1);
		$i = null;
		{
			$_g1 = 0; $_g = $lines->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$line = com_wiris_util_xml_MathMLUtils::stripRootTag($lines[$i1], "mrow");
				$equalIndex = _hx_index_of($line, $equal, null);
				if($equalIndex !== -1) {
					$equalIndex += strlen($equal);
					$label = $s[0] . _hx_substr($line, 0, $equalIndex) . $s[2];
					$value = _hx_substr($line, $equalIndex, null);
					$a = _hx_index_of($value, "<annotation encoding=\"text/plain\">", null);
					if($a !== -1) {
						$a = _hx_index_of($value, ">", $a) + 1;
						$b = _hx_index_of($value, "</annotation>", $a);
						$value = _hx_substr($value, $a, $b - $a);
						unset($b);
					} else {
						$value = $s[0] . $value . $s[2];
					}
					$answer = new _hx_array(array($label, $value));
					$answers->push($answer);
					unset($value,$label,$answer,$a);
				}
				unset($line,$i1,$equalIndex);
			}
		}
		return $answers;
	}
	static function compoundAnswerIsEquality($compoundAnswer) {
		if($compoundAnswer->length === 1) {
			$lhs = com_wiris_util_xml_MathMLUtils::mathMLToText($compoundAnswer[0][0]);
			return com_wiris_util_type_StringUtils::contains($lhs, "+") || com_wiris_util_type_StringUtils::contains($lhs, "*") || com_wiris_util_type_StringUtils::contains($lhs, "/") || _hx_index_of($lhs, "-", null) !== _hx_last_index_of($lhs, "-", null) || $lhs === "y=" || $lhs === "x=";
		} else {
			return false;
		}
	}
	static function joinCompoundAnswer($answers) {
		$sb = new StringBuf();
		$m = new com_wiris_quizzes_impl_MathContent();
		if($answers->length > 0) {
			$mml = com_wiris_quizzes_impl_MathContent::getMathType($answers[0][0]) === com_wiris_quizzes_impl_MathContent::$TYPE_MATHML;
			$m->type = com_wiris_quizzes_impl_CompoundAnswerParser_0($answers, $m, $mml, $sb);
			$root = "<math>";
			$i = null;
			{
				$_g1 = 0; $_g = $answers->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if($i1 !== 0) {
						$sb->add((($mml) ? "<mspace linebreak=\"newline\"/>" : "\x0A"));
					}
					$ans = $answers[$i1];
					$s = com_wiris_util_xml_MathMLUtils::splitRootTag($ans[0], "math");
					$sb->add($s[1]);
					$root = com_wiris_quizzes_impl_CompoundAnswerParser::combineTagAtts($root, $s[0]);
					$s = com_wiris_util_xml_MathMLUtils::splitRootTag($ans[1], "math");
					$sb->add($s[1]);
					$root = com_wiris_quizzes_impl_CompoundAnswerParser::combineTagAtts($root, $s[0]);
					unset($s,$i1,$ans);
				}
			}
			$m->content = $sb->b;
			if($mml) {
				$m->content = $root . $m->content . "</math>";
			}
		} else {
			$m->set("");
		}
		return $m;
	}
	static function combineTagAtts($t1, $t2) {
		$p1 = _hx_index_of($t1, " ", null);
		$p2 = _hx_index_of($t2, " ", null);
		if($p1 === -1) {
			return $t2;
		}
		if($p2 === -1) {
			return $t1;
		}
		$t1 = _hx_substr($t1, 0, strlen($t1) - 1);
		$t2 = _hx_substr($t2, 0, strlen($t2) - 1);
		$t2 = _hx_substr($t2, $p2 + 1, null);
		$atts = _hx_explode(" ", $t2);
		$i = 0;
		{
			$_g1 = 0; $_g = $atts->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(_hx_index_of($t1, $atts[$i1], null) === -1) {
					$t1 = $t1 . " " . $atts[$i1];
				}
				unset($i1);
			}
		}
		$t1 = $t1 . ">";
		return $t1;
	}
	static function getCompoundInitialContent($correctAnswer, $previousInitialContent, $mathml) {
		$content = new com_wiris_quizzes_impl_MathContent();
		$content->set($correctAnswer);
		$compoundModel = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($content);
		$content->set($previousInitialContent);
		$compoundInitialContent = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($content);
		$i = 0;
		while($i < $compoundModel->length) {
			if($i >= $compoundInitialContent->length) {
				$part = new _hx_array(array());
				$part[0] = $compoundModel[$i][0];
				$part[1] = "";
				$compoundInitialContent->push($part);
				unset($part);
			} else {
				$compoundInitialContent[$i][0] = $compoundModel[$i][0];
				if($mathml && com_wiris_quizzes_impl_MathContent::getMathType($compoundInitialContent[$i][1]) === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT) {
					$tools = new com_wiris_quizzes_impl_HTMLTools();
					$compoundInitialContent[$i][1] = $tools->textToMathML($compoundInitialContent[$i][1]);
					unset($tools);
				}
			}
			$i++;
		}
		while($i < $compoundInitialContent->length) {
			$compoundInitialContent->pop();
		}
		return com_wiris_quizzes_impl_CompoundAnswerParser::joinCompoundAnswer($compoundInitialContent)->content;
	}
	function __toString() { return 'com.wiris.quizzes.impl.CompoundAnswerParser'; }
}
function com_wiris_quizzes_impl_CompoundAnswerParser_0(&$answers, &$m, &$mml, &$sb) {
	if($mml) {
		return com_wiris_quizzes_impl_MathContent::$TYPE_MATHML;
	} else {
		return com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
	}
}
