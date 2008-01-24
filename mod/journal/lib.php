<?php // $Id$


if (!isset($CFG->journal_showrecentactivity)) {
    set_config("journal_showrecentactivity", true);
} 



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
            echo "<p><font size=\"1\">".get_string("lastedited").": ".userdate($entry->modified)."</font></p>";
        }
        if ($entry->text) {
            echo format_text($entry->text, $entry->format);
        }
        if ($entry->teacher) {
            $grades = make_grades_menu($journal->assessed);
            journal_print_feedback($course, $entry, $grades);
        }
        print_simple_box_end();

    } else {
        print_string("noentry", "journal");
    }
}


function journal_user_complete_index($course, $user, $journal, $journalopen, $heading) {
/// Prints a journal, entry and feedback ... used on the journal index page.

    if (isteacher($course->id)) {
        $entrycount = journal_count_entries($journal, get_current_group($course->id));
        $entryinfo  = "&nbsp;(<a href=\"report.php?id=$journal->coursemodule\">".get_string("viewallentries","journal", $entrycount)."</a>)";
    } else {
        $entryinfo = "";
    }

    $journal->name = "<a href=\"view.php?id=$journal->coursemodule\">".format_string($journal->name,true)."</a>";

    if ($heading) {
        echo "<h3>$heading - $journal->name$entryinfo</h3>";
    } else {
        echo "<h3>$journal->name$entryinfo</h3>";
    }

    print_simple_box_start("left", "90%");
    echo format_text($journal->intro,  $journal->introformat);
    print_simple_box_end();
    echo "<br clear=\"all\" />";
    echo "<br />";
    
    if (isstudent($course->id) or isteacher($course->id)) {

        print_simple_box_start("right", "90%");
    
        if ($journalopen) {
            echo "<p align=\"right\"><a href=\"edit.php?id=$journal->coursemodule\">";
            echo get_string("edit")."</a></p>";
        } else {
            echo "<p align=\"right\"><a href=\"view.php?id=$journal->coursemodule\">";
            echo get_string("view")."</a></p>";
        }
    
        if ($entry = get_record("journal_entries", "userid", $user->id, "journal", $journal->id)) {
            if ($entry->modified) {
                echo "<p align=\"center\"><font size=\"1\">".get_string("lastedited").": ".userdate($entry->modified)."</font></p>";
            }
            if ($entry->text) {
                echo format_text($entry->text, $entry->format);
            }
            if ($entry->teacher) {
                $grades = make_grades_menu($journal->assessed);
                journal_print_feedback($course, $entry, $grades);
            }
        } else {
            print_string("noentry", "journal");
        }
    
        print_simple_box_end();
        echo "<br clear=\"all\" />";
        echo "<br />";
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

            unset($journalinfo);
            $journalinfo->teacher = fullname($teacher);
            $journalinfo->journal = format_string($entry->name,true);
            $journalinfo->url = "$CFG->wwwroot/mod/journal/view.php?id=$mod->id";
            $modnamepl = get_string( 'modulenameplural','journal' );
            $msubject = get_string( 'mailsubject','journal' );

            $postsubject = "$course->shortname: $msubject: ".format_string($entry->name,true);
            $posttext  = "$course->shortname -> $modnamepl -> ".format_string($entry->name,true)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= get_string("journalmail", "journal", $journalinfo)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($user->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
                "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                "<a href=\"$CFG->wwwroot/mod/journal/index.php?id=$course->id\">journals</a> ->".
                "<a href=\"$CFG->wwwroot/mod/journal/view.php?id=$mod->id\">".format_string($entry->name,true)."</a></font></p>";
                $posthtml .= "<hr /><font face=\"sans-serif\">";
                $posthtml .= "<p>".get_string("journalmailhtml", "journal", $journalinfo)."</p>";
                $posthtml .= "</font><hr />";
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

function journal_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    if (!empty($CFG->journal_showrecentactivity)) {    // Don't even bother
        return false;
    }

    $content = false;
    $journals = NULL;

    // log table should not be used here

    if (!$logs = get_records_select('log', 'time > \''.$timestart.'\' AND '.
                                           'course = \''.$course->id.'\' AND '.
                                           'module = \'journal\' AND '.
                                           '(action = \'add entry\' OR action = \'update entry\')', 'time ASC')){
        return false;
    }

    foreach ($logs as $log) {
        ///Get journal info.  I'll need it later
        $j_log_info = journal_log_info($log);

        $cm = $modinfo->instances['journal'][$j_log_info->id];
        if (!$cm->uservisible) {
            continue;
        }

        if (!isset($journals[$log->info])) {
            $journals[$log->info] = $j_log_info;
            $journals[$log->info]->time = $log->time;
            $journals[$log->info]->url = str_replace('&', '&amp;', $log->url);
        }
    }

    if ($journals) {
        $content = true;
        print_headline(get_string('newjournalentries', 'journal').':');
        foreach ($journals as $journal) {
            print_recent_activity_note($journal->time, $journal, $journal->name,
                                       $CFG->wwwroot.'/mod/journal/'.$journal->url);
        }
    }
 
    return $content;
}

function journal_grades($journalid) {
/// Must return an array of grades, indexed by user, and a max grade.

    if (!$journal = get_record("journal", "id", $journalid)) {
        return NULL;
    }

    $grades = get_records_menu("journal_entries", "journal", 
                               $journal->id, "", "userid,rating");

    if ($journal->assessed > 0) {
        $return->grades = $grades;
        $return->maxgrade = $journal->assessed;

    } else if ($journal->assessed == 0) {
        return NULL;

    } else {
        if ($scale = get_record("scale", "id", - $journal->assessed)) {
            $scalegrades = make_menu_from_list($scale->scale);
            if ($grades) {
                foreach ($grades as $key => $grade) {
                    $grades[$key] = $scalegrades[$grade];
                }
            }
        }
        $return->grades = $grades;
        $return->maxgrade = "";
    }

    return $return;
}

function journal_get_participants($journalid) {
//Returns the users with data in one journal
//(users with records in journal_entries, students and teachers)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}journal_entries j
                                 WHERE j.journal = '$journalid' and
                                       u.id = j.userid");
    //Get teachers
    $teachers = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}journal_entries j
                                 WHERE j.journal = '$journalid' and
                                       u.id = j.teacher");

    //Add teachers to students
    if ($teachers) {
        foreach ($teachers as $teacher) {
            $students[$teacher->id] = $teacher;
        }
    }
    //Return students array (it contains an array of unique users)
    return ($students);
}

function journal_scale_used ($journalid,$scaleid) {
//This function returns if a scale is being used by one journal
    
    $return = false;                  
                                 
    $rec = get_record("journal","id","$journalid","assessed","-$scaleid");

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of journal
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any journal
 */
function journal_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('journal', 'assessed', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

// SQL FUNCTIONS ///////////////////////////////////////////////////////////////////

function journal_get_users_done($journal) {
    global $CFG;

    $journals = get_records_sql ("SELECT u.*
                                  FROM {$CFG->prefix}journal_entries j,
                                       {$CFG->prefix}user u 
                                 WHERE j.userid = u.id
                                   AND j.journal = $journal->id
                              ORDER BY j.modified DESC");

    if (empty($journals)) {
        return NULL;
    }

    // remove unenrolled participants
    foreach ($journals as $key=>$user) {
        if (!isteacher($journal->course, $user->id) and !isstudent($journal->course, $user->id)) {
            unset($journals[$key]);
        } 
    }
    return $journals;
}

function journal_count_entries($journal, $groupid=0) {
/// Counts all the journal entries (optionally in a given group)

    global $CFG, $db;

    if ($groupid) {     /// How many in a particular group?
        return count_records_sql("SELECT COUNT(*) 
                                     FROM {$CFG->prefix}journal_entries j,
                                          {$CFG->prefix}groups_members g
                                    WHERE j.journal = $journal->id 
                                      AND g.groupid = '$groupid' 
                                      AND j.userid = g.userid");

    } else { /// Count all the entries from the whole course
    
        $journals = get_records_sql ("SELECT u.*
                                      FROM {$CFG->prefix}journal_entries j,
                                           {$CFG->prefix}user u 
                                     WHERE j.userid = u.id
                                       AND j.journal = $journal->id
                                  ORDER BY j.modified DESC");

        if (empty($journals)) {
            return 0;
        }

        // remove unenrolled participants
        foreach ($journals as $key=>$user) {
            if (!isteacher($journal->course, $user->id) and !isstudent($journal->course, $user->id)) {
                unset($journals[$key]);
            } 
        }
        return count($journals);
    }
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



function journal_print_user_entry($course, $user, $entry, $teachers, $grades) {
    global $USER;

    echo "\n<table border=\"1\" cellspacing=\"0\" valign=\"top\" cellpadding=\"10\">";
        
    echo "\n<tr>";
    echo "\n<td rowspan=\"2\" width=\"35\" valign=\"top\">";
    print_user_picture($user->id, $course->id, $user->picture);
    echo "</td>";
    echo "<td nowrap=\"nowrap\" width=\"100%\">".fullname($user);
    if ($entry) {
        echo "&nbsp;&nbsp;<font size=\"1\">".get_string("lastedited").": ".userdate($entry->modified)."</font>";
    }
    echo "</td>";
    echo "</tr>";

    echo "\n<tr><td width=\"100%\">";
    if ($entry) {
        echo format_text($entry->text, $entry->format);
    } else {
        print_string("noentry", "journal");
    }
    echo "</td></tr>";

    if ($entry) {
        echo "\n<tr>";
        echo "<td width=\"35\" valign=\"top\">";
        if (!$entry->teacher) {
            $entry->teacher = $USER->id;
        }
        print_user_picture($entry->teacher, $course->id, $teachers[$entry->teacher]->picture);
        echo "</td>";
        echo "<td>".get_string("feedback").":";
        choose_from_menu($grades, "r$entry->id", $entry->rating, get_string("nograde")."...");
        if ($entry->timemarked) {
            echo "&nbsp;&nbsp;<font size=\"1\">".userdate($entry->timemarked)."</font>";
        }
        echo "<br /><textarea name=\"c$entry->id\" rows=\"12\" cols=\"60\" wrap=\"virtual\">";
        p($entry->entrycomment);
        echo "</textarea><br />";
        echo "</td></tr>";
    }
    echo "</table><br clear=\"all\" />\n";
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


function journal_print_feedback($course, $entry, $grades) {
    global $CFG;

    if (! $teacher = get_record('user', 'id', $entry->teacher)) {
        error('Weird journal error');
    }

    echo '<table cellspacing="0" align="center" class="feedbackbox">';

    echo '<tr>';
    echo '<td class="left picture">';
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo '</td>';
    echo '<td class="entryheader">';
    echo '<span class="author">'.fullname($teacher).'</span>';
    echo '&nbsp;&nbsp;<span class="time">'.userdate($entry->timemarked).'</span>';
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="left side">&nbsp;</td>';
    echo '<td class="entrycontent">';

    echo '<div class="grade">';
    
    if (!empty($entry->rating) and !empty($grades[$entry->rating])) {
        echo get_string('grade').': ';
        echo $grades[$entry->rating];
    } else {
        print_string('nograde');
    }
    echo '</div>';

    echo format_text($entry->entrycomment);
    echo '</td></tr></table>';
}

function journal_get_view_actions() {
    return array('view','view all','view responses');
}

function journal_get_post_actions() {
    return array('add entry','update entry','update feedback');
}

?>
