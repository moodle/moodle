<?php

class com_wiris_util_geometry_GeometryElement {
	public function __construct($data) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
	}}
	public function isStatsObject() {
		$type = $this->getType();
		return com_wiris_util_geometry_GeometryElement::$HISTOGRAM === $type || com_wiris_util_geometry_GeometryElement::$PIE_CHART === $type || com_wiris_util_geometry_GeometryElement::$BOX_PLOT === $type || com_wiris_util_geometry_GeometryElement::$BAR_CHART === $type || com_wiris_util_geometry_GeometryElement::$LINE_CHART === $type;
	}
	public function setProperty($key, $value) {
		$this->data->set($key, $value);
	}
	public function setBinLimits($b) {
		$c = new _hx_array(array());
		{
			$_g1 = 0; $_g = $b->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$c->push($b[$i]);
				unset($i);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryElement::$BIN_LIMITS, $c);
	}
	public function setStatisticalData2D($d) {
		$c = new _hx_array(array());
		{
			$_g1 = 0; $_g = $d->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$cc = new _hx_array(array());
				{
					$_g3 = 0; $_g2 = _hx_array_get($d, $i)->length;
					while($_g3 < $_g2) {
						$j = $_g3++;
						$cc->push($d[$i][$j]);
						unset($j);
					}
					unset($_g3,$_g2);
				}
				$c->push($cc);
				unset($i,$cc);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryElement::$STATISTICAL_DATA, $c);
	}
	public function setStatisticalData1D($d) {
		$c = new _hx_array(array());
		{
			$_g1 = 0; $_g = $d->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$c->push($d[$i]);
				unset($i);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryElement::$STATISTICAL_DATA, $c);
	}
	public function addCircuit($vertices, $pieces, $pieceReversed) {
		$circuits = null;
		if($this->data->exists(com_wiris_util_geometry_GeometryElement::$CIRCUITS)) {
			$circuits = com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryElement::$CIRCUITS));
		} else {
			$circuits = new _hx_array(array());
			$this->data->set(com_wiris_util_geometry_GeometryElement::$CIRCUITS, $circuits);
		}
		$circuit = new Hash();
		$circuit->set(com_wiris_util_geometry_GeometryElement::$VERTICES, $vertices);
		$circuit->set(com_wiris_util_geometry_GeometryElement::$PIECE_REVERSED, $pieceReversed);
		$piecesArray = new _hx_array(array());
		$i = 0;
		{
			$_g1 = 0; $_g = $pieces->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$piecesArray->push(_hx_array_get($pieces, $i1)->data);
				unset($i1);
			}
		}
		$circuit->set(com_wiris_util_geometry_GeometryElement::$PIECES, $piecesArray);
		$circuits->push($circuit);
	}
	public function setTextBoxMathML($text) {
		$this->data->set(com_wiris_util_geometry_GeometryElement::$TEXT, $text);
	}
	public function setWirisCasComputed($wirisCasComputed) {
		$b = (($wirisCasComputed) ? "true" : "false");
		$this->data->set(com_wiris_util_geometry_GeometryElement::$FROM_CAS_KERNEL, $b);
	}
	public function setRayCoordinates($x, $y, $v0, $v1) {
		$c = new _hx_array(array());
		$p = new _hx_array(array());
		$d = new _hx_array(array());
		$p->push($x);
		$p->push($y);
		$d->push($v0);
		$d->push($v1);
		$c->push($p);
		$c->push($d);
		$this->data->set(com_wiris_util_geometry_GeometryElement::$COORDINATES, $c);
	}
	public function setLineCoordinates($coords) {
		$this->setCoordinates($coords);
	}
	public function setDefinition($definition) {
		$this->data->set(com_wiris_util_geometry_GeometryElement::$DEFINITION, $definition);
	}
	public function getValue() {
		return com_wiris_util_json_JSon::getString($this->data->get(com_wiris_util_geometry_GeometryElement::$VALUE));
	}
	public function setValue($value) {
		$this->data->set(com_wiris_util_geometry_GeometryElement::$VALUE, $value);
	}
	public function getElement($i) {
		return new com_wiris_util_geometry_GeometryElement(com_wiris_util_json_JSon::getHash(_hx_array_get(com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryElement::$ELEMENTS)), $i)));
	}
	public function getElementsLength() {
		return com_wiris_util_geometry_GeometryElement_0($this);
	}
	public function addElement($e) {
		$elements = (($this->data->exists(com_wiris_util_geometry_GeometryElement::$ELEMENTS)) ? com_wiris_util_json_JSon::getArray($this->data->get(com_wiris_util_geometry_GeometryElement::$ELEMENTS)) : new _hx_array(array()));
		$elements->push($e->data);
		$this->data->set(com_wiris_util_geometry_GeometryElement::$ELEMENTS, $elements);
	}
	public function setPolygonalCoordinates($n, $x, $y) {
		$c = new _hx_array(array());
		$i = null;
		{
			$_g = 0;
			while($_g < $n) {
				$i1 = $_g++;
				$p = new _hx_array(array());
				$p->push($x[$i1]);
				$p->push($y[$i1]);
				$c->push($p);
				unset($p,$i1);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryElement::$COORDINATES, $c);
	}
	public function setConicArcCoordinates($coords, $start, $end) {
		$this->setCoordinates($coords);
		$this->setInterval($start, $end);
	}
	public function setInterval($start, $end) {
		$i = new _hx_array(array());
		$i->push($start);
		$i->push($end);
		$this->data->set(com_wiris_util_geometry_GeometryElement::$INTERVAL, $i);
	}
	public function setArcCoordinates($x, $y, $r, $start, $end) {
		$this->setCircumferenceCoordinates($x, $y, $r);
		$this->setInterval($start, $end);
	}
	public function setCoordinates($coords) {
		$c = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $coords->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$c->push($coords[$i1]);
				unset($i1);
			}
		}
		$this->data->set(com_wiris_util_geometry_GeometryElement::$COORDINATES, $c);
	}
	public function setConicCoordinates($coords) {
		$this->setCoordinates($coords);
	}
	public function setCircumferenceCoordinates($x, $y, $r) {
		$c = new _hx_array(array());
		$p = new _hx_array(array());
		$p->push($x);
		$p->push($y);
		$c->push($p);
		$c->push($r);
		$this->data->set(com_wiris_util_geometry_GeometryElement::$COORDINATES, $c);
	}
	public function setSegmentCoordinates($x1, $y1, $x2, $y2) {
		$c = new _hx_array(array());
		$p1 = new _hx_array(array());
		$p1->push($x1);
		$p1->push($y1);
		$c->push($p1);
		$p2 = new _hx_array(array());
		$p2->push($x2);
		$p2->push($y2);
		$c->push($p2);
		$this->data->set(com_wiris_util_geometry_GeometryElement::$COORDINATES, $c);
	}
	public function setPointCoordinates($x, $y) {
		$c = new _hx_array(array());
		$c->push($x);
		$c->push($y);
		$this->data->set(com_wiris_util_geometry_GeometryElement::$COORDINATES, $c);
	}
	public function getCoordinates() {
		return $this->data->get(com_wiris_util_geometry_GeometryElement::$COORDINATES);
	}
	public function setId($id) {
		$this->data->set(com_wiris_util_geometry_GeometryElement::$ID, $id);
	}
	public function getId() {
		return com_wiris_util_json_JSon::getString($this->data->get(com_wiris_util_geometry_GeometryElement::$ID));
	}
	public function setType($type) {
		$this->data->set(com_wiris_util_geometry_GeometryElement::$TYPE, $type);
	}
	public function getType() {
		return com_wiris_util_json_JSon::getString($this->data->get(com_wiris_util_geometry_GeometryElement::$TYPE));
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
	static $POINT = "point";
	static $LINE = "line";
	static $LINE_SEGMENT = "line_segment";
	static $VECTOR = "vector";
	static $RAY = "ray";
	static $POLYLINE = "polyline";
	static $ARC = "arc";
	static $ARC_BY_POINTS = "arc_by_points";
	static $CIRCLE_ARC = "circle_arc";
	static $CIRCLE = "circle";
	static $PATH = "path";
	static $POLYGON = "polygon";
	static $COMPOUND = "compound_element";
	static $CONIC = "conic";
	static $FUNCTION_GRAPH = "function_graph";
	static $REGION = "region";
	static $REGION_ARC = "region_arc";
	static $REGION_CIRCLE_ARC = "region_circle_arc";
	static $UNDEFINED = "undefined";
	static $TEXT_BOX = "text_box";
	static $PARAMETRIC_CURVE = "parametric_curve";
	static $AREA = "area";
	static $RECTANGLE = "rectangle";
	static $PARALLELOGRAM = "parallelogram";
	static $ANGLE = "angle";
	static $PART_OF_ELEMENT = "part_of_element";
	static $LENGTH = "length";
	static $DISTANCE = "distance";
	static $LABEL = "label";
	static $IMAGE = "image";
	static $INTERPOLATING_POLYNOMIAL = "interpolating_polynomial";
	static $HISTOGRAM = "histogram";
	static $BOX_PLOT = "box_plot";
	static $BAR_CHART = "bar_chart";
	static $PIE_CHART = "pie_chart";
	static $LINE_CHART = "line_chart";
	static $TYPE = "type";
	static $ID = "id";
	static $COORDINATES = "coordinates";
	static $PATH_CLOSED = "closed";
	static $INTERVAL = "interval";
	static $VALUE = "value_content";
	static $DEFINITION = "definition_content";
	static $ELEMENTS = "elements";
	static $START_POINT = "start_point";
	static $END_POINT = "end_point";
	static $TEXT = "text";
	static $CIRCUITS = "circuits";
	static $VERTICES = "vertices";
	static $PIECES = "pieces";
	static $PIECE_REVERSED = "piece_reversed";
	static $BOUNDED = "bounded";
	static $STATISTICAL_DATA = "statistical_data";
	static $BIN_LIMITS = "bin_limits";
	static $CLOSED_LEFT = "closed_left";
	static $WHISKER_RANGE = "whisker_range";
	static $LABEL_VERTICAL_POSITION = "text_box_vertical_position";
	static $LABEL_HORIZONTAL_POSITION = "text_box_horizontal_position";
	static $LABEL_POSITION_RIGHT = "right";
	static $LABEL_POSITION_LEFT = "left";
	static $LABEL_POSITION_CENTER = "centered";
	static $LABEL_POSITION_BASELINE = "baseline";
	static $LABEL_POSITION_TOP = "top";
	static $LABEL_POSITION_BOTTOM = "bottom";
	static $DELETE_FROM_CONSTRUCTION = "deleteFromConstruction";
	static $FROM_CAS_KERNEL = "wiris_cas_kernel_computed";
	static function newGeometryElement() {
		return new com_wiris_util_geometry_GeometryElement(new Hash());
	}
	function __toString() { return 'com.wiris.util.geometry.GeometryElement'; }
}
function com_wiris_util_geometry_GeometryElement_0(&$»this) {
	if($»this->data->exists(com_wiris_util_geometry_GeometryElement::$ELEMENTS)) {
		return com_wiris_util_json_JSon::getArray($»this->data->get(com_wiris_util_geometry_GeometryElement::$ELEMENTS))->length;
	} else {
		return 0;
	}
}
