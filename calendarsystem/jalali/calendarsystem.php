<?php
class calendarsystem_plugin_jalali extends calendarsystem_plugin_base
{
	public function calendar_days_in_month($m, $y)
	{
		if ($m <= 6)
			return 31;
		elseif ($m != 12 or $this->isleap_solar($y))
			return 30;

		return 29;
	}

	public function usergetdate($time, $timezone=99) {
		$date = usergetdate_old($time);
		$new_date = $this->from_gregorian($date["mday"], $date["mon"], $date["year"]);

		$date["month"] = get_string("month{$new_date['month']}", 'calendarsystem_jalali');
		$date["weekday"] = get_string("weekday{$date['wday']}", 'calendarsystem_jalali');
		$date["yday"] = null;
		$date["year"] = $new_date['year'];
		$date["mon"] = $new_date['month'];
		$date["mday"] = $new_date['day'];

		return $date;
}
	
	public function checkdate($m, $d, $y)
	{
		// $m not in 1..12 or $d not in 1..31
		if ($m < 1 or 12 < $m or $d < 1 or $d > 31)
			return false;

        // $m in 1..6 and at this line $d in 1..31
		if ($m < 7)
			return true;

        // $m in 7..11 and possible value for $d is in 0..31 (but 31 is invalid)
		if ($m != 12)
			if ($d == 31) {
				return false;
			} else {
				return true;
			}

        // $m is 12
		if ($this->isleap_solar($y))
		{
			if ($d == 31)
				return false;
		}
		else // $y is not leap year.
            if ($d == 31)
                return false;
		
		return true;
	}

	public function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {
		$new_date = $this->to_gregorian($day, $month, $year);
		return make_timestamp_old($new_date['year'], $new_date['month'], $new_date['day'], $hour, $minute, $second, $timezone, $applydst);
	}

    public function userdate($date, $format='', $timezone=99, $fixday = true, $fixhour = true) {
        static $amstring = null, $pmstring = null, $AMstring = null, $PMstring = null;

        if (!$amstring) {
            $amstring = get_string('am', 'calendarsystem_jalali');
            $pmstring = get_string('pm', 'calendarsystem_jalali');
            $AMstring = get_string('am_caps', 'calendarsystem_jalali');
            $PMstring = get_string('pm_caps', 'calendarsystem_jalali');
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
	        $months[$i] = get_string("month{$i}", 'calendarsystem_jalali');
	    }
	    
	    return $months;
	}

    public function get_min_year()
    {
        return 1350;
    }

	public function get_max_year()
    {
        return 1400;
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


    private $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    private $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    private function isleap_solar($year) {
        /* 33-year cycles, it better matches Iranian rules */
        return (($year+16)%33+33)%33*8%33 < 8;
    }

    private function from_gregorian($g_d, $g_m, $g_y) {
        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400);

        for ($i=0; $i < $gm; ++$i)
          $g_day_no += $this->g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
          /* leap and after Feb */
          ++$g_day_no;
        $g_day_no += $gd;

        $j_day_no = $g_day_no-79;

        $j_np = $this->div($j_day_no, 12053);
        $j_day_no %= 12053;

        $jy = 979+33*$j_np+4*$this->div($j_day_no,1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
          $jy += $this->div($j_day_no-1, 365);
          $j_day_no = ($j_day_no-1)%365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $this->j_days_in_month[$i]; ++$i) {
          $j_day_no -= $this->j_days_in_month[$i];
        }
        $jm = $i+1;
        $jd = $j_day_no+1;


        return array('year' => $jy,
                    'month' => $jm,
                    'day' => $jd);
	}

	private function to_gregorian($j_d, $j_m, $j_y) {
        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;

        $j_day_no = 365*$jy + $this->div($jy, 33)*8 + $this->div($jy%33+3, 4);
        for ($i=0; $i < $jm; ++$i)
            $j_day_no += $this->j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no+79;

        $gy = 1600 + 400*$this->div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
        {
            $g_day_no--;
            $gy += 100*$this->div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4*$this->div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += $this->div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $this->g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $this->g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i+1;
        $gd = $g_day_no+1;

        return array('year' => $gy,
                    'month' => $gm,
                    'day' => $gd);
    }

    private function div($a,$b) {
        return (int) ($a / $b);
    }
}
?>