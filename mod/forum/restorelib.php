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
 * This php script contains all the stuff to backup/restore forum mods
 *
 * @package mod-forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    //This is the "graphical" structure of the forum mod:
    //
    //                               forum
    //                            (CL,pk->id)
    //                                 |
    //         ---------------------------------------------------
    //         |                                                 |
    //    subscriptions                                  forum_discussions
    //(UL,pk->id, fk->forum)           ---------------(UL,pk->id, fk->forum)
    //                                 |                         |
    //                                 |                         |
    //                                 |                         |
    //                                 |                     forum_posts
    //                                 |-------------(UL,pk->id,fk->discussion,
    //                                 |                  nt->parent,files)
    //                                 |
    //                                 |
    //                                 |
    //                            forum_read
    //                       (UL,pk->id,fk->post
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
    function forum_restore_logs($restore,$log) {
        global $DB;

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
        case "mark read":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url and info fields)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?f=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "start tracking":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url and info fields)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?f=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "stop tracking":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url and info fields)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?f=".$mod->new_id;
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
        case "view forum":
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
        case "view forums":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "subscribeall":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "unsubscribeall":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "subscribe":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info and url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?f=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view subscriber":
        case "view subscribers":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info and field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "subscribers.php?id=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "unsubscribe":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info and url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?f=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "add discussion":
            if ($log->cmid) {
                //Get the new_id of the discussion (to recode the info and url field)
                $dis = backup_getid($restore->backup_unique_code,"forum_discussions",$log->info);
                if ($dis) {
                    $log->url = "discuss.php?d=".$dis->new_id;
                    $log->info = $dis->new_id;
                    $status = true;
                }
            }
            break;
        case "view discussion":
            if ($log->cmid) {
                //Get the new_id of the discussion (to recode the info and url field)
                $dis = backup_getid($restore->backup_unique_code,"forum_discussions",$log->info);
                if ($dis) {
                    $log->url = "discuss.php?d=".$dis->new_id;
                    $log->info = $dis->new_id;
                    $status = true;
                }
            }
            break;
        case "move discussion":
            if ($log->cmid) {
                //Get the new_id of the discussion (to recode the info and url field)
                $dis = backup_getid($restore->backup_unique_code,"forum_discussions",$log->info);
                if ($dis) {
                    $log->url = "discuss.php?d=".$dis->new_id;
                    $log->info = $dis->new_id;
                    $status = true;
                }
            }
            break;
        case "delete discussi":
        case "delete discussion":
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
        case "add post":
            if ($log->cmid) {
                //Get the new_id of the post (to recode the url and info field)
                $pos = backup_getid($restore->backup_unique_code,"forum_posts",$log->info);
                if ($pos) {
                    //Get the post record from database
                    $dbpos = $DB->get_record("forum_posts", array("id"=>$pos->new_id));
                    if ($dbpos) {
                        $log->url = "discuss.php?d=".$dbpos->discussion."&parent=".$pos->new_id;
                        $log->info = $pos->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "prune post":
            if ($log->cmid) {
                //Get the new_id of the post (to recode the url and info field)
                $pos = backup_getid($restore->backup_unique_code,"forum_posts",$log->info);
                if ($pos) {
                    //Get the post record from database
                    $dbpos = $DB->get_record("forum_posts", array("id"=>$pos->new_id));
                    if ($dbpos) {
                        $log->url = "discuss.php?d=".$dbpos->discussion;
                        $log->info = $pos->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "update post":
            if ($log->cmid) {
                //Get the new_id of the post (to recode the url and info field)
                $pos = backup_getid($restore->backup_unique_code,"forum_posts",$log->info);
                if ($pos) {
                    //Get the post record from database
                    $dbpos = $DB->get_record("forum_posts", array("id"=>$pos->new_id));
                    if ($dbpos) {
                        $log->url = "discuss.php?d=".$dbpos->discussion."&parent=".$pos->new_id;
                        $log->info = $pos->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "delete post":
            if ($log->cmid) {
                //Extract the discussion id from the url field
                $disid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the discussion (to recode the url field)
                $dis = backup_getid($restore->backup_unique_code,"quiz_discussions",$disid);
                if ($dis) {
                    $log->url = "discuss.php?d=".$dis->new_id;
                    $status = true;
                }
            }
            break;
        case "user report":
            //Extract mode from url
            $mode = substr(strrchr($log->url,"="),1);
            //Get new user id
            if ($use = backup_getid($restore->backup_unique_code, 'user', $log->info)) {
                $log->url = 'user.php?course=' . $log->course . '&id=' . $use->new_id . '&mode=' . $mode;
                $log->info = $use->new_id;
                $status = true;
            }
            break;
        case "search":
            $log->url = "search.php?id=".$log->course."&search=".urlencode($log->info);
            $status = true;
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

