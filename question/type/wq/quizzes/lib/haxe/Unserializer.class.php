<?php

class haxe_Unserializer {
	public function __construct($buf) {
		if(!php_Boot::$skip_constructor) {
		$this->buf = $buf;
		$this->length = strlen($buf);
		$this->pos = 0;
		$this->scache = new _hx_array(array());
		$this->cache = new _hx_array(array());
		$r = haxe_Unserializer::$DEFAULT_RESOLVER;
		if($r === null) {
			$r = _hx_qtype("Type");
			haxe_Unserializer::$DEFAULT_RESOLVER = $r;
		}
		$this->setResolver($r);
	}}
	public function unserialize() {
		switch(ord(substr($this->buf,$this->pos++,1))) {
		case 110:{
			return null;
		}break;
		case 116:{
			return true;
		}break;
		case 102:{
			return false;
		}break;
		case 122:{
			return 0;
		}break;
		case 105:{
			return $this->readDigits();
		}break;
		case 100:{
			$p1 = $this->pos;
			while(true) {
				$c = ord(substr($this->buf,$this->pos,1));
				if($c >= 43 && $c < 58 || $c === 101 || $c === 69) {
					$this->pos++;
				} else {
					break;
				}
				unset($c);
			}
			return Std::parseFloat(_hx_substr($this->buf, $p1, $this->pos - $p1));
		}break;
		case 121:{
			$len = $this->readDigits();
			if(ord(substr($this->buf,$this->pos++,1)) !== 58 || $this->length - $this->pos < $len) {
				throw new HException("Invalid string length");
			}
			$s = _hx_substr($this->buf, $this->pos, $len);
			$this->pos += $len;
			$s = urldecode($s);
			$this->scache->push($s);
			return $s;
		}break;
		case 107:{
			return Math::$NaN;
		}break;
		case 109:{
			return Math::$NEGATIVE_INFINITY;
		}break;
		case 112:{
			return Math::$POSITIVE_INFINITY;
		}break;
		case 97:{
			$buf = $this->buf;
			$a = new _hx_array(array());
			$this->cache->push($a);
			while(true) {
				$c = ord(substr($this->buf,$this->pos,1));
				if($c === 104) {
					$this->pos++;
					break;
				}
				if($c === 117) {
					$this->pos++;
					$n = $this->readDigits();
					$a[$a->length + $n - 1] = null;
					unset($n);
				} else {
					$a->push($this->unserialize());
				}
				unset($c);
			}
			return $a;
		}break;
		case 111:{
			$o = _hx_anonymous(array());
			$this->cache->push($o);
			$this->unserializeObject($o);
			return $o;
		}break;
		case 114:{
			$n = $this->readDigits();
			if($n < 0 || $n >= $this->cache->length) {
				throw new HException("Invalid reference");
			}
			return $this->cache[$n];
		}break;
		case 82:{
			$n = $this->readDigits();
			if($n < 0 || $n >= $this->scache->length) {
				throw new HException("Invalid string reference");
			}
			return $this->scache[$n];
		}break;
		case 120:{
			throw new HException($this->unserialize());
		}break;
		case 99:{
			$name = $this->unserialize();
			$cl = $this->resolver->resolveClass($name);
			if($cl === null) {
				throw new HException("Class not found " . $name);
			}
			$o = Type::createEmptyInstance($cl);
			$this->cache->push($o);
			$this->unserializeObject($o);
			return $o;
		}break;
		case 119:{
			$name = $this->unserialize();
			$edecl = $this->resolver->resolveEnum($name);
			if($edecl === null) {
				throw new HException("Enum not found " . $name);
			}
			$e = $this->unserializeEnum($edecl, $this->unserialize());
			$this->cache->push($e);
			return $e;
		}break;
		case 106:{
			$name = $this->unserialize();
			$edecl = $this->resolver->resolveEnum($name);
			if($edecl === null) {
				throw new HException("Enum not found " . $name);
			}
			$this->pos++;
			$index = $this->readDigits();
			$tag = _hx_array_get(Type::getEnumConstructs($edecl), $index);
			if($tag === null) {
				throw new HException("Unknown enum index " . $name . "@" . _hx_string_rec($index, ""));
			}
			$e = $this->unserializeEnum($edecl, $tag);
			$this->cache->push($e);
			return $e;
		}break;
		case 108:{
			$l = new HList();
			$this->cache->push($l);
			$buf = $this->buf;
			while(ord(substr($this->buf,$this->pos,1)) !== 104) {
				$l->add($this->unserialize());
			}
			$this->pos++;
			return $l;
		}break;
		case 98:{
			$h = new Hash();
			$this->cache->push($h);
			$buf = $this->buf;
			while(ord(substr($this->buf,$this->pos,1)) !== 104) {
				$s = $this->unserialize();
				$h->set($s, $this->unserialize());
				unset($s);
			}
			$this->pos++;
			return $h;
		}break;
		case 113:{
			$h = new IntHash();
			$this->cache->push($h);
			$buf = $this->buf;
			$c = ord(substr($this->buf,$this->pos++,1));
			while($c === 58) {
				$i = $this->readDigits();
				$h->set($i, $this->unserialize());
				$c = ord(substr($this->buf,$this->pos++,1));
				unset($i);
			}
			if($c !== 104) {
				throw new HException("Invalid IntHash format");
			}
			return $h;
		}break;
		case 118:{
			$d = Date::fromString(_hx_substr($this->buf, $this->pos, 19));
			$this->cache->push($d);
			$this->pos += 19;
			return $d;
		}break;
		case 115:{
			$len = $this->readDigits();
			$buf = $this->buf;
			if(ord(substr($this->buf,$this->pos++,1)) !== 58 || $this->length - $this->pos < $len) {
				throw new HException("Invalid bytes length");
			}
			$codes = haxe_Unserializer::$CODES;
			if($codes === null) {
				$codes = haxe_Unserializer::initCodes();
				haxe_Unserializer::$CODES = $codes;
			}
			$i = $this->pos;
			$rest = $len & 3;
			$size = ($len >> 2) * 3 + (haxe_Unserializer_0($this, $buf, $codes, $i, $len, $rest));
			$max = $i + ($len - $rest);
			$bytes = haxe_io_Bytes::alloc($size);
			$bpos = 0;
			while($i < $max) {
				$c1 = $codes[ord(substr($buf,$i++,1))];
				$c2 = $codes[ord(substr($buf,$i++,1))];
				$bytes->b[$bpos++] = chr($c1 << 2 | $c2 >> 4);
				$c3 = $codes[ord(substr($buf,$i++,1))];
				$bytes->b[$bpos++] = chr($c2 << 4 | $c3 >> 2);
				$c4 = $codes[ord(substr($buf,$i++,1))];
				$bytes->b[$bpos++] = chr($c3 << 6 | $c4);
				unset($c4,$c3,$c2,$c1);
			}
			if($rest >= 2) {
				$c1 = $codes[ord(substr($buf,$i++,1))];
				$c2 = $codes[ord(substr($buf,$i++,1))];
				$bytes->b[$bpos++] = chr($c1 << 2 | $c2 >> 4);
				if($rest === 3) {
					$c3 = $codes[ord(substr($buf,$i++,1))];
					$bytes->b[$bpos++] = chr($c2 << 4 | $c3 >> 2);
				}
			}
			$this->pos += $len;
			$this->cache->push($bytes);
			return $bytes;
		}break;
		case 67:{
			$name = $this->unserialize();
			$cl = $this->resolver->resolveClass($name);
			if($cl === null) {
				throw new HException("Class not found " . $name);
			}
			$o = Type::createEmptyInstance($cl);
			$this->cache->push($o);
			$o->hxUnserialize($this);
			if(ord(substr($this->buf,$this->pos++,1)) !== 103) {
				throw new HException("Invalid custom data");
			}
			return $o;
		}break;
		default:{
		}break;
		}
		$this->pos--;
		throw new HException("Invalid char " . _hx_char_at($this->buf, $this->pos) . " at position " . _hx_string_rec($this->pos, ""));
	}
	public function unserializeEnum($edecl, $tag) {
		if(ord(substr($this->buf,$this->pos++,1)) !== 58) {
			throw new HException("Invalid enum format");
		}
		$nargs = $this->readDigits();
		if($nargs === 0) {
			return Type::createEnum($edecl, $tag, null);
		}
		$args = new _hx_array(array());
		while($nargs-- > 0) {
			$args->push($this->unserialize());
		}
		return Type::createEnum($edecl, $tag, $args);
	}
	public function unserializeObject($o) {
		while(true) {
			if($this->pos >= $this->length) {
				throw new HException("Invalid object");
			}
			if(ord(substr($this->buf,$this->pos,1)) === 103) {
				break;
			}
			$k = $this->unserialize();
			if(!Std::is($k, _hx_qtype("String"))) {
				throw new HException("Invalid object key");
			}
			$v = $this->unserialize();
			$o->{$k} = $v;
			unset($v,$k);
		}
		$this->pos++;
	}
	public function readDigits() {
		$k = 0;
		$s = false;
		$fpos = $this->pos;
		while(true) {
			$c = ord(substr($this->buf,$this->pos,1));
			if(($c === 0)) {
				break;
			}
			if($c === 45) {
				if($this->pos !== $fpos) {
					break;
				}
				$s = true;
				$this->pos++;
				continue;
			}
			if($c < 48 || $c > 57) {
				break;
			}
			$k = $k * 10 + ($c - 48);
			$this->pos++;
			unset($c);
		}
		if($s) {
			$k *= -1;
		}
		return $k;
	}
	public function get($p) {
		return ord(substr($this->buf,$p,1));
	}
	public function getResolver() {
		return $this->resolver;
	}
	public function setResolver($r) {
		if($r === null) {
			$this->resolver = _hx_anonymous(array("resolveClass" => array(new _hx_lambda(array(&$r), "haxe_Unserializer_1"), 'execute'), "resolveEnum" => array(new _hx_lambda(array(&$r), "haxe_Unserializer_2"), 'execute')));
		} else {
			$this->resolver = $r;
		}
	}
	public $resolver;
	public $scache;
	public $cache;
	public $length;
	public $pos;
	public $buf;
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
	static $DEFAULT_RESOLVER;
	static $BASE64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789%:";
	static $CODES = null;
	static function initCodes() {
		$codes = new _hx_array(array());
		{
			$_g1 = 0; $_g = strlen(haxe_Unserializer::$BASE64);
			while($_g1 < $_g) {
				$i = $_g1++;
				$codes[ord(substr(haxe_Unserializer::$BASE64,$i,1))] = $i;
				unset($i);
			}
		}
		return $codes;
	}
	static function run($v) {
		return _hx_deref(new haxe_Unserializer($v))->unserialize();
	}
	function __toString() { return 'haxe.Unserializer'; }
}
haxe_Unserializer::$DEFAULT_RESOLVER = _hx_qtype("Type");
function haxe_Unserializer_0(&$»this, &$buf, &$codes, &$i, &$len, &$rest) {
	if($rest >= 2) {
		return $rest - 1;
	} else {
		return 0;
	}
}
function haxe_Unserializer_1(&$r, $_) {
	{
		return null;
	}
}
function haxe_Unserializer_2(&$r, $_) {
	{
		return null;
	}
}
