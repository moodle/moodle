<?php

include('INC/connexion.php'); 
//include_once('../trainingsessions/htmlrenderers.php');         
        
function use_stats_extract_logs_frank($from, $to, $for = null, $course = null) {
      
    global $CFG, $USER, $DB;

        $sql = "SELECT
             id,
             courseid as course,
             action,
             timecreated as time,
             userid,
             contextid,
             contextlevel
           FROM
             mdl_logstore_standard_log
           WHERE
             timecreated > ? AND
             timecreated < ? AND
             ((courseid = ? AND action = 'loggedin') OR
              (1
              ))
             AND userid = ? 
           ORDER BY
             timecreated";


    

    include('INC/connexion.php'); 


    $req_log = $bdd->prepare($sql);
    
    $req_log->execute(array( $from, $to, $course, $for ));


    $LesLogs = array();
    $i=0;
    while ($donneesLog = $req_log->fetch(PDO::FETCH_OBJ)) {     
        $LesLogs[$i] = $donneesLog;
        $i++;
    }   
    return $LesLogs;
}

function use_stats_aggregate_logs_frank($logs, $dimension, $origintime = 0) {
        global $CFG, $DB, $OUTPUT, $USER, $COURSE;
        $sessionid = 0;

        $ignoremodulelist = array();

        $threshold = 7200; // Fixé dans $CFG
        $lastpingcredit = 900; // Fixé dans $CFG

        $currentuser = 0;
        $aggregate = array();
        $aggregate['sessions'] = array();

        if (!empty($logs)) {
            $logs = array_values($logs);

            $memlap = 0; // will store the accumulated time for in the way but out of scope laps.


            for ($i = 0 ; $i < count($logs) ; $i++) {
                $log = $logs[$i];
                
                // We "guess" here the real identity of the log's owner.
                $currentuser = $log->userid;

                // Let's get lap time to next log in track
                if (isset($logs[$i + 1])) {
                    $lognext = $logs[$i + 1];
                    $lap = $lognext->time - $log->time;
                } else {
                    $lap = 900;
                }


                $log->module = 'undefined';
      

                switch ($log->contextlevel) {
                    case '10': //CONTEXT_SYSTEM
                        if ($log->action == 'loggedin') {
                            $log->module = 'user';
                            $log->action = 'login';
                        } else {
                            $log->module = 'system';
                        }
                        $log->cmid = 0;
                        break;
                    case '30':
                        $log->module = 'user';
                        $log->cmid = 0;
                        break;
                    case '70':
                        $sql = 'SELECT instanceid FROM mdl_context WHERE id = ?';
                        include('INC/connexion.php');
                        $req_cmid = $bdd->prepare($sql);
                        $req_cmid->execute(array( $log->contextid ));
                        $cmid_array = $req_cmid->fetch(PDO::FETCH_ASSOC);
                        $cmid = $cmid_array['instanceid'];


                        $sql = 'SELECT module FROM mdl_course_modules WHERE id = ?';
                        include('INC/connexion.php');
                        $req_moduleid = $bdd->prepare($sql);
                        $req_moduleid->execute(array( $cmid ));
                        $moduleid_array = $req_moduleid->fetch(PDO::FETCH_ASSOC);
                        $moduleid = $moduleid_array['module'];


                        $sql = 'SELECT name FROM mdl_modules WHERE id = ?';
                        include('INC/connexion.php');
                        $req_modulename = $bdd->prepare($sql);
                        $req_modulename->execute(array( $moduleid ));
                        $modulename_array = $req_modulename->fetch(PDO::FETCH_ASSOC);
                        $modulename = $modulename_array['name'];

                        $log->module = $modulename; 
                        $log->cmid = 0 + @$cmid; // Protect in case of faulty module.
                        break;
                    default:
                        $log->cmid = 0;
                        $log->module = 'course';
                        break;
                }


                // Fix session breaks over the threshold time.
                $sessionpunch = false;
                if ($lap > $threshold) {
                    $lap = $lastpingcredit;
                    if ($lognext->action != 'login') {
                        $sessionpunch = true;
                    }
                }

                // discard unsignificant cases
                if ($log->action == 'loggedout') {
                    $memlap = 0;
                    continue;
                }
                if ($log->$dimension == 'system' and $log->action == 'failed') continue;

                $lap = $lap + $memlap;
                $memlap = 0;

                if (!isset($log->$dimension)) {
                   // echo 'unknown dimension';
                }

                // Per login session aggregation.

                // Repair inconditionally first visible session track that has no login
                if ($sessionid == 0) {
                    if (!isset($aggregate['sessions'][0]->sessionstart)) {
                        @$aggregate['sessions'][0]->sessionstart = $logs[0]->time;
                    }
                }


                // Next visible log is a login. So current session ends
                @$aggregate['sessions'][$sessionid]->courses[$log->course] = $log->course; // this will collect all visited course ids during this session
                if (($log->action != 'login') && ('login' == @$lognext->action)) {
                    // We are the last action before a new login 
                    @$aggregate['sessions'][$sessionid]->elapsed += $lap;
                    @$aggregate['sessions'][$sessionid]->sessionend = $log->time + $lap;
                } else {
                    // all other cases : login or non login
                    if ($log->action == 'login') {
                        // We are explicit login
                        if (@$lognext->action != 'login') {
                           $sessionid++;
                           @$aggregate['sessions'][$sessionid]->elapsed = $lap;
                           @$aggregate['sessions'][$sessionid]->sessionstart = $log->time;
                       } else {
                       }
                    } else {
                        // all other cases
                        if ($sessionpunch || @$lognext->action == 'login') {
                            // this record is the last one of the current session.
                            @$aggregate['sessions'][$sessionid]->sessionend = $log->time + $lap;
                            @$aggregate['sessions'][$sessionid]->elapsed += $lap;
                            if ($sessionpunch) {
                                // $logs[$i + 1]->action = 'login';
                                $sessionid++;
                                @$aggregate['sessions'][$sessionid]->sessionstart = $lognext->time;
                                @$aggregate['sessions'][$sessionid]->elapsed = 0;
                            }
                            // $sessionid++;
                            // @$aggregate['sessions'][$sessionid]->sessionstart = $lognext->time;
                            // @$aggregate['sessions'][$sessionid]->elapsed = $lap;
                        } else {
                            if (!isset($aggregate['sessions'][$sessionid])) {
                                @$aggregate['sessions'][$sessionid]->sessionstart = $log->time;
                                @$aggregate['sessions'][$sessionid]->elapsed = $lap;
                            } else {
                                @$aggregate['sessions'][$sessionid]->elapsed += $lap;
                            }
                        }
                    }
                }

                // Standard global lap aggregation.
                if ($log->$dimension == 'course') {
                    if (array_key_exists(''.$log->$dimension, $aggregate) && array_key_exists($log->course, $aggregate[$log->$dimension])){
                        @$aggregate['course'][$log->course]->elapsed += $lap;
                        @$aggregate['course'][$log->course]->events += 1;
                        @$aggregate['course'][$log->course]->lastaccess = $log->time;
                    } else {
                        @$aggregate['course'][$log->course]->elapsed = $lap;
                        @$aggregate['course'][$log->course]->events = 1;
                        @$aggregate['course'][$log->course]->firstaccess = $log->time;
                        @$aggregate['course'][$log->course]->lastaccess = $log->time;
                    }
                } else {
                    if (array_key_exists(''.$log->$dimension, $aggregate) && array_key_exists($log->cmid, $aggregate[$log->$dimension])){
                        @$aggregate[$log->$dimension][$log->cmid]->elapsed += $lap;
                        @$aggregate[$log->$dimension][$log->cmid]->events += 1;
                        @$aggregate[$log->$dimension][$log->cmid]->lastaccess = $log->time;
                    } else {
                        @$aggregate[$log->$dimension][$log->cmid]->elapsed = $lap;
                        @$aggregate[$log->$dimension][$log->cmid]->events = 1;
                        @$aggregate[$log->$dimension][$log->cmid]->firstaccess = $log->time;
                        @$aggregate[$log->$dimension][$log->cmid]->lastaccess = $log->time;
                    }
                }

                /// Standard non course level aggregation
                if ($log->$dimension != 'course') {
                    if ($log->cmid) {
                        $key = 'activities';
                    } else {
                        $key = 'other';
                    }
                    if (array_key_exists($key, $aggregate) && array_key_exists($log->course, $aggregate[$key])) {
                        $aggregate[$key][$log->course]->elapsed += $lap;
                        $aggregate[$key][$log->course]->events += 1;
                    } else {
                        $aggregate[$key][$log->course] = new StdClass();
                        $aggregate[$key][$log->course]->elapsed = $lap;
                        $aggregate[$key][$log->course]->events = 1;
                    }
                }

                // Standard course level lap aggregation.
                if (array_key_exists('coursetotal', $aggregate) && array_key_exists($log->course, $aggregate['coursetotal'])) {
                    @$aggregate['coursetotal'][$log->course]->elapsed += $lap;
                    @$aggregate['coursetotal'][$log->course]->events += 1;
                    @$aggregate['coursetotal'][$log->course]->firstaccess = $log->time;
                    @$aggregate['coursetotal'][$log->course]->lastaccess = $log->time;
                } else {
                    @$aggregate['coursetotal'][$log->course]->elapsed = $lap;
                    @$aggregate['coursetotal'][$log->course]->events = 1;
                    if (!isset($aggregate['coursetotal'][$log->course]->firstaccess)) {
                        @$aggregate['coursetotal'][$log->course]->firstaccess = $log->time;
                    }
                    @$aggregate['coursetotal'][$log->course]->lastaccess = $log->time;
                }
                $origintime = $log->time;

            }

        }

        // Check assertions
        if (!empty($aggregate['coursetotal'])) {
            foreach(array_keys($aggregate['coursetotal']) as $courseid) {
                if ($aggregate['coursetotal'][$courseid]->events != 
                            $aggregate['course'][$courseid]->events + 
                            $aggregate['activities'][$courseid]->events + 
                            $aggregate['other'][$courseid]->events) {
                    echo "Bad sumcheck on events for course $courseid <br/>";
                }
                if ($aggregate['coursetotal'][$courseid]->elapsed != 
                            $aggregate['course'][$courseid]->elapsed + 
                            $aggregate['activities'][$courseid]->elapsed + 
                            $aggregate['other'][$courseid]->elapsed) {
                    echo "Bad sumcheck on time for course $courseid <br/>";
                }
            }
        }

        // Finish last session.
        @$aggregate['sessions'][$sessionid]->sessionend = $log->time + $lap;

        // This is our last change to guess a user when no logs available.
        

        // ==========================================================================A VOIR SI BUG EN DESSOUS ??? ==========================================================


        if (empty($currentuser)) {
            $currentuser = optional_param('userid', $USER->id, PARAM_INT);
        }

        // we need finally adjust some times from time recording activities

        if (array_key_exists('scorm', $aggregate)) {
            // echo '<p> <b> IL Y A UN SCORM </b> </p>';
            foreach (array_keys($aggregate['scorm']) as $cmid) {
                
                $sql = 'SELECT * FROM mdl_course_modules WHERE id = ?';
                        include('INC/connexion.php');
                        $req_cmid = $bdd->prepare($sql);
                        $req_cmid->execute(array( $cmid ));
                        $cm = $req_cmid->fetch(PDO::FETCH_ASSOC);
                if ($cm) {
                    // These are all scorms.
                    // scorm activities have their accurate recorded time
                    $realtotaltime = 0;

                    $sql = 'SELECT id, element, value FROM mdl_scorm_scoes_track WHERE element = "cmi.core.total_time" AND scormid = '.$cm['instance'].' AND userid = '.$currentuser;
                    // echo $sql;
                        include('INC/connexion.php');
                        $req_cmid = $bdd->prepare($sql);
                        $req_cmid->execute();
                        
                        $realtimes = $req_cmid->fetch(PDO::FETCH_ASSOC);

                    if ($realtimes) {
                        foreach ($realtimes as $rt) {
                            $realcomps = preg_match("/(\d\d):(\d\d):(\d\d)\./", $rt->value, $matches);
                            $realtotaltime += $matches[1] * 3600 + $matches[2] * 60 + $matches[3];
                        }
                    }
                    if ($aggregate['scorm'][$cmid]->elapsed < $realtotaltime) {
                        // $aggregate['scorm'][$cmid]->elapsed = $realtotaltime;
                        $diff = $realtotaltime - $aggregate['scorm'][$cmid]->elapsed;
                        $aggregate['scorm'][$cmid]->elapsed += $diff;
                        if (!array_key_exists($cm->course, $aggregate['coursetotal'])) {
                        $aggregate['coursetotal'][$cm->course] = new StdClass();
                        }
                        @$aggregate['coursetotal'][$cm->course]->elapsed += $diff;
                        @$aggregate['activities'][$cm->course]->elapsed += $diff;
                    }
                }
            } 
        }

        return $aggregate;
    }

    function reports_get_course_structure_frank($courseid, &$itemcount){
        global $CFG, $DB;
        
        $structure = array();

            $sql = 'SELECT * FROM mdl_course_sections WHERE course = ?';
            include('INC/connexion.php');
            $req_sections = $bdd->prepare($sql);
            $req_sections->execute(array( $courseid ));
            
            while ($section = $req_sections->fetch(PDO::FETCH_OBJ)){
                $element = new StdClass;
                $element->type = 'section';
                $element->plugintype = 'section';
                $element->instance = $section;
                $element->instance->visible = $section->visible;
                // $element->instance->visible = 1;

                $element->id = $section->id;
                //shall we try to capture any title in there ?
                if (preg_match('/<h[1-7][^>]*?>(.*?)<\\/h[1-7][^>]*?>/i', $section->summary, $matches)){
                    $element->name = $matches[1];
                } else {
                    if ($section->section){
                        $element->name = 'section'.' '.(string)$section->section ;
                    } else {
                       // $element->name = get_string('headsection', 'report_trainingsessions') ;
                    }
                }

                if (!empty($section->sequence)) {
                    $element->subs = array();
                    $sequence = explode(",", $section->sequence);
                    foreach ($sequence as $seq) {
                        $sql = 'SELECT * FROM mdl_course_modules WHERE id = ?';
                        include('INC/connexion.php');
                        $req_modules = $bdd->prepare($sql);
                        $req_modules->execute(array($seq));
                        $cm = $req_modules->fetch(PDO::FETCH_OBJ);

                        if (!$cm)  { /*= $DB->get_record('course_modules', array('id' => $seq)))*/
                            // if (debugging()) notify("missing module of id $seq");
                            continue;
                        }
                        $sql = 'SELECT * FROM mdl_modules WHERE id = ?';
                        include('INC/connexion.php');
                        $req_modules2 = $bdd->prepare($sql);
                        $req_modules2->execute(array($cm->module));
                        $module = $req_modules2->fetch(PDO::FETCH_OBJ);


                       //$module = $DB->get_record('modules', array('id' => $cm->module));
                       if (preg_match('/label$/', $module->name)) continue; // discard all labels
                       
                        $sql = 'SELECT * FROM mdl_'.$module->name.' WHERE id = ?';
                        include('INC/connexion.php');
                        $req_modules3 = $bdd->prepare($sql);
                        $req_modules3->execute(array($cm->instance));
                        $moduleinstance = $req_modules3->fetch(PDO::FETCH_OBJ);
                    
                       //$moduleinstance = $DB->get_record($module->name, array('id' => $cm->instance));
                       $sub = new StdClass;
                       $sub->id                 = $cm->id;
                       $sub->plugin             = 'mod';
                       $sub->type               = $module->name;
                       $sub->instance           = $cm;
                       $sub->name               = $moduleinstance->name;
                        $sub->visible            = $cm->visible;
                       // $sub->visible            = 1;

                       $element->subs[] = $sub;
                       $itemcount++;
                    }
                }
                $structure[] = $element; 
            }


        return $structure;
    }

    function training_reports_format_time_frank($timevalue, $mode = 'html'){
        if ($timevalue){
                $secs = $timevalue % 60;
                $mins = floor($timevalue / 60);
                $hours = floor($mins / 60);
                $mins = $mins % 60;
                return "{$hours}h {$mins}m {$secs}s";
        } else {
            if ($mode == 'html'){
                return get_string('unvisited', 'report_trainingsessions');
            }
            return '';
        }
    }

    function training_reports_print_html_frank(&$str, $structure, &$aggregate, &$done, $indent='', $level = 1){
        global $CFG, $COURSE;

        $ignoremodulelist = array(); 

        if (empty($structure)) {
            $str .= 'No structure';
            return;
        }

        $indent = str_repeat('&nbsp;&nbsp;', $level);
        $suboutput = '';

        // initiates a blank dataobject
        if (!isset($dataobject)){
            $dataobject = new StdClass;
            $dataobject->elapsed = 0;
            $dataobject->events = 0;
        }

        if (is_array($structure)){
            // if an array of elements produce sucessively each output and collect aggregates
            foreach($structure as $element){
               // if (isset($element->instance) && empty($element->instance->visible)) continue; 
                $res = training_reports_print_html_frank($str, $element, $aggregate, $done, $indent, $level);
                $dataobject->elapsed += $res->elapsed;
                $dataobject->events += (0 + @$res->events);
            } 
        } else {
            $nodestr = '';
              // non visible items should not be displayed

                // name is not empty. It is a significant module (non structural)
                if (!empty($structure->name)){
                    $nodestr .= "<table class=\"sessionreport level$level\">";
                    $nodestr .= "<tr class=\"sessionlevel{$level}\" valign=\"top\">";
                    $nodestr .= "<td class=\"sessionitem item\" width=\"40%\">";
                    $nodestr .= $indent;
                   // $nodestr .= shorten_text($structure->name, 85);
                    $nodestr .= '</td>';
                    $nodestr .= "<td class=\"sessionitem rangedate\" width=\"20%\">";
                    if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                        $nodestr .= date('Y/m/d h:i', 0 + (@$aggregate[$structure->type][$structure->id]->firstaccess));
                    }
                    $nodestr .= '</td>';
                    $nodestr .= "<td class=\"sessionitem rangedate\" width=\"20%\">";
                    if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                        $nodestr .= date('Y/m/d h:i', 0 + (@$aggregate[$structure->type][$structure->id]->lastaccess));
                    }
                    $nodestr .= '</td>';
                    $nodestr .= "<td class=\"reportvalue rangedate\" align=\"right\" width=\"20%\">";
                    if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                        $done++;
                        $dataobject = $aggregate[$structure->type][$structure->id];
                    } 
                    if (!empty($structure->subs)) {
                        $res = training_reports_print_html_frank($suboutput, $structure->subs, $aggregate, $done, $indent, $level + 1);
                        $dataobject->elapsed += $res->elapsed;
                        $dataobject->events += $res->events;
                    }                

                    if (!in_array($structure->type, $ignoremodulelist)){
                        if (!empty($dataobject->timesource) && $dataobject->timesource == 'credit' && $dataobject->elapsed){
                            //$nodestr .= get_string('credittime', 'block_use_stats');
                            $nodestr .= 'credittime';
                        }
                        if (!empty($dataobject->timesource) && $dataobject->timesource == 'declared' && $dataobject->elapsed){
                            //$nodestr .= get_string('declaredtime', 'block_use_stats');
                            $nodestr .= 'declaredtime';
                        }
                       // $nodestr .= training_reports_format_time($dataobject->elapsed, 'html');
                        $nodestr .= ' ('.(0 + @$dataobject->events).')';
                    } else {
                        //$nodestr .= get_string('ignored', 'block_use_stats');
                        $nodestr .= 'ignored';
                    }
        
                    // plug here specific details
                    $nodestr .= '</td>';
                    $nodestr .= '</tr>';
                    $nodestr .= "</table>\n";
                } else {
                    // It is only a structural module that should not impact on level
                    if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                        $dataobject = $aggregate[$structure->type][$structure->id];
                    }
                    if (!empty($structure->subs)) {
                        $res = training_reports_print_html_frank($suboutput, $structure->subs, $aggregate, $done, $indent, $level);
                        $dataobject->elapsed += $res->elapsed;
                        $dataobject->events += $res->events;
                    }
                }
        
                if (!empty($structure->subs)){
                    $str .= "<table class=\"trainingreport subs\">";
                    $str .= "<tr valign=\"top\">";
                    $str .= "<td colspan=\"2\">";
                    $str .= '<br/>';
                    $str .= $suboutput;
                    $str .= '</td>';
                    $str .= '</tr>';
                    $str .= "</table>\n";
                }
                $str .= $nodestr;
            
        }   
        return $dataobject;
    }


    /* -------------------------------------------------------------------------------------------------------------------------------- */






        function temps_equivalent($userid, $courseid) {



           //ob_start();
          
         //   include_once '../../blocks/use_stats/locallib.php'; 

            $data = new StdClass;
            $data->from = 1483226000;
            $data->userid = $userid;
        //  $data->fromstart = -1;
            $data->output = 'html';

        // get data

            $logusers = $data->userid;  //$logusers ne sert a rien derierre !
            
            $logs = use_stats_extract_logs_frank($data->from, time(), $data->userid, $courseid);
            if (empty($logs)) return 0;


            $aggregate = use_stats_aggregate_logs_frank($logs, 'module');


            if (empty($aggregate['sessions'])) $aggregate['sessions'] = array();
        // get course structure

            $coursestructure = reports_get_course_structure_frank($courseid, $items);
            
        // print result

            if ($data->output == 'html'){
               //    require_once('htmlrenderers.php');
                // time period form

                
                $str = '';
                $dataobject = training_reports_print_html_frank($str, $coursestructure, $aggregate, $done);
             //   echo '<br /> On sort de course print_html !';
                $dataobject->items = $items;
                $dataobject->done = $done;

                if ($dataobject->done > $items) $dataobject->done = $items;
                
                // in-activity 

                $dataobject->activityelapsed = @$aggregate['activities'][$COURSE->id]->elapsed;
                $dataobject->activityhits = @$aggregate['activities'][$COURSE->id]->events;
                
                $dataobject->course = new StdClass;
                // calculate in-course-out-activities
                $dataobject->course->elapsed = 0;
                $dataobject->course->hits = 0;
                if (!empty($aggregate['course'])){
                    foreach($aggregate['course'] as $citemid => $courselevel){
                        $dataobject->course->elapsed = 0 + @$dataobject->course->elapsed + @$aggregate['course'][$citemid]->elapsed;
                        $dataobject->course->hits = 0 + @$dataobject->course->hits + @$aggregate['course'][$citemid]->events;
                    }
                }

                // calculate everything        
                $dataobject->elapsed += $dataobject->course->elapsed;
                $dataobject->hits = $dataobject->activityhits + $dataobject->course->hits;

                $dataobject->sessions = (!empty($aggregate['sessions'])) ? count(@$aggregate['sessions']) - 1 : 0 ;
                if (array_key_exists('upload', $aggregate)){
                    $dataobject->elapsed += @$aggregate['upload'][0]->elapsed;
                    $dataobject->upload = new StdClass;
                    $dataobject->upload->elapsed = 0 + @$aggregate['upload'][0]->elapsed;
                    $dataobject->upload->hits = 0 + @$aggregate['upload'][0]->events;
                }

           

            }    
                
            return $dataobject->elapsed;
        }

        function  seconds_to_hours($time){
            $hours = (int)($time / 3600);
            $minutes = (int)(($time % 3600) /60);
            $seconds = (int)($time % 60);

            $str = $hours . ":" . $minutes . ":" . $seconds;

            return $str;
        }

        function  seconds_to_hours_text($time){
            $hours = (int)($time / 3600);
            $minutes = (int)(($time % 3600) /60);
            $seconds = (int)($time % 60);

            $str = $hours . " h " . $minutes . " m " . $seconds. " s";

            return $str;
        }


        function temps_equivalent_activite($userid, $courseid) {

             global $CFG, $COURSE;


           //ob_start();
          
         //   include_once '../../blocks/use_stats/locallib.php'; 

            $data = new StdClass;
            $data->from = 1483226000;
            $data->userid = $userid;
        //  $data->fromstart = -1;
            $data->output = 'html';

        // get data

            $logusers = $data->userid;  //$logusers ne sert a rien derierre !
            
            $logs = use_stats_extract_logs_frank($data->from, time(), $data->userid, $courseid);
            if (empty($logs)) return 0;


            $aggregate = use_stats_aggregate_logs_frank($logs, 'module');


            if (empty($aggregate['sessions'])) $aggregate['sessions'] = array();
        // get course structure

            $coursestructure = reports_get_course_structure_frank($courseid, $items);
            
        // print result

            if ($data->output == 'html'){
               //    require_once('htmlrenderers.php');
                // time period form

                
                $str = '';
                $dataobject = training_reports_print_html_frank($str, $coursestructure, $aggregate, $done);
             //   echo '<br /> On sort de course print_html !';
                $dataobject->items = $items;
                $dataobject->done = $done;

                if ($dataobject->done > $items) $dataobject->done = $items;
                
                // in-activity 

                $dataobject->activityelapsed = @$aggregate['activities'][$courseid]->elapsed;
                $dataobject->activityhits = @$aggregate['activities'][$courseid]->events;
                
                $dataobject->course = new StdClass;
                // calculate in-course-out-activities
                $dataobject->course->elapsed = 0;
                $dataobject->course->hits = 0;
                if (!empty($aggregate['course'])){
                    foreach($aggregate['course'] as $citemid => $courselevel){
                        $dataobject->course->elapsed = 0 + @$dataobject->course->elapsed + @$aggregate['course'][$citemid]->elapsed;
                        $dataobject->course->hits = 0 + @$dataobject->course->hits + @$aggregate['course'][$citemid]->events;
                    }
                }

                // calculate everything        
                $dataobject->elapsed += $dataobject->course->elapsed;
                $dataobject->hits = $dataobject->activityhits + $dataobject->course->hits;

                $dataobject->sessions = (!empty($aggregate['sessions'])) ? count(@$aggregate['sessions']) - 1 : 0 ;
                if (array_key_exists('upload', $aggregate)){
                    $dataobject->elapsed += @$aggregate['upload'][0]->elapsed;
                    $dataobject->upload = new StdClass;
                    $dataobject->upload->elapsed = 0 + @$aggregate['upload'][0]->elapsed;
                    $dataobject->upload->hits = 0 + @$aggregate['upload'][0]->events;
                }

           

            }    
                
            return $dataobject->activityelapsed;
        }

?>

