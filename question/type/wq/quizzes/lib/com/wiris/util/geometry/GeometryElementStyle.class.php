<?php

class com_wiris_util_geometry_GeometryElementStyle {
	public function __construct($data) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
	}}
	public function getProperty($key) {
		return $this->data->get($key);
	}
	public function setProperty($key, $value) {
		$this->data->set($key, $value);
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
	static function __meta__() { $»args = func_get_args(); return call_user_func_array(self::$__meta__, $»args); }
	static $__meta__;
	static $REFER = "ref";
	static $PARENT = "ref_parent";
	static $STROKE = "stroke";
	static $STROKE_WIDTH = "stroke_width";
	static $STROKE_DASHARRAY = "stroke_dasharray";
	static $LINE_STYLE = "line_style";
	static $BORDER_WIDTH = "border_width";
	static $BORDER_OPACITY = "border_opacity";
	static $FILL = "fill";
	static $FILL_OPACITY = "fill_opacity";
	static $POINT_SIZE = "point_size";
	static $POINT_STYLE = "point_style";
	static $LABEL = "label";
	static $LABEL_VISIBILITY = "label_visibility";
	static $LABEL_COLOR = "label_color";
	static $LABEL_BOLD = "label_bold";
	static $LABEL_ITALIC = "label_italic";
	static $LABEL_POSITION_LOGIC = "label_position_logic";
	static $VISIBLE = "visible";
	static $FIXED = "fixed";
	static $COLOR = "color";
	static $FONT_SIZE = "font_size";
	static $VIEW_3D = "view_3D";
	static $PIE_RADIUS = "pie_radius";
	static $PIE_HEIGHT = "pie_height";
	static $PIE_INCLINATION_ANGLE = "pie_inclination_angle";
	static $PIE_STARTING_ANGLE = "pie_starting_angle";
	static $PIE_CLOCKWISE = "pie_clockwise";
	static $SPACE_BETWEEN_BARS = "space_between_bars";
	static $BAR_WIDTH = "bar_width";
	static $SPACE_BETWEEN_SIDE_BARS = "space_between_side_bars";
	static $BESIDE = "beside";
	static $BOX_WIDTH = "box_width";
	static $SPACE_BETWEEN_BOXES = "space_between_boxes";
	static $NOTCH = "notch";
	static $SHOW_OUTLIERS = "show_outliers";
	static $FREQUENCIES = "frequencies";
	static $SHOW_NAME_LABELS = "show_name_labels";
	static $SHOW_DATA_LABELS = "show_data_labels";
	static $LABELS_TEXT = "name_labels";
	static $COLORS = "colors";
	static $XREF = "xref";
	static $CHANGE_STEP = "change_step";
	static $LENGTH_FOR_NEWS = "length_for_news";
	static $MAX_VALUE_PIE = "max_value_pie";
	static $POINT_SIZES = "point_sizes";
	static $STROKE_WIDTHS = "stroke_widths";
	static function readJSON($json) {
		return new com_wiris_util_geometry_GeometryElementStyle(com_wiris_util_json_JSon::getHash(com_wiris_util_json_JSon::decode($json)));
	}
	static function newGeometryElementStyle() {
		return new com_wiris_util_geometry_GeometryElementStyle(new Hash());
	}
	function __toString() { return 'com.wiris.util.geometry.GeometryElementStyle'; }
}
com_wiris_util_geometry_GeometryElementStyle::$__meta__ = _hx_anonymous(array("statics" => _hx_anonymous(array("LABEL" => _hx_anonymous(array("Deprecated" => null))))));
