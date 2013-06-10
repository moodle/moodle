<?php

class calendarsystem_plugin_hijri extends calendarsystem_plugin_base
{
/*
    private function isleap($year) {
        return (((year * 11) + 14) % 30) < 11;
    }
*/
    public function calendar_days_in_month($m, $y)
    {
        $temp = $this->to_gregorian(1, $m+1, $y);
        $temp = $this->from_gregorian($temp['day']-1, $temp['month'], $temp['year']);
        return $temp['day'];
    }

    public function usergetdate($time, $timezone=99) {
        $date = usergetdate_old($time);
        $new_date = $this->from_gregorian($date["mday"], $date["mon"], $date["year"]);

        $date["month"] = get_string("month{$new_date['month']}", 'calendarsystem_hijri');
        $date["weekday"] = get_string("weekday{$date['wday']}", 'calendarsystem_hijri');
        $date["yday"] = null;
        $date["year"] = $new_date['year'];
        $date["mon"] = $new_date['month'];
        $date["mday"] = $new_date['day'];

        return $date;
    }

    public function checkdate($m, $d, $y)
    {
        $temp = $this->to_gregorian($d, $m, $y);
        $temp = $this->from_gregorian($temp['day'], $temp['month'], $temp['year']);
        return ($temp['day'] == $d) && ($temp['month'] == $m) && ($temp['year'] == $y);
    }

	public function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {
		$new_date = $this->to_gregorian($day, $month, $year);
		return make_timestamp_old($new_date['year'], $new_date['month'], $new_date['day'], $hour, $minute, $second, $timezone, $applydst);
	}

    public function userdate($date, $format='', $timezone=99, $fixday = true, $fixhour = true) {
        static $amstring = null, $pmstring = null, $AMstring = null, $PMstring = null;

        if (!$amstring) {
            $amstring = get_string('am', 'calendarsystem_hijri');
            $pmstring = get_string('pm', 'calendarsystem_hijri');
            $AMstring = get_string('am_caps', 'calendarsystem_hijri');
            $PMstring = get_string('pm_caps', 'calendarsystem_hijri');
        }

	    if (empty($format)) {
			$format = get_string('strftimedaydatetime');
		}

		if (!empty($CFG->nofixday)) {  // Config.php can force %d not to be fixed.
            $fixday = false;
        }

        $date_ = $this->usergetdate($date);
		//this is not sufficient code, change it. but it works correctly.
        $format = str_replace( array(
                                    "%a",
                                    "%A",
                                    "%b",
                                    "%B",
                                    "%d",
                                    "%m",
                                    "%y",
                                    "%Y",
                                    "%p",
                                    "%P"
                                    ),
                               array(
                                    $date_["weekday"],
                                    $date_["weekday"],
                                    $date_["month"],
                                    $date_["month"],
                                    (($date_["mday"] < 10 && !$fixday) ? '0' : '') . $date_["mday"],
                                    ($date_["mon"] < 10 ? '0' : '') . $date_["mon"],
                                    $date_["year"] % 100,
                                    $date_["year"],
                                    ($date_["hours"] < 12 ? $AMstring : $PMstring),
                                    ($date_["hours"] < 12 ? $amstring : $pmstring)
                                    ),
    						  $format);

		return userdate_old($date, $format, $timezone, $fixday, $fixhour);
	}

	public function today()
	{
	    list($g_y, $g_m, $g_d) = explode( "-", date("Y-m-d"));
	    $today = $this->from_gregorian((int)$g_d, (int)$g_m, (int)$g_y);

		return array($today['month'], $today['day'], $today['year']);
	}

    public function get_month_names()
    {
        $months = array();

        for ($i=1; $i<=12; $i++) {
            $months[$i] = get_string("month{$i}", 'calendarsystem_hijri');
        }

        return $months;
    }

	public function get_min_year()
	{
		return 1390;
	}

	public function get_max_year()
	{
		return 1440;
	}

    public function gmmktime($hour=null, $minute=null, $second=null, $month=null, $day=null, $year=null) {
        if (empty($day) || empty($month) || empty($year)) {
            $today = $this->today();
            if (empty($day)) {
                $day = $today['day'];
            }
            if (empty($month)) {
                $month = $today['month'];
            }
            if (empty($year)) {
                $year = $today['year'];
            }
        }

        $g_date = $this->to_gregorian($day, $month, $year);

        return gmmktime($hour, $minute, $second, $g_date['month'], $g_date['day'], $g_date['year']);
    }

    public function mktime($hour=null, $minute=null, $second=null, $month=null, $day=null, $year=null) {
        if (empty($day) || empty($month) || empty($year)) {
            $today = $this->today();

            if (empty($day)) {
                $day = $today['day'];
            }
            if (empty($month)) {
                $month = $today['month'];
            }
            if (empty($year)) {
                $year = $today['year'];
            }
        }

        $g_date = $this->to_gregorian($day, $month, $year);

        return mktime($hour, $minute, $second, $g_date['month'], $g_date['day'], $g_date['year']);
    }

    public function dayofweek($day, $month, $year) {
        $g_date = $this->to_gregorian($day, $month, $year);
        return intval(date('w', mktime(12, 0, 0, $g_date['month'], $g_date['day'], $g_date['year'])));
    }

    private $ISLAMIC_EPOCH = 1948439.5;
    private $GREGORIAN_EPOCH = 1721425.5;

    //  LEAP_GREGORIAN  --  Is a given year in the Gregorian calendar a leap year ?
    private function leap_gregorian($year)
    {
        return (($year % 4) == 0) &&
                (!((($year % 100) == 0) && (($year % 400) != 0)));
    }

    //  GREGORIAN_TO_JD  --  Determine Julian day number from Gregorian calendar date
    private function gregorian_to_jd($year, $month, $day)
    {
        return ($this->GREGORIAN_EPOCH - 1) +
               (365 * ($year - 1)) +
               floor(($year - 1) / 4) +
               (-floor(($year - 1) / 100)) +
               floor(($year - 1) / 400) +
               floor((((367 * $month) - 362) / 12) +
               (($month <= 2) ? 0 : ($this->leap_gregorian($year) ? -1 : -2)
               ) +
               $day);
    }

    //  JD_TO_GREGORIAN  --  Calculate Gregorian calendar date from Julian day
    private function jd_to_gregorian($jd) {
        $wjd = floor($jd - 0.5) + 0.5;
        $depoch = $wjd - $this->GREGORIAN_EPOCH;
        $quadricent = floor($depoch / 146097);
        $dqc = $depoch % 146097;
        $cent = floor($dqc / 36524);
        $dcent = $dqc % 36524;
        $quad = floor($dcent / 1461);
        $dquad = $dcent % 1461;
        $yindex = floor($dquad / 365);
        $year = ($quadricent * 400) + ($cent * 100) + ($quad * 4) + $yindex;
        if (!(($cent == 4) || ($yindex == 4))) {
            $year++;
        }
        $yearday = $wjd - $this->gregorian_to_jd($year, 1, 1);
        $leapadj = (($wjd < $this->gregorian_to_jd($year, 3, 1)) ? 0 : ($this->leap_gregorian($year) ? 1 : 2));
        $month = floor(((($yearday + $leapadj) * 12) + 373) / 367);
        $day = ($wjd - $this->gregorian_to_jd($year, $month, 1)) + 1;

        return array('year' => $year,
                    'month' => $month,
                    'day' => $day);
    }


    private function islamic_to_jd($year, $month, $day)
    {
        return ($day +
                ceil(29.5 * ($month - 1)) +
                ($year - 1) * 354 +
                floor((3 + (11 * $year)) / 30) +
                $this->ISLAMIC_EPOCH) - 1;
    }



    //  JD_TO_ISLAMIC  --  Calculate Islamic date from Julian day
    private function jd_to_islamic($jd)
    {
        $jd = floor($jd) + 0.5;
        $year = floor(((30 * ($jd - $this->ISLAMIC_EPOCH)) + 10646) / 10631);
        $month = min(12,
                    ceil(($jd - (29 + $this->islamic_to_jd($year, 1, 1))) / 29.5) + 1);
        $day = ($jd - $this->islamic_to_jd($year, $month, 1)) + 1;

        return array('year' => $year,
                    'month' => $month,
                    'day' => $day);
    }

    private function from_gregorian($g_d, $g_m, $g_y) {
        $jd = $this->gregorian_to_jd($g_y, $g_m, $g_d);
        return $this->jd_to_islamic($jd);
	}

	private function to_gregorian($i_d, $i_m, $i_y) {
	    $jd = $this->islamic_to_jd($i_y, $i_m, $i_d);
	    return $this->jd_to_gregorian($jd);
    }
}
?>