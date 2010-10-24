<?php

    //This function returns a log record with all the necessary transformations
    //done. It's used by restore_log_module() to restore modules log.
    function quiz_restore_logs($restore,$log) {

        $status = false;

        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "submit":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "review":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "editquestions":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "preview":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "attempt.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "start attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "close attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "continue attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "continue attemp":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $log->action = "continue attempt";  //To recover some bad actions
                        $status = true;
                    }
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
