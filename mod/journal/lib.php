<?PHP // $Id$

$JOURNAL_RATING = array ("3" => get_string("journalrating3", "journal"),
                         "2" => get_string("journalrating2", "journal"),
                         "1" => get_string("journalrating1", "journal") );



// STANDARD MODULE FUNCTIONS /////////////////////////////////////////////////////////

function journal_user_outline($course, $user, $mod, $journal) {
    if ($entry = get_record("journal_entries", "userid", $user->id, "journal", $journal->id)) {

        $numwords = count(preg_split("/\w\b/", $entry->text)) - 1;

        $result->info = get_string("numwords", "", $numwords);
        $result->time = $entry->modified;
        return $result;
    }
    return NULL;
}


function journal_user_complete($course, $user, $mod, $journal) {

    if ($entry = get_record("journal_entries", "userid", $user->id, "journal", $journal->id)) {

        print_simple_box_start();
        if ($entry->modified) {
            echo "<P><FONT SIZE=1>".get_string("lastedited").": ".userdate($entry->modified)."</FONT></P>";
        }
        if ($entry->text) {
            echo format_text($entry->text, $entry->format);
        }
        if ($entry->teacher) {
            journal_print_feedback($course, $entry);
        }
        print_simple_box_end();

    } else {
        print_string("noentry", "journal");
    }
}


function journal_cron () {
// Function to be run periodically according to the moodle cron
// Finds all journal notifications that have yet to be mailed out, and mails them

    global $CFG, $USER;

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($entries = journal_get_unmailed_graded($cutofftime)) {
        $timenow = time();

        foreach ($entries as $entry) {

            echo "Processing journal entry $entry->id\n";

            if (! $user = get_record("user", "id", "$entry->userid")) {
                echo "Could not find user $entry->userid\n";
                continue;
            }

            $USER->lang = $user->lang;

            if (! $course = get_record("course", "id", "$entry->course")) {
                echo "Could not find course $entry->course\n";
                continue;
            }

            if (! isstudent($course->id, $user->id) and !isteacher($course->id, $user->id)) {
                continue;  // Not an active participant
            }

            if (! $teacher = get_record("user", "id", "$entry->teacher")) {
                echo "Could not find teacher $entry->teacher\n";
                continue;
            }


            if (! $mod = get_coursemodule_from_instance("journal", $entry->journal, $course->id)) {
                echo "Could not find course module for journal id $entry->journal\n";
                continue;
            }

            $postsubject = "$course->shortname: Journal feedback: $entry->name";
            $posttext  = "$course->shortname -> Journals -> $entry->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= "$teacher->firstname $teacher->lastname has posted some feedback on your\n";
            $posttext .= "journal entry for '$entry->name'\n\n";
            $posttext .= "You can see it appended to your journal entry:\n";
            $posttext .= "   $CFG->wwwroot/mod/journal/view.php?id=$mod->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($user->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
              "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/journal/index.php?id=$course->id\">Journals</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/journal/view.php?id=$mod->id\">$entry->name</A></FONT></P>";
              $posthtml .= "<HR><FONT FACE=sans-serif>";
              $posthtml .= "<P>$teacher->firstname $teacher->lastname has posted some feedback on your";
              $posthtml .= " journal entry for '<B>$entry->name</B>'</P>";
              $posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/journal/view.php?id=$mod->id\">";
              $posthtml .= "appended to your journal entry</A>.</P></FONT><HR>";
            } else {
              $posthtml = "";
            }

            if (! email_to_user($user, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: Journal cron: Could not send out mail for id $entry->id to user $user->id ($user->email)\n";
            }
            if (! set_field("journal_entries", "mailed", "1", "id", "$entry->id")) {
                echo "Could not update the mailed field for id $entry->id\n";
            }
        }
    }

    return true;
}

function journal_print_recent_activity(&$logs, $isteacher=false) {
    global $CFG, $COURSE_TEACHER_COLOR;

    $content = false;
    $journals = NULL;

    foreach ($logs as $log) {
        if ($log->module == "journal") {
            if ($log->action == "add entry" or $log->action == "update entry") {
                if (!isset($journals[$log->info])) {
                    $journals[$log->info] = journal_log_info($log);
                    $journals[$log->info]->time = $log->time;
                    $journals[$log->info]->url = $log->url;
                }
            }
        }
    }

    if ($journals) {
        $content = true;
        $strftimerecent = get_string("strftimerecent");
        print_headline(get_string("newjournalentries", "journal").":");
        foreach ($journals as $journal) {
            $date = userdate($journal->time, $strftimerecent);
            echo "<P><FONT SIZE=1>$date - $journal->firstname $journal->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/journal/$journal->url\">";
            echo "$journal->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    return $content;
}

function journal_grades($journalid) {
/// Must return an array of grades, indexed by user, and a max grade.
    global $JOURNAL_RATING;

    if ($return->grades = get_records_menu("journal_entries", "journal", $journalid, "", "userid,rating")) {
        foreach ($return->grades as $key => $value) {
            if ($value) {
                $return->grades[$key] = $JOURNAL_RATING[$value];
            } else {
                $return->grades[$key] = "-";
            }
        }
        $return->maxgrade = "";
    }
    return $return;
}


// SQL FUNCTIONS ///////////////////////////////////////////////////////////////////

function journal_get_users_done($journal) {
    global $CFG;
    return get_records_sql("SELECT u.* 
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_students s, 
                                   {$CFG->prefix}user_teachers t, 
                                   {$CFG->prefix}journal_entries j
                             WHERE ((s.course = '$journal->course' AND s.userid = u.id) 
                                OR  (t.course = '$journal->course' AND t.userid = u.id))
                               AND u.id = j.userid 
                               AND j.journal = '$journal->id'
                          ORDER BY j.modified DESC");
}

function journal_get_unmailed_graded($cutofftime) {
    global $CFG;
    return get_records_sql("SELECT e.*, j.course, j.name
                              FROM {$CFG->prefix}journal_entries e, 
                                   {$CFG->prefix}journal j
                             WHERE e.mailed = '0' 
                               AND e.timemarked < '$cutofftime' 
                               AND e.timemarked > 0
                               AND e.journal = j.id");
}

function journal_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT j.*, u.firstname, u.lastname
                             FROM {$CFG->prefix}journal j, 
                                  {$CFG->prefix}journal_entries e, 
                                  {$CFG->prefix}user u
                            WHERE e.id = '$log->info' 
                              AND e.journal = j.id
                              AND e.userid = u.id");
}

// OTHER JOURNAL FUNCTIONS ///////////////////////////////////////////////////////////////////



function journal_print_user_entry($course, $user, $entry, $teachers, $ratings) {
    global $THEME;

    if ($entry->timemarked < $entry->modified) {
        $colour = $THEME->cellheading2;
    } else {
        $colour = $THEME->cellheading;
    }

    echo "\n<TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=10>";
        
    echo "\n<TR>";
    echo "\n<TD ROWSPAN=2 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($user->id, $course->id, $user->picture);
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$colour\">$user->firstname $user->lastname";
    if ($entry) {
        echo "&nbsp;&nbsp;<FONT SIZE=1>".get_string("lastedited").": ".userdate($entry->modified)."</FONT>";
    }
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";
    if ($entry) {
        echo format_text($entry->text, $entry->format);
    } else {
        print_string("noentry", "journal");
    }
    echo "</TD></TR>";

    if ($entry) {
        echo "\n<TR>";
        echo "<TD WIDTH=35 VALIGN=TOP>";
        if (!$entry->teacher) {
            $entry->teacher = $USER->id;
        }
        print_user_picture($entry->teacher, $course->id, $teachers[$entry->teacher]->picture);
        echo "<TD BGCOLOR=\"$colour\">".get_string("feedback").":";
        choose_from_menu($ratings, "r$entry->id", $entry->rating, get_string("rate", "journal")."...");
        if ($entry->timemarked) {
            echo "&nbsp;&nbsp;<FONT SIZE=1>".userdate($entry->timemarked)."</FONT>";
        }
        echo "<BR><TEXTAREA NAME=\"c$entry->id\" ROWS=12 COLS=60 WRAP=virtual>";
        p($entry->comment);
        echo "</TEXTAREA><BR>";
        echo "</TD></TR>";
    }
    echo "</TABLE><BR CLEAR=ALL>\n";
}


function journal_add_instance($journal) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $journal->timemodified = time();

    return insert_record("journal", $journal);
}


function journal_update_instance($journal) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $journal->timemodified = time();
    $journal->id = $journal->instance;

    return update_record("journal", $journal);
}


function journal_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $journal = get_record("journal", "id", $id)) {
        return false;
    }

    $result = true;

    if (! delete_records("journal_entries", "journal", $journal->id)) {
        $result = false;
    }

    if (! delete_records("journal", "id", $journal->id)) {
        $result = false;
    }

    return $result;

}


function journal_print_feedback($course, $entry) {
    global $CFG, $THEME, $JOURNAL_RATING;

    if (! $teacher = get_record("user", "id", $entry->teacher)) {
        error("Weird journal error");
    }

    echo "\n<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 ALIGN=CENTER><TR><TD BGCOLOR=#888888>";
    echo "\n<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 VALIGN=TOP>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=3 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">$teacher->firstname $teacher->lastname";
    echo "&nbsp;&nbsp;<FONT SIZE=2><I>".userdate($entry->timemarked)."</I>";
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";

    echo "<P ALIGN=RIGHT><FONT SIZE=-1><I>";
    if ($JOURNAL_RATING[$entry->rating]) {
        echo get_string("overallrating", "journal").": ";
        echo $JOURNAL_RATING[$entry->rating];
    } else {
        print_string("noratinggiven", "journal");
    }
    echo "</I></FONT></P>";

    echo text_to_html($entry->comment);
    echo "</TD></TR></TABLE>";
    echo "</TD></TR></TABLE>";
}

?>
