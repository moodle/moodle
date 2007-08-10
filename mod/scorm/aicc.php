<?php
    require_once('../../config.php');
    require_once('locallib.php');
    
    $command = required_param('command', PARAM_ALPHA);
    $sessionid = required_param('session_id', PARAM_ALPHANUM);
    $aiccdata = optional_param('aicc_data', '', PARAM_RAW);

    require_login();
    
    if (!empty($command) && confirm_sesskey($sessionid)) {
        $command = strtolower($command);
       
        if (isset($SESSION->scorm_scoid)) {
            $scoid = $SESSION->scorm_scoid;
        } else {
            error('Invalid script call');
        }
        $mode = 'normal';
        if (isset($SESSION->scorm_mode)) {
            $mode = $SESSION->scorm_mode;
        }
        $status = 'Not Initialized';
        if (isset($SESSION->scorm_status)) {
            $status = $SESSION->scorm_status;
        }
        if (isset($SESSION->attempt)) {
            $attempt = $SESSION->attempt;
        } else {
            $attempt = 1;
        }

        if ($sco = scorm_get_sco($scoid, SCO_ONLY)) {
            if (!$scorm = get_record('scorm','id',$sco->scorm)) {
                error('Invalid script call');
            }
        } else {
            error('Invalid script call');
        }

        if ($scorm = get_record('scorm','id',$sco->scorm)) {
            switch ($command) {
                case 'getparam':
                    if ($status == 'Not Initialized') {
                        $SESSION->scorm_status = 'Running';
                        $status = 'Running';
                    }
                    if ($status != 'Running') {
                        echo "error = 101\nerror_text = Terminated\n";
                    } else {
                        if ($usertrack=scorm_get_tracks($scoid,$USER->id,$attempt)) {
                            $userdata = $usertrack;
                        } else {
                            $userdata->status = '';
                            $userdata->score_raw = '';
                        }
                        $userdata->student_id = $USER->username;
                        $userdata->student_name = $USER->lastname .', '. $USER->firstname;
                        $userdata->mode = $mode;
                        if ($userdata->mode == 'normal') {
                            $userdata->credit = 'credit';
                        } else {
                            $userdata->credit = 'no-credit';
                        } 
                
                        if ($sco = scorm_get_sco($scoid)) {
                            $userdata->course_id = $sco->identifier;
                            $userdata->datafromlms = isset($sco->datafromlms)?$sco->datafromlms:'';
                            $userdata->masteryscore = isset($sco->masteryscore)?$sco->masteryscore:'';
                            $userdata->maxtimeallowed = isset($sco->maxtimeallowed)?$sco->maxtimeallowed:'';
                            $userdata->timelimitaction = isset($sco->timelimitaction)?$sco->timelimitaction:'';
                               
                            echo "error = 0\nerror_text = Successful\naicc_data=\n";
                            echo "[Core]\n";
                            echo 'Student_ID = '.$userdata->student_id."\n";
                            echo 'Student_Name = '.$userdata->student_name."\n";
                            if (isset($userdata->{'cmi.core.lesson_location'})) {
                                echo 'Lesson_Location = '.$userdata->{'cmi.core.lesson_location'}."\n";
                            } else {
                                echo 'Lesson_Location = '."\n";
                            }
                            echo 'Credit = '.$userdata->credit."\n";
                            if (isset($userdata->status)) {
                                if ($userdata->status == '') {
                                    $userdata->entry = ', ab-initio';
                                } else {
                                    if (isset($userdata->{'cmi.core.exit'}) && ($userdata->{'cmi.core.exit'} == 'suspend')) {
                                        $userdata->entry = ', resume';
                                    } else {
                                        $userdata->entry = '';
                                    }
                                }
                            }
                            if (isset($userdata->{'cmi.core.lesson_status'})) {
                                echo 'Lesson_Status = '.$userdata->{'cmi.core.lesson_status'}.$userdata->entry."\n";
                                $SESSION->scorm_lessonstatus = $userdata->{'cmi.core.lesson_status'};
                            } else {
                                echo 'Lesson_Status = not attempted'.$userdata->entry."\n";
                                $SESSION->scorm_lessonstatus = 'not attempted';
                            }
                            if (isset($userdata->{'cmi.core.score.raw'})) {
                                $max = '';
                                $min = '';
                                if (isset($userdata->{'cmi.core.score.max'}) && !empty($userdata->{'cmi.core.score.max'})) {
                                    $max = ', '.$userdata->{'cmi.core.score.max'};
                                    if (isset($userdata->{'cmi.core.score.min'}) && !empty($userdata->{'cmi.core.score.min'})) {
                                        $min = ', '.$userdata->{'cmi.core.score.min'};
                                    }
                                }
                                echo 'Score = '.$userdata->{'cmi.core.score.raw'}.$max.$min."\n";
                            } else {
                                echo 'Score = '."\n";
                            }
                            if (isset($userdata->{'cmi.core.total_time'})) {
                                echo 'Time = '.$userdata->{'cmi.core.total_time'}."\n";
                            } else {
                                echo 'Time = '.'00:00:00'."\n";
                            }
                            echo 'Lesson_Mode = '.$userdata->mode."\n";
                            if (isset($userdata->{'cmi.suspend_data'})) {
                                echo "[Core_Lesson]\n".$userdata->{'cmi.suspend_data'}."\n";
                            } else {
                                echo "[Core_Lesson]\n"."\n";
                            }
                            echo "[Core_Vendor]\n".$userdata->datafromlms."\n";
                            echo "[Evaluation]\nCourse_ID = {".$userdata->course_id."}\n";
                            echo "[Student_Data]\n";
                            echo 'Mastery_Score = '.$userdata->masteryscore."\n";
                            echo 'Max_Time_Allowed = '.$userdata->maxtimeallowed."\n";
                            echo 'Time_Limit_Action = '.$userdata->timelimitaction."\n";
                        } else {
                            error('Sco not found');
                        }
                    }
                break;
                case 'putparam':
                    if ($status == 'Running') {
                        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $scorm->course)) {
                            echo "error = 1\nerror_text = Unknown\n"; // No one must see this error message if not hacked
                        }
                        if (!empty($aiccdata) && has_capability('mod/scorm:savetrack', get_context_instance(CONTEXT_MODULE, $cm->id))) {
                            $initlessonstatus = 'not attempted';
                            $lessonstatus = 'not attempted';
                            if (isset($SESSION->scorm_lessonstatus)) {
                                $initlessonstatus = $SESSION->scorm_lessonstatus;
                            }
                            $score = '';
                            $datamodel['lesson_location'] = 'cmi.core.lesson_location';
                            $datamodel['lesson_status'] = 'cmi.core.lesson_status';
                            $datamodel['score'] = 'cmi.core.score.raw';
                            $datamodel['time'] = 'cmi.core.session_time';
                            $datamodel['[core_lesson]'] = 'cmi.suspend_data';
                            $datamodel['[comments]'] = 'cmi.comments';
                            $datarows = explode("\n",$aiccdata);
                            reset($datarows);
                            while ((list(,$datarow) = each($datarows)) !== false) {
                                if (($equal = strpos($datarow, '=')) !== false) {
                                    $element = strtolower(trim(substr($datarow,0,$equal)));
                                    $value = trim(substr($datarow,$equal+1));
                                    if (isset($datamodel[$element])) {
                                        $element = $datamodel[$element];
                                        switch ($element) {
                                            case 'cmi.core.lesson_location':
                                                $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
                                            break;
                                            case 'cmi.core.lesson_status':
                                                $statuses = array(
                                                           'passed' => 'passed',
                                                           'completed' => 'completed',
                                                           'failed' => 'failed',
                                                           'incomplete' => 'incomplete',
                                                           'browsed' => 'browsed',
                                                           'not attempted' => 'not attempted',
                                                           'p' => 'passed',
                                                           'c' => 'completed',
                                                           'f' => 'failed',
                                                           'i' => 'incomplete',
                                                           'b' => 'browsed',
                                                           'n' => 'not attempted'
                                                           );
                                                $exites = array(
                                                           'logout' => 'logout',
                                                           'time-out' => 'time-out',
                                                           'suspend' => 'suspend',
                                                           'l' => 'logout',
                                                           't' => 'time-out',
                                                           's' => 'suspend',
                                                           );
                                                $values = explode(',',$value);
                                                $value = '';
                                                if (count($values) > 1) {
                                                    $value = trim(strtolower($values[1]));
                                                    if (isset($exites[$value])) {
                                                        $value = $exites[$value];
                                                    }
                                                }
                                                if (empty($value) || isset($exites[$value])) {
                                                    $subelement = 'cmi.core.exit';
                                                    $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $subelement, $value);
                                                }
                                                $value = trim(strtolower($values[0]));
                                                if (isset($statuses[$value]) && ($mode == 'normal')) {
                                                    $value = $statuses[$value];
                                                    $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
                                                }
                                                $lessonstatus = $value;
                                            break;
                                            case 'cmi.core.score.raw':
                                                 $values = explode(',',$value);
                                                 if ((count($values) > 1) && ($values[1] >= $values[0]) && is_numeric($values[1])) {
                                                     $subelement = 'cmi.core.score.max';
                                                     $value = trim($values[1]);
                                                     $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $subelement, $value);
                                                     if ((count($values) == 3) && ($values[2] <= $values[0]) && is_numeric($values[2])) {
                                                         $subelement = 'cmi.core.score.min';
                                                         $value = trim($values[2]);
                                                         $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $subelement, $value);
                                                     }
                                                 }
                                              
                                                 $value = '';
                                                 if (is_numeric($values[0])) {
                                                     $value = trim($values[0]);
                                                     $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
                                                 }
                                                 $score = $value;
                                            break;
                                            case 'cmi.core.session_time':
                                                 $SESSION->scorm_session_time = $value;
                                            break;
                                        }
                                    }
                                } else {
                                    if (isset($datamodel[strtolower(trim($datarow))])) {
                                        $element = $datamodel[strtolower(trim($datarow))];
                                        $value = '';
                                        while ((($datarow = current($datarows)) !== false) && (substr($datarow,0,1) != '[')) {
                                            $value .= $datarow;
                                            next($datarows);
                                        }
                                        $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, $element, $value);
                                    }
                                }                               
                            }
                            if (($mode == 'browse') && ($initlessonstatus == 'not attempted')){
                                $lessonstatus = 'browsed';
                                $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, 'cmi.core.lesson_status', 'browsed');
                            }
                            if ($mode == 'normal') {
                                if ($lessonstatus == 'completed') {
                                    if (!empty($sco->masteryscore) && !empty($score) && ($score >= $sco->masteryscore)) {
                                        $lessonstatus = 'passed';
                                    } else {
                                        $lessonstatus = 'failed';
                                    }
                                    $id = scorm_insert_track($USER->id, $scorm->id, $sco->id, $attempt, 'cmi.core.lesson_status', $lessonstatus);
                                }
                            }                  
                        }
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                case 'putcomments':
                    if ($status == 'Running') {
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                case 'putinteractions':
                    if ($status == 'Running') {
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                case 'putobjectives':
                    if ($status == 'Running') {
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                case 'putpath':
                    if ($status == 'Running') {
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                case 'putperformance':
                    if ($status == 'Running') {
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                case 'exitau':
                    if ($status == 'Running') {
                        if (isset($SESSION->scorm_session_time) && ($SESSION->scorm_session_time != '')) {
                            if ($track = get_record_select('scorm_scoes_track',"userid='$USER->id' AND scormid='$scorm->id' AND scoid='$sco->id' AND element='cmi.core.total_time'")) {
                                // Add session_time to total_time
                                $value = scorm_add_time($track->value, $SESSION->scorm_session_time);
                                $track->value = $value;
                                $track->timemodified = time();
                                update_record('scorm_scoes_track',$track);
                                $id = $track->id;
                            } else {
                                $track = new object();
                                $track->userid = $USER->id;
                                $track->scormid = $scorm->id;
                                $track->scoid = $sco->id;
                                $track->element = 'cmi.core.total_time';
                                $track->value = $SESSION->scorm_session_time;
                                $track->timemodified = time();
                                $id = insert_record('scorm_scoes_track',$track);
                            }
                            scorm_update_grades($scorm, $USER->id);
                        }
                        
                        $SESSION->scorm_status = 'Terminated';
                        $SESSION->scorm_session_time = '';
                        echo "error = 0\nerror_text = Successful\n";
                    } else if ($status == 'Terminated') {
                        echo "error = 1\nerror_text = Terminated\n";
                    } else {
                        echo "error = 1\nerror_text = Not Initialized\n";
                    }
                break;
                default:
                    echo "error = 1\nerror_text = Invalid Command\n";
                break;
            }
        }
    } else {
        if (empty($command)) {
            echo "error = 1\nerror_text = Invalid Command\n";
        } else {
            echo "error = 3\nerror_text = Invalid Session ID\n";
        }
    }
?>
