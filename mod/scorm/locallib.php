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

/**
 * Library of internal classes and functions for module SCORM
 *
 * @package    mod_scorm
 * @copyright  1999 onwards Roberto Pinna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/mod/scorm/lib.php");
require_once("$CFG->libdir/filelib.php");

// Constants and settings for module scorm.
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

// Local Library of functions for module scorm.

/**
 * @package   mod_scorm
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
    $cfgscorm = get_config('scorm');

    return array('scrollbars' => isset($cfgscorm->scrollbars) ? $cfgscorm->scrollbars : 0,
                 'directories' => isset($cfgscorm->directories) ? $cfgscorm->directories : 0,
                 'location' => isset($cfgscorm->location) ? $cfgscorm->location : 0,
                 'menubar' => isset($cfgscorm->menubar) ? $cfgscorm->menubar : 0,
                 'toolbar' => isset($cfgscorm->toolbar) ? $cfgscorm->toolbar : 0,
                 'status' => isset($cfgscorm->status) ? $cfgscorm->status : 0);
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
 * Returns an array of the array of navigation buttons display options
 *
 * @return array an array of navigation buttons display options
 */
function scorm_get_navigation_display_array() {
    return array(SCORM_NAV_DISABLED => get_string('no'),
                 SCORM_NAV_UNDER_CONTENT => get_string('undercontent', 'scorm'),
                 SCORM_NAV_FLOATING => get_string('floating', 'scorm'));
}

/**
 * Returns an array of the array of attempt options
 *
 * @return array an array of attempt options
 */
function scorm_get_attempts_array() {
    $attempts = array(0 => get_string('nolimit', 'scorm'),
                      1 => get_string('attempt1', 'scorm'));

    for ($i = 2; $i <= 6; $i++) {
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
    $cfgscorm = get_config('scorm');

    if (!isset($scorm->cmid)) {
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);
        $scorm->cmid = $cm->id;
    }
    $context = context_module::instance($scorm->cmid);
    $newhash = $scorm->sha1hash;

    if ($scorm->scormtype === SCORM_TYPE_LOCAL or $scorm->scormtype === SCORM_TYPE_LOCALSYNC) {

        $fs = get_file_storage();
        $packagefile = false;
        $packagefileimsmanifest = false;

        if ($scorm->scormtype === SCORM_TYPE_LOCAL) {
            if ($packagefile = $fs->get_file($context->id, 'mod_scorm', 'package', 0, '/', $scorm->reference)) {
                if ($packagefile->is_external_file()) { // Get zip file so we can check it is correct.
                    $packagefile->import_external_file_contents();
                }
                $newhash = $packagefile->get_contenthash();
                if (strtolower($packagefile->get_filename()) == 'imsmanifest.xml') {
                    $packagefileimsmanifest = true;
                }
            } else {
                $newhash = null;
            }
        } else {
            if (!$cfgscorm->allowtypelocalsync) {
                // Sorry - localsync disabled.
                return;
            }
            if ($scorm->reference !== '') {
                $fs->delete_area_files($context->id, 'mod_scorm', 'package');
                $filerecord = array('contextid' => $context->id, 'component' => 'mod_scorm', 'filearea' => 'package',
                                    'itemid' => 0, 'filepath' => '/');
                if ($packagefile = $fs->create_file_from_url($filerecord, $scorm->reference, array('calctimeout' => true), true)) {
                    $newhash = $packagefile->get_contenthash();
                } else {
                    $newhash = null;
                }
            }
        }

        if ($packagefile) {
            if (!$full and $packagefile and $scorm->sha1hash === $newhash) {
                if (strpos($scorm->version, 'SCORM') !== false) {
                    if ($packagefileimsmanifest || $fs->get_file($context->id, 'mod_scorm', 'content', 0, '/', 'imsmanifest.xml')) {
                        // No need to update.
                        return;
                    }
                } else if (strpos($scorm->version, 'AICC') !== false) {
                    // TODO: add more sanity checks - something really exists in scorm_content area.
                    return;
                }
            }
            if (!$packagefileimsmanifest) {
                // Now extract files.
                $fs->delete_area_files($context->id, 'mod_scorm', 'content');

                $packer = get_file_packer('application/zip');
                $packagefile->extract_to_storage($packer, $context->id, 'mod_scorm', 'content', 0, '/');
            }

        } else if (!$full) {
            return;
        }
        if ($packagefileimsmanifest) {
            require_once("$CFG->dirroot/mod/scorm/datamodels/scormlib.php");
            // Direct link to imsmanifest.xml file.
            if (!scorm_parse_scorm($scorm, $packagefile)) {
                $scorm->version = 'ERROR';
            }

        } else if ($manifest = $fs->get_file($context->id, 'mod_scorm', 'content', 0, '/', 'imsmanifest.xml')) {
            require_once("$CFG->dirroot/mod/scorm/datamodels/scormlib.php");
            // SCORM.
            if (!scorm_parse_scorm($scorm, $manifest)) {
                $scorm->version = 'ERROR';
            }
        } else {
            require_once("$CFG->dirroot/mod/scorm/datamodels/aicclib.php");
            // AICC.
            $result = scorm_parse_aicc($scorm);
            if (!$result) {
                $scorm->version = 'ERROR';
            } else {
                $scorm->version = 'AICC';
            }
        }

    } else if ($scorm->scormtype === SCORM_TYPE_EXTERNAL and $cfgscorm->allowtypeexternal) {
        require_once("$CFG->dirroot/mod/scorm/datamodels/scormlib.php");
        // SCORM only, AICC can not be external.
        if (!scorm_parse_scorm($scorm, $scorm->reference)) {
            $scorm->version = 'ERROR';
        }
        $newhash = sha1($scorm->reference);

    } else if ($scorm->scormtype === SCORM_TYPE_AICCURL  and $cfgscorm->allowtypeexternalaicc) {
        require_once("$CFG->dirroot/mod/scorm/datamodels/aicclib.php");
        // AICC.
        $result = scorm_parse_aicc($scorm);
        if (!$result) {
            $scorm->version = 'ERROR';
        } else {
            $scorm->version = 'AICC';
        }

    } else {
        // Sorry, disabled type.
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
    for ($i = 0; $i < $times; $i++) {
        $return .= $what;
    }
    return $return;
}

function scorm_external_link($link) {
    // Check if a link is external.
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

    if ($sco = $DB->get_record('scorm_scoes', array('id' => $id))) {
        $sco = ($what == SCO_DATA) ? new stdClass() : $sco;
        if (($what != SCO_ONLY) && ($scodatas = $DB->get_records('scorm_scoes_data', array('scoid' => $id)))) {
            foreach ($scodatas as $scodata) {
                $sco->{$scodata->name} = $scodata->value;
            }
        } else if (($what != SCO_ONLY) && (!($scodatas = $DB->get_records('scorm_scoes_data', array('scoid' => $id))))) {
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

    $queryarray = array('scorm' => $id);
    if (!empty($organisation)) {
        $queryarray['organization'] = $organisation;
    }
    if ($scoes = $DB->get_records('scorm_scoes', $queryarray, 'sortorder, id')) {
        // Drop keys so that it is a simple array as expected.
        $scoes = array_values($scoes);
        foreach ($scoes as $sco) {
            if ($scodatas = $DB->get_records('scorm_scoes_data', array('scoid' => $sco->id))) {
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

function scorm_insert_track($userid, $scormid, $scoid, $attempt, $element, $value, $forcecompleted=false, $trackdata = null) {
    global $DB, $CFG;

    $id = null;

    if ($forcecompleted) {
        // TODO - this could be broadened to encompass SCORM 2004 in future.
        if (($element == 'cmi.core.lesson_status') && ($value == 'incomplete')) {
            if ($track = $DB->get_record_select('scorm_scoes_track',
                                                'userid=? AND scormid=? AND scoid=? AND attempt=? '.
                                                'AND element=\'cmi.core.score.raw\'',
                                                array($userid, $scormid, $scoid, $attempt))) {
                $value = 'completed';
            }
        }
        if ($element == 'cmi.core.score.raw') {
            if ($tracktest = $DB->get_record_select('scorm_scoes_track',
                                                    'userid=? AND scormid=? AND scoid=? AND attempt=? '.
                                                    'AND element=\'cmi.core.lesson_status\'',
                                                    array($userid, $scormid, $scoid, $attempt))) {
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
                    }
                }
            }
        }

    }

    $track = null;
    if ($trackdata !== null) {
        if (isset($trackdata[$element])) {
            $track = $trackdata[$element];
        }
    } else {
        $track = $DB->get_record('scorm_scoes_track', array('userid' => $userid,
                                                            'scormid' => $scormid,
                                                            'scoid' => $scoid,
                                                            'attempt' => $attempt,
                                                            'element' => $element));
    }
    if ($track) {
        if ($element != 'x.start.time' ) { // Don't update x.start.time - keep the original value.
            if ($track->value != $value) {
                $track->value = $value;
                $track->timemodified = time();
                $DB->update_record('scorm_scoes_track', $track);
            }
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
    // Gets all tracks of specified sco and user.
    global $DB;

    if (empty($attempt)) {
        if ($scormid = $DB->get_field('scorm_scoes', 'scorm', array('id' => $scoid))) {
            $attempt = scorm_get_last_attempt($scormid, $userid);
        } else {
            $attempt = 1;
        }
    }
    if ($tracks = $DB->get_records('scorm_scoes_track', array('userid' => $userid, 'scoid' => $scoid,
                                                              'attempt' => $attempt), 'element ASC')) {
        $usertrack = scorm_format_interactions($tracks);
        $usertrack->userid = $userid;
        $usertrack->scoid = $scoid;

        return $usertrack;
    } else {
        return false;
    }
}
/**
 * helper function to return a formatted list of interactions for reports.
 *
 * @param array $trackdata the records from scorm_scoes_track table
 * @return object formatted list of interactions
 */
function scorm_format_interactions($trackdata) {
    $usertrack = new stdClass();

    // Defined in order to unify scorm1.2 and scorm2004.
    $usertrack->score_raw = '';
    $usertrack->status = '';
    $usertrack->total_time = '00:00:00';
    $usertrack->session_time = '00:00:00';
    $usertrack->timemodified = 0;

    foreach ($trackdata as $track) {
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

    return $usertrack;
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
    $params = array('userid' => $userid, 'scormid' => $scormid, 'attempt' => $attempt);
    if (!empty($scoid)) {
        $params['scoid'] = $scoid;
    }
    $tracks = $DB->get_records('scorm_scoes_track', $params, "timemodified ASC");
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

function scorm_grade_user_attempt($scorm, $userid, $attempt=1) {
    global $DB;
    $attemptscore = new stdClass();
    $attemptscore->scoes = 0;
    $attemptscore->values = 0;
    $attemptscore->max = 0;
    $attemptscore->sum = 0;
    $attemptscore->lastmodify = 0;

    if (!$scoes = $DB->get_records('scorm_scoes', array('scorm' => $scorm->id), 'sortorder, id')) {
        return null;
    }

    foreach ($scoes as $sco) {
        if ($userdata = scorm_get_tracks($sco->id, $userid, $attempt)) {
            if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                $attemptscore->scoes++;
            }
            if (!empty($userdata->score_raw) || (isset($scorm->type) && $scorm->type == 'sco' && isset($userdata->score_raw))) {
                $attemptscore->values++;
                $attemptscore->sum += $userdata->score_raw;
                $attemptscore->max = ($userdata->score_raw > $attemptscore->max) ? $userdata->score_raw : $attemptscore->max;
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
                $score = $attemptscore->sum / $attemptscore->values;
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
            $score = $attemptscore->max;   // Remote Learner GRADEHIGHEST is default.
    }

    return $score;
}

function scorm_grade_user($scorm, $userid) {

    // Ensure we dont grade user beyond $scorm->maxattempt settings.
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
                $maxscore = $attemptscore > $maxscore ? $attemptscore : $maxscore;
            }
            return $maxscore;

        break;
        case AVERAGEATTEMPT:
            $attemptcount = scorm_get_attempt_count($userid, $scorm, true, true);
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
    return $DB->count_records_select('scorm_scoes', "scorm = ? $sqlorganization AND ".
                                        $DB->sql_isnotempty('scorm_scoes', 'launch', false, true),
                                        $params);
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

    // Find the last attempt number for the given user id and scorm id.
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

    // Find the last completed attempt number for the given user id and scorm id.
    $sql = "SELECT MAX(attempt)
              FROM {scorm_scoes_track}
             WHERE userid = ? AND scormid = ?
               AND (".$DB->sql_compare_text('value')." = ".$DB->sql_compare_text('?')." OR ".
                      $DB->sql_compare_text('value')." = ".$DB->sql_compare_text('?').")";
    $lastattempt = $DB->get_field_sql($sql, array($userid, $scormid, 'completed', 'passed'));
    if (empty($lastattempt)) {
        return '1';
    } else {
        return $lastattempt;
    }
}

/**
 * Returns the full list of attempts a user has made.
 *
 * @param int $scormid the id of the scorm.
 * @param int $userid the id of the user.
 *
 * @return array array of attemptids
 */
function scorm_get_all_attempts($scormid, $userid) {
    global $DB;
    $attemptids = array();
    $sql = "SELECT DISTINCT attempt FROM {scorm_scoes_track} WHERE userid = ? AND scormid = ? ORDER BY attempt";
    $attempts = $DB->get_records_sql($sql, array($userid, $scormid));
    foreach ($attempts as $attempt) {
        $attemptids[] = $attempt->attempt;
    }
    return $attemptids;
}

function scorm_view_display ($user, $scorm, $action, $cm) {
    global $CFG, $DB, $PAGE, $OUTPUT, $COURSE;

    if ($scorm->updatefreq == SCORM_UPDATE_EVERYTIME) {
        scorm_parse($scorm, false);
    }

    $organization = optional_param('organization', '', PARAM_INT);

    if ($scorm->displaycoursestructure == 1) {
        echo $OUTPUT->box_start('generalbox boxaligncenter toc', 'toc');
        echo html_writer::div(get_string('contents', 'scorm'), 'structurehead');
    }
    if (empty($organization)) {
        $organization = $scorm->launch;
    }
    if ($orgs = $DB->get_records_select_menu('scorm_scoes', 'scorm = ? AND '.
                                         $DB->sql_isempty('scorm_scoes', 'launch', false, true).' AND '.
                                         $DB->sql_isempty('scorm_scoes', 'organization', false, false),
                                         array($scorm->id), 'sortorder, id', 'id,title')) {
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

    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe.
    if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
        $scorm->version = 'scorm_12';
    }
    require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');

    $result = scorm_get_toc($user, $scorm, $cm->id, TOCFULLURL, $orgidentifier);
    $incomplete = $result->incomplete;

    // Do we want the TOC to be displayed?
    if ($scorm->displaycoursestructure == 1) {
        echo $result->toc;
        echo $OUTPUT->box_end();
    }

    // Is this the first attempt ?
    $attemptcount = scorm_get_attempt_count($user->id, $scorm);

    // Do not give the player launch FORM if the SCORM object is locked after the final attempt.
    if ($scorm->lastattemptlock == 0 || $result->attemptleft > 0) {
            echo html_writer::start_div('scorm-center');
            echo html_writer::start_tag('form', array('id' => 'scormviewform',
                                                        'method' => 'post',
                                                        'action' => $CFG->wwwroot.'/mod/scorm/player.php'));
        if ($scorm->hidebrowse == 0) {
            print_string('mode', 'scorm');
            echo ': '.html_writer::empty_tag('input', array('type' => 'radio', 'id' => 'b', 'name' => 'mode', 'value' => 'browse')).
                        html_writer::label(get_string('browse', 'scorm'), 'b');
            echo html_writer::empty_tag('input', array('type' => 'radio',
                                                        'id' => 'n', 'name' => 'mode',
                                                        'value' => 'normal', 'checked' => 'checked')).
                    html_writer::label(get_string('normal', 'scorm'), 'n');

        } else {
            echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'mode', 'value' => 'normal'));
        }
        if ($scorm->forcenewattempt == 1) {
            if ($incomplete === false) {
                echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'newattempt', 'value' => 'on'));
            }
        } else if (!empty($attemptcount) && ($incomplete === false) && (($result->attemptleft > 0)||($scorm->maxattempt == 0))) {
                echo html_writer::empty_tag('br');
                echo html_writer::checkbox('newattempt', 'on', false, '', array('id' => 'a'));
                echo html_writer::label(get_string('newattempt', 'scorm'), 'a');
        }
        if (!empty($scorm->popup)) {
            echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'display', 'value' => 'popup'));
        }

        echo html_writer::empty_tag('br');
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'scoid', 'value' => $scorm->launch));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'cm', 'value' => $cm->id));
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'currentorg', 'value' => $orgidentifier));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('enter', 'scorm')));
        echo html_writer::end_tag('form');
        echo html_writer::end_div();
    }
}

function scorm_simple_play($scorm, $user, $context, $cmid) {
    global $DB;

    $result = false;

    if (has_capability('mod/scorm:viewreport', $context)) {
        // If this user can view reports, don't skipview so they can see links to reports.
        return $result;
    }

    if ($scorm->updatefreq == SCORM_UPDATE_EVERYTIME) {
        scorm_parse($scorm, false);
    }
    $scoes = $DB->get_records_select('scorm_scoes', 'scorm = ? AND '.
        $DB->sql_isnotempty('scorm_scoes', 'launch', false, true), array($scorm->id), 'sortorder, id', 'id');

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
                                                                'currentorg' => $orgidentifier,
                                                                'scoid' => $sco->id));
            if ($scorm->skipview == SCORM_SKIPVIEW_ALWAYS || !scorm_has_tracks($scorm->id, $user->id)) {
                if (!empty($scorm->forcenewattempt)) {
                    $result = scorm_get_toc($user, $scorm, $cmid, TOCFULLURL, $orgidentifier);
                    if ($result->incomplete === false) {
                        $url->param('newattempt', 'on');
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
 * @param string $elementname Name of array element to get values for
 * @param array $children list of sub elements of this array element that also need instantiating
 * @return Javascript array elements
 */
function scorm_reconstitute_array_element($sversion, $userdata, $elementname, $children) {
    // Reconstitute comments_from_learner and comments_from_lms.
    $current = '';
    $currentsubelement = '';
    $currentsub = '';
    $count = 0;
    $countsub = 0;
    $scormseperator = '_';
    $return = '';
    if (scorm_version_check($sversion, SCORM_13)) { // Scorm 1.3 elements use a . instead of an _ .
        $scormseperator = '.';
    }
    // Filter out the ones we want.
    $elementlist = array();
    foreach ($userdata as $element => $value) {
        if (substr($element, 0, strlen($elementname)) == $elementname) {
            $elementlist[$element] = $value;
        }
    }

    // Sort elements in .n array order.
    uksort($elementlist, "scorm_element_cmp");

    // Generate JavaScript.
    foreach ($elementlist as $element => $value) {
        if (scorm_version_check($sversion, SCORM_13)) {
            $element = preg_replace('/\.(\d+)\./', ".N\$1.", $element);
            preg_match('/\.(N\d+)\./', $element, $matches);
        } else {
            $element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
            preg_match('/\_(\d+)\./', $element, $matches);
        }
        if (count($matches) > 0 && $current != $matches[1]) {
            if ($countsub > 0) {
                $return .= '    '.$elementname.$scormseperator.$current.'.'.$currentsubelement.'._count = '.$countsub.";\n";
            }
            $current = $matches[1];
            $count++;
            $currentsubelement = '';
            $currentsub = '';
            $countsub = 0;
            $end = strpos($element, $matches[1]) + strlen($matches[1]);
            $subelement = substr($element, 0, $end);
            $return .= '    '.$subelement." = new Object();\n";
            // Now add the children.
            foreach ($children as $child) {
                $return .= '    '.$subelement.".".$child." = new Object();\n";
                $return .= '    '.$subelement.".".$child."._children = ".$child."_children;\n";
            }
        }

        // Now - flesh out the second level elements if there are any.
        if (scorm_version_check($sversion, SCORM_13)) {
            $element = preg_replace('/(.*?\.N\d+\..*?)\.(\d+)\./', "\$1.N\$2.", $element);
            preg_match('/.*?\.N\d+\.(.*?)\.(N\d+)\./', $element, $matches);
        } else {
            $element = preg_replace('/(.*?\_\d+\..*?)\.(\d+)\./', "\$1_\$2.", $element);
            preg_match('/.*?\_\d+\.(.*?)\_(\d+)\./', $element, $matches);
        }

        // Check the sub element type.
        if (count($matches) > 0 && $currentsubelement != $matches[1]) {
            if ($countsub > 0) {
                $return .= '    '.$elementname.$scormseperator.$current.'.'.$currentsubelement.'._count = '.$countsub.";\n";
            }
            $currentsubelement = $matches[1];
            $currentsub = '';
            $countsub = 0;
            $end = strpos($element, $matches[1]) + strlen($matches[1]);
            $subelement = substr($element, 0, $end);
            $return .= '    '.$subelement." = new Object();\n";
        }

        // Now check the subelement subscript.
        if (count($matches) > 0 && $currentsub != $matches[2]) {
            $currentsub = $matches[2];
            $countsub++;
            $end = strrpos($element, $matches[2]) + strlen($matches[2]);
            $subelement = substr($element, 0, $end);
            $return .= '    '.$subelement." = new Object();\n";
        }

        $return .= '    '.$element.' = '.json_encode($value).";\n";
    }
    if ($countsub > 0) {
        $return .= '    '.$elementname.$scormseperator.$current.'.'.$currentsubelement.'._count = '.$countsub.";\n";
    }
    if ($count > 0) {
        $return .= '    '.$elementname.'._count = '.$count.";\n";
    }
    return $return;
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
        return -1; // Smaller.
    } else if ($left > $right) {
        return 1;  // Bigger.
    } else {
        // Look for a second level qualifier eg cmi.interactions_0.correct_responses_0.pattern.
        if (preg_match('/.*?(\d+)\.(.*?)\.(\d+)\./', $a, $matches)) {
            $leftterm = intval($matches[2]);
            $left = intval($matches[3]);
            if (preg_match('/.*?(\d+)\.(.*?)\.(\d+)\./', $b, $matches)) {
                $rightterm = intval($matches[2]);
                $right = intval($matches[3]);
                if ($leftterm < $rightterm) {
                    return -1; // Smaller.
                } else if ($leftterm > $rightterm) {
                    return 1;  // Bigger.
                } else {
                    if ($left < $right) {
                        return -1; // Smaller.
                    } else if ($left > $right) {
                        return 1;  // Bigger.
                    }
                }
            }
        }
        // Fall back for no second level matches or second level matches are equal.
        return 0;  // Equal to.
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

    $result = html_writer::start_tag('p').get_string('noattemptsallowed', 'scorm').': ';
    if ($scorm->maxattempt > 0) {
        $result .= $scorm->maxattempt . html_writer::empty_tag('br');
    } else {
        $result .= get_string('unlimited').html_writer::empty_tag('br');
    }
    $result .= get_string('noattemptsmade', 'scorm').': ' . $attemptcount . html_writer::empty_tag('br');

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
                $gradereported = $gradereported / $scorm->maxgrade;
                $gradereported = number_format($gradereported * 100, 0) .'%';
            }
            $result .= get_string('gradeforattempt', 'scorm').' ' . $i . ': ' . $gradereported .html_writer::empty_tag('br');
            $i++;
        }
    }
    $calculatedgrade = scorm_grade_user($scorm, $user->id);
    if ($scorm->grademethod !== GRADESCOES && !empty($scorm->maxgrade)) {
        $calculatedgrade = $calculatedgrade / $scorm->maxgrade;
        $calculatedgrade = number_format($calculatedgrade * 100, 0) .'%';
    }
    $result .= get_string('grademethod', 'scorm'). ': ' . $grademethod;
    if (empty($attempts)) {
        $result .= html_writer::empty_tag('br').get_string('gradereported', 'scorm').
                    ': '.get_string('none').html_writer::empty_tag('br');
    } else {
        $result .= html_writer::empty_tag('br').get_string('gradereported', 'scorm').
                    ': '.$calculatedgrade.html_writer::empty_tag('br');
    }
    $result .= html_writer::end_tag('p');
    if ($attemptcount >= $scorm->maxattempt and $scorm->maxattempt > 0) {
        $result .= html_writer::tag('p', get_string('exceededmaxattempts', 'scorm'), array('class' => 'exceededmaxattempts'));
    }
    if (!empty($cm)) {
        $context = context_module::instance($cm->id);
        if (has_capability('mod/scorm:deleteownresponses', $context) &&
            $DB->record_exists('scorm_scoes_track', array('userid' => $user->id, 'scormid' => $scorm->id))) {
            // Check to see if any data is stored for this user.
            $deleteurl = new moodle_url($PAGE->url, array('action' => 'delete', 'sesskey' => sesskey()));
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
 * @param bool $returnobjects if true returns a object with attempts, if false returns count of attempts.
 * @param bool $ignoremissingcompletion - ignores attempts that haven't reported a grade/completion.
 * @return int - no. of attempts so far
 */
function scorm_get_attempt_count($userid, $scorm, $returnobjects = false, $ignoremissingcompletion = false) {
    global $DB;

    // Historically attempts that don't report these elements haven't been included in the average attempts grading method
    // we may want to change this in future, but to avoid unexpected grade decreases we're leaving this in. MDL-43222 .
    if (scorm_version_check($scorm->version, SCORM_13)) {
        $element = 'cmi.score.raw';
    } else if ($scorm->grademethod == GRADESCOES) {
        $element = 'cmi.core.lesson_status';
    } else {
        $element = 'cmi.core.score.raw';
    }

    if ($returnobjects) {
        $params = array('userid' => $userid, 'scormid' => $scorm->id);
        if ($ignoremissingcompletion) { // Exclude attempts that don't have the completion element requested.
            $params['element'] = $element;
        }
        $attempts = $DB->get_records('scorm_scoes_track', $params, 'attempt', 'DISTINCT attempt AS attemptnumber');
        return $attempts;
    } else {
        $params = array($userid, $scorm->id);
        $sql = "SELECT COUNT(DISTINCT attempt)
                  FROM {scorm_scoes_track}
                 WHERE userid = ? AND scormid = ?";
        if ($ignoremissingcompletion) { // Exclude attempts that don't have the completion element requested.
            $sql .= ' AND element = ?';
            $params[] = $element;
        }

        $attemptscount = $DB->count_records_sql($sql, $params);
        return $attemptscount;
    }
}

/**
 * Figure out with this is a debug situation
 *
 * @param object $scorm a moodle scrom object - mdl_scorm
 * @return boolean - debugging true/false
 */
function scorm_debugging($scorm) {
    global $USER;
    $cfgscorm = get_config('scorm');

    if (!$cfgscorm->allowapidebug) {
        return false;
    }
    $identifier = $USER->username.':'.$scorm->name;
    $test = $cfgscorm->apidebugmask;
    // Check the regex is only a short list of safe characters.
    if (!preg_match('/^[\w\s\*\.\?\+\:\_\\\]+$/', $test)) {
        return false;
    }

    if (preg_match('/^'.$test.'/', $identifier)) {
        return true;
    }
    return false;
}

/**
 * Delete Scorm tracks for selected users
 *
 * @param array $attemptids list of attempts that need to be deleted
 * @param stdClass $scorm instance
 *
 * @return bool true deleted all responses, false failed deleting an attempt - stopped here
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
 * @param stdClass $scorm Scorm object
 * @param int $attemptid user attempt that need to be deleted
 *
 * @return bool true suceeded
 */
function scorm_delete_attempt($userid, $scorm, $attemptid) {
    global $DB;

    $DB->delete_records('scorm_scoes_track', array('userid' => $userid, 'scormid' => $scorm->id, 'attempt' => $attemptid));
    $cm = get_coursemodule_from_instance('scorm', $scorm->id);

    // Trigger instances list viewed event.
    $event = \mod_scorm\event\attempt_deleted::create(array(
         'other' => array('attemptid' => $attemptid),
         'context' => context_module::instance($cm->id),
         'relateduserid' => $userid
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('scorm', $scorm);
    $event->trigger();

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
    // Fetch date/time strings.
    $stryears = get_string('years');
    $strmonths = get_string('nummonths');
    $strdays = get_string('days');
    $strhours = get_string('hours');
    $strminutes = get_string('minutes');
    $strseconds = get_string('seconds');

    if ($duration[0] == 'P') {
        // If timestamp starts with 'P' - it's a SCORM 2004 format
        // this regexp discards empty sections, takes Month/Minute ambiguity into consideration,
        // and outputs filled sections, discarding leading zeroes and any format literals
        // also saves the only zero before seconds decimals (if there are any) and discards decimals if they are zero.
        $pattern = array( '#([A-Z])0+Y#', '#([A-Z])0+M#', '#([A-Z])0+D#', '#P(|\d+Y)0*(\d+)M#',
                            '#0*(\d+)Y#', '#0*(\d+)D#', '#P#', '#([A-Z])0+H#', '#([A-Z])[0.]+S#',
                            '#\.0+S#', '#T(|\d+H)0*(\d+)M#', '#0*(\d+)H#', '#0+\.(\d+)S#',
                            '#0*([\d.]+)S#', '#T#' );
        $replace = array( '$1', '$1', '$1', '$1$2 '.$strmonths.' ', '$1 '.$stryears.' ', '$1 '.$strdays.' ',
                            '', '$1', '$1', 'S', '$1$2 '.$strminutes.' ', '$1 '.$strhours.' ',
                            '0.$1 '.$strseconds, '$1 '.$strseconds, '');
    } else {
        // Else we have SCORM 1.2 format there
        // first convert the timestamp to some SCORM 2004-like format for conveniency.
        $duration = preg_replace('#^(\d+):(\d+):([\d.]+)$#', 'T$1H$2M$3S', $duration);
        // Then convert in the same way as SCORM 2004.
        $pattern = array( '#T0+H#', '#([A-Z])0+M#', '#([A-Z])[0.]+S#', '#\.0+S#', '#0*(\d+)H#',
                            '#0*(\d+)M#', '#0+\.(\d+)S#', '#0*([\d.]+)S#', '#T#' );
        $replace = array( 'T', '$1', '$1', 'S', '$1 '.$strhours.' ', '$1 '.$strminutes.' ',
                            '0.$1 '.$strseconds, '$1 '.$strseconds, '' );
    }

    $result = preg_replace($pattern, $replace, $duration);

    return $result;
}

function scorm_get_toc_object($user, $scorm, $currentorg='', $scoid='', $mode='normal', $attempt='',
                                $play=false, $organizationsco=null) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    // Always pass the mode even if empty as that is what is done elsewhere and the urls have to match.
    $modestr = '&mode=';
    if ($mode != 'normal') {
        $modestr = '&mode='.$mode;
    }

    $result = array();
    $incomplete = false;

    if (!empty($organizationsco)) {
        $result[0] = $organizationsco;
        $result[0]->isvisible = 'true';
        $result[0]->statusicon = '';
        $result[0]->url = '';
    }

    if ($scoes = scorm_get_scoes($scorm->id, $currentorg)) {
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
                $sco->isvisible = 'true';
            }

            if (empty($sco->title)) {
                $sco->title = $sco->identifier;
            }

            if (scorm_version_check($scorm->version, SCORM_13)) {
                $sco->prereq = true;
            } else {
                $sco->prereq = empty($sco->prerequisites) || scorm_eval_prerequisites($sco->prerequisites, $usertracks);
            }

            if ($sco->isvisible === 'true') {
                if (!empty($sco->launch)) {
                    if (empty($scoid) && ($mode != 'normal')) {
                        $scoid = $sco->id;
                    }

                    if (isset($usertracks[$sco->identifier])) {
                        $usertrack = $usertracks[$sco->identifier];
                        $strstatus = get_string($usertrack->status, 'scorm');

                        if ($sco->scormtype == 'sco') {
                            $statusicon = html_writer::img($OUTPUT->pix_url($usertrack->status, 'scorm'), $strstatus,
                                                            array('title' => $strstatus));
                        } else {
                            $statusicon = html_writer::img($OUTPUT->pix_url('asset', 'scorm'), get_string('assetlaunched', 'scorm'),
                                                            array('title' => get_string('assetlaunched', 'scorm')));
                        }

                        if (($usertrack->status == 'notattempted') ||
                                ($usertrack->status == 'incomplete') ||
                                ($usertrack->status == 'browsed')) {
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
                            $statusicon = html_writer::img($OUTPUT->pix_url('suspend', 'scorm'), $strstatus.' - '.$strsuspended,
                                                            array('title' => $strstatus.' - '.$strsuspended));
                        }

                    } else {
                        if ($play && empty($scoid)) {
                            $scoid = $sco->id;
                        }

                        $incomplete = true;

                        if ($sco->scormtype == 'sco') {
                            $statusicon = html_writer::img($OUTPUT->pix_url('notattempted', 'scorm'),
                                                            get_string('notattempted', 'scorm'),
                                                            array('title' => get_string('notattempted', 'scorm')));
                        } else {
                            $statusicon = html_writer::img($OUTPUT->pix_url('asset', 'scorm'), get_string('asset', 'scorm'),
                                                            array('title' => get_string('asset', 'scorm')));
                        }
                    }
                }
            }

            if (empty($statusicon)) {
                $sco->statusicon = html_writer::img($OUTPUT->pix_url('notattempted', 'scorm'), get_string('notattempted', 'scorm'),
                                                    array('title' => get_string('notattempted', 'scorm')));
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
    $result = scorm_get_toc_get_parent_child($result, $currentorg);

    // Be safe, prevent warnings from showing up while returning array.
    if (!isset($scoid)) {
        $scoid = '';
    }

    return array('scoes' => $result, 'usertracks' => $usertracks, 'scoid' => $scoid);
}

function scorm_get_toc_get_parent_child(&$result, $currentorg) {
    $final = array();
    $level = 0;
    // Organization is always the root, prevparent.
    if (!empty($currentorg)) {
        $prevparent = $currentorg;
    } else {
        $prevparent = '/';
    }

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

function scorm_format_toc_for_treeview($user, $scorm, $scoes, $usertracks, $cmid, $toclink=TOCJSLINK, $currentorg='',
                                        $attempt='', $play=false, $organizationsco=null, $children=false) {
    global $CFG;

    $result = new stdClass();
    $result->prerequisites = true;
    $result->incomplete = true;
    $result->toc = '';

    if (!$children) {
        $attemptsmade = scorm_get_attempt_count($user->id, $scorm);
        $result->attemptleft = $scorm->maxattempt == 0 ? 1 : $scorm->maxattempt - $attemptsmade;
    }

    if (!$children) {
        $result->toc = html_writer::start_tag('ul');

        if (!$play && !empty($organizationsco)) {
            $result->toc .= html_writer::start_tag('li').$organizationsco->title.html_writer::end_tag('li');
        }
    }

    $prevsco = '';
    if (!empty($scoes)) {
        foreach ($scoes as $sco) {

            if ($sco->isvisible === 'false') {
                continue;
            }

            $result->toc .= html_writer::start_tag('li');
            $scoid = $sco->id;

            $score = '';

            if (isset($usertracks[$sco->identifier])) {
                $viewscore = has_capability('mod/scorm:viewscores', context_module::instance($cmid));
                if (isset($usertracks[$sco->identifier]->score_raw) && $viewscore) {
                    if ($usertracks[$sco->identifier]->score_raw != '') {
                        $score = '('.get_string('score', 'scorm').':&nbsp;'.$usertracks[$sco->identifier]->score_raw.')';
                    }
                }
            }

            if (!empty($sco->prereq)) {
                if ($sco->id == $scoid) {
                    $result->prerequisites = true;
                }

                if (!empty($prevsco) && scorm_version_check($scorm->version, SCORM_13) && !empty($prevsco->hidecontinue)) {
                    if ($sco->scormtype == 'sco') {
                        $result->toc .= html_writer::span($sco->statusicon.'&nbsp;'.format_string($sco->title));
                    } else {
                        $result->toc .= html_writer::span('&nbsp;'.format_string($sco->title));
                    }
                } else if ($toclink == TOCFULLURL) {
                    $url = $CFG->wwwroot.'/mod/scorm/player.php?'.$sco->url;
                    if (!empty($sco->launch)) {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= $sco->statusicon.'&nbsp;';
                            $result->toc .= html_writer::link($url, format_string($sco->title)).$score;
                        } else {
                            $result->toc .= '&nbsp;'.html_writer::link($url, format_string($sco->title),
                                                                        array('data-scoid' => $sco->id)).$score;
                        }
                    } else {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= $sco->statusicon.'&nbsp;'.format_string($sco->title).$score;
                        } else {
                            $result->toc .= '&nbsp;'.format_string($sco->title).$score;
                        }
                    }
                } else {
                    if (!empty($sco->launch)) {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= html_writer::tag('a', $sco->statusicon.'&nbsp;'.
                                                                format_string($sco->title).'&nbsp;'.$score,
                                                                array('data-scoid' => $sco->id, 'title' => $sco->url));
                        } else {
                            $result->toc .= html_writer::tag('a', '&nbsp;'.format_string($sco->title).'&nbsp;'.$score,
                                                                array('data-scoid' => $sco->id, 'title' => $sco->url));
                        }
                    } else {
                        if ($sco->scormtype == 'sco') {
                            $result->toc .= html_writer::span($sco->statusicon.'&nbsp;'.format_string($sco->title));
                        } else {
                            $result->toc .= html_writer::span('&nbsp;'.format_string($sco->title));
                        }
                    }
                }

            } else {
                if ($play) {
                    if ($sco->scormtype == 'sco') {
                        $result->toc .= html_writer::span($sco->statusicon.'&nbsp;'.format_string($sco->title));
                    } else {
                        $result->toc .= '&nbsp;'.format_string($sco->title).html_writer::end_span();
                    }
                } else {
                    if ($sco->scormtype == 'sco') {
                        $result->toc .= $sco->statusicon.'&nbsp;'.format_string($sco->title);
                    } else {
                        $result->toc .= '&nbsp;'.format_string($sco->title);
                    }
                }
            }

            if (!empty($sco->children)) {
                $result->toc .= html_writer::start_tag('ul');
                $childresult = scorm_format_toc_for_treeview($user, $scorm, $sco->children, $usertracks, $cmid,
                                                                $toclink, $currentorg, $attempt, $play, $organizationsco, true);

                // Is any of the children incomplete?
                $sco->incomplete = $childresult->incomplete;
                $result->toc .= $childresult->toc;
                $result->toc .= html_writer::end_tag('ul');
                $result->toc .= html_writer::end_tag('li');
            } else {
                $result->toc .= html_writer::end_tag('li');
            }
            $prevsco = $sco;
        }
        $result->incomplete = $sco->incomplete;
    }

    if (!$children) {
        $result->toc .= html_writer::end_tag('ul');
    }

    return $result;
}

function scorm_format_toc_for_droplist($scorm, $scoes, $usertracks, $currentorg='', $organizationsco=null,
                                        $children=false, $level=0, $tocmenus=array()) {
    if (!empty($scoes)) {
        if (!empty($organizationsco) && !$children) {
            $tocmenus[$organizationsco->id] = $organizationsco->title;
        }

        $parents[$level] = '/';
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

            if ($sco->scormtype == 'sco') {
                $tocmenus[$sco->id] = scorm_repeater('&minus;', $level) . '&gt;' . format_string($sco->title);
            }

            if (!empty($sco->children)) {
                $tocmenus = scorm_format_toc_for_droplist($scorm, $sco->children, $usertracks, $currentorg,
                                                            $organizationsco, true, $level, $tocmenus);
            }
        }
    }

    return $tocmenus;
}

function scorm_get_toc($user, $scorm, $cmid, $toclink=TOCJSLINK, $currentorg='', $scoid='', $mode='normal',
                        $attempt='', $play=false, $tocheader=false) {
    global $CFG, $DB, $OUTPUT;

    if (empty($attempt)) {
        $attempt = scorm_get_last_attempt($scorm->id, $user->id);
    }

    $result = new stdClass();
    $organizationsco = null;

    if ($tocheader) {
        $result->toc = html_writer::start_div('yui3-g-r', array('id' => 'scorm_layout'));
        $result->toc .= html_writer::start_div('yui3-u-1-5', array('id' => 'scorm_toc'));
        $result->toc .= html_writer::div('', '', array('id' => 'scorm_toc_title'));
        $result->toc .= html_writer::start_div('', array('id' => 'scorm_tree'));
    }

    if (!empty($currentorg)) {
        $organizationsco = $DB->get_record('scorm_scoes', array('scorm' => $scorm->id, 'identifier' => $currentorg));
        if (!empty($organizationsco->title)) {
            if ($play) {
                $result->toctitle = $organizationsco->title;
            }
        }
    }

    $scoes = scorm_get_toc_object($user, $scorm, $currentorg, $scoid, $mode, $attempt, $play, $organizationsco);

    $treeview = scorm_format_toc_for_treeview($user, $scorm, $scoes['scoes'][0]->children, $scoes['usertracks'], $cmid,
                                                $toclink, $currentorg, $attempt, $play, $organizationsco, false);

    if ($tocheader) {
        $result->toc .= $treeview->toc;
    } else {
        $result->toc = $treeview->toc;
    }

    if (!empty($scoes['scoid'])) {
        $scoid = $scoes['scoid'];
    }

    if (empty($scoid)) {
        // If this is a normal package with an org sco and child scos get the first child.
        if (!empty($scoes['scoes'][0]->children)) {
            $result->sco = $scoes['scoes'][0]->children[0];
        } else { // This package only has one sco - it may be a simple external AICC package.
            $result->sco = $scoes['scoes'][0];
        }

    } else {
        $result->sco = scorm_get_sco($scoid);
    }

    if ($scorm->hidetoc == SCORM_TOC_POPUP) {
        $tocmenu = scorm_format_toc_for_droplist($scorm, $scoes['scoes'][0]->children, $scoes['usertracks'],
                                                    $currentorg, $organizationsco);

        $modestr = '';
        if ($mode != 'normal') {
            $modestr = '&mode='.$mode;
        }

        $url = new moodle_url('/mod/scorm/player.php?a='.$scorm->id.'&currentorg='.$currentorg.$modestr);
        $result->tocmenu = $OUTPUT->single_select($url, 'scoid', $tocmenu, $result->sco->id, null, "tocmenu");
    }

    $result->prerequisites = $treeview->prerequisites;
    $result->incomplete = $treeview->incomplete;
    $result->attemptleft = $treeview->attemptleft;

    if ($tocheader) {
        $result->toc .= html_writer::end_div().html_writer::end_div();
        $result->toc .= html_writer::start_div('', array('id' => 'scorm_toc_toggle'));
        $result->toc .= html_writer::tag('button', '', array('id' => 'scorm_toc_toggle_btn')).html_writer::end_div();
        $result->toc .= html_writer::start_div('', array('id' => 'scorm_content'));
        $result->toc .= html_writer::div('', '', array('id' => 'scorm_navpanel'));
        $result->toc .= html_writer::end_div().html_writer::end_div();
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
    // Same options as in {@link download_file_content()}, used in {@link scorm_parse_scorm()}.
    $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => true, 'CURLOPT_MAXREDIRS' => 5));
    $cmsg = $curl->head($url);
    $info = $curl->get_info();
    if (empty($info['http_code']) || $info['http_code'] != 200) {
        return get_string('invalidurlhttpcheck', 'scorm', array('cmsg' => $cmsg));
    }

    return true;
}

/**
 * Check for a parameter in userdata and return it if it's set
 * or return the value from $ifempty if its empty
 *
 * @param stdClass $userdata Contains user's data
 * @param string $param parameter that should be checked
 * @param string $ifempty value to be replaced with if $param is not set
 * @return string value from $userdata->$param if its not empty, or $ifempty
 */
function scorm_isset($userdata, $param, $ifempty = '') {
    if (isset($userdata->$param)) {
        return $userdata->$param;
    } else {
        return $ifempty;
    }
}

/**
 * Check if the current sco is launchable
 * If not, find the next launchable sco
 *
 * @param stdClass $scorm Scorm object
 * @param integer $scoid id of scorm_scoes record.
 * @return integer scoid of correct sco to launch or empty if one cannot be found, which will trigger first sco.
 */
function scorm_check_launchable_sco($scorm, $scoid) {
    global $DB;
    if ($sco = scorm_get_sco($scoid, SCO_ONLY)) {
        if ($sco->launch == '') {
            // This scoid might be a top level org that can't be launched, find the first launchable sco after this sco.
            $scoes = $DB->get_records_select('scorm_scoes',
                                             'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true).
                                             ' AND id > ?', array($scorm->id, $sco->id), 'sortorder, id', 'id', 0, 1);
            if (!empty($scoes)) {
                $sco = reset($scoes); // Get first item from the list.
                return $sco->id;
            }
        } else {
            return $sco->id;
        }
    }
    // Returning 0 will cause default behaviour which will find the first launchable sco in the package.
    return 0;
}

/**
 * Check if a SCORM is available for the current user.
 *
 * @param  stdClass  $scorm            SCORM record
 * @param  boolean $checkviewreportcap Check the scorm:viewreport cap
 * @param  stdClass  $context          Module context, required if $checkviewreportcap is set to true
 * @return array                       status (available or not and possible warnings)
 */
function scorm_get_availability_status($scorm, $checkviewreportcap = false, $context = null) {
    $open = true;
    $closed = false;
    $warnings = array();

    $timenow = time();
    if (!empty($scorm->timeopen) and $scorm->timeopen > $timenow) {
        $open = false;
    }
    if (!empty($scorm->timeclose) and $timenow > $scorm->timeclose) {
        $closed = true;
    }

    if (!$open or $closed) {
        if ($checkviewreportcap and !empty($context) and has_capability('mod/scorm:viewreport', $context)) {
            return array(true, $warnings);
        }

        if (!$open) {
            $warnings['notopenyet'] = userdate($scorm->timeopen);
        }
        if ($closed) {
            $warnings['expired'] = userdate($scorm->timeclose);
        }
        return array(false, $warnings);
    }

    // Scorm is available.
    return array(true, $warnings);
}

/**
 * Requires a SCORM package to be available for the current user.
 *
 * @param  stdClass  $scorm            SCORM record
 * @param  boolean $checkviewreportcap Check the scorm:viewreport cap
 * @param  stdClass  $context          Module context, required if $checkviewreportcap is set to true
 * @throws moodle_exception
 */
function scorm_require_available($scorm, $checkviewreportcap = false, $context = null) {

    list($available, $warnings) = scorm_get_availability_status($scorm, $checkviewreportcap, $context);

    if (!$available) {
        $reason = current(array_keys($warnings));
        throw new moodle_exception($reason, 'scorm', '', $warnings[$reason]);
    }

}
