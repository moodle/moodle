<?php

class com_wiris_quizzes_impl_ClasspathLoader {
	public function __construct(){}
	static function load($classpath) {
		$rootpath = dirname(__FILE__) . "/../../../../..";
		com_wiris_quizzes_impl_ClasspathLoader::loadImpl($rootpath . "/" . $classpath);
	}
	static function loadImpl($classpath) {
		if(file_exists($classpath)) {
			if(is_dir($classpath)) {
				$files = sys_FileSystem::readDirectory($classpath);
				$i = null;
				{
					$_g1 = 0; $_g = $files->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						$file = $files[$i1];
						$path = $classpath . "/" . $files[$i1];
						if(!is_dir($path) && StringTools::endsWith($file, ".class.php")) {
							com_wiris_quizzes_impl_ClasspathLoader::registerClass($path, $file);
						}
						unset($path,$i1,$file);
					}
				}
			}
		}
	}
	static function registerClass($path, $file) {
		$name = _hx_string_call($file, "substr", array(0, _hx_len($file) - 10));
		require_once($path);
		_hx_register_type(new _hx_class($name, $name));
	}
	function __toString() { return 'com.wiris.quizzes.impl.ClasspathLoader'; }
}
