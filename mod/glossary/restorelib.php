<?php
    //This php script contains all the stuff to backup/restore
    //glossary mods

    //This is the "graphical" structure of the glossary mod:
    //
    //                     glossary ----------------------------------------- glossary_categories
    //                    (CL,pk->id)                                     (CL,pk->id,fk->glossaryid)
    //                        |                                                       |
    //                        |                                                       |
    //                        |                                                       |
    //                  glossary_entries --------------------------------glossary_entries_categories
    //         (UL,pk->id, fk->glossaryid, files)         |               (UL, pk->categoryid,entryid)
    //                        |                           |
    //                        |                           |--------------------glossary_ratings
    //                        |                           |               (UL, pk->id, pk->entryid)
    //                  glossary_comments                 |
    //              (UL,pk->id, fk->entryid)              |---------------------glossary_alias
    //                                                                     (UL, pk->id, pk->entryid)
    //
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function glossary_restore_logs($restore,$log) {

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
        case "add category":
            if ($log->cmid) {
                //Get the new_id of the glossary_category (to recode the info field)
                $cat = backup_getid($restore->backup_unique_code,"glossary_categories",$log->info);
                if ($cat) {
                    $log->url = "editcategories.php?id=".$log->cmid;
                    $log->info = $cat->new_id;
                    $status = true;
                }
            }
            break;
        case "edit category":
            if ($log->cmid) {
                //Get the new_id of the glossary_category (to recode the info field)
                $cat = backup_getid($restore->backup_unique_code,"glossary_categories",$log->info);
                if ($cat) {
                    $log->url = "editcategories.php?id=".$log->cmid;
                    $log->info = $cat->new_id;
                    $status = true;
                }
            }
            break;
        case "delete category":
            if ($log->cmid) {
                //Get the new_id of the glossary_category (to recode the info field)
                $cat = backup_getid($restore->backup_unique_code,"glossary_categories",$log->info);
                if ($cat) {
                    $log->url = "editcategories.php?id=".$log->cmid;
                    $log->info = $cat->new_id;
                    $status = true;
                }
            }
            break;
        case "add entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "view.php?id=".$log->cmid."&mode=entry&hook=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "update entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "view.php?id=".$log->cmid."&mode=entry&hook=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "delete entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "view.php?id=".$log->cmid."&mode=entry&hook=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "approve entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "showentry.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "view entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "showentry.php?&eid=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "add comment":
            if ($log->cmid) {
                //Extract the entryid from the url field
                $entid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the glossary_entry (to recode the url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$entid);
                //Get the new_id of the glossary_comment (to recode the info field)
                $com = backup_getid($restore->backup_unique_code,"glossary_comments",$log->info);
                if ($ent and $com) {
                    $log->url = "comments.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $com->new_id;
                    $status = true;
                }
            }
            break;
        case "update comment":
            if ($log->cmid) {
                //Extract the entryid from the url field
                $entid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the glossary_entry (to recode the url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$entid);
                //Get the new_id of the glossary_comment (to recode the info field)
                $com = backup_getid($restore->backup_unique_code,"glossary_comments",$log->info);
                if ($ent and $com) {
                    $log->url = "comments.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $com->new_id;
                    $status = true;
                }
            }
            break;
        case "delete comment":
            if ($log->cmid) {
                //Extract the entryid from the url field
                $entid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the glossary_entry (to recode the url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$entid);
                //Get the new_id of the glossary_comment (to recode the info field)
                $com = backup_getid($restore->backup_unique_code,"glossary_comments",$log->info);
                if ($ent and $com) {
                    $log->url = "comments.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $com->new_id;
                    $status = true;
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

