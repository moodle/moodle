<?PHP  // $Id$

/// Library of functions and constants for module glossary
/// (replace glossary with the name of your module and delete this line)


$glossary_CONSTANT = 7;     /// for example


function glossary_add_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $glossary->timecreated = time();
    $glossary->timemodified = $glossary->timecreated;

    # May have to add extra stuff in here #

    return insert_record("glossary", $glossary);
}


function glossary_update_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $glossary->timemodified = time();
    $glossary->id = $glossary->instance;

    # May have to add extra stuff in here #

    return update_record("glossary", $glossary);
}


function glossary_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $glossary = get_record("glossary", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records("glossary", "id", "$glossary->id")) {
        $result = false;
    }
    delete_records("glossary_entries", "glossaryid", "$glossary->id");

    return $result;
}

function glossary_user_outline($course, $user, $mod, $glossary) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    return $return;
}

function glossary_user_complete($course, $user, $mod, $glossary) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function glossary_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in glossary activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG, $THEME;

    if (!$logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'glossary' AND ".
                                           "action = 'add %' ", "time ASC")) {
        return false;
    }

	echo "<h1>ANTES</h1>";
    foreach ($logs as $log) {
        //Create a temp valid module structure (course,id)
        $tempmod->course = $log->course;
        $tempmod->id = $log->info;
        //Obtain the visible property from the instance
        $modvisible = instance_is_visible($log->module,$tempmod);
	echo "<h1>ADENTRO => ANTES</h1>";

        //Only if the mod is visible
        if ($modvisible) {
            $entries[$log->info] = glossary_log_info($log);
            $entries[$log->info]->time = $log->time;
            $entries[$log->info]->url  = $log->url;
        }
	echo "<h1>ADENTRO => DESPUES</h1>";
    }

	echo "<h1>DESPUES</h1>";

    $content = false;
    if ($entries) {
        $strftimerecent = get_string("strftimerecent");
        $content = true;
        print_headline(get_string("newentries", "glossary").":");
        foreach ($entries as $entry) {
            $date = userdate($entry->timemodified, $strftimerecent);
            echo "<p><font size=1>$date - $entry->firstname $entry->lastname<br>";
            echo "\"<a href=\"$CFG->wwwroot/mod/glossary/$entry->url\">";
            echo "$entry->concept";
            echo "</a>\"</font></p>";
        }
    }

    return $content;
}

function glossary_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function glossary_grades($glossaryid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.

    $return->grades = NULL;
    $return->maxgrade = NULL;

    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other glossary functions go here.  Each of them must have a name that
/// starts with glossary_

function glossary_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT g.*, u.firstname, u.lastname
                             FROM {$CFG->prefix}glossary_entries g,
                                  {$CFG->prefix}user u
                            WHERE g.glossaryid = '$log->info'
                              AND u.id = '$log->userid'");
}

function glossary_get_entries($glossaryid, $entrylist) {
    global $CFG;

    return get_records_sql("SELECT id,userid,concept,definition,format
                            FROM {$CFG->prefix}glossary_entries
                            WHERE glossaryid = '$glossaryid'
                            AND id IN ($entrylist)");
}

function glossary_print_entry($course, $cm, $glossary, $entry) {
     switch ( $glossary->displayformat ) {
     	case 0:
            echo "<table width=70% border=0><tr><td>";
            glossary_print_entry_with_user($course, $cm, $glossary, $entry);
            echo "</td></tr></table>";
            break;
     	case 1:
            echo "<table width=70% border=0><tr><td>";
            glossary_print_entry_without_user($course, $cm, $glossary, $entry);
            echo "</td></tr></table>";
            break;
        case 2:
//            echo "<table width=70% border=0><tr><td>";
            glossary_print_short_entries($course, $cm, $glossary, $entry);
//            echo "</td></tr></table>";
            break;
     }
}
function glossary_print_entry_with_user($course, $cm, $glossary, $entry) {
    global $THEME, $USER;

//    if ($entry->timemarked < $entry->modified) {
        $colour = $THEME->cellheading2;
//    } else {
//        $colour = $THEME->cellheading;
//    }

    $user = get_record("user", "id", $entry->userid);
    $strby = get_string("writtenby","glossary");

    echo "\n<TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=10>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=2 BGCOLOR=\"$THEME->cellheading\" WIDTH=35 VALIGN=TOP>";
    if ($entry) {
    	print_user_picture($user->id, $course->id, $user->picture);
    }
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$colour\">";
    if ($entry) {
    	echo "<b>$entry->concept</b><br><FONT SIZE=2>$strby $user->firstname $user->lastname</font>";
        echo "&nbsp;&nbsp;<FONT SIZE=1>(".get_string("lastedited").": ".userdate($entry->timemodified).")</FONT></small>";
    }
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";
    if ($entry) {
	  echo format_text($entry->definition, $entry->format);

	  glossary_print_entry_icons($course, $cm, $glossary, $entry);

    } else {
	  echo "<center>";
        print_string("noentry", "glossary");
	  echo "</center>";
    }
    echo "</TD></TR>";

    echo "</TABLE>\n";
}

function glossary_print_entry_without_user($course, $cm, $glossary, $entry) {
    global $THEME, $USER;

//    if ($entry->timemarked < $entry->modified) {
        $colour = $THEME->cellheading2;
//    } else {
//        $colour = $THEME->cellheading;
//    }

    echo "\n<TABLE BORDER=1 CELLSPACING=0 width=100% valign=top cellpadding=10>";

    echo "\n<TR>";
    echo "<TD WIDTH=100% BGCOLOR=\"$colour\"><b>$entry->concept</b><br>";
    if ($entry) {
        echo "&nbsp;&nbsp;<FONT SIZE=1>".get_string("lastedited").": ".userdate($entry->timemodified)."</FONT>";
    }
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";
    if ($entry) {
	  echo format_text($entry->definition, $entry->format);

	  glossary_print_entry_icons($course, $cm, $glossary, $entry);

    } else {
	  echo "<center>";
        print_string("noentry", "glossary");
	  echo "</center>";
    }
    echo "</TD></TR>";

    echo "</TABLE>\n";
}

function glossary_print_short_entries($course, $cm, $glossary, $entry) {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<TR>";
    echo "<TD WIDTH=100% BGCOLOR=\"#FFFFFF\"><b>$entry->concept</b>: ";
    echo format_text($entry->definition, $entry->format);
    glossary_print_entry_icons($course, $cm, $glossary, $entry);
    echo "</td>";
    echo "</TR>";
}

function glossary_print_entry_icons($course, $cm, $glossary, $entry) {
    global $THEME, $USER;

	  if (isteacher($course->id) or $glossary->studentcanpost and $entry->userid == $USER->id) {
 	  	echo "<p align=right>";
		if (isteacher($course->id) and !$glossary->mainglossary) {
			$mainglossary = get_record("glossary","mainglossary",1,"course",$course->id);
			if ( $mainglossary ) {
/*				link_to_popup_window ("$CFG->wwwroot/mod/glossary/exportentry.php?id=$cm->id&entry=$entry->id",
								"popup",
								"<img  alt=\"" . get_string("exporttomainglossary","glossary") . "\"src=\"export.gif\" height=11 width=11 border=0>",
                               			400, 500, get_string("exporttomainglossary","glossary"), "none");
*/

				echo "<a href=\"exportentry.php?id=$cm->id&entry=$entry->id\"><img  alt=\"" . get_string("exporttomainglossary","glossary") . "\"src=\"export.gif\" height=11 width=11 border=0></a> ";

			}
		}
		echo "<a href=\"deleteentry.php?id=$cm->id&mode=delete&entry=$entry->id\"><img  alt=\"" . get_string("delete") . "\"src=\"../../pix/t/delete.gif\" height=11 width=11 border=0></a> ";
	  	echo "<a href=\"edit.php?id=$cm->id&e=$entry->id\"><img  alt=\"" . get_string("edit") . "\" src=\"../../pix/t/edit.gif\" height=11 width=11 border=0></a>";
	  }
}

function glossary_search_entries($searchterms, $glossary, $includedefinition) {
/// Returns a list of entries found using an array of search terms
/// eg   word  +word -word
///

    global $CFG;

    if (!isteacher($glossary->course)) {
        $glossarymodule = get_record("modules", "name", "glossary");
        $onlyvisible = " AND f.id = cm.instance AND cm.visible = 1 AND cm.module = $glossarymodule->id";
        $onlyvisibletable = ", {$CFG->prefix}course_modules cm";
    } else {

        $onlyvisible = "";
        $onlyvisibletable = "";
    }

    /// Some differences in syntax for PostgreSQL
    if ($CFG->dbtype == "postgres7") {
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

    $conceptsearch = "";
    $definitionsearch = "";


    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) < 2) {
            continue;
        }
        if ($conceptsearch) {
            $conceptsearch.= " OR ";
        }
        if ($definitionsearch) {
            $definitionsearch.= " OR ";
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $conceptsearch.= " e.concept $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $definitionsearch .= " e.definition $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $conceptsearch .= " e.concept $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $definitionsearch .= " e.definition $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $conceptsearch .= " e.concept $LIKE '%$searchterm%' ";
            $definitionsearch .= " e.definition $LIKE '%$searchterm%' ";
        }
    }

	if ( !$includedefinition ) {
		$definitionsearch = "0";
	}

    $selectsql = "{$CFG->prefix}glossary_entries e,
                  {$CFG->prefix}glossary g $onlyvisibletable
             WHERE ($conceptsearch OR $definitionsearch)
               AND e.glossaryid = g.id $onlyvisible
		   AND g.id = $glossary->id";

    $totalcount = count_records_sql("SELECT COUNT(*) FROM $selectsql");

	return get_records_sql("SELECT e.concept, e.definition, e.userid, e.timemodified, e.id, e.format  FROM
                            $selectsql ORDER BY e.concept ASC $limit");
}

function glossary_get_participants($glossaryid) {
//Returns the users with data in one glossary
//(users with records in glossary_entries, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}glossary_entries g
                                 WHERE g.glossaryid = '$glossaryid' and
                                       u.id = g.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

?>