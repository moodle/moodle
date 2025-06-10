<?php

class com_wiris_util_geometry_GeometryFile {
	public function __construct($data) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
	}}
	public function getGeometryHandwriting($i) {
		return new com_wiris_util_geometry_GeometryHandwriting(com_wiris_util_json_JSon::getHash(_hx_array_get($this->getHandwritingTraces(), $i)));
	}
	public function getHandwritingTracesLength() {
		$a = $this->getHandwritingTraces();
		return com_wiris_util_geometry_GeometryFile_0($this, $a);
	}
	public function getHandwritingTraces() {
		return com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryFile::$HANDWRITING_TRACES));
	}
	public function addHandwriting($h) {
		$this->getHandwritingTraces()->push($h->data);
	}
	public function deleteElement($i) {
		if($i < 0 || $i >= $this->getElementsLength()) {
			return;
		}
		$this->getElements()->splice($i, 1);
	}
	public function removeDisplay($i) {
		if($i < 0 || $i >= $this->getDisplaysLength()) {
			return;
		}
		$this->getDisplays()->splice($i, 1);
	}
	public function getGeometryConstraint($i) {
		return new com_wiris_util_geometry_GeometryConstraint(com_wiris_util_json_JSon::getHash(_hx_array_get($this->getConstraints(), $i)));
	}
	public function addConstraint($c) {
		$this->getConstraints()->push($c->data);
	}
	public function getConstraintsLength() {
		$a = $this->getConstraints();
		return com_wiris_util_geometry_GeometryFile_1($this, $a);
	}
	public function getConstraints() {
		return com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryFile::$CONSTRAINTS));
	}
	public function getDisplayById($id) {
		{
			$_g1 = 0; $_g = $this->getDisplaysLength();
			while($_g1 < $_g) {
				$i = $_g1++;
				$display = $this->getDisplay($i);
				if(_hx_equal($id, $display->getProperty(com_wiris_util_geometry_GeometryDisplay::$ID))) {
					return $display;
				}
				unset($i,$display);
			}
		}
		return null;
	}
	public function getDisplay($i) {
		return new com_wiris_util_geometry_GeometryDisplay(com_wiris_util_json_JSon::getHash(_hx_array_get($this->getDisplays(), $i)));
	}
	public function newDisplay() {
		$this->getDisplays()->push(new Hash());
		return $this->getDisplay($this->getDisplaysLength() - 1);
	}
	public function getDisplaysLength() {
		$a = $this->getDisplays();
		return com_wiris_util_geometry_GeometryFile_2($this, $a);
	}
	public function getDisplays() {
		return com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryFile::$DISPLAYS));
	}
	public function addElement($e) {
		$this->getElements()->push($e->data);
	}
	public function getElement($i) {
		return new com_wiris_util_geometry_GeometryElement(_hx_array_get($this->getElements(), $i));
	}
	public function getElementsLength() {
		$a = $this->getElements();
		return com_wiris_util_geometry_GeometryFile_3($this, $a);
	}
	public function getElements() {
		return com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryFile::$ELEMENTS));
	}
	public function toJSON() {
		return com_wiris_util_json_JSon::encode($this->data);
	}
	public $data;
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
	static $ELEMENTS = "elements";
	static $CONSTRAINTS = "constraints";
	static $DISPLAYS = "displays";
	static $HANDWRITING_TRACES = "handwriting_traces";
	static function readJSON($json) {
		return new com_wiris_util_geometry_GeometryFile(com_wiris_util_json_JSon::getHash(com_wiris_util_json_JSon::decode($json)));
	}
	static function newGeometryFile() {
		$data = new Hash();
		$data->set(com_wiris_util_geometry_GeometryFile::$ELEMENTS, new _hx_array(array()));
		$data->set(com_wiris_util_geometry_GeometryFile::$CONSTRAINTS, new _hx_array(array()));
		$data->set(com_wiris_util_geometry_GeometryFile::$DISPLAYS, new _hx_array(array()));
		$data->set(com_wiris_util_geometry_GeometryFile::$HANDWRITING_TRACES, new _hx_array(array()));
		return new com_wiris_util_geometry_GeometryFile($data);
	}
	static function isGeometryFile($str) {
		try {
			if(com_wiris_util_json_JSon::isJson($str) && _hx_index_of($str, "{", null) !== -1 && _hx_index_of($str, "}", null) !== -1) {
				$hash = com_wiris_util_json_JSon::getHash(com_wiris_util_json_JSon::decode($str));
				if($hash === null || !com_wiris_system_TypeTools::isHash($hash)) {
					return false;
				}
				$geometryTags = new _hx_array(array(com_wiris_util_geometry_GeometryFile::$ELEMENTS, com_wiris_util_geometry_GeometryFile::$CONSTRAINTS, com_wiris_util_geometry_GeometryFile::$DISPLAYS));
				{
					$_g = 0;
					while($_g < $geometryTags->length) {
						$geometryTag = $geometryTags[$_g];
						++$_g;
						if(com_wiris_util_type_HashUtils::exists($hash, $geometryTag)) {
							return true;
						}
						unset($geometryTag);
					}
				}
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$t = $_ex_;
			{
				return false;
			}
		}
		return false;
	}
	function __toString() { return 'com.wiris.util.geometry.GeometryFile'; }
}
function com_wiris_util_geometry_GeometryFile_0(&$»this, &$a) {
	if($a === null) {
		return 0;
	} else {
		return $a->length;
	}
}
function com_wiris_util_geometry_GeometryFile_1(&$»this, &$a) {
	if($a !== null) {
		return $»this->getConstraints()->length;
	} else {
		return 0;
	}
}
function com_wiris_util_geometry_GeometryFile_2(&$»this, &$a) {
	if($a !== null) {
		return $a->length;
	} else {
		return 0;
	}
}
function com_wiris_util_geometry_GeometryFile_3(&$»this, &$a) {
	if($a !== null) {
		return $a->length;
	} else {
		return 0;
	}
}
