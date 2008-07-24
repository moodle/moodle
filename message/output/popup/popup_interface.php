<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * This script returns the popup messages that need to be shown.
 * FIXME: this is just a temp script to show how it could be done 
 * (this should be integrated with my new YUI interface)
 *
 * @author Luis Rodrigues
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package 
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');


$processor = $DB->get_record('message_processors', array('name' => 'popup'));

$messagesproc = $DB->get_records('message_working', array('processorid'=>$processor->id));

//for every message to process check if it's for current user and process
foreach ($messagesproc as $msgp){
    $message = $DB->get_record('message', array('id'=>$msgp->unreadmessageid, 'useridto'=>$USER->id));
    if (!$message){
        continue;
    }

    //this is the show for now --> SHOULD BE SOMETHING ELSE...
    echo "Usr from: ".$message->useridfrom." to: ".$message->useridto." subject:".$message->subject."<br>";

    /// Move the entry to the other table
    $message->timeread = time();
    $messageid = $message->id;
    unset($message->id);

    //delete what we've processed and check if can move message
    $DB->delete_records('message_working', array('id' => $msgp->id));
    if ( $DB->count_records('message_working', array('unreadmessageid'=>$messageid)) == 0){
        if ($DB->insert_record('message_read', $message)) {
            $DB->delete_records('message', array('id' => $messageid));
        }
    }

}

?>
