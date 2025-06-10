<?php

class com_wiris_util_geometry_GeometryHandwriting {
	public function __construct($data) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
	}}
	public function setDisplayId($displayId) {
		$this->data->set(com_wiris_util_geometry_GeometryHandwriting::$DISPLAY_ID, $displayId);
	}
	public function getDisplayId() {
		return (($this->data->exists(com_wiris_util_geometry_GeometryHandwriting::$DISPLAY_ID)) ? com_wiris_util_json_JSon::getString($this->data->get(com_wiris_util_geometry_GeometryHandwriting::$DISPLAY_ID)) : "");
	}
	public function setStrokes($strokes) {
		$this->data->set(com_wiris_util_geometry_GeometryHandwriting::$STROKES, $strokes);
	}
	public $data;
	public function getStrokes() {
		$s = new _hx_array(array());
		$o = $this->data->get(com_wiris_util_geometry_GeometryHandwriting::$STROKES);
		if($o === null) {
			return $s;
		}
		$a1 = com_wiris_util_json_JSon::getArray($o);
		{
			$_g = 0;
			while($_g < $a1->length) {
				$o1 = $a1[$_g];
				++$_g;
				$s1 = new _hx_array(array());
				$a2 = com_wiris_util_json_JSon::getArray($o1);
				{
					$_g1 = 0;
					while($_g1 < $a2->length) {
						$o2 = $a2[$_g1];
						++$_g1;
						$a3 = com_wiris_util_json_JSon::getArray($o2);
						$s2 = new _hx_array(array());
						$k = null;
						{
							$_g3 = 0; $_g2 = $a3->length;
							while($_g3 < $_g2) {
								$k1 = $_g3++;
								$s2[$k1] = com_wiris_util_json_JSon::getFloat($a3[$k1]);
								unset($k1);
							}
							unset($_g3,$_g2);
						}
						$s1->push($s2);
						unset($s2,$o2,$k,$a3);
					}
					unset($_g1);
				}
				$s->push($s1);
				unset($s1,$o1,$a2);
			}
		}
		return $s;
	}
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
	static $RECOGNIZER = "recognizer";
	static $DISPLAY_ID = "display_id";
	static $STROKES = "strokes";
	static function newGeometryHandwriting() {
		$data = new Hash();
		return new com_wiris_util_geometry_GeometryHandwriting($data);
	}
	function __toString() { return 'com.wiris.util.geometry.GeometryHandwriting'; }
}
