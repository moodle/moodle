<?php

class com_wiris_util_type_StringUtils {
	public function __construct(){}
	static function stripAccents($s) {
		$sb = new StringBuf();
		$i = com_wiris_system_Utf8::getIterator($s);
		while($i->hasNext()) {
			$sb->add(com_wiris_util_xml_WCharacterBase::stripAccent($i->next()));
		}
		return $sb->b;
	}
	static function padLeftZeros($s, $length) {
		if($s === null || strlen($s) >= $length) {
			return $s;
		}
		$stringBuf = new StringBuf();
		{
			$_g = strlen($s);
			while($_g < $length) {
				$i = $_g++;
				$stringBuf->add("0");
				unset($i);
			}
		}
		$stringBuf->add($s);
		return $stringBuf->b;
	}
	static function compareIgnoringAccents($a, $b) {
		return com_wiris_util_type_StringUtils::stripAccents($a) === com_wiris_util_type_StringUtils::stripAccents($b);
	}
	static function contains($a, $b) {
		return _hx_index_of($a, $b, null) !== -1;
	}
	static function slice($s, $beginIndex, $endIndex) {
		$stringLength = strlen($s);
		if($beginIndex < 0) {
			$beginIndex = com_wiris_util_type_IntegerTools::max(0, $stringLength + $beginIndex);
		} else {
			if($beginIndex > $stringLength) {
				$beginIndex = $stringLength;
			}
		}
		if($endIndex < 0) {
			$endIndex = com_wiris_util_type_IntegerTools::max(0, strlen($s) + $endIndex);
		} else {
			if($endIndex > $stringLength) {
				$endIndex = $stringLength;
			}
		}
		$span = com_wiris_util_type_IntegerTools::max(0, $endIndex - $beginIndex);
		return _hx_substr($s, $beginIndex, $span);
	}
	static function compareVersions($a, $b) {
		$va = _hx_explode(".", $a);
		$vb = _hx_explode(".", $b);
		$i = 0;
		while($i < $va->length) {
			$na = Std::parseInt($va[$i]);
			$nb = 0;
			if($i < $vb->length) {
				$nb = Std::parseInt($vb[$i]);
			}
			$n = $na - $nb;
			if($n !== 0) {
				return $n;
			}
			$i++;
			unset($nb,$na,$n);
		}
		while($i < $vb->length) {
			$nb = Std::parseInt($vb[$i]);
			if($nb !== 0) {
				return -$nb;
			}
			$i++;
			unset($nb);
		}
		return 0;
	}
	static function append($a, $b) {
		return $a . $b;
	}
	static function replaceFirstOccurrence($s, $target, $replacement) {
		if($s === null || $s === "") {
			return $s;
		}
		return com_wiris_util_type_StringUtils::replaceOccurrenceImpl($s, $target, $replacement, _hx_index_of($s, $target, null));
	}
	static function replaceLastOccurrence($s, $target, $replacement) {
		if($s === null || $s === "") {
			return $s;
		}
		return com_wiris_util_type_StringUtils::replaceOccurrenceImpl($s, $target, $replacement, _hx_last_index_of($s, $target, null));
	}
	static function replaceOccurrenceImpl($s, $target, $replacement, $pos) {
		if($pos === -1) {
			return $s;
		}
		$sb = new StringBuf();
		$sb->b .= _hx_substr($s, 0, $pos);
		$sb->add($replacement);
		$sb->b .= _hx_substr($s, $pos + strlen($target), strlen($s) - $pos - strlen($target));
		return $sb->b;
	}
	static function countOccurrences($s, $target) {
		if($s === null || $target === null || $target === "") {
			return -1;
		}
		$count = 0;
		$pos = _hx_index_of($s, $target, null);
		while($pos !== -1) {
			$count++;
			$pos = _hx_index_of($s, $target, $pos + strlen($target));
		}
		return $count;
	}
	static function splitOnGreek($text) {
		$length = haxe_Utf8::length($text);
		if($length === 0) {
			return null;
		}
		$runs = null;
		$runStartIndex = 0;
		$currentIndex = 0;
		$currentGreekRun = true;
		$it = com_wiris_system_Utf8::getIterator($text);
		while($it->hasNext()) {
			$codepoint = $it->next();
			$isGreek = com_wiris_util_xml_WCharacterBase::isGreek($codepoint);
			if($isGreek && !$currentGreekRun) {
				if($runs === null) {
					$runs = new _hx_array(array());
				}
				$runText = haxe_Utf8::sub($text, $runStartIndex, $currentIndex - $runStartIndex);
				$runs->push($runText);
				unset($runText);
			}
			if($isGreek) {
				if($runs === null) {
					$runs = new _hx_array(array());
				}
				$runs->push(com_wiris_util_type_StringUtils_0($codepoint, $currentGreekRun, $currentIndex, $isGreek, $it, $length, $runStartIndex, $runs, $text));
			}
			if($isGreek !== $currentGreekRun) {
				$runStartIndex = $currentIndex;
				$currentGreekRun = $isGreek;
			}
			$currentIndex += 1;
			unset($isGreek,$codepoint);
		}
		if($runs !== null && !$currentGreekRun) {
			$runText = haxe_Utf8::sub($text, $runStartIndex, $currentIndex - $runStartIndex);
			$runs->push($runText);
		}
		return $runs;
	}
	function __toString() { return 'com.wiris.util.type.StringUtils'; }
}
function com_wiris_util_type_StringUtils_0(&$codepoint, &$currentGreekRun, &$currentIndex, &$isGreek, &$it, &$length, &$runStartIndex, &$runs, &$text) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($codepoint);
		return $s->toString();
	}
}
