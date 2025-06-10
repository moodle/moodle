<?php

class com_wiris_util_telemetry_TelemetryLoggerImpl implements com_wiris_util_telemetry_TelemetryLogger{
	public function __construct($service, $level) {
		if(!php_Boot::$skip_constructor) {
		$this->service = $service;
		$this->level = $level;
		com_wiris_util_telemetry_TelemetryLoggerImpl::populateLevels();
	}}
	public function logWithParams($text, $parameters, $messageLevel) {
		if(com_wiris_util_telemetry_TelemetryLoggerImpl::loggingLevelToInt($this->level) >= com_wiris_util_telemetry_TelemetryLoggerImpl::loggingLevelToInt($messageLevel)) {
			if($parameters === null) {
				$parameters = new Hash();
			}
			$parameters->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$LEVEL_KEY, com_wiris_util_telemetry_TelemetryLoggerImpl::levelToString($messageLevel));
			$parameters->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$MESSAGE_KEY, $text);
			$message = new com_wiris_util_telemetry_Message(com_wiris_util_telemetry_TelemetryLoggerImpl::$LOGGING_TOPIC, $parameters);
			$this->service->sendMessage($message);
		}
	}
	public function log($text, $messageLevel) {
		$this->logWithParams($text, null, $messageLevel);
	}
	public $service;
	public $level;
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
	static $NONE = "NONE";
	static $ERROR = "ERROR";
	static $WARNING = "WARNING";
	static $INFO = "INFO";
	static $DEBUG = "DEBUG";
	static $LEVELS;
	static $LOGGING_TOPIC = 0;
	static $LEVEL_KEY = "level";
	static $MESSAGE_KEY = "message";
	static $levels;
	static function populateLevels() {
		if(com_wiris_util_telemetry_TelemetryLoggerImpl::$levels === null) {
			com_wiris_util_telemetry_TelemetryLoggerImpl::$levels = new Hash();
			com_wiris_util_telemetry_TelemetryLoggerImpl::$levels->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$NONE, com_wiris_util_telemetry_LoggingLevel::$NONE);
			com_wiris_util_telemetry_TelemetryLoggerImpl::$levels->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$ERROR, com_wiris_util_telemetry_LoggingLevel::$ERROR);
			com_wiris_util_telemetry_TelemetryLoggerImpl::$levels->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$WARNING, com_wiris_util_telemetry_LoggingLevel::$WARNING);
			com_wiris_util_telemetry_TelemetryLoggerImpl::$levels->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$INFO, com_wiris_util_telemetry_LoggingLevel::$INFO);
			com_wiris_util_telemetry_TelemetryLoggerImpl::$levels->set(com_wiris_util_telemetry_TelemetryLoggerImpl::$DEBUG, com_wiris_util_telemetry_LoggingLevel::$DEBUG);
		}
	}
	static function loggingLevelFromString($level) {
		com_wiris_util_telemetry_TelemetryLoggerImpl::populateLevels();
		return com_wiris_util_telemetry_TelemetryLoggerImpl::$levels->get($level);
	}
	static function loggingLevelToInt($level) {
		return com_wiris_util_type_Arrays::indexOfElementArray(com_wiris_util_telemetry_TelemetryLoggerImpl::$LEVELS, com_wiris_util_telemetry_TelemetryLoggerImpl::levelToString($level));
	}
	static function levelToString($level) {
		return com_wiris_util_type_HashUtils::getKey($level, com_wiris_util_telemetry_TelemetryLoggerImpl::$levels);
	}
	function __toString() { return 'com.wiris.util.telemetry.TelemetryLoggerImpl'; }
}
com_wiris_util_telemetry_TelemetryLoggerImpl::$LEVELS = new _hx_array(array(com_wiris_util_telemetry_TelemetryLoggerImpl::$NONE, com_wiris_util_telemetry_TelemetryLoggerImpl::$ERROR, com_wiris_util_telemetry_TelemetryLoggerImpl::$WARNING, com_wiris_util_telemetry_TelemetryLoggerImpl::$INFO, com_wiris_util_telemetry_TelemetryLoggerImpl::$DEBUG));
