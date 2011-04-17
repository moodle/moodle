<?php
    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);  // sco ID
    $attempt = required_param('attempt', PARAM_INT);  // attempt number
//    $attempt = $SESSION->scorm_attempt;


    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $scorm = $DB->get_record("scorm", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else if (!empty($a)) {
        if (! $scorm = $DB->get_record("scorm", array("id"=>$a))) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$scorm->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {
        print_error('missingparameter');
    }

    $PAGE->set_url('/mod/scorm/datamodel.php', array('scoid'=>$scoid,'attempt'=>$attempt, 'id'=>$cm->id));

    require_login($course->id, false, $cm);

    if (confirm_sesskey() && (!empty($scoid))) {
        $result = true;
        $request = null;
        if (has_capability('mod/scorm:savetrack', get_context_instance(CONTEXT_MODULE,$cm->id))) {
            foreach (data_submitted() as $element => $value) {
                $element = str_replace('__','.',$element);
                if (substr($element,0,3) == 'cmi') {
                    $netelement = preg_replace('/\.N(\d+)\./',"\.\$1\.",$element);
                    $result = scorm_insert_track($USER->id, $scorm->id, $scoid, $attempt, $element, $value, $scorm->forcecompleted) && $result;
                }
                if (substr($element,0,15) == 'adl.nav.request') {
                    // SCORM 2004 Sequencing Request
                    require_once($CFG->dirroot.'/mod/scorm/datamodels/sequencinglib.php');

                    $search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@', '@exit@', '@exitAll@', '@abandon@', '@abandonAll@');
                    $replace = array('continue_', 'previous_', '\1', 'exit_', 'exitall_', 'abandon_', 'abandonall');
                    $action = preg_replace($search, $replace, $value);

                    if ($action != $value) {
                        // Evaluating navigation request
                        $valid = scorm_seq_overall ($scoid,$USER->id,$action,$attempt);
                        $valid = 'true';

                        // Set valid request
                        $search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@');
                        $replace = array('true', 'true', 'true');
                        $matched = preg_replace($search, $replace, $value);
                        if ($matched == 'true') {
                            $request = 'adl.nav.request_valid["'.$action.'"] = "'.$valid.'";';
                        }
                    }
                }
                // Log every datamodel update requested
                if (substr($element,0,15) == 'adl.nav.request' || substr($element,0,3) == 'cmi') {
                    if (scorm_debugging($scorm)) {
                        add_to_log($course->id, 'scorm', 'trk: scoid/'.$scoid.' at: '.$attempt, 'view.php?id='.$cm->id, "$element => $value", $cm->id);
                    }
                }
            }
        }
        if ($result) {
            echo "true\n0";
        } else {
            echo "false\n101";
        }
        if ($request != null) {
            echo "\n".$request;
        }
    }

