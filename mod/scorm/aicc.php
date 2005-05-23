<?php
    require_once('../../config.php');
    require_once('lib.php');

    require_login($course->id, false, $cm);
    
    if (isset($_POST['command']) && confirm_sesskey($_POST['session_id'])) {
        $command = strtolower($_POST['command']);
       
        if (isset($SESSION->scorm_scoid)) {
            $scoid = $SESSION->scorm_scoid;
        } else {
            error('Invalid script call');
        }
        $mode = 'normal';
        if (isset($SESSION->scorm_mode)) {
            $mode = $SESSION->scorm_mode;
        }
        $status = $SESSION->scorm_status;
        if ($sco = get_record('scorm_scoes','id',$scoid)) {
            if (!$scorm = get_record('scorm','id',$sco->scorm)) {
                error('Invalid script call');
            }
        } else {
            error('Invalid script call');
        }
        if ($scorm = get_record('scorm','id',$sco->scorm)) {
        switch $command {
            case 'getparam':
                if ($status == 'Not Initialized') {
                    $SESSION->scorm_status = 'Running';
                    $status = 'Running';
                }
                if ($status != 'Running') {
                    echo "error = 101\nerror_text = Terminated\n";
                } else {
                    if ($usertrack=scorm_get_tracks($scoid,$USER->id)) {
                        $userdata = $usertrack;
                    } else {
                        $userdata->status = '';
                        $userdata->scorre_raw = '';
                    }
                    $userdata->student_id = $USER->username;
                    $userdata->student_name = $USER->lastname .', '. $USER->firstname;
                    $userdata->mode = $mode;
                
                    if ($sco = get_record('scorm_scoes','id',$scoid)) {
                        $userdata->datafromlms = $sco->datafromlms;
                        $userdata->masteryscore = $sco->masteryscore;
                        $userdata->maxtimeallowed = $sco->maxtimeallowed;
                        $userdata->timelimitaction = $sco->timelimitaction;
                        if (!empty($sco->masteryscore)) {
                            $userdata->credit = 'credit';
                        } else {
                            $userdata->credit = 'no-credit';
                        }    
                        echo "error = 0\nerror_text = Successful\naicc_data=\n";
                        echo "[Core]\n";
                        echo 'Student_ID = '.$userdata->student_id."\n";
                        echo 'Student_Name = '.$userdata->student_name."\n";
                        echo 'Lesson_Location = '.isset($userdata->{'cmi.core.lesson_location'})?$userdata->{'cmi.core.lesson_location'}:''."\n";
                        echo 'Credit = '.$userdata->credit."\n";
                        echo 'Lesson_Status = '.isset($userdata->{'cmi.core.lesson_status'})?$userdata->{'cmi.core.lesson_status'}:''."\n";
                        echo 'Score = '.isset($userdata->{'cmi.core.score.raw'})?$userdata->{'cmi.core.score.raw'}:''."\n";
                        echo 'Time = '.isset($userdata->{'cmi.core.total_time'})?$userdata->{'cmi.core.total_time'}:'00:00:00'."\n";
                        echo "[Core_Lesson]\n".isset($userdata->{'cmi.suspend_data'})?$userdata->{'cmi.suspend_data'}:''."\n";
                        echo "[Core_Vendor]\n".$userdata->datafromlms."\n";
                    } else {
                        error('Sco not found');
                    }
                }
            break;
            case 'putparam':
                if ($status == 'Running') {
                    print_r($_POST['aicc_data']);
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
            case 'putcomments':
                if ($status == 'Running') {
                    print_r($_POST['aicc_data']);
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
            case 'putinteractions':
                if ($status == 'Running') {
                    print_r($_POST['aicc_data']);
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
            case 'putobjectives':
                if ($status == 'Running') {
                    print_r($_POST['aicc_data']);
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
            case 'putpath':
                if ($status == 'Running') {
                    print_r($_POST['aicc_data']);
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
            case 'putperformance':
                if ($status == 'Running') {
                    print_r($_POST['aicc_data']);
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
            case 'exitau':
                if ($status == 'Running') {
                    $SESSION->scorm_status = 'Terminated';                  
                    echo "error = 0\nerror_text = Successful\n";
                }
            break;
        }
    }
?>

