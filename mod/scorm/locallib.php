<?php  // $Id$

/// Constants and settings for module scorm

define('GRADESCOES', '0');
define('GRADEHIGHEST', '1');
define('GRADEAVERAGE', '2');
define('GRADESUM', '3');
$SCORM_GRADE_METHOD = array (GRADESCOES => get_string('gradescoes', 'scorm'),
                             GRADEHIGHEST => get_string('gradehighest', 'scorm'),
                             GRADEAVERAGE => get_string('gradeaverage', 'scorm'),
                             GRADESUM => get_string('gradesum', 'scorm'));

define('HIGHESTATTEMPT', '0');
define('AVERAGEATTEMPT', '1');
define('FIRSTATTEMPT', '2');
define('LASTATTEMPT', '3');
$SCORM_WHAT_GRADE = array (HIGHESTATTEMPT => get_string('highestattempt', 'scorm'),
                           AVERAGEATTEMPT => get_string('averageattempt', 'scorm'),
                           FIRSTATTEMPT => get_string('firstattempt', 'scorm'),
                           LASTATTEMPT => get_string('lastattempt', 'scorm'));

$SCORM_POPUP_OPTIONS = array('resizable'=>1, 
                             'scrollbars'=>1, 
                             'directories'=>0, 
                             'location'=>0,
                             'menubar'=>0, 
                             'toolbar'=>0, 
                             'status'=>0);
$stdoptions = '';
foreach ($SCORM_POPUP_OPTIONS as $popupopt => $value) {
    $stdoptions .= $popupopt.'='.$value;
    if ($popupopt != 'status') {
        $stdoptions .= ',';
    }
}

if (!isset($CFG->scorm_maxattempts)) {
    set_config('scorm_maxattempts','6');
}

if (!isset($CFG->scorm_frameheight)) {
    set_config('scorm_frameheight','500');
}

if (!isset($CFG->scorm_framewidth)) {
    set_config('scorm_framewidth','100%');
}

if (!isset($CFG->scorm_advancedsettings)) {
    set_config('scorm_advancedsettings','0');
}

if (!isset($CFG->scorm_windowsettings)) {
    set_config('scorm_windowsettings','0');
}

//
// Repository configurations
//
$repositoryconfigfile = $CFG->dirroot.'/mod/resource/type/ims/repository_config.php';
$repositorybrowser = '/mod/resource/type/ims/finder.php';

/// Local Library of functions for module scorm

/**
* This function will permanently delete the given
* directory and all files and subdirectories.
*
* @param string $directory The directory to remove
* @return boolean
*/
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
         set_time_limit(5);
        }
        rmdir($directory);
        return true;
    }
    return false;
}

/**
* Given a diretory path returns the file list
*
* @param string $directory
* @return array
*/
function scorm_scandir($directory) {
    if (version_compare(phpversion(),'5.0.0','>=')) {
        return scandir($directory);
    } else {
        $files = array();
        if ($dh = opendir($directory)) {
            while (($file = readdir($dh)) !== false) {
               $files[] = $file;
            }
            closedir($dh);
        }
        return $files;
    }
}

/**
* Create a new temporary subdirectory with a random name in the given path
*
* @param string $strpath The scorm data directory
* @return string/boolean
*/
function scorm_datadir($strPath)
{
    global $CFG;

    if (is_dir($strPath)) {
        do {
            // Create a random string of 8 chars
            $randstring = NULL;
            $lchar = '';
            $len = 8;
            for ($i=0; $i<$len; $i++) {
                $char = chr(rand(48,122));
                while (!ereg('[a-zA-Z0-9]', $char)){
                    if ($char == $lchar) continue;
                        $char = chr(rand(48,90));
                    }
                    $randstring .= $char;
                    $lchar = $char;
            } 
            $datadir='/'.$randstring;
        } while (file_exists($strPath.$datadir));
        mkdir($strPath.$datadir, $CFG->directorypermissions);
        @chmod($strPath.$datadir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        return $strPath.$datadir;
    } else {
        return false;
    }
}

function scorm_array_search($item, $needle, $haystacks, $strict=false) {
    if (!empty($haystacks)) {
        foreach ($haystacks as $key => $element) {
            if ($strict) {
                if ($element->{$item} === $needle) {
                    return $key;
                }
            } else {
                if ($element->{$item} == $needle) {
                    return $key;
                }
            }
        }
    }
    return false;
}

function scorm_repeater($what, $times) {
    if ($times <= 0) {
        return null;
    }
    $return = '';
    for ($i=0; $i<$times;$i++) {
        $return .= $what;
    }
    return $return;
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

/**
* Given a package directory, this function will check if the package is valid
*
* @param string $packagedir The package directory
* @return mixed
*/
function scorm_validate($packagedir) {
    $validation = new stdClass();
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
        if (!isset($validation->result)) {
            $validation->result = 'nomanifest';
            $validation->pkgtype = 'SCORM';
        }
    }
    return $validation;
}

function scorm_insert_track($userid,$scormid,$scoid,$attempt,$element,$value) {
    $id = null;
    if ($track = get_record_select('scorm_scoes_track',"userid='$userid' AND scormid='$scormid' AND scoid='$scoid' AND attempt='$attempt' AND element='$element'")) {
        $track->value = $value;
        $track->timemodified = time();
        $id = update_record('scorm_scoes_track',$track);
    } else {
        $track->userid = $userid;
        $track->scormid = $scormid;
        $track->scoid = $scoid;
        $track->attempt = $attempt;
        $track->element = $element;
        $track->value = addslashes($value);
        $track->timemodified = time();
        $id = insert_record('scorm_scoes_track',$track);
    }
    return $id;
}

function scorm_get_tracks($scoid,$userid,$attempt='') {
/// Gets all tracks of specified sco and user
    global $CFG;

    if (empty($attempt)) {
        if ($scormid = get_field('scorm_scoes','scorm','id',$scoid)) {
            $attempt = scorm_get_last_attempt($scormid,$userid);
        } else {
            $attempt = 1;
        }
    }
    $attemptsql = ' AND attempt=' . $attempt;
    if ($tracks = get_records_select('scorm_scoes_track',"userid=$userid AND scoid=$scoid".$attemptsql,'element ASC')) {
        $usertrack->userid = $userid;
        $usertrack->scoid = $scoid; 
        // Defined in order to unify scorm1.2 and scorm2004
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
                case 'cmi.completion_status':
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

function scorm_grade_user_attempt($scorm, $userid, $attempt=1, $time=false) {
    $attemptscore = NULL; 
    $attemptscore->scoes = 0;
    $attemptscore->values = 0;
    $attemptscore->max = 0;
    $attemptscore->sum = 0;
    $attemptscore->lastmodify = 0;
    
    if (!$scoes = get_records('scorm_scoes','scorm',$scorm->id)) {
        return NULL;
    }

    $grademethod = $scorm->grademethod % 10;

    foreach ($scoes as $sco) { 
        if ($userdata=scorm_get_tracks($sco->id, $userid,$attempt)) {
            if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                $attemptscore->scoes++;
            }       
            if (!empty($userdata->score_raw)) {
                $attemptscore->values++;
                $attemptscore->sum += $userdata->score_raw;
                $attemptscore->max = ($userdata->score_raw > $attemptscore->max)?$userdata->score_raw:$attemptscore->max;
                if (isset($userdata->timemodified) && ($userdata->timemodified > $attemptscore->lastmodify)) {
                    $attemptscore->lastmodify = $userdata->timemodified;
                } else {
                    $attemptscore->lastmodify = 0;
                }
            }       
        }       
    }
    switch ($grademethod) {
        case GRADEHIGHEST:
            $score = $attemptscore->max;
        break;  
        case GRADEAVERAGE:
            if ($attemptscore->values > 0) {
                $score = $attemptscore->sum/$attemptscore->values;
            } else {
                $score = 0;
            }       
        break;  
        case GRADESUM:
            $score = $attemptscore->sum;
        break;  
        case GRADESCOES:
            $score = $attemptscore->scoes;
        break;  
    }

    if ($time) {
        $result = new stdClass();
        $result->score = $score;
        $result->time = $attemptscore->lastmodify;
    } else {
        $result = $score;
    }

    return $result;
}

function scorm_grade_user($scorm, $userid, $time=false) {

    $whatgrade = intval($scorm->grademethod / 10);

    switch ($whatgrade) {
        case FIRSTATTEMPT:
            return scorm_grade_user_attempt($scorm, $userid, 1, $time);
        break;    
        case LASTATTEMPT:
            return scorm_grade_user_attempt($scorm, $userid, scorm_get_last_attempt($scorm->id, $userid), $time);
        break;
        case HIGHESTATTEMPT:
            $lastattempt = scorm_get_last_attempt($scorm->id, $userid);
            $maxscore = 0;
            $attempttime = 0;
            for ($attempt = 1; $attempt <= $lastattempt; $attempt++) {
                $attemptscore = scorm_grade_user_attempt($scorm, $userid, $attempt, $time);
                if ($time) {
                    if ($attemptscore->score > $maxscore) {
                        $maxscore = $attemptscore->score;
                        $attempttime = $attemptscore->time;
                    }
                } else {
                    $maxscore = $attemptscore > $maxscore ? $attemptscore: $maxscore;
                }
            }
            if ($time) {
                $result = new stdClass();
                $result->score = $maxscore;
                $result->time = $attempttime;
                return $result;
            } else {
               return $maxscore;
            }
        break;
        case AVERAGEATTEMPT:
            $lastattempt = scorm_get_last_attempt($scorm->id, $userid);
            $sumscore = 0;
            for ($attempt = 1; $attempt <= $lastattempt; $attempt++) {
                $attemptscore = scorm_grade_user_attempt($scorm, $userid, $attempt, $time);
                if ($time) {
                    $sumscore += $attemptscore->score;
                } else {
                    $sumscore += $attemptscore;
                }
            }

            if ($lastattempt > 0) {
                $score = $sumscore / $lastattempt;
            } else {
                $score = 0;
            }

            if ($time) {
                $result = new stdClass();
                $result->score = $score;
                $result->time = $attemptscore->time;
                return $result;
            } else {
               return $score;
            }
        break;
    }
}

function scorm_count_launchable($scormid,$organization) {
    return count_records_select('scorm_scoes',"scorm=$scormid AND organization='$organization' AND launch<>''");
}

function scorm_get_last_attempt($scormid, $userid) {
/// Find the last attempt number for the given user id and scorm id
    if ($lastattempt = get_record('scorm_scoes_track', 'userid', $userid, 'scormid', $scormid, '', '', 'max(attempt) as a')) {
        if (empty($lastattempt->a)) {
            return '1';
        } else {
            return $lastattempt->a;
        }
    }
}

function scorm_course_format_display($user,$course) {
    global $CFG;

    $strupdate = get_string('update');
    $strmodule = get_string('modulename','scorm');

    echo '<div class="mod-scorm">';
    if ($scorms = get_all_instances_in_course('scorm', $course)) {
        // The module SCORM activity with the least id is the course  
        $scorm = current($scorms);
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        $colspan = '';
        $headertext = '<table width="100%"><tr><td class="title">'.get_string('name').': <b>'.format_string($scorm->name).'</b>';
        if (isteacher($course->id, $user->id, true)) {
            if (isediting($course->id)) {
                // Display update icon
                $path = $CFG->wwwroot.'/course';
                $headertext .= '<span class="commands">'.
                        '<a title="'.$strupdate.'" href="'.$path.'/mod.php?update='.$cm->id.'&amp;sesskey='.sesskey().'">'.
                        '<img src="'.$CFG->pixpath.'/t/edit.gif" hspace="2" height="11" width="11" border="0" alt="'.$strupdate.'" /></a></span>';
            }
            $headertext .= '</td>';
            // Display report link
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                $headertext .= '<td class="reportlink">'.
                              '<a target="'.$CFG->framename.'" href="'.$CFG->wwwroot.'/mod/scorm/report.php?id='.$cm->id.'">'.
                               get_string('viewallreports','scorm',$trackedusers->c).'</a>';
            } else {
                $headertext .= '<td class="reportlink">'.get_string('noreports','scorm');
            }
            $colspan = ' colspan="2"';
        } 
        $headertext .= '</td></tr><tr><td'.$colspan.'>'.format_text(get_string('summary').':<br />'.$scorm->summary).'</td></tr></table>';
        print_simple_box($headertext,'','100%');
        scorm_view_display($user, $scorm, 'view.php?id='.$course->id, $cm, '100%');
    } else {
        if (isteacheredit($course->id, $user->id)) {
            // Create a new activity
            redirect($CFG->wwwroot.'/course/mod.php?id='.$course->id.'&amp;section=0&sesskey='.sesskey().'&amp;add=scorm');
        } else {
            notify('Could not find a scorm course here');
        }
    }
    echo '</div>';
}

function scorm_view_display ($user, $scorm, $action, $cm, $boxwidth='') {
    global $CFG;

    $organization = optional_param('organization', '', PARAM_INT);

    print_simple_box_start('center',$boxwidth);
?>
        <div class="structurehead"><?php print_string('coursestruct','scorm') ?></div>
<?php
    if (empty($organization)) {
        $organization = $scorm->launch;
    }
    if ($orgs = get_records_select_menu('scorm_scoes',"scorm='$scorm->id' AND organization='' AND launch=''",'id','id,title')) {
        if (count($orgs) > 1) {
 ?>
            <div class='center'>
                <?php print_string('organizations','scorm') ?>
                <form name='changeorg' method='post' action='<?php echo $action ?>'>
                    <?php choose_from_menu($orgs, 'organization', "$organization", '','submit()') ?>
                </form>
            </div>
<?php
        }
    }
    $orgidentifier = '';
    if ($org = get_record('scorm_scoes','id',$organization)) {
        if (($org->organization == '') && ($org->launch == '')) {
            $orgidentifier = $org->identifier;
        } else {
            $orgidentifier = $org->organization;
        }
    }
    $result = scorm_get_toc($user,$scorm,'structlist',$orgidentifier);
    $incomplete = $result->incomplete;
    echo $result->toc;
    print_simple_box_end();
?>
            <div class="center">
                <form name="theform" method="post" action="<?php echo $CFG->wwwroot ?>/mod/scorm/player.php?id=<?php echo $cm->id ?>"<?php echo $scorm->popup == 1?' target="newwin"':'' ?>>
              <?php
                  if ($scorm->hidebrowse == 0) {
                      print_string("mode","scorm");
                      echo ': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse','scorm').'</label>'."\n";
                      if ($incomplete === true) {
                          echo '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal','scorm')."</label>\n";
                      } else {
                          echo '<input type="radio" id="r" name="mode" value="review" checked="checked" /><label for="r">'.get_string('review','scorm')."</label>\n";
                      }
                  } else {
                      if ($incomplete === true) {
                          echo '<input type="hidden" name="mode" value="normal" />'."\n";
                      } else {
                          echo '<input type="hidden" name="mode" value="review" />'."\n";
                      }
                  }
                  if (($incomplete === false) && (($result->attemptleft > 0)||($scorm->maxattempt == 0))) {
?>
                  <br />
                  <input type="checkbox" id="a" name="newattempt" />
                  <label for="a"><?php print_string('newattempt','scorm') ?></label>
<?php
                  }
              ?>
              <br />
              <input type="hidden" name="scoid" />
              <input type="hidden" name="currentorg" value="<?php echo $orgidentifier ?>" />
              <input type="submit" value="<?php print_string('entercourse','scorm') ?>" />
              </form>
          </div>
<?php
}

/// Library of functions and constants for parsing packages

function scorm_parse($scorm) {
    global $CFG,$repositoryconfigfile;

    // Parse scorm manifest
    if ($scorm->pkgtype == 'AICC') {
        $scorm->launch = scorm_parse_aicc($scorm->dir.'/'.$scorm->id,$scorm->id);
    } else {
        $reference = $scorm->reference;
        if ($scorm->reference[0] == '#') {
            require_once($repositoryconfigfile);
            $reference = $CFG->repository.substr($scorm->reference,1).'/imsmanifest.xml';
        } else if (substr($reference,0,7) != 'http://') {
            $reference = $CFG->dataroot.'/'.$scorm->course.'/'.$scorm->reference;
        }

        if (basename($reference) != 'imsmanifest.xml') {
            $scorm->launch = scorm_parse_scorm($scorm->dir.'/'.$scorm->id,$scorm->id);
        } else {
            $scorm->launch = scorm_parse_scorm(dirname($reference),$scorm->id);
        }
    }
    return $scorm->launch;
}

/**
* Take the header row of an AICC definition file
* and returns sequence of columns and a pointer to
* the sco identifier column.
*
* @param string $row AICC header row
* @param string $mastername AICC sco identifier column
* @return mixed
*/
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

/**
* Given a colums array return a string containing the regular
* expression to match the columns in a text row.
*
* @param array $column The header columns
* @param string $remodule The regular expression module for a single column
* @return string
*/
function scorm_forge_cols_regexp($columns,$remodule='(".*")?,') {
    $regexp = '/^';
    foreach ($columns as $column) {
        $regexp .= $remodule;
    }
    if (substr($regexp,-1) == ',') {
        $regexp = substr($regexp,0,-1);
    }
    $regexp .= '/';
    return $regexp;
}

function scorm_parse_aicc($pkgdir,$scormid) {
    $version = 'AICC';
    $ids = array();
    $courses = array();
    $extaiccfiles = array('crs','des','au','cst','ort','pre','cmp');
    if ($handle = opendir($pkgdir)) {
        while (($file = readdir($handle)) !== false) {
            if ($file[0] != '.') {
                $ext = substr($file,strrpos($file,'.'));
                $extension = strtolower(substr($ext,1));
                if (in_array($extension,$extaiccfiles)) {
                    $id = strtolower(basename($file,$ext));
                    $ids[$id]->$extension = $file;
                }
            }
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
                    for ($j=0;$j<count($matches)-1;$j++) {
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
                    for ($j=0;$j<count($matches)-1;$j++) {
                        $column = $columns->columns[$j];
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]),1,-1)]->$column = substr(trim($matches[$j+1]),1,-1);
                    }
                }
            }
        }
        if (isset($id->cst)) {
            $rows = file($pkgdir.'/'.$id->cst);
            $columns = scorm_get_aicc_columns($rows[0],'block');
            $regexp = scorm_forge_cols_regexp($columns->columns,'("[\w]+")?,?');
            for ($i=1;$i<count($rows);$i++) {
                if (preg_match($regexp,$rows[$i],$matches)) {
                    for ($j=0;$j<count($matches)-1;$j++) {
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

    $oldscoes = get_records('scorm_scoes','scorm',$scormid);
    
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
            if (get_record('scorm_scoes','scorm',$scormid,'identifier',$sco->identifier)) {
                $id = update_record('scorm_scoes',$sco);
                unset($oldscoes[$id]);
            } else {
                $id = insert_record('scorm_scoes',$sco);
            }

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
                    if ($oldscoid = scorm_array_search('identifier',$sco->identifier,$oldscoes)) {
                        $sco->id = $oldscoid;
                        $id = update_record('scorm_scoes',$sco);
                        unset($oldscoes[$oldscoid]);
                    } else {
                        $id = insert_record('scorm_scoes',$sco);
                    }
                    if ($launch==0) {
                        $launch = $id;
                    }
                }
            }
        }
    }
    if (!empty($oldscoes)) {
        foreach($oldscoes as $oldsco) {
            delete_records('scorm_scoes','id',$oldsco->id);
            delete_records('scorm_scoes_track','scoid',$oldsco->id);
        }
    }
    set_field('scorm','version','AICC','id',$scormid);
    return $launch;
}

function scorm_get_resources($blocks) {
    $resources = array();
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
                                    if (isset($metadata['tagData']) && (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches))) {
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
                        $base = '';
                        if (isset($resources[$idref]['XML:BASE'])) {
                            $base = $resources[$idref]['XML:BASE'];
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->launch = addslashes($base.$resources[$idref]['HREF']);
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
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = addslashes($block['tagData']);
                break;
                case 'ADLCP:PREREQUISITES':
                    if ($block['attrs']['TYPE'] == 'aicc_script') {
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = addslashes($block['tagData']);
                    }
                break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = addslashes($block['tagData']);
                break;
                case 'ADLCP:TIMELIMITACTION':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = addslashes($block['tagData']);
                break;
                case 'ADLCP:DATAFROMLMS':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = addslashes($block['tagData']);
                break;
                case 'ADLCP:MASTERYSCORE':
                    $parent = array_pop($parents);
                    array_push($parents, $parent);
                    if (!isset($block['tagData'])) {
                        $block['tagData'] = '';
                    }
                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = addslashes($block['tagData']);
                break;
            }
        }
    }
    return $scoes;
}

function scorm_parse_scorm($pkgdir,$scormid) {
    global $CFG;
    
    $launch = 0;
    $manifestfile = $pkgdir.'/imsmanifest.xml';

    if (is_file($manifestfile)) {
    
        $xmltext = file_get_contents($manifestfile);
   
        $pattern = '/&(?!\w{2,6};)/';
        $replacement = '&amp;';
    
        $xmltext = preg_replace($pattern, $replacement, $xmltext);

        $objXML = new xml2Array();
        $manifests = $objXML->parse($xmltext);
        //   print_r($manifests); 
        $scoes = new stdClass();
        $scoes->version = '';
        $scoes = scorm_get_manifest($manifests,$scoes);

        if (count($scoes->elements) > 0) {
            $olditems = get_records('scorm_scoes','scorm',$scormid);
            foreach ($scoes->elements as $manifest => $organizations) {
                foreach ($organizations as $organization => $items) {
                    foreach ($items as $identifier => $item) {
                        $item->scorm = $scormid;
                        $item->manifest = $manifest;
                        $item->organization = $organization;
                        if ($olditemid = scorm_array_search('identifier',$item->identifier,$olditems)) {
                            $item->id = $olditemid;
                            $id = update_record('scorm_scoes',$item);
                            unset($olditems[$olditemid]);
                        } else {
                            $id = insert_record('scorm_scoes',$item);
                        }
                
                        if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier))) {
                            $launch = $id;
                        }
                    }
                }
            }
            if (!empty($olditems)) {
                foreach($olditems as $olditem) {
                   delete_records('scorm_scoes','id',$olditem->id);
                   delete_records('scorm_scoes_track','scoid',$olditem->id);
                }
            }
            set_field('scorm','version',$scoes->version,'id',$scormid);
        }
    } 
    
    return $launch;
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

function scorm_eval_prerequisites($prerequisites,$usertracks) {
    $element = '';
    $stack = array();
    $statuses = array(
                'passed' => 'passed',
                'completed' => 'completed',
                'failed' => 'failed',
                'incomplete' => 'incomplete',
                'browsed' => 'browsed',
                'not attempted' => 'notattempted',
                'p' => 'passed',
                'c' => 'completed',
                'f' => 'failed',
                'i' => 'incomplete',
                'b' => 'browsed',
                'n' => 'notattempted'
                );
    $i=0;  
    while ($i<strlen($prerequisites)) {
        $symbol = $prerequisites[$i];
        switch ($symbol) {
            case '&':
            case '|':
                $symbol .= $symbol;
            case '~':
            case '(':
            case ')':
            case '*':
                $element = trim($element);
                
                if (!empty($element)) {
                    $element = trim($element);
                    if (isset($usertracks[$element])) {
                        $element = '((\''.$usertracks[$element]->status.'\' == \'completed\') || '.
                                  '(\''.$usertracks[$element]->status.'\' == \'passed\'))'; 
                    } else if (($operator = strpos($element,'=')) !== false) {
                        $item = trim(substr($element,0,$operator));
                        if (!isset($usertracks[$item])) {
                            return false;
                        }
                        
                        $value = trim(trim(substr($element,$operator+1)),'"');
                        if (isset($statuses[$value])) {
                            $status = $statuses[$value];
                        } else {
                            return false;
                        }
                                              
                        $element = '(\''.$usertracks[$item]->status.'\' == \''.$status.'\')';
                    } else if (($operator = strpos($element,'<>')) !== false) {
                        $item = trim(substr($element,0,$operator));
                        if (!isset($usertracks[$item])) {
                            return false;
                        }
                        
                        $value = trim(trim(substr($element,$operator+2)),'"');
                        if (isset($statuses[$value])) {
                            $status = $statuses[$value];
                        } else {
                            return false;
                        }
                        
                        $element = '(\''.$usertracks[$item]->status.'\' != \''.$status.'\')';
                    } else if (is_numeric($element)) {
                        if ($symbol == '*') {
                            $symbol = '';
                            $open = strpos($prerequisites,'{',$i);
                            $opened = 1;
                            $closed = 0;
                            for ($close=$open+1; (($opened > $closed) && ($close<strlen($prerequisites))); $close++) { 
                                 if ($prerequisites[$close] == '}') {
                                     $closed++;
                                 } else if ($prerequisites[$close] == '{') {
                                     $opened++;
                                 }
                            } 
                            $i = $close;
                            
                            $setelements = explode(',', substr($prerequisites, $open+1, $close-($open+1)-1));
                            $settrue = 0;
                            foreach ($setelements as $setelement) {
                                if (scorm_eval_prerequisites($setelement,$usertracks)) {
                                    $settrue++;
                                }
                            }
                            
                            if ($settrue >= $element) {
                                $element = 'true'; 
                            } else {
                                $element = 'false';
                            }
                        }
                    } else {
                        return false;
                    }
                    
                    array_push($stack,$element);
                    $element = '';
                }
                if ($symbol == '~') {
                    $symbol = '!';
                }
                if (!empty($symbol)) {
                    array_push($stack,$symbol);
                }
            break;
            default:
                $element .= $symbol;
            break;
        }
        $i++;
    }
    if (!empty($element)) {
        $element = trim($element);
        if (isset($usertracks[$element])) {
            $element = '((\''.$usertracks[$element]->status.'\' == \'completed\') || '.
                       '(\''.$usertracks[$element]->status.'\' == \'passed\'))'; 
        } else if (($operator = strpos($element,'=')) !== false) {
            $item = trim(substr($element,0,$operator));
            if (!isset($usertracks[$item])) {
                return false;
            }
            
            $value = trim(trim(substr($element,$operator+1)),'"');
            if (isset($statuses[$value])) {
                $status = $statuses[$value];
            } else {
                return false;
            }
            
            $element = '(\''.$usertracks[$item]->status.'\' == \''.$status.'\')';
        } else if (($operator = strpos($element,'<>')) !== false) {
            $item = trim(substr($element,0,$operator));
            if (!isset($usertracks[$item])) {
                return false;
            }
            
            $value = trim(trim(substr($element,$operator+1)),'"');
            if (isset($statuses[$value])) {
                $status = $statuses[$value];
            } else {
                return false;
            }
            
            $element = '(\''.$usertracks[$item]->status.'\' != \''.trim($status).'\')';
        } else {
            return false;
        }
        
        array_push($stack,$element);
    }
    return eval('return '.implode($stack).';');
}

function scorm_get_toc($user,$scorm,$liststyle,$currentorg='',$scoid='',$mode='normal',$attempt='',$play=false) {
    global $CFG;

    $strexpand = get_string('expcoll','scorm');
    $modestr = '';
    if ($mode == 'browse') {
        $modestr = '&amp;mode='.$mode;
    } 
    $scormpixdir = $CFG->modpixpath.'/scorm/pix';
    
    $result = new stdClass();
    $result->toc = "<ul id='0' class='$liststyle'>\n";
    $tocmenus = array();
    $result->prerequisites = true;
    $incomplete = false;
    
    //
    // Get the current organization infos
    //
    $organizationsql = '';
    if (!empty($currentorg)) {
        if (($organizationtitle = get_field('scorm_scoes','title','scorm',$scorm->id,'identifier',$currentorg)) != '') {
            $result->toc .= "\t<li>$organizationtitle</li>\n";
            $tocmenus[] = $organizationtitle;
        }
        $organizationsql = "AND organization='$currentorg'";
    }
    //
    // If not specified retrieve the last attempt number
    //
    if (empty($attempt)) {
        $attempt = scorm_get_last_attempt($scorm->id, $user->id);
    }
    $result->attemptleft = $scorm->maxattempt - $attempt;
    if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' $organizationsql order by id ASC")){
        //
        // Retrieve user tracking data for each learning object
        // 
        $usertracks = array();
        foreach ($scoes as $sco) {
            if (!empty($sco->launch)) {
                if ($usertrack=scorm_get_tracks($sco->id,$user->id,$attempt)) {
                    if ($usertrack->status == '') {
                        $usertrack->status = 'notattempted';
                    }
                    $usertracks[$sco->identifier] = $usertrack;
                }
            }
        }

        $level=0;
        $sublist=1;
        $previd = 0;
        $nextid = 0;
        $findnext = false;
        $parents[$level]='/';
        
        foreach ($scoes as $sco) {
            if ($parents[$level]!=$sco->parent) {
                if ($newlevel = array_search($sco->parent,$parents)) {
                    for ($i=0; $i<($level-$newlevel); $i++) {
                        $result->toc .= "\t\t</ul></li>\n";
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
                        $style = '';
                        if (isset($_COOKIE['hide:SCORMitem'.$sco->id])) {
                            $style = ' style="display: none;"';
                        }
                        $result->toc .= "\t\t<li><ul id='$sublist' class='$liststyle'$style>\n";
                        $level++;
                    } else {
                        $result->toc .= $closelist;
                        $level = $i;
                    }
                    $parents[$level]=$sco->parent;
                }
            }
            $result->toc .= "\t\t<li>";
            $nextsco = next($scoes);
            if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
                $sublist++;
                $icon = 'minus';
                if (isset($_COOKIE['hide:SCORMitem'.$nextsco->id])) {
                    $icon = 'plus';
                }
                $result->toc .= '<a href="javascript:expandCollide(\'img'.$sublist.'\','.$sublist.','.$nextsco->id.');"><img id="img'.$sublist.'" src="'.$scormpixdir.'/'.$icon.'.gif" alt="'.$strexpand.'" title="'.$strexpand.'"/></a>';
            } else {
                $result->toc .= '<img src="'.$scormpixdir.'/spacer.gif" />';
            }
            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }
            if (!empty($sco->launch)) {
                $startbold = '';
                $endbold = '';
                $score = '';
                if (empty($scoid) && ($mode != 'normal')) {
                    $scoid = $sco->id;
                }
                if (isset($usertracks[$sco->identifier])) {
                    $usertrack = $usertracks[$sco->identifier];
                    $strstatus = get_string($usertrack->status,'scorm');
                    $result->toc .= '<img src="'.$scormpixdir.'/'.$usertrack->status.'.gif" alt="'.$strstatus.'" title="'.$strstatus.'" />';
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
                        $result->toc .= '<img src="'.$scormpixdir.'/notattempted.gif" alt="'.get_string('notattempted','scorm').'" title="'.get_string('notattempted','scorm').'" />';
                        $incomplete = true;
                    } else {
                        $result->toc .= '<img src="'.$scormpixdir.'/asset.gif" alt="'.get_string('asset','scorm').'" title="'.get_string('asset','scorm').'" />';
                    }
                }
                if ($sco->id == $scoid) {
                    $startbold = '<b>';
                    $endbold = '</b>';
                    $findnext = true;
                    $shownext = $sco->next;
                    $showprev = $sco->previous;
                }
                
                if (($nextid == 0) && (scorm_count_launchable($scorm->id,$currentorg) > 1) && ($nextsco!==false) && (!$findnext)) {
                    if (!empty($sco->launch)) {
                        $previd = $sco->id;
                    }
                }
                if (empty($sco->prerequisites) || scorm_eval_prerequisites($sco->prerequisites,$usertracks)) {
                    if ($sco->id == $scoid) {
                        $result->prerequisites = true;
                    }
                    $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid='.$sco->id;
                    $result->toc .= '&nbsp'.$startbold.'<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score.$endbold."</li>\n";
                    $tocmenus[$sco->id] = scorm_repeater('&minus;',$level) . '&gt;' . format_string($sco->title);
                } else {
                    if ($sco->id == $scoid) {
                        $result->prerequisites = false;
                    }
                    $result->toc .= '&nbsp;'.$sco->title."</li>\n";
                }
            } else {
                $result->toc .= '&nbsp;'.$sco->title."</li>\n";
            }
            if (($nextsco !== false) && ($nextid == 0) && ($findnext)) {
                if (!empty($nextsco->launch)) {
                    $nextid = $nextsco->id;
                }
            }
        }
        for ($i=0;$i<$level;$i++) {
            $result->toc .= "\t\t</ul></li>\n";
        }
        
        if ($play) {
            $sco = get_record('scorm_scoes','id',$scoid);
            $sco->previd = $previd;
            $sco->nextid = $nextid;
            $result->sco = $sco;
            $result->incomplete = $incomplete;
        } else {
            $result->incomplete = $incomplete;
        }
    }
    $result->toc .= "\t</ul>\n";
    if ($scorm->hidetoc == 0) {
        $result->toc .= '
          <script language="javascript" type="text/javascript">
          <!--
              function expandCollide(which,list,item) {
                  var nn=document.ids?true:false
                  var w3c=document.getElementById?true:false
                  var beg=nn?"document.ids.":w3c?"document.getElementById(":"document.all.";
                  var mid=w3c?").style":".style";
                  which = which.substring(0,(which.length));

                  if (eval(beg+list+mid+".display") != "none") {
                      document.getElementById(which).src = "'.$scormpixdir.'/plus.gif";
                      eval(beg+list+mid+".display=\'none\';");
                      new cookie("hide:SCORMitem" + item, 1, 356, "/").set();
                  } else {
                      document.getElementById(which).src = "'.$scormpixdir.'/minus.gif";

                      eval(beg+list+mid+".display=\'block\';");
                      new cookie("hide:SCORMitem" + item, 1, -1, "/").set();
                  }
              }
          -->
          </script>'."\n";
    }
    
    $url = $CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;currentorg='.$currentorg.$modestr.'&amp;scoid=';
    $result->tocmenu = popup_form($url,$tocmenus, "tocmenu", $sco->id, '', '', '', true);

    return $result;
}

/**
* Convert a utf-8 string to html entities
*
* @param string $str The UTF-8 string
* @return string
*/
function scorm_utf8_to_entities($str) {
    global $CFG;

    $entities = '';
    $values = array();
    $lookingfor = 1;

    if (empty($CFG->unicodedb)) {  // If Unicode DB support enable does not convert string
        $textlib = textlib_get_instance();
        for ($i = 0; $i < $textlib->strlen($str,'utf-8'); $i++) {
            $thisvalue = ord($str[$i]);
            if ($thisvalue < 128) {
                $entities .= $str[$i]; // Leave ASCII chars unchanged 
            } else {
                if (count($values) == 0) {
                    $lookingfor = ($thisvalue < 224) ? 2 : 3;
                }
                $values[] = $thisvalue;
                if (count($values) == $lookingfor) {
                    $number = ($lookingfor == 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64):
                        (($values[0] % 32) * 64) + ($values[1] % 64);
                    $entities .= '&#' . $number . ';';
                    $values = array();
                    $lookingfor = 1;
                }
            }
        }
        return $entities;
    } else {
        return $str;
    }
}

/* Usage
 Grab some XML data, either from a file, URL, etc. however you want. Assume storage in $strYourXML;

 $objXML = new xml2Array();
 $arrOutput = $objXML->parse($strYourXML);
 print_r($arrOutput); //print it out, or do whatever!
  
*/
class xml2Array {
   
   var $arrOutput = array();
   var $resParser;
   var $strXmlData;
   
   /**
   * Parse an XML text string and create an array tree that rapresent the XML structure
   *
   * @param string $strInputXML The XML string
   * @return array
   */
   function parse($strInputXML) {
           /*if (($start = strpos($strInputXML,'encoding=')) !== false) {
               $endchr = substr($strInputXML,$start+9,1);
               if (($end = strpos($strInputXML,$endchr,$start+10)) !== false) {
                   $charset = strtolower(substr($strInputXML,$start+10,$end-$start-10));
                   if ($charset != 'utf-8') {
                       $strInputXML = str_ireplace('encoding='.$endchr.$charset.$endchr,'encoding='.$endchr.'UTF-8'.$endchr,$strInputXML);
                       $textlib = textlib_get_instance();
                   }
               }
           }*/
           $this->resParser = xml_parser_create ('UTF-8');
           xml_set_object($this->resParser,$this);
           xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
           
           xml_set_character_data_handler($this->resParser, "tagData");
       
           $this->strXmlData = xml_parse($this->resParser,$strInputXML );
           if(!$this->strXmlData) {
               die(sprintf("XML error: %s at line %d",
                           xml_error_string(xml_get_error_code($this->resParser)),
                           xml_get_current_line_number($this->resParser)));
           }
                           
           xml_parser_free($this->resParser);
           
           return $this->arrOutput;
   }
   
   function tagOpen($parser, $name, $attrs) {
       $tag=array("name"=>$name,"attrs"=>$attrs); 
       array_push($this->arrOutput,$tag);
   }
   
   function tagData($parser, $tagData) {
       if(trim($tagData)) {
           if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= scorm_utf8_to_entities($tagData);
           } else {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] = scorm_utf8_to_entities($tagData);
           }
       }
   }
   
   function tagClosed($parser, $name) {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
   }
}
?>
