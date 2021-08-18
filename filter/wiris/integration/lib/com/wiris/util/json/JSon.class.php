<?php

class com_wiris_util_json_JSon extends com_wiris_util_json_StringParser {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newLine($depth, $sb) {
		$sb->add("\x0D\x0A");
		$i = null;
		{
			$_g = 0;
			while($_g < $depth) {
				$i1 = $_g++;
				$sb->add("  ");
				unset($i1);
			}
		}
		$this->lastDepth = $depth;
	}
	public function setAddNewLines($addNewLines) {
		$this->addNewLines = $addNewLines;
	}
	public function decodeArray() {
		$v = new _hx_array(array());
		$this->nextToken();
		$this->skipBlanks();
		if($this->c === 93) {
			$this->nextToken();
			return $v;
		}
		while($this->c !== 93) {
			$o = $this->localDecode();
			$v->push($o);
			$this->skipBlanks();
			if($this->c === 44) {
				$this->nextToken();
				$this->skipBlanks();
			} else {
				if($this->c !== 93) {
					throw new HException("Expected ',' or ']'.");
				}
			}
			unset($o);
		}
		$this->nextToken();
		return $v;
	}
	public function decodeHash() {
		$h = new Hash();
		$this->nextToken();
		$this->skipBlanks();
		if($this->c === 125) {
			$this->nextToken();
			return $h;
		}
		while($this->c !== 125) {
			$key = $this->decodeString();
			$this->skipBlanks();
			if($this->c !== 58) {
				throw new HException("Expected ':'.");
			}
			$this->nextToken();
			$this->skipBlanks();
			$o = $this->localDecode();
			$h->set($key, $o);
			$this->skipBlanks();
			if($this->c === 44) {
				$this->nextToken();
				$this->skipBlanks();
			} else {
				if($this->c !== 125) {
					throw new HException("Expected ',' or '}'. " . $this->getPositionRepresentation());
				}
			}
			unset($o,$key);
		}
		$this->nextToken();
		return $h;
	}
	public function decodeNumber() {
		$sb = new StringBuf();
		$hex = false;
		$floating = false;
		do {
			$sb->add(com_wiris_util_json_JSon_0($this, $floating, $hex, $sb));
			$this->nextToken();
			if($this->c === 120) {
				$hex = true;
				$sb->add(com_wiris_util_json_JSon_1($this, $floating, $hex, $sb));
				$this->nextToken();
			}
			if($this->c === 46 || $this->c === 69 || $this->c === 101) {
				$floating = true;
			}
		} while($this->c >= 48 && $this->c <= 58 || $hex && $this->isHexDigit($this->c) || $floating && ($this->c === 46 || $this->c === 69 || $this->c === 101 || $this->c === 43 || $this->c === 45));
		if($floating) {
			return Std::parseFloat($sb->b);
		} else {
			return Std::parseInt($sb->b);
		}
	}
	public function decodeString() {
		$sb = new StringBuf();
		$d = $this->c;
		$this->nextToken();
		while($this->c !== $d) {
			if($this->c === 92) {
				$this->nextToken();
				if($this->c === 110) {
					$sb->add("\x0A");
				} else {
					if($this->c === 114) {
						$sb->add("\x0D");
					} else {
						if($this->c === 34) {
							$sb->add("\"");
						} else {
							if($this->c === 39) {
								$sb->add("'");
							} else {
								if($this->c === 116) {
									$sb->add("\x09");
								} else {
									if($this->c === 92) {
										$sb->add("\\");
									} else {
										if($this->c === 117) {
											$this->nextToken();
											$code = com_wiris_util_json_JSon_2($this, $d, $sb);
											$this->nextToken();
											$code .= com_wiris_util_json_JSon_3($this, $code, $d, $sb);
											$this->nextToken();
											$code .= com_wiris_util_json_JSon_4($this, $code, $d, $sb);
											$this->nextToken();
											$code .= com_wiris_util_json_JSon_5($this, $code, $d, $sb);
											$dec = Std::parseInt("0x" . $code);
											$sb->add(com_wiris_util_json_JSon_6($this, $code, $d, $dec, $sb));
											unset($dec,$code);
										} else {
											throw new HException("Unknown scape sequence '\\" . com_wiris_util_json_JSon_7($this, $d, $sb) . "'");
										}
									}
								}
							}
						}
					}
				}
			} else {
				$sb->add(com_wiris_util_json_JSon_8($this, $d, $sb));
			}
			$this->nextToken();
		}
		$this->nextToken();
		return $sb->b;
	}
	public function decodeBooleanOrNull() {
		$sb = new StringBuf();
		while(com_wiris_util_xml_WCharacterBase::isLetter($this->c)) {
			$sb->b .= chr($this->c);
			$this->nextToken();
		}
		$word = $sb->b;
		if($word === "true") {
			return true;
		} else {
			if($word === "false") {
				return false;
			} else {
				if($word === "null") {
					return null;
				} else {
					throw new HException("Unrecognized keyword \"" . $word . "\".");
				}
			}
		}
	}
	public function localDecode() {
		$this->skipBlanks();
		if($this->c === 123) {
			return $this->decodeHash();
		} else {
			if($this->c === 91) {
				return $this->decodeArray();
			} else {
				if($this->c === 34) {
					return $this->decodeString();
				} else {
					if($this->c === 39) {
						return $this->decodeString();
					} else {
						if($this->c === 45 || $this->c >= 48 && $this->c <= 58) {
							return $this->decodeNumber();
						} else {
							if($this->c === 116 || $this->c === 102 || $this->c === 110) {
								return $this->decodeBooleanOrNull();
							} else {
								throw new HException("Unrecognized char " . _hx_string_rec($this->c, ""));
							}
						}
					}
				}
			}
		}
	}
	public function localDecodeString($str) {
		$this->init($str);
		return $this->localDecode();
	}
	public function encodeIntegerFormat($sb, $i) {
		$sb->add($i->toString());
	}
	public function encodeLong($sb, $i) {
		$sb->add("" . Std::string($i));
	}
	public function encodeFloat($sb, $d) {
		$sb->add(com_wiris_system_TypeTools::floatToString($d));
	}
	public function encodeBoolean($sb, $b) {
		$sb->add((($b) ? "true" : "false"));
	}
	public function encodeInteger($sb, $i) {
		$sb->add("" . _hx_string_rec($i, ""));
	}
	public function encodeString($sb, $s) {
		$s = str_replace("\\", "\\\\", $s);
		$s = str_replace("\"", "\\\"", $s);
		$s = str_replace("\x0D", "\\r", $s);
		$s = str_replace("\x0A", "\\n", $s);
		$s = str_replace("\x09", "\\t", $s);
		$sb->add("\"");
		$sb->add($s);
		$sb->add("\"");
	}
	public function encodeArrayString($sb, $v) {
		$astr = new _hx_array(array());
		{
			$_g = 0;
			while($_g < $v->length) {
				$s = $v[$_g];
				++$_g;
				$astr->push($s);
				unset($s);
			}
		}
		$this->encodeArray($sb, $astr);
	}
	public function encodeArrayBoolean($sb, $v) {
		$v2 = new _hx_array(array());
		$i = 0;
		while($i < $v->length) {
			$v2->push($v[$i]);
			++$i;
		}
		$this->encodeArray($sb, $v2);
	}
	public function encodeArrayDouble($sb, $v) {
		$v2 = new _hx_array(array());
		$i = 0;
		while($i < $v->length) {
			$v2->push($v[$i]);
			++$i;
		}
		$this->encodeArray($sb, $v2);
	}
	public function encodeArrayInt($sb, $v) {
		$v2 = new _hx_array(array());
		$i = 0;
		while($i < $v->length) {
			$v2->push($v[$i]);
			++$i;
		}
		$this->encodeArray($sb, $v2);
	}
	public function encodeArray($sb, $v) {
		$newLines = $this->addNewLines && com_wiris_util_json_JSon::getDepth($v) > 2;
		$this->depth++;
		$myDepth = $this->lastDepth;
		$sb->add("[");
		if($newLines) {
			$this->newLine($this->depth, $sb);
		}
		$i = null;
		{
			$_g1 = 0; $_g = $v->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$o = $v[$i1];
				if($i1 > 0) {
					$sb->add(",");
					if($newLines) {
						$this->newLine($this->depth, $sb);
					}
				}
				$this->encodeImpl($sb, $o);
				unset($o,$i1);
			}
		}
		if($newLines) {
			$this->newLine($myDepth, $sb);
		}
		$sb->add("]");
		$this->depth--;
	}
	public function encodeHash($sb, $h) {
		$newLines = $this->addNewLines && com_wiris_util_json_JSon::getDepth($h) > 2;
		$this->depth++;
		$myDepth = $this->lastDepth;
		$sb->add("{");
		if($newLines) {
			$this->newLine($this->depth, $sb);
		}
		$e = $h->keys();
		$first = true;
		while($e->hasNext()) {
			if($first) {
				$first = false;
			} else {
				$sb->add(",");
				if($newLines) {
					$this->newLine($this->depth, $sb);
				}
			}
			$key = $e->next();
			$this->encodeString($sb, $key);
			$sb->add(":");
			$this->encodeImpl($sb, $h->get($key));
			unset($key);
		}
		if($newLines) {
			$this->newLine($myDepth, $sb);
		}
		$sb->add("}");
		$this->depth--;
	}
	public function encodeImpl($sb, $o) {
		if(com_wiris_system_TypeTools::isHash($o)) {
			$this->encodeHash($sb, $o);
		} else {
			if(com_wiris_system_TypeTools::isArray($o)) {
				$this->encodeArray($sb, $o);
			} else {
				if(Std::is($o, _hx_qtype("Array"))) {
					$this->encodeArrayInt($sb, $o);
				} else {
					if(Std::is($o, _hx_qtype("Array"))) {
						$this->encodeArrayDouble($sb, $o);
					} else {
						if(Std::is($o, _hx_qtype("Array"))) {
							$this->encodeArrayBoolean($sb, $o);
						} else {
							if(Std::is($o, _hx_qtype("Array"))) {
								$this->encodeArrayString($sb, $o);
							} else {
								if(Std::is($o, _hx_qtype("String"))) {
									$this->encodeString($sb, $o);
								} else {
									if(Std::is($o, _hx_qtype("Int"))) {
										$this->encodeInteger($sb, $o);
									} else {
										if(Std::is($o, _hx_qtype("haxe.Int64"))) {
											$this->encodeLong($sb, $o);
										} else {
											if(Std::is($o, _hx_qtype("com.wiris.util.json.JSonIntegerFormat"))) {
												$this->encodeIntegerFormat($sb, $o);
											} else {
												if(Std::is($o, _hx_qtype("Bool"))) {
													$this->encodeBoolean($sb, $o);
												} else {
													if(Std::is($o, _hx_qtype("Float"))) {
														$this->encodeFloat($sb, $o);
													} else {
														throw new HException("Impossible to convert to json object of type " . Std::string(Type::getClass($o)));
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	public function encodeObject($o) {
		$sb = new StringBuf();
		$this->depth = 0;
		$this->encodeImpl($sb, $o);
		return $sb->b;
	}
	public $lastDepth;
	public $depth;
	public $addNewLines;
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
	static function sb() { $»args = func_get_args(); return call_user_func_array(self::$sb, $»args); }
	static $sb;
	static function encode($o) {
		$js = new com_wiris_util_json_JSon();
		return $js->encodeObject($o);
	}
	static function decode($str) {
		$json = new com_wiris_util_json_JSon();
		return $json->localDecodeString($str);
	}
	static function getDepth($o) {
		if(com_wiris_system_TypeTools::isHash($o)) {
			$h = $o;
			$m = 0;
			if($h->exists("_left_") || $h->exists("_right_")) {
				if($h->exists("_left_")) {
					$m = com_wiris_common_WInteger::max(com_wiris_util_json_JSon::getDepth($h->get("_left_")), $m);
				}
				if($h->exists("_right_")) {
					$m = com_wiris_common_WInteger::max(com_wiris_util_json_JSon::getDepth($h->get("_right_")), $m);
				}
				return $m;
			}
			$iter = $h->keys();
			while($iter->hasNext()) {
				$key = $iter->next();
				$m = com_wiris_common_WInteger::max(com_wiris_util_json_JSon::getDepth($h->get($key)), $m);
				unset($key);
			}
			return $m + 2;
		} else {
			if(com_wiris_system_TypeTools::isArray($o)) {
				$a = $o;
				$i = null;
				$m = 0;
				{
					$_g1 = 0; $_g = $a->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						$m = com_wiris_common_WInteger::max(com_wiris_util_json_JSon::getDepth($a[$i1]), $m);
						unset($i1);
					}
				}
				return $m + 1;
			} else {
				return 1;
			}
		}
	}
	static function getString($o) {
		return $o;
	}
	static function getFloat($n) {
		if(Std::is($n, _hx_qtype("Float"))) {
			return $n;
		} else {
			if(Std::is($n, _hx_qtype("Int"))) {
				return $n + 0.0;
			} else {
				return 0.0;
			}
		}
	}
	static function getInt($n) {
		if(Std::is($n, _hx_qtype("Float"))) {
			return Math::round($n);
		} else {
			if(Std::is($n, _hx_qtype("Int"))) {
				return $n;
			} else {
				return 0;
			}
		}
	}
	static function getBoolean($b) {
		return $b;
	}
	static function getArray($a) {
		return $a;
	}
	static function getHashArray($a) {
		return $a;
	}
	static function getHash($a) {
		return $a;
	}
	static function isJson($json) {
		try {
			com_wiris_util_json_JSon::decode($json);
			return true;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			if(($e = $_ex_) instanceof com_wiris_system_Exception){
				return false;
			} else throw $»e;;
		}
	}
	function __toString() { return 'com.wiris.util.json.JSon'; }
}
function com_wiris_util_json_JSon_0(&$»this, &$floating, &$hex, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_1(&$»this, &$floating, &$hex, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_2(&$»this, &$d, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_3(&$»this, &$code, &$d, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_4(&$»this, &$code, &$d, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_5(&$»this, &$code, &$d, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_6(&$»this, &$code, &$d, &$dec, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($dec);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_7(&$»this, &$d, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
function com_wiris_util_json_JSon_8(&$»this, &$d, &$sb) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}
