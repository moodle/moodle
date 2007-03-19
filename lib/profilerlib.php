<?php
/**
* Profiler adapted from Pear::APD's pprofp script. Not quite there yet, I need
* to get this to accept a similar list of arguments as the script does,
* and process them the same way. Also make sure that the file being loaded
* is the right one. Also support multiple pids used in one page load (up to 4 so far).
* Then output all this in a nicely formatted table.
*/
class Profiler
{
    var $stimes;
    var $utimes;
    var $calls;
    var $c_stimes;
    var $c_utimes;
    var $mem;

    function get_profiling($opt = array())
    { 
        global $CFG;
echo "BLAH";
        $retstring = '';
        $dataFile = ini_get('apd.dumpdir') . '/pprof.' . getmypid() . '.*';
        
        if (!$dataFile) {
            return $this->usage();
        }
        
        if(($DATA = fopen($dataFile, "r")) == FALSE) {
            die("Failed to open $dataFile for reading\n");
        }

        $cfg = array();
        $this->parse_info('HEADER', $DATA, $cfg);

        $callstack = array();
        $calls = array();
        $indent_cur = 0;
        $file_hash = array();
        $this->mem = array();
        $t_rtime = 0;
        $t_stime = 0;
        $t_utime = 0;
        $c_rtimes = array();
        $this->c_stimes = array();
        $this->c_utimes = array();
        $rtimes = array();
        $this->stimes = array();
        $this->utimes = array();
        $rtotal = 0;
        $stotal = 0;
        $utotal = 0;
        $last_memory = 0;

        $symbol_hash = array();
        $symbol_type = array();

        while($line = fgets($DATA)) {
            $line = rtrim($line);
            if(preg_match("/^END_TRACE/", $line)){
                break;
            }
            list($token, $data) = preg_split("/ /",$line, 2);
            if($token == '!') {
            list ($index, $file) = preg_split("/ /", $data, 2);
            $file_hash[$index] = $file;
            continue;
            }
            if( $token == '&') {
                list ($index, $name, $type) = preg_split("/ /", $data, 3);
                $symbol_hash[$index] = $name;
            $symbol_type[$index] = $type;
                continue;
            }
            if( $token == '+') {
                list($index, $file, $line) = preg_split("/ /",$data, 3);
                if(array_key_exists('i',$opt) && $symbol_type[$index] == 1) {
                    continue;
                }	
                $index_cur = $index;
                $calls[$index_cur]++;
                array_push($callstack, $index_cur);
                if(array_key_exists('T', $opt)) {
                    if(array_key_exists('c', $opt)) {
                       $retstring .= sprintf("%2.02f ", $rtotal/1000000);
                    }
                    $retstring .= str_repeat("  ", $indent_cur).$symbol_hash[$index_cur]."\n";
                if(array_key_exists('m', $opt)) {
                $retstring .= str_repeat("  ", $indent_cur)."C: $file_hash[$file]:$line M: $memory\n";
                }
            }
                elseif(array_key_exists('t', $opt)) {
                    if ( $indent_last == $indent_cur && $index_last == $index_cur ) {
                        $repcnt++;
                    }
                    else {
                        if ( $repcnt ) {
                            $repstr = ' ('.++$repcnt.'x)';
                        }
                        if(array_key_exists('c', $opt)) {
                            $retstring .= sprintf("%2.02f ", $rtotal/1000000);
                        }
                        $retstring .= str_repeat("  ", $indent_last).$symbol_hash[$index_last].$repstr."\n";
                if(array_key_exists('m', $opt)) {
                   $retstring .= str_repeat("  ", $indent_cur)."C: $file_hash[$file_last]:$line_last M: $memory\n";
                }
                        $repstr = '';
                        $repcnt = 0;
                        $index_last = $index_cur;
                        $indent_last = $indent_cur;
                $file_last = $file;
                $line_last = $line;
                    }
                }
            $indent_cur++;
                continue;
            }
            if( $token == '@') {
                list($file_no, $line_no, $ut, $st, $rt) = preg_split("/ /", $data);
                $top = array_pop($callstack);
                $this->utimes[$top] += $ut;
                $utotal += $ut;
                $this->stimes[$top] += $st;
                $stotal += $st;
                $rtimes[$top] += $rt;
                $rtotal += $rt;
                array_push($callstack, $top);
            foreach ($callstack as $stack_element) {
                    $this->c_utimes[$stack_element] += $ut;
                    $this->c_stimes[$stack_element] += $st;
                    $c_rtimes[$stack_element] += $rt;
                }
                continue;
            }
            if ($token == '-') {
                list  ($index, $memory) = preg_split("/ /", $data, 2);
                if(array_key_exists('i',$opt) && $symbol_type[$index] == 1)
                {
                    continue;
                }
                $this->mem[$index] += ($memory - $last_memory);
                $last_memory = $memory;
                $indent_cur--;
                $tmp = array_pop($callstack);
                continue;
            }
        }
        $this->parse_info('FOOTER', $DATA, $cfg);
        $sort = 'by_time';
        if(array_key_exists('l', $opt)) { $sort = 'by_calls'; }
        if(array_key_exists('m', $opt)) { $sort = 'by_mem'; }
        if(array_key_exists('a', $opt)) { $sort = 'by_name'; }
        if(array_key_exists('v', $opt)) { $sort = 'by_avgcpu'; }
        if(array_key_exists('r', $opt)) { $sort = 'by_rtime'; }
        if(array_key_exists('R', $opt)) { $sort = 'by_c_rtime'; }
        if(array_key_exists('s', $opt)) { $sort = 'by_stime'; }
        if(array_key_exists('S', $opt)) { $sort = 'by_c_stime'; }
        if(array_key_exists('u', $opt)) { $sort = 'by_utime'; }
        if(array_key_exists('U', $opt)) { $sort = 'by_c_utime'; }
        if(array_key_exists('Z', $opt)) { $sort = 'by_c_time'; }
        if( !count($symbol_hash)) {
            continue;
        }

        $retstring .= sprintf("
        Trace for %s
        Total Elapsed Time = %4.2f
        Total System Time  = %4.2f
        Total User Time    = %4.2f
        ", $cfg['caller'], $rtotal/1000000, $stotal/1000000, $utotal/1000000);
        
        $retstring .= "\n
                 Real         User        System             secs/    cumm
        %Time (excl/cumm)  (excl/cumm)  (excl/cumm) Calls    call    s/call  Memory Usage Name
        --------------------------------------------------------------------------------------\n";
        $l = 0;
        $itotal = 0;
        $percall = 0;
        $cpercall = 0;

        uksort($symbol_hash, $sort);
        foreach (array_keys($symbol_hash) as $j) {
            if(array_key_exists('i', $opt) && $symbol_type[$j] == 1) {
                continue;
            }
            if ($l++ <  $opt['O']) {
                $pcnt = 100*($this->stimes[$j] + $this->utimes[$j])/($utotal + $stotal + $itotal);
                $c_pcnt = 100* ($this->c_stimes[$j] + $this->c_utimes[$j])/($utotal + $stotal + $itotal);
                $rsecs = $rtimes[$j]/1000000;
                $ssecs = $this->stimes[$j]/1000000;
                $usecs = $this->utimes[$j]/1000000;
                $c_rsecs = $c_rtimes[$j]/1000000;
                $c_ssecs = $this->c_stimes[$j]/1000000;
                $c_usecs = $this->c_utimes[$j]/1000000;
                $ncalls = $calls[$j];
            if(array_key_exists('z', $opt)) {
                    $percall = ($usecs + $ssecs)/$ncalls;
                    $cpercall = ($c_usecs + $c_ssecs)/$ncalls;
                        if($utotal + $stotal) {
                    $pcnt = 100*($this->stimes[$j] + $this->utimes[$j])/($utotal + $stotal);
                        }
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('Z', $opt)) {
                    $percall = ($usecs + $ssecs)/$ncalls;
                    $cpercall = ($c_usecs + $c_ssecs)/$ncalls;
                        if($utotal + $stotal) {
                    $pcnt = 100*($this->c_stimes[$j] + $this->c_utimes[$j])/($utotal + $stotal);
                        }
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('r', $opt)) {
                    $percall = ($rsecs)/$ncalls;
                    $cpercall = ($c_rsecs)/$ncalls;
                        if($rtotal) {
                    $pcnt = 100*$rtimes[$j]/$rtotal;
                        }
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('R', $opt)) {
                    $percall = ($rsecs)/$ncalls;
                    $cpercall = ($c_rsecs)/$ncalls;
                        if($rtotal) {
                    $pcnt = 100*$c_rtimes[$j]/$rtotal;
                        }
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('u', $opt)) {
                    $percall = ($usecs)/$ncalls;
                    $cpercall = ($c_usecs)/$ncalls;
                        if($utotal) {
                    $pcnt = 100*$this->utimes[$j]/$utotal;
                        } 
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('U', $opt)) {
                    $percall = ($usecs)/$ncalls;
                    $cpercall = ($c_usecs)/$ncalls;
                        if($utotal) {
                    $pcnt = 100*$this->c_utimes[$j]/$utotal;
                        }
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('s', $opt)) {
                    $percall = ($ssecs)/$ncalls;
                    $cpercall = ($c_ssecs)/$ncalls;
                        if($stotal) {
                    $pcnt = 100*$this->stimes[$j]/$stotal;
                        }
                        else {
                            $pcnt = 100;
                        }
            }
            if(array_key_exists('S', $opt)) {
                    $percall = ($ssecs)/$ncalls;
                    $cpercall = ($c_ssecs)/$ncalls;
                        if($stotal) {
                    $pcnt = 100*$this->c_stimes[$j]/$stotal;
                        }
                        else {
                            $pcnt = 100;
                        }
            }
        //        $cpercall = ($c_usecs + $c_ssecs)/$ncalls;
                $mem_usage = $this->mem[$j];
                $name = $symbol_hash[$j];
                $retstring .=  sprintf("%3.01f %2.02f %2.02f  %2.02f %2.02f  %2.02f %2.02f  %4d  %2.04f   %2.04f %12d %s\n", 
                                $pcnt, $rsecs, $c_rsecs, $usecs, $c_usecs, $ssecs, $c_ssecs, $ncalls, $percall, $cpercall, $mem_usage, $name);
                return $retstring;
            }
        }
    }

    function usage() {
    return <<<EOD
    pprofp <flags> <trace file>
        Sort options
        -a          Sort by alphabetic names of subroutines.
        -l          Sort by number of calls to subroutines
        -m          Sort by memory used in a function call.
        -r          Sort by real time spent in subroutines.
        -R          Sort by real time spent in subroutines (inclusive of child calls).
        -s          Sort by system time spent in subroutines.
        -S          Sort by system time spent in subroutines (inclusive of child calls).
        -u          Sort by user time spent in subroutines.
        -U          Sort by user time spent in subroutines (inclusive of child calls).
        -v          Sort by average amount of time spent in subroutines.
        -z          Sort by user+system time spent in subroutines. (default)

        Display options
        -c          Display Real time elapsed alongside call tree.
        -i          Suppress reporting for php builtin functions
        -O <cnt>    Specifies maximum number of subroutines to display. (default 15)
        -t          Display compressed call tree.
        -T          Display uncompressed call tree.

EOD;
        exit(1);
    }

    function parse_info($tag, $datasource, &$cfg) {
        while($line = fgets($datasource)) {
            $line = rtrim($line);
            if(preg_match("/^END_$tag$/", $line)) {
                break;
            }
            if(preg_match("/(\w+)=(.*)/", $line, $matches)) {
                $cfg[$matches[1]] = $matches[2];
            }
        }
    }

    function num_cmp($a, $b) {
        if (intval($a) > intval($b)) { return 1;}
        elseif(intval($a) < intval($b)) { return -1;}
        else {return 0;}
    }

    function by_time($a,$b) {
        return $this->num_cmp(($this->stimes[$b] + $this->utimes[$b]),($this->stimes[$a] + $this->utimes[$a]));
    }

    function by_c_time($a,$b) {
        return $this->num_cmp(($this->c_stimes[$b] + $this->c_utimes[$b]),($this->c_stimes[$a] + $this->c_utimes[$a]));
    }

    function by_avgcpu($a,$b) {
        return $this->num_cmp(($this->stimes[$b] + $this->utimes[$b])/$this->calls[$b],($this->stimes[$a] + $this->utimes[$a])/$this->calls[$a]);
    }

    function by_calls($a, $b) {
        return $this->num_cmp($this->calls[$b], $this->calls[$a]);
    }
    
    function by_rtime($a,$b) { 
        return $this->num_cmp($this->rtimes[$b], $this->rtimes[$a]);
    }
    
    function by_c_rtime($a,$b) { 
        return $this->num_cmp($this->c_rtimes[$b], $this->c_rtimes[$a]); 
    }
    
    function by_stime($a,$b) { 
        return $this->num_cmp($this->stimes[$b], $this->stimes[$a]); 
    }
    
    function by_c_stime($a,$b) { 
        return $this->num_cmp($this->c_stimes[$b], $this->c_stimes[$a]); 
    }
    
    function by_utime($a,$b) { 
        return $this->num_cmp($this->utimes[$b], $this->utimes[$a]); 
    }
    
    function by_c_utime($a,$b) { 
        return $this->num_cmp($this->c_utimes[$b], $this->c_utimes[$a]); 
    }
    
    function by_mem($a, $b) { 
        return $this->num_cmp($this->mem[$b], $this->mem[$a]); 
    } 
}
?>
