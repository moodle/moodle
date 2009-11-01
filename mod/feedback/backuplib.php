<?php
    //This php script contains all the stuff to backup/restore
    //feedback mods

    //This is the "graphical" structure of the feedback mod:
    //
    //                     feedback---------------------------------feedback_tracking
    //                    (CL,pk->id)                            (UL, pk->id, fk->feedback,completed)
    //                        |                                           |
    //                        |                                           |
    //                        |                                           |
    //                 feedback_template                            feedback_completed
    //                   (CL,pk->id)                           (UL, pk->id, fk->feedback)
    //                        |                                           |
    //                        |                                           |
    //                        |                                           |
    //                 feedback_item---------------------------------feedback_value
    //        (ML,pk->id, fk->feedback, fk->template)       (UL, pk->id, fk->item, fk->completed)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          CL->course level info
    //          ML->modul level info
    //          UL->userid level info
    //          message->text of each feedback_posting
    //
    //-----------------------------------------------------------

    //This function executes all the backup procedure about this mod
   function feedback_backup_mods($bf,$preferences) {
      global $CFG, $DB;

      $status = true;

      //Iterate over feedback table
      $feedbacks = $DB->get_records ("feedback", array("course"=>$preferences->backup_course), "id");
      if ($feedbacks) {
         foreach ($feedbacks as $feedback) {
            if (backup_mod_selected($preferences,'feedback',$feedback->id)) {
               $status = feedback_backup_one_mod($bf,$preferences,$feedback);
            }
         }
      }
      return $status;
   }

   function feedback_backup_one_mod($bf,$preferences,$feedback) {
      global $CFG, $DB;

      if (is_numeric($feedback)) {
         $feedback = $DB->get_record('feedback', array('id'=>$feedback));
      }

      $status = true;
      fwrite ($bf,start_tag("MOD",3,true));
      //Print feedback data
      fwrite ($bf,full_tag("ID",4,false,$feedback->id));
      fwrite ($bf,full_tag("MODTYPE",4,false,"feedback"));
      fwrite ($bf,full_tag("VERSION",4,false,1)); //version 1 steht fuer die neue Version
      fwrite ($bf,full_tag("NAME",4,false,$feedback->name));
      fwrite ($bf,full_tag("SUMMARY",4,false,$feedback->intro));
      fwrite ($bf,full_tag("ANONYMOUS",4,false,$feedback->anonymous));
      fwrite ($bf,full_tag("EMAILNOTIFICATION",4,false,$feedback->email_notification));
      fwrite ($bf,full_tag("MULTIPLESUBMIT",4,false,$feedback->multiple_submit));
      fwrite ($bf,full_tag("AUTONUMBERING",4,false,$feedback->autonumbering));
      fwrite ($bf,full_tag("SITEAFTERSUB",4,false,$feedback->site_after_submit));
      fwrite ($bf,full_tag("PAGEAFTERSUB",4,false,$feedback->page_after_submit));
      fwrite ($bf,full_tag("PUBLISHSTATS",4,false,$feedback->publish_stats));
      fwrite ($bf,full_tag("TIMEOPEN",4,false,$feedback->timeopen));
      fwrite ($bf,full_tag("TIMECLOSE",4,false,$feedback->timeclose));
      fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$feedback->timemodified));

      //backup the items of each feedback
      feedback_backup_data($bf, $preferences, $feedback->id);

      //End mod
      $status =fwrite ($bf,end_tag("MOD",3,true));
      return $status;
   }

   function feedback_backup_data($bf, $preferences, $feedbackid) {
      global $CFG, $DB;

      $status = true;
      $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedbackid));
      if(function_exists('backup_userdata_selected')) { //compatibility-hack for moodle 1.5.x
         $backup_userdata = backup_userdata_selected($preferences,'feedback',$feedbackid);
      }else {
         $backup_userdata = $preferences->mods["feedback"]->userinfo;
      }

      if ($feedbackitems) {
         $status =fwrite ($bf,start_tag("ITEMS",4,true));
         foreach ($feedbackitems as $feedbackitem) {
            //Start item
            fwrite ($bf,start_tag("ITEM",5,true));
            //Print item data
            fwrite ($bf,full_tag("ID",6,false,$feedbackitem->id));
            fwrite ($bf,full_tag("NAME",6,false,$feedbackitem->name));
            fwrite ($bf,full_tag("PRESENTATION",6,false,$feedbackitem->presentation));
            fwrite ($bf,full_tag("TYP",6,false,$feedbackitem->typ));
            fwrite ($bf,full_tag("HASVALUE",6,false,$feedbackitem->hasvalue));
            fwrite ($bf,full_tag("POSITION",6,false,$feedbackitem->position));
            fwrite ($bf,full_tag("REQUIRED",6,false,$feedbackitem->required));

            if ($backup_userdata) {
               //backup the values of items
               $feedbackvalues = $DB->get_records('feedback_value', array('item'=>$feedbackitem->id));
               if($feedbackvalues) {
                  $status =fwrite ($bf,start_tag("FBVALUES",6,true));
                  foreach($feedbackvalues as $feedbackvalue) {
                     //start value
                     fwrite ($bf,start_tag("FBVALUE",7,true));
                     //print value data
                     fwrite ($bf,full_tag("ID",8,false,$feedbackvalue->id));
                     fwrite ($bf,full_tag("ITEM",8,false,$feedbackvalue->item));
                     fwrite ($bf,full_tag("COMPLETED",8,false,$feedbackvalue->completed));
                     fwrite ($bf,full_tag("VAL",8,false,$feedbackvalue->value));
                     fwrite ($bf,full_tag("COURSE_ID",8,false,$feedbackvalue->course_id));
                     //End value
                     $status =fwrite ($bf,end_tag("FBVALUE",7,true));
                  }
                  $status =fwrite ($bf,end_tag("FBVALUES",6,true));
               }
            }
            //End item
            $status =fwrite ($bf,end_tag("ITEM",5,true));
         }
         $status =fwrite ($bf,end_tag("ITEMS",4,true));
      }

      if($backup_userdata) {
         //backup of feedback-completeds
         $feedbackcompleteds = $DB->get_records('feedback_completed', array('feedback'=>$feedbackid));
         if($feedbackcompleteds) {
            fwrite ($bf,start_tag("COMPLETEDS",4,true));
            foreach ($feedbackcompleteds as $feedbackcompleted) {
               //Start completed
               fwrite ($bf,start_tag("COMPLETED",5,true));
               //Print completed data
               fwrite ($bf,full_tag("ID",6,false,$feedbackcompleted->id));
               fwrite ($bf,full_tag("FEEDBACK",6,false,$feedbackcompleted->feedback));
               fwrite ($bf,full_tag("USERID",6,false,$feedbackcompleted->userid));
               fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$feedbackcompleted->timemodified));
               fwrite ($bf,full_tag("RANDOMRESPONSE",6,false,$feedbackcompleted->random_response));
               fwrite ($bf,full_tag("ANONYMOUSRESPONSE",6,false,$feedbackcompleted->anonymous_response));

               //End completed
               $status =fwrite ($bf,end_tag("COMPLETED",5,true));
            }
            $status =fwrite ($bf,end_tag("COMPLETEDS",4,true));
         }

         //backup of tracking-data
         $feedbacktrackings = $DB->get_records('feedback_tracking', array('feedback'=>$feedbackid));
         if($feedbacktrackings) {
            fwrite ($bf,start_tag("TRACKINGS",4,true));
            foreach ($feedbacktrackings as $feedbacktracking) {
               //Start tracking
               fwrite ($bf,start_tag("TRACKING",5,true));
               //Print tracking data
               fwrite ($bf,full_tag("ID",6,false,$feedbacktracking->id));
               fwrite ($bf,full_tag("USERID",6,false,$feedbacktracking->userid));
               fwrite ($bf,full_tag("FEEDBACK",6,false,$feedbacktracking->feedback));
               fwrite ($bf,full_tag("COMPLETED",6,false,$feedbacktracking->completed));

               //End completed
               $status =fwrite ($bf,end_tag("TRACKING",5,true));
            }
            $status =fwrite ($bf,end_tag("TRACKINGS",4,true));
         }

      }

   }


   function feedback_backup_template_data($bf, $templateid, $userinfo) {
      global $CFG, $DB;

      $status = true;
      $templateitems = $DB->get_records('feedback_item', array('template'=>$templateid));

      if ($templateitems) {
         $status =fwrite ($bf,start_tag("ITEMS",5,true));
         foreach ($templateitems as $templateitem) {
            //Start item
            fwrite ($bf,start_tag("ITEM",6,true));
            //Print item data
            fwrite ($bf,full_tag("ID",7,false,$templateitem->id));
            fwrite ($bf,full_tag("NAME",7,false,$templateitem->name));
            fwrite ($bf,full_tag("PRESENTATION",7,false,$templateitem->presentation));
            fwrite ($bf,full_tag("TYP",7,false,$templateitem->typ));
            fwrite ($bf,full_tag("HASVALUE",7,false,$templateitem->hasvalue));
            fwrite ($bf,full_tag("POSITION",7,false,$templateitem->position));
            fwrite ($bf,full_tag("REQUIRED",7,false,$templateitem->required));

            //End item
            $status =fwrite ($bf,end_tag("ITEM",6,true));
         }
         $status =fwrite ($bf,end_tag("ITEMS",5,true));
      }
   }




   //Return an array of info (name,value)
   function feedback_check_backup_mods($course,$user_data=false,$backup_unique_code, $instances=null) {
      if (!empty($instances) && is_array($instances) && count($instances)) {
         $info = array();
         foreach ($instances as $id => $instance) {
            $info += feedback_check_backup_mods_instances($instance,$backup_unique_code);
         }
         return $info;
      }
      //First the course data
      $info[0][0] = get_string("modulenameplural","feedback");
      $info[0][1] = feedback_count($course);

      //Now, if requested, the user_data

      if ($user_data) {
         $info[1][0] = get_string('ready_feedbacks','feedback');
         $info[1][1] = feedback_completed_count($course);
      }

      return $info;
   }

   ////Return an array of info (name,value)
   function feedback_check_backup_mods_instances($instance,$backup_unique_code) {
      global $DB;

      //First the course data
      $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
      $info[$instance->id.'0'][1] = '';

      //Now, if requested, the user_data
      if (!empty($instance->userdata)) {
         $info[$instance->id.'1'][0] = get_string("responses","feedback");
         if ($responses_count = $DB->count_records ('feedback_completed', array('feedback'=>$instance->id))) {
               $info[$instance->id.'1'][1] = $responses_count;
         } else {
               $info[$instance->id.'1'][1] = 0;
         }
      }
      return $info;
   }

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
//// INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

   //Returns an array of feedbacks ids
   function feedback_count ($course) {
      global $DB;

      return $DB->count_records('feedback', array('course'=>$course));
   }

   function feedback_completed_count($course) {
      global $DB;

      $count = 0;
      //get all feedbacks
      $feedbacks = $DB->get_records('feedback', array('course'=>$course));
      if($feedbacks) {
         foreach($feedbacks as $feedback) {
            $count += $DB->count_records('feedback_completed', array('feedback'=>$feedback->id));
         }
      }
      return $count;
   }


