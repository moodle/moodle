<?php
    //This php script contains all the stuff to backup/restore
    //feedback mods

    //This is the "graphical" structure of the feedback mod:
    //
    //                            feedback---------------------------------feedback_tracking
    //                          (CL,pk->id)                                     (UL, pk->id, fk->feedback,completed)
    //                                |                                                         |
    //                                |                                                         |
    //                                |                                                         |
    //                      feedback_template                                     feedback_completed
    //                         (CL,pk->id)                                    (UL, pk->id, fk->feedback)
    //                                |                                                         |
    //                                |                                                         |
    //                                |                                                         |
    //                      feedback_item---------------------------------feedback_value
    //          (ML,pk->id, fk->feedback, fk->template)         (UL, pk->id, fk->item, fk->completed)
    //
    // Meaning: pk->primary key field of the table
    //             fk->foreign key to link with parent
    //             CL->course level info
    //             ML->modul level info
    //             UL->userid level info
    //             message->text of each feedback_posting
    //
    //-----------------------------------------------------------


    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function feedback_restore_logs($restore,$log) {

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
        case "add entry":
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
        case "update entry":
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
        case "view responses":
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
        case "update feedback":
            if ($log->cmid) {
                $log->url = "report.php?id=".$log->cmid;
                $status = true;
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                      //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }


