<?php

class com_wiris_quizzes_telemetry_QuizzesTrackingTopic extends Enum {
	public static $ACTIVITY_SWITCH;
	public static $HELP_REQUESTED;
	public static $STUDIO_CLOSED;
	public static $STUDIO_OPENED;
	public static $__constructors = array(3 => 'ACTIVITY_SWITCH', 2 => 'HELP_REQUESTED', 1 => 'STUDIO_CLOSED', 0 => 'STUDIO_OPENED');
	}
com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$ACTIVITY_SWITCH = new com_wiris_quizzes_telemetry_QuizzesTrackingTopic("ACTIVITY_SWITCH", 3);
com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$HELP_REQUESTED = new com_wiris_quizzes_telemetry_QuizzesTrackingTopic("HELP_REQUESTED", 2);
com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$STUDIO_CLOSED = new com_wiris_quizzes_telemetry_QuizzesTrackingTopic("STUDIO_CLOSED", 1);
com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$STUDIO_OPENED = new com_wiris_quizzes_telemetry_QuizzesTrackingTopic("STUDIO_OPENED", 0);
