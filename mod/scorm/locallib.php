<?php  // $Id$

/// Constants and settings for module scorm
define('UPDATE_NEVER', '0');
define('UPDATE_ONCHANGE', '1');
define('UPDATE_EVERYDAY', '2');
define('UPDATE_EVERYTIME', '3');

define('SCO_ALL', 0);
define('SCO_DATA', 1);
define('SCO_ONLY', 2);

define('GRADESCOES', '0');
define('GRADEHIGHEST', '1');
define('GRADEAVERAGE', '2');
define('GRADESUM', '3');

define('HIGHESTATTEMPT', '0');
define('AVERAGEATTEMPT', '1');
define('FIRSTATTEMPT', '2');
define('LASTATTEMPT', '3');

/**
 * Returns an array of the popup options for SCORM and each options default value
 *
 * @return array an array of popup options as the key and their defaults as the value
 */
function scorm_get_popup_options_array(){
    global $CFG;
    return array('resizable'=> isset($CFG->scorm_resizable) ? $CFG->scorm_resizable : 0,
                 'scrollbars'=> isset($CFG->scorm_scrollbars) ? $CFG->scorm_scrollbars : 0,
                 'directories'=> isset($CFG->scorm_directories) ? $CFG->scorm_directories : 0,
                 'location'=> isset($CFG->scorm_location) ? $CFG->scorm_location : 0,
                 'menubar'=> isset($CFG->scorm_menubar) ? $CFG->scorm_menubar : 0,
                 'toolbar'=> isset($CFG->scorm_toolbar) ? $CFG->scorm_toolbar : 0,
                 'status'=> isset($CFG->scorm_status) ? $CFG->scorm_status : 0);
}

/// Local Library of functions for module scorm
/**
 * Returns an array of the array of what grade options
 *
 * @return array an array of what grade options
 */
function scorm_get_grade_method_array(){
    return array (GRADESCOES => get_string('gradescoes', 'scorm'),
                  GRADEHIGHEST => get_string('gradehighest', 'scorm'),
                  GRADEAVERAGE => get_string('gradeaverage', 'scorm'),
                  GRADESUM => get_string('gradesum', 'scorm'));
}

/**
 * Returns an array of the array of what grade options
 *
 * @return array an array of what grade options
 */
function scorm_get_what_grade_array(){
    return array (HIGHESTATTEMPT => get_string('highestattempt', 'scorm'),
                  AVERAGEATTEMPT => get_string('averageattempt', 'scorm'),
                  FIRSTATTEMPT => get_string('firstattempt', 'scorm'),
                  LASTATTEMPT => get_string('lastattempt', 'scorm'));
}

/**
 * Returns an array of the array of skip view options
 *
 * @return array an array of skip view options
 */
function scorm_get_skip_view_array(){
   return array(0 => get_string('never'),
                 1 => get_string('firstaccess','scorm'),
                 2 => get_string('always'));
}

/**
 * Returns an array of the array of hide table of contents options
 *
 * @return array an array of hide table of contents options
 */
function scorm_get_hidetoc_array(){
     return array(0 =>get_string('sided','scorm'),
                  1 => get_string('hidden','scorm'),
                  2 => get_string('popupmenu','scorm'));
}

/**
 * Returns an array of the array of update frequency options
 *
 * @return array an array of update frequency options
 */
function scorm_get_updatefreq_array(){
    return array(0 => get_string('never'),
                 1 => get_string('everyday','scorm'),
                 2 => get_string('everytime','scorm'));
}

/**
 * Returns an array of the array of popup display options
 *
 * @return array an array of popup display options
 */
function scorm_get_popup_display_array(){
    return array(0 => get_string('iframe', 'scorm'),
                 1 => get_string('popup', 'scorm'));
}

/**
 * Returns an array of the array of attempt options
 *
 * @return array an array of attempt options
 */
function scorm_get_attempts_array(){
    $attempts = array(0 => get_string('nolimit','scorm'),
                      1 => get_string('attempt1','scorm'));

    for ($i=2; $i<=6; $i++) {
        $attempts[$i] = get_string('attemptsx','scorm', $i);
    }

    return $attempts;
}

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
        set_time_limit(0);
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
function scorm_tempdir($strPath)
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
* Returns an object containing all datas relative to the given sco ID
*
* @param integer $id The sco ID
* @return mixed (false if sco id does not exists)
*/

function scorm_get_sco($id,$what=SCO_ALL) {
    if ($sco = get_record('scorm_scoes','id',$id)) {
        $sco = ($what == SCO_DATA) ? new stdClass() : $sco;
        if (($what != SCO_ONLY) && ($scodatas = get_records('scorm_scoes_data','scoid',$id))) {
            foreach ($scodatas as $scodata) {
                $sco->{$scodata->name} = $scodata->value;
            }
        } else if (($what != SCO_ONLY) && (!($scodatas = get_records('scorm_scoes_data','scoid',$id)))) {
            $sco->parameters = '';
        }
        return $sco;
    } else {
        return false;
    }
}

/**
* Returns an object (array) containing all the scoes data related to the given sco ID
*
* @param integer $id The sco ID
* @param integer $organisation an organisation ID - defaults to false if not required
* @return mixed (false if there are no scoes or an array)
*/

function scorm_get_scoes($id,$organisation=false) {
    $organizationsql = '';
    if (!empty($organisation)) {
        $organizationsql = "AND organization='$organisation'";
    }
    if ($scoes = get_records_select('scorm_scoes',"scorm='$id' $organizationsql order by id ASC")) {
        // drop keys so that it is a simple array as expected
        $scoes = array_values($scoes);
        foreach ($scoes as $sco) {
            if ($scodatas = get_records('scorm_scoes_data','scoid',$sco->id)) {
                foreach ($scodatas as $scodata) {
                    $sco->{$scodata->name} = stripslashes_safe($scodata->value);
                }
            }
        }
        return $scoes;
    } else {
        return false;
    }
}

function scorm_insert_track($userid,$scormid,$scoid,$attempt,$element,$value) {
    $id = null;
    if ($track = get_record_select('scorm_scoes_track',"userid='$userid' AND scormid='$scormid' AND scoid='$scoid' AND attempt='$attempt' AND element='$element'")) {
        $track->value = addslashes_js($value);
        $track->timemodified = time();
        $id = update_record('scorm_scoes_track',$track);
    } else {
        $track->userid = $userid;
        $track->scormid = $scormid;
        $track->scoid = $scoid;
        $track->attempt = $attempt;
        $track->element = $element;
        $track->value = addslashes_js($value);
        $track->timemodified = time();
        $id = insert_record('scorm_scoes_track',$track);
    }

    if (strstr($element, '.score.raw') ||
        (($element == 'cmi.core.lesson_status' || $element == 'cmi.completion_status') && ($track->value == 'completed' || $track->value == 'passed'))) {
        $scorm = get_record('scorm', 'id', $scormid);
        $grademethod = $scorm->grademethod % 10;
        include_once('lib.php');
        scorm_update_grades($scorm, $userid);
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
            $track->value = stripslashes_safe($track->value);
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
                    $usertrack->score_raw = sprintf('%0d', $track->value);
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
        if (is_array($usertrack)) {
            ksort($usertrack);
        }
        return $usertrack;
    } else {
        return false;
    }
}

/* Find the start and finsh time for a a given SCO attempt
 *
 * @param int $scormid SCORM Id
 * @param int $scoid SCO Id
 * @param int $userid User Id
 * @param int $attemt Attempt Id
 *
 * @return object start and finsh time EPOC secods
 *
 */
function scorm_get_sco_runtime($scormid, $scoid, $userid, $attempt=1) {

    $timedata = new object();
    $sql = !empty($scoid) ? "userid=$userid AND scormid=$scormid AND scoid=$scoid AND attempt=$attempt" : "userid=$userid AND scormid=$scormid AND attempt=$attempt";
    $tracks = get_records_select('scorm_scoes_track',"$sql ORDER BY timemodified ASC");
    if ($tracks) {
        $tracks = array_values($tracks);
    }

    if ($tracks) {
        $timedata->start = $tracks[0]->timemodified;
    }
    else {
        $timedata->start = false;
    }
    if ($tracks && $track = array_pop($tracks)) {
        $timedata->finish = $track->timemodified;
    }
    else {
        $timedata->finish = $timedata->start;
    }
    return $timedata;
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

    // this treatment is necessary as the whatgrade field was not in the DB
    // and so whatgrade and grademethod are combined in grademethod 10s are whatgrade
    // and 1s are grademethod
    $grademethod = $scorm->grademethod % 10;

    foreach ($scoes as $sco) {
        if ($userdata = scorm_get_tracks($sco->id, $userid, $attempt)) {
            if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                $attemptscore->scoes++;
            }
            if (isset($userdata->score_raw)) {
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
        default:
            $score = $attemptscore->max;   // Remote Learner GRADEHIGHEST is default
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
    // this treatment is necessary as the whatgrade field was not in the DB
    // and so whatgrade and grademethod are combined in grademethod 10s are whatgrade
    // and 1s are grademethod
    $whatgrade = intval($scorm->grademethod / 10);

    // insure we dont grade user beyond $scorm->maxattempt settings
    $lastattempt = scorm_get_last_attempt($scorm->id, $userid);
    if($scorm->maxattempt != 0 && $lastattempt >= $scorm->maxattempt){
        $lastattempt = $scorm->maxattempt;
    }

    switch ($whatgrade) {
        case FIRSTATTEMPT:
            return scorm_grade_user_attempt($scorm, $userid, 1, $time);
        break;
        case LASTATTEMPT:
            return scorm_grade_user_attempt($scorm, $userid, scorm_get_last_attempt($scorm->id, $userid), $time);
        break;
        case HIGHESTATTEMPT:
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
    return count_records_select('scorm_scoes',"scorm=$scormid$strorganization AND launch<>'".sql_empty()."'");
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
            error('Course Module ID was incorrect');
        }
        $colspan = '';
        $headertext = '<table width="100%"><tr><td class="title">'.get_string('name').': <b>'.format_string($scorm->name).'</b>';
        if (has_capability('moodle/course:manageactivities', $context)) {
            if (isediting($course->id)) {
                // Display update icon
                $path = $CFG->wwwroot.'/course';
                $headertext .= '<span class="commands">'.
                        '<a title="'.$strupdate.'" href="'.$path.'/mod.php?update='.$cm->id.'&amp;sesskey='.sesskey().'">'.
                        '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$strupdate.'" /></a></span>';
            }
            $headertext .= '</td>';
            // Display report link
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                $headertext .= '<td class="reportlink">'.
                              '<a '.$CFG->frametarget.'" href="'.$CFG->wwwroot.'/mod/scorm/report.php?id='.$cm->id.'">'.
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

    if ($scorm->updatefreq == UPDATE_EVERYTIME){
        require_once($CFG->dirroot.'/mod/scorm/lib.php');

        $scorm->instance = $scorm->id;
        scorm_update_instance($scorm);
    }

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
            <div class='scorm-center'>
                <?php print_string('organizations','scorm') ?>
                <form id='changeorg' method='post' action='<?php echo $action ?>'>
                    <?php choose_from_menu($orgs, 'organization', "$organization", '','submit()') ?>
                </form>
            </div>
<?php
        }
    }
    $orgidentifier = '';
    if ($sco = scorm_get_sco($organization, SCO_ONLY)) {
        if (($sco->organization == '') && ($sco->launch == '')) {
            $orgidentifier = $sco->identifier;
        } else {
            $orgidentifier = $sco->organization;
        }
    }

/*
 $orgidentifier = '';
    if ($org = get_record('scorm_scoes','id',$organization)) {
        if (($org->organization == '') && ($org->launch == '')) {
            $orgidentifier = $org->identifier;
        } else {
            $orgidentifier = $org->organization;
        }
    }*/

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
            <div class="scorm-center">
               <form id="theform" method="post" action="<?php echo $CFG->wwwroot ?>/mod/scorm/player.php">
              <?php
                  if ($scorm->hidebrowse == 0) {
                      print_string('mode','scorm');
                      echo ': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse','scorm').'</label>'."\n";
                      echo '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal','scorm')."</label>\n";
                  } else {
                      echo '<input type="hidden" name="mode" value="normal" />'."\n";
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
              <input type="hidden" name="scoid"/>
              <input type="hidden" name="id" value="<?php echo $cm->id ?>"/>
              <input type="hidden" name="currentorg" value="<?php echo $orgidentifier ?>" />
              <input type="submit" value="<?php print_string('enter','scorm') ?>" />
              </form>
          </div>
<?php
}
function scorm_simple_play($scorm,$user) {
    $result = false;

    if ($scorm->updatefreq == UPDATE_EVERYTIME) {
        scorm_parse($scorm);
    }

    $scoes = get_records_select('scorm_scoes','scorm='.$scorm->id.' AND launch<>\''.sql_empty().'\'');

    if ($scoes) {
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
/*
function scorm_simple_play($scorm,$user) {
    $result = false;
    if ($scoes = get_records_select('scorm_scoes','scorm='.$scorm->id.' AND launch<>""')) {
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
    }
    return $result;
}
*/
function scorm_parse($scorm) {
    global $CFG;

    if ($scorm->reference[0] == '#') {
        if (isset($CFG->repositoryactivate) && $CFG->repositoryactivate) {
            $referencedir = $CFG->repository.substr($scorm->reference,1);
        }
    } else {
        if ((!scorm_external_link($scorm->reference)) && (basename($scorm->reference) == 'imsmanifest.xml')) {
            $referencedir = $CFG->dataroot.'/'.$scorm->course.'/'.$scorm->datadir;
        } else {
            $referencedir = $CFG->dataroot.'/'.$scorm->course.'/moddata/scorm/'.$scorm->id;
        }
    }

    // Parse scorm manifest
    if ($scorm->pkgtype == 'AICC') {
        require_once('datamodels/aicclib.php');
        $scorm->launch = scorm_parse_aicc($referencedir, $scorm->id);
    } else {
        require_once('datamodels/scormlib.php');
        $scorm->launch = scorm_parse_scorm($referencedir,$scorm->id);
    }
    return $scorm->launch;
}

/**
* Given a manifest path, this function will check if the manifest is valid
*
* @param string $manifest The manifest file
* @return object
*/
function scorm_validate_manifest($manifest) {
    $validation = new stdClass();
    if (is_file($manifest)) {
        $validation->result = true;
    } else {
        $validation->result = false;
        $validation->errors['reference'] = get_string('nomanifest','scorm');
    }
    return $validation;
}

/**
* Given a aicc package directory, this function will check if the course structure is valid
*
* @param string $packagedir The aicc package directory path
* @return object
*/
function scorm_validate_aicc($packagedir) {
    $validation = new stdClass();
    $validation->result = false;
    if (is_dir($packagedir)) {
        if ($handle = opendir($packagedir)) {
            while (($file = readdir($handle)) !== false) {
                $ext = substr($file,strrpos($file,'.'));
                if (strtolower($ext) == '.cst') {
                    $validation->result = true;
                    break;
                }
            }
            closedir($handle);
        }
    }
    if ($validation->result == false) {
        $validation->errors['reference'] = get_string('nomanifest','scorm');
    }
    return $validation;
}


function scorm_validate($data) {
    global $CFG;

    $validation = new stdClass();
    $validation->errors = array();

    if (!isset($data['course']) || empty($data['course'])) {
        $validation->errors['reference'] = get_string('missingparam','scorm');
        $validation->result = false;
        return $validation;
    }
    $courseid = $data['course'];                  // Course Module ID

    if (!isset($data['reference']) || empty($data['reference'])) {
        $validation->errors['reference'] = get_string('packagefile','scorm');
        $validation->result = false;
        return $validation;
    }
    $reference = $data['reference'];              // Package/manifest path/location

    $scormid = $data['instance'];                 // scorm ID
    $scorm = new stdClass();
    if (!empty($scormid)) {
        if (!$scorm = get_record('scorm','id',$scormid)) {
            $validation->errors['reference'] = get_string('missingparam','scorm');
            $validation->result = false;
            return $validation;
        }
    }

    if ($reference[0] == '#') {
        if (isset($CFG->repositoryactivate) && $CFG->repositoryactivate) {
            $reference = $CFG->repository.substr($reference,1).'/imsmanifest.xml';
        } else {
            $validation->errors['reference'] = get_string('badpackage','scorm');
            $validation->result = false;
            return $validation;
        }
    } else if (!scorm_external_link($reference)) {
        $reference = $CFG->dataroot.'/'.$courseid.'/'.$reference;
    }

    // Create a temporary directory to unzip package or copy manifest and validate package
    $tempdir = '';
    $scormdir = '';
    if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
        if ($tempdir = scorm_tempdir($scormdir)) {
            $localreference = $tempdir.'/'.basename($reference);
            copy ("$reference", $localreference);
            if (!is_file($localreference)) {
                $validation->errors['reference'] = get_string('badpackage','scorm');
                $validation->result = false;
            } else {
                $ext = strtolower(substr(basename($localreference),strrpos(basename($localreference),'.')));
                switch ($ext) {
                    case '.pif':
                    case '.zip':
                        if (!unzip_file($localreference, $tempdir, false)) {
                            $validation->errors['reference'] = get_string('unziperror','scorm');
                            $validation->result = false;
                        } else {
                            unlink ($localreference);
                            if (is_file($tempdir.'/imsmanifest.xml')) {
                                $validation = scorm_validate_manifest($tempdir.'/imsmanifest.xml');
                                $validation->pkgtype = 'SCORM';
                            } else {
                                $validation = scorm_validate_aicc($tempdir);
                                if (($validation->result == 'regular') || ($validation->result == 'found')) {
                                    $validation->pkgtype = 'AICC';
                                } else {
                                    $validation->errors['reference'] = get_string('nomanifest','scorm');
                                    $validation->result = false;
                                }
                            }
                        }
                    break;
                    case '.xml':
                        if (basename($localreference) == 'imsmanifest.xml') {
                            $validation = scorm_validate_manifest($localreference);
                        } else {
                            $validation->errors['reference'] = get_string('nomanifest','scorm');
                            $validation->result = false;
                        }
                    break;
                    default:
                        $validation->errors['reference'] = get_string('badpackage','scorm');
                        $validation->result = false;
                    break;
                }
            }
            if (is_dir($tempdir)) {
            // Delete files and temporary directory
                scorm_delete_files($tempdir);
            }
        } else {
            $validation->errors['reference'] = get_string('packagedir','scorm');
            $validation->result = false;
        }
    } else {
        $validation->errors['reference'] = get_string('datadir','scorm');
        $validation->result = false;
    }
    return $validation;
}

function scorm_check_package($data) {
    global $CFG, $COURSE;

    require_once($CFG->libdir.'/filelib.php');

    $courseid = $data->course;                  // Course Module ID
    $reference = $data->reference;              // Package path
    $scormid = $data->instance;                 // scorm ID

    $validation = new stdClass();

    if (!empty($courseid) && !empty($reference)) {
        $externalpackage = scorm_external_link($reference);

        $validation->launch = 0;
        $referencefield = $reference;
        if (empty($reference)) {
            $validation = null;
        } else if ($reference[0] == '#') {
            if (isset($CFG->repositoryactivate) && $CFG->repositoryactivate) {
                $referencefield = $reference.'/imsmanifest.xml';
                $reference = $CFG->repository.substr($reference,1).'/imsmanifest.xml';
            } else {
                $validation = null;
            }
        } else if (!$externalpackage) {
            $reference = $CFG->dataroot.'/'.$courseid.'/'.$reference;
        }

        if (!empty($scormid)) {
        //
        // SCORM Update
        //
            if ((!empty($validation)) && (is_file($reference) || $externalpackage)){

                if (!$externalpackage) {
                    $mdcheck = md5_file($reference);
                } else if ($externalpackage){
                    if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
                        if ($tempdir = scorm_tempdir($scormdir)) {
                            $content = download_file_content($reference);
                            $file = fopen($tempdir.'/'.basename($reference), 'x');
                            fwrite($file, $content);
                            fclose($file);
                            $mdcheck = md5_file($tempdir.'/'.basename($reference));
                            scorm_delete_files($tempdir);
                        }
                    }
                }

                if ($scorm = get_record('scorm','id',$scormid)) {
                    if ($scorm->reference[0] == '#') {
                        if (isset($CFG->repositoryactivate) && $CFG->repositoryactivate) {
                            $oldreference = $CFG->repository.substr($scorm->reference,1).'/imsmanifest.xml';
                        } else {
                            $oldreference = $scorm->reference;
                        }
                    } else if (!scorm_external_link($scorm->reference)) {
                        $oldreference = $CFG->dataroot.'/'.$courseid.'/'.$scorm->reference;
                    } else {
                        $oldreference = $scorm->reference;
                    }
                    $validation->launch = $scorm->launch;
                    if ((($oldreference == $reference) && ($mdcheck != $scorm->md5hash)) || ($oldreference != $reference)) {
                        // This is a new or a modified package
                        $validation->launch = 0;
                    } else {
                    // Old package already validated
                        if (strpos($scorm->version,'AICC') !== false) {
                            $validation->pkgtype = 'AICC';
                        } else {
                            $validation->pkgtype = 'SCORM';
                        }
                    }
                } else {
                    $validation = null;
                }
            } else {
                $validation = null;
            }
        }
        //$validation->launch = 0;
        if (($validation != null) && ($validation->launch == 0)) {
        //
        // Package must be validated
        //
            $ext = strtolower(substr(basename($reference),strrpos(basename($reference),'.')));
            $tempdir = '';
            switch ($ext) {
                case '.pif':
                case '.zip':
                // Create a temporary directory to unzip package and validate package
                    $scormdir = '';
                    if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
                        if ($tempdir = scorm_tempdir($scormdir)) {
                            if ($externalpackage){
                                $content = download_file_content($reference);
                                $file = fopen($tempdir.'/'.basename($reference), 'x');
                                fwrite($file, $content);
                                fclose($file);
                            } else {
                                copy ("$reference", $tempdir.'/'.basename($reference));
                            }
                            unzip_file($tempdir.'/'.basename($reference), $tempdir, false);
                            if (!$externalpackage) {
                                unlink ($tempdir.'/'.basename($reference));
                            }
                            if (is_file($tempdir.'/imsmanifest.xml')) {
                                $validation = scorm_validate_manifest($tempdir.'/imsmanifest.xml');
                                $validation->pkgtype = 'SCORM';
                            } else {
                                $validation = scorm_validate_aicc($tempdir);
                                $validation->pkgtype = 'AICC';
                            }
                        } else {
                            $validation = null;
                        }
                    } else {
                        $validation = null;
                    }
                break;
                case '.xml':
                    if (basename($reference) == 'imsmanifest.xml') {
                        if ($externalpackage) {
                            if ($scormdir = make_upload_directory("$courseid/$CFG->moddata/scorm")) {
                                if ($tempdir = scorm_tempdir($scormdir)) {
                                    $content = download_file_content($reference);
                                    $file = fopen($tempdir.'/'.basename($reference), 'x');
                                    fwrite($file, $content);
                                    fclose($file);
                                    if (is_file($tempdir.'/'.basename($reference))) {
                                        $validation = scorm_validate_manifest($tempdir.'/'.basename($reference));
                                    } else {
                                        $validation = null;
                                    }
                                }
                            }
                        } else {
                            $validation = scorm_validate_manifest($reference);
                        }
                        $validation->pkgtype = 'SCORM';
                    } else {
                        $validation = null;
                    }
                break;
                default:
                    $validation = null;
                break;
            }
            if ($validation == null) {
                if (is_dir($tempdir)) {
                // Delete files and temporary directory
                    scorm_delete_files($tempdir);
                }
            } else {
                if (($ext == '.xml') && (!$externalpackage)) {
                    $validation->datadir = dirname($referencefield);
                } else {
                    $validation->datadir = substr($tempdir,strlen($scormdir));
                }
                $validation->launch = 0;
            }
        }
    } else {
        $validation = null;
    }
    return $validation;
}


function scorm_get_count_users($scormid, $groupingid=null) {

    global $CFG;

    if (!empty($CFG->enablegroupings) && !empty($groupingid)) {
        $sql = "SELECT COUNT(DISTINCT st.userid)
                FROM {$CFG->prefix}scorm_scoes_track st
                    INNER JOIN {$CFG->prefix}groups_members gm ON st.userid = gm.userid
                    INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = gg.groupid
                WHERE st.scormid = $scormid AND gg.groupingid = $groupingid
                ";
    } else {
        $sql = "SELECT COUNT(DISTINCT st.userid)
                FROM {$CFG->prefix}scorm_scoes_track st
                WHERE st.scormid = $scormid
                ";
    }

    return(count_records_sql($sql));
}

 /**
* Build up the JavaScript representation of an array element
*
* @param string $sversion SCORM API version
* @param array $userdata User track data
* @param string $element_name Name of array element to get values for
* @param array $children list of sub elements of this array element that also need instantiating
* @return None
*/
function scorm_reconstitute_array_element($sversion, $userdata, $element_name, $children) {
    // reconstitute comments_from_learner and comments_from_lms
    $current = '';
    $current_subelement = '';
    $current_sub = '';
    $count = 0;
    $count_sub = 0;

    // filter out the ones we want
    $element_list = array();
    foreach($userdata as $element => $value){
        if (substr($element,0,strlen($element_name)) == $element_name) {
            $element_list[$element] = $value;
        }
    }

    // sort elements in .n array order
    uksort($element_list, "scorm_element_cmp");

    // generate JavaScript
    foreach($element_list as $element => $value){
        if ($sversion == 'scorm_13') {
            $element = preg_replace('/\.(\d+)\./', ".N\$1.", $element);
            preg_match('/\.(N\d+)\./', $element, $matches);
        } else {
            $element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
            preg_match('/\_(\d+)\./', $element, $matches);
        }
        if (count($matches) > 0 && $current != $matches[1]) {
            if ($count_sub > 0) {
                echo '    '.$element_name.'_'.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
            }
            $current = $matches[1];
            $count++;
            $current_subelement = '';
            $current_sub = '';
            $count_sub = 0;
            $end = strpos($element,$matches[1])+strlen($matches[1]);
            $subelement = substr($element,0,$end);
            echo '    '.$subelement." = new Object();\n";
            // now add the children
            foreach ($children as $child) {
                echo '    '.$subelement.".".$child." = new Object();\n";
                echo '    '.$subelement.".".$child."._children = ".$child."_children;\n";
            }
        }

        // now - flesh out the second level elements if there are any
        if ($sversion == 'scorm_13') {
            $element = preg_replace('/(.*?\.N\d+\..*?)\.(\d+)\./', "\$1.N\$2.", $element);
            preg_match('/.*?\.N\d+\.(.*?)\.(N\d+)\./', $element, $matches);
        } else {
            $element = preg_replace('/(.*?\_\d+\..*?)\.(\d+)\./', "\$1_\$2.", $element);
            preg_match('/.*?\_\d+\.(.*?)\_(\d+)\./', $element, $matches);
        }

        // check the sub element type
        if (count($matches) > 0 && $current_subelement != $matches[1]) {
            if ($count_sub > 0) {
                if ($sversion == 'scorm_13') {
                    echo '    '.$element_name.'.'.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
                }
                else {
                    echo '    '.$element_name.'_'.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
                }
            }
            $current_subelement = $matches[1];
            $current_sub = '';
            $count_sub = 0;
            $end = strpos($element,$matches[1])+strlen($matches[1]);
            $subelement = substr($element,0,$end);
            echo '    '.$subelement." = new Object();\n";
        }

        // now check the subelement subscript
        if (count($matches) > 0 && $current_sub != $matches[2]) {
            $current_sub = $matches[2];
            $count_sub++;
            $end = strrpos($element,$matches[2])+strlen($matches[2]);
            $subelement = substr($element,0,$end);
            echo '    '.$subelement." = new Object();\n";
        }

        echo '    '.$element.' = \''.$value."';\n";
    }
    if ($count_sub > 0) {
        if ($sversion == 'scorm_13') {
            echo '    '.$element_name.'.'.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
        }
        else {
            echo '    '.$element_name.'_'.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
        }
    }
    if ($count > 0) {
        echo '    '.$element_name.'._count = '.$count.";\n";
    }
}

/**
* Build up the JavaScript representation of an array element
*
* @param string $a left array element
* @param string $b right array element
* @return comparator - 0,1,-1
*/
function scorm_element_cmp($a, $b) {
    preg_match('/.*?(\d+)\./', $a, $matches);
    $left = intval($matches[1]);
    preg_match('/.?(\d+)\./', $b, $matches);
    $right = intval($matches[1]);
    if ($left < $right) {
        return -1; // smaller
    } elseif ($left > $right) {
        return 1;  // bigger
    } else {
        // look for a second level qualifier eg cmi.interactions_0.correct_responses_0.pattern
        if (preg_match('/.*?(\d+)\.(.*?)\.(\d+)\./', $a, $matches)) {
            $leftterm = intval($matches[2]);
            $left = intval($matches[3]);
            if (preg_match('/.*?(\d+)\.(.*?)\.(\d+)\./', $b, $matches)) {
                $rightterm = intval($matches[2]);
                $right = intval($matches[3]);
                if ($leftterm < $rightterm) {
                    return -1; // smaller
                } elseif ($leftterm > $rightterm) {
                    return 1;  // bigger
                } else {
                    if ($left < $right) {
                        return -1; // smaller
                    } elseif ($left > $right) {
                        return 1;  // bigger
                    }
                }
            }
        }
        // fall back for no second level matches or second level matches are equal
        return 0;  // equal to
    }
}

/**
* Delete Scorm tracks for selected users
*
* @param array $attemptids list of attempts that need to be deleted
* @param int $scormid ID of Scorm
*
* return bool true deleted all responses, false failed deleting an attempt - stopped here
*/
function scorm_delete_responses($attemptids, $scormid) {
    if(!is_array($attemptids) || empty($attemptids)) {
        return false;
    }

    foreach($attemptids as $num => $attemptid) {
        if(empty($attemptid)) {
            unset($attemptids[$num]);
        }
    }

    foreach($attemptids as $attempt) {
        $keys = explode(':', $attempt);
        if (count($keys) == 2) {
            $userid = clean_param($keys[0], PARAM_INT);
            $attemptid = clean_param($keys[1], PARAM_INT);
            if (!$userid || !$attemptid || !scorm_delete_attempt($userid, $scormid, $attemptid)) {
                    return false;
            }
        } else {
            return false;
        }
    }
    return true;
}

/**
* Delete Scorm tracks for selected users
*
* @param int $userid ID of User
* @param int $scormid ID of Scorm
* @param int $attemptid user attempt that need to be deleted
*
* return bool true suceeded
*/
function scorm_delete_attempt($userid, $scormid, $attemptid) {
    delete_records('scorm_scoes_track', 'userid', $userid, 'scormid', $scormid, 'attempt', $attemptid);
    return true;
}

/**
 * Converts SCORM date/time notation to human-readable format
 * The function works with both SCORM 1.2 and SCORM 2004 time formats
 * @param $datetime string SCORM date/time
 * @return string human-readable date/time
 */
function scorm_format_date_time($datetime) {
    // fetch date/time strings
    $stryears = get_string('numyears');
    $strmonths = get_string('nummonths');
    $strdays = get_string('numdays');
    $strhours = get_string('numhours');
    $strminutes = get_string('numminutes');
    $strseconds = get_string('numseconds'); 
    
    if ($datetime[0] == 'P') {
        // if timestamp starts with 'P' - it's a SCORM 2004 format
        // this regexp discards empty sections, takes Month/Minute ambiguity into consideration,
        // and outputs filled sections, discarding leading zeroes and any format literals
        // also saves the only zero before seconds decimals (if there are any) and discards decimals if they are zero
        $pattern = array( '#([A-Z])0+Y#', '#([A-Z])0+M#', '#([A-Z])0+D#', '#P(|\d+Y)0*(\d+)M#', '#0*(\d+)Y#', '#0*(\d+)D#', '#P#',
                          '#([A-Z])0+H#', '#([A-Z])[0.]+S#', '#\.0+S#', '#T(|\d+H)0*(\d+)M#', '#0*(\d+)H#', '#0+\.(\d+)S#', '#0*([\d.]+)S#', '#T#' );
        $replace = array( '$1', '$1', '$1', '$1$2'.$strmonths.' ', '$1'.$stryears.' ', '$1'.$strdays.' ', '',
                          '$1', '$1', 'S', '$1$2'.$strminutes.' ', '$1'.$strhours.' ', '0.$1'.$strseconds, '$1'.$strseconds, '');
    } else {
        // else we have SCORM 1.2 format there
        // first convert the timestamp to some SCORM 2004-like format for conveniency
        $datetime = preg_replace('#^(\d+):(\d+):([\d.]+)$#', 'T$1H$2M$3S', $datetime);
        // then convert in the same way as SCORM 2004
        $pattern = array( '#T0+H#', '#([A-Z])0+M#', '#([A-Z])[0.]+S#', '#\.0+S#', '#0*(\d+)H#', '#0*(\d+)M#', '#0+\.(\d+)S#', '#0*([\d.]+)S#', '#T#' );
        $replace = array( 'T', '$1', '$1', 'S', '$1'.$strhours.' ', '$1'.$strminutes.' ', '0.$1'.$strseconds, '$1'.$strseconds, '' );
        //$pattern = '##';
        //$replace = '';
    }

    $result = preg_replace($pattern, $replace, $datetime);

    return $result;
}

?>