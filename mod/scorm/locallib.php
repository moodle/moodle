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

function scorm_count_launchable($scormid,$organization='') {
    $strorganization = '';
    if (!empty($organization)) {
        $strorganization = " AND organization='$organization'";
    }
    return count_records_select('scorm_scoes',"scorm=$scormid$strorganization AND launch<>''");
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
    $context = get_context_instance(CONTEXT_COURSE,$course->id);

    echo '<div class="mod-scorm">';
    if ($scorms = get_all_instances_in_course('scorm', $course)) {
        // The module SCORM activity with the least id is the course  
        $scorm = current($scorms);
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        $colspan = '';
        $headertext = '<table width="100%"><tr><td class="title">'.get_string('name').': <b>'.format_string($scorm->name).'</b>';
        if (has_capability('moodle/course:manageactivities', $context)) {
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
        if (has_capability('moodle/course:update', $context)) {
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
        <div class="structurehead"><?php print_string('contents','scorm') ?></div>
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
    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
    if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
        $scorm->version = 'scorm_12';
    }
    require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');

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

function scorm_simple_play($scorm,$user) {
    $result = false;
    $scoes = get_records_select('scorm_scoes','scorm='.$scorm->id.' AND launch<>""');
    if (count($scoes) == 1) {
        if ($scorm->skipview >= 1) {
            $sco = current($scoes);
            if (scorm_get_tracks($sco->id,$user->id) === false) {
                header('Location: player.php?a='.$scorm->id.'&scoid='.$sco->id);
                $result = true;
            } else if ($scorm->skipview == 2) {
                header('Location: player.php?a='.$scorm->id.'&scoid='.$sco->id);
                $result = true;
            }
        }
    }
    return $result;
}

function scorm_parse($scorm) {
    global $CFG,$repositoryconfigfile;

    // Parse scorm manifest
    if ($scorm->pkgtype == 'AICC') {
        require_once('datamodels/aicclib.php');
        $scorm->launch = scorm_parse_aicc($scorm->dir.'/'.$scorm->id,$scorm->id);
    } else {
        require_once('datamodels/scormlib.php');
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

?>
