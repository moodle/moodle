<?php  // $Id$

/**
* Given an object containing all the necessary data,
* (defined by the form in mod.html) this function
* will create a new instance and return the id number
* of the new instance.
*
* @param mixed $scorm Form data
* @return int
*/
function scorm_add_instance($scorm) {
    if(empty($scorm->datadir)) { //check to make sure scorm object is valid BEFORE entering it in the database.
        error(get_string('badpackage', 'scorm'));
    } else {
        global $CFG;
        $scorm->timemodified = time();

        $scorm = scorm_option2text($scorm);
        $scorm->width = str_replace('%','',$scorm->width);
        $scorm->height = str_replace('%','',$scorm->height);

        //sanitize submitted values a bit
        $scorm->width = clean_param($scorm->width, PARAM_INT);
        $scorm->height = clean_param($scorm->height, PARAM_INT);

        if (!isset($scorm->whatgrade)) {
            $scorm->whatgrade = 0;
        }
        $scorm->grademethod = ($scorm->whatgrade * 10) + $scorm->grademethod;

        $id = insert_record('scorm', $scorm);

        if ((basename($scorm->reference) != 'imsmanifest.xml') && ($scorm->reference[0] != '#')) {
            // Rename temp scorm dir to scorm id
            $scorm->dir = $CFG->dataroot.'/'.$scorm->course.'/moddata/scorm';
            rename($scorm->dir.$scorm->datadir,$scorm->dir.'/'.$id);
        }

        // Parse scorm manifest
        if ($scorm->parse == 1) {
            require_once('locallib.php');
            $scorm->id = $id;
            $scorm->launch = scorm_parse($scorm);
            set_field('scorm','launch',$scorm->launch,'id',$scorm->id);
        }

        return $id;
    }
}

/**
* Given an object containing all the necessary data,
* (defined by the form in mod.html) this function
* will update an existing instance with new data.
*
* @param mixed $scorm Form data
* @return int
*/
function scorm_update_instance($scorm) {

    global $CFG;

    $scorm->timemodified = time();
    $scorm->id = $scorm->instance;

    $scorm = scorm_option2text($scorm);
    $scorm->width = str_replace('%','',$scorm->width);
    $scorm->height = str_replace('%','',$scorm->height);

    if (!isset($scorm->whatgrade)) {
        $scorm->whatgrade = 0;
    }
    $scorm->grademethod = ($scorm->whatgrade * 10) + $scorm->grademethod;

    // Check if scorm manifest needs to be reparsed
    if ($scorm->parse == 1) {
        require_once('locallib.php');
        $scorm->dir = $CFG->dataroot.'/'.$scorm->course.'/moddata/scorm';
        if (is_dir($scorm->dir.'/'.$scorm->id)) {
            scorm_delete_files($scorm->dir.'/'.$scorm->id);
        }
        if (isset($scorm->datadir) && ($scorm->datadir != $scorm->id) && 
           (basename($scorm->reference) != 'imsmanifest.xml') && ($scorm->reference[0] != '#')) {
            rename($scorm->dir.$scorm->datadir,$scorm->dir.'/'.$scorm->id);
        }

/*          delete_records('scorm_sequencing_controlmode','scormid',$scorm->id);
            delete_records('scorm_sequencing_rolluprules','scormid',$scorm->id);
            delete_records('scorm_sequencing_rolluprule','scormid',$scorm->id);
            delete_records('scorm_sequencing_rollupruleconditions','scormid',$scorm->id);
            delete_records('scorm_sequencing_rolluprulecondition','scormid',$scorm->id);
            delete_records('scorm_sequencing_ruleconditions','scormid',$scorm->id);
            delete_records('scorm_sequencing_rulecondition','scormid',$scorm->id);
*/
        $scorm->launch = scorm_parse($scorm);
    } else {
        $oldscorm = get_record('scorm','id',$scorm->id);
        $scorm->reference = $oldscorm->reference; // This fix a problem with Firefox when the teacher choose Cancel on overwrite question
    }
    
    return update_record('scorm', $scorm);
}

/**
* Given an ID of an instance of this module,
* this function will permanently delete the instance
* and any data that depends on it.
*
* @param int $id Scorm instance id
* @return boolean
*/
function scorm_delete_instance($id) {

    global $CFG;

    if (! $scorm = get_record('scorm', 'id', $id)) {
        return false;
    }

    $result = true;

    $scorm->dir = $CFG->dataroot.'/'.$scorm->course.'/moddata/scorm';
    if (is_dir($scorm->dir.'/'.$scorm->id)) {
        // Delete any dependent files
        require_once('locallib.php');
        scorm_delete_files($scorm->dir.'/'.$scorm->id);
    }

    // Delete any dependent records
    if (! delete_records('scorm_scoes_track', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_scoes', 'scorm', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm', 'id', $scorm->id)) {
        $result = false;
    }

    /*if (! delete_records('scorm_sequencing_controlmode', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_sequencing_rolluprules', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_sequencing_rolluprule', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_sequencing_rollupruleconditions', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_sequencing_rolluprulecondition', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_sequencing_rulecondition', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_sequencing_ruleconditions', 'scormid', $scorm->id)) {
        $result = false;
    }*/       
    return $result;
}

/**
* Return a small object with summary information about what a
* user has done with a given particular instance of this module
* Used for user activity reports.
*
* @param int $course Course id
* @param int $user User id
* @param int $mod  
* @param int $scorm The scorm id
* @return mixed
*/
function scorm_user_outline($course, $user, $mod, $scorm) { 

    $return = NULL;

    require_once('locallib.php');

    $return = scorm_grade_user($scorm, $user->id, true);

    return $return;
}

/**
* Print a detailed representation of what a user has done with
* a given particular instance of this module, for user activity reports.
*
* @param int $course Course id
* @param int $user User id
* @param int $mod  
* @param int $scorm The scorm id
* @return boolean
*/
function scorm_user_complete($course, $user, $mod, $scorm) {
    global $CFG;

    $liststyle = 'structlist';
    $scormpixdir = $CFG->modpixpath.'/scorm/pix';
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
        $report .= '<div class="mod-scorm">'."\n";
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
                        $report .= '<img src="'.$scormpixdir.'/spacer.gif" />';
                    }

                    if ($sco->launch) {
                        require_once('locallib.php');
                        $score = '';
                        $totaltime = '';
                        if ($usertrack=scorm_get_tracks($sco->id,$user->id)) {
                            if ($usertrack->status == '') {
                                $usertrack->status = 'notattempted';
                            }
                            $strstatus = get_string($usertrack->status,'scorm');
                            $report .= "<img src='".$scormpixdir.'/'.$usertrack->status.".gif' alt='$strstatus' title='$strstatus' />";
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
                                $report .= '<img src="'.$scormpixdir.'/'.'notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                            } else {
                                $report .= '<img src="'.$scormpixdir.'/'.'asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
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
        $report .= "</div>\n";
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

/**
* Given a list of logs, assumed to be those since the last login
* this function prints a short list of changes related to this module
* If isteacher is true then perhaps additional information is printed.
* This function is called from course/lib.php: print_recent_activity()
*
* @param reference $logs Logs reference
* @param boolean $isteacher
* @return boolean
*/
function scorm_print_recent_activity(&$logs, $isteacher=false) {
    return false;  // True if anything was printed, otherwise false
}

/**
* Function to be run periodically according to the moodle cron
* This function searches for things that need to be done, such
* as sending out mail, toggling flags etc ...
*
* @return boolean
*/
function scorm_cron () {

    global $CFG;

    return true;
}

/**
* Given a scorm id return all the grades of that activity
*
* @param int $scormid Scorm instance id
* @return mixed
*/
function scorm_grades($scormid) {

    global $CFG;

    if (!$scorm = get_record('scorm', 'id', $scormid)) {
        return NULL;
    }

    if (($scorm->grademethod % 10) == 0) { // GRADESCOES
        if (!$return->maxgrade = count_records_select('scorm_scoes',"scorm='$scormid' AND launch<>''")) {
            return NULL;
        }
    } else {
        $return->maxgrade = $scorm->maxgrade;
    }

    $return->grades = NULL;
    if ($scousers=get_records_select('scorm_scoes_track', "scormid='$scormid' GROUP BY userid", "", "userid,null")) {
        require_once('locallib.php');
        foreach ($scousers as $scouser) {
            $return->grades[$scouser->userid] = scorm_grade_user($scorm, $scouser->userid);
        }
    }
    return $return;
}

function scorm_get_view_actions() {
    return array('pre-view','view','view all','report');
}

function scorm_get_post_actions() {
    return array();
}

function scorm_option2text($scorm) {
    global $CFG;

    require_once('locallib.php');

    if (isset($scorm->popup)) {
        if ($scorm->popup) {
            $optionlist = array();
            foreach ($SCORM_POPUP_OPTIONS as $name => $option) {
                if (isset($scorm->$name)) {
                    $optionlist[] = $name.'='.$scorm->$name;
                } else {
                    $optionlist[] = $name.'=0';
                }
            }       
            $scorm->options = implode(',', $optionlist);
        } else {
            $scorm->options = '';
        } 
    } else {
        $scorm->popup = 0;
        $scorm->options = '';
    }
    return $scorm;
}

?>
