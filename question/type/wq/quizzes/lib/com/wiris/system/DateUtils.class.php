<?php

class com_wiris_system_DateUtils {
	public function __construct(){}
	static function getUTCTime() {
		return (time()*1000) + (date('Z')*1000);
	}
	static function getUTCDate() {
		$time = (time()*1000) + (date('Z')*1000);
		$mili = _hx_mod($time, 1000);
		$dateTime = Date::fromTime($time);
		$date = $dateTime->toString();
		$formattedDate = str_replace(" ", "T", $date);
		$formattedDate .= "." . _hx_string_rec($mili, "") . "Z";
		return $formattedDate;
	}
	function __toString() { return 'com.wiris.system.DateUtils'; }
}
