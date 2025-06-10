<?php

class com_wiris_util_telemetry_LoggingLevel extends Enum {
	public static $DEBUG;
	public static $ERROR;
	public static $INFO;
	public static $NONE;
	public static $WARNING;
	public static $__constructors = array(4 => 'DEBUG', 1 => 'ERROR', 3 => 'INFO', 0 => 'NONE', 2 => 'WARNING');
	}
com_wiris_util_telemetry_LoggingLevel::$DEBUG = new com_wiris_util_telemetry_LoggingLevel("DEBUG", 4);
com_wiris_util_telemetry_LoggingLevel::$ERROR = new com_wiris_util_telemetry_LoggingLevel("ERROR", 1);
com_wiris_util_telemetry_LoggingLevel::$INFO = new com_wiris_util_telemetry_LoggingLevel("INFO", 3);
com_wiris_util_telemetry_LoggingLevel::$NONE = new com_wiris_util_telemetry_LoggingLevel("NONE", 0);
com_wiris_util_telemetry_LoggingLevel::$WARNING = new com_wiris_util_telemetry_LoggingLevel("WARNING", 2);
