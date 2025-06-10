<?php

class com_wiris_util_geometry_GeometryMode extends Enum {
	public static $BAR_CHART;
	public static $BOX_PLOT;
	public static $HISTOGRAM;
	public static $LINE_CHART;
	public static $PIE_CHART;
	public static $STANDARD;
	public static $__constructors = array(2 => 'BAR_CHART', 3 => 'BOX_PLOT', 1 => 'HISTOGRAM', 5 => 'LINE_CHART', 4 => 'PIE_CHART', 0 => 'STANDARD');
	}
com_wiris_util_geometry_GeometryMode::$BAR_CHART = new com_wiris_util_geometry_GeometryMode("BAR_CHART", 2);
com_wiris_util_geometry_GeometryMode::$BOX_PLOT = new com_wiris_util_geometry_GeometryMode("BOX_PLOT", 3);
com_wiris_util_geometry_GeometryMode::$HISTOGRAM = new com_wiris_util_geometry_GeometryMode("HISTOGRAM", 1);
com_wiris_util_geometry_GeometryMode::$LINE_CHART = new com_wiris_util_geometry_GeometryMode("LINE_CHART", 5);
com_wiris_util_geometry_GeometryMode::$PIE_CHART = new com_wiris_util_geometry_GeometryMode("PIE_CHART", 4);
com_wiris_util_geometry_GeometryMode::$STANDARD = new com_wiris_util_geometry_GeometryMode("STANDARD", 0);
