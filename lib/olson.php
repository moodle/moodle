<?php //$Id$

/***
 *** olson_todst ($filename)
 ***
 *** Parses the olson files for Zones and DST rules.
 *** It updates the Moodle database with the Zones/DST rules
 ***
 *** Returns true/false
 ***
 */
function olson_todst ($filename) {

    $zones = olson_simple_zone_parser($filename);
    $rules = olson_simple_rule_parser($filename);
    
    $mdl_zones = array();

    /**
     *** To translate the combined Zone & Rule changes
     *** in the Olson files to the Moodle single ruleset
     *** format, we need to trasverse every year and see
     *** if either the Zone or the relevant Rule has a 
     *** change. It's yuck but it yields a rationalized
     *** set of data, which is arguably simpler.
     ***
     *** Also note that I am starting at the epoch (1970)
     *** because I don't think we'll see many events scheduled
     *** before that, anyway. 
     ***
     **/
    $maxyear = localtime(time(), true);
    $maxyear = $maxyear['tm_year'] + 1900 + 10;

    foreach ($zones as $zname=>$zbyyear) { // loop over zones
        /**
         *** Loop over years, only adding a rule when zone or rule
         *** have changed. All loops preserver the last seen vars
         *** until there's an explicit decision to delete them
         *** 
         **/ 

        // clean the slate for a new zone
        $zone = NULL;
        $rule = NULL;
        
        //
        // Find the pre 1970 zone rule entries
        //
        for ($y = 1970 ; $y > 0 ; $y--) {
            if (array_key_exists((string)$y, $zbyyear )) { // we have a zone entry for the year
                $zone = $zbyyear[$y];
                //print_object("Zone $zname pre1970 is in  $y\n");
                break; // Perl's last -- get outta here
            }
        }
        if (!empty($zone['rule'])) {
            $rule = NULL;
            for ($y = 1970 ; $y > 0 ; $y--) {
                if (array_key_exists((string)$y, $rules[$zone['rule']] )) { // we have a rule entry for the year                    
                    $rule  =  $rules[$zone['rule']][$y];
                    //print_object("Rule $rule[name] pre1970 is $y\n");
                    break; // Perl's last -- get outta here
                }
                
            }  
            if (empty($rule)) {
                // Colombia and a few others refer to rules before they exist 
                // Perhaps we should comment out this warning... 
                // trigger_error("Cannot find rule in $zone[rule] <= 1970");
                $rule  = array();
            }
        } else {
            // no DST this year!
            $rule  = array();
        }
        
        // Prepare to insert the base 1970 zone+rule        
        if (!empty($rule)) {
            // merge the two arrays into the moodle rule
            unset($rule['name']); // warning: $rule must NOT be a reference! 
            unset($rule['year']);
            $mdl_tz = array_merge($zone, $rule);

            //fix (de)activate_time (AT) field to be GMT
            $mdl_tz['dst_time'] = olson_parse_at($mdl_tz['dst_time'], 'set',   $mdl_tz['gmtoff']);
            $mdl_tz['std_time'] = olson_parse_at($mdl_tz['std_time'], 'reset', $mdl_tz['gmtoff']);
        } else {
            // just a simple zone
            $mdl_tz = $zone;
            // TODO: Add other default values here!
            $mdl_tz['dstoff'] = 0;
        }        

        // Fix the from year to 1970
        $mdl_tz['from'] = 1970;

        // add to the array
        $mdl_zones[] = $mdl_tz;
        //print_object("Zero entry for $zone[name] added");

        ///
        /// 1971 onwards
        /// 
        for ($y = 1971; $y < $maxyear ; $y++) {
            $changed = false;
            ///
            /// We create a "zonerule" entry if either zone or rule change...
            ///
            /// force $y to string to avoid PHP
            /// thinking of a positional array
            ///
            if (array_key_exists((string)$y, $zbyyear)) { // we have a zone entry for the year
                $changed = true;
                $zone    = $zbyyear[(string)$y];
            }
            if (!empty($zone['rule'])){                
                if (array_key_exists((string)$y, $rules[$zone['rule']])) {
                    $changed = true;
                    $rule    = $rules[$zone['rule']][(string)$y];
                }
            } else {
                $rule = array();
            }

            if ($changed) {
                //print_object("CHANGE YEAR $y Zone $zone[name] Rule $zone[rule]\n");
                if (!empty($rule)) {
                    // merge the two arrays into the moodle rule
                    unset($rule['name']); 
                    unset($rule['year']);
                    $mdl_tz = array_merge($zone, $rule);

                    // VERY IMPORTANT!!
                    $mdl_tz['from'] = $y;

                    //fix (de)activate_time (AT) field to be GMT
                    $mdl_tz['dst_time'] = olson_parse_at($mdl_tz['dst_time'], 'set',   $mdl_tz['gmtoff']);
                    $mdl_tz['std_time'] = olson_parse_at($mdl_tz['std_time'], 'reset', $mdl_tz['gmtoff']);
                } else {
                    // just a simple zone
                    $mdl_tz = $zone;
                } 

/*
if(isset($mdl_tz['dst_time']) && !strpos($mdl_tz['dst_time'], ':') || isset($mdl_tz['std_time']) &&  !strpos($mdl_tz['std_time'], ':')) {
    print_object($mdl_tz);
    print_object('---');
}
*/

                $mdl_zones[] = $mdl_tz;
            }
        } 
        
    }

    /* 
    if (function_exists('memory_get_usage')) {
        trigger_error("We are consuming this much memory: " . get_memory_usage());
    }
    */

    return $mdl_zones;
}


/***
 *** olson_simple_rule_parser($filename)
 ***
 *** Parses the olson files for DST rules.
 *** It's a simple implementation that simplifies some fields
 ***
 *** Returns a multidimensional array, or false on error
 ***
 */
function olson_simple_rule_parser ($filename) {

    $file = fopen($filename, 'r', 0); 

    if (empty($file)) {
        return false;
    }
    
    $rules = array();
    while ($line = fgets($file)) {
        // only pay attention to rules lines
        if(!preg_match('/^Rule\s/', $line)){
            continue;
        }
        $line = preg_replace('/\n$/', '',$line); // chomp
        $rule = preg_split('/\s+/', $line);
        list($discard,
             $name,
             $from,
             $to,
             $type,
             $in,
             $on,
             $at,
             $save,
             $letter) = $rule;

        $srs = ($save === '0') ? 'reset' : 'set';
        $rules[$name][$from][$srs] = $rule;

        /*
        if($to == 'only' || $to == 'max') {
            // In both cases, a single entry for one year will suffice
            $to = $from;
        }

        for($i = $from; $i <= $to; ++$i) {
            $rules[$name][$i][$srs] = $rule;
        }
        */
        
    }

    fclose($file);

    $months = array('jan' =>  1, 'feb' =>  2, 
                    'mar' =>  3, 'apr' =>  4, 
                    'may' =>  5, 'jun' =>  6,
                    'jul' =>  7, 'aug' =>  8, 
                    'sep' =>  9, 'oct' => 10, 
                    'nov' => 11, 'dec' => 12);


    // now reformat it a bit to match Moodle's DST table
    $moodle_rules = array();
    foreach ($rules as $rule => $rulesbyyear) {
        foreach ($rulesbyyear as $year => $rulesthisyear) {

            if(!isset($rulesthisyear['reset'])) {
                // No "reset" rule. We will assume that this is somewhere in the southern hemisphere
                // after a period of not using DST, otherwise it doesn't make sense at all.
                // With that assumption, we can put in a fake reset e.g. on Jan 1, 12:00.
                /*
                print_object("no reset");
                print_object($rules);
                die();
                */
                $rulesthisyear['reset'] = array(
                    NULL, NULL, NULL, NULL, NULL, 'jan', 1, '12:00', '00:00', NULL
                );
            }

            if(!isset($rulesthisyear['set'])) {
                // No "set" rule. We will assume that this is somewhere in the southern hemisphere
                // and that it begins a period of not using DST, otherwise it doesn't make sense at all.
                // With that assumption, we can put in a fake set on Dec 31, 12:00, shifting time by 0 minutes.
                $rulesthisyear['set'] = array(
                    NULL, $rulesthisyear['reset'][1], NULL, NULL, NULL, 'dec', 31, '12:00', '00:00', NULL
                );
            }

            list($discard,
                 $name,
                 $from,
                 $to,
                 $type,
                 $in,
                 $on,
                 $at,
                 $save,
                 $letter) = $rulesthisyear['set'];
    
            $moodle_rule = array();
    
            list($hours, $mins) = explode(':', $save);
            $save = $hours * 60 + $mins;
            // we'll parse $at later
            // $at = olson_parse_at($at); 
            $in = strtolower($in);
            if(!isset($months[$in])) {
                trigger_error('Unknown month: '.$in);
            }
    
            $moodle_rule['name']   = $name;
            $moodle_rule['year']   = $year;
            $moodle_rule['dstoff'] = $save; // time offset

            $moodle_rule['dst_month'] = $months[$in]; // the month
            $moodle_rule['dst_time']  = $at; // the time

            // Encode index and day as per Moodle's specs
            $on = olson_parse_on($on);
            $moodle_rule['dst_startday']  = $on['startday'];
            $moodle_rule['dst_weekday']   = $on['weekday'];
            $moodle_rule['dst_skipweeks'] = $on['skipweeks'];
            
            // and now the "deactivate" data
            list($discard,
                 $name,
                 $from,
                 $to,
                 $type,
                 $in,
                 $on,
                 $at,
                 $save,
                 $letter) = $rulesthisyear['reset'];
    
            // we'll parse $at later
            // $at = olson_parse_at($at);
            $in = strtolower($in);
            if(!isset($months[$in])) {
                trigger_error('Unknown month: '.$in);
            }

            $moodle_rule['std_month'] = $months[$in]; // the month
            $moodle_rule['std_time']  = $at; // the time
    
            // Encode index and day as per Moodle's specs
            $on = olson_parse_on($on);
            $moodle_rule['std_startday']  = $on['startday'];
            $moodle_rule['std_weekday']   = $on['weekday'];
            $moodle_rule['std_skipweeks'] = $on['skipweeks'];
                
            $moodle_rules[$moodle_rule['name']][$moodle_rule['year']] = $moodle_rule;
            //print_object($moodle_rule);
        }

    }

    return $moodle_rules;
}

/***
 *** olson_simple_zone_parser($filename)
 ***
 *** Parses the olson files for zone info
 ***
 *** Returns a multidimensional array, or false on error
 ***
 */
function olson_simple_zone_parser ($filename) {

    $file = fopen($filename, 'r', 0); 

    if (empty($file)) {
        return false;
    }
    
    $zones = array();
    $lastzone = NULL;

    while ($line = fgets($file)) {
        // skip obvious non-zone lines
        if (preg_match('/^(?:#|Rule|Link|Leap)/',$line)) {
            $lastzone = NULL; // reset lastzone
            continue;
        }

        // If there are blanks in the start of the line but the first non-ws character is a #,
        // assume it's an "inline comment". The funny thing is that this happens only during
        // the definition of the Rule for Europe/Athens.
        if(substr(trim($line), 0, 1) == '#') {
            continue;
        }

        /*** Notes
         ***
         *** By splitting on space, we are only keeping the
         *** year of the UNTIL field -- that's on purpose.
         ***
         *** The Zone lines are followed by continuation lines
         *** were we reuse the info from the last one seen.
         ***
         *** We are transforming "until" fields into "from" fields
         *** which make more sense from the Moodle perspective, so
         *** each initial Zone entry is "from" the year 0, and for the 
         *** continuation lines, we shift the "until" from the previous field
         *** into this line's "from".
         ***
         *** If a RULES field contains a time instead of a rule we discard it
         *** I have no idea of how to create a DST rule out of that
         *** (what are the start/end times?)
         ***
         *** We remove "until" from the data we keep, but preserve 
         *** it in $lastzone.
         */
        if (preg_match('/^Zone/', $line)) { // a new zone
            $line = trim($line);
            $line = preg_split('/\s+/', $line);
            $zone = array();
            list( $discard, // 'Zone'
                  $zone['name'],
                  $zone['gmtoff'],
                  $zone['rule'],
                  $discard // format
                  ) = $line;
            // the things we do to avoid warnings
            if (!empty($line[5])) { 
                $zone['until'] = $line[5];
            }
            $zone['from'] = '0';
            
            $zones[$zone['name']] = array();

        } else if (!empty($lastzone) && preg_match('/^\s+/', $line)){ 
            // looks like a credible continuation line  
            $line = trim($line);
            $line = preg_split('/\s+/', $line);
            if (count($line) < 3) {
                $lastzone = NULL;
                continue;
            }
            // retrieve info from the lastzone
            $zone = $lastzone;
            $zone['from'] = $zone['until'];
            // overwrite with current data
            list(
                  $zone['gmtoff'],
                  $zone['rule'],
                  $discard // format
                  ) = $line;
            // the things we do to avoid warnings
            if (!empty($line[3])) { 
                $zone['until'] = $line[3];
            }

        } else {
            $lastzone = NULL;
            continue;
        }

        // tidy up, we're done 
        // perhaps we should insert in the DB at this stage?
        $lastzone = $zone;
        unset($zone['until']);
        $zone['gmtoff'] = olson_parse_offset($zone['gmtoff']);
        if ($zone['rule'] === '-') { // cleanup empty rules
            $zone['rule'] = '';
        }
        if (preg_match('/:/',$zone['rule'])) { 
            // we are not handling direct SAVE rules here
            // discard it
            $zone['rule'] = '';
        }
        
        $zones[$zone['name']][(string)$zone['from']] = $zone;
    }

    return $zones;
}

/***
 *** olson_parse_offset($offset)
 ***
 *** parses time offsets from the GMTOFF and SAVE
 *** fields into +/-MINUTES 
 */
function olson_parse_offset ($offset) {
    $offset = trim($offset);
    
    // perhaps it's just minutes
    if (preg_match('/^(-?)(\d*)$/', $offset)) {
        return $offset;
    }
    // (-)hours:minutes(:seconds) 
    if (preg_match('/^(-?)(\d*):(\d+)/', $offset, $matches)) {
        // we are happy to discard the seconds
        $sign    = $matches[1];
        $hours   = (int)$matches[2];
        $seconds = (int)$matches[3];
        $offset  = $sign . ($hours*60 + $seconds);
        return $offset;
    } 

    trigger_error('Strange time format in olson_parse_offset() ' .$offset);
    return 0;

}


/***
 *** olson_parse_on_($on)
 ***
 *** see `man zic`. This function translates the following formats 
 *** 5        the fifth of the month
 *** lastSun  the last Sunday in the month
 *** lastMon  the last Monday in the month
 *** Sun>=8   first Sunday on or after the eighth
 *** Sun<=25  last Sunday on or before the 25th
 ***
 *** to a moodle friendly format. Returns an array with:
 ***
 *** startday: the day of the month that we start counting from.
 ***           if negative, it means we start from that day and
 ***           count backwards. since -1 would be meaningless,
 ***           it means "end of month and backwards".
 *** weekday:  the day of the week that we must find. we will
 ***           scan days from the startday until we find the
 ***           first such weekday. 0...6 = Sun...Sat.
 ***           -1 means that any day of the week will do,
 ***           effectively ending the search on startday.
 *** skipweeks:after finding our end day as outlined above,
 ***           skip this many weeks. this enables us to find
 ***           "the second sunday >= 10". usually will be 0.
 */
function olson_parse_on ($on) {

    $rule = array();
    $days = array('sun' => 0, 'mon' => 1, 
                  'tue' => 2, 'wed' => 3, 
                  'thu' => 4, 'fri' => 5, 
                  'sat' => 6);

    if(is_numeric($on)) {
        $rule['startday']  = intval($on); // Start searching from that day
        $rule['weekday']   = -1;          // ...and stop there, no matter what weekday
        $rule['skipweeks'] = 0;           // Don't skip any weeks.
    }
    else {
        $on = strtolower($on);
        if(substr($on, 0, 4) == 'last') {
            // e.g. lastSun
            if(!isset($days[substr($on, 4)])) {
                trigger_error('Unknown last weekday: '.substr($on, 4));
            }
            else {
                $rule['startday']  = -1;                    // Start from end of month
                $rule['weekday']   = $days[substr($on, 4)]; // Find the first such weekday
                $rule['skipweeks'] = 0;                     // Don't skip any weeks.
            }
        }
        else if(substr($on, 3, 2) == '>=') {
            // e.g. Sun>=8
            if(!isset($days[substr($on, 0, 3)])) {
                trigger_error('Unknown >= weekday: '.substr($on, 0, 3));
            }
            else {
                $rule['startday']  = intval(substr($on, 5));   // Start from that day of the month
                $rule['weekday']   = $days[substr($on, 0, 3)]; // Find the first such weekday
                $rule['skipweeks'] = 0;                        // Don't skip any weeks.
            }
        }
        else if(substr($on, 3, 2) == '<=') {
            // e.g. Sun<=25
            if(!isset($days[substr($on, 0, 3)])) {
                trigger_error('Unknown <= weekday: '.substr($on, 0, 3));
            }
            else {
                $rule['startday']  = -intval(substr($on, 5));  // Start from that day of the month; COUNT BACKWARDS (minus sign)
                $rule['weekday']   = $days[substr($on, 0, 3)]; // Find the first such weekday
                $rule['skipweeks'] = 0;                        // Don't skip any weeks.
            }
        }
        else {
            trigger_error('unknown on '.$on);
        }
    }    
    return $rule;
}


/***
 *** olson_parse_at($at, $set, $gmtoffset)
 ***
 *** see `man zic`. This function translates
 ***
 ***      2        time in hours
 ***      2:00     time in hours and minutes
 ***     15:00     24-hour format time (for times after noon)
 ***      1:28:14  time in hours, minutes, and seconds
 ***
 ***  Any of these forms may be followed by the letter w if the given
 ***  time is local "wall clock" time, s if the given time  is  local
 ***  "standard"  time, or u (or g or z) if the given time is univer-
 ***  sal time; in the absence of an indicator, wall  clock  time  is
 ***  assumed.
 ***
 *** returns a moodle friendly $at, in GMT, which is what Moodle wants
 ***
 *** 
 */
function olson_parse_at ($at, $set = 'set', $gmtoffset) {

    // find the time "signature";
    $sig = '';
    if (preg_match('/\w$/', $at, $matches)) {
        $sig = $matches[0];
        $at  = substr($at, 0, strlen($at)-1); // chop
    }

    list($hours, $mins) = explode(':', $at);

    // GMT -- return as is!
    if ( !empty($sig) && ( $sig === 'u'
                           || $sig === 'g'
                           || $sig === 'z'    )) {
        return $at;
    }

    // Wall clock
    if (empty($sig) || $sig === 'w') { 
        if ($set !== 'set'){ // wall clock is on DST, assume by 1hr
            $hours = $hours-1;
        } 
        $sig = 's';
    }

    // Standard time
    if (!empty($sig) && $sig === 's') {
        $mins = $mins + $hours*60 + $gmtoffset;
        $hours = $mins / 60;
        $hours = (int)$hours;
        $mins  = abs($mins % 60);
        return sprintf('%02d:%02d', $hours, $mins);
    }

    trigger_error('unhandled case - AT flag is ' .  $matches[0]);
}


?>