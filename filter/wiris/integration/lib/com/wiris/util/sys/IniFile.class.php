<?php

class com_wiris_util_sys_IniFile {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->props = new Hash();
	}}
	public function loadProperties($file) {
		$start = null;
		$end = 0;
		$count = 1;
		while(($start = _hx_index_of($file, "\x0A", $end)) !== -1) {
			$line = _hx_substr($file, $end, $start - $end);
			$end = $start + 1;
			$this->loadPropertiesLine($line, $count);
			$count++;
			unset($line);
		}
		if($end < strlen($file)) {
			$line = _hx_substr($file, $end, null);
			$this->loadPropertiesLine($line, $count);
		}
	}
	public function loadPropertiesLine($line, $count) {
		$line = trim($line);
		if(strlen($line) === 0) {
			return;
		}
		if(StringTools::startsWith($line, ";") || StringTools::startsWith($line, "#")) {
			return;
		}
		$equals = _hx_index_of($line, "=", null);
		if($equals === -1) {
			throw new HException("Malformed INI file " . $this->filename . " in line " . _hx_string_rec($count, "") . " no equal sign found.");
		}
		$key = _hx_substr($line, 0, $equals);
		$key = trim($key);
		$value = _hx_substr($line, $equals + 1, null);
		$value = trim($value);
		if(StringTools::startsWith($value, "\"") && StringTools::endsWith($value, "\"")) {
			$value = _hx_substr($value, 1, strlen($value) - 2);
		}
		$backslash = 0;
		while(($backslash = _hx_index_of($value, "\\", $backslash)) !== -1) {
			if(strlen($value) <= $backslash + 1) {
				continue;
			}
			$letter = _hx_substr($value, $backslash + 1, 1);
			if($letter === "n") {
				$letter = "\x0A";
			} else {
				if($letter === "r") {
					$letter = "\x0D";
				} else {
					if($letter === "t") {
						$letter = "\x09";
					}
				}
			}
			$value = _hx_substr($value, 0, $backslash) . $letter . _hx_substr($value, $backslash + 2, null);
			$backslash++;
			unset($letter);
		}
		$this->props->set($key, $value);
	}
	public function loadINI() {
		$s = com_wiris_system_Storage::newStorage($this->filename);
		if(!$s->exists()) {
			$s = com_wiris_system_Storage::newResourceStorage($this->filename);
		}
		try {
			$file = $s->read();
			if($file !== null) {
				$this->loadProperties($file);
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
			}
		}
	}
	public function getProperties() {
		return $this->props;
	}
	public $props;
	public $filename;
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
	static function newIniFileFromFilename($path) {
		$ini = new com_wiris_util_sys_IniFile();
		$ini->filename = $path;
		$ini->loadINI();
		return $ini;
	}
	static function newIniFileFromString($inifile) {
		$ini = new com_wiris_util_sys_IniFile();
		$ini->filename = "";
		$ini->loadProperties($inifile);
		return $ini;
	}
	static function propertiesToString($h) {
		$sb = new StringBuf();
		$iter = $h->keys();
		$keys = new _hx_array(array());
		while($iter->hasNext()) {
			$keys->push($iter->next());
		}
		$i = null;
		$j = null;
		$n = $keys->length;
		{
			$_g = 0;
			while($_g < $n) {
				$i1 = $_g++;
				{
					$_g1 = $i1 + 1;
					while($_g1 < $n) {
						$j1 = $_g1++;
						$s1 = $keys[$i1];
						$s2 = $keys[$j1];
						if(com_wiris_util_sys_IniFile::compareStrings($s1, $s2) > 0) {
							$keys[$i1] = $s2;
							$keys[$j1] = $s1;
						}
						unset($s2,$s1,$j1);
					}
					unset($_g1);
				}
				unset($i1);
			}
		}
		{
			$_g = 0;
			while($_g < $n) {
				$i1 = $_g++;
				$key = $keys[$i1];
				$sb->add($key);
				$sb->add("=");
				$value = $h->get($key);
				$value = str_replace("\\", "\\\\", $value);
				$value = str_replace("\x0A", "\\n", $value);
				$value = str_replace("\x0D", "\\r", $value);
				$value = str_replace("\x09", "\\t", $value);
				$sb->add($value);
				$sb->add("\x0A");
				unset($value,$key,$i1);
			}
		}
		return $sb->b;
	}
	static function compareStrings($a, $b) {
		$i = null;
		$an = strlen($a);
		$bn = strlen($b);
		$n = (($an > $bn) ? $bn : $an);
		{
			$_g = 0;
			while($_g < $n) {
				$i1 = $_g++;
				$c = _hx_char_code_at($a, $i1) - _hx_char_code_at($b, $i1);
				if($c !== 0) {
					return $c;
				}
				unset($i1,$c);
			}
		}
		return strlen($a) - strlen($b);
	}
	function __toString() { return 'com.wiris.util.sys.IniFile'; }
}
