<?php 
/***
 *** olson_simple_parser($filename)
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

    $months = array('jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' =>  4, 'may' =>  5, 'jun' =>  6,
                    'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12);
    $days = array('sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6);

    // now reformat it a bit to match Moodle's DST table
    $moodle_rules = array();
    foreach ($rules as $family => $rulesbyyear) {
        foreach ($rulesbyyear as $year => $rulesthisyear) {

            if(!isset($rulesthisyear['set']) || !isset($rulesthisyear['reset'])) {
                // What are we supposed to do with this???
                print_object($family.' - '.$year.' was rejected');
                continue;
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
            list($hours, $mins) = explode(':', $at);
            $at = sprintf('%02d:%02d', $hours, $mins);
            $in = strtolower($in);
            if(!isset($months[$in])) {
                error('Unknown month: '.$in);
            }
    
            $moodle_rule['family'] = $name;
            $moodle_rule['year'] = $year;
            $moodle_rule['apply_offset']   = $save; // time offset

            $moodle_rule['activate_month'] = $months[$in]; // the month
            $moodle_rule['activate_time']  = $at; // the time

            // Encode index and day as per Moodle's specs
            if(is_numeric($on)) {
                $moodle_rule['activate_index'] = $on;
                $moodle_rule['activate_day']   = -1;
            }
            else {
                $on = strtolower($on);
                if(substr($on, 0, 4) == 'last') {
                    // e.g. lastSun
                    if(!isset($days[substr($on, 4)])) {
                        error('Unknown last weekday: '.substr($on, 4));
                    }
                    else {
                        $moodle_rule['activate_index'] = -1;
                        $moodle_rule['activate_day']   = $days[substr($on, 4)];
                    }
                }
                else if(substr($on, 3, 2) == '>=') {
                    // e.g. Sun>=8
                    if(!isset($days[substr($on, 0, 3)])) {
                        error('Unknown last weekday: '.substr($on, 0, 3));
                    }
                    else {
                        $moodle_rule['activate_index'] = substr($on, 5);
                        $moodle_rule['activate_day']   = $days[substr($on, 0, 3)];
                    }
                }
                else {
                    error('unknown on '.$on);
                }
            }

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
    
            list($hours, $mins) = explode(':', $at);
            $at = sprintf('%02d:%02d', $hours, $mins);
            $in = strtolower($in);
            if(!isset($months[$in])) {
                error('Unknown month: '.$in);
            }

            $moodle_rule['deactivate_month'] = $months[$in]; // the month
            $moodle_rule['deactivate_time']  = $at; // the time
    
            // Encode index and day as per Moodle's specs
            if(is_numeric($on)) {
                $moodle_rule['deactivate_index'] = $on;
                $moodle_rule['deactivate_day']   = -1;
            }
            else {
                $on = strtolower($on);
                if(substr($on, 0, 4) == 'last') {
                    // e.g. lastSun
                    if(!isset($days[substr($on, 4)])) {
                        error('Unknown last weekday: '.substr($on, 4));
                    }
                    else {
                        $moodle_rule['deactivate_index'] = -1;
                        $moodle_rule['deactivate_day']   = $days[substr($on, 4)];
                    }
                }
                else if(substr($on, 3, 2) == '>=') {
                    // e.g. Sun>=8
                    if(!isset($days[substr($on, 0, 3)])) {
                        error('Unknown last weekday: '.substr($on, 0, 3));
                    }
                    else {
                        $moodle_rule['deactivate_index'] = substr($on, 5);
                        $moodle_rule['deactivate_day']   = $days[substr($on, 0, 3)];
                    }
                }
                else {
                    error('unknown on '.$on);
                }
            }
    
            $moodle_rules[] = $moodle_rule;
            print_object($moodle_rule);
        }

    }

    return $moodle_rules;
}

?>