<?php

class com_wiris_system_CallWrapper {
	public function __construct() {
		;
	}
	public function autoload($className) {
		if(function_exists('__autoload')) {
			__autoload($className);
		}
	}
	public function phpStop() {
		spl_autoload_unregister('_hx_autoload');
		spl_autoload_register(array($this, 'autoload'));
		restore_exception_handler();
		restore_error_handler();
		error_reporting($this->errorReportingLevel);
	}
	public function setErrorReporting($level) {
		$this->errorReportingLevel = $level;
	}
	public function phpStart() {
		$this->errorReportingLevel = error_reporting(E_ALL & ~E_STRICT);
		set_error_handler('_hx_error_handler', E_ALL);
		set_exception_handler('_hx_exception_handler');
		spl_autoload_register('_hx_autoload');
	}
	public function stop() {
		if($this->isRunning) {
			$this->isRunning = false;
			$this->phpStop();
		}
	}
	public function start() {
		if(!$this->isRunning) {
			$this->isRunning = true;
			$this->phpStart();
			com_wiris_system_Storage::$resourcesDir = null;
		}
	}
	public function init($haxelib) {
		if(!class_exists('php_Boot', false)) {
			$this->setErrorReporting(error_reporting());
			require_once($haxelib . '/lib/php/Boot.class.php');;
			$this->phpStop();
		} else {
			if(is_file($haxelib . '/cache/haxe_autoload.php')) {
				require_once($haxelib . '/cache/haxe_autoload.php');;
			} else {
				
					if (!function_exists('_hx_wiris_load')) {
						function _hx_wiris_load($d, $pack = array()) {
							$h = opendir($d);
							while(false !== ($f = readdir($h))) {
								if($f == '.' || $f == '..') continue;
								$p = $d . '/' . $f;
								if (is_file($p) && substr($f, -4) == '.php') {
									$bn = basename($f, '.php');
									$name = false;
									if(substr($bn, -6) == '.class') {
										$bn = substr($bn, 0, -6);
										$t = '_hx_class';
									} else if(substr($bn, -5) == '.enum') {
										$bn = substr($bn, 0, -5);
										$t = '_hx_enum';
									} else if(substr($bn, -10) == '.interface') {
										$bn = substr($bn, 0, -10);
										$t = '_hx_interface';
									} else if(substr($bn, -7) == '.extern') {
										$bn = substr($bn, 0, -7);
										$t = '_hx_class';
										$name = $bn;
									} else {
										continue;
									}
									$qname = ($bn == 'HList' && empty($pack)) ? 'List' : join('.', array_merge($pack, array($bn)));
									$phpname = !empty($name) ? $name : join('_', array_merge($pack, array($bn)));
									_hx_register_type(new $t($phpname, $qname, $p));

								} else if(is_dir($p)) {
									_hx_wiris_load($p, array_merge($pack, array($f)));
								}
							}
						}
					}
					_hx_wiris_load($haxelib . '/lib');
					;
			}
		}
	}
	public $errorReportingLevel;
	public $isRunning = false;
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
	static $wrapper;
	static function getInstance() {
		if(com_wiris_system_CallWrapper::$wrapper === null) {
			com_wiris_system_CallWrapper::$wrapper = new com_wiris_system_CallWrapper();
		}
		return com_wiris_system_CallWrapper::$wrapper;
	}
	function __toString() { return 'com.wiris.system.CallWrapper'; }
}
