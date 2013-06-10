<?php

class calendarsystem_plugin_gregorian extends calendarsystem_plugin_base
{
	public function calendar_days_in_month($m, $y)
	{
	    return intval(date('t', mktime(0, 0, 0, $m, 1, $y)));
	}

	public function usergetdate($time, $timezone=99) {
		return usergetdate_old($time, $timezone);
	}

	public function checkdate($m, $d, $y)
	{
		return checkdate($m, $d, $y);
	}

	public function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {
		return make_timestamp_old($year, $month, $day, $hour, $minute, $second, $timezone, $applydst);
	}

    public function userdate($date, $format='', $timezone=99, $fixday = true, $fixhour = true) {
        static $amstring = null, $pmstring = null, $AMstring = null, $PMstring = null;

        if (!$amstring) {
            $amstring = get_string('am', 'calendarsystem_gregorian');
            $pmstring = get_string('pm', 'calendarsystem_gregorian');
            $AMstring = get_string('am_caps', 'calendarsystem_gregorian');
            $PMstring = get_string('pm_caps', 'calendarsystem_gregorian');
        }

        $format = str_replace( array(
                                    "%p",
                                    "%P"
                                    ),
                               array(
                                    ($date["hours"] < 12 ? $AMstring : $PMstring),
                                    ($date["hours"] < 12 ? $amstring : $pmstring)
                                    ),
                              $format);

        return userdate_old($date, $format, $timezone, $fixday, $fixhour);
    }

	public function today()
	{
		list($y, $m, $d) = explode( "-", date("Y-m-d"));

		return array((int)$m, (int)$d, (int)$y);
	}

	public function get_month_names()
	{
		$months = array();

	    for ($i=1; $i<=12; $i++) {
	        $months[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), '%B');
	    }

	    return $months;
	}

	public function get_min_year()
	{
		return 1970;
	}

	public function get_max_year()
	{
		return 2020;
	}

    public function gmmktime($hour=null, $minute=null, $second=null, $month=null, $day=null, $year=null) {
        return gmmktime($hour, $minute, $second, $month, $day, $year);
    }

    public function mktime($hour=null, $minute=null, $second=null, $month=null, $day=null, $year=null) {
        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    function dayofweek($day, $month, $year) {
        // I wonder if this is any different from
        // strftime('%w', mktime(12, 0, 0, $month, $daysinmonth, $year, 0));
        return intval(date('w', mktime(12, 0, 0, $month, $day, $year)));
    }
}
?>