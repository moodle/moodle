<?php
    //Prints course's messages info (tables message, message_read and message_contacts)
    function backup_messages ($bf,$preferences) {
        global $CFG, $DB;

        $status = true;

    /// Check we have something to backup
        $unreads = $DB->count_records ('message');
        $reads   = $DB->count_records ('message_read');
        $contacts= $DB->count_records ('message_contacts');

        if ($unreads || $reads || $contacts) {
            $counter = 0;
        /// message open tag
            fwrite ($bf,start_tag("MESSAGES",2,true));

            if ($unreads) {
                $rs_unreads = $DB->get_recordset('message');
            /// Iterate over every unread
                foreach ($rs_unreads as $unread) {
                /// start message
                    fwrite($bf, start_tag("MESSAGE",3,true));
                    fwrite ($bf,full_tag("ID",4,false,$unread->id));
                    fwrite ($bf,full_tag("STATUS",4,false,"UNREAD"));
                    fwrite ($bf,full_tag("USERIDFROM",4,false,$unread->useridfrom));
                    fwrite ($bf,full_tag("USERIDTO",4,false,$unread->useridto));
                    fwrite ($bf,full_tag("MESSAGE",4,false,$unread->message));
                    fwrite ($bf,full_tag("FORMAT",4,false,$unread->format));
                    fwrite ($bf,full_tag("TIMECREATED",4,false,$unread->timecreated));
                    fwrite ($bf,full_tag("MESSAGETYPE",4,false,$unread->messagetype));
                /// end message
                    fwrite ($bf,end_tag("MESSAGE",3,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                $rs_unreads->close();
            }

            if ($reads) {
                $rs_reads = $DB->get_recordset('message_read');
            /// Iterate over every unread
                foreach ($rs_reads as $read) {
                /// start message
                    fwrite($bf, start_tag("MESSAGE",3,true));
                    fwrite ($bf,full_tag("ID",4,false,$read->id));
                    fwrite ($bf,full_tag("STATUS",4,false,"READ"));
                    fwrite ($bf,full_tag("USERIDFROM",4,false,$read->useridfrom));
                    fwrite ($bf,full_tag("USERIDTO",4,false,$read->useridto));
                    fwrite ($bf,full_tag("MESSAGE",4,false,$read->message));
                    fwrite ($bf,full_tag("FORMAT",4,false,$read->format));
                    fwrite ($bf,full_tag("TIMECREATED",4,false,$read->timecreated));
                    fwrite ($bf,full_tag("MESSAGETYPE",4,false,$read->messagetype));
                    fwrite ($bf,full_tag("TIMEREAD",4,false,$read->timeread));
                    fwrite ($bf,full_tag("MAILED",4,false,$read->mailed));
                /// end message
                    fwrite ($bf,end_tag("MESSAGE",3,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                $rs_reads->close();
            }

            if ($contacts) {
                fwrite($bf, start_tag("CONTACTS",3,true));
                $rs_contacts = $DB->get_recordset('message_contacts');
            /// Iterate over every contact
                foreach ($rs_contacts as $contact) {
                /// start contact
                    fwrite($bf, start_tag("CONTACT",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$contact->id));
                    fwrite ($bf,full_tag("USERID",5,false,$contact->userid));
                    fwrite ($bf,full_tag("CONTACTID",5,false,$contact->contactid));
                    fwrite ($bf,full_tag("BLOCKED",5,false,$contact->blocked));
                /// end contact
                    fwrite ($bf,end_tag("CONTACT",4,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                $rs_contacts->close();
                fwrite($bf, end_tag("CONTACTS",3,true));
            }

        /// messages close tag
            $status = fwrite ($bf,end_tag("MESSAGES",2,true));
        }

        return $status;

    }

    //Print blogs info (post table, module=blog, course=0)
    function backup_blogs($bf, $preferences) {
        global $CFG, $DB;

        $status = true;

    /// Check we have something to backup
        $siteblogs = $DB->count_records('post', array('module'=>'blog', 'courseid'=>0));

        if ($siteblogs) {
            $counter = 0;
        /// blogs open tag
            fwrite ($bf, start_tag("BLOGS",2,true));

            if ($siteblogs) {
                $rs_blogs = $DB->get_records('post', array('module'=>'blog', 'courseid'=>0));
            /// Iterate over every blog
                foreach ($rs_blogs as $blog) {
                    backup_blog($bf, $blog->id, 3);

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                $rs_blogs-close();
            }
        /// blogs close tag
            $status = fwrite($bf, end_tag("BLOGS",2,true));
        }

        return $status;
    }


    function backup_blog($bf, $blogid, $level) {
        global $DB;
        $blog = $DB->get_record('post', array('module'=>'blog', 'id'=>$blogid));

                /// start blog
        fwrite($bf, start_tag("BLOG",$level,true));
                /// blog body
        fwrite ($bf,full_tag("ID",$level+1,false,$blog->id));
        fwrite ($bf,full_tag("MODULE",$level+1,false,$blog->module));
        fwrite ($bf,full_tag("USERID",$level+1,false,$blog->userid));
        fwrite ($bf,full_tag("COURSEID",$level+1,false,$blog->courseid));
        fwrite ($bf,full_tag("GROUPID",$level+1,false,$blog->groupid));
        fwrite ($bf,full_tag("MODULEID",$level+1,false,$blog->moduleid));
        fwrite ($bf,full_tag("COURSEMODULEID",$level+1,false,$blog->coursemoduleid));
        fwrite ($bf,full_tag("SUBJECT",$level+1,false,$blog->subject));
        fwrite ($bf,full_tag("SUMMARY",$level+1,false,$blog->summary));
        fwrite ($bf,full_tag("CONTENT",$level+1,false,$blog->content));
        fwrite ($bf,full_tag("UNIQUEHASH",$level+1,false,$blog->uniquehash));
        fwrite ($bf,full_tag("RATING",$level+1,false,$blog->rating));
        fwrite ($bf,full_tag("FORMAT",$level+1,false,$blog->format));
        fwrite ($bf,full_tag("ATTACHMENT",$level+1,false,$blog->attachment));
        fwrite ($bf,full_tag("PUBLISHSTATE",$level+1,false,$blog->publishstate));
        fwrite ($bf,full_tag("LASTMODIFIED",$level+1,false,$blog->lastmodified));
        fwrite ($bf,full_tag("CREATED",$level+1,false,$blog->created));
        fwrite ($bf,full_tag("USERMODIFIED",$level+1,false,$blog->usermodified));

        /// Blog tags
        /// Check if we have blog tags to backup
        if (!empty($CFG->usetags)) {
            if ($tags = tag_get_tags('post', $blog->id)) { //This return them ordered by default
            /// Start BLOG_TAGS tag
                fwrite ($bf,start_tag("BLOG_TAGS",$level+1,true));
            /// Write blog tags fields
                foreach ($tags as $tag) {
                    fwrite ($bf,start_tag("BLOG_TAG",$level+2,true));
                    fwrite ($bf,full_tag("NAME",$level+3,false,$tag->name));
                    fwrite ($bf,full_tag("RAWNAME",$level+3,false,$tag->rawname));
                    fwrite ($bf,end_tag("BLOG_TAG",$level+2,true));
                }
            /// End BLOG_TAGS tag
                fwrite ($bf,end_tag("BLOG_TAGS",$level+1,true));
            }
        }
        /// end blog
        fwrite($bf, end_tag("BLOG",$level,true));
    }

    //Prints course's format data (any data the format might want to save).
    function backup_format_data ($bf,$preferences) {
        global $CFG, $DB;

        // Check course format
        if(!($format = $DB->get_field('course','format', array('id'=>$preferences->backup_course)))) {
                return false;
        }
        // Write appropriate tag. Note that we always put this tag there even if
        // blank, it makes parsing easier
        fwrite ($bf,start_tag("FORMATDATA",2,true));

        $file=$CFG->dirroot."/course/format/$format/backuplib.php";
        if(file_exists($file)) {
            // If the file is there, the function must be or it's an error.
            require_once($file);
            $function=$format.'_backup_format_data';
            if(!function_exists($function)) {
                    return false;
            }
            if(!$function($bf,$preferences)) {
                    return false;
            }
        }

        // This last return just checks the file writing has been ok (ish)
        return fwrite ($bf,end_tag("FORMATDATA",2,true));
    }

    function backup_gradebook_categories_history_info($bf, $preferences) {
        global $CFG, $DB;

        $status = true;

        // find all grade categories history
        if ($chs = $DB->get_records('grade_categories_history', array('courseid'=>$preferences->backup_course))) {
            fwrite ($bf,start_tag("GRADE_CATEGORIES_HISTORIES",3,true));
            foreach ($chs as $ch) {
                fwrite ($bf,start_tag("GRADE_CATEGORIES_HISTORY",4,true));
                fwrite ($bf,full_tag("ID",5,false,$ch->id));
                fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                fwrite ($bf,full_tag("PARENT",5,false,$ch->parent));
                fwrite ($bf,full_tag("DEPTH",5,false,$ch->depth));
                fwrite ($bf,full_tag("PATH",5,false,$ch->path));
                fwrite ($bf,full_tag("FULLNAME",5,false,$ch->fullname));
                fwrite ($bf,full_tag("AGGRETGATION",5,false,$ch->aggregation));
                fwrite ($bf,full_tag("KEEPHIGH",5,false,$ch->keephigh));
                fwrite ($bf,full_tag("DROPLOW",5,false,$ch->droplow));
                fwrite ($bf,full_tag("AGGREGATEONLYGRADED",5,false,$ch->aggregateonlygraded));
                fwrite ($bf,full_tag("AGGREGATEOUTCOMES",5,false,$ch->aggregateoutcomes));
                fwrite ($bf,full_tag("AGGREGATESUBCATS",5,false,$ch->aggregatesubcats));
                fwrite ($bf,end_tag("GRADE_CATEGORIES_HISTORY",4,true));
            }
            $status = fwrite ($bf,end_tag("GRADE_CATEGORIES_HISTORIES",3,true));
        }
        return $status;
    }

    function backup_gradebook_grades_history_info($bf, $preferences) {
        global $CFG, $DB;
        $status = true;

        // find all grade categories history
        if ($chs = $DB->get_records_sql("SELECT ggh.*
                                           FROM {grade_grades_history} ggh
                                                JOIN {grade_item} gi ON gi.id = ggh.itemid
                                          WHERE gi.courseid = ?", array($preferences->backup_course))) {
            fwrite ($bf,start_tag("GRADE_GRADES_HISTORIES",3,true));
            foreach ($chs as $ch) {
            /// Grades are only sent to backup if the user is one target user
                if (backup_getid($preferences->backup_unique_code, 'user', $ch->userid)) {
                    fwrite ($bf,start_tag("GRADE_GRADES_HISTORY",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$ch->id));
                    fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                    fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                    fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                    fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                    fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                    fwrite ($bf,full_tag("ITEMID",5,false,$ch->itemid));
                    fwrite ($bf,full_tag("USERID",5,false,$ch->userid));
                    fwrite ($bf,full_tag("RAWGRADE",5,false,$ch->rawgrade));
                    fwrite ($bf,full_tag("RAWGRADEMAX",5,false,$ch->rawgrademax));
                    fwrite ($bf,full_tag("RAWGRADEMIN",5,false,$ch->rawgrademin));
                    fwrite ($bf,full_tag("RAWSCALEID",5,false,$ch->rawscaleid));
                    fwrite ($bf,full_tag("USERMODIFIED",5,false,$ch->usermodified));
                    fwrite ($bf,full_tag("FINALGRADE",5,false,$ch->finalgrade));
                    fwrite ($bf,full_tag("HIDDEN",5,false,$ch->hidden));
                    fwrite ($bf,full_tag("LOCKED",5,false,$ch->locked));
                    fwrite ($bf,full_tag("LOCKTIME",5,false,$ch->locktime));
                    fwrite ($bf,full_tag("EXPORTED",5,false,$ch->exported));
                    fwrite ($bf,full_tag("OVERRIDDEN",5,false,$ch->overridden));
                    fwrite ($bf,full_tag("EXCLUDED",5,false,$ch->excluded));
                    fwrite ($bf,full_tag("FEEDBACK",5,false,$ch->feedback));
                    fwrite ($bf,full_tag("FEEDBACKFORMAT",5,false,$ch->feedbackformat));
                    fwrite ($bf,full_tag("INFORMATION",5,false,$ch->information));
                    fwrite ($bf,full_tag("INFORMATIONFORMAT",5,false,$ch->informationformat));
                    fwrite ($bf,end_tag("GRADE_GRADES_HISTORY",4,true));
                }
            }
            $status = fwrite ($bf,end_tag("GRADE_GRADES_HISTORIES",3,true));
        }
        return $status;
    }

    function backup_gradebook_items_history_info($bf, $preferences) {
        global $CFG, $DB;
        $status = true;

        // find all grade categories history
        if ($chs = $DB->get_records('grade_items_history', array('courseid'=>$preferences->backup_course))) {
            fwrite ($bf,start_tag("GRADE_ITEM_HISTORIES",3,true));
            foreach ($chs as $ch) {
                fwrite ($bf,start_tag("GRADE_ITEM_HISTORY",4,true));
                fwrite ($bf,full_tag("ID",5,false,$ch->id));
                fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                fwrite ($bf,full_tag("CATEGORYID",5,false,$ch->categoryid));
                fwrite ($bf,full_tag("ITEMNAME",5,false,$ch->itemname));
                fwrite ($bf,full_tag("ITEMTYPE",5,false,$ch->itemtype));
                fwrite ($bf,full_tag("ITEMMODULE",5,false,$ch->itemmodule));
                fwrite ($bf,full_tag("ITEMINSTANCE",5,false,$ch->iteminstance));
                fwrite ($bf,full_tag("ITEMNUMBER",5,false,$ch->itemnumber));
                fwrite ($bf,full_tag("ITEMINFO",5,false,$ch->iteminfo));
                fwrite ($bf,full_tag("IDNUMBER",5,false,$ch->idnumber));
                fwrite ($bf,full_tag("CALCULATION",5,false,$ch->calculation));
                fwrite ($bf,full_tag("GRADETYPE",5,false,$ch->gradetype));
                fwrite ($bf,full_tag("GRADEMAX",5,false,$ch->grademax));
                fwrite ($bf,full_tag("GRADEMIN",5,false,$ch->grademin));
                fwrite ($bf,full_tag("SCALEID",5,false,$ch->scaleid));
                fwrite ($bf,full_tag("OUTCOMEID",5,false,$ch->outcomeid));
                fwrite ($bf,full_tag("GRADEPASS",5,false,$ch->gradepass));
                fwrite ($bf,full_tag("MULTFACTOR",5,false,$ch->multfactor));
                fwrite ($bf,full_tag("PLUSFACTOR",5,false,$ch->plusfactor));
                fwrite ($bf,full_tag("AGGREGATIONCOEF",5,false,$ch->aggregationcoef));
                fwrite ($bf,full_tag("SORTORDER",5,false,$ch->sortorder));
                //fwrite ($bf,full_tag("DISPLAY",7,false,$ch->display));
                //fwrite ($bf,full_tag("DECIMALS",7,false,$ch->decimals));
                fwrite ($bf,full_tag("HIDDEN",5,false,$ch->hidden));
                fwrite ($bf,full_tag("LOCKED",5,false,$ch->locked));
                fwrite ($bf,full_tag("LOCKTIME",5,false,$ch->locktime));
                fwrite ($bf,full_tag("NEEDSUPDATE",5,false,$ch->needsupdate));
                fwrite ($bf,end_tag("GRADE_ITEM_HISTORY",4,true));
            }
            $status = fwrite ($bf,end_tag("GRADE_ITEM_HISTORIES",3,true));

        }
        return $status;
    }

    function backup_gradebook_outcomes_history($bf, $preferences) {
        global $CFG, $DB;
        $status = true;

        // find all grade categories history
        if ($chs = $DB->get_records('grade_outcomes_history', array('courseid'=>$preferences->backup_course))) {
            fwrite ($bf,start_tag("GRADE_OUTCOME_HISTORIES",3,true));
            foreach ($chs as $ch) {
                fwrite ($bf,start_tag("GRADE_OUTCOME_HISTORY",4,true));
                fwrite ($bf,full_tag("ID",5,false,$ch->id));
                fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                fwrite ($bf,full_tag("SHORTNAME",5,false,$ch->shortname));
                fwrite ($bf,full_tag("FULLNAME",5,false,$ch->fullname));
                fwrite ($bf,full_tag("SCALEID",5,false,$ch->scaleid));
                fwrite ($bf,full_tag("DESCRIPTION",5,false,$ch->description));
                fwrite ($bf,end_tag("GRADE_OUTCOME_HISTORY",4,true));
            }
            $status = fwrite ($bf,end_tag("GRADE_OUTCOME_HISTORIES",3,true));
        }
        return $status;
    }

    //Backup events info (course events)
    function backup_events_info($bf,$preferences) {
        global $CFG, $DB;

        $status = true;

        //Counter, points to current record
        $counter = 0;

        //Get events (course events)
        $events = $DB->get_records("event", array("courseid"=>$preferences->backup_course, 'instance'=>0),"id");

        //Pring events header
        if ($events) {
            //Pring events header
            fwrite ($bf,start_tag("EVENTS",2,true));
            //Iterate
            foreach ($events as $event) {
                //Begin event tag
                fwrite ($bf,start_tag("EVENT",3,true));
                //Output event tag
                fwrite ($bf,full_tag("ID",4,false,$event->id));
                fwrite ($bf,full_tag("NAME",4,false,$event->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$event->description));
                fwrite ($bf,full_tag("FORMAT",4,false,$event->format));
                fwrite ($bf,full_tag("GROUPID",4,false,$event->groupid));
                fwrite ($bf,full_tag("USERID",4,false,$event->userid));
                fwrite ($bf,full_tag("REPEATID",4,false,$event->repeatid));
                fwrite ($bf,full_tag("EVENTTYPE",4,false,$event->eventtype));
                fwrite ($bf,full_tag("MODULENAME",4,false,$event->modulename));
                fwrite ($bf,full_tag("TIMESTART",4,false,$event->timestart));
                fwrite ($bf,full_tag("TIMEDURATION",4,false,$event->timeduration));
                fwrite ($bf,full_tag("VISIBLE",4,false,$event->visible));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$event->timemodified));
                //End event tag
                fwrite ($bf,end_tag("EVENT",3,true));
            }
            //End events tag
            $status = fwrite ($bf,end_tag("EVENTS",2,true));
        }
        return $status;
    }
