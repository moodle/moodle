<?php
ini_set('display_errors', "On");

require_once 'PEAR.php';

if(!function_exists('scandir'))
{
    function scandir($dir, $sortorder = 0)
    {
        if(is_dir($dir))
        {
            $dirlist = opendir($dir);

            while( ($file = readdir($dirlist)) !== false)
            {
                if(!is_dir($file))
                {
                    $files[] = $file;
                }
            }

            ($sortorder == 0) ? asort($files) : arsort($files);

            return $files;
        }
        else
        {
            return FALSE;
            break;
        }
    }
}


/**
 * Command-line options parsing class.
 *
 * @author Andrei Zmievski <andrei@php.net>
 *
 */
class Console_Getopt {
    /**
     * Parses the command-line options.
     *
     * The first parameter to this function should be the list of command-line
     * arguments without the leading reference to the running program.
     *
     * The second parameter is a string of allowed short options. Each of the
     * option letters can be followed by a colon ':' to specify that the option
     * requires an argument, or a double colon '::' to specify that the option
     * takes an optional argument.
     *
     * The third argument is an optional array of allowed long options. The
     * leading '--' should not be included in the option name. Options that
     * require an argument should be followed by '=', and options that take an
     * option argument should be followed by '=='.
     *
     * The return value is an array of two elements: the list of parsed
     * options and the list of non-option command-line arguments. Each entry in
     * the list of parsed options is a pair of elements - the first one
     * specifies the option, and the second one specifies the option argument,
     * if there was one.
     *
     * Long and short options can be mixed.
     *
     * Most of the semantics of this function are based on GNU getopt_long().
     *
     * @param array  $args           an array of command-line arguments
     * @param string $short_options  specifies the list of allowed short options
     * @param array  $long_options   specifies the list of allowed long options
     *
     * @return array two-element array containing the list of parsed options and
     * the non-option arguments
     *
     * @access public
     *
     */
    function getopt2($args, $short_options, $long_options = null)
    {
        return Console_Getopt::doGetopt(2, $args, $short_options, $long_options);
    }

    /**
     * This function expects $args to start with the script name (POSIX-style).
     * Preserved for backwards compatibility.
     * @see getopt2()
     */
    function getopt($args, $short_options, $long_options = null)
    {
        return Console_Getopt::doGetopt(1, $args, $short_options, $long_options);
    }

    /**
     * The actual implementation of the argument parsing code.
     */
    function doGetopt($version, $args, $short_options, $long_options = null)
    {
        // in case you pass directly readPHPArgv() as the first arg
        if (PEAR::isError($args)) {
            return $args;
        }
        if (empty($args)) {
            return array(array(), array());
        }
        $opts     = array();
        $non_opts = array();

        settype($args, 'array');

        if ($long_options) {
            sort($long_options);
        }

        /*
         * Preserve backwards compatibility with callers that relied on
         * erroneous POSIX fix.
         */
        if ($version < 2) {
            if (isset($args[0]{0}) && $args[0]{0} != '-') {
                array_shift($args);
            }
        }

        reset($args);
        while (list($i, $arg) = each($args)) {

            /* The special element '--' means explicit end of
               options. Treat the rest of the arguments as non-options
               and end the loop. */
            if ($arg == '--') {
                $non_opts = array_merge($non_opts, array_slice($args, $i + 1));
                break;
            }

            if ($arg{0} != '-' || (strlen($arg) > 1 && $arg{1} == '-' && !$long_options)) {
                $non_opts = array_merge($non_opts, array_slice($args, $i));
                break;
            } elseif (strlen($arg) > 1 && $arg{1} == '-') {
                $error = Console_Getopt::_parseLongOption(substr($arg, 2), $long_options, $opts, $args);
                if (PEAR::isError($error))
                    return $error;
            } else {
                $error = Console_Getopt::_parseShortOption(substr($arg, 1), $short_options, $opts, $args);
                if (PEAR::isError($error))
                    return $error;
            }
        }

        return array($opts, $non_opts);
    }

    /**
     * @access private
     *
     */
    function _parseShortOption($arg, $short_options, &$opts, &$args)
    {
        for ($i = 0; $i < strlen($arg); $i++) {
            $opt = $arg{$i};
            $opt_arg = null;

            /* Try to find the short option in the specifier string. */
            if (($spec = strstr($short_options, $opt)) === false || $arg{$i} == ':')
            {
                return PEAR::raiseError("Console_Getopt: unrecognized option -- $opt");
            }

            if (strlen($spec) > 1 && $spec{1} == ':') {
                if (strlen($spec) > 2 && $spec{2} == ':') {
                    if ($i + 1 < strlen($arg)) {
                        /* Option takes an optional argument. Use the remainder of
                           the arg string if there is anything left. */
                        $opts[] = array($opt, substr($arg, $i + 1));
                        break;
                    }
                } else {
                    /* Option requires an argument. Use the remainder of the arg
                       string if there is anything left. */
                    if ($i + 1 < strlen($arg)) {
                        $opts[] = array($opt,  substr($arg, $i + 1));
                        break;
                    } else if (list(, $opt_arg) = each($args))
                        /* Else use the next argument. */;
                    else
                        return PEAR::raiseError("Console_Getopt: option requires an argument -- $opt");
                }
            }

            $opts[] = array($opt, $opt_arg);
        }
    }

    /**
     * @access private
     *
     */
    function _parseLongOption($arg, $long_options, &$opts, &$args)
    {
        @list($opt, $opt_arg) = explode('=', $arg);
        $opt_len = strlen($opt);

        for ($i = 0; $i < count($long_options); $i++) {
            $long_opt  = $long_options[$i];
            $opt_start = substr($long_opt, 0, $opt_len);

            /* Option doesn't match. Go on to the next one. */
            if ($opt_start != $opt)
                continue;

            $opt_rest  = substr($long_opt, $opt_len);

            /* Check that the options uniquely matches one of the allowed
               options. */
            if ($opt_rest != '' && $opt{0} != '=' &&
                $i + 1 < count($long_options) &&
                $opt == substr($long_options[$i+1], 0, $opt_len)) {
                return PEAR::raiseError("Console_Getopt: option --$opt is ambiguous");
            }

            if (substr($long_opt, -1) == '=') {
                if (substr($long_opt, -2) != '==') {
                    /* Long option requires an argument.
                       Take the next argument if one wasn't specified. */;
                    if (!strlen($opt_arg) && !(list(, $opt_arg) = each($args))) {
                        return PEAR::raiseError("Console_Getopt: option --$opt requires an argument");
                    }
                }
            } else if ($opt_arg) {
                return PEAR::raiseError("Console_Getopt: option --$opt doesn't allow an argument");
            }

            $opts[] = array('--' . $opt, $opt_arg);
            return;
        }

        return PEAR::raiseError("Console_Getopt: unrecognized option --$opt");
    }

    /**
    * Safely read the $argv PHP array across different PHP configurations.
    * Will take care on register_globals and register_argc_argv ini directives
    *
    * @access public
    * @return mixed the $argv PHP array or PEAR error if not registered
    */
    function readPHPArgv()
    {
        global $argv;
        if (!is_array($argv)) {
            if (!@is_array($_SERVER['argv'])) {
                if (!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
                    return PEAR::raiseError("Console_Getopt: Could not read cmd args (register_argc_argv=Off?)");
                }
                return $GLOBALS['HTTP_SERVER_VARS']['argv'];
            }
            return $_SERVER['argv'];
        }
        return $argv;
    }

}


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
   
    /**
     * Concatenates all the pprof files generated by apd_set_pprof_trace()
     * and returns the resulting string, which can then be processed by 
     * get_profiling();
     * It also deletes these files once finished, in order to limit
     * cluttering of the filesystem. This can be switched off by
     * providing "false" as the only argument to this function.
     * 
     * WARNING: If you switch cleanup off, profiling data will
     * accumulate from one pageload to the next.
     *
     * @param boolean $cleanup Whether to delete pprof files or not.
     * @return String Profiling raw data
     */
    function _get_pprofp($cleanup = true)
    {
        global $CFG, $USER;
        // List all files under our temporary directory
        $tempdir = $CFG->dataroot . '/temp/profile/' . $USER->id; 
        if ($files = scandir($tempdir)) {
            // Concatenate the files
            print_r($files); 
        } else {
            print "Error: Profiler could not read the directory $tempdir.";
            return false;
        }
        

        // Return a handle to the resulting file
        
        
        if(($DATA = fopen($dataFile, "r")) == FALSE) {
            return "Failed to open $dataFile for reading\n";
        }
        return $handle;
    }


    /**
     * Returns profiling information gathered using APD functions.
     * Accepts a numerical array of command-line arguments.
     * 
     * @usage Profiler::get_profiling($args)
     *  Sort options
     *  -a          Sort by alphabetic names of subroutines.
     *  -l          Sort by number of calls to subroutines
     *  -m          Sort by memory used in a function call.
     *  -r          Sort by real time spent in subroutines.
     *  -R          Sort by real time spent in subroutines (inclusive of child calls).
     *  -s          Sort by system time spent in subroutines.
     *  -S          Sort by system time spent in subroutines (inclusive of child calls).
     *  -u          Sort by user time spent in subroutines.
     *  -U          Sort by user time spent in subroutines (inclusive of child calls).
     *  -v          Sort by average amount of time spent in subroutines.
     *  -z          Sort by user+system time spent in subroutines. (default)
     *
     *  Display options
     *  -c          Display Real time elapsed alongside call tree.
     *  -i          Suppress reporting for php builtin functions
     *  -O <cnt>    Specifies maximum number of subroutines to display. (default 15)
     *  -t          Display compressed call tree.
     *  -T          Display uncompressed call tree.
     *
     *  Example array: array('-a', '-l');
     *   
     * @param Array $args
     * @return String Profiling info
     */
    function get_profiling($args)
    { 
        $con = new Console_Getopt;
        array_shift($args);
        
        $shortoptions = 'acg:hiIlmMrRsStTuUO:vzZ';
        $retval = $con->getopt( $args, $shortoptions);
        if(is_object($retval)) {
            usage();
        }
        
        $opt['O'] = 20;
        foreach ($retval[0] as $kv_array) {
            $opt[$kv_array[0]] = $kv_array[1];
        }
        
        $DATA = Profiler::_get_pprofp();

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
        return $retstring;
    }

    function usage() {
    return <<<EOD
    Profiler::get_profiling(\$args)
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
