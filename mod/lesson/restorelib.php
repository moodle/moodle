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
 * This php script contains all the stuff to restore lesson mods
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

    //This is the "graphical" structure of the lesson mod:
    //
    //           lesson ----------------------------|--------------------------|--------------------------|
    //        (CL,pk->id)                           |                          |                          |
    //              |                               |                          |                          |
    //              |                         lesson_grades              lesson_high_scores         lesson_timer
    //              |                  (UL, pk->id,fk->lessonid)    (UL, pk->id,fk->lessonid)   (UL, pk->id,fk->lessonid)
    //              |
    //              |
    //       lesson_pages---------------------------|
    //   (CL,pk->id,fk->lessonid)                   |
    //              |                               |
    //              |                         lesson_branch
    //              |                   (UL, pk->id,fk->pageid)
    //        lesson_answers
    //     (CL,pk->id,fk->pageid)
    //              |
    //              |
    //              |
    //        lesson_attempts
    //   (UL,pk->id,fk->answerid)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

defined('MOODLE_INTERNAL') || die();


    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function lesson_restore_logs($restore,$log) {

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
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "start":
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
        case "end":
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
                //Get the new_id of the page (to recode the url field)
                $pag = backup_getid($restore->backup_unique_code,"lesson_pages",$log->info);
                if ($pag) {
                    $log->url = "view.php?id=".$log->cmid."&action=navigation&pageid=".$pag->new_id;
                    $log->info = $pag->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br/>";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }

