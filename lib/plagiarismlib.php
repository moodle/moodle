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
 * plagiarismlib.php - Contains Plagiarism related functions called by Modules.
 *
 * @since 2.0
 * @package    moodlecore
 * @subpackage plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

///// GENRIC PLAGIARISM FUNCTIONS ////////////////////////////////////////////////////

/**
 * returns list of possible plagiarism config options
 *
 * @return array - list of config options
 */
function plagiarism_config_options() {
    //list of config vars - defined here to allow re-use
    return array('use_plagiarism','plagiarism_show_student_score','plagiarism_show_student_report',
                 'plagiarism_draft_submit','plagiarism_compare_student_papers','plagiarism_compare_internet',
                 'plagiarism_compare_journals','plagiarism_compare_institution','plagiarism_report_gen',
                 'plagiarism_exclude_biblio','plagiarism_exclude_quoted','plagiarism_exclude_matches',
                 'plagiarism_exclude_matches_value');
}

/**
 * This function should be used to initialise settings and check if plagiarism is enabled
 * *
 * @return mixed - false if not enabled, or returns an array of relavant settings.
 */
function plagiarism_get_settings() {
   global $DB;
   $plagiarismsettings = (array)get_config('plagiarism');
   //check if tii enabled.
   if (isset($plagiarismsettings['turnitin_use']) && $plagiarismsettings['turnitin_use'] && isset($plagiarismsettings['turnitin_accountid']) && $plagiarismsettings['turnitin_accountid']) {
      //now check to make sure required settings are set!
      if (empty($plagiarismsettings['turnitin_secretkey'])) {
        error("Turnitin Secret Key not set!");
      }
      if (empty($plagiarismsettings['turnitin_userid'])) {
        error("Turnitin userid not set!");
      }
      if (empty($plagiarismsettings['turnitin_email'])) {
        error("Turnitin email not set!");
      }
      if (empty($plagiarismsettings['turnitin_firstname']) || empty($plagiarismsettings['turnitin_lastname'])) {
        error("Turnitin firstname/lastname not set!");
      }
      return $plagiarismsettings;
   } else {
    return false;
   }
}

/**
 * internal function that returns xml when provided a URL
 *
 * @param string $url the url being passed.
 * @return xml
 */
function plagiarism_get_xml($url) {
    require_once("filelib.php");
    if (!($fp = download_file_content($url))) {
        error("error trying to open plagiarism XML file!".$url);
    } else {
            //now do something with the XML file to check to see if this has worked!
        $xml = new SimpleXMLElement($fp);
        return $xml;
    }
}

/**
 * used by admin/cron.php to get similarity scores from submitted files.
 *
 */
function plagiarism_check_files() {
    $plagiarismsettings = plagiarism_get_settings();
    if (!empty($plagiarismsettings['turnitin_use'])) {
        //turnitin_send_files($plagiarismsettings); //now handled by events
        turnitin_get_scores($plagiarismsettings);
    }
}

/**
 * generates a url to allow access to a similarity report. - helper functino for plagiarism_get_link
 *
 * @param object  $file - single record from plagiarism_files table
 * @param object  $course - usually global $COURSE value
 * @param array  $plagiarismsettings - from a call to plagiarism_get_settings
 * @return string - url to allow login/viewing of a similarity report
 */
function plagiarism_get_report_link($file, $course, $plagiarismsettings) {
    global $DB;
    $return = '';

    $tii = array();
    if (!has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $file->cm))) {
        $user = $DB->get_record('user', array('id'=>$file->userid));

        $tii['username'] = $user->username;
        $tii['uem']      = $user->email;
        $tii['ufn']      = $user->firstname;
        $tii['uln']      = $user->lastname;
        $tii['uid']      = $user->username;
        $tii['utp'] = '1'; //1 = this user is an student
    } else {
        $tii['username'] = $plagiarismsettings['turnitin_userid'];
        $tii['uem']      = $plagiarismsettings['turnitin_email'];
        $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
        $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
        $tii['uid']      = $plagiarismsettings['turnitin_userid'];
       $tii['utp']      = '2'; //2 = this user is an instructor
    }
    $tii['cid']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //course ID //may need to include sitename in this to allow more than 1 moodle site with the same TII account to use TII API
    $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //Course title.  -this uses Course->id and shortname to ensure uniqueness.
    $tii['fcmd'] = '1'; //when set to 2 the tii api call returns XML
    $tii['fid'] = '6';
    $tii['oid'] = $file->externalid;

    return turnitin_get_url($tii);
}

/**
 * displays the similarity score and provides a link to the full report if allowed.
 *
 * @param int $userid - userid of the user related to the file
 * @param object  $file - file object
 * @param object  $cmid - this course module id
 * @param object  $module - this modules settings.
 * @return string - url to allow login/viewing of a similarity report
 */
function plagiarism_get_links($userid, $file, $cmid, $course, $module) {
    global $DB, $CFG, $USER;
    $plagiarismvalues = $DB->get_records_menu('plagiarism_config', array('cm'=>$cmid),'','name,value');
    if (empty($plagiarismvalues['use_plagiarism'])) {
        //nothing to do here... move along!
       return '';
    }
    $modulecontext = get_context_instance(CONTEXT_MODULE, $cmid);
    $output = '';

    //check if this is a user trying to look at their details, or a teacher with viewsimilarityscore rights.
    if (($USER->id == $userid) || has_capability('moodle/plagiarism:viewsimilarityscore', $modulecontext)) {
        if ($plagiarismsettings = plagiarism_get_settings()) {
            $plagiarismfile = $DB->get_record('plagiarism_files', array('cm'=>$cmid,
                                                                        'userid'=>$userid,
                                                                        'file'=>$file->get_id()));
            if (isset($plagiarismfile->similarityscore) && $plagiarismfile->statuscode=='success') { //if TII has returned a succesful score.
                //check for open mod.
                $assignclosed = false;
                $time = time();
                if (!empty($module->preventlate) && !empty($module->timedue)) {
                    $assignclosed = ($module->timeavailable <= $time && $time <= $module->timedue);
                } elseif (!empty($module->timeavailable)) {
                    $assignclosed = ($module->timeavailable <= $time);
                }
                $assignclosed = false;
                $rank = plagiarism_get_css_rank($plagiarismfile->similarityscore);
                if ($USER->id <> $userid) { //this is a teacher with moodle/plagiarism:viewsimilarityscore
                    if (has_capability('moodle/plagiarism:viewfullreport', $modulecontext)) {
                        $output .= '<span class="plagiarismreport"><a href="'.plagiarism_get_report_link($plagiarismfile, $course, $plagiarismsettings).'" target="_blank">'.get_string('similarity', 'plagiarism').':</a><span class="'.$rank.'">'.$plagiarismfile->similarityscore.'%</span></span>';
                    } else {
                        $output .= '<span class="plagiarismreport">'.get_string('similarity', 'plagiarism').':<span class="'.$rank.'">'.$plagiarismfile->similarityscore.'%</span></span>';
                    }
                } elseif (isset($plagiarismvalues['plagiarism_show_student_report']) && isset($plagiarismvalues['plagiarism_show_student_score']) and //if report and score fields are set.
                         ($plagiarismvalues['plagiarism_show_student_report']== 1 or $plagiarismvalues['plagiarism_show_student_score'] ==1 or //if show always is set
                         ($plagiarismvalues['plagiarism_show_student_score']==2 && $assignclosed) or //if student score to be show when assignment closed
                         ($plagiarismvalues['plagiarism_show_student_report']==2 && $assignclosed))) { //if student report to be shown when assignment closed
                    if (($plagiarismvalues['plagiarism_show_student_report']==2 && $assignclosed) or $plagiarismvalues['plagiarism_show_student_report']==1) {
                        $output .= '<span class="plagiarismreport"><a href="'.plagiarism_get_report_link($plagiarismfile, $course, $plagiarismsettings).'" target="_blank">'.get_string('similarity', 'plagiarism').'</a>';
                        if ($plagiarismvalues['plagiarism_show_student_score']==1 or ($plagiarismvalues['plagiarism_show_student_score']==2 && $assignclosed)) {
                            $output .= ':<span class="'.$rank.'">'.$plagiarismfile->similarityscore.'%</span>';
                        }
                        $output .= '</span>';
                    } else {
                        $output .= '<span class="plagiarismreport">'.get_string('similarity', 'plagiarism').':<span class="'.$rank.'">'.$plagiarismfile->similarityscore.'%</span>';
                    }
                }
                //now check if grademark enabled and return the status of this file.
                if (!empty($plagiarismsettings['turnitin_enablegrademark'])) {
                        $output .= '<span class="grademark">'.turnitin_get_grademark_link($plagiarismfile, $course, $module, $plagiarismsettings)."</span>";
                }
            } elseif(isset($plagiarismfile->statuscode)) { //always display errors - even if the student isn't able to see report/score.
                $output .= turnitin_error_text($plagiarismfile->statuscode);
            }
        }
    }
    return $output.'<br/>';
}

/**
 * updates a plagiarism_files record
 *
 * @param object $event  - data from an event trigger
 * @param object $file - a file object
 * @return int - id of plagiarism_files record
 */
function plagiarism_update_record($event, $file='') {
    global $USER, $DB,$CFG;

    $fileid = $event->file->get_id();
    //now update or insert record into plagiarism_files
    if ($plagiarism_file = $DB->get_record('plagiarism_files', array('cm'=>$event->cm->id,
                                                       'userid'=>$event->user->id,
                                                       'file'=>$fileid))) {
        //update record.
        $plagiarism_file->statuscode = 'pending';
        $plagiarism_file->similarityscore ='0';
        if (! $DB->update_record('plagiarism_files', $plagiarism_file)) {
            debugging("update plagiarism_files failed!");
        }
        return $plagiarism_file->id;
    } else {
        $plagiarism_file = new object();
        $plagiarism_file->cm = $event->cm->id;
        $plagiarism_file->userid = $event->user->id;
        $plagiarism_file->file = $fileid;
        $plagiarism_file->statuscode = 'pending';
        if (!$pid =  $DB->insert_record('plagiarism_files', $plagiarism_file)) {
            debugging("insert into plagiarism_files failed");
        }
        return $pid;
    }
}

/**
 * saves/updates plagiarism settings from a modules config page - called by course/modedit.php
 *
 * @param object $data - form data
 */
function plagiarism_save_form_elements($data) {
    global $DB;
    if (!plagiarism_get_settings()) {
        return;
    }
    if (isset($data->use_plagiarism)) {
      //array of posible plagiarism config options.
      $plagiarismelements = plagiarism_config_options();

      //first get existing values
      $existingelements = $DB->get_records_menu('plagiarism_config', array('cm'=>$data->coursemodule),'','name,id');
      foreach($plagiarismelements as $element) {
          if (isset($data->$element)) {
              $newelement = new object();
              $newelement->cm = $data->coursemodule;
              $newelement->name = $element;
              $newelement->value = $data->$element;
              if (isset($existingelements[$element])) { //update
                  $newelement->id = $existingelements[$element];
                  $DB->update_record('plagiarism_config', $newelement);
              } else { //insert
                  $DB->insert_record('plagiarism_config', $newelement);
              }
          }
      }
  }
}

/**
 * adds the list of plagiarism settings to a form
 *
 * @param object $mform - Moodle form object
 */
function plagiarism_get_form_elements($mform) {
        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
        $tiioptions = array(0 => get_string("never"), 1 => get_string("always"), 2 => get_string("showwhenclosed", "plagiarism"));
        $tiidraftoptions = array(0 => get_string("submitondraft","plagiarism"), 1 => get_string("submitonfinal","plagiarism"));
        $reportgenoptions = array( 0 => get_string('reportgenimmediate', 'plagiarism'), 1 => get_string('reportgenimmediateoverwrite', 'plagiarism'), 2 => get_string('reportgenduedate', 'plagiarism'));
        $excludetype = array( 0 => get_string('no'), 1 => get_string('wordcount', 'plagiarism'), 2 => get_string('percentage', 'plagiarism'));

        $mform->addElement('header', 'plagiarismdesc', get_string('plagiarism', 'plagiarism'));
        $mform->addElement('select', 'use_plagiarism', get_string("useplagiarism", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_show_student_score', get_string("showstudentsscore", "plagiarism"), $tiioptions);
        $mform->addElement('select', 'plagiarism_show_student_report', get_string("showstudentsreport", "plagiarism"), $tiioptions);
        if ($mform->elementExists('var4')) {
            $mform->addElement('select', 'plagiarism_draft_submit', get_string("draftsubmit", "plagiarism"), $tiidraftoptions);
        }
        $mform->addElement('select', 'plagiarism_compare_student_papers', get_string("plagiarismcomparestudents", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_compare_internet', get_string("plagiarismcompareinternet", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_compare_journals', get_string("plagiarismcomparejournals", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_compare_institution', get_string("plagiarismcompareinstitution", "plagiarism"), $ynoptions);
        //$mform->addElement('select', 'plagiarism_compare_institution', get_string("plagiarismcompareinstitution", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_report_gen', get_string("plagiarismreportgen", "plagiarism"), $reportgenoptions);
        $mform->addElement('select', 'plagiarism_exclude_biblio', get_string("plagiarismexcludebiblio", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_exclude_quoted', get_string("plagiarismexcludequoted", "plagiarism"), $ynoptions);
        $mform->addElement('select', 'plagiarism_exclude_matches', get_string("plagiarismexcludematches", "plagiarism"), $excludetype);
        $mform->addElement('text', 'plagiarism_exclude_matches_value', '');
        $mform->addRule('plagiarism_exclude_matches_value', null, 'numeric', null, 'client');
        $mform->disabledIf('plagiarism_exclude_matches_value', 'plagiarism_exclude_matches', 'eq', 0);

}

/**
 * adds the list of plagiarism settings to a form - called inside modules that have enabled plagiarism
 *
 * @param object $mform - Moodle form object
 * @param object $context - context object
 */
function plagiarism_get_form_elements_module($mform, $context) {
    global $CFG, $DB;
    if (!plagiarism_get_settings()) {
        return;
    }
    $cmid = optional_param('update', 0, PARAM_INT); //there doesn't seem to be a way to obtain the current cm a better way - $this->_cm is not available here.
    if (!empty($cmid)) {
        $plagiarismvalues = $DB->get_records_menu('plagiarism_config', array('cm'=>$cmid),'','name,value');
    }
    $plagiarismdefaults = $DB->get_records_menu('plagiarism_config', array('cm'=>0),'','name,value'); //cmid(0) is the default list.
    $plagiarismelements = plagiarism_config_options();

    if (has_capability('moodle/plagiarism:enable', $context)) {
        plagiarism_get_form_elements($mform);
        if ($mform->elementExists('plagiarism_draft_submit')) {
            $mform->disabledIf('plagiarism_draft_submit', 'var4', 'eq', 0);
        }
        //disable all plagiarism elements if use_plagiarism eg 0
        foreach ($plagiarismelements as $element) {
            if ($element <> 'use_plagiarism') { //ignore this var
                $mform->disabledIf($element, 'use_plagiarism', 'eq', 0);
            }
        }
    } else { //add plagiarism settings as hidden vars.
        foreach ($plagiarismelements as $element) {
            $mform->addElement('hidden', $element);
        }
    }
    //now set defaults.
    foreach ($plagiarismelements as $element) {
        if (isset($plagiarismvalues[$element])) {
            $mform->setDefault($element, $plagiarismvalues[$element]);
        } elseif (isset($plagiarismdefaults[$element])) {
            $mform->setDefault($element, $plagiarismdefaults[$element]);
        }
    }
}
/**
 * updates the status of all files within a module
 *
 * @param object $course - full Course object
 * @param object $cm - full cm object
 */
function plagiarism_update_status($course, $cm) {
    global $DB;
    if (!$plagiarismsettings = plagiarism_get_settings()) {
        return;
    }
    //currently only used for grademark - check if enabled and return if not.
    if (empty($plagiarismsettings['turnitin_enablegrademark'])) {
        return;
    }
    if (!$moduletype = $DB->get_field('modules','name', array('id'=>$cm->module))) {
        debugging("invalid moduleid! - moduleid:".$cm->module." Module:".$moduletype);
        continue;
    }
    if (!$module = $DB->get_record($moduletype, array('id'=>$cm->instance))) {
        debugging("invalid instanceid! - instance:".$cm->instance." Module:".$moduletype);
        continue;
    }

    $tii = array();
    //set globals.
    $tii['username'] = $plagiarismsettings['turnitin_userid'];
    $tii['uem']      = $plagiarismsettings['turnitin_email'];
    $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
    $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
    $tii['uid']      = $plagiarismsettings['turnitin_userid'];
    $tii['utp']      = '2'; //2 = this user is an instructor
    $tii['cid']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //course ID
    $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //Course title.  -this uses Course->id and shortname to ensure uniqueness.
    $turnitin_assignid = $DB->get_field('plagiarism_config','value', array('cm'=>$cm->id, 'name'=>'turnitin_assignid'));
    if (!empty($turnitin_assignid)) {
        $tii['assignid'] = $turnitin_assignid;
    }
    $tii['assign']   = $plagiarismsettings['turnitin_courseprefix']. '_'.$module->name.'_'.$module->id; //assignment name stored in TII
    $tii['fcmd']     = '2';
    $tii['fid']     = '10';
    $tiixml = plagiarism_get_xml(turnitin_get_url($tii));

    if (!empty($tiixml->object)) {
        //get full list of plagiarism_files for this cm
        $grademarkstatus= array();
        foreach($tiixml->object as $tiiobject) {
            $grademarkstatus[(int)$tiiobject->objectID[0]] = (int)$tiiobject->gradeMarkStatus[0];
        }
        if (!empty($grademarkstatus)) {
            $plagiarsim_files = $DB->get_records('plagiarism_files', array('cm'=>$cm->id));
            foreach ($plagiarsim_files as $file) {
                if (isset($grademarkstatus[$file->externalid]) && $file->externalstatus <> $grademarkstatus[$file->externalid]) {
                    $file->externalstatus = $grademarkstatus[$file->externalid];
                    $DB->update_record('plagiarism_files', $file);
                }
            }
        }
    }
}
/**
* Function that returns the name of the css class to use for a given similarity score
* @param integer $score - the similarity score
* @return string - string name of css class
*/
function plagiarism_get_css_rank ($score) {
    $rank = "none";
    if($score >  90) { $rank = "1"; }
    elseif($score >  80) { $rank = "2"; }
    elseif($score >  70) { $rank = "3"; }
    elseif($score >  60) { $rank = "4"; }
    elseif($score >  50) { $rank = "5"; }
    elseif($score >  40) { $rank = "6"; }
    elseif($score >  30) { $rank = "7"; }
    elseif($score >  20) { $rank = "8"; }
    elseif($score >  10) { $rank = "9"; }
    elseif($score >=  0) { $rank = "10"; }

    return "rank$rank";
}

/**
* Function that prints the student disclosure notifying that the files will be checked for plagiarism
* @param integer $cmid - the cmid of this module
*/
function plagiarism_print_disclosure($cmid) {
    global $DB, $OUTPUT;
    if ($plagiarismsettings = plagiarism_get_settings()) {
        if (!empty($plagiarismsettings['turnitin_student_disclosure'])) {
            $showdisclosure = $DB->get_field('plagiarism_config', 'value', array('cm'=>$cmid, 'name'=>'use_plagiarism'));
            if ($showdisclosure) {
                echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
                $formatoptions = new stdClass;
                $formatoptions->noclean = true;
                echo format_text($plagiarismsettings['turnitin_student_disclosure'], FORMAT_MOODLE, $formatoptions);
                echo $OUTPUT->box_end();
            }
        }
    }
}

/// MODULE SPECIFIC FUNCTIONS ////////////////////////////////////////////////////
/// Functions that manage events triggered by modules.

/**
 * event handler for assignment file submission events
 * @param object $eventdata - data passed by the event handler.
 */
function plagiarism_event_assignment_file_submission($eventdata) {
    global $DB, $CFG;
    $result = true;

    //require_once($CFG->libdir.'/filelib.php');
    $plagiarismsettings = plagiarism_get_settings();
    $plagiarismvalues = $DB->get_records_menu('plagiarism_config', array('cm'=>$eventdata->cm->id),'','name,value');
    if (!$plagiarismsettings || empty($plagiarismvalues['use_plagiarism'])) {
       //nothing to do here... move along!
       return $result;
    }
    //check if the module associated with this event still exists
    if (!$DB->record_exists('course_modules', array('id' => $eventdata->cm->id))) {
        return $result;
    }
    if (!empty($eventdata->file)) { //this is an upload event.
        //hacky way to check file still exists
        $fs = get_file_storage();
        $fileid = $fs->get_file_by_id($eventdata->file->get_id());
        if (empty($fileid)) {
            return $result;
        }
        if (empty($plagiarismvalues['plagiarism_draft_submit'])) { //check if this is an advanced assignment and shouldn't send the file yet.
            $pid = plagiarism_update_record($eventdata);
            $result = turnitin_send_file($pid, $eventdata->file);
        }
    } else { //this is a finalize event
        if (isset($plagiarismvalues['plagiarism_draft_submit']) && $plagiarismvalues['plagiarism_draft_submit'] == 1) { // is file to be sent on final submission?
            // we need to get a list of files attached to this assignment and put them in an array, so that
            // we can submit each of them for processing.
            $modulecontext = get_context_instance(CONTEXT_MODULE, $eventdata->cm->id);
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($modulecontext->id, 'assignment_submission', $eventdata->user->id, "timemodified", false)) {
                foreach ($files as $file) {
                    //TODO: need to check if this file has already been sent! - possible that the file was sent before draft submit was set.
                    $eventdata->file = $file;
                    $pid = plagiarism_update_record($eventdata, $file);
                    $result = turnitin_send_file($pid, $file);
                }
            }
        }
   }
   return $result;
}
/**
 * Event handler for assignment creation process
 * @param object $eventdata - data passed by the event handler.
 */
function plagiarism_event_assignment_mod_created($eventdata) {
    //call specific Plagiarism tools if needed.
    $result = turnitin_update_assignment($eventdata, 'create');

    return $result;
}

/**
 * Event handler for assignment update process
 * @param object $eventdata - data passed by the event handler.
 */
function plagiarism_event_assignment_mod_updated($eventdata) {
    //call specific Plagiarism tools if needed.
    $result = turnitin_update_assignment($eventdata, 'update');

    return $result;
}

/**
 * Event handler for assignment delete process
 * @param object $eventdata - data passed by the event handler.
 */
function plagiarism_event_assignment_mod_deleted($eventdata) {
    //call specific Plagiarism tools if needed.
    $result = turnitin_update_assignment($eventdata, 'delete');

    return $result;
}

/// TURNITIN FUNCTIONS ////////////////////////////////////////////////////
//function specific to the Turnitin plagiarism tool

/**
 * generates a url including md5 for use in posting to Turnitin API.
 *
 * @param object $tii the intial $tii object
 * @param bool $returnArray - if true, returns a formatted $tii object, if false returns a url.
 * @return mixed - array or url depending on $returnArray.
 */
function turnitin_get_url($tii, $returnArray=false) {
    global $CFG,$DB;
    $plagiarismsettings = plagiarism_get_settings();

    //make sure all $tii values are clean.
    foreach($tii as $key => $value) {
        if (!empty($value) AND $key <> 'tem' AND $key <> 'uem') {
            $value = rawurldecode($value); //decode url first. (in case has already be encoded - don't want to end up with double % replacements)
            $value = rawurlencode($value);
            $value = str_replace('%20', '_', $value);
            $tii[$key] = $value;
        }
    }
    //TODO need to check lengths of certain vars. - some cannot be under 5 or over 50.
    if (isset($plagiarismsettings['turnitin_senduseremail']) && $plagiarismsettings['turnitin_senduseremail']) {
        $tii['dis'] ='0'; //sets e-mail notification for users in tii system to enabled.
    } else {
        $tii['dis'] ='1'; //sets e-mail notification for users in tii system to disabled.
    }
    //munge e-mails if prefix is set.
    if (isset($plagiarismsettings['turnitin_emailprefix'])) { //if email prefix is set
        if ($tii['uem'] <> $plagiarismsettings['turnitin_email']) { //if email is not the global teacher.
            $tii['uem'] = $plagiarismsettings['turnitin_emailprefix'] . $tii['uem']; //munge e-mail to prevent user access.
        }
    }
    //set vars if not set.
    if (!isset($tii['encrypt'])) {
        $tii['encrypt'] = '0';
    }
    if (!isset($tii['diagnostic'])) {
        $tii['diagnostic'] = '0';
    }
    if (!isset($tii['tem'])) {
        $tii['tem'] = $plagiarismsettings['turnitin_email'];
    }
    if (!isset($tii['upw'])) {
        $tii['upw'] = '';
    }
    if (!isset($tii['cpw'])) {
        $tii['cpw'] = '';
    }
    if (!isset($tii['ced'])) {
        $tii['ced'] = '';
    }
    if (!isset($tii['dtdue'])) {
        $tii['dtdue'] = '';
    }
    if (!isset($tii['dtstart'])) {
        $tii['dtstart'] = '';
    }
    if (!isset($tii['newassign'])) {
        $tii['newassign'] = '';
    }
    if (!isset($tii['newupw'])) {
        $tii['newupw'] = '';
    }
    if (!isset($tii['oid'])) {
        $tii['oid'] = '';
    }
    if (!isset($tii['pfn'])) {
        $tii['pfn'] = '';
    }
    if (!isset($tii['pln'])) {
        $tii['pln'] = '';
    }
    if (!isset($tii['ptl'])) {
        $tii['ptl'] = '';
    }
    if (!isset($tii['ptype'])) {
        $tii['ptype'] = '';
    }
    if (!isset($tii['said'])) {
        $tii['said'] = '';
    }
    if (!isset($tii['assignid'])) {
        $tii['assignid'] = '';
    }
    if (!isset($tii['assign'])) {
        $tii['assign'] = '';
    }
    if (!isset($tii['cid'])) {
        $tii['cid'] = '';
    }
    if (!isset($tii['ctl'])) {
        $tii['ctl'] = '';
    }

    $tii['gmtime']  = turnitin_get_gmtime();
    $tii['aid']     = $plagiarismsettings['turnitin_accountid'];
    $tii['version'] = rawurlencode($CFG->release); //only used internally by TII.

    //prepare $tii for md5string - need to urldecode before generating the md5.
    $tiimd5 = array();
    foreach($tii as $key => $value) {
        if (!empty($value) AND $key <> 'tem' AND $key <> 'uem') {
            $value = rawurldecode($value); //decode url for calculating MD5
            $tiimd5[$key] = $value;
        } else {
            $tiimd5[$key] = $value;
        }
    }

    $tii['md5'] = turnitin_get_md5string($tiimd5);

    if ($returnArray) {
        return $tii;
    } else {
        $url = $plagiarismsettings['turnitin_api']."?";
        foreach ($tii as $key => $value) {
            $url .= $key .'='. $value. '&';
        }

        return $url;
    }
}

/**
 * internal function gets the current time formatted for use in the Turnitin Url, used by turnitin_get_url
 *
 * @return string - formatted for use in Turnitin API call.
 */
function turnitin_get_gmtime() {
    return substr(gmdate('YmdHi'), 0, -1);
}

/**
 * internal function that generates an md5 based on particular items in a $tii array - used by turnitin_get_url
 *
 * @param object $tii the intial $tii object
 * @return string - calculated md5
 */
function turnitin_get_md5string($tii){
    global $CFG,$DB;
    $plagiarismsettings = plagiarism_get_settings();

    $md5string = $plagiarismsettings['turnitin_accountid'].
                $tii['assign'].
                $tii['assignid'].
                $tii['ced'].
                $tii['cid'].
                $tii['cpw'].
                $tii['ctl'].
                $tii['diagnostic'].
                $tii['dis'].
                $tii['dtdue'].
                $tii['dtstart'].
                $tii['encrypt'].
                $tii['fcmd'].
                $tii['fid'].
                $tii['gmtime'].
                $tii['newassign'].
                $tii['newupw'].
                $tii['oid'].
                $tii['pfn'].
                $tii['pln'].
                $tii['ptl'].
                $tii['ptype'].
                $tii['said'].
                $tii['tem'].
                $tii['uem'].
                $tii['ufn'].
                $tii['uid'].
                $tii['uln'].
                $tii['upw'].
                $tii['username'].
                $tii['utp'].
                $plagiarismsettings['turnitin_secretkey'];

    return md5($md5string);
}


/**
 * post data to TII
 *
 * @param object $tii - the object containing all the settings required.
 * @return xml
 */
function turnitin_post_data($tii, $file='') {
    global $DB, $CFG;
    $fields = turnitin_get_url($tii, 'array');
    $url = get_config('plagiarism', 'turnitin_api');

    $status = check_dir_exists($CFG->dataroot."/plagiarism/",true);
    if ($status && !empty($file)) {
        //We cannot access the file location of $file directly - we must create a temp file to point to instead
        $filename = $CFG->dataroot."/plagiarism/".time(); //unique name for this file.
        $fh = fopen($filename,'w');
        fwrite($fh, $file->get_content());
        fclose($fh);
        $fields['pdata'] = '@'.$filename;
        $c = new curl(array('proxy'=>true));
        $status = new SimpleXMLElement($c->post($url,$fields));
        unlink($filename);
    } else {
        $c = new curl(array('proxy'=>true));
        $status = new SimpleXMLElement($c->post($url,$fields));
    }
    return $status;
}

/**
 * Function that starts Turnitin session - some api calls require this
 *
 * @param object  $plagiarismsettings - from a call to plagiarism_get_settings
 * @return string - Turnitin sessionid
 */
function turnitin_start_session($plagiarismsettings) {
    $tii = array();
    //set globals.
    $tii['username'] = $plagiarismsettings['turnitin_userid'];
    $tii['uem']      = $plagiarismsettings['turnitin_email'];
    $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
    $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
    $tii['uid']      = $plagiarismsettings['turnitin_userid'];
    $tii['utp']      = '2'; //2 = this user is an instructor
    $tii['fcmd']     = '2';
    $tii['fid']     = '17';
    $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
    if (isset($tiixml->sessionid[0])) {
        return $tiixml->sessionid[0];
    } else {
        return '';
    }
}
/**
 * Function that ends a Turnitin session
 *
 * @param object  $plagiarismsettings - from a call to plagiarism_get_settings
 * @param string - Turnitin sessionid - from a call to turnitin_start_session
 */

function turnitin_end_session($plagiarismsettings, $tiisession) {
    if (empty($tiisession)) {
        return;
    }
    $tii = array();
    //set globals.
    $tii['username'] = $plagiarismsettings['turnitin_userid'];
    $tii['uem']      = $plagiarismsettings['turnitin_email'];
    $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
    $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
    $tii['uid']      = $plagiarismsettings['turnitin_userid'];
    $tii['utp']      = '2'; //2 = this user is an instructor
    $tii['fcmd']     = '2';
    $tii['fid']     = '18';
    $tii['session-id'] = $tiisession;
    $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
}

/**
 * used to send files to turnitin for processing
 * $pid - id of this record from plagiarism_files table
 * $file - contains actual file object
 *
 */
function turnitin_send_file($pid, $file) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/filelib.php');
    $plagiarismsettings = plagiarism_get_settings();
    if (empty($plagiarismsettings['turnitin_use'])) {
        return true;
    }
    //get information about this file
    $plagiarism_file = $DB->get_record('plagiarism_files', array('id'=>$pid));
    $plagiarism_file->fileobject = $file; //store fileobject for use in submission.

    if (!$user = $DB->get_record('user', array('id'=>$plagiarism_file->userid))) {
        debugging("invalid userid! - userid:".$file->userid." Module:".$moduletype." Fileid:".$plagiarism_file->id);
        continue;
    }
    if (!$cm = $DB->get_record('course_modules', array('id'=>$plagiarism_file->cm))) {
        debugging("invalid cmid! ".$file->cm." Fileid:".$file->id);
        continue;
    }
    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        debugging("invalid cmid! - courseid:".$file->course." Module:".$moduletype." Fileid:".$plagiarism_file->id);
        continue;
    }
    if (!$moduletype = $DB->get_field('modules','name', array('id'=>$cm->module))) {
        debugging("invalid moduleid! - moduleid:".$cm->module." Module:".$moduletype." Fileid:".$plagiarism_file->id);
        continue;
    }
    if (!$module = $DB->get_record($moduletype, array('id'=>$cm->instance))) {
        debugging("invalid instanceid! - instance:".$cm->instance." Module:".$moduletype." Fileid:".$plagiarism_file->id);
        continue;
    }

    //Start Turnitin Session
    $tiisession = turnitin_start_session($plagiarismsettings);
    //now send the file.
    $tii = array();
    $tii['username'] = $user->username;
    $tii['uem']      = $user->email;
    $tii['ufn']      = $user->firstname;
    $tii['uln']      = $user->lastname;
    $tii['uid']      = $user->username;
    $tii['utp']      = '1'; // 1= this is a student.
    $tii['cid']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname;
    $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname;
    $tii['fcmd']     = '2'; //when set to 2 the tii api call returns XML
    $tii['session-id'] = $tiisession;
    //$tii2['diagnostic'] = '1';
    $tii['fid']      = '1'; //set command. - create user and login student to Turnitin (fid=1)
    $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
    if (empty($tiixml->rcode[0]) or $tiixml->rcode[0] <> '11') { //this is the success code for uploading a file. - we need to return the oid and save it!
         mtrace('could not create user/login to turnitin code:'.$tiixml->rcode[0]);
    } else {
        $plagiarism_file->statuscode = $tiixml->rcode[0];
        if (! $DB->update_record('plagiarism_files', $plagiarism_file)) {
            debugging("Error updating plagiarism_files record");
        }

        //now enrol user in class under the given account (fid=3)
        $turnitin_assignid = $DB->get_field('plagiarism_config','value', array('cm'=>$cm->id, 'name'=>'turnitin_assignid'));
        if (!empty($turnitin_assignid)) {
            $tii['assignid'] = $turnitin_assignid;
        }
        $tii['assign']   = $plagiarismsettings['turnitin_courseprefix']. '_'.$module->name.'_'.$module->id;
        $tii['fid']      = '3';
        //$tii2['diagnostic'] = '1';
        $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
        if (empty($tiixml->rcode[0]) or $tiixml->rcode[0] <> '31') { //this is the success code for uploading a file. - we need to return the oid and save it!
            mtrace('could not enrol user in turnitin class code:'.$tiixml->rcode[0]);
        } else {
            $plagiarism_file->statuscode = $tiixml->rcode[0];
            if (! $DB->update_record('plagiarism_files', $plagiarism_file)) {
                debugging("Error updating plagiarism_files record");
            }

            //now submit this uploaded file to Tii! (fid=5)
            $tii['utp']     = '1'; //2 = instructor, 1= student.
            $tii['fid']     = '5';
            $tii['ptl']     = $file->get_filename(); //paper title
            $tii['ptype']   = '2'; //filetype
            $tii['pfn']     = $tii['ufn'];
            $tii['pln']     = $tii['uln'];
            $tii['submit_date'] = gmdate('Ymd', $file->get_timemodified());
            //$tii['diagnostic'] = '1';
            $tiixml = turnitin_post_data($tii, $file);
            if ($tiixml->rcode[0] == '51') { //this is the success code for uploading a file. - we need to return the oid and save it!
                $plagiarism_file->externalid = $tiixml->objectID[0];
                debugging("success uploading assignment", DEBUG_DEVELOPER);
            } else {
                debugging("failed to upload assignment errorcode".$tiixml->rcode[0]);
            }
            $plagiarism_file->statuscode = $tiixml->rcode[0];
            if (! $DB->update_record('plagiarism_files', $plagiarism_file)) {
                debugging("Error updating plagiarism_files record");
            }
            turnitin_end_session($plagiarismsettings, $tiisession);

            return $tiixml;
        }
    }
}
/**
 * used to obtain similarity scores from Turnitin for submitted files.
 *
 * @param object  $plagiarismsettings - from a call to plagiarism_get_settings
 *
 */
function turnitin_get_scores($plagiarismsettings) {
    global $DB;

    $count = 0;
    mtrace("getting Turnitin scores");
    //first do submission
    //get all files set to "51" - success code for uploading.
    $files = $DB->get_records('plagiarism_files',array('statuscode'=>'51'));
    if (!empty($files)) {
        foreach($files as $file) {
            //set globals.
            $user = $DB->get_record('user', array('id'=>$file->userid));
            $coursemodule = $DB->get_record('course_modules', array('id'=>$file->cm));
            $course = $DB->get_record('course', array('id'=>$coursemodule->course));
            $tii['username'] = $user->username;
            $tii['uem']      = $user->email;
            $tii['ufn']      = $user->firstname;
            $tii['uln']      = $user->lastname;
            $tii['uid']      = $user->username;
            $tii['utp']      = '1'; // 1= this is a student.
            $tii['cid']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname;
            $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname;
            $tii['fcmd']     = '2'; //when set to 2 the tii api call returns XML
            $tii['fid']      = '6';
            $tii['oid']      = $file->externalid;
            $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
            if ($tiixml->rcode[0] == '61') { //this is the success code for uploading a file. - we need to return the oid and save it!
                $file->similarityscore = $tiixml->originalityscore[0];
                $file->statuscode = 'success';
                if (! $DB->update_record('plagiarism_files', $file)) {
                    debugging("Error updating plagiarism_files record");
                }
            } else {
                 mtrace('similarity report not available yet for fileid:'.$file->id. " code:".$tiixml->rcode[0]);
            }
        }
    }
/*    if (!empty($plagiarismsettings['turnitin_enablegrademark'])) {
        mtrace("check for external Grades");
    }*/
}


/**
 * given an error code, returns the description for this error
 * @param string statuscode The Error code.
 * @param boolean $notify if true, returns a notify call - otherwise just returns the text of the error.
 */
function turnitin_error_text($statuscode, $notify=true) {
   $return = '';
   $statuscode = (int) $statuscode;
   if (!empty($statuscode)) {
       if ($statuscode < 100) { //don't return an error state for codes 0-99
          return '';
       } elseif (($statuscode > 1006 && $statuscode < 1014) or ($statuscode > 1022 && $statuscode < 1025) or $statuscode == 1020) { //these are general errors that a could be useful to students.
           $return = get_string('tiierror'.$statuscode, 'plagiarism');
       } elseif ($statuscode > 1024 && $statuscode < 2000) { //don't have documentation on the other 1000 series errors, so just display a general one.
           $return = get_string('tiierrorpaperfail', 'plagiarism').':'.$statuscode;
       } elseif ($statuscode < 1025 || $statuscode > 2000) { //these are not errors that a student can make any sense out of.
           $return = get_string('tiiconfigerror', 'plagiarism').'('.$statuscode.')';
       }
       if (!empty($return) && $notify) {
           $return = notify($return, 'notifyproblem', 'left', true);
       }
   }
   return $return;
}

/**
 * creates/updates the assignment within Turnitin - used by event handlers.
 *
 * @param object $eventdata - data returned in an Event
 * @return boolean  returns false if unexpected error occurs.
 */
function turnitin_update_assignment($eventdata, $action) {
    global $DB;
    $result = true;
    $plagiarismsettings = plagiarism_get_settings();
    $plagiarismvalues = $DB->get_records_menu('plagiarism_config', array('cm'=>$eventdata->cm),'','name,value');
    if (!$plagiarismsettings || empty($plagiarismvalues['use_plagiarism'])) { //plagiarism not enabled so don't continue
        return $result;
    }
    if ($action=='delete') {
        //delete function deliberately not handled (fid=8)
        //if an assignment is deleted "accidentally" we can resotre off backups - but if
        //the external Turnitin assignment is deleted, we can't easily restore that.
        //maybe a config option could be added to enable/disable this
        return true;
    }
    //first set up this assignment/assign the global teacher to this course.
    $course = $DB->get_record('course',  array('id'=>$eventdata->course));
    if (empty($course)) {
        debugging("couldn't find course record - might have been deleted?", DEBUG_DEVELOPER);
        return true; //don't let this event kill cron
    }
    if (!$cm = $DB->get_record('course_modules', array('id'=>$eventdata->cm))) {
        debugging("invalid cmid! - might have been deleted?".$eventdata->cm, DEBUG_DEVELOPER);
        return true; //don't let this event kill cron
    }
    if (!$moduletype = $DB->get_field('modules','name', array('id'=>$cm->module))) {
        debugging("invalid moduleid! - moduleid:".$cm->module." Module:".$moduletype, DEBUG_DEVELOPER);
        return true; //don't let this event kill cron
    }
    if (!$module = $DB->get_record($moduletype, array('id'=>$cm->instance))) {
        debugging("invalid instanceid! - instance:".$cm->instance." Module:".$moduletype, DEBUG_DEVELOPER);
        return true; //don't let this event kill cron
    }
    
    $tiisession = turnitin_start_session($plagiarismsettings);
    $tii = array();
    //set globals.
    $tii['username'] = $plagiarismsettings['turnitin_userid'];
    $tii['uem']      = $plagiarismsettings['turnitin_email'];
    $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
    $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
    $tii['uid']      = $plagiarismsettings['turnitin_userid'];
    $tii['utp']      = '2'; //2 = this user is an instructor
    $tii['cid']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //course ID
    $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //Course title.  -this uses Course->id and shortname to ensure uniqueness.
    $tii['session-id'] = $tiisession;
    if ($action=='create' or $action=='update') { //TODO: split this into 2 - we don't need to call the create if we know it already exists.
        $tii['fcmd'] = '2'; //when set to 2 the TII API should return XML
        $tii['fid'] = '2'; // create class under the given account and assign above user as instructor (fid=2)
        //$tii['diagnostic'] = '1';
        $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
        if (!empty($tiixml->rcode[0]) && ($tiixml->rcode[0] == '20' or $tiixml->rcode[0] == '21' or $tiixml->rcode[0] == '22')) { //these rcodes signify that this assignment exists, or has been successfully updated.         
            //now create Assignment in Class
            $tii['assignid'] = $plagiarismsettings['turnitin_courseprefix']. '_'.$module->name.'_'.$module->id; //assignment ID - uses $returnid to ensure uniqueness
            $tii['assign']   = $plagiarismsettings['turnitin_courseprefix']. '_'.$module->name.'_'.$module->id; //assignment name stored in TII
            $tii['fid']      = '4';
            $tii['ptl']      = $course->id.$course->shortname; //paper title? - assname?
            $tii['ptype']    = '2'; //filetype
            $tii['pfn']      = $tii['ufn'];
            $tii['pln']      = $tii['uln'];
            if (!empty($module->timeavailable)) {
                $tii['dtstart']  = gmdate('Ymd', $module->timeavailable);
            } else {
                $tii['dtstart']  = gmdate('Ymd', time());
            }
            if (!empty($module->timedue) && !empty($module->preventlate)) {
                $tii['dtdue']    = gmdate('Ymd', $module->timedue); //set to 1 day in future from date due.
            } else {
                $tii['dtdue']    = gmdate('Ymd', time()+ (30 * 24 * 60 * 60)); //set to 30 days in future if not set by the module.
            }
            $tii['s_view_report']     = (empty($plagiarismvalues['plagiarism_show_student_report']) ? 0 : 1); //allow students to view the full report.
            $tii['s_paper_check']     = (isset($plagiarismvalues['plagiarism_compare_student_papers']) ? $plagiarismvalues['plagiarism_compare_student_papers'] : '');
            $tii['internet_check']    = (isset($plagiarismvalues['plagiarism_compare_internet']) ? $plagiarismvalues['plagiarism_compare_internet'] : '');
            $tii['journal_check']     = (isset($plagiarismvalues['plagiarism_compare_journals']) ? $plagiarismvalues['plagiarism_compare_journals'] : '');
            $tii['institution_check'] = (isset($plagiarismvalues['plagiarism_compare_institution']) ? $plagiarismvalues['plagiarism_compare_institution'] : '');
            $tii['report_gen_speed']  = (isset($plagiarismvalues['plagiarism_compare_institution']) ? $plagiarismvalues['plagiarism_compare_institution'] : '');
            $tii['exclude_biblio']    = (isset($plagiarismvalues['plagiarism_exclude_biblio']) ? $plagiarismvalues['plagiarism_exclude_biblio'] : '');
            $tii['exclude_quoted']    = (isset($plagiarismvalues['plagiarism_exclude_quoted']) ? $plagiarismvalues['plagiarism_exclude_quoted'] : '');
            $tii['exclude_type']      = (isset($plagiarismvalues['plagiarism_exclude_matches']) ? $plagiarismvalues['plagiarism_exclude_matches'] : '');
            $tii['exclude_value']     = (isset($plagiarismvalues['plagiarism_exclude_matches_value']) ? $plagiarismvalues['plagiarism_exclude_matches_valuesss'] : '');
            //$tii['diagnostic'] = '1'; //debug only - uncomment when using in production.
            //first check if this assignment has already been created
            if (empty($plagiarismvalues['turnitin_assignid'])) {
                $tii['fcmd'] = '2'; //when set to 2 create the assignment
            } else {
                $tii['assignid'] = $plagiarismvalues['turnitin_assignid'];
                $tii['fcmd'] = '3'; //when set to 3 - it updates the course
            }
            $tiixml = turnitin_post_data($tii);
            if ($tiixml->rcode[0]=='419') { //if assignment already exists then update it and set externalassignid correctly
                $tii['fcmd'] = '3'; //when set to 3 - it updates the course
                $tiixml = turnitin_post_data($tii);
            }
            if ($tiixml->rcode[0]=='41' or $tiixml->rcode[0]=='42') {
                mtrace("Turnitin Success creating Class and assignment");
            } else {
                mtrace("Error: could not create assignment in class statuscode:".$tiixml->rcode[0]);
                $return = false;
            }
            if (!empty($tiixml->assignmentid[0])) {
                if (empty($plagiarismvalues['turnitin_assignid'])) {
                    $configval = new stdclass();
                    $configval->cm = $eventdata->cm;
                    $configval->name = 'turnitin_assignid';
                    $configval->value = $tiixml->assignmentid[0];
                    $DB->insert_record('plagiarism_config', $configval);
                } else {
                    $configval = $DB->get_record('plagiarism_config', array('cm'=> $eventdata->cm, 'name'=> 'turnitin_assignid'));
                    $configval->value = $tiixml->assignmentid[0];
                    $DB->update_record('plagiarism_config', $configval);
                }
            }
        } else {
            mtrace("Error: could not create class and assign global instructor statuscode:".$rcode);
            $return = false;
        }
    }
    turnitin_end_session($plagiarismsettings, $tiisession);
    return $result;
}

/**
 * returns link to grademark for a file.
 * this function assumes that another process has already updated the grademark status
 *
 * @param object $plagiarismfile - record from plagiarsim_files table
 * @param object $course - course record
 * @param object $course - module record
 * @param object  $plagiarismsettings - from a call to plagiarism_get_settings
 * @return string - link to grademark function including images.
 */
function turnitin_get_grademark_link($plagiarismfile, $course, $module, $plagiarismsettings) {
    global $DB, $CFG, $OUTPUT, $USER;
    $output = '';
    //first check the grademark status - don't show link if not enabled
    if (empty($plagiarismsettings['turnitin_enablegrademark'])) {
        return $output;
    }
    if (empty($plagiarismfile->externalstatus) ||
       ($USER->id <> $plagiarism_file->userid && !empty($module->timedue) && $module->timedue > time())) {
        //Grademark isn't available yet - don't provide link
        $output = '<img src="'.$OUTPUT->pix_url('i/grademark-grey').'">';
    } else {
        $tii = array();
        if (!has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $plagiarismfile->cm))) {
            $user = $DB->get_record('user', array('id'=>$plagiarismfile->userid));

            $tii['username'] = $user->username;
            $tii['uem']      = $user->email;
            $tii['ufn']      = $user->firstname;
            $tii['uln']      = $user->lastname;
            $tii['uid']      = $user->username;
            $tii['utp'] = '1'; //1 = this user is an student
        } else {
            $tii['username'] = $plagiarismsettings['turnitin_userid'];
            $tii['uem']      = $plagiarismsettings['turnitin_email'];
            $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
            $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
            $tii['uid']      = $plagiarismsettings['turnitin_userid'];
           $tii['utp']      = '2'; //2 = this user is an instructor
        }
        $tii['cid']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //course ID //may need to include sitename in this to allow more than 1 moodle site with the same TII account to use TII API
        $tii['ctl']      = $plagiarismsettings['turnitin_courseprefix'].$course->id.$course->shortname; //Course title.  -this uses Course->id and shortname to ensure uniqueness.
        $tii['fcmd'] = '1'; //when set to 2 the tii api call returns XML
        $tii['fid'] = '13';
        $tii['oid'] = $plagiarismfile->externalid;
        $output = '<a href="'.turnitin_get_url($tii).'"><img src="'.$OUTPUT->pix_url('i/grademark').'"></a>';
    }
    return $output; 
}
/**
 * Function that returns turnaround time for reports from Turnitin
 *
 * @param object  $plagiarismsettings - from a call to plagiarism_get_settings
 * @return xml - xml
 */
function turnitin_get_responsetime($plagiarismsettings) {
    $tii = array();
    //set globals.
    $tii['username'] = $plagiarismsettings['turnitin_userid'];
    $tii['uem']      = $plagiarismsettings['turnitin_email'];
    $tii['ufn']      = $plagiarismsettings['turnitin_firstname'];
    $tii['uln']      = $plagiarismsettings['turnitin_lastname'];
    $tii['uid']      = $plagiarismsettings['turnitin_userid'];
    $tii['utp']      = '2'; //2 = this user is an instructor
    $tii['fcmd']     = '2';
    $tii['fid']     = '14';
    $tiixml = plagiarism_get_xml(turnitin_get_url($tii));
    return $tiixml;
}
