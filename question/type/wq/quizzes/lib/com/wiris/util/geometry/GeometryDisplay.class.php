<?php

class com_wiris_util_geometry_GeometryDisplay {
	public function __construct($data) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
	}}
	public function isStatsPlotter() {
		$property = $this->getProperty(com_wiris_util_geometry_GeometryDisplay::$STATS_AXIS);
		return $property !== null && com_wiris_util_json_JSon::getBoolean($property);
	}
	public function getElementStyleData($referer) {
		$elemStyles = $this->data->get(com_wiris_util_geometry_GeometryDisplay::$STYLES);
		{
			$_g = 0;
			while($_g < $elemStyles->length) {
				$elem = $elemStyles[$_g];
				++$_g;
				$styleData = com_wiris_util_json_JSon::getHash($elem);
				if(_hx_equal($referer, $styleData->get(com_wiris_util_geometry_GeometryElementStyle::$REFER))) {
					return $styleData;
				}
				unset($styleData,$elem);
			}
		}
		return null;
	}
	public function getElementNames() {
		$styles = $this->data->get(com_wiris_util_geometry_GeometryDisplay::$STYLES);
		if($styles === null) {
			return new _hx_array(array());
		}
		$refs = new _hx_array(array());
		{
			$_g = 0;
			while($_g < $styles->length) {
				$s = $styles[$_g];
				++$_g;
				$refs->push(_hx_string_call($s->get(com_wiris_util_geometry_GeometryElementStyle::$REFER), "toString", array()));
				unset($s);
			}
		}
		return $refs;
	}
	public function setElementStyle($referer, $style) {
		$h = $style->data;
		$h->set(com_wiris_util_geometry_GeometryElementStyle::$REFER, $referer);
		if(!$this->data->exists(com_wiris_util_geometry_GeometryDisplay::$STYLES)) {
			$this->data->set(com_wiris_util_geometry_GeometryDisplay::$STYLES, new _hx_array(array()));
		}
		$this->data->get(com_wiris_util_geometry_GeometryDisplay::$STYLES)->push($h);
	}
	public function deleteElement($elemName) {
		$index = 0;
		$styles = $this->data->get(com_wiris_util_geometry_GeometryDisplay::$STYLES);
		while($index < $styles->length) {
			$elemData = $styles[$index];
			if(_hx_equal(_hx_string_call($elemData->get(com_wiris_util_geometry_GeometryElementStyle::$REFER), "toString", array()), $elemName)) {
				break;
			} else {
				++$index;
			}
			unset($elemData);
		}
		$this->data->get(com_wiris_util_geometry_GeometryDisplay::$STYLES)->splice($index, 1);
	}
	public function getElementStyle($referer) {
		return new com_wiris_util_geometry_GeometryElementStyle($this->getElementStyleData($referer));
	}
	public function containsElement($elemName) {
		return com_wiris_system_ArrayEx::contains($this->getElementNames(), $elemName);
	}
	public function getProperty($key) {
		return $this->data->get($key);
	}
	public function setProperties($properties) {
		$keys = $properties->keys();
		while($keys->hasNext()) {
			$key = $keys->next();
			$this->data->set($key, $properties->get($key));
			unset($key);
		}
	}
	public function setProperty($key, $value) {
		$this->data->set($key, $value);
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
	static $ID = "id";
	static $STYLES = "styles";
	static $BACKGROUND_COLOR = "background_color";
	static $CENTER = "center";
	static $HEIGHT = "height";
	static $WIDTH = "width";
	static $ASPECT_RATIO = "aspect_ratio";
	static $AXIS_X = "axis_x";
	static $AXIS_Y = "axis_y";
	static $AXIS_COLOR = "axis_color";
	static $VERTICAL_AXIS_VALUES_POSITION = "vertical_axis_values_position";
	static $HORIZONTAL_AXIS_VALUES_POSITION = "horizontal_axis_values_position";
	static $VERTICAL_AXIS_LABEL = "vertical_axis_label";
	static $HORIZONTAL_AXIS_LABEL = "horizontal_axis_label";
	static $HORIZONTAL_AXIS_STEP = "horizontal_axis_step";
	static $VERTICAL_AXIS_STEP = "vertical_axis_step";
	static $GRID_X = "grid_x";
	static $GRID_Y = "grid_y";
	static $GRID_SUBDIVISIONS = "grid_subdivisions";
	static $GRID_SUBDIVISIONS_X = "grid_subdivisions_x";
	static $GRID_SUBDIVISIONS_Y = "grid_subdivisions_y";
	static $GRID_PRIMARY_COLOR = "grid_primary_color";
	static $GRID_SECONDARY_COLOR = "grid_secondary_color";
	static $HORIZONTAL_GRID_STEP = "horizontal_grid_step";
	static $VERTICAL_GRID_STEP = "vertical_grid_step";
	static $STATS_AXIS = "stats_axis";
	static $HORIZONTAL_ORIENTATION = "horizontal_orientation";
	static $ABOVE = "above";
	static $RIGHT = "right";
	static $BELOW = "below";
	static $LEFT = "left";
	static $NONE = "none";
	static $AUTO = "auto";
	static $DELETE_ON_UPDATE_FILE = "delete_on_update_file";
	static $EXTERNAL = "external";
	static function fromJSON($json) {
		return new com_wiris_util_geometry_GeometryDisplay(com_wiris_util_json_JSon::getHash(com_wiris_util_json_JSon::decode($json)));
	}
	function __toString() { return 'com.wiris.util.geometry.GeometryDisplay'; }
}
