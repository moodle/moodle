<?php

class com_wiris_util_geometry_GraphMode extends Enum {
	public static $BAR_CHART;
	public static $BOX_PLOT;
	public static $HISTOGRAM;
	public static $LINE_CHART;
	public static $PIE_CHART;
	public static $SKETCH;
	public static $STANDARD;
	public static $UNDECIDED_STANDARD_SKETCH;
	public static $__constructors = array(2 => 'BAR_CHART', 3 => 'BOX_PLOT', 1 => 'HISTOGRAM', 5 => 'LINE_CHART', 4 => 'PIE_CHART', 6 => 'SKETCH', 0 => 'STANDARD', 7 => 'UNDECIDED_STANDARD_SKETCH');
	}
com_wiris_util_geometry_GraphMode::$BAR_CHART = new com_wiris_util_geometry_GraphMode("BAR_CHART", 2);
com_wiris_util_geometry_GraphMode::$BOX_PLOT = new com_wiris_util_geometry_GraphMode("BOX_PLOT", 3);
com_wiris_util_geometry_GraphMode::$HISTOGRAM = new com_wiris_util_geometry_GraphMode("HISTOGRAM", 1);
com_wiris_util_geometry_GraphMode::$LINE_CHART = new com_wiris_util_geometry_GraphMode("LINE_CHART", 5);
com_wiris_util_geometry_GraphMode::$PIE_CHART = new com_wiris_util_geometry_GraphMode("PIE_CHART", 4);
com_wiris_util_geometry_GraphMode::$SKETCH = new com_wiris_util_geometry_GraphMode("SKETCH", 6);
com_wiris_util_geometry_GraphMode::$STANDARD = new com_wiris_util_geometry_GraphMode("STANDARD", 0);
com_wiris_util_geometry_GraphMode::$UNDECIDED_STANDARD_SKETCH = new com_wiris_util_geometry_GraphMode("UNDECIDED_STANDARD_SKETCH", 7);
