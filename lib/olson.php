<?php //$Id$
/***
 *** olson_simple_rule_parser($filename)
 ***
 *** Parses the olson files for DST rules.
 *** It's a simple implementation that captures the 
 *** most up-to-date DST rule for each ruleset.
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

        if(intval($to) == 0) {
            $to = $from;
        }

        for($i = $from; $i <= $to; ++$i) {
            $rules[$name][$i][$srs] = $rule;
        }
        
    }

    fclose($file);

    $months = array('jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' =>  4, 'may' =>  5, 'jun' =>  6,
                    'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12);


    // now reformat it a bit to match Moodle's DST table
    $moodle_rules = array();
    foreach ($rules as $family => $rulesbyyear) {
        foreach ($rulesbyyear as $year => $rulesthisyear) {

            if(!isset($rulesthisyear['reset'])) {
                // No "reset" rule. We will assume that this is somewhere in the southern hemisphere
                // after a period of not using DST, otherwise it doesn't make sense at all.
                // With that assumption, we can put in a fake reset e.g. on Jan 1, 12:00.
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
            $at = olson_parse_at($at);
            $in = strtolower($in);
            if(!isset($months[$in])) {
                trigger_error('Unknown month: '.$in);
            }
    
            $moodle_rule['family'] = $name;
            $moodle_rule['year'] = $year;
            $moodle_rule['apply_offset']   = $save; // time offset

            $moodle_rule['activate_month'] = $months[$in]; // the month
            $moodle_rule['activate_time']  = $at; // the time

            // Encode index and day as per Moodle's specs
            $on = olson_parse_on($on);
            $moodle_rule['activate_index'] = $on['index'];
            $moodle_rule['activate_day']   = $on['day'];
            
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
    
            $at = olson_parse_at($at);
            $in = strtolower($in);
            if(!isset($months[$in])) {
                trigger_error('Unknown month: '.$in);
            }

            $moodle_rule['deactivate_month'] = $months[$in]; // the month
            $moodle_rule['deactivate_time']  = $at; // the time
    
            // Encode index and day as per Moodle's specs
            $on = olson_parse_on($on);

            $moodle_rule['deactivate_index'] = $on['index'];
            $moodle_rule['deactivate_day']   = $on['day'];
                
            $moodle_rules[] = $moodle_rule;
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
         *** each initial Zone entry is "from" 1970, and for the 
         *** continuation lines, we shift the "until" from the previous field
         *** into this line's "from".
         ***
         *** We remove "until" from the data we keep, but preserve 
         *** it in $lastzone
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
            $zone['from'] = '1970';


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
        
        $zones[] = $zone;
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
 *** see `man zic`. This function translated the following formats 
 *** 5        the fifth of the month
 *** lastSun  the last Sunday in the month
 *** lastMon  the last Monday in the month
 *** Sun>=8   first Sunday on or after the eighth
 *** Sun<=25  last Sunday on or before the 25th
 ***
 *** to a moodle friendly format. Returns
 *** array(index =>$index, day =>$day)
 ***
 */
function olson_parse_on ($on) {
    
    $rule = array();
    $days = array('sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6);

    if(is_numeric($on)) {
        $rule['index'] = $on;
        $rule['day']   = -1;
    }
    else {
        $on = strtolower($on);
        if(substr($on, 0, 4) == 'last') {
            // e.g. lastSun
            if(!isset($days[substr($on, 4)])) {
                trigger_error('Unknown last weekday: '.substr($on, 4));
            }
            else {
                $rule['index'] = -1;
                $rule['day']   = $days[substr($on, 4)];
            }
        }
        else if(substr($on, 3, 2) == '>=') {
            // e.g. Sun>=8
            if(!isset($days[substr($on, 0, 3)])) {
                trigger_error('Unknown last weekday: '.substr($on, 0, 3));
            }
            else {
                $rule['index'] = substr($on, 5);
                $rule['day']   = $days[substr($on, 0, 3)];
            }
        }
        else {
            trigger_error('unknown on '.$on);
        }
    }    
    return $rule;
}


/***
 *** olson_parse_at($on)
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
 *** returns a moodle friendly $at
 */
function olson_parse_at ($at, $set = 'set') {

    list($hours, $mins) = explode(':', $at);

    if ( !empty($matches[0]) && (   $matches[0] === 'u'
                                 || $matches[0] === 'g'
                                 || $matches[0] === 'z'    )) {
        return sprintf('%02d:%02d', $hours, $mins);
    }

    // try and fetch the trailing alpha char if present
    if (empty($matches[0]) || $matches[0] === 'w') { 
        // wall clock
        if ($set !== 'set'){ // wall clock is on DST, assume by 1hr
            $hours = $hours-1;
        } 
        trigger_error('TOOD turn this time to gmt');
        return sprintf('%02d:%02d', $hours, $mins);
    }

    trigger_error('unhandled case - AT flag is ' .  $matches[0]);
}


?>