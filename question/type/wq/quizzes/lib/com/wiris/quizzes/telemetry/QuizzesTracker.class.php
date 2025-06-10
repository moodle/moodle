<?php

class com_wiris_quizzes_telemetry_QuizzesTracker extends com_wiris_util_telemetry_Tracker {
	public function __construct($service) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct($service);
	}}
	public function sendInformation($topic, $parameters) {
		$index = com_wiris_quizzes_telemetry_QuizzesTracker::getIndex($topic);
		if($index >= 0) {
			$this->sendInformationImpl($index, $parameters);
		}
	}
	public function activitySwitch($question, $from, $to) {
		$parameters = new Hash();
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_QUESTION, $question->serialize());
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_FROM, $from);
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_TO, $to);
		if(!_hx_equal($this->activityTimestamp, -1)) {
			$activityTime = Date::now()->getTime() - $this->activityTimestamp;
			$this->studioTimestamp = Date::now()->getTime();
			$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_ACTIVITY_TIME, _hx_string_rec($activityTime, "") . "");
		}
		$this->sendInformation(com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$ACTIVITY_SWITCH, $parameters);
	}
	public function helpRequested($question, $activity) {
		$parameters = new Hash();
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_QUESTION, $question->serialize());
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_ACTIVITY, $activity);
		$this->sendInformation(com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$HELP_REQUESTED, $parameters);
	}
	public function studioClosed($question, $activity, $cause) {
		$parameters = new Hash();
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_QUESTION, $question->serialize());
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_ACTIVITY, $activity);
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_CAUSE, $cause);
		if(!_hx_equal($this->studioTimestamp, -1)) {
			$sessionTime = Date::now()->getTime() - $this->studioTimestamp;
			$this->studioTimestamp = -1;
			$this->activityTimestamp = -1;
			$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_SESSION_TIME, _hx_string_rec($sessionTime, "") . "");
		}
		$this->sendInformation(com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$STUDIO_CLOSED, $parameters);
	}
	public function studioOpened($question) {
		$this->studioTimestamp = Date::now()->getTime();
		$this->activityTimestamp = Date::now()->getTime();
		$parameters = new Hash();
		$parameters->set(com_wiris_quizzes_telemetry_QuizzesTracker::$KEY_QUESTION, $question->serialize());
		$this->sendInformation(com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$STUDIO_OPENED, $parameters);
	}
	public $activityTimestamp = -1;
	public $studioTimestamp = -1;
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
	static $KEY_QUESTION = "question";
	static $KEY_ACTIVITY = "activity";
	static $KEY_CAUSE = "cause";
	static $KEY_ACTIVITY_TIME = "activity-time";
	static $KEY_SESSION_TIME = "session-time";
	static $KEY_FROM = "from";
	static $KEY_TO = "to";
	static $VALUE_CANCEL = "cancel";
	static $VALUE_SAVE = "save";
	static function getIndex($topic) {
		if($topic === com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$STUDIO_OPENED) {
			return 1;
		} else {
			if($topic === com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$STUDIO_CLOSED) {
				return 2;
			} else {
				if($topic === com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$HELP_REQUESTED) {
					return 3;
				} else {
					if($topic === com_wiris_quizzes_telemetry_QuizzesTrackingTopic::$ACTIVITY_SWITCH) {
						return 4;
					}
				}
			}
		}
		return -1;
	}
	function __toString() { return 'com.wiris.quizzes.telemetry.QuizzesTracker'; }
}
