<?php

/**
 *  BENNU - PHP iCalendar library
 *  (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org). All rights reserved.
 *
 *  Released under the LGPL.
 *
 *  See http://bennu.sourceforge.net/ for more information and downloads.
 *
 * @author Ioannis Papaioannou 
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

/*

   All names of properties, property parameters, enumerated property
   values and property parameter values are case-insensitive. However,
   all other property values are case-sensitive, unless otherwise
   stated.

*/

define('RFC2445_CRLF',               "\r\n");
define('RFC2445_WSP',                "\t ");
define('RFC2445_WEEKDAYS',           'MO,TU,WE,TH,FR,SA,SU');
define('RFC2445_FOLDED_LINE_LENGTH', 75);

define('RFC2445_PARAMETER_SEPARATOR',	';');
define('RFC2445_VALUE_SEPARATOR',    	':');

define('RFC2445_REQUIRED', 0x01);
define('RFC2445_OPTIONAL', 0x02);
define('RFC2445_ONCE',     0x04);

define('RFC2445_PROP_FLAGS',       0);
define('RFC2445_PROP_TYPE',        1);
define('RFC2445_PROP_DEFAULT',     2);

define('RFC2445_XNAME', 'X-');

define('RFC2445_TYPE_BINARY',       0);
define('RFC2445_TYPE_BOOLEAN',      1);
define('RFC2445_TYPE_CAL_ADDRESS',  2);
define('RFC2445_TYPE_DATE',         3);
define('RFC2445_TYPE_DATE_TIME',    4);
define('RFC2445_TYPE_DURATION',     5);
define('RFC2445_TYPE_FLOAT',        6);
define('RFC2445_TYPE_INTEGER',      7);
define('RFC2445_TYPE_PERIOD',       8);
define('RFC2445_TYPE_RECUR',        9);
define('RFC2445_TYPE_TEXT',        10);
define('RFC2445_TYPE_TIME',        11);
define('RFC2445_TYPE_URI',         12); // CAL_ADDRESS === URI
define('RFC2445_TYPE_UTC_OFFSET',  13);


function rfc2445_fold($string) {
    if(core_text::strlen($string, 'utf-8') <= RFC2445_FOLDED_LINE_LENGTH) {
        return $string;
    }

    $retval = '';
  
    $i=0;
    $len_count=0;

    //multi-byte string, get the correct length
    $section_len = core_text::strlen($string, 'utf-8');

    while($len_count<$section_len) {
        
        //get the current portion of the line
        $section = core_text::substr($string, ($i * RFC2445_FOLDED_LINE_LENGTH), (RFC2445_FOLDED_LINE_LENGTH), 'utf-8');

        //increment the length we've processed by the length of the new portion
        $len_count += core_text::strlen($section, 'utf-8');
        
        /* Add the portion to the return value, terminating with CRLF.HTAB
           As per RFC 2445, CRLF.HTAB will be replaced by the processor of the 
           data */
        $retval .= $section.RFC2445_CRLF.RFC2445_WSP;
        
        $i++;
    }

    return $retval;

}

function rfc2445_unfold($string) {
    for($i = 0; $i < strlen(RFC2445_WSP); ++$i) {
        $string = str_replace(RFC2445_CRLF.substr(RFC2445_WSP, $i, 1), '', $string);
    }

    return $string;
}

function rfc2445_is_xname($name) {

    // If it's less than 3 chars, it cannot be legal
    if(strlen($name) < 3) {
        return false;
    }

    // If it contains an illegal char anywhere, reject it
    if(strspn($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-') != strlen($name)) {
        return false;
    }

    // To be legal, it must still start with "X-"
    return substr($name, 0, 2) === 'X-';
}

function rfc2445_is_valid_value($value, $type) {

    // This branch should only be taken with xname values
    if($type === NULL) {
        return true;
    }

    switch($type) {
        case RFC2445_TYPE_CAL_ADDRESS:
        case RFC2445_TYPE_URI:
            if(!is_string($value)) {
                return false;
            }

            $valid_schemes = array('ftp', 'http', 'ldap', 'gopher', 'mailto', 'news', 'nntp', 'telnet', 'wais', 'file', 'prospero');

            $pos = strpos($value, ':');
            if(!$pos) {
                return false;
            }
        
            $scheme = strtolower(substr($value, 0, $pos));
            $remain = substr($value, $pos + 1);
            
            if(!in_array($scheme, $valid_schemes)) {
                return false;
            }
        
            if($scheme === 'mailto') {
                $regexp = '#^[a-zA-Z0-9]+[_a-zA-Z0-9\-]*(\.[_a-z0-9\-]+)*@(([0-9a-zA-Z\-]+\.)+[a-zA-Z][0-9a-zA-Z\-]+|([0-9]{1,3}\.){3}[0-9]{1,3})$#';
            }
            else {
                $regexp = '#^//(.+(:.*)?@)?(([0-9a-zA-Z\-]+\.)+[a-zA-Z][0-9a-zA-Z\-]+|([0-9]{1,3}\.){3}[0-9]{1,3})(:[0-9]{1,5})?(/.*)?$#';
            }
        
            return preg_match($regexp, $remain);
        break;

        case RFC2445_TYPE_BINARY:
            if(!is_string($value)) {
                return false;
            }

            $len = strlen($value);
            
            if($len % 4 != 0) {
                return false;
            }

            for($i = 0; $i < $len; ++$i) {
                $ch = $value{$i};
                if(!($ch >= 'a' && $ch <= 'z' || $ch >= 'A' && $ch <= 'Z' || $ch >= '0' && $ch <= '9' || $ch == '-' || $ch == '+')) {
                    if($ch == '=' && $len - $i <= 2) {
                        continue;
                    }
                    return false;
                }
            }
            return true;
        break;

        case RFC2445_TYPE_BOOLEAN:
            if(is_bool($value)) {
                return true;
            }
            if(is_string($value)) {
                $value = strtoupper($value);
                return ($value == 'TRUE' || $value == 'FALSE');
            }
            return false;
        break;

        case RFC2445_TYPE_DATE:
            if(is_int($value)) {
                if($value < 0) {
                    return false;
                }
                $value = "$value";
            }
            else if(!is_string($value)) {
                return false;
            }

            if(strlen($value) != 8) {
                return false;
            }

            $y = intval(substr($value, 0, 4));
            $m = intval(substr($value, 4, 2));
            $d = intval(substr($value, 6, 2));

            return checkdate($m, $d, $y);
        break;

        case RFC2445_TYPE_DATE_TIME:
            if(!is_string($value) || strlen($value) < 15) {
                return false;
            }

            return($value{8} == 'T' && 
                   rfc2445_is_valid_value(substr($value, 0, 8), RFC2445_TYPE_DATE) &&
                   rfc2445_is_valid_value(substr($value, 9), RFC2445_TYPE_TIME));
        break;

        case RFC2445_TYPE_DURATION:
            if(!is_string($value)) {
                return false;
            }

            $len = strlen($value);

            if($len < 3) {
                // Minimum conformant length: "P1W"
                return false;
            }

            if($value{0} == '+' || $value{0} == '-') {
                $value = substr($value, 1);
                --$len; // Don't forget to update this!
            }

            if($value{0} != 'P') {
                return false;
            }

            // OK, now break it up
            $num = '';
            $allowed = 'WDT';

            for($i = 1; $i < $len; ++$i) {
                $ch = $value{$i};
                if($ch >= '0' && $ch <= '9') {
                    $num .= $ch;
                    continue;
                }
                if(strpos($allowed, $ch) === false) {
                    // Non-numeric character which shouldn't be here
                    return false;
                }
                if($num === '' && $ch != 'T') {
                    // Allowed non-numeric character, but no digits came before it
                    return false;
                }

                // OK, $ch now holds a character which tells us what $num is
                switch($ch) {
                    case 'W':
                        // If duration in weeks is specified, this must end the string
                        return ($i == $len - 1);
                    break;

                    case 'D':
                        // Days specified, now if anything comes after it must be a 'T'
                        $allowed = 'T';
                    break;

                    case 'T':
                        // Starting to specify time, H M S are now valid delimiters
                        $allowed = 'HMS';
                    break;

                    case 'H':
                        $allowed = 'M';
                    break;

                    case 'M':
                        $allowed = 'S';
                    break;

                    case 'S':
                        return ($i == $len - 1);
                    break;
                }

                // If we 're going to continue, reset $num
                $num = '';

            }

            // $num is kept for this reason: if we 're here, we ran out of chars
            // therefore $num must be empty for the period to be legal
            return ($num === '' && $ch != 'T');

        break;
        
        case RFC2445_TYPE_FLOAT:
            if(is_float($value)) {
                return true;
            }
            if(!is_string($value) || $value === '') {
                return false;
            }

            $dot = false;
            $int = false;
            $len = strlen($value);
            for($i = 0; $i < $len; ++$i) {
                switch($value{$i}) {
                    case '-': case '+':
                        // A sign can only be seen at position 0 and cannot be the only char
                        if($i != 0 || $len == 1) {
                            return false;
                        }
                    break;
                    case '.':
                        // A second dot is an error
                        // Make sure we had at least one int before the dot
                        if($dot || !$int) {
                            return false;
                        }
                        $dot = true;
                        // Make also sure that the float doesn't end with a dot
                        if($i == $len - 1) {
                            return false;
                        }
                    break;
                    case '0': case '1': case '2': case '3': case '4':
                    case '5': case '6': case '7': case '8': case '9':
                        $int = true;
                    break;
                    default:
                        // Any other char is a no-no
                        return false;
                    break;
                }
            }
            return true;
        break;

        case RFC2445_TYPE_INTEGER:
            if(is_int($value)) {
                return true;
            }
            if(!is_string($value) || $value === '') {
                return false;
            }

            if($value{0} == '+' || $value{0} == '-') {
                if(strlen($value) == 1) {
                    return false;
                }
                $value = substr($value, 1);
            }

            if(strspn($value, '0123456789') != strlen($value)) {
                return false;
            }

            return ($value >= -2147483648 && $value <= 2147483647);
        break;

        case RFC2445_TYPE_PERIOD:
            if(!is_string($value) || empty($value)) {
                return false;
            }

            $parts = explode('/', $value);
            if(count($parts) != 2) {
                return false;
            }

            if(!rfc2445_is_valid_value($parts[0], RFC2445_TYPE_DATE_TIME)) {
                return false;
            }

            // Two legal cases for the second part:
            if(rfc2445_is_valid_value($parts[1], RFC2445_TYPE_DATE_TIME)) {
                // It has to be after the start time, so
                return ($parts[1] > $parts[0]);
            }
            else if(rfc2445_is_valid_value($parts[1], RFC2445_TYPE_DURATION)) {
                // The period MUST NOT be negative
                return ($parts[1]{0} != '-');
            }

            // It seems to be illegal
            return false;
        break;

        case RFC2445_TYPE_RECUR:
            if(!is_string($value)) {
                return false;
            }

            $parts = explode(';', strtoupper($value));

            // First of all, we need at least a FREQ and a UNTIL or COUNT part, so...
            if(count($parts) < 2) {
                return false;
            }

            // Let's get that into a more easily comprehensible format
            $vars = array();
            foreach($parts as $part) {

                $pieces = explode('=', $part);
                // There must be exactly 2 pieces, e.g. FREQ=WEEKLY
                if(count($pieces) != 2) {
                    return false;
                }

                // It's illegal for a variable to appear twice
                if(isset($vars[$pieces[0]])) {
                    return false;
                }

                // Sounds good
                $vars[$pieces[0]] = $pieces[1];
            }

            // OK... now to test everything else

            // FREQ must be the first thing appearing
            reset($vars);
            if(key($vars) != 'FREQ') {
                return false;
            }

            // It's illegal to have both UNTIL and COUNT appear
            if(isset($vars['UNTIL']) && isset($vars['COUNT'])) {
                return false;
            }

            // Special case: BYWEEKNO is only valid for FREQ=YEARLY
            if(isset($vars['BYWEEKNO']) && $vars['FREQ'] != 'YEARLY') {
                return false;
            }

            // Special case: BYSETPOS is only valid if another BY option is specified
            if(isset($vars['BYSETPOS'])) {
                $options = array('BYSECOND', 'BYMINUTE', 'BYHOUR', 'BYDAY', 'BYMONTHDAY', 'BYYEARDAY', 'BYWEEKNO', 'BYMONTH');
                $defined = array_keys($vars);
                $common  = array_intersect($options, $defined);
                if(empty($common)) {
                    return false;
                }
            }

            // OK, now simply check if each element has a valid value,
            // unsetting them on the way. If at the end the array still
            // has some elements, they are illegal.

            if($vars['FREQ'] != 'SECONDLY' && $vars['FREQ'] != 'MINUTELY' && $vars['FREQ'] != 'HOURLY' && 
               $vars['FREQ'] != 'DAILY'    && $vars['FREQ'] != 'WEEKLY' &&
               $vars['FREQ'] != 'MONTHLY'  && $vars['FREQ'] != 'YEARLY') {
                return false;
            }
            unset($vars['FREQ']);

            // Set this, we may need it later
            $weekdays = explode(',', RFC2445_WEEKDAYS);

            if(isset($vars['UNTIL'])) {
                if(rfc2445_is_valid_value($vars['UNTIL'], RFC2445_TYPE_DATE_TIME)) {
                    // The time MUST be in UTC format
                    if(!(substr($vars['UNTIL'], -1) == 'Z')) {
                        return false;
                    }
                }
                else if(!rfc2445_is_valid_value($vars['UNTIL'], RFC2445_TYPE_DATE_TIME)) {
                    return false;
                }
            }
            unset($vars['UNTIL']);


            if(isset($vars['COUNT'])) {
                if(empty($vars['COUNT'])) {
                    // This also catches the string '0', which makes no sense
                    return false;
                }
                if(strspn($vars['COUNT'], '0123456789') != strlen($vars['COUNT'])) {
                    return false;
                }
            }
            unset($vars['COUNT']);

            
            if(isset($vars['INTERVAL'])) {
                if(empty($vars['INTERVAL'])) {
                    // This also catches the string '0', which makes no sense
                    return false;
                }
                if(strspn($vars['INTERVAL'], '0123456789') != strlen($vars['INTERVAL'])) {
                    return false;
                }
            }
            unset($vars['INTERVAL']);

            
            if(isset($vars['BYSECOND'])) {
                if($vars['BYSECOND'] == '') {
                    return false;
                }
                // Comma also allowed
                if(strspn($vars['BYSECOND'], '0123456789,') != strlen($vars['BYSECOND'])) {
                    return false;
                }
                $secs = explode(',', $vars['BYSECOND']);
                foreach($secs as $sec) {
                    if($sec == '' || $sec < 0 || $sec > 59) {
                        return false;
                    }
                }
            }
            unset($vars['BYSECOND']);

            
            if(isset($vars['BYMINUTE'])) {
                if($vars['BYMINUTE'] == '') {
                    return false;
                }
                // Comma also allowed
                if(strspn($vars['BYMINUTE'], '0123456789,') != strlen($vars['BYMINUTE'])) {
                    return false;
                }
                $mins = explode(',', $vars['BYMINUTE']);
                foreach($mins as $min) {
                    if($min == '' || $min < 0 || $min > 59) {
                        return false;
                    }
                }
            }
            unset($vars['BYMINUTE']);

            
            if(isset($vars['BYHOUR'])) {
                if($vars['BYHOUR'] == '') {
                    return false;
                }
                // Comma also allowed
                if(strspn($vars['BYHOUR'], '0123456789,') != strlen($vars['BYHOUR'])) {
                    return false;
                }
                $hours = explode(',', $vars['BYHOUR']);
                foreach($hours as $hour) {
                    if($hour == '' || $hour < 0 || $hour > 23) {
                        return false;
                    }
                }
            }
            unset($vars['BYHOUR']);
            

            if(isset($vars['BYDAY'])) {
                if(empty($vars['BYDAY'])) {
                    return false;
                }

                // First off, split up all values we may have
                $days = explode(',', $vars['BYDAY']);
                
                foreach($days as $day) {
                    $daypart = substr($day, -2);
                    if(!in_array($daypart, $weekdays)) {
                        return false;
                    }

                    if(strlen($day) > 2) {
                        $intpart = substr($day, 0, strlen($day) - 2);
                        if(!rfc2445_is_valid_value($intpart, RFC2445_TYPE_INTEGER)) {
                            return false;
                        }
                        if(intval($intpart) == 0) {
                            return false;
                        }
                    }
                }
            }
            unset($vars['BYDAY']);


            if(isset($vars['BYMONTHDAY'])) {
                if(empty($vars['BYMONTHDAY'])) {
                    return false;
                }
                $mdays = explode(',', $vars['BYMONTHDAY']);
                foreach($mdays as $mday) {
                    if(!rfc2445_is_valid_value($mday, RFC2445_TYPE_INTEGER)) {
                        return false;
                    }
                    $mday = abs(intval($mday));
                    if($mday == 0 || $mday > 31) {
                        return false;
                    }
                }
            }
            unset($vars['BYMONTHDAY']);


            if(isset($vars['BYYEARDAY'])) {
                if(empty($vars['BYYEARDAY'])) {
                    return false;
                }
                $ydays = explode(',', $vars['BYYEARDAY']);
                foreach($ydays as $yday) {
                    if(!rfc2445_is_valid_value($yday, RFC2445_TYPE_INTEGER)) {
                        return false;
                    }
                    $yday = abs(intval($yday));
                    if($yday == 0 || $yday > 366) {
                        return false;
                    }
                }
            }
            unset($vars['BYYEARDAY']);


            if(isset($vars['BYWEEKNO'])) {
                if(empty($vars['BYWEEKNO'])) {
                    return false;
                }
                $weeknos = explode(',', $vars['BYWEEKNO']);
                foreach($weeknos as $weekno) {
                    if(!rfc2445_is_valid_value($weekno, RFC2445_TYPE_INTEGER)) {
                        return false;
                    }
                    $weekno = abs(intval($weekno));
                    if($weekno == 0 || $weekno > 53) {
                        return false;
                    }
                }
            }
            unset($vars['BYWEEKNO']);


            if(isset($vars['BYMONTH'])) {
                if(empty($vars['BYMONTH'])) {
                    return false;
                }
                // Comma also allowed
                if(strspn($vars['BYMONTH'], '0123456789,') != strlen($vars['BYMONTH'])) {
                    return false;
                }
                $months = explode(',', $vars['BYMONTH']);
                foreach($months as $month) {
                    if($month == '' || $month < 1 || $month > 12) {
                        return false;
                    }
                }
            }
            unset($vars['BYMONTH']);


            if(isset($vars['BYSETPOS'])) {
                if(empty($vars['BYSETPOS'])) {
                    return false;
                }
                $sets = explode(',', $vars['BYSETPOS']);
                foreach($sets as $set) {
                    if(!rfc2445_is_valid_value($set, RFC2445_TYPE_INTEGER)) {
                        return false;
                    }
                    $set = abs(intval($set));
                    if($set == 0 || $set > 366) {
                        return false;
                    }
                }
            }
            unset($vars['BYSETPOS']);


            if(isset($vars['WKST'])) {
                if(!in_array($vars['WKST'], $weekdays)) {
                    return false;
                }
            }
            unset($vars['WKST']);


            // Any remaining vars must be x-names
            if(empty($vars)) {
                return true;
            }

            foreach($vars as $name => $var) {
                if(!rfc2445_is_xname($name)) {
                    return false;
                }
            }

            // At last, all is OK!
            return true;

        break;

        case RFC2445_TYPE_TEXT:
            return true;
        break;

        case RFC2445_TYPE_TIME:
            if(is_int($value)) {
                if($value < 0) {
                    return false;
                }
                $value = "$value";
            }
            else if(!is_string($value)) {
                return false;
            }

            if(strlen($value) == 7) {
                if(strtoupper(substr($value, -1)) != 'Z') {
                    return false;
                }
                $value = substr($value, 0, 6);
            }
            if(strlen($value) != 6) {
                return false;
            }

            $h = intval(substr($value, 0, 2));
            $m = intval(substr($value, 2, 2));
            $s = intval(substr($value, 4, 2));

            return ($h <= 23 && $m <= 59 && $s <= 60);
        break;

        case RFC2445_TYPE_UTC_OFFSET:
            if(is_int($value)) {
                if($value >= 0) {
                    $value = "+$value";
                }
                else {
                    $value = "$value";
                }
            }
            else if(!is_string($value)) {
                return false;
            }

            $s = 0;
            if(strlen($value) == 7) {
                $s = intval(substr($value, 5, 2));
                $value = substr($value, 0, 5);
            }
            if(strlen($value) != 5 || $value == "-0000") {
                return false;
            }

            if($value{0} != '+' && $value{0} != '-') {
                return false;
            }

            $h = intval(substr($value, 1, 2));
            $m = intval(substr($value, 3, 2));

            return ($h <= 23 && $m <= 59 && $s <= 59);
        break;
    }

    // TODO: remove this assertion
    trigger_error('bad code path', E_USER_WARNING);
    var_dump($type);
    return false;
}

function rfc2445_do_value_formatting($value, $type) {
    // Note: this does not only do formatting; it also does conversion to string!
    switch($type) {
        case RFC2445_TYPE_CAL_ADDRESS:
        case RFC2445_TYPE_URI:
            // Enclose in double quotes
            $value = '"'.$value.'"';
        break;
        case RFC2445_TYPE_TEXT:
            // Escape entities
            $value = strtr($value, array("\r\n" => '\\n', "\n" => '\\n', '\\' => '\\\\', ',' => '\\,', ';' => '\\;'));
        break;
    }
    return $value;
}

function rfc2445_undo_value_formatting($value, $type) {
    switch($type) {
        case RFC2445_TYPE_CAL_ADDRESS:
        case RFC2445_TYPE_URI:
            // Trim beginning and end double quote
            $value = substr($value, 1, strlen($value) - 2);
        break;
        case RFC2445_TYPE_TEXT:
            // Unescape entities
            $value = strtr($value, array('\\n' => "\n", '\\N' => "\n", '\\\\' => '\\', '\\,' => ',', '\\;' => ';'));
        break;
    }
    return $value;
}
