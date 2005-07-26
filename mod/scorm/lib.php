<?php  // $Id$

/// Library of functions and constants for module scorm
/// (replace scorm with the name of your module and delete this line)

require_once('xml2array.class.php');  // Used to parse manifest

define('VALUESCOES', '0');
define('VALUEHIGHEST', '1');
define('VALUEAVERAGE', '2');
define('VALUESUM', '3');
$SCORM_GRADE_METHOD = array (VALUESCOES => get_string('gradescoes', 'scorm'),
                             VALUEHIGHEST => get_string('gradehighest', 'scorm'),
                             VALUEAVERAGE => get_string('gradeaverage', 'scorm'),
                             VALUESUM => get_string('gradesum', 'scorm'));

//if (!isset($CFG->scorm_validate)) {
//    $scormvalidate = 'none';
//    if (extension_loaded('domxml') && version_compare(phpversion(),'5.0.0','<')) {
//        $scormvalidate = 'domxml';
//    }
//    if (version_compare(phpversion(),'5.0.0','>=')) {
//        $scormvalidate = 'php5';
//    }
//    set_config('scorm_validate', $scormvalidate);
//}

if (!isset($CFG->scorm_frameheight)) {
    set_config('scorm_frameheight','600');
}

if (!isset($CFG->scorm_framewidth)) {
    set_config('scorm_framewidth','800');
}

function scorm_add_instance($scorm) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $scorm->timemodified = time();

    # May have to add extra stuff in here #
    global $CFG;

    $id = insert_record('scorm', $scorm);

    //
    // Rename temp scorm dir to scorm id
    //

    $scormdir = $CFG->dataroot.'/'.$scorm->course.'/moddata/scorm';
    rename($scormdir.$scorm->datadir,$scormdir.'/'.$id);
    //
    // Parse scorm manifest
    //
    if ($scorm->launch == 0) {
        $scorm->launch = scorm_parse($scormdir.'/'.$id,$scorm->pkgtype,$id);
        set_field('scorm','launch',$scorm->launch,'id',$id);
    }

    return $id;
}


function scorm_update_instance($scorm) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $scorm->timemodified = time();
    $scorm->id = $scorm->instance;

    # May have to add extra stuff in here #
    global $CFG;


    //
    // Check if scorm manifest needs to be reparsed
    //
    if ($scorm->launch == 0) {
        $scormdir = $CFG->dataroot.'/'.$scorm->course.'/moddata/scorm';
        if (isset($scorm->datadir) && ($scorm->datadir != $scorm->id)) {
            scorm_delete_files($scormdir.'/'.$scorm->id);
            rename($scormdir.$scorm->datadir,$scormdir.'/'.$scorm->id);
        }
        $scorm->launch = scorm_parse($scormdir.'/'.$scorm->id,$scorm->pkgtype,$scorm->id);
    }

    return update_record('scorm', $scorm);
}


function scorm_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    require('../config.php');

    if (! $scorm = get_record('scorm', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent files #
    scorm_delete_files($CFG->dataroot.'/'.$scorm->course.'/moddata/scorm/'.$scorm->id);

    # Delete any dependent records here #
    if (! delete_records('scorm_scoes_track', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_scoes', 'scorm', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm', 'id', $scorm->id)) {
        $result = false;
    }


    return $result;
}

function scorm_user_outline($course, $user, $mod, $scorm) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.

    $return = NULL;
    $scores->values = 0;
    $scores->sum = 0;
    $scores->max = 0;
    $scores->lastmodify = 0;
    $scores->count = 0;
    if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' ORDER BY id")) {
        foreach ($scoes as $sco) {
            if ($sco->launch!='') {
		$scores->count++;
		if ($userdata = scorm_get_tracks($sco->id, $user->id)) {
                    if (!isset($scores->{$userdata->status})) {
                        $scores->{$userdata->status} = 1;
                    } else {    
                        $scores->{$userdata->status}++;
                    }
                    if (!empty($userdata->score_raw)) {
                        $scores->values++;
                        $scores->sum += $userdata->score_raw;
                        $scores->max = ($userdata->score_raw > $scores->max)?$userdata->score_raw:$scores->max;
                    }
                    if (isset($userdata->timemodified) && ($userdata->timemodified > $scores->lastmodify)) {
                        $scores->lastmodify = $userdata->timemodified;
                    }
                }
            }
        }
        switch ($scorm->grademethod) {
            case VALUEHIGHEST:
		if ($scores->values > 0) {
                    $return->info = get_string('score','scorm').':&nbsp;'.$scores->max;
                    $return->time = $scores->lastmodify;
                }
            break;
            case VALUEAVERAGE:
                if ($scores->values > 0) {
                    $return->info = get_string('score','scorm').':&nbsp;'.$scores->sum/$scores->values;
                    $return->time = $scores->lastmodify;
                }
            break;
            case VALUESUM:
                if ($scores->values > 0) {
                    $return->info = get_string('score','scorm').':&nbsp;'.$scores->sum;
                    $return->time = $scores->lastmodify;
                }
            break;
            case VALUESCOES:
                $return->info = '';
                $scores->notattempted = $scores->count;
                if (isset($scores->completed)) {
		    $return->info .= get_string('completed','scorm').':&nbsp;'.$scores->completed.'<br />';
                    $scores->notattempted -= $scores->completed;
                }
                if (isset($scores->passed)) {
		    $return->info .= get_string('passed','scorm').':&nbsp;'.$scores->passed.'<br />';
                    $scores->notattempted -= $scores->passed;
                }
                if (isset($scores->failed)) {
		    $return->info .= get_string('failed','scorm').':&nbsp;'.$scores->failed.'<br />';
                    $scores->notattempted -= $scores->failed;
                }
                if (isset($scores->incomplete)) {
		    $return->info .= get_string('incomplete','scorm').':&nbsp;'.$scores->incomplete.'<br />';
                    $scores->notattempted -= $scores->incomplete;
                }
                if (isset($scores->browsed)) {
		    $return->info .= get_string('browsed','scorm').':&nbsp;'.$scores->browsed.'<br />';
                    $scores->notattempted -= $scores->browsed;
                }
                $return->time = $scores->lastmodify;
                if ($return->info == '') {
                    $return = NULL;
                } else {
		    $return->info .= get_string('notattempted','scorm').':&nbsp;'.$scores->notattempted.'<br />';
                }
            break;
        }
    }
    return $return;
}

function scorm_user_complete($course, $user, $mod, $scorm) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.
    global $CFG;

    $liststyle = 'structlist';
    $scormpixdir = $CFG->wwwroot.'/mod/scorm/pix/';
    $now = time();
    $firstmodify = $now;
    $lastmodify = 0;
    $sometoreport = false;
    $report = '';
    
    if ($orgs = get_records_select('scorm_scoes',"scorm='$scorm->id' AND organization='' AND launch=''",'id','id,identifier,title')) {
        if (count($orgs) <= 1) {
            unset($orgs);
            $orgs[]->identifier = '';
        }
        foreach ($orgs as $org) {
            $organizationsql = '';
            $currentorg = '';
            if (!empty($org->identifier)) {
                $report .= '<div class="orgtitle">'.$org->title.'</div>';
                $currentorg = $org->identifier;
                $organizationsql = "AND organization='$currentorg'";
            }
            $report .= "<ul id='0' class='$liststyle'>";
            if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' $organizationsql order by id ASC")){
                $level=0;
                $sublist=1;
                $parents[$level]='/';
                foreach ($scoes as $sco) {
                    if ($parents[$level]!=$sco->parent) {
                        if ($level>0 && $parents[$level-1]==$sco->parent) {
                            $report .= "\t\t</ul></li>\n";
                            $level--;
                        } else {
                            $i = $level;
                            $closelist = '';
                            while (($i > 0) && ($parents[$level] != $sco->parent)) {
                                $closelist .= "\t\t</ul></li>\n";
                                $i--;
                            }
                            if (($i == 0) && ($sco->parent != $currentorg)) {
                                $report .= "\t\t<li><ul id='$sublist' class='$liststyle'>\n";
                                $level++;
                            } else {
                                $report .= $closelist;
                                $level = $i;
                            }
                            $parents[$level]=$sco->parent;
                        }
                    }
                    $report .= "\t\t<li>";
                    $nextsco = next($scoes);
                    if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
                        $sublist++;
                    } else {
                        $report .= '<img src="'.$scormpixdir.'spacer.gif" />';
                    }

                    if ($sco->launch) {
                        $score = '';
                        $totaltime = '';
                        if ($usertrack=scorm_get_tracks($sco->id,$user->id)) {
                            if ($usertrack->status == '') {
                                $usertrack->status = 'notattempted';
                            }
                            $strstatus = get_string($usertrack->status,'scorm');
                            $report .= "<img src='".$scormpixdir.$usertrack->status.".gif' alt='$strstatus' title='$strstatus' />";
                            //if ($usertrack->score_raw != '') {
                            //    $score = ' - ('.get_string('score','scorm').':&nbsp;'.$usertrack->score_raw.')';
                            //}
                            //if ($usertrack->total_time != '00:00:00') {
                            //    $totaltime = ' - ('.get_string('totaltime','scorm').':&nbsp;'.$usertrack->total_time.')';
                            //}
                            if ($usertrack->timemodified != 0) {
                                if ($usertrack->timemodified > $lastmodify) {
                                    $lastmodify = $usertrack->timemodified;
                                }
                                if ($usertrack->timemodified < $firstmodify) {
                                    $firstmodify = $usertrack->timemodified;
                                }
                            }
                        } else {
                            if ($sco->scormtype == 'sco') {
                                $report .= '<img src="'.$scormpixdir.'notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                            } else {
                                $report .= '<img src="'.$scormpixdir.'asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                            }
                        }
                        $report .= "&nbsp;$sco->title $score$totaltime</li>\n";
                        if ($usertrack !== false) {
                            $sometoreport = true;
                            $report .= "\t\t\t<li><ul class='$liststyle'>\n";
                            foreach($usertrack as $element => $value) {
                                if (substr($element,0,3) == 'cmi') {
                                    $report .= '<li>'.$element.' => '.$value.'</li>';
                                }
                            }
                            $report .= "\t\t\t</ul></li>\n";
                        } 
                    } else {
                        $report .= "&nbsp;$sco->title</li>\n";
                    }
                }
                for ($i=0;$i<$level;$i++) {
                    $report .= "\t\t</ul></li>\n";
                }
            }
            $report .= "\t</ul><br />\n";
        }
    }
    if ($sometoreport) {
        if ($firstmodify < $now) {
            $timeago = format_time($now - $firstmodify);
            echo get_string('firstaccess','scorm').': '.userdate($firstmodify).' ('.$timeago.")<br />\n";
        }
        if ($lastmodify > 0) {
            $timeago = format_time($now - $lastmodify);
            echo get_string('lastaccess','scorm').': '.userdate($lastmodify).' ('.$timeago.")<br />\n";
        }
        echo get_string('report','scorm').":<br />\n";
        echo $report;
    } else {
    	print_string('noactivity','scorm');
    }

    return true;
}

function scorm_print_recent_activity(&$logs, $isteacher=false) {
/// Given a list of logs, assumed to be those since the last login
/// this function prints a short list of changes related to this module
/// If isteacher is true then perhaps additional information is printed.
/// This function is called from course/lib.php: print_recent_activity()

    return false;  // True if anything was printed, otherwise false
}

function scorm_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function scorm_grades($scormid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.

    global $CFG;

    if (!$scorm = get_record('scorm', 'id', $scormid)) {
        return NULL;
    }
    if (!$scoes = get_records('scorm_scoes','scorm',$scormid)) {
        return NULL;
    }

    if ($scorm->grademethod == VALUESCOES) {
        if (!$return->maxgrade = count_records_select('scorm_scoes',"scorm='$scormid' AND launch<>''")) {
            return NULL;
        }
    } else {
        $return->maxgrade = $scorm->maxgrade;
    }

    $return->grades = NULL;
    if ($scousers=get_records_select('scorm_scoes_track', "scormid='$scormid' GROUP BY userid", "", "userid,null")) {
        foreach ($scousers as $scouser) {
            $scores = NULL;
            $scores->scoes = 0;
            $scores->values = 0;
            $scores->max = 0;
            $scores->sum = 0;

            foreach ($scoes as $sco) {
                $userdata=scorm_get_tracks($sco->id, $scouser->userid);
                if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                    $scores->scoes++;
                }
                if (!empty($userdata->score_raw)) {
                    $scores->values++;
                    $scores->sum += $userdata->score_raw;
                    $scores->max = ($userdata->score_raw > $scores->max)?$userdata->score_raw:$scores->max;
                }
            }
            switch ($scorm->grademethod) {
                case VALUEHIGHEST:
                    $return->grades[$scouser->userid] = $scores->max;
                break;
                case VALUEAVERAGE:
                    if ($scores->values > 0) {
                        $return->grades[$scouser->userid] = $scores->sum/$scores->values;
                    } else {
                        $return->grades[$scouser->userid] = 0;
                    }
                break;
                case VALUESUM:
                    $return->grades[$scouser->userid] = $scores->sum;
                break;
                case VALUESCOES:
                    $return->grades[$scouser->userid] = $scores->scoes;
                break;
            }
       }
    }
    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other scorm functions go here.  Each of them must have a name that
/// starts with scorm_


function scorm_randstring($len = '8') {
    $rstring = NULL;
    $lchar = '';
    for ($i=0; $i<$len; $i++) {
        $char = chr(rand(48,122));
        while (!ereg('[a-zA-Z0-9]', $char)){
            if ($char == $lchar) continue;
            $char = chr(rand(48,90));
        }
        $rstring .= $char;
        $lchar = $char;
    }
    return $rstring;
}


function scorm_datadir($strPath, $existingdir='')
{
    global $CFG;

    if (($existingdir!='') && (is_dir($strPath.'/'.$existingdir)))
        return $strPath.'/'.$existingdir;

    if (is_dir($strPath)) {
        do {
            $datadir='/'.scorm_randstring();
        } while (file_exists($strPath.$datadir));
        mkdir($strPath.$datadir, $CFG->directorypermissions);
        @chmod($strPath.$datadir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        return $strPath.$datadir;
    } else {
        return false;
    }
}

function scorm_validate($packagedir) {
    if (is_file($packagedir.'/imsmanifest.xml')) {
        $validation->result = 'found';
        $validation->pkgtype = 'SCORM';
    } else {
        if ($handle = opendir($packagedir)) {
            while (($file = readdir($handle)) !== false) {
                $ext = substr($file,strrpos($file,'.'));
                if (strtolower($ext) == '.cst') {
                    $validation->result = 'found';
                    $validation->pkgtype = 'AICC';
                    break;
                }
            }
            closedir($handle);
        }
        if (!isset($validation)) {
            $validation->result = 'nomanifest';
            $validation->pkgtype = 'SCORM';
        }
    }
    return $validation;
}

function scorm_delete_files($directory) {
    if (is_dir($directory)) {
        $files=scorm_scandir($directory);
        foreach($files as $file) {
            if (($file != '.') && ($file != '..')) {
                if (!is_dir($directory.'/'.$file)) {
                    unlink($directory.'/'.$file);
                } else {
                    scorm_delete_files($directory.'/'.$file);
                }
            }
        }
        rmdir($directory);
    }
}

function scorm_scandir($directory) {
    if (version_compare(phpversion(),'5.0.0','>=')) {
        return scandir($directory);
    } else {
        $files = null;
        if ($dh = opendir($directory)) {
            while (($file = readdir($dh)) !== false) {
               $files[] = $file;
            }
            closedir($dh);
        }
        return $files;
    }
}

function scorm_parse($pkgdir,$pkgtype,$scormid){
    delete_records('scorm_scoes','scorm',$scormid);
    delete_records('scorm_scoes_track','scormid',$scormid);

    if ($pkgtype == 'AICC') {
        return scorm_parse_aicc($pkgdir,$scormid);
    } else {
        return scorm_parse_scorm($pkgdir.'/imsmanifest.xml',$scormid);
    }
}

function scorm_get_aicc_columns($row,$mastername='system_id') {
    $tok = strtok(strtolower($row),"\",\n\r");
    $result->columns = array();
    $i=0;
    while ($tok) {
        if ($tok !='') {
            $result->columns[] = $tok;
            if ($tok == $mastername) {
                $result->mastercol = $i;
            }
            $i++;
        }
        $tok = strtok("\",\n\r");
    }
    return $result;
}

function scorm_forge_cols_regexp($columns,$remodule='(".*")?,') {
    $regexp = '/^';
    foreach ($columns as $column) {
        $regexp .= $remodule;
    }
    $regexp = substr($regexp,0,-1) . '/';
    return $regexp;
}

function scorm_parse_aicc($pkgdir,$scormid){
    $version = 'AICC';
    $ids = array();
    $courses = array();
    if ($handle = opendir($pkgdir)) {
        while (($file = readdir($handle)) !== false) {
            $ext = substr($file,strrpos($file,'.'));
            $extension = strtolower(substr($ext,1));
            $id = strtolower(basename($file,$ext));
            $ids[$id]->$extension = $file;
        }
        closedir($handle);
    }
    foreach ($ids as $courseid => $id) {
        if (isset($id->crs)) {
            if (is_file($pkgdir.'/'.$id->crs)) {
                $rows = file($pkgdir.'/'.$id->crs);
                foreach ($rows as $row) {
                    if (preg_match("/^(.+)=(.+)$/",$row,$matches)) {
                        switch (strtolower(trim($matches[1]))) {
                            case 'course_id':
                                $courses[$courseid]->id = trim($matches[2]);
                            break;
                            case 'course_title':
                                $courses[$courseid]->title = trim($matches[2]);
                            break;
                            case 'version':
                                $courses[$courseid]->version = 'AICC_'.trim($matches[2]);
                            break;
                        }
                    }
                }
            }
        }
        if (isset($id->des)) {
            $rows = file($pkgdir.'/'.$id->des);
            $columns = scorm_get_aicc_columns($rows[0]);
            $regexp = scorm_forge_cols_regexp($columns->columns);
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($columns->columns);$j++) {
                        $column = $columns->columns[$j];
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]),1,-1)]->$column = substr(trim($matches[$j+1]),1,-1);
                    }
                }
            }
        }
        if (isset($id->au)) {
            $rows = file($pkgdir.'/'.$id->au);
            $columns = scorm_get_aicc_columns($rows[0]);
            $regexp = scorm_forge_cols_regexp($columns->columns);
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($columns->columns);$j++) {
                        $column = $columns->columns[$j];
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]),1,-1)]->$column = substr(trim($matches[$j+1]),1,-1);
                    }
                }
            }
        }
        if (isset($id->cst)) {
            $rows = file($pkgdir.'/'.$id->cst);
            $columns = scorm_get_aicc_columns($rows[0],'block');
            $regexp = scorm_forge_cols_regexp($columns->columns,'(.+)?,');
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($columns->columns);$j++) {
                        if ($j != $columns->mastercol) {
                            $courses[$courseid]->elements[substr(trim($matches[$j+1]),1,-1)]->parent = substr(trim($matches[$columns->mastercol+1]),1,-1);
                        }
                    }
                }
            }
        }
        if (isset($id->ort)) {
            $rows = file($pkgdir.'/'.$id->ort);
        }
        if (isset($id->pre)) {
            $rows = file($pkgdir.'/'.$id->pre);
            $columns = scorm_get_aicc_columns($rows[0],'structure_element');
            $regexp = scorm_forge_cols_regexp($columns->columns,'(.+),');
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    $courses[$courseid]->elements[$columns->mastercol+1]->prerequisites = substr(trim($matches[1-$columns->mastercol+1]),1,-1);
                }
            }
        }
        if (isset($id->cmp)) {
            $rows = file($pkgdir.'/'.$id->cmp);
        }
    }
    //print_r($courses);
    $launch = 0;
    if (isset($courses)) {
        foreach ($courses as $course) {
            unset($sco);
            $sco->identifier = $course->id;
            $sco->scorm = $scormid;
            $sco->organization = '';
            $sco->title = $course->title;
            $sco->parent = '/';
            $sco->launch = '';
            $sco->scormtype = '';
            //print_r($sco);
            $id = insert_record('scorm_scoes',$sco);
            if ($launch == 0) {
                $launch = $id;
            }
            if (isset($course->elements)) {
                foreach($course->elements as $element) {
                    unset($sco);
                    $sco->identifier = $element->system_id;
                    $sco->scorm = $scormid;
                    $sco->organization = $course->id;
                    $sco->title = $element->title;
                    if (strtolower($element->parent) == 'root') {
                        $sco->parent = '/';
                    } else {
                        $sco->parent = $element->parent;
                    }
                    if (isset($element->file_name)) {
                        $sco->launch = $element->file_name;
                        $sco->scormtype = 'sco';
                    } else {
                        $element->file_name = '';
                        $sco->scormtype = '';
                    }
                    if (!isset($element->prerequisites)) {
                        $element->prerequisites = '';
                    }
                    $sco->prerequisites = $element->prerequisites;
                    if (!isset($element->max_time_allowed)) {
                        $element->max_time_allowed = '';
                    }
                    $sco->maxtimeallowed = $element->max_time_allowed;
                    if (!isset($element->time_limit_action)) {
                        $element->time_limit_action = '';
                    }
                    $sco->timelimitaction = $element->time_limit_action;
                    if (!isset($element->mastery_score)) {
                        $element->mastery_score = '';
                    }
                    $sco->masteryscore = $element->mastery_score;
                    $sco->previous = 0;
                    $sco->next = 0;
                    $id = insert_record('scorm_scoes',$sco);
                    if ($launch==0) {
                        $launch = $id;
                    }
                }
            }
        }
    }
    set_field('scorm','version','AICC','id',$scormid);
    return $launch;
}

function scorm_get_resources($blocks) {
    foreach ($blocks as $block) {
        if ($block['name'] == 'RESOURCES') {
            foreach ($block['children'] as $resource) {
                if ($resource['name'] == 'RESOURCE') {
                    $resources[addslashes($resource['attrs']['IDENTIFIER'])] = $resource['attrs'];
                }
            }
        }
    }
    return $resources;
}

function scorm_get_manifest($blocks,$scoes) {
    static $parents = array();
    static $resources;

    static $manifest;
    static $organization;

    if (count($blocks) > 0) {
        foreach ($blocks as $block) {
            switch ($block['name']) {
                case 'METADATA':
                    if (isset($block['children'])) {
                        foreach ($block['children'] as $metadata) {
                            if ($metadata['name'] == 'SCHEMAVERSION') {
                                if (empty($scoes->version)) {
                                    if (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches)) {
                                        $scoes->version = 'SCORM_'.$matches[count($matches)-1];
                                    } else {
                                        $scoes->version = 'SCORM_1.2';
                                    }
                                }
                            }
                        }
                    }
                break;
                case 'MANIFEST':
                    $manifest = addslashes($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $resources = array();
                    $resources = scorm_get_resources($block['children']);
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    if (count($scoes->elements) <= 0) {
                        foreach ($resources as $item => $resource) {
                            if (!empty($resource['HREF'])) {
                                $sco = new stdClass();
                                $sco->identifier = $item;
                                $sco->title = $item;
                                $sco->parent = '/';
                                $sco->launch = addslashes($resource['HREF']);
                                $sco->scormtype = addslashes($resource['ADLCP:SCORMTYPE']);
                                $scoes->elements[$manifest][$organization][$item] = $sco;
                            }
                        }
                    }
                break;
                case 'ORGANIZATIONS':
                    if (!isset($scoes->defaultorg)) {
                        $scoes->defaultorg = addslashes($block['attrs']['DEFAULT']);
                    }
                    $scoes = scorm_get_manifest($block['children'],$scoes);
                break;
                case 'ORGANIZATION':
                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $organization = '';
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = '/';
                    $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                    $scoes->elements[$manifest][$organization][$identifier]->scormtype = '';

                    $parents = array();
                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);
                    $organization = $identifier;

                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    
                    array_pop($parents);
                break;
                case 'ITEM':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    
                    $identifier = addslashes($block['attrs']['IDENTIFIER']);
                    $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                    $scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
                    if (!isset($block['attrs']['ISVISIBLE'])) {
                        $block['attrs']['ISVISIBLE'] = 'true';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->isvisible = addslashes($block['attrs']['ISVISIBLE']);
                    if (!isset($block['attrs']['PARAMETERS'])) {
                        $block['attrs']['PARAMETERS'] = '';
                    }
                    $scoes->elements[$manifest][$organization][$identifier]->parameters = addslashes($block['attrs']['PARAMETERS']);
                    if (!isset($block['attrs']['IDENTIFIERREF'])) {
                        $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
                    } else {
                        $idref = addslashes($block['attrs']['IDENTIFIERREF']);
                        $scoes->elements[$manifest][$organization][$identifier]->launch = addslashes($resources[$idref]['HREF']);
                        if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
                            $resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = addslashes($resources[$idref]['ADLCP:SCORMTYPE']);
                    }

                    $parent = new stdClass();
                    $parent->identifier = $identifier;
                    $parent->organization = $organization;
                    array_push($parents, $parent);

                    $scoes = scorm_get_manifest($block['children'],$scoes);
                    
                    array_pop($parents);
                break;
                case 'TITLE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = addslashes($block['tagData']);
                break;
                case 'ADLCP:PREREQUISITES':
                    if ($block['attrs']['TYPE'] == 'aicc_script') {
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = addslashes($block['tagData']);
                    }
                break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = addslashes($block['tagData']);
                break;
                case 'ADLCP:TIMELIMITACTION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = addslashes($block['tagData']);
                break;
                case 'ADLCP:DATAFROMLMS':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = addslashes($block['tagData']);
                break;
                case 'ADLCP:MASTERYSCORE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = addslashes($block['tagData']);
                break;
            }
        }
    }

    return $scoes;
}


function scorm_parse_scorm($manifestfile,$scormid) {
    
    $launch = 0;

    if (is_file($manifestfile)) {
        $xmlstring = file_get_contents($manifestfile);
        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmlstring);
            
        $scoes = new stdClass();
        $scoes->version = 'SCORM';
        $scoes = scorm_get_manifest($manifests,$scoes);

        if (count($scoes->elements) > 0) {
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        $item->scorm = $scormid;
                        $item->manifest = $manifest;
                        $item->organization = $organization;
                        $id = insert_record('scorm_scoes',$item);
                
                        if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                            $launch = $id;
                        }
                    }
                }
            }
            set_field('scorm','version',$scoes->version,'id',$scormid);
        }
    } 
    
    return $launch;
}

function scorm_get_tracks($scoid,$userid) {
/// Gets all tracks of specified sco and user
    global $CFG;

    if ($tracks = get_records_select('scorm_scoes_track',"userid=$userid AND scoid=$scoid",'element ASC')) {
        $usertrack->userid = $userid;
        $usertrack->scoid = $scoid;
        $usertrack->score_raw = '';
        $usertrack->status = '';
        $usertrack->total_time = '00:00:00';
        $usertrack->session_time = '00:00:00';
        $usertrack->timemodified = 0;
        foreach ($tracks as $track) {
            $element = $track->element;
            $usertrack->{$element} = $track->value;
            switch ($element) {
                case 'cmi.core.lesson_status':
                case 'cmi.completition_status':
                    if ($track->value == 'not attempted') {
                        $track->value = 'notattempted';
                    }
                    $usertrack->status = $track->value;
                break;
                case 'cmi.core.score.raw':
                case 'cmi.score.raw':
                    $usertrack->score_raw = $track->value;
                break;
                case 'cmi.core.session_time':
                case 'cmi.session_time':
                    $usertrack->session_time = $track->value;
                break;
                case 'cmi.core.total_time':
                case 'cmi.total_time':
                    $usertrack->total_time = $track->value;
                break;
            }
            if (isset($track->timemodified) && ($track->timemodified > $usertrack->timemodified)) {
                $usertrack->timemodified = $track->timemodified;
            }
        }
        return $usertrack;
    } else {
        return false;
    }
}

function scorm_get_user_data($userid) {
/// Gets user info required to display the table of scorm results
/// for report.php

    return get_record('user','id',$userid,'','','','','firstname, lastname, picture');
}

function scorm_remove_spaces($sourcestr) {
// Remove blank space from a string
    $newstr='';
    for( $i=0; $i<strlen($sourcestr); $i++) {
        if ($sourcestr[$i]!=' ') {
            $newstr .=$sourcestr[$i];
        }
    }
    return $newstr;
}

function scorm_string_round($stringa, $len=11) {
// Crop a string to $len character and set an anchor title to the full string
    if (strlen($stringa)>$len) {
        return '<a name="none" title="'.$stringa.'">'.substr($stringa,0,$len-4).'...'.substr($stringa,strlen($stringa)-1,1).'</a>';
    } else {
        return $stringa;
    }
}

function scorm_insert_track($userid,$scormid,$scoid,$element,$value) {
    $id = null;
    if ($track = get_record_select('scorm_scoes_track',"userid='$userid' AND scormid='$scormid' AND scoid='$scoid' AND element='$element'")) {
        $track->value = $value;
        $track->timemodified = time();
        $id = update_record('scorm_scoes_track',$track);
    } else {
        $track->userid = $userid;
        $track->scormid = $scormid;
        $track->scoid = $scoid;
        $track->element = $element;
        $track->value = $value;
        $track->timemodified = time();
        $id = insert_record('scorm_scoes_track',$track);
    }
    return $id;
}

function scorm_add_time($a, $b) {
    $aes = explode(':',$a);
    $bes = explode(':',$b);
    $aseconds = explode('.',$aes[2]);
    $bseconds = explode('.',$bes[2]);
    $change = 0;

    $acents = 0;  //Cents
    if (count($aseconds) > 1) {
        $acents = $aseconds[1];
    }
    $bcents = 0;
    if (count($bseconds) > 1) {
        $bcents = $bseconds[1];
    }
    $cents = $acents + $bcents;
    $change = floor($cents / 100);
    $cents = $cents - ($change * 100);
    if (floor($cents) < 10) {
        $cents = '0'. $cents;
    }

    $secs = $aseconds[0] + $bseconds[0] + $change;  //Seconds
    $change = floor($secs / 60);
    $secs = $secs - ($change * 60);
    if (floor($secs) < 10) {
        $secs = '0'. $secs;
    }

    $mins = $aes[1] + $bes[1] + $change;   //Minutes
    $change = floor($mins / 60);
    $mins = $mins - ($change * 60);
    if ($mins < 10) {
        $mins = '0' .  $mins;
    }

    $hours = $aes[0] + $bes[0] + $change;  //Hours
    if ($hours < 10) {
        $hours = '0' . $hours;
    }

    if ($cents != '0') {
        return $hours . ":" . $mins . ":" . $secs . '.' . $cents;
    } else {
        return $hours . ":" . $mins . ":" . $secs;
    }
}

function scorm_external_link($link) {
// check if a link is external
    $result = false;
    $link = strtolower($link);
    if (substr($link,0,7) == 'http://') {
        $result = true;
    } else if (substr($link,0,8) == 'https://') {
        $result = true;
    } else if (substr($link,0,4) == 'www.') {
        $result = true;
    }
    return $result;
}

function scorm_count_launchable($scormid,$organization) {
    return count_records_select('scorm_scoes',"scorm=$scormid AND organization='$organization' AND launch<>''");
}

function scorm_display_structure($scorm,$liststyle,$currentorg='',$scoid='',$mode='normal',$play=false) {
    global $USER, $CFG;

    $strexpand = get_string('expcoll','scorm');

    echo "<ul id='0' class='$liststyle'>";
    $incomplete = false;
    $organizationsql = '';
    if (!empty($currentorg)) {
        $organizationsql = "AND organization='$currentorg'";
    }
    if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' $organizationsql order by id ASC")){
        $level=0;
        $sublist=1;
        $previd = 0;
        $nextid = 0;
        $parents[$level]='/';
        foreach ($scoes as $sco) {
            if ($parents[$level]!=$sco->parent) {
                if ($newlevel = array_search($sco->parent,$parents)) {
                    for ($i=0; $i<($level-$newlevel); $i++) {
                        echo "\t\t</ul></li>\n";
                    }
                    $level = $newlevel;
                } else {
                    $i = $level;
                    $closelist = '';
                    while (($i > 0) && ($parents[$level] != $sco->parent)) {
                        $closelist .= "\t\t</ul></li>\n";
                        $i--;
                    }
                    if (($i == 0) && ($sco->parent != $currentorg)) {
                        echo "\t\t<li><ul id='$sublist' class='$liststyle'>\n";
                        $level++;
                    } else {
                        echo $closelist;
                        $level = $i;
                    }
                    $parents[$level]=$sco->parent;
                }
            }
            echo "\t\t<li>";
            $nextsco = next($scoes);
            if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
                $sublist++;
                echo '<a href="#" onClick="expandCollide(img'.$sublist.','.$sublist.');"><img id="img'.$sublist.'" src="'.$CFG->wwwroot.'/mod/scorm/pix/minus.gif" alt="'.$strexpand.'" title="'.$strexpand.'"/></a>';
            } else {
                echo '<img src="'.$CFG->wwwroot.'/mod/scorm/pix/spacer.gif" />';
            }
            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }
            if ($sco->launch) {
                $startbold = '';
                $endbold = '';
                $score = '';
                if (empty($scoid) && ($mode != 'normal')) {
                    $scoid = $sco->id;
                }
                if ($usertrack=scorm_get_tracks($sco->id,$USER->id)) {
                    if ($usertrack->status == '') {
                        $usertrack->status = 'notattempted';
                    }
                    $strstatus = get_string($usertrack->status,'scorm');
                    echo "<img src='".$CFG->wwwroot."/mod/scorm/pix/".$usertrack->status.".gif' alt='$strstatus' title='$strstatus' />";
                    if (($usertrack->status == 'notattempted') || ($usertrack->status == 'incomplete') || ($usertrack->status == 'browsed')) {
                        $incomplete = true;
                        if ($play && empty($scoid)) {
                            $scoid = $sco->id;
                        }
                    }
                    if ($usertrack->score_raw != '') {
                        $score = '('.get_string('score','scorm').':&nbsp;'.$usertrack->score_raw.')';
                    }
                } else {
                    if ($play && empty($scoid)) {
                        $scoid = $sco->id;
                    }
                    if ($sco->scormtype == 'sco') {
                        echo '<img src="'.$CFG->wwwroot.'/mod/scorm/pix/notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                        $incomplete = true;
                    } else {
                        echo '<img src="'.$CFG->wwwroot.'/mod/scorm/pix/asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                    }
                }
                if ($sco->id == $scoid) {
                    $startbold = '<b>';
                    $endbold = '</b>';
                    if ($nextsco !== false) {
                        $nextid = $nextsco->id;
                    } else {
                        $nextid = 0;
                    }
                    $shownext = $sco->next;
                    $showprev = $sco->previous;
                }
                if (($nextid == 0) && (scorm_count_launchable($scorm->id,$currentorg) > 1) && ( $nextsco!==false)) {
                    $previd = $sco->id;
                }
                echo "&nbsp;$startbold<a href='javascript:playSCO(".$sco->id.");'>$sco->title</a> $score$endbold</li>\n";
            } else {
                echo "&nbsp;$sco->title</li>\n";
            }
        }
        for ($i=0;$i<$level;$i++) {
            echo "\t\t</ul></li>\n";
        }
    }
    echo "\t</ul>\n";
    if ($play) {
        $result->id = $scoid;
        $result->prev = $previd;
        $result->next = $nextid;
        $result->showprev = $showprev;
        $result->shownext = $shownext;
        return $result;
    } else {
        return $incomplete;
    }
}
?>
