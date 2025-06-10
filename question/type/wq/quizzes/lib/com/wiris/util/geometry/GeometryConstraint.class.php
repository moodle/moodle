<?php

class com_wiris_util_geometry_GeometryConstraint {
	public function __construct($data) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
	}}
	public function setInput($input) {
		$a = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $input->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$a->push($input[$i1]);
				unset($i1);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryConstraint::$INPUT, $a);
	}
	public function getInput() {
		$a = com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryConstraint::$INPUT));
		$inp = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$inp[$i1] = com_wiris_util_json_JSon::getString($a[$i1]);
				unset($i1);
			}
		}
		return $inp;
	}
	public function setOutput($output) {
		$a = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $output->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$a->push($output[$i1]);
				unset($i1);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryConstraint::$OUTPUT, $a);
	}
	public function getOutput() {
		$a = com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryConstraint::$OUTPUT));
		$ou = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$ou[$i1] = com_wiris_util_json_JSon::getString($a[$i1]);
				unset($i1);
			}
		}
		return $ou;
	}
	public function setType($type) {
		$this->data->set(com_wiris_util_geometry_GeometryConstraint::$TYPE, $type);
	}
	public function getType() {
		return com_wiris_util_json_JSon::getString($this->data->get(com_wiris_util_geometry_GeometryConstraint::$TYPE));
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
	static $LINE_BY_TWO_POINTS = "line_by_two_points";
	static $LINE_BY_POINT = "line_by_point";
	static $LINE_SEGMENT_BY_POINTS = "line_segment_by_points";
	static $LINE_SEGMENT_BY_POINTS_AND_POLY = "line_segment_by_points_and_poly";
	static $VECTOR_BY_POINTS = "vector_by_points";
	static $CIRCLE_BY_CENTER_AND_POINT = "circle_by_center_and_point";
	static $CIRCLE_BY_THREE_POINTS = "circle_by_three_points";
	static $CONIC_BY_FIVE_POINTS = "conic_by_five_points";
	static $ELLIPSE_BY_FOCI_AND_POINT = "ellipse_by_foci_and_point";
	static $HYPERBOLA_BY_FOCI_AND_POINT = "hyperbola_by_foci_and_point";
	static $LINE_PARALLEL_TO_LINE_BY_POINT = "line_parallel_to_line_by_point";
	static $POINT_INTERSECTION_OF_TWO_LINES = "point_intersection_of_two_lines";
	static $POINT_ON_LINE = "point_on_line";
	static $RAY_FROM_POINT_BY_POINT = "ray_from_point_by_point";
	static $ARC_BY_THREE_POINTS = "arc_by_three_points";
	static $ELEMENT_FIXED = "element_fixed";
	static $ELLIPSE_ARC_BY_FIVE_POINTS = "ellipse_arc_by_five_points";
	static $LINE_PERPENDICULAR_TO_ELEMENT_BY_EXTERIOR_POINT = "line_perpendicular_to_element_by_exterior_point";
	static $LINE_PERPENDICULAR_TO_ELEMENT_BY_POINT = "line_perpendicular_to_element_by_point";
	static $LINE_PERPENDICULAR_TO_LINE_BY_POINT = "line_perpendicular_to_line_by_point";
	static $LINE_TANGENT_TO_ELEMENT_BY_EXTERIOR_POINT = "line_tangent_to_element_by_exterior point";
	static $LINE_TANGENT_TO_ELEMENT_BY_POINT = "line_tangent_to_element_by_point";
	static $PARABOLA_BY_FOCUS_AND_DIRECTRIX = "parabola_by_focus_and_directrix";
	static $PARABOLA_BY_FOCUS_AND_VERTEX = "parabola_by_focus_and_vertex";
	static $POINT_CENTER_OF_CIRCLE = "point_center_of_circle";
	static $POINT_CRITICAL_OF_FUNCTION = "point_critical_of_function";
	static $POINT_FIXED = "point_fixed";
	static $POINT_INTERSECTION_OF_TWO_ELEMENTS = "point_intersection_of_two_elements";
	static $POINT_MIDPOINT_OF_LINE_SEGMENT = "point_midpoint_of_line_segment";
	static $POINT_ON_CONIC = "point_on_conic";
	static $POINT_ON_FUNCTION = "point_on_function";
	static $POINT_ORIGIN_OF_VECTOR = "point_origin_of_vector";
	static $POINT_ON_GENERIC_ELEMENT = "point_on_generic_element";
	static $POINTS_INTERSECTION_OF_CONIC_AND_LINE = "points_intersection_of_conic_and_line";
	static $POLYGON_BY_POINTS = "polygon_by_points";
	static $POLYLINE_BY_POINTS = "polyline_by_points";
	static $AREA_BY_BOUNDARY_ELEMENTS = "area_by_boundary_elements";
	static $PARALLELOGRAM_BY_TWO_VECTORS = "parallelogram_by_two_vectors";
	static $ANGLE_BY_TWO_ELEMENTS = "angle_by_two_elements";
	static $DISTANCE_BETWEEN_TWO_ELEMENTS = "distance_between_two_elements";
	static $PART_OF_ELEMENT_BY_ELEMENT_AND_TWO_POINTS = "part_of_element_by_element_and_two_points";
	static $LENGTH_OF_ELEMENT = "length_of_element";
	static $LABEL_OF_ELEMENT = "label_of_element";
	static $LABEL_FIXED = "label_fixed";
	static $ELEMENT_BY_LABEL = "element_by_label";
	static $INTERPOLATING_POLYNOMIAL_BY_POINTS = "interpolating_polynomial_by_points";
	static $IMAGE_BY_POINTS = "image_by_points";
	static $LABEL_OF_ELEMENT_PART = "label_of_element_part";
	static $TYPE = "type";
	static $INPUT = "input";
	static $OUTPUT = "output";
	static function newGeometryConstraint() {
		return new com_wiris_util_geometry_GeometryConstraint(new Hash());
	}
	function __toString() { return 'com.wiris.util.geometry.GeometryConstraint'; }
}
