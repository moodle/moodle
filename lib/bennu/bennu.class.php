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

class Bennu {
    static function timestamp_to_datetime($t = NULL) {
        if($t === NULL) {
            $t = time();
        }
        return gmstrftime('%Y%m%dT%H%M%SZ', $t);
    }

    static function generate_guid() {
        // Implemented as per the Network Working Group draft on UUIDs and GUIDs
    
        // These two octets get special treatment
        $time_hi_and_version       = sprintf('%02x', (1 << 6) + mt_rand(0, 15)); // 0100 plus 4 random bits
        $clock_seq_hi_and_reserved = sprintf('%02x', (1 << 7) + mt_rand(0, 63)); // 10 plus 6 random bits
    
        // Need another 14 random octects
        $pool = '';
        for($i = 0; $i < 7; ++$i) {
            $pool .= sprintf('%04x', mt_rand(0, 65535));
        }
    
        // time_low = 4 octets
        $random  = substr($pool, 0, 8).'-';
    
        // time_mid = 2 octets
        $random .= substr($pool, 8, 4).'-';
    
        // time_high_and_version = 2 octets
        $random .= $time_hi_and_version.substr($pool, 12, 2).'-';
    
        // clock_seq_high_and_reserved = 1 octet
        $random .= $clock_seq_hi_and_reserved;
    
        // clock_seq_low = 1 octet
        $random .= substr($pool, 13, 2).'-';
    
        // node = 6 octets
        $random .= substr($pool, 14, 12);
    
        return $random;
    }
}

