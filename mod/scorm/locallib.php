<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once("$CFG->dirroot/mod/scorm/lib.php");
require_once("$CFG->libdir/filelib.php");

/// Constants and settings for module scorm
define('SCORM_UPDATE_NEVER', '0');
define('SCORM_UPDATE_EVERYDAY', '2');
define('SCORM_UPDATE_EVERYTIME', '3');

define('SCORM_SKIPVIEW_NEVER', '0');
define('SCORM_SKIPVIEW_FIRST', '1');
define('SCORM_SKIPVIEW_ALWAYS', '2');

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

define('TOCJSLINK', 1);
define('TOCFULLURL', 2);

/// Local Library of functions for module scorm

/**
 * @package   mod-scorm
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scorm_package_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

/**
 * Returns an array of the popup options for SCORM and each options default value
 *
 * @return array an array of popup options as the key and their defaults as the value
 */
function scorm_get_popup_options_array() {
    global $CFG;
    $cfg_scorm = get_config('scorm');

    return array('resizable'=> isset($cfg_scorm->resizable) ? $cfg_scorm->resizable : 0,
                 'scrollbars'=> isset($cfg_scorm->scrollbars) ? $cfg_scorm->scrollbars : 0,
                 'directories'=> isset($cfg_scorm->directories) ? $cfg_scorm->directories : 0,
                 'location'=> isset($cfg_scorm->location) ? $cfg_scorm->location : 0,
                 'menubar'=> isset($cfg_scorm->menubar) ? $cfg_scorm->menubar : 0,
                 'toolbar'=> isset($cfg_scorm->toolbar) ? $cfg_scorm->toolbar : 0,
                 'status'=> isset($cfg_scorm->status) ? $cfg_scorm->status : 0);
}

/**
 * Returns an array of the array of what grade options
 *
 * @return array an array of what grade options
 */
function scorm_get_grade_method_array() {
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
function scorm_get_what_grade_array() {
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
function scorm_get_skip_view_array() {
    return array(SCORM_SKIPVIEW_NEVER => get_string('never'),
                 SCORM_SKIPVIEW_FIRST => get_string('firstaccess', 'scorm'),
                 SCORM_SKIPVIEW_ALWAYS => get_string('always'));
}

/**
 * Returns an array of the array of hide table of contents options
 *
 * @return array an array of hide table of contents options
 */
function scorm_get_hidetoc_array() {
     return array(SCORM_TOC_SIDE => get_string('sided', 'scorm'),
                  SCORM_TOC_HIDDEN => get_string('hidden', 'scorm'),
                  SCORM_TOC_POPUP => get_string('popupmenu', 'scorm'),
                  SCORM_TOC_DISABLED => get_string('disabled', 'scorm'));
}

/**
 * Returns an array of the array of update frequency options
 *
 * @return array an array of update frequency options
 */
function scorm_get_updatefreq_array() {
    return array(SCORM_UPDATE_NEVER => get_string('never'),
                 SCORM_UPDATE_EVERYDAY => get_string('everyday', 'scorm'),
                 SCORM_UPDATE_EVERYTIME => get_string('everytime', 'scorm'));
}

/**
 * Returns an array of the array of popup display options
 *
 * @return array an array of popup display options
 */
function scorm_get_popup_display_array() {
    return array(0 => get_string('currentwindow', 'scorm'),
                 1 => get_string('popup', 'scorm'));
}

/**
 * Returns an array of the array of attempt options
 *
 * @return array an array of attempt options
 */
function scorm_get_attempts_array() {
    $attempts = array(0 => get_string('nolimit', 'scorm'),
                      1 => get_string('attempt1', 'scorm'));

    for ($i=2; $i<=6; $i++) {
        $attempts[$i] = get_string('attemptsx', 'scorm', $i);
    }

    return $attempts;
}

/**
 * Returns an array of the attempt status options
 *
 * @return array an array of attempt status options
 */
function scorm_get_attemptstatus_array() {
    return array(SCORM_DISPLAY_ATTEMPTSTATUS_NO => get_string('no'),
                 SCORM_DISPLAY_ATTEMPTSTATUS_ALL => get_string('attemptstatusall', 'scorm'),
                 SCORM_DISPLAY_ATTEMPTSTATUS_MY => get_string('attemptstatusmy', 'scorm'),
                 SCORM_DISPLAY_ATTEMPTSTATUS_ENTRY => get_string('attemptstatusentry', 'scorm'));
}

/**
 * Extracts scrom package, sets up all variables.
 * Called whenever scorm changes
 * @param object $scorm instance - fields are updated and changes saved into database
 * @param bool $full force full update if true
 * @return void
 */
function scorm_parse($scorm, $full) {
    global $CFG, $DB;
    $cfg_scorm = get_config('scorm');

    if (!isset($scorm->cmid)) {
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);
        $scorm->cmid = $cm->id;
    }
    $context = context_module::instance($scorm->cmid);
    $newhash = $scorm->sha1hash;

    if ($scorm->scormtype === SCORM_TYPE_LOCAL or $scorm->scormtype === SCORM_TYPE_LOCALSYNC) {

        $fs = get_file_storage();
        $packagefile = false;

        if ($scorm->scormtype === SCORM_TYPE_LOCAL) {
            if ($packagefile = $fs->get_file($context->id, 'mod_scorm', 'package', 0, '/', $scorm->reference)) {
                $newhash = $packagefile->get_contenthash();
            } else {
                $newhash = null;
            }
        } else {
            if (!$cfg_scorm->allowtypelocalsync) {
                // sorry - localsync disabled
                return;
            }
            if ($scorm->reference !== '' and (!$full or $scorm->sha1hash !== sha1($scorm->reference))) {
                $fs->delete_area_files($context->id, 'mod_scorm', 'package');
                $file_record = array('contextid'=>$context->id, 'component'=>'mod_scorm', 'filearea'=>'package', 'itemid'=>0, 'filepath'=>'/');
                if ($packagefile = $fs->create_file_from_url($file_record, $scorm->reference, array('calctimeout' => true))) {
                    $newhash = sha1($scorm->reference);
                } else {
                    $newhash = null;
                }
            }
        }

        if ($packagefile) {
            if (!$full and $packagefile and $scorm->sha1hash === $newhash) {
                if (strpos($scorm->version, 'SCORM') !== false) {
                    if ($fs->get_file($context->id, 'mod_scorm', 'content', 0, '/', 'imsmanifest.xml')) {
                        // no need to update
                        return;
                    }
                } else if (strpos($scorm->version, 'AICC') !== false) {
                    // TODO: add more sanity checks - something really exists in scorm_content area
                    return;
                }
            }

            // now extract files
            $fs->delete_area_files($context->id, 'mod_scorm', 'content');

            $packer = get_file_packer('application/zip');
            $packagefile->extract_to_storage($packer, $context->id, 'mod_scorm', 'content', 0, '/');

        } else if (!$full) {
            return;
        }

        if ($manifest = $fs->get_file($context->id, 'mod_scorm', 'content', 0, '/', 'imsmanifest.xml')) {
            require_once("$CFG->dirroot/mod/scorm/datamodels/scormlib.php");
            // SCORM
            if (!scorm_parse_scorm($scorm, $manifest)) {
                $scorm->version = 'ERROR';
            }
        } else {
            require_once("$CFG->dirroot/mod/scorm/datamodels/aicclib.php");
            // AICC
            if (!scorm_parse_aicc($scorm)) {
                $scorm->version = 'ERROR';
            }
            $scorm->version = 'AICC';
        }

    } else if ($scorm->scormtype === SCORM_TYPE_EXTERNAL and $cfg_scorm->allowtypeexternal) {
        require_once("$CFG->dirroot/mod/scorm/datamodels/scormlib.php");
        // SCORM only, AICC can not be external
        if (!scorm_parse_scorm($scorm, $scorm->reference)) {
            $scorm->version = 'ERROR';
        }
        $newhash = sha1($scorm->reference);

    } else if ($scorm->scormtype === SCORM_TYPE_IMSREPOSITORY and !empty($CFG->repositoryactivate) and $cfg_scorm->allowtypeimsrepository) {
        if (!$full and $scorm->sha1hash === sha1($scorm->reference)) {
            return;
        }
        require_once("$CFG->dirroot/mod/scorm/datamodels/scormlib.php");
        if (!scorm_parse_scorm($scorm, $CFG->repository.substr($scorm->reference, 1).'/imsmanifest.xml')) {
            $scorm->version = 'ERROR';
        }
        $newhash = sha1($scorm->reference);
    } else if ($scorm->scormtype === SCORM_TYPE_AICCURL  and $cfg_scorm->allowtypeexternalaicc) {
        require_once("$CFG->dirroot/mod/scorm/datamodels/aicclib.php");
        // AICC
        if (!scorm_parse_aicc($scorm)) {
            $scorm->version = 'ERROR';
        }
        $scorm->version = 'AICC';
    } else {
        // sorry, disabled type
        return;
    }

    $scorm->revision++;
    $scorm->sha1hash = $newhash;
    $DB->update_record('scorm', $scorm);
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
    for ($i=0; $i<$times; $i++) {
        $return .= $what;
    }
    return $return;
}

function scorm_external_link($link) {
    // check if a link is external
    $result = false;
    $link = strtolower($link);
    if (substr($link, 0, 7) == 'http://') {
        $result = true;
    } else if (substr($link, 0, 8) == 'https://') {
        $result = true;
    } else if (substr($link, 0, 4) == 'www.') {
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
function scorm_get_sco($id, $what=SCO_ALL) {
    global $DB;

    if ($sco = $DB->get_record('scorm_scoes', array('id'=>$id))) {
        $sco = ($what == SCO_DATA) ? new stdClass() : $sco;
        if (($what != SCO_ONLY) && ($scodatas = $DB->get_records('scorm_scoes_data', array('scoid'=>$id)))) {
            foreach ($scodatas as $scodata) {
                $sco->{$scodata->name} = $scodata->value;
            }
        } else if (($what != SCO_ONLY) && (!($scodatas = $DB->get_records('scorm_scoes_data', array('scoid'=>$id))))) {
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
function scorm_get_scoes($id, $organisation=false) {
    global $DB;

    $organizationsql = '';
    $queryarray = array('scorm'=>$id);
    if (!empty($organisation)) {
        $queryarray['organization'] = $organisation;
    }
    if ($scoes = $DB->get_records('scorm_scoes', $queryarray, 'id ASC')) {
        // drop keys so that it is a simple array as expected
        $scoes = array_values($scoes);
        foreach ($scoes as $sco) {
            if ($scodatas = $DB->get_records('scorm_scoes_data', array('scoid'=>$sco->id))) {
                foreach ($scodatas as $scodata) {
                    $sco->{$scodata->name} = $scodata->value;
                }
            }
        }
        return $scoes;
    } else {
        return false;
    }
}

function scorm_insert_track($userid, $scormid, $scoid, $attempt, $element, $value, $forcecompleted=false) {
    global $DB, $CFG;

    $id = null;

    if ($forcecompleted) {
        //TODO - this could be broadened to encompass SCORM 2004 in future
        if (($element == 'cmi.core.lesson_status') && ($value == 'incomplete')) {
            if ($track = $DB->get_record_select('scorm_scoes_track', 'userid=? AND scormid=? AND scoid=? AND attempt=? AND element=\'cmi.core.score.raw\'', array($userid, $scormid, $scoid, $attempt))) {
                $value = 'completed';
            }
        }
        if ($element == 'cmi.core.score.raw') {
            if ($tracktest = $DB->get_record_select('scorm_scoes_track', 'userid=? AND scormid=? AND scoid=? AND attempt=? AND element=\'cmi.core.lesson_status\'', array($userid, $scormid, $scoid, $attempt))) {
                if ($tracktest->value == "incomplete") {
                    $tracktest->value = "completed";
                    $DB->update_record('scorm_scoes_track', $tracktest);
                }
            }
        }
        if (($element == 'cmi.success_status') && ($value == 'passed' || $value == 'failed')) {
            if ($DB->get_record('scorm_scoes_data', array('scoid' => $scoid, 'name' => 'objectivesetbycontent'))) {
                $objectiveprogressstatus = true;
                $objectivesatisfiedstatus = false;
                if ($value == 'passed') {
                    $objectivesatisfiedstatus = true;
                }

                if ($track = $DB->get_record('scorm_scoes_track', array('userid' => $userid,
                                                                        'scormid' => $scormid,
                                                                        'scoid' => $scoid,
                                                                        'attempt' => $attempt,
                                                                        'element' => 'objectiveprogressstatus'))) {
                    $track->value = $objectiveprogressstatus;
                    $track->timemodified = time();
                    $DB->update_record('scorm_scoes_track', $track);
                    $id = $track->id;
                } else {
                    $track = new stdClass();
                    $track->userid = $userid;
                    $track->scormid = $scormid;
                    $track->scoid = $scoid;
                    $track->attempt = $attempt;
                    $track->element = 'objectiveprogressstatus';
                    $track->value = $objectiveprogressstatus;
                    $track->timemodified = time();
                    $id = $DB->insert_record('scorm_scoes_track', $track);
                }
                if ($objectivesatisfiedstatus) {
                    if ($track = $DB->get_record('scorm_scoes_track', array('userid' => $userid,
                                                                            'scormid' => $scormid,
                                                                            'scoid' => $scoid,
                                                                            'attempt' => $attempt,
                                                                            'element' => 'objectivesatisfiedstatus'))) {
                        $track->value = $objectivesatisfiedstatus;
                        $track->timemodified = time();
                        $DB->update_record('scorm_scoes_track', $track);
                        $id = $track->id;
                    } else {
                        $track = new stdClass();
                        $track->userid = $userid;
                        $track->scormid = $scormid;
                        $track->scoid = $scoid;
                        $track->attempt = $attempt;
                        $track->element = 'objectivesatisfiedstatus';
                        $track->value = $objectivesatisfiedstatus;
                        $track->timemodified = time();
                        $id = $DB->insert_record('scorm_scoes_track', $track);
                        ob_start();
                        $filepath = $CFG->dataroot."\\temp\\tempfile.txt";
                        $fh = fopen($filepath, "a+");
                        var_dump($track);
                        $string = ob_get_clean();
                        fwrite($fh, $string);
                        fclose($fh);
                    }
                }
            }
        }

    }

    if ($track = $DB->get_record('scorm_scoes_track', array('userid' => $userid,
                                                            'scormid' => $scormid,
                                                            'scoid' => $scoid,
                                                            'attempt' => $attempt,
                                                            'element' => $element))) {
        if ($element != 'x.start.time' ) { // Don't update x.start.time - keep the original value.
            $track->value = $value;
            $track->timemodified = time();
            $DB->update_record('scorm_scoes_track', $track);
            $id = $track->id;
        }
    } else {
        $track = new stdClass();
        $track->userid = $userid;
        $track->scormid = $scormid;
        $track->scoid = $scoid;
        $track->attempt = $attempt;
        $track->element = $element;
        $track->value = $value;
        $track->timemodified = time();
        $id = $DB->insert_record('scorm_scoes_track', $track);
    }

    if (strstr($element, '.score.raw') ||
        (in_array($element, array('cmi.completion_status', 'cmi.core.lesson_status', 'cmi.success_status'))
         && in_array($track->value, array('completed', 'passed')))) {
        $scorm = $DB->get_record('scorm', array('id' => $scormid));
        include_once($CFG->dirroot.'/mod/scorm/lib.php');
        scorm_update_grades($scorm, $userid);
    }

    return $id;
}

/**
 * simple quick function to return true/false if this user has tracks in this scorm
 *
 * @param integer $scormid The scorm ID
 * @param integer $userid the users id
 * @return boolean (false if there are no tracks)
 */
function scorm_has_tracks($scormid, $userid) {
    global $DB;
    return $DB->record_exists('scorm_scoes_track', array('userid' => $userid, 'scormid' => $scormid));
}

function scorm_get_tracks($scoid, $userid, $attempt='') {
    /// Gets all tracks of specified sco and user
    global $CFG, $DB;

    if (empty($attempt)) {
        if ($scormid = $DB->get_field('scorm_scoes', 'scorm', array('id'=>$scoid))) {
            $attempt = scorm_get_last_attempt($scormid, $userid);
        } else {
            $attempt = 1;
        }
    }
    if ($tracks = $DB->get_records('scorm_scoes_track', array('userid'=>$userid, 'scoid'=>$scoid, 'attempt'=>$attempt), 'element ASC')) {
        $usertrack = new stdClass();
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
                    $usertrack->score_raw = (float) sprintf('%2.2f', $track->value);
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
    global $DB;

    $timedata = new stdClass();
    $sql = !empty($scoid) ? "userid=$userid AND scormid=$scormid AND scoid=$scoid AND attempt=$attempt" : "userid=$userid AND scormid=$scormid AND attempt=$attempt";
    $tracks = $DB->get_records_select('scorm_scoes_track', "$sql ORDER BY timemodified ASC");
    if ($tracks) {
        $tracks = array_values($tracks);
    }

    if ($tracks) {
        $timedata->start = $tracks[0]->timemodified;
    } else {
        $timedata->start = false;
    }
    if ($tracks && $track = array_pop($tracks)) {
        $timedata->finish = $track->timemodified;
    } else {
        $timedata->finish = $timedata->start;
    }
    return $timedata;
}


function scorm_get_user_data($userid) {
    global $DB;
    /// Gets user info required to display the table of scorm results
    /// for report.php

    return $DB->get_record('user', array('id'=>$userid), user_picture::fields());
}

function scorm_grade_user_attempt($scorm, $userid, $attempt=1) {
    global $DB;
    $attemptscore = new stdClass();
    $attemptscore->scoes = 0;
    $attemptscore->values = 0;
    $attemptscore->max = 0;
    $attemptscore->sum = 0;
    $attemptscore->lastmodify = 0;

    if (!$scoes = $DB->get_records('scorm_scoes', array('scorm'=>$scorm->id))) {
        return null;
    }

    foreach ($scoes as $sco) {
        if ($userdata=scorm_get_tracks($sco->id, $userid, $attempt)) {
            if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                $attemptscore->scoes++;
            }
            if (!empty($userdata->score_raw) || (isset($scorm->type) && $scorm->type=='sco' && isset($userdata->score_raw))) {
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
    switch ($scorm->grademethod) {
        case GRADEHIGHEST:
            $score = (float) $attemptscore->max;
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

    return $score;
}

function scorm_grade_user($scorm, $userid) {

    // ensure we dont grade user beyond $scorm->maxattempt settings
    $lastattempt = scorm_get_last_attempt($scorm->id, $userid);
    if ($scorm->maxattempt != 0 && $lastattempt >= $scorm->maxattempt) {
        $lastattempt = $scorm->maxattempt;
    }

    switch ($scorm->whatgrade) {
        case FIRSTATTEMPT:
            return scorm_grade_user_attempt($scorm, $userid, 1);
        break;
        case LASTATTEMPT:
            return scorm_grade_user_attempt($scorm, $userid, scorm_get_last_completed_attempt($scorm->id, $userid));
        break;
        case HIGHESTATTEMPT:
            $maxscore = 0;
            for ($attempt = 1; $attempt <= $lastattempt; $attempt++) {
                $attemptscore = scorm_grade_user_attempt($scorm, $userid, $attempt);
                $maxscore = $attemptscore > $maxscore ? $attemptscore: $maxscore;
            }
            return $maxscore;

        break;
        case AVERAGEATTEMPT:
            $attemptcount = scorm_get_attempt_count($userid, $scorm, true);
            if (empty($attemptcount)) {
                return 0;
            } else {
                $attemptcount = count($attemptcount);
            }
            $lastattempt = scorm_get_last_attempt($scorm->id, $userid);
            $sumscore = 0;
            for ($attempt = 1; $attempt <= $lastattempt; $attempt++) {
                $attemptscore = scorm_grade_user_attempt($scorm, $userid, $attempt);
                $sumscore += $attemptscore;
            }

            return round($sumscore / $attemptcount);
        break;
    }
}

function scorm_count_launchable($scormid, $organization='') {
    global $DB;

    $sqlorganization = '';
    $params = array($scormid);
    if (!empty($organization)) {
        $sqlorganization = " AND organization=?";
        $params[] = $organization;
    }
    return $DB->count_records_select('scorm_scoes', "scorm = ? $sqlorganization AND ".$DB->sql_isnotempty('scorm_scoes', 'launch', false, true), $params);
}

/**
 * Returns the last attempt used - if no attempts yet, returns 1 for first attempt
 *
 * @param int $scormid the id of the scorm.
 * @param int $userid the id of the user.
 *
 * @return int The attempt number to use.
 */
function scorm_get_last_attempt($scormid, $userid) {
    global $DB;

    /// Find the last attempt number for the given user id and scorm id
    $sql = "SELECT MAX(attempt)
              FROM {scorm_scoes_track}
             WHERE userid = ? AND scormid = ?";
    $lastattempt = $DB->get_field_sql($sql, array($userid, $scormid));
    if (empty($lastattempt)) {
        return '1';
    } else {
        return $lastattempt;
    }
}

/**
 * Returns the last completed attempt used - if no completed attempts yet, returns 1 for first attempt
 *
 * @param int $scormid the id of the scorm.
 * @param int $userid the id of the user.
 *
 * @return int The attempt number to use.
 */
function scorm_get_last_completed_attempt($scormid, $userid) {
    global $DB;

    /// Find the last completed attempt number for the given user id and scorm id
    $sql = "SELECT MAX(attempt)
              FROM {scorm_scoes_track}
             WHERE userid = ? AND scormid = ?
               AND (value='completed' OR value='passed')";
    $lastattempt = $DB->get_field_sql($sql, array($userid, $scormid));
    if (empty($lastattempt)) {
        return '1';
    } else {
        return $lastattempt;
    }
}

function scorm_course_format_display($user, $course) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    $strupdate = get_string('update');
    $context = context_course::instance($course->id);

    echo '<div class="mod-scorm">';
    if ($scorms = get_all_instances_in_course('scorm', $course)) {
        // The module SCORM activity with the least id is the course
        $scorm = current($scorms);
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
        $contextmodule = context_module::instance($cm->id);
        if ((has_capability('mod/scorm:skipview', $contextmodule))) {
            scorm_simple_play($scorm, $user, $contextmodule, $cm->id);
        }
        $colspan = '';
        $headertext = '<table width="100%"><tr><td class="title">'.get_string('name').': <b>'.format_string($scorm->name).'</b>';
        if (has_capability('moodle/course:manageactivities', $context)) {
            if ($PAGE->user_is_editing()) {
                // Display update icon
                $path = $CFG->wwwroot.'/course';
                $headertext .= '<span class="commands">'.
                        '<a title="'.$strupdate.'" href="'.$path.'/mod.php?update='.$cm->id.'&amp;sesskey='.sesskey().'">'.
                        '<img src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" alt="'.$strupdate.'" /></a></span>';
            }
            $headertext .= '</td>';
            // Display report link
            $trackedusers = $DB->get_record('scorm_scoes_track', array('scormid'=>$scorm->id), 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                $headertext .= '<td class="reportlink">'.
                              '<a href="'.$CFG->wwwroot.'/mod/scorm/report.php?id='.$cm->id.'">'.
                               get_string('viewallreports', 'scorm', $trackedusers->c).'</a>';
            } else {
                $headertext .= '<td class="reportlink">'.get_string('noreports', 'scorm');
            }
            $colspan = ' colspan="2"';
        }
        $headertext .= '</td></tr><tr><td'.$colspan.'>'.get_string('summary').':<br />'.format_module_intro('scorm', $scorm, $scorm->coursemodule).'</td></tr></table>';
        echo $OUTPUT->box($headertext, 'generalbox boxwidthwide');
        scorm_view_display($user, $scorm, 'view.php?id='.$course->id, $cm);
    } else {
        if (has_capability('moodle/course:update', $context)) {
            // Create a new activity
            $url = new moodle_url('/course/mod.php', array('id'=>$course->id, 'section'=>'0', 'sesskey'=>sesskey(),'add'=>'scorm'));
            redirect($url);
        } else {
            echo $OUTPUT->notification('Could not find a scorm course here');
        }
    }
    echo '</div>';
}

function scorm_view_display ($user, $scorm, $action, $cm) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    if ($scorm->scormtype != SCORM_TYPE_LOCAL && $scorm->updatefreq == SCORM_UPDATE_EVERYTIME) {
        scorm_parse($scorm, false);
    }

    $organization = optional_param('organization', '', PARAM_INT);

    if ($scorm->displaycoursestructure == 1) {
        echo $OUTPUT->box_start('generalbox boxaligncenter toc');
        ?>
        <div class="structurehead"><?php print_string('contents', 'scorm') ?></div>
        <?php
    }
    if (empty($organization)) {
        $organization = $scorm->launch;
    }
    if ($orgs = $DB->get_records_select_menu('scorm_scoes', 'scorm = ? AND '.
                                         $DB->sql_isempty('scorm_scoes', 'launch', false, true).' AND '.
                                         $DB->sql_isempty('scorm_scoes', 'organization', false, false),
                                         array($scorm->id), 'id', 'id,title')) {
        if (count($orgs) > 1) {
            $select = new single_select(new moodle_url($action), 'organization', $orgs, $organization, null);
            $select->label = get_string('organizations', 'scorm');
            $select->class = 'scorm-center';
            echo $OUTPUT->render($select);
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

    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
    if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
        $scorm->version = 'scorm_12';
    }
    require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');

    $result = scorm_get_toc($user, $scorm, $cm->id, TOCFULLURL, $orgidentifier);
    $incomplete = $result->incomplete;

    // do we want the TOC to be displayed?
    if ($scorm->displaycoursestructure == 1) {
        echo $result->toc;
        echo $OUTPUT->box_end();
    }

    // is this the first attempt ?
    $attemptcount = scorm_get_attempt_count($user->id, $scorm);

    // do not give the player launch FORM if the SCORM object is locked after the final attempt
    if ($scorm->lastattemptlock == 0 || $result->attemptleft > 0) {
        ?>
            <div class="scorm-center">
               <form id="scormviewform" method="post" action="<?php echo $CFG->wwwroot ?>/mod/scorm/player.php">
        <?php
        if ($scorm->hidebrowse == 0) {
            print_string('mode', 'scorm');
            echo ': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse', 'scorm').'</label>'."\n";
            echo '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal', 'scorm')."</label>\n";
        } else {
            echo '<input type="hidden" name="mode" value="normal" />'."\n";
        }
        if ($scorm->forcenewattempt == 1) {
            if ($incomplete === false) {
                echo '<input type="hidden" name="newattempt" value="on" />'."\n";
            }
        } else if (!empty($attemptcount) && ($incomplete === false) && (($result->attemptleft > 0)||($scorm->maxattempt == 0))) {
            ?>
                      <br />
                      <input type="checkbox" id="a" name="newattempt" />
                      <label for="a"><?php print_string('newattempt', 'scorm') ?></label>
            <?php
        }
        if (!empty($scorm->popup)) {
            echo '<input type="hidden" name="display" value="popup" />'."\n";
        }
        ?>
              <br />
              <input type="hidden" name="scoid"/>
              <input type="hidden" name="cm" value="<?php echo $cm->id ?>"/>
              <input type="hidden" name="currentorg" value="<?php echo $orgidentifier ?>" />
              <input type="submit" value="<?php print_string('enter', 'scorm') ?>" />
              </form>
          </div>
        <?php
    }
}

function scorm_simple_play($scorm, $user, $context, $cmid) {
    global $DB;

    $result = false;

    if (has_capability('mod/scorm:viewreport', $context)) { //if this user can view reports, don't skipview so they can see links to reports.
        return $result;
    }

    if ($scorm->scormtype != SCORM_TYPE_LOCAL && $scorm->updatefreq == SCORM_UPDATE_EVERYTIME) {
        scorm_parse($scorm, false);
    }
    $scoes = $DB->get_records_select('scorm_scoes', 'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true), array($scorm->id), 'id', 'id');

    if ($scoes) {
        $orgidentifier = '';
        if ($sco = scorm_get_sco($scorm->launch, SCO_ONLY)) {
            if (($sco->organization == '') && ($sco->launch == '')) {
                $orgidentifier = $sco->identifier;
            } else {
                $orgidentifier = $sco->organization;
            }
        }
        if ($scorm->skipview >= SCORM_SKIPVIEW_FIRST) {
            $sco = current($scoes);
            $url = new moodle_url('/mod/scorm/player.php', array('a' => $scorm->id,
                                                                'currentorg'=>$orgidentifier,
                                                                'scoid'=>$sco->id));
            if ($scorm->skipview == SCORM_SKIPVIEW_ALWAYS || !scorm_has_tracks($scorm->id, $user->id)) {
                if (!empty($scorm->forcenewattempt)) {
                    $result = scorm_get_toc($user, $scorm, $cmid, TOCFULLURL, $orgidentifier);
                    if ($result->incomplete === false) {
                        $url->param('newattempt','on');
                    }
                }
                redirect($url);
            }
        }
    }
    return $result;
}

function scorm_get_count_users($scormid, $groupingid=null) {
    global $CFG, $DB;

    if (!empty($groupingid)) {
        $sql = "SELECT COUNT(DISTINCT st.userid)
                FROM {scorm_scoes_track} st
                    INNER JOIN {groups_members} gm ON st.userid = gm.userid
                    INNER JOIN {groupings_groups} gg ON gm.groupid = gg.groupid
                WHERE st.scormid = ? AND gg.groupingid = ?
                ";
        $params = array($scormid, $groupingid);
    } else {
        $sql = "SELECT COUNT(DISTINCT st.userid)
                FROM {scorm_scoes_track} st
                WHERE st.scormid = ?
                ";
        $params = array($scormid);
    }

    return ($DB->count_records_sql($sql, $params));
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
    $scormseperator = '_';
    if (scorm_version_check($sversion, SCORM_13)) { //scorm 1.3 elements use a . instead of an _
        $scormseperator = '.';
    }
    // filter out the ones we want
    $element_list = array();
    foreach ($userdata as $element => $value) {
        if (substr($element, 0, strlen($element_name)) == $element_name) {
            $element_list[$element] = $value;
        }
    }

    // sort elements in .n array order
    uksort($element_list, "scorm_element_cmp");

    // generate JavaScript
    foreach ($element_list as $element => $value) {
        if (scorm_version_check($sversion, SCORM_13)) {
            $element = preg_replace('/\.(\d+)\./', ".N\$1.", $element);
            preg_match('/\.(N\d+)\./', $element, $matches);
        } else {
            $element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
            preg_match('/\_(\d+)\./', $element, $matches);
        }
        if (count($matches) > 0 && $current != $matches[1]) {
            if ($count_sub > 0) {
                echo '    '.$element_name.$scormseperator.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
            }
            $current = $matches[1];
            $count++;
            $current_subelement = '';
            $current_sub = '';
            $count_sub = 0;
            $end = strpos($element, $matches[1])+strlen($matches[1]);
            $subelement = substr($element, 0, $end);
            echo '    '.$subelement." = new Object();\n";
            // now add the children
            foreach ($children as $child) {
                echo '    '.$subelement.".".$child." = new Object();\n";
                echo '    '.$subelement.".".$child."._children = ".$child."_children;\n";
            }
        }

        // now - flesh out the second level elements if there are any
        if (scorm_version_check($sversion, SCORM_13)) {
            $element = preg_replace('/(.*?\.N\d+\..*?)\.(\d+)\./', "\$1.N\$2.", $element);
            preg_match('/.*?\.N\d+\.(.*?)\.(N\d+)\./', $element, $matches);
        } else {
            $element = preg_replace('/(.*?\_\d+\..*?)\.(\d+)\./', "\$1_\$2.", $element);
            preg_match('/.*?\_\d+\.(.*?)\_(\d+)\./', $element, $matches);
        }

        // check the sub element type
        if (count($matches) > 0 && $current_subelement != $matches[1]) {
            if ($count_sub > 0) {
                echo '    '.$element_name.$scormseperator.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
            }
            $current_subelement = $matches[1];
            $current_sub = '';
            $count_sub = 0;
            $end = strpos($element, $matches[1])+strlen($matches[1]);
            $subelement = substr($element, 0, $end);
            echo '    '.$subelement." = new Object();\n";
        }

        // now check the subelement subscript
        if (count($matches) > 0 && $current_sub != $matches[2]) {
            $current_sub = $matches[2];
            $count_sub++;
            $end = strrpos($element, $matches[2])+strlen($matches[2]);
            $subelement = substr($element, 0, $end);
            echo '    '.$subelement." = new Object();\n";
        }

        echo '    '.$element.' = \''.$value."';\n";
    }
    if ($count_sub > 0) {
        echo '    '.$element_name.$scormseperator.$current.'.'.$current_subelement.'._count = '.$count_sub.";\n";
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
    } else if ($left > $right) {
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
                } else if ($leftterm > $rightterm) {
                    return 1;  // bigger
                } else {
                    if ($left < $right) {
                        return -1; // smaller
                    } else if ($left > $right) {
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
 * Generate the user attempt status string
 *
 * @param object $user Current context user
 * @param object $scorm a moodle scrom object - mdl_scorm
 * @return string - Attempt status string
 */
function scorm_get_attempt_status($user, $scorm, $cm='') {
    global $DB, $PAGE, $OUTPUT;

    $attempts = scorm_get_attempt_count($user->id, $scorm, true);
    if (empty($attempts)) {
        $attemptcount = 0;
    } else {
        $attemptcount = count($attempts);
    }

    $result = '<p>'.get_string('noattemptsallowed', 'scorm').': ';
    if ($scorm->maxattempt > 0) {
        $result .= $scorm->maxattempt . '<br />';
    } else {
        $result .= get_string('unlimited').'<br />';
    }
    $result .= get_string('noattemptsmade', 'scorm').': ' . $attemptcount . '<br />';

    if ($scorm->maxattempt == 1) {
        switch ($scorm->grademethod) {
            case GRADEHIGHEST:
                $grademethod = get_string('gradehighest', 'scorm');
            break;
            case GRADEAVERAGE:
                $grademethod = get_string('gradeaverage', 'scorm');
            break;
            case GRADESUM:
                $grademethod = get_string('gradesum', 'scorm');
            break;
            case GRADESCOES:
                $grademethod = get_string('gradescoes', 'scorm');
            break;
        }
    } else {
        switch ($scorm->whatgrade) {
            case HIGHESTATTEMPT:
                $grademethod = get_string('highestattempt', 'scorm');
            break;
            case AVERAGEATTEMPT:
                $grademethod = get_string('averageattempt', 'scorm');
            break;
            case FIRSTATTEMPT:
                $grademethod = get_string('firstattempt', 'scorm');
            break;
            case LASTATTEMPT:
                $grademethod = get_string('lastattempt', 'scorm');
            break;
        }
    }

    if (!empty($attempts)) {
        $i = 1;
        foreach ($attempts as $attempt) {
            $gradereported = scorm_grade_user_attempt($scorm, $user->id, $attempt->attemptnumber);
            if ($scorm->grademethod !== GRADESCOES && !empty($scorm->maxgrade)) {
                $gradereported = $gradereported/$scorm->maxgrade;
                $gradereported = number_format($gradereported*100, 0) .'%';
            }
            $result .= get_string('gradeforattempt', 'scorm').' ' . $i . ': ' . $gradereported .'<br />';
            $i++;
        }
    }
    $calculatedgrade = scorm_grade_user($scorm, $user->id);
    if ($scorm->grademethod !== GRADESCOES && !empty($scorm->maxgrade)) {
        $calculatedgrade = $calculatedgrade/$scorm->maxgrade;
        $calculatedgrade = number_format($calculatedgrade*100, 0) .'%';
    }
    $result .= get_string('grademethod', 'scorm'). ': ' . $grademethod;
    if (empty($attempts)) {
        $result .= '<br />' . get_string('gradereported', 'scorm') . ': ' . get_string('none') . '<br />';
    } else {
        $result .= '<br />' . get_string('gradereported', 'scorm') . ': ' . $calculatedgrade . '<br />';
    }
    $result .= '</p>';
    if ($attemptcount >= $scorm->maxattempt and $scorm->maxattempt > 0) {
        $result .= '<p><font color="#cc0000">'.get_string('exceededmaxattempts', 'scorm').'</font></p>';
    }
    if (!empty($cm)) {
        $context = context_module::instance($cm->id);
        if (has_capability('mod/scorm:deleteownresponses', $context) &&
            $DB->record_exists('scorm_scoes_track', array('userid' => $user->id, 'scormid' => $scorm->id))) {
            //check to see if any data is stored for this user:
            $deleteurl = new moodle_url($PAGE->url, array('action'=>'delete', 'sesskey' => sesskey()));
            $result .= $OUTPUT->single_button($deleteurl, get_string('deleteallattempts', 'scorm'));
        }
    }


    return $result;
}

/**
 * Get SCORM attempt count
 *
 * @param object $user Current context user
 * @param object $scorm a moodle scrom object - mdl_scorm
 * @param bool $attempts return the list of attempts
 * @return int - no. of attempts so far
 */
function scorm_get_attempt_count($userid, $scorm, $attempts_only=false) {
    global $DB;
    $attemptcount = 0;
    $element = 'cmi.core.score.raw';
    if ($scorm->grademethod == GRADESCOES) {
        $element = 'cmi.core.lesson_status';
    }
    if (scorm_version_check($scorm->version, SCORM_13)) {
        $element = 'cmi.score.raw';
    }
    $attempts = $DB->get_records_select('scorm_scoes_track', "element=? AND userid=? AND scormid=?", array($element, $userid, $scorm->id), 'attempt', 'DISTINCT attempt AS attemptnumber');
    if ($attempts_only) {
        return $attempts;
    }
    if (!empty($attempts)) {
        $attemptcount = count($attempts);
    }
    return $attemptcount;
}

/**
 * Figure out with this is a debug situation
 *
 * @param object $scorm a moodle scrom object - mdl_scorm
 * @return boolean - debugging true/false
 */
function scorm_debugging($scorm) {
    global $CFG, $USER;
    $cfg_scorm = get_config('scorm');

    if (!$cfg_scorm->allowapidebug) {
        return false;
    }
    $identifier = $USER->username.':'.$scorm->name;
    $test = $cfg_scorm->apidebugmask;
    // check the regex is only a short list of safe characters
    if (!preg_match('/^[\w\s\*\.\?\+\:\_\\\]+$/', $test)) {
        return false;
    }
    $res = false;
    eval('$res = preg_match(\'/^'.$test.'/\', $identifier) ? true : false;');
    return $res;
}

/**
 * Delete Scorm tracks for selected users
 *
 * @param array $attemptids list of attempts that need to be deleted
 * @param int $scorm instance
 *
 * return bool true deleted all responses, false failed deleting an attempt - stopped here
 */
function scorm_delete_responses($attemptids, $scorm) {
    if (!is_array($attemptids) || empty($attemptids)) {
        return false;
    }

    foreach ($attemptids as $num => $attemptid) {
        if (empty($attemptid)) {
            unset($attemptids[$num]);
        }
    }

    foreach ($attemptids as $attempt) {
        $keys = explode(':', $attempt);
        if (count($keys) == 2) {
            $userid = clean_param($keys[0], PARAM_INT);
            $attemptid = clean_param($keys[1], PARAM_INT);
            if (!$userid || !$attemptid || !scorm_delete_attempt($userid, $scorm, $attemptid)) {
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
function scorm_delete_attempt($userid, $scorm, $attemptid) {
    global $DB;

    $DB->delete_records('scorm_scoes_track', array('userid' => $userid, 'scormid' => $scorm->id, 'attempt' => $attemptid));
    include_once('lib.php');
    scorm_update_grades($scorm, $userid, true);
    return true;
}

/**
 * Converts SCORM duration notation to human-readable format
 * The function works with both SCORM 1.2 and SCORM 2004 time formats
 * @param $duration string SCORM duration
 * @return string human-readable date/time
 */
function scorm_format_duration($duration) {
    // fetch date/time strings
    $stryears = get_string('years');
    $strmonths = get_string('nummonths');
    $strdays = get_string('days');
    $strhours = get_string('hours');
    $strminutes = get_string('minutes');
    $strseconds = get_string('seconds');

    if ($duration[0] == 'P') {
        // if timestamp starts with 'P' - it's a SCORM 2004 format
        // this regexp discards empty sections, takes Month/Minute ambiguity into consideration,
        // and outputs filled sections, discarding leading zeroes and any format literals
        // also saves the only zero before seconds decimals (if there are any) and discards decimals if they are zero
        $pattern = array( '#([A-Z])0+Y#', '#([A-Z])0+M#', '#([A-Z])0+D#', '#P(|\d+Y)0*(\d+)M#', '#0*(\d+)Y#', '#0*(\d+)D#', '#P#',
                          '#([A-Z])0+H#', '#([A-Z])[0.]+S#', '#\.0+S#', '#T(|\d+H)0*(\d+)M#', '#0*(\d+)H#', '#0+\.(\d+)S#', '#0*([\d.]+)S#', '#T#' );
        $replace = array( '$1', '$1', '$1', '$1$2 '.$strmonths.' ', '$1 '.$stryears.' ', '$1 '.$strdays.' ', '',
                          '$1', '$1', 'S', '$1$2 '.$strminutes.' ', '$1 '.$strhours.' ', '0.$1 '.$strseconds, '$1 '.$strseconds, '');
    } else {
        // else we have SCORM 1.2 format there
        // first convert the timestamp to some SCORM 2004-like format for conveniency
        $duration = preg_replace('#^(\d+):(\d+):([\d.]+)$#', 'T$1H$2M$3S', $duration);
        // then convert in the same way as SCORM 2004
        $pattern = array( '#T0+H#', '#([A-Z])0+M#', '#([A-Z])[0.]+S#', '#\.0+S#', '#0*(\d+)H#', '#0*(\d+)M#', '#0+\.(\d+)S#', '#0*([\d.]+)S#', '#T#' );
        $replace = array( 'T', '$1', '$1', 'S', '$1 '.$strhours.' ', '$1 '.$strminutes.' ', '0.$1 '.$strseconds, '$1 '.$strseconds, '' );
    }

    $result = preg_replace($pattern, $replace, $duration);

    return $result;
}

function scorm_get_toc_object($user, $scorm, $currentorg='', $scoid='', $mode='normal', $attempt='', $play=false, $organizationsco=null) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    $modestr = '';
    if ($mode == 'browse') {
        $modestr = '&amp;mode='.$mode;
    }

    $result = array();
    $incomplete = false;

    if (!empty($organizationsco)) {
        $result[0] = $organizationsco;
        $result[0]->isvisible = true;
        $result[0]->statusicon = '';
        $result[0]->url = '';
    }

    if ($scoes = scorm_get_scoes($scorm->id, $currentorg)){
        // Retrieve user tracking data for each learning object.
        $usertracks = array();
        foreach ($scoes as $sco) {
            if (!empty($sco->launch)) {
                if ($usertrack = scorm_get_tracks($sco->id, $user->id, $attempt)) {
                    if ($usertrack->status == '') {
                        $usertrack->status = 'notattempted';
                    }
                    $usertracks[$sco->identifier] = $usertrack;
                }
            }
        }
        foreach ($scoes as $sco) {
            if (!isset($sco->isvisible)) {
                $sco->isvisible = true;
            }

            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }

            if (scorm_version_check($scorm->version, SCORM_13)) {
                $sco->prereq = true;
            } else {
                $sco->prereq = empty($sco->prerequisites) || scorm_eval_prerequisites($sco->prerequisites, $usertracks);
            }

            if ($sco->isvisible) {
                if (!empty($sco->launch)) {
                    if (empty($scoid) && ($mode != 'normal')) {
                        $scoid = $sco->id;
                    }

                    if (isset($usertracks[$sco->identifier])) {
                        $usertrack = $usertracks[$sco->identifier];
                        $strstatus = get_string($usertrack->status,'scorm');

                        if ($sco->scormtype == 'sco') {
                            $statusicon = '<img src="'.$OUTPUT->pix_url($usertrack->status, 'scorm').'" alt="'.$strstatus.'" title="'.$strstatus.'" />';
                        } else {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('asset', 'scorm').'" alt="'.get_string('assetlaunched', 'scorm').'" title="'.get_string('assetlaunched', 'scorm').'" />';
                        }

                        if (($usertrack->status == 'notattempted') || ($usertrack->status == 'incomplete') || ($usertrack->status == 'browsed')) {
                            $incomplete = true;
                            if ($play && empty($scoid)) {
                                $scoid = $sco->id;
                            }
                        }

                        $strsuspended = get_string('suspended', 'scorm');

                        $exitvar = 'cmi.core.exit';

                        if (scorm_version_check($scorm->version, SCORM_13)) {
                            $exitvar = 'cmi.exit';
                        }

                        if ($incomplete && isset($usertrack->{$exitvar}) && ($usertrack->{$exitvar} == 'suspend')) {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('suspend', 'scorm').'" alt="'.$strstatus.' - '.$strsuspended.'" title="'.$strstatus.' - '.$strsuspended.'" />';
                        }

                    } else {
                        if ($play && empty($scoid)) {
                            $scoid = $sco->id;
                        }

                        $incomplete = true;

                        if ($sco->scormtype == 'sco') {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('notattempted', 'scorm').'" alt="'.get_string('notattempted', 'scorm').'" title="'.get_string('notattempted', 'scorm').'" />';
                        } else {
                            $statusicon = '<img src="'.$OUTPUT->pix_url('asset', 'scorm').'" alt="'.get_string('asset', 'scorm').'" title="'.get_string('asset', 'scorm').'" />';
                        }
                    }
                }
            }

            if (empty($statusicon)) {
                $sco->statusicon = '<img src="'.$OUTPUT->pix_url('notattempted', 'scorm').'" alt="'.get_string('notattempted', 'scorm').'" title="'.get_string('notattempted', 'scorm').'" />';
            } else {
                $sco->statusicon = $statusicon;
            }

            $sco->url = 'a='.$scorm->id.'&scoid='.$sco->id.'&currentorg='.$currentorg.$modestr.'&attempt='.$attempt;
            $sco->incomplete = $incomplete;

            if (!in_array($sco->id, array_keys($result))) {
                $result[$sco->id] = $sco;
            }
        }
    }

    // Get the parent scoes!
    $result = scorm_get_toc_get_parent_child($result);

    // Be safe, prevent warnings from showing up while returning array
    if (!isset($scoid)) {
        $scoid = '';
    }

    return array('scoes' => $result, 'usertracks' => $usertracks, 'scoid' => $scoid);
}

function scorm_get_toc_get_parent_child(&$result) {
    $final = array();
    $level = 0;
    $prevparent = '/';
    ksort($result);

    foreach ($result as $sco) {
        if ($sco->parent == '/') {
            $final[$level][$sco->identifier] = $sco;
            $prevparent = $sco->identifier;
            unset($result[$sco->id]);
        } else {
            if ($sco->parent == $prevparent) {
                $final[$level][$sco->identifier] = $sco;
                $prevparent = $sco->identifier;
                unset($result[$sco->id]);
            } else {
                if (!empty($final[$level])) {
                    $found = false;
                    foreach ($final[$level] as $fin) {
                        if ($sco->parent == $fin->identifier) {
                            $found = true;
                        }
                    }

                    if ($found) {
                        $final[$level][$sco->identifier] = $sco;
                        unset($result[$sco->id]);
                        $found = false;
                    } else {
                        $level++;
                        $final[$level][$sco->identifier] = $sco;
                        unset($result[$sco->id]);
                    }
                }
            }
        }
    }

    for ($i = 0; $i <= $level; $i++) {
        $prevparent = '';
        foreach ($final[$i] as $ident => $sco) {
            if (empty($prevparent)) {
                $prevparent = $ident;
            }
            if (!isset($final[$i][$prevparent]->children)) {
                $final[$i][$prevparent]->children = array();
            }
            if ($sco->parent == $prevparent) {
                $final[$i][$prevparent]->children[] = $sco;
                $prevparent = $ident;
            } else {
                $parent = false;
                foreach ($final[$i] as $identifier => $scoobj) {
                    if ($identifier == $sco->parent) {
                        $parent = $identifier;
                    }
                }

                if ($parent !== false) {
                    $final[$i][$parent]->children[] = $sco;
                }
            }
        }
    }

    $results = array();
    for ($i = 0; $i <= $level; $i++) {
        $keys = array_keys($final[$i]);
        $results[] = $final[$i][$keys[0]];
    }

    return $results;
}

function scorm_format_toc_for_treeview($user, $scorm, $scoes, $usertracks, $cmid, $toclink=TOCJSLINK, $currentorg='', $attempt='', $play=false, $organizationsco=null, $children=false) {
    global $CFG;

    $result = new stdClass();
    $result->prerequisites = true;
    $result->incomplete = true;

    if (!$children) {
        $result->attemptleft = $scorm->maxattempt == 0 ? 1 : $scorm->maxattempt - $attempt;
    }

    if (!$children) {
        $result->toc = "<ul>\n";

        if (!$play && !empty($organizationsco))  {
            $result->toc .= "\t<li>".$organizationsco->title."</li>\n";
        }
    }

    $prevsco = '';
    if (!empty($scoes)) {
        foreach ($scoes as $sco) {
            $result->toc .= "\t<li>\n";
            $scoid = $sco->id;

            $sco->isvisible = true;

            if ($sco->isvisible) {
                $score = '';

                if (isset($usertracks[$sco->identifier])) {
                    $viewscore = has_capability('mod/scorm:viewscores', context_module::instance($cmid));
                    if (isset($usertracks[$sco->identifier]->score_raw) && $viewscore) {
                        if ($usertracks[$sco->identifier]->score_raw != '') {
                            $score = '('.get_string('score','scorm').':&nbsp;'.$usertracks[$sco->identifier]->score_raw.')';
                        }
                    }
                }

                if (!empty($sco->prereq)) {
                    if ($sco->id == $scoid) {
                        $result->prerequisites = true;
                    }

                    if (!empty($prevsco) && scorm_version_check($scorm->version, SCORM_13) && !empty($prevsco->hidecontinue)) {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= '<span>'.$sco->statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                        } else {
                            $result->toc .= '<span>&nbsp;'.format_string($sco->title).'</span>';
                        }
                    } else if ($toclink == TOCFULLURL) {
                        $url = $CFG->wwwroot.'/mod/scorm/player.php?'.$sco->url;
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= $sco->statusicon.'&nbsp;<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score."\n";
                        } else {
                            $result->toc .= '&nbsp;<a href="'.$url.'">'.format_string($sco->title).'</a>'.$score."\n";
                        }
                    } else {
                        if ($sco->launch) {
                            if ($sco->scormtype == 'sco') {
                                $result->toc .= '<a title="'.$sco->url.'">'.$sco->statusicon.'&nbsp;'.format_string($sco->title).'&nbsp;'.$score.'</a>';
                            } else {
                                $result->toc .= '<a title="'.$sco->url.'">&nbsp;'.format_string($sco->title).'&nbsp;'.$score.'</a>';
                            }
                        } else {
                            if ($sco->scormtype == 'sco') {
                                $result->toc .= '<span>'.$sco->statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                            } else {
                                $result->toc .= '<span>&nbsp;'.format_string($sco->title).'</span>';
                            }
                        }
                    }

                } else {
                    if ($play) {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= '<span>'.$sco->statusicon.'&nbsp;'.format_string($sco->title).'</span>';
                        } else {
                            $result->toc .= '&nbsp;'.format_string($sco->title).'</span>';
                        }
                    } else {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= $sco->statusicon.'&nbsp;'.format_string($sco->title)."\n";
                        } else {
                            $result->toc .= '&nbsp;'.format_string($sco->title)."\n";
                        }
                    }
                }

            } else {
                $result->toc .= "\t\t&nbsp;".format_string($sco->title)."\n";
            }

            if (!empty($sco->children)) {
                $result->toc .= "\n\t\t<ul>\n";
                $childresult = scorm_format_toc_for_treeview($user, $scorm, $sco->children, $usertracks, $cmid, $toclink, $currentorg, $attempt, $play, $organizationsco, true);
                $result->toc .= $childresult->toc;
                $result->toc .= "\t\t</ul>\n";
                $result->toc .= "\t</li>\n";
            } else {
                $result->toc .= "\t</li>\n";
            }
            $prevsco = $sco;
        }
        $result->incomplete = $sco->incomplete;
    }

    if (!$children) {
        $result->toc .= "</ul>\n";
    }

    return $result;
}

function scorm_format_toc_for_droplist($scorm, $scoes, $usertracks, $currentorg='', $organizationsco=null, $children=false, $level=0, $tocmenus=array()) {
    if (!empty($scoes)) {
        if (!empty($organizationsco) && !$children) {
            $tocmenus[$organizationsco->id] = $organizationsco->title;
        }

        $parents[$level]='/';
        foreach ($scoes as $sco) {
            if ($parents[$level] != $sco->parent) {
                if ($newlevel = array_search($sco->parent, $parents)) {
                    $level = $newlevel;
                } else {
                    $i = $level;
                    while (($i > 0) && ($parents[$level] != $sco->parent)) {
                        $i--;
                    }

                    if (($i == 0) && ($sco->parent != $currentorg)) {
                        $level++;
                    } else {
                        $level = $i;
                    }

                    $parents[$level] = $sco->parent;
                }
            }

            if ($sco->prereq) {
                if ($sco->scormtype == 'sco') {
                    $tocmenus[$sco->id] = scorm_repeater('&minus;', $level) . '&gt;' . format_string($sco->title);
                }
            } else {
                if ($sco->scormtype == 'sco') {
                    $tocmenus[$sco->id] = scorm_repeater('&minus;', $level) . '&gt;' . format_string($sco->title);
                }
            }

            if (!empty($sco->children)) {
                $tocmenus = scorm_format_toc_for_droplist($scorm, $sco->children, $usertracks, $currentorg, $organizationsco, true, $level, $tocmenus);
            }
        }
    }

    return $tocmenus;
}

function scorm_get_toc($user, $scorm, $cmid, $toclink=TOCJSLINK, $currentorg='', $scoid='', $mode='normal', $attempt='', $play=false, $tocheader=false) {
    global $CFG, $DB, $OUTPUT;

    if (empty($attempt)) {
        $attempt = scorm_get_attempt_count($user->id, $scorm);
    }

    $result = new stdClass();
    $organizationsco = null;

    if ($tocheader) {
        $result->toc = "<div id=\"scorm_layout\">\n";
        $result->toc .= "<div id=\"scorm_toc\">\n";
        $result->toc .= "<div id=\"scorm_tree\">\n";
    }

    if (!empty($currentorg)) {
        $organizationsco = $DB->get_record('scorm_scoes', array('scorm'=>$scorm->id, 'identifier'=>$currentorg));
        if (!empty($organizationsco->title)) {
            if ($play) {
                $result->toctitle = $organizationsco->title;
            }
        }
    }

    $scoes = scorm_get_toc_object($user, $scorm, $currentorg, $scoid, $mode, $attempt, $play, $organizationsco);

    $treeview = scorm_format_toc_for_treeview($user, $scorm, $scoes['scoes'][0]->children, $scoes['usertracks'], $cmid, $toclink, $currentorg, $attempt, $play, $organizationsco, false);

    if ($tocheader) {
        $result->toc .= $treeview->toc;
    } else {
        $result->toc = $treeview->toc;
    }

    if (!empty($scoes['scoid'])) {
        $scoid = $scoes['scoid'];
    }

    if (empty($scoid)) {
        $result->sco = $scoes['scoes'][0]->children;
    } else {
        $result->sco = scorm_get_sco($scoid);
    }

    if ($scorm->hidetoc == SCORM_TOC_POPUP) {
        $tocmenu = scorm_format_toc_for_droplist($scorm, $scoes['scoes'][0]->children, $scoes['usertracks'], $currentorg, $organizationsco);

        $modestr = '';
        if ($mode == 'browse') {
            $modestr = '&amp;mode='.$mode;
        }

        $url = new moodle_url('/mod/scorm/player.php?a='.$scorm->id.'&currentorg='.$currentorg.$modestr);
        $result->tocmenu = $OUTPUT->single_select($url, 'scoid', $tocmenu, $result->sco->id, null, "tocmenu");
    }

    $result->prerequisites = $treeview->prerequisites;
    $result->incomplete = $treeview->incomplete;
    $result->attemptleft = $treeview->attemptleft;

    if ($tocheader) {
        $result->toc .= "</div></div></div>\n";
        $result->toc .= "<div id=\"scorm_navpanel\"></div>\n";
    }

    return $result;
}

function scorm_get_adlnav_json ($scoes, &$adlnav = array(), $parentscoid = null) {
    if (is_object($scoes)) {
        $sco = $scoes;
        if (isset($sco->url)) {
            $adlnav[$sco->id]['identifier'] = $sco->identifier;
            $adlnav[$sco->id]['launch'] = $sco->launch;
            $adlnav[$sco->id]['title'] = $sco->title;
            $adlnav[$sco->id]['url'] = $sco->url;
            $adlnav[$sco->id]['parent'] = $sco->parent;
            if (isset($sco->choice)) {
                $adlnav[$sco->id]['choice'] = $sco->choice;
            }
            if (isset($sco->flow)) {
                $adlnav[$sco->id]['flow'] = $sco->flow;
            } else if (isset($parentscoid) && isset($adlnav[$parentscoid]['flow'])) {
                $adlnav[$sco->id]['flow'] = $adlnav[$parentscoid]['flow'];
            }
            if (isset($sco->isvisible)) {
                $adlnav[$sco->id]['isvisible'] = $sco->isvisible;
            }
            if (isset($sco->parameters)) {
                $adlnav[$sco->id]['parameters'] = $sco->parameters;
            }
            if (isset($sco->hidecontinue)) {
                $adlnav[$sco->id]['hidecontinue'] = $sco->hidecontinue;
            }
            if (isset($sco->hideprevious)) {
                $adlnav[$sco->id]['hideprevious'] = $sco->hideprevious;
            }
            if (isset($sco->hidesuspendall)) {
                $adlnav[$sco->id]['hidesuspendall'] = $sco->hidesuspendall;
            }
            if (!empty($parentscoid)) {
                $adlnav[$sco->id]['parentscoid'] = $parentscoid;
            }
            if (isset($adlnav['prevscoid'])) {
                $adlnav[$sco->id]['prevscoid'] = $adlnav['prevscoid'];
                $adlnav[$adlnav['prevscoid']]['nextscoid'] = $sco->id;
                if (isset($adlnav['prevparent']) && $adlnav['prevparent'] == $sco->parent) {
                    $adlnav[$sco->id]['prevsibling'] = $adlnav['prevscoid'];
                    $adlnav[$adlnav['prevscoid']]['nextsibling'] = $sco->id;
                }
            }
            $adlnav['prevscoid'] = $sco->id;
            $adlnav['prevparent'] = $sco->parent;
        }
        if (isset($sco->children)) {
            foreach ($sco->children as $children) {
                scorm_get_adlnav_json($children, $adlnav, $sco->id);
            }
        }
    } else {
        foreach ($scoes as $sco) {
            scorm_get_adlnav_json ($sco, $adlnav);
        }
        unset($adlnav['prevscoid']);
        unset($adlnav['prevparent']);
    }
    return json_encode($adlnav);
}

/**
 * Check for the availability of a resource by URL.
 *
 * Check is performed using an HTTP HEAD call.
 *
 * @param $url string A valid URL
 * @return bool|string True if no issue is found. The error string message, otherwise
 */
function scorm_check_url($url) {
    $curl = new curl;

    if (!ini_get('open_basedir') and !ini_get('safe_mode')) {
        // Same options as in {@link download_file_content()}, used in {@link scorm_parse_scorm()}.
        $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => true, 'CURLOPT_MAXREDIRS' => 5));
    }
    $cmsg = $curl->head($url);
    $info = $curl->get_info();
    if (empty($info['http_code']) || $info['http_code'] != 200) {
        return get_string('invalidurlhttpcheck', 'scorm', array('cmsg' => $cmsg));
    }

    return true;
}
