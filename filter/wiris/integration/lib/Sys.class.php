<?php

class Sys {
	public function __construct(){}
	static function hprint($v) {
		echo(Std::string($v));
	}
	static function println($v) {
		Sys::hprint($v);
		Sys::hprint("\x0A");
	}
	static function args() {
		return ((array_key_exists("argv", $_SERVER)) ? new _hx_array(array_slice($_SERVER["argv"], 1)) : new _hx_array(array()));
	}
	static function getEnv($s) {
		return getenv($s);
	}
	static function putEnv($s, $v) {
		putenv($s . "=" . $v);
		return;
	}
	static function sleep($seconds) {
		usleep($seconds * 1000000);
		return;
	}
	static function setTimeLocale($loc) {
		return setlocale(LC_TIME, $loc) !== false;
	}
	static function getCwd() {
		$cwd = getcwd();
		$l = _hx_substr($cwd, -1, null);
		return $cwd . ((($l === "/" || $l === "\\") ? "" : "/"));
	}
	static function setCwd($s) {
		chdir($s);
	}
	static function systemName() {
		$s = php_uname("s");
		$p = null;
		if(($p = _hx_index_of($s, " ", null)) >= 0) {
			return _hx_substr($s, 0, $p);
		} else {
			return $s;
		}
	}
	static function escapeArgument($arg) {
		$ok = true;
		{
			$_g1 = 0; $_g = strlen($arg);
			while($_g1 < $_g) {
				$i = $_g1++;
				switch(_hx_char_code_at($arg, $i)) {
				case 32:case 34:{
					$ok = false;
				}break;
				case 0:case 13:case 10:{
					$arg = _hx_substr($arg, 0, $i);
				}break;
				}
				unset($i);
			}
		}
		if($ok) {
			return $arg;
		}
		return "\"" . _hx_explode("\"", $arg)->join("\\\"") . "\"";
	}
	static function command($cmd, $args = null) {
		if($args !== null) {
			$cmd = Sys::escapeArgument($cmd);
			{
				$_g = 0;
				while($_g < $args->length) {
					$a = $args[$_g];
					++$_g;
					$cmd .= " " . Sys::escapeArgument($a);
					unset($a);
				}
			}
		}
		$result = 0;
		system($cmd, $result);
		return $result;
	}
	static function hexit($code) {
		exit($code);
	}
	static function time() {
		return microtime(true);
	}
	static function cpuTime() {
		return microtime(true) - $_SERVER['REQUEST_TIME'];
	}
	static function executablePath() {
		return $_SERVER['SCRIPT_FILENAME'];
	}
	static function environment() {
		return php_Lib::hashOfAssociativeArray($_SERVER);
	}
	static function stdin() {
		return new sys_io_FileInput(fopen("php://stdin", "r"));
	}
	static function stdout() {
		return new sys_io_FileOutput(fopen("php://stdout", "w"));
	}
	static function stderr() {
		return new sys_io_FileOutput(fopen("php://stderr", "w"));
	}
	static function getChar($echo) {
		$v = fgetc(STDIN);
		if($echo) {
			echo($v);
		}
		return $v;
	}
	function __toString() { return 'Sys'; }
}
