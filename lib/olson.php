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

        
        $id = $name;
        if ($save === '0') {
            $id .= '-reset';
        } else {
            $id .= '-set';
        }

        if (isset($rules[$id])) {
            if ($rules[$id][2] < $from) {
                $rules[$id] = $rule;
            }
        } else {
            $rules[$id] = $rule;
        }
    }

    // now reformat it a bit to match Moodle's DST table
    $moodle_rules = array();
    foreach (array_keys($rules) as $rule) {
        if (preg_match('/-reset$/', $rule)) {
            continue; // we skip these
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
             $letter) = $rules[$rule];

        $moodle_rule = array();
        $moodle_rule['name'] = $name;
        $moodle_rule['apply_offset']   = $save; // time offset
        $moodle_rule['activate_index'] = $on; // the weeknumber 
        $moodle_rule['activate_day']   = $on; // the weekday
        $moodle_rule['activate_month'] = $in; // the month
        $moodle_rule['activate_time']  = $at; // the weeknumber

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
             $letter) = $rules[$name.'-reset'];

        $moodle_rule['deactivate_index'] = $on; // the weeknumber 
        $moodle_rule['deactivate_day']   = $on; // the weekday
        $moodle_rule['deactivate_month'] = $in; // the month
        $moodle_rule['deactivate_time']  = $at; // the weeknumber

        $moodle_rules[$name] = $moodle_rule;

    }

    return $moodle_rules;
}

?>