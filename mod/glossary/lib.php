<?PHP  // $Id$

/// Library of functions and constants for module glossary
/// (replace glossary with the name of your module and delete this line)

require_once("$CFG->dirroot/files/mimetypes.php");

define("GLOSSARY_SHOW_ALL_CATEGORIES", 0);
define("GLOSSARY_SHOW_NOT_CATEGORISED", -1);

define("GLOSSARY_NO_VIEW", -1);
define("GLOSSARY_STANDARD_VIEW", 0);
define("GLOSSARY_CATEGORY_VIEW", 1);
define("GLOSSARY_DATE_VIEW", 2);
define("GLOSSARY_AUTHOR_VIEW", 3);
define("GLOSSARY_ADDENTRY_VIEW", 4);
define("GLOSSARY_IMPORT_VIEW", 5);
define("GLOSSARY_EXPORT_VIEW", 6);
define("GLOSSARY_APPROVAL_VIEW", 7);

define("GLOSSARY_FORMAT_SIMPLE", 0);
define("GLOSSARY_FORMAT_CONTINUOUS", 1);

function glossary_add_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    if (!$glossary->userating) {
        $glossary->assessed = 0;
    }

    if (!empty($glossary->ratingtime)) {
        $glossary->assesstimestart  = make_timestamp($glossary->startyear, $glossary->startmonth, $glossary->startday, 
                                                  $glossary->starthour, $glossary->startminute, 0);
        $glossary->assesstimefinish = make_timestamp($glossary->finishyear, $glossary->finishmonth, $glossary->finishday, 
                                                  $glossary->finishhour, $glossary->finishminute, 0);
    } else {
        $glossary->assesstimestart  = 0;
        $glossary->assesstimefinish = 0;
    }

    if ( !isset($glossary->globalglossary) ) {
        $glossary->globalglossary = 0;
    } elseif ( !isadmin() ) {
        $glossary->globalglossary = 0;
    }

    $glossary->timecreated = time();
    $glossary->timemodified = $glossary->timecreated;

    # May have to add extra stuff in here #

    return insert_record("glossary", $glossary);
}


function glossary_update_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.
global $CFG;
    if ( !isadmin() ) {
        unset($glossary->globalglossary);
    }
    if (empty($glossary->globalglossary)) {
        $glossary->globalglossary = 0;
    }

    $glossary->timemodified = time();
    $glossary->id = $glossary->instance;

    if (!$glossary->userating) {
        $glossary->assessed = 0;
    }

    if (!empty($glossary->ratingtime)) {
        $glossary->assesstimestart  = make_timestamp($glossary->startyear, $glossary->startmonth, $glossary->startday, 
                                                  $glossary->starthour, $glossary->startminute, 0);
        $glossary->assesstimefinish = make_timestamp($glossary->finishyear, $glossary->finishmonth, $glossary->finishday, 
                                                  $glossary->finishhour, $glossary->finishminute, 0);
    } else {
        $glossary->assesstimestart  = 0;
        $glossary->assesstimefinish = 0;
    }

    $return = update_record("glossary", $glossary);
	if ($return and $glossary->defaultapproval) {
        execute_sql("update {$CFG->prefix}glossary_entries SET approved = 1 where approved != 1 and glossaryid = " . $glossary->id,false);
    }

    return $return;
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
    } else {
        if ($categories = get_records("glossary_categories","glossaryid",$glossary->id)) {
            $cats = "";
            foreach ( $categories as $hook ) {
                $cats .= "$cat->id,";
            }
            $cats = substr($cats,0,-1);
            if ($cats) {
                delete_records_select("glossary_entries_categories", "categoryid in ($cats)");
                delete_records("glossary_categories", "glossaryid", $glossary->id);
            }
        }
        if ( $entries = get_records("glossary_entries", "glossaryid", $glossary->id) ) {
            $ents = "";
            foreach ( $entries as $entry ) {
                if ( $entry->sourceglossaryid ) {
                    $entry->glossaryid = $entry->sourceglossaryid;
                    $entry->sourceglossaryid = 0;
                    update_record("glossary_entries",$entry);
                } else {
                    $ents .= "$entry->id,";
                }
            }
            $ents = substr($ents,0,-1);
            if ($ents) {
                delete_records_select("glossary_comments", "entryid in ($ents)");
                delete_records_select("glossary_alias", "entryid in ($ents)");
                delete_records_select("glossary_ratings", "entryid in ($ents)");
            }
        }
        glossary_delete_attachments($glossary);
        delete_records("glossary_entries", "glossaryid", "$glossary->id");
    }

    return $result;
}

function glossary_user_outline($course, $user, $mod, $glossary) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        $result->info = count($entries) . ' ' . get_string("entries", "glossary");

        $lastentry = array_pop($entries);
        $result->time = $lastentry->timemodified;
        return $result;
    }
    return NULL;
}

function glossary_get_user_entries($glossaryid, $userid) {
/// Get all the entries for a user in a glossary
    global $CFG;

    return get_records_sql("SELECT e.*, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}glossary g, 
                                   {$CFG->prefix}glossary_entries e, 
                                   {$CFG->prefix}user u 
                             WHERE g.id = '$glossaryid' 
                               AND e.glossaryid = g.id 
                               AND e.userid = '$userid' 
                               AND e.userid = u.id
                          ORDER BY e.timemodified ASC");
}

function glossary_user_complete($course, $user, $mod, $glossary) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.
    global $CFG;

    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        if ( $glossary->displayformat == GLOSSARY_FORMAT_SIMPLE or
             $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
            print_simple_box_start("center","70%");
        } else {
            echo '<table width="95%" border="0"><tr><td>';
        }
        foreach ($entries as $entry) {
            $cm = get_coursemodule_from_instance("glossary", $glossary->id, $course->id);
            glossary_print_entry($course, $cm, $glossary, $entry,"","",0);
            echo '<p>';
        }
        if ( $glossary->displayformat == GLOSSARY_FORMAT_SIMPLE or
             $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
            print_simple_box_end();
        } else {
            echo '</td></tr></table>';
        }
    }
}

function glossary_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in glossary activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG, $THEME;

    if (!$logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'glossary' AND ".
                                           "(action = 'add entry' OR ".
                                           " action  = 'approve entry')", "time ASC")) {
        return false;
    }

    foreach ($logs as $log) {
        //Create a temp valid module structure (course,id)
        $tempmod->course = $log->course;        
        $entry           = get_record("glossary_entries","id",$log->info);
        $tempmod->id = $entry->glossaryid;
        //Obtain the visible property from the instance        
        $modvisible = instance_is_visible($log->module,$tempmod);

        //Only if the mod is visible
        if ($modvisible and $entry->approved) {
            $entries[$log->info] = glossary_log_info($log);
            $entries[$log->info]->time = $log->time;
            $entries[$log->info]->url  = $log->url;
        }
    }

    $content = false;
    if ($entries) {
        $strftimerecent = get_string("strftimerecent");
        $content = true;
        print_headline(get_string("newentries", "glossary").":");
        foreach ($entries as $entry) {
            $date = userdate($entry->timemodified, $strftimerecent);
            
            $user = get_record("user","id",$entry->userid);
            $fullname = fullname($user, $isteacher);
            echo "<p><font size=1>$date - $fullname<br>";
            echo "\"<a href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid&mode=entry&hook=$entry->id\">";
            echo "$entry->concept";
            echo "</a>\"</font></p>";
        }
    }

    return $content;
}

function glossary_log_info($log) {
    global $CFG;

    return get_record_sql("SELECT e.*, u.firstname, u.lastname
                             FROM {$CFG->prefix}glossary_entries e,
                                  {$CFG->prefix}user u
                            WHERE e.id = '$log->info'
                              AND u.id = '$log->userid'");
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
    if (!$glossary = get_record("glossary", "id", $glossaryid)) {
        return false;
    }
    if (!$glossary->assessed) {
        return false;
    }
    $scalemenu = make_grades_menu($glossary->scale);

    $currentuser = 0;
    $ratingsuser = array();

    if ($ratings = glossary_get_user_grades($glossaryid)) {
        foreach ($ratings as $rating) {     // Ordered by user
            if ($currentuser and $rating->userid != $currentuser) {
                if (!empty($ratingsuser)) {
                    if ($glossary->scale < 0) {
                        $return->grades[$currentuser] = glossary_get_ratings_mean(0, $scalemenu, $ratingsuser);
                        $return->grades[$currentuser] .= "<br />".glossary_get_ratings_summary(0, $scalemenu, $ratingsuser);
                    } else {
                        $total = 0;
                        $count = 0;
                        foreach ($ratingsuser as $ra) {
                            $total += $ra;
                            $count ++;
                        }
                        $return->grades[$currentuser] = (string) format_float($total/$count, 2);
                        if ( count($ratingsuser) > 1 ) {
                            $return->grades[$currentuser] .= " (" . count($ratingsuser) . ")";
                        }
                    }
                } else {
                    $return->grades[$currentuser] = "";
                }
                $ratingsuser = array();
            }
            $ratingsuser[] = $rating->rating;
            $currentuser = $rating->userid;
        }
        if (!empty($ratingsuser)) {
            if ($glossary->scale < 0) {
                $return->grades[$currentuser] = glossary_get_ratings_mean(0, $scalemenu, $ratingsuser);
                $return->grades[$currentuser] .= "<br />".glossary_get_ratings_summary(0, $scalemenu, $ratingsuser);
            } else {
                $total = 0;
                $count = 0;
                foreach ($ratingsuser as $ra) {
                    $total += $ra;
                    $count ++;
                }
                $return->grades[$currentuser] = (string) format_float((float)$total/(float)$count, 2);
                
                if ( count($ratingsuser) > 1 ) {
                    $return->grades[$currentuser] .= " (" . count($ratingsuser) . ")";
                }
            }
        } else {
            $return->grades[$currentuser] = "";
        }
    } else {
        $return->grades = array();
    }

    if ($glossary->scale < 0) {
        $return->maxgrade = "";
    } else {
        $return->maxgrade = $glossary->scale;
    }
    return $return;
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

function glossary_scale_used ($glossaryid,$scaleid) {
//This function returns if a scale is being used by one glossary
   
    $return = false;

    $rec = get_record("glossary","id","$glossaryid","scale","-$scaleid");

    if (!empty($rec)  && !empty($scaleid)) {
        $return = true;               
    }                            
    
    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other glossary functions go here.  Each of them must have a name that
/// starts with glossary_

function glossary_debug($debug,$text,$br=1) {
    if ( $debug ) {
        echo '<font color=red>' . $text . '</font>';
        if ( $br ) {
            echo '<br>';
        }
    }
}

function glossary_get_entries($glossaryid, $entrylist, $pivot = "") {
    global $CFG;
    if ($pivot) {
       $pivot .= ",";
    }

    return get_records_sql("SELECT $pivot id,userid,concept,definition,format
                            FROM {$CFG->prefix}glossary_entries
                            WHERE glossaryid = '$glossaryid'
                            AND id IN ($entrylist)");
}
function glossary_get_entries_sorted($glossary, $where="", $orderby="", $pivot = "") {
global $CFG;
    if ($where) {
       $where = " and $where";
    }
    if ($orderby) {
       $orderby = " ORDER BY $orderby";
    }
    if ($pivot) {
       $pivot .= ",";
    }
    return      get_records_sql("SELECT $pivot *
                                 FROM {$CFG->prefix}glossary_entries 
                                 WHERE (glossaryid = $glossary->id or sourceglossaryid = $glossary->id) $where $orderby");
}

function glossary_get_entries_by_category($glossary, $hook, $where="", $orderby="", $pivot = "") {
global $CFG;
    if ($where) {
       $where = " and $where";
    }
    if ($orderby) {
       $orderby = " ORDER BY $orderby";
    }
    if ($pivot) {
       $pivot .= ",";
    }
    return      get_records_sql("SELECT $pivot ge.*
                                 FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}glossary_entries_categories c
                                 WHERE (ge.id = c.entryidid and c.categoryid = $hook) and
                                             (ge.glossaryid = $glossary->id or ge.sourceglossaryid = $glossary->id) $where $orderby");
}

function glossary_print_entry($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons = 1, $displayformat  = -1, $ratings = NULL) {
    global $THEME, $USER, $CFG;
    $return = false;
    if ( $displayformat < 0 ) {
        $displayformat = $glossary->displayformat;
    }
    if ($entry->approved or ($USER->id == $entry->userid) or ($mode == 'approval' and !$entry->approved) ) {
        $permissiongranted = 0;
        $formatfile = "$CFG->dirroot/mod/glossary/formats/$displayformat.php";
        $functionname = "glossary_print_entry_by_format";

        $basicformat = ($displayformat == GLOSSARY_FORMAT_SIMPLE or
                        $displayformat == GLOSSARY_FORMAT_CONTINUOUS);
        if ( !$basicformat ) {
            if ( file_exists($formatfile) ) {
               include_once($formatfile);
               if (function_exists($functionname) ) {
                      $permissiongranted = 1;
               }
            }
        } else {
           $permissiongranted = 1;
        }
    
        if ( !$basicformat and $permissiongranted or $displayformat >= 2) {
            $return = glossary_print_entry_by_format($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings);
        } else {
            switch ( $displayformat ) {
            case GLOSSARY_FORMAT_SIMPLE:
                $return = glossary_print_entry_by_default($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings);
            break;
            case GLOSSARY_FORMAT_CONTINUOUS:
                $return = glossary_print_entry_continuous($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings);
            break;
            }
        }
    }
        return $return;
}
function  glossary_print_entry_concept($entry) {
    $options->para = false;
    $text = format_text('<nolink>' . $entry->concept . '</nolink>', FORMAT_MOODLE, $options);
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    echo $text;
}

function glossary_print_entry_definition($entry) {

    $definition = $entry->definition;

    $tags = array();

    //Calculate all the strings to be no-linked
    //First, the concept
    $term = preg_quote(trim($entry->concept),"/");
    $pat = '/('.$term.')/is';
    $doNolinks[] = $pat;
    //Now the aliases
    if ( $aliases = get_records("glossary_alias","entryid",$entry->id) ) {
        foreach ($aliases as $alias) {
            $term = preg_quote(trim($alias->alias),"/");
            $pat = '/('.$term.')/is';
            $doNolinks[] = $pat;
        }
    }

    //Extract all tags from definition
    preg_match_all('/(<.*?>)/is',$definition,$list_of_tags);

    //Save them into tags array to use them later
    foreach (array_unique($list_of_tags[0]) as $key=>$value) {
        $tags['<@'.$key.'@>'] = $value;
    }

    //Take off every tag from definition
    if ( $tags ) {
        $definition = str_replace($tags,array_keys($tags),$definition);
    }
    
    //Put doNolinks (concept + aliases) enclosed by <nolink> tag
    $definition= preg_replace($doNolinks,'<nolink>$1</nolink>',$definition);
        
    //Restore tags
    if ( $tags ) {
        $definition = str_replace(array_keys($tags),$tags,$definition);
    }

    $options->para = false;
    $text = format_text($definition, $entry->format,$options);
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    echo $text;
}

function  glossary_print_entry_aliases($course, $cm, $glossary, $entry,$mode="",$hook="", $type = 'print') {
    $return = '';
    if ( $aliases = get_records("glossary_alias","entryid",$entry->id) ) {
        foreach ($aliases as $alias) {
            if (trim($alias->alias)) {
                if ($return == '') {
                    $return = '<select style="font-size:8pt">';
                }
                $return .= "<option>$alias->alias</option>";
            }
        }
        if ($return != '') {
            $return .= '</select>';
//            $return = "<table border=0 align=$align><tr><td>$return</td></tr></table>";
        }
    } 
    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

function glossary_print_entry_icons($course, $cm, $glossary, $entry,$mode="",$hook="", $type = 'print') {
    global $THEME, $USER, $CFG;

    $importedentry = ($entry->sourceglossaryid == $glossary->id);
    $isteacher = isteacher($course->id);
    $ismainglossary = $glossary->mainglossary;
	
    $return = "<font size=1>";
    if (!$entry->approved) {
        $return .= get_string("entryishidden","glossary");
    }
    $return .= glossary_print_entry_commentslink($course, $cm, $glossary, $entry,$mode,$hook,'html');

    $return .= "</font> ";

    
    if ( $glossary->allowcomments and !isguest()) {
        $return .= " <a title=\"" . get_string("addcomment","glossary") . "\" href=\"comment.php?id=$cm->id&eid=$entry->id\"><img src=\"comment.gif\" height=11 width=11 border=0></a> ";
    }

    if ($isteacher or $glossary->studentcanpost and $entry->userid == $USER->id) {
        // only teachers can export entries so check it out
        if ($isteacher and !$ismainglossary and !$importedentry) {
            $mainglossary = get_record("glossary","mainglossary",1,"course",$course->id);
            if ( $mainglossary ) {  // if there is a main glossary defined, allow to export the current entry

                $return .= " <a title=\"" . get_string("exporttomainglossary","glossary") . "\" href=\"exportentry.php?id=$cm->id&entry=$entry->id&mode=$mode&hook=$hook\"><img src=\"export.gif\" height=11 width=11 border=0></a> ";

            }
        }

        if ( $entry->sourceglossaryid ) {
            $icon = "minus.gif";   // graphical metaphor (minus) for deleting an imported entry
        } else {
            $icon = "$CFG->pixpath/t/delete.gif";
        }

        // Exported entries can be updated/deleted only by teachers in the main glossary
        if ( !$importedentry and ($isteacher or !$ismainglossary) ) {
            $return .= " <a title=\"" . get_string("delete") . "\" href=\"deleteentry.php?id=$cm->id&mode=delete&entry=$entry->id&prevmode=$mode&hook=$hook\"><img src=\"";
            $return .= $icon;
            $return .= "\" height=11 width=11 border=0></a> ";
            
            $return .= " <a title=\"" . get_string("edit") . "\" href=\"edit.php?id=$cm->id&e=$entry->id&mode=$mode&hook=$hook\"><img src=\"$CFG->pixpath/t/edit.gif\" height=11 width=11 border=0></a>";
        } elseif ( $importedentry ) {
            $return .= " <font size=-1>" . get_string("exportedentry","glossary") . "</font>";
        }
    }
    $return .= "&nbsp;&nbsp;"; // just to make up a little the output in Mozilla ;)
    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

function glossary_print_entry_commentslink($course, $cm, $glossary, $entry,$mode,$hook, $type = 'print') {
    $return = '';

    $count = count_records("glossary_comments","entryid",$entry->id);
    if ($count) {
        $return = "<font size=1>";
        $return .= "<a href=\"comments.php?id=$cm->id&eid=$entry->id\">$count ";
        if ($count == 1) {
            $return .= get_string("comment", "glossary");
        } else {
            $return .= get_string("comments", "glossary");
        }
        $return .= "</a>";
        $return .= "</font>";
    }

    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

function  glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook,$printicons) {

    $aliases = glossary_print_entry_aliases($course, $cm, $glossary, $entry, $mode, $hook,"html");
    $icons   = "";
    if ( $printicons ) {
        $icons   = glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,"html");
    }
    if ( $aliases ) {
        echo '<table border="0" width="100%" align="center"><tr>' .
              '<td align="right" width="50%" valign=top><font size=1>' .
              get_string("aliases","glossary") . ': ' . $aliases . '</td>' .
              '<td align=right width="50%" valign=top>'.
              $icons .
              '</td></tr></table>';
    } else {
        echo "<p align=right>$icons";
    }
}

function glossary_print_entry_attachment($entry,$format=NULL,$align="right") {
///   valid format values: html  : Return the HTML link for the attachment as an icon
///                        text  : Return the HTML link for tha attachment as text
///                        blank : Print the output to the screen
    if ($entry->attachment) {
          $glossary = get_record("glossary","id",$entry->glossaryid);		  
          $entry->course = $glossary->course; //used inside print_attachment
          echo "<table border=0 align=$align><tr><td>";
          echo glossary_print_attachments($entry,$format,$align);
          echo "</td></tr></table>";
    }
}

function  glossary_print_entry_approval($cm, $entry, $mode) {
    if ( $mode == 'approval' and !$entry->approved ) {
        echo "<a title=\"" . get_string("approve","glossary"). "\" href=\"approve.php?id=$cm->id&eid=$entry->id&mode=$mode\"><IMG align=\"right\" src=\"check.gif\" border=0 width=\"34\" height=\"34\"></a>";
    }
}

function glossary_print_entry_by_default($course, $cm, $glossary, $entry,$mode="",$hook="",$printicons=1, $ratings=NULL) {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<TR>";
    echo "<TD WIDTH=100% valign=\"top\" BGCOLOR=\"#FFFFFF\">";
        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");
        echo "<b>";
        glossary_print_entry_concept($entry);
        echo ":</b> ";
        glossary_print_entry_definition($entry);
        glossary_print_entry_lower_section($course, $cm, $glossary, $entry,$mode,$hook,$printicons);
        echo ' ';
        $return = glossary_print_entry_ratings($course, $entry, $ratings);
    echo "</td>";
    echo "</TR>";
    return $return;
}

function glossary_print_entry_continuous($course, $cm, $glossary, $entry,$mode="",$hook="",$printicons=1, $ratings = NULL) {
    global $THEME, $USER;
    $return = false;
    if ($entry) {
        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");
        glossary_print_entry_concept($entry);
        echo " ";
        
        glossary_print_entry_definition($entry);
        
        $icons = '';
        if ( $printicons ) {
            $icons = glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,"html");        
        }        

        echo '(';
        if ( $icons ) {
            echo $icons;
        }
        $return = glossary_print_entry_ratings($course, $entry, $ratings);
        
        echo ')<br>';

    }
    return $return;
}

function glossary_search($course, $searchterms, $extended = 0, $glossary = NULL) {
// It returns all entries from all glossaries that matches the specified criteria 
//    within a given $course. It performs an $extended search if necessary.
// It restrict the search to only one $glossary if the $glossary parameter is set.

    global $CFG;
    if ( !$glossary ) {
        if ( $glossaries = get_records("glossary", "course", $course->id) ) {
            $glos = "";
            foreach ( $glossaries as $glossary ) {
                $glos .= "$glossary->id,";
            }
            $glos = substr($ents,0,-1);
        }
    } else {
        $glos = $glossary->id;
    }
    
    if (!isteacher($glossary->course)) {
        $glossarymodule = get_record("modules", "name", "glossary");
        $onlyvisible = " AND g.id = cm.instance AND cm.visible = 1 AND cm.module = $glossarymodule->id";
        $onlyvisibletable = ", {$CFG->prefix}course_modules cm";
    } else {

        $onlyvisible = "";
        $onlyvisibletable = "";
    }

    /// Some differences in syntax for entrygreSQL
    switch ($CFG->dbtype) {
    case 'postgres7':
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    break;
    case 'mysql':
    default:
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    break;
    }    

    $conceptsearch = "";
    $definitionsearch = "";


    foreach ($searchterms as $searchterm) {
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

    if ( !$extended ) {
        $definitionsearch = "0";
    }

    $selectsql = "{$CFG->prefix}glossary_entries e,
                  {$CFG->prefix}glossary g $onlyvisibletable
             WHERE ($conceptsearch OR $definitionsearch)
               AND (e.glossaryid = g.id or e.sourceglossaryid = g.id) $onlyvisible
               AND g.id IN ($glos) AND e.approved != 0";

    return get_records_sql("SELECT e.* 
                            FROM $selectsql ORDER BY e.concept ASC");
}

function glossary_search_entries($searchterms, $glossary, $extended) {
    $course = get_record("course","id",$glossary->course);
    return glossary_search($course,$searchterms,$extended,$glossary);
}

function glossary_file_area_name($entry) {
    global $CFG;
//  Creates a directory file name, suitable for make_upload_directory()

    // I'm doing this workaround for make it works for delete_instance also 
    //  (when called from delete_instance, glossary is already deleted so
    //   getting the course from mdl_glossary does not work)
    $module = get_record("modules","name","glossary");
    $cm = get_record("course_modules","module",$module->id,"instance",$entry->glossaryid);
    return "$cm->course/$CFG->moddata/glossary/$entry->glossaryid/$entry->id";
}

function glossary_file_area($entry) {
    return make_upload_directory( glossary_file_area_name($entry) );
}

function glossary_main_file_area($glossary) {
    $modarea = glossary_mod_file_area($glossary);
    return "$modarea/$glossary->id";
}

function glossary_mod_file_area($glossary) {
    global $CFG;
    
    return make_upload_directory( "$glossary->course/$CFG->moddata/glossary" );
}

function glossary_delete_old_attachments($entry, $exception="") {
// Deletes all the user files in the attachments area for a entry
// EXCEPT for any file named $exception

    if ($basedir = glossary_file_area($entry)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                if ($file != $exception) {
                    unlink("$basedir/$file");
//                    notify("Existing file '$file' has been deleted!");
                }
            }
        }
        if (!$exception) {  // Delete directory as well, if empty
            rmdir("$basedir");
        }
    }
}
function glossary_delete_attachments($glossary) {
// Deletes all the user files in the attachments area for the glossary
    if ( $entries = get_records("glossary_entries","glossaryid",$glossary->id) ) {
        $deleted = 0;
        foreach ($entries as $entry) {
            if ( $entry->attachment ) {
                if ($basedir = glossary_file_area($entry)) {
                    if ($files = get_directory_list($basedir)) {
                        foreach ($files as $file) {
                            unlink("$basedir/$file");
                        }
                    }
                    rmdir("$basedir");
                    $deleted++;
                }
            }            
        }
        if ( $deleted ) {
            $attachmentdir = glossary_main_file_area($glossary);
            $glossarydir = glossary_mod_file_area($glossary);
            
            rmdir("$attachmentdir");
            if (!$files = get_directory_list($glossarydir) ) {
                rmdir( "$glossarydir" );
            }
        }        
    }
}

function glossary_copy_attachments($entry, $newentry) {
/// Given a entry object that is being copied to glossaryid,
/// this function checks that entry
/// for attachments, and if any are found, these are
/// copied to the new glossary directory.

    global $CFG;

    $return = true;

    if ($entries = get_records_select("glossary_entries", "id = '$entry->id' AND attachment <> ''")) {
        foreach ($entries as $curentry) {
            $oldentry->id = $entry->id;
            $oldentry->course = $entry->course;
            $oldentry->glossaryid = $curentry->glossaryid;
            $oldentrydir = "$CFG->dataroot/".glossary_file_area_name($oldentry);
            if (is_dir($oldentrydir)) {

                $newentrydir = glossary_file_area($newentry);
                if (! copy("$oldentrydir/$newentry->attachment", "$newentrydir/$newentry->attachment")) {
                    $return = false;
                }
            }
        }
     }
    return $return;
}

function glossary_move_attachments($entry, $glossaryid) {
/// Given a entry object that is being moved to glossaryid,
/// this function checks that entry
/// for attachments, and if any are found, these are
/// moved to the new glossary directory.

    global $CFG;

    $return = true;

    if ($entries = get_records_select("glossary_entries", "glossaryid = '$entry->id' AND attachment <> ''")) {
        foreach ($entries as $entry) {
            $oldentry->course = $entry->course;
            $oldentry->glossaryid = $entry->glossaryid;
            $oldentrydir = "$CFG->dataroot/".glossary_file_area_name($oldentry);
            if (is_dir($oldentrydir)) {
                $newentry = $oldentry;
                $newentry->glossaryid = $glossaryid;
                $newentrydir = "$CFG->dataroot/".glossary_file_area_name($newentry);
                if (! @rename($oldentrydir, $newentrydir)) {
                    $return = false;
                }
            }
        }
    }
    return $return;
}

function glossary_add_attachment($entry, $newfile) {
// $entry is a full entry record, including course and glossary
// $newfile is a full upload array from $_FILES
// If successful, this function returns the name of the file

    global $CFG;

    if (empty($newfile['name'])) {
        return "";
    }

    $newfile_name = clean_filename($newfile['name']);

    if (valid_uploaded_file($newfile)) {
        if (! $newfile_name) {
            notify("This file had a wierd filename and couldn't be uploaded");

        } else if (! $dir = glossary_file_area($entry)) {
            notify("Attachment could not be stored");
            $newfile_name = "";

        } else {
            if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
                chmod("$dir/$newfile_name", $CFG->directorypermissions);
                glossary_delete_old_attachments($entry, $newfile_name);
            } else {
                notify("An error happened while saving the file on the server");
                $newfile_name = "";
            }
        }
    } else {
        $newfile_name = "";
    }

    return $newfile_name;
}

function glossary_print_attachments($entry, $return=NULL, $align="left") {
// if return=html, then return a html string.
// if return=text, then return a text-only string.
// otherwise, print HTML for non-images, and return image HTML
//     if attachment is an image, $align set its aligment.
    global $CFG;
    
    $newentry = $entry;
    if ( $newentry->sourceglossaryid ) {
        $newentry->glossaryid = $newentry->sourceglossaryid;
    }

    $filearea = glossary_file_area_name($newentry);

    $imagereturn = "";
    $output = "";

    if ($basedir = glossary_file_area($newentry)) {
        if ($files = get_directory_list($basedir)) {
            $strattachment = get_string("attachment", "glossary");
            $strpopupwindow = get_string("popupwindow");
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$filearea/$file";
                } else {
                    $ffurl = "file.php?file=/$filearea/$file";
                }
                $image = "<img border=0 src=\"$CFG->pixpath/f/$icon\" height=16 width=16 alt=\"$strpopupwindow\">";

                if ($return == "html") {
                    $output .= "<a target=_image href=\"$CFG->wwwroot/$ffurl\">$image</a> ";
                    $output .= "<a target=_image href=\"$CFG->wwwroot/$ffurl\">$file</a><br />";
                } else if ($return == "text") {
                    $output .= "$strattachment $file:\n$CFG->wwwroot/$ffurl\n";

                } else {
                    if ($icon == "image.gif") {    // Image attachments don't get printed as links
                        $imagereturn .= "<br /><img src=\"$CFG->wwwroot/$ffurl\" align=$align>";
                    } else {
                        link_to_popup_window("/$ffurl", "attachment", $image, 500, 500, $strattachment);
                        echo "<a target=_image href=\"$CFG->wwwroot/$ffurl\">$file</a>";
                        echo "<br />";
                    }
                }
            }
        }
    }

    if ($return) {
        return $output;
    }

    return $imagereturn;
}

function glossary_print_tabbed_table_start($data, $currenttab, $tTHEME = NULL) {

if ( !$tTHEME ) {
     global $THEME;
     $tTHEME = $THEME;
}

$tablecolor           = $tTHEME->TabTableBGColor;
$currenttabcolor      = $tTHEME->ActiveTabColor;
$tabcolor             = $tTHEME->InactiveTabColor;
$inactivefontcolor    = $tTHEME->InactiveFontColor;

$tablewidth           = $tTHEME->TabTableWidth;
$tabsperrow           = $tTHEME->TabsPerRow;
$tabseparation        = $tTHEME->TabSeparation;

$tabs                 = count($data);
$tabwidth             = (int) (100 / $tabsperrow);

$currentrow           = ( $currenttab - ( $currenttab % $tabsperrow) ) / $tabsperrow;

$numrows              = (int) ( $tabs / $tabsperrow ) + 1;

?>
  <center>
  <table border="0" cellpadding="0" cellspacing="0" width="<?php p($tablewidth) ?>">
    <tr>
      <td width="100%">

      <table border="0" cellpadding="0" cellspacing="0" width="100%">

<?php
$tabproccessed = 0;
for ($row = 0; $row < $numrows; $row++) {
     echo "<tr>\n";
     if ( $row != $currentrow ) {
          for ($col = 0; $col < $tabsperrow; $col++) {
               if ( $tabproccessed < $tabs ) {
                    if ( $col == 0 ) {
                        echo "<td width=\"$tabseparation\" align=\"center\">&nbsp;</td>";
                    }
                    if ($tabproccessed == $currenttab) {
                         $currentcolor = $currenttabcolor;
                         $currentstyle = 'generaltabselected';
                    } else {
                         $currentcolor = $tabcolor;
                         $currentstyle = 'generaltab';
                    }
                    echo "<td class=\"$currentstyle\" width=\"$tabwidth%\" bgcolor=\"$currentcolor\" align=\"center\"><b>";
                    if ($tabproccessed != $currenttab and $data[$tabproccessed]->link) {
                        echo "<a href=\"" . $data[$tabproccessed]->link . "\">";
                    }

                    if ( !$data[$tabproccessed]->link ) {
                        echo "<font color=\"$inactivefontcolor\">";
                    }
                    echo $data[$tabproccessed]->caption;
                    if ( !$data[$tabproccessed]->link ) {
                        echo "</font>";
                    }

                    if ($tabproccessed != $currenttab and $data[$tabproccessed]->link) {
                        echo "</a>";
                    }
                    echo "</b></td>";
                    
                    if ( $col < $tabsperrow ) {
                        echo "<td width=\"$tabseparation\" align=\"center\">&nbsp;</td>";
                    }
               } else {
                    $currentcolor = "";
               }
               $tabproccessed++;
          }
     } else {
          $firsttabincurrentrow = $tabproccessed;
          $tabproccessed += $tabsperrow;
     }
     echo "</tr><tr><td colspan=" . (2* $tabsperrow) . " ></td></tr>\n";
}
     echo "<tr>\n";
          $tabproccessed = $firsttabincurrentrow;
          for ($col = 0; $col < $tabsperrow; $col++) {
               if ( $tabproccessed < $tabs ) {
                    if ( $col == 0 ) {
                        echo "<td width=\"$tabseparation\" align=\"center\">&nbsp;</td>";
                    }
                    if ($tabproccessed == $currenttab) {
                         $currentcolor = $currenttabcolor;
                         $currentstyle = 'generaltabselected';
                    } else {
                         $currentcolor = $tabcolor;
                         $currentstyle = 'generaltab';
                    }

                    if (!isset($data[$tabproccessed]->link)) {
                        $data[$tabproccessed]->link = NULL;
                    }
                    echo "<td class=\"$currentstyle\" width=\"$tabwidth%\" bgcolor=\"$currentcolor\" align=\"center\"><b>";
                    if ($tabproccessed != $currenttab and $data[$tabproccessed]->link) {
                        echo "<a href=\"" . $data[$tabproccessed]->link . "\">";
                    }

                    if ( !$data[$tabproccessed]->link ) {
                        echo "<font color=\"$inactivefontcolor\">";
                    }
                    echo $data[$tabproccessed]->caption;
                    if ( !$data[$tabproccessed]->link ) {
                        echo "</font>";
                    }

                    if ($tabproccessed != $currenttab and $data[$tabproccessed]->link) {
                        echo "</a>";
                    }
                    echo "</b></td>";

                    if ($col < $tabsperrow) {
                         echo "<td width=\"$tabseparation\" align=\"center\">&nbsp;</td>";
                    }
               } else {
                    if ($numrows > 1) {
                         $currentcolor = $tabcolor;
                    } else {
                         $currentcolor = "";
                    }
                    echo "<td colspan = " . (2 * ($tabsperrow - $col)) . " bgcolor=\"$currentcolor\" align=\"center\">";
                    echo "</td>";

                    $col = $tabsperrow;
               }
               $tabproccessed++;
          }
     echo "</tr>\n";
     ?>

      </table>
      </td>
    </tr>
    <tr>
      <td width="100%" bgcolor="<?php p($tablecolor) ?>"><hr></td>
    </tr>
    <tr>
      <td width="100%" bgcolor="<?php p($tablecolor) ?>">
          <center>
<?php
}

function glossary_print_tabbed_table_end() {
     echo "</center><p></td></tr></table></center>";
}

function glossary_print_approval_menu($cm, $glossary,$mode, $hook, $sortkey = '', $sortorder = '') {
    if ($glossary->showalphabet and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS) {
        echo '<center>' . get_string("explainalphabet","glossary") . '<p>';
    }
    glossary_print_special_links($cm, $glossary, $mode, $hook);

    glossary_print_alphabet_links($cm, $glossary, $mode, $hook,$sortkey, $sortorder);

    glossary_print_all_links($cm, $glossary, $mode, $hook);
	 
    glossary_print_sorting_links($cm, $mode, 'CREATION', 'asc');
}

function glossary_print_addentry_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<center>' . get_string("explainaddentry","glossary") . '<p>';
}

function glossary_print_import_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<center>' . get_string("explainimport","glossary") . '<p>';
}

function glossary_print_export_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<center>' . get_string("explainexport","glossary") . '<p>';
}

function glossary_print_alphabet_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    if ( $mode != 'date' ) {
        if ($glossary->showalphabet and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS) {
            echo '<center>' . get_string("explainalphabet","glossary") . '<p>';
        }

        glossary_print_special_links($cm, $glossary, $mode, $hook);

        glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder);

        glossary_print_all_links($cm, $glossary, $mode, $hook);
    } else {
        glossary_print_sorting_links($cm, $mode, $sortkey,$sortorder);
    }
}

function glossary_print_author_menu($cm, $glossary,$mode, $hook, $sortkey = '', $sortorder = '') {
    if ($glossary->showalphabet and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS) {
        echo '<center>' . get_string("explainalphabet","glossary") . '<br />';
    }

    glossary_print_sorting_links($cm, $mode, $sortkey,$sortorder);
    glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder);
    glossary_print_all_links($cm, $glossary, $mode, $hook);
//    echo "<br />";
}

function glossary_print_categories_menu($cm, $glossary, $hook, $category) {
global $CFG, $THEME;
     echo '<table border=0 width=100%>';
     echo '<tr>';

     echo '<td align=center width=20%>';
     if ( isteacher($glossary->course) ) {
             $options['id'] = $cm->id;
             $options['mode'] = 'cat';
             $options['hook'] = $hook;
             echo print_single_button("editcategories.php", $options, get_string("editcategories","glossary"), "get");
     }
     echo '</td>';

     echo '<td align=center width=60%>';
     echo '<b>';

     $menu[GLOSSARY_SHOW_ALL_CATEGORIES] = get_string("allcategories","glossary");
     $menu[GLOSSARY_SHOW_NOT_CATEGORISED] = get_string("notcategorised","glossary");

     $categories = get_records("glossary_categories", "glossaryid", $glossary->id, "name ASC");
     $selected = '';
     if ( $categories ) {
          foreach ($categories as $currentcategory) {
                 $url = $currentcategory->id;
                 if ( $category ) {
                     if ($currentcategory->id == $category->id) {
                         $selected = $url;
                     }
                 }
                 $menu[$url] = $currentcategory->name;
          }
     }
     if ( !$selected ) {
         $selected = GLOSSARY_SHOW_NOT_CATEGORISED;
     }

     if ( $category ) {
        echo $category->name;
     } else {
        if ( $hook == GLOSSARY_SHOW_NOT_CATEGORISED ) {

            echo get_string("entrieswithoutcategory","glossary");
            $selected = GLOSSARY_SHOW_NOT_CATEGORISED;

        } elseif ( $hook == GLOSSARY_SHOW_ALL_CATEGORIES ) {

            echo get_string("allcategories","glossary");
            $selected = GLOSSARY_SHOW_ALL_CATEGORIES;

        }
     }
     echo '</b></td>';
     echo '<td align=center width=20%>';

     echo popup_form("$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&mode=cat&hook=", $menu, "catmenu", $selected, "",
                      "", "", false);

     echo '</td>';
     echo '</tr>';

     echo '</table>';
}

function glossary_print_all_links($cm, $glossary, $mode, $hook) {
global $CFG;  
     if ( $glossary->showall and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS) {
         $strallentries       = get_string("allentries", "glossary");
         if ( $hook == 'ALL' ) {
              echo "<b>$strallentries</b>";
         } else {
              $strexplainall = strip_tags(get_string("explainall","glossary"));
              echo "<a title=\"$strexplainall\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&mode=$mode&hook=ALL\">$strallentries</a>";
         }
     }
}

function glossary_print_special_links($cm, $glossary, $mode, $hook) {
global $CFG;
     if ( $glossary->showspecial and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS ) {
         $strspecial          = get_string("special", "glossary");
         if ( $hook == 'SPECIAL' ) {
              echo "<b>$strspecial</b> | ";
         } else {
              $strexplainspecial = strip_tags(get_string("explainspecial","glossary"));
              echo "<a title=\"$strexplainspecial\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&mode=$mode&hook=SPECIAL\">$strspecial</a> | ";
         }
     }
}

function glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder) {
global $CFG;
     if ( $glossary->showalphabet and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS ) {
          $alphabet = explode(",", get_string("alphabet"));
          $letters_by_line = 14;
          for ($i = 0; $i < count($alphabet); $i++) {
              if ( $hook == $alphabet[$i] and $hook) {
                   echo "<b>$alphabet[$i]</b>";
              } else {
                   echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&mode=$mode&hook=$alphabet[$i]&sortkey=$sortkey&sortorder=$sortorder\">$alphabet[$i]</a>";
              }
              if ((int) ($i % $letters_by_line) != 0 or $i == 0) {
                   echo ' | ';
              } else {
                   echo '<br>';
              }
          }
     }
}

function glossary_print_sorting_links($cm, $mode, $sortkey = '',$sortorder = '') {
global $CFG;

    $asc    = get_string("ascending","glossary");
    $desc   = get_string("descending","glossary");
    $bopen  = '<b>';
    $bclose = '</b>';
    
     $neworder = '';
     if ( $sortorder ) {
         if ( $sortorder == 'asc' ) {
             $neworder = '&sortorder=desc';
             $newordertitle = $desc;
         } else {
             $neworder = '&sortorder=asc';
             $newordertitle = $asc;
         }
         $icon = " <img src=\"$sortorder.gif\" border=0 width=16 height=16>";
     } else {
         if ( $sortkey != 'CREATION' and $sortkey != 'UPDATE' and
               $sortkey != 'FIRSTNAME' and $sortkey != 'LASTNAME' ) {
             $icon = "";
             $newordertitle = $asc;
         } else {
             $newordertitle = $desc;
             $neworder = '&sortorder=desc';
             $icon = ' <img src="asc.gif" border=0 width=16 height=16>';
         }
     }
     $ficon     = '';
     $fneworder = '';
     $fbtag     = '';
     $fendbtag  = '';

     $sicon     = '';
     $sneworder = '';

     $sbtag      = '';
     $fbtag      = '';
     $fendbtag      = '';
     $sendbtag      = '';

     $sendbtag  = '';

     if ( $sortkey == 'CREATION' or $sortkey == 'FIRSTNAME' ) {
         $ficon       = $icon;
         $fneworder   = $neworder;
         $fordertitle = $newordertitle;
         $sordertitle = $asc;
         $fbtag       = $bopen;
         $fendbtag    = $bclose;
     } elseif ($sortkey == 'UPDATE' or $sortkey == 'LASTNAME') {
         $sicon = $icon;
         $sneworder   = $neworder;
         $fordertitle = $asc;
         $sordertitle = $newordertitle;
         $sbtag       = $bopen;
         $sendbtag    = $bclose;
     } else {
         $fordertitle = $asc;
         $sordertitle = $asc;
     }

     if ( $sortkey == 'CREATION' or $sortkey == 'UPDATE' ) {
         $forder = 'CREATION';
         $sorder =  'UPDATE';
         $fsort  = get_string("sortbycreation", "glossary");
         $ssort  = get_string("sortbylastupdate", "glossary");

         $sort        = get_string("sortchronogically", "glossary");
     } elseif ( $sortkey == 'FIRSTNAME' or $sortkey == 'LASTNAME') {
         $forder = 'FIRSTNAME';
         $sorder =  'LASTNAME';
         $fsort  = get_string("firstname");
         $ssort  = get_string("lastname");

         $sort        = get_string("sortby", "glossary");
     }

     echo "<br>$sort: $sbtag<a title=\"$ssort $sordertitle\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&sortkey=$sorder$sneworder&mode=$mode\">$ssort$sicon</a>$sendbtag | ".
                          "$fbtag<a title=\"$fsort $fordertitle\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&sortkey=$forder$fneworder&mode=$mode\">$fsort$ficon</a>$fendbtag<br />";
}

function glossary_sort_entries ( $entry0, $entry1 ) {
    if ( strtolower(ltrim($entry0->concept)) < strtolower(ltrim($entry1->concept)) ) {
        return -1;
    } elseif ( strtolower(ltrim($entry0->concept)) > strtolower(ltrim($entry1->concept)) ) {
        return 1;
    } else {
        return 0;
    }
}

function glossary_print_comment($course, $cm, $glossary, $entry, $comment) {
    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    $user = get_record("user", "id", $comment->userid);
    $strby = get_string("writtenby","glossary");
    $fullname = fullname($user, isteacher($course->id));

    echo '<table align="center" border="0" width="70%" cellpadding="3" cellspacing="0" class="forumpost">';
    echo "<tr>";
    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($user->id, $course->id, $user->picture);
    echo "</td>";
    echo "<td bgcolor=\"$THEME->cellheading\" class=\"forumpostheader\" width=\"100%\">";
    echo "<p>";
    echo "<font size=2><a href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$fullname</a></font><br />";
    echo "<font size=1>".get_string("lastedited").": ".userdate($comment->timemodified)."</font>";
    echo "</p></td></tr>";

    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostside\" width=\"10\">";
    echo "&nbsp;";
    echo "</td><td bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">\n";

    echo format_text($comment->comment, $comment->format);

    echo "<div align=right><p align=right>";
    if ( (time() - $comment->timemodified <  $CFG->maxeditingtime and $USER->id == $comment->userid)  or isteacher($course->id) ) {
        echo "<a href=\"comment.php?id=$cm->id&eid=$entry->id&cid=$comment->id&action=edit\"><img  
               alt=\"" . get_string("edit") . "\" src=\"$CFG->pixpath/t/edit.gif\" height=11 width=11 border=0></a> ";
    }
    if ( $USER->id == $comment->userid or isteacher($course->id) ) {
        echo "<a href=\"comment.php?id=$cm->id&eid=$entry->id&cid=$comment->id&action=delete\"><img  
               alt=\"" . get_string("delete") . "\" src=\"$CFG->pixpath/t/delete.gif\" height=11 width=11 border=0></a>";
    }
    
    echo "</p>";
    echo "</div>";
    echo "</td></tr>\n</table>\n\n";

}

function  glossary_print_entry_ratings($course, $entry, $ratings = NULL) {
global $USER;
    $ratingsmenuused = false;
    if (!empty($ratings) and !empty($USER->id)) {
        $useratings = true;
        if ($ratings->assesstimestart and $ratings->assesstimefinish) {
            if ($entry->timecreated < $ratings->assesstimestart or $entry->timecreated > $ratings->assesstimefinish) {
                $useratings = false;
            }
        }
        if ($useratings) {
            if (isteacher($course->id)) {
                glossary_print_ratings_mean($entry->id, $ratings->scale);
                if ($USER->id != $entry->userid) {
                     glossary_print_rating_menu($entry->id, $USER->id, $ratings->scale);
                     $ratingsmenuused = true;
                }
            } else if ($USER->id == $entry->userid) {
                glossary_print_ratings_mean($entry->id, $ratings->scale);
            } else if (!empty($ratings->allow) ) {
                glossary_print_rating_menu($entry->id, $USER->id, $ratings->scale);
                $ratingsmenuused = true;
            }
        }
    }
    return $ratingsmenuused;
}

function glossary_print_dynaentry($courseid, $entries, $displayformat = -1) {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<center><table width=95% border=0><tr>";
    echo "<td width=100%\">";
    if ( $entries ) {
        foreach ( $entries as $entry ) {
            if (! $glossary = get_record("glossary", "id", $entry->glossaryid)) {
                error("Glossary ID was incorrect or no longer exists");
            }
            if (! $course = get_record("course", "id", $glossary->course)) {
                error("Glossary is misconfigured - don't know what course it's from");
            }
            if (!$cm = get_coursemodule_from_instance("glossary", $entry->glossaryid, $glossary->course) ) {
                error("Glossary is misconfigured - don't know what course module it is ");
            }

            //If displayformat is present, override glossary->displayformat
            if ($displayformat == -1) {
                $dp = $glossary->displayformat;
            } else { 
                $dp = $displayformat;
            }

            // Hard-coded until the Display formats manager is done.
            if ( $dprecord = get_record("glossary_displayformats","fid", $dp) ) {
                if ( $dprecord->relatedview >= 0 ) {
                    $dp = $dprecord->relatedview;
                }
            }

            glossary_print_entry($course, $cm, $glossary, $entry, "","",0,$dp);
        }
    }
    echo "</td>";
    echo "</tr></table></center>";
}

function glossary_generate_export_file($glossary, $hook = "", $hook = 0) {
global $CFG;
    glossary_check_moddata_dir($glossary);

    if (!$h = glossary_open_xml($glossary)) {
        error("An error occurred while opening a file to write to.");
    }

    $status = fwrite ($h,glossary_start_tag("INFO",1,true));
        fwrite ($h,glossary_full_tag("NAME",2,false,$glossary->name));
        fwrite ($h,glossary_full_tag("INTRO",2,false,$glossary->intro));
        fwrite ($h,glossary_full_tag("STUDENTCANPOST",2,false,$glossary->studentcanpost));
        fwrite ($h,glossary_full_tag("ALLOWDUPLICATEDENTRIES",2,false,$glossary->allowduplicatedentries));
        fwrite ($h,glossary_full_tag("DISPLAYFORMAT",2,false,$glossary->displayformat));
        fwrite ($h,glossary_full_tag("SHOWSPECIAL",2,false,$glossary->showspecial));
        fwrite ($h,glossary_full_tag("SHOWALPHABET",2,false,$glossary->showalphabet));
        fwrite ($h,glossary_full_tag("SHOWALL",2,false,$glossary->showall));
        fwrite ($h,glossary_full_tag("ALLOWCOMMENTS",2,false,$glossary->allowcomments));
        fwrite ($h,glossary_full_tag("USEDYNALINK",2,false,$glossary->usedynalink));
        fwrite ($h,glossary_full_tag("DEFAULTAPPROVAL",2,false,$glossary->defaultapproval));
        fwrite ($h,glossary_full_tag("GLOBALGLOSSARY",2,false,$glossary->globalglossary));
        fwrite ($h,glossary_full_tag("ENTBYPAGE",2,false,$glossary->entbypage));

        if ( $entries = get_records("glossary_entries","glossaryid",$glossary->id) ) {
            $status = fwrite ($h,glossary_start_tag("ENTRIES",2,true));
            foreach ($entries as $entry) {
                $permissiongranted = 1;
                if ( $hook ) {
                    switch ( $hook ) {
                    case "ALL":
                    case "SPECIAL":
                    break;
                    default:
                        $permissiongranted = ($entry->concept[ strlen($hook)-1 ] == $hook);
                    break;
                    }
                }
                if ( $hook ) {
                    switch ( $hook ) {
                    case GLOSSARY_SHOW_ALL_CATEGORIES:
                    break;
                    case GLOSSARY_SHOW_NOT_CATEGORISED:
                        $permissiongranted = !record_exists("glossary_entries_categories","entryid",$entry->id);
                    break;
                    default:
                        $permissiongranted = record_exists("glossary_entries_categories","entryid",$entry->id, "categoryid",$hook);
                    break;
                    }
                }
                if ( $entry->approved and $permissiongranted ) {
                    $status = fwrite($h,glossary_start_tag("ENTRY",3,true));
                    fwrite($h,glossary_full_tag("CONCEPT",4,false,trim($entry->concept)));
                    fwrite($h,glossary_full_tag("DEFINITION",4,false,$entry->definition));
                    fwrite($h,glossary_full_tag("FORMAT",4,false,$entry->format));
                    fwrite($h,glossary_full_tag("USEDYNALINK",4,false,$entry->usedynalink));
                    fwrite($h,glossary_full_tag("CASESENSITIVE",4,false,$entry->casesensitive));
                    fwrite($h,glossary_full_tag("FULLMATCH",4,false,$entry->fullmatch));
                    fwrite($h,glossary_full_tag("TEACHERENTRY",4,false,$entry->teacherentry));

                    if ( $aliases = get_records("glossary_alias","entryid",$entry->id) ) {
                        $status = fwrite ($h,glossary_start_tag("ALIASES",4,true));
                        foreach ($aliases as $alias) {
                            $status = fwrite ($h,glossary_start_tag("ALIAS",5,true));
                                fwrite($h,glossary_full_tag("NAME",6,false,trim($alias->alias)));
                            $status = fwrite($h,glossary_end_tag("ALIAS",5,true));
                        }
                        $status = fwrite($h,glossary_end_tag("ALIASES",4,true));
                    }
                    if ( $catentries = get_records("glossary_entries_categories","entryid",$entry->id) ) {
                        $status = fwrite ($h,glossary_start_tag("CATEGORIES",4,true));
                        foreach ($catentries as $catentry) {
                            $category = get_record("glossary_categories","id",$catentry->categoryid);

                            $status = fwrite ($h,glossary_start_tag("CATEGORY",5,true));
                                fwrite($h,glossary_full_tag("NAME",6,false,$category->name));
                                fwrite($h,glossary_full_tag("USEDYNALINK",6,false,$category->usedynalink));
                            $status = fwrite($h,glossary_end_tag("CATEGORY",5,true));
                        }
                        $status = fwrite($h,glossary_end_tag("CATEGORIES",4,true));
                    }

                    $status =fwrite($h,glossary_end_tag("ENTRY",3,true));
                }
            }
            $status =fwrite ($h,glossary_end_tag("ENTRIES",2,true));

        }


    $status =fwrite ($h,glossary_end_tag("INFO",1,true));

    $h = glossary_close_xml($h);
}
// Functions designed by Eloy Lafuente
//
//Function to create, open and write header of the xml file
function glossary_open_xml($glossary) {

        global $CFG;
        
        $status = true;

        //Open for writing

        $glossaryname = clean_filename(strip_tags($glossary->name)); 
        $pathname = make_upload_directory("$glossary->course/glossary/$glossaryname");
        $filename = "$pathname/glossary.xml";

        if (!$h = fopen($filename,"w")) {
            notify("Error opening '$filename'");
            return false;
        }

        //Writes the header
        $status = fwrite ($h,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        if ($status) {
            $status = fwrite ($h,glossary_start_tag("GLOSSARY",0,true));
        }
        if ($status) {
            return $h;
        } else {
            return false;
        }
}

function glossary_read_imported_file($file) {
require_once "../../lib/xmlize.php";
    $h = fopen($file,"r");
    $line = '';
    if ($h) {
        while ( !feof($h) ) {
           $char = fread($h,1024);
           $line .= $char;
        }
        fclose($h);
	}
    return xmlize($line);
}
//Close the file
function glossary_close_xml($h) {
        $status = fwrite ($h,glossary_end_tag("GLOSSARY",0,true));
        return fclose($h);
}

//Return the xml start tag 
function glossary_start_tag($tag,$level=0,$endline=false) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."<".strtoupper($tag).">".$endchar;
}
    
//Return the xml end tag 
function glossary_end_tag($tag,$level=0,$endline=true) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."</".strtoupper($tag).">".$endchar;
}
    
//Return the start tag, the contents and the end tag
function glossary_full_tag($tag,$level=0,$endline=true,$content,$to_utf=true) {
        $st = glossary_start_tag($tag,$level,$endline);
        $co="";
        if ($to_utf) {
            $co = preg_replace("/\r\n|\r/", "\n", utf8_encode(htmlspecialchars($content)));
        } else {
            $co = preg_replace("/\r\n|\r/", "\n", htmlspecialchars($content));
        }
        $et = glossary_end_tag($tag,0,true);
        return $st.$co.$et;
}

    //Function to check and create the needed moddata dir to
    //save all the mod backup files. We always name it moddata
    //to be able to restore it, but in restore we check for
    //$CFG->moddata !!
function glossary_check_moddata_dir($glossary) {
  
    global $CFG;

    $status = glossary_check_dir_exists($CFG->dataroot."/$glossary->course",true);
    if ( $status ) {
        $status = glossary_check_dir_exists($CFG->dataroot."/$glossary->course/glossary",true);
        if ( $status ) {
            $status = glossary_check_dir_exists($CFG->dataroot."/$glossary->course/glossary/". clean_filename($glossary->name),true);
        }
    }
    return $status;
}

//Function to check if a directory exists
//and, optionally, create it
function glossary_check_dir_exists($dir,$create=false) {

    global $CFG; 

    $status = true;
    if(!is_dir($dir)) {
        if (!$create) {
            $status = false;
        } else {
            umask(0000);
            $status = mkdir ($dir,$CFG->directorypermissions);
        }
    }
    return $status;
}
/*
* Adding grading functions 
*/

function glossary_get_ratings($entryid, $sort="u.firstname ASC") {
/// Returns a list of ratings for a particular entry - sorted.
    global $CFG;
    return get_records_sql("SELECT u.*, r.rating, r.time 
                              FROM {$CFG->prefix}glossary_ratings r, 
                                   {$CFG->prefix}user u
                             WHERE r.entryid = '$entryid' 
                               AND r.userid = u.id 
                             ORDER BY $sort");
}

function glossary_get_user_grades($glossaryid) {
/// Get all user grades for a glossary
    global $CFG;

    return get_records_sql("SELECT r.id, e.userid, r.rating
                              FROM {$CFG->prefix}glossary_entries e, 
                                   {$CFG->prefix}glossary_ratings r
                             WHERE e.glossaryid = '$glossaryid' 
                               AND r.entryid = e.id
                             ORDER by e.userid ");
}

function glossary_count_unrated_entries($glossaryid, $userid) {
// How many unrated entries are in the given glossary for a given user?
    global $CFG;
    if ($entries = get_record_sql("SELECT count(*) as num
                                   FROM {$CFG->prefix}glossary_entries
                                  WHERE glossaryid = '$glossaryid' 
                                    AND userid <> '$userid' ")) {

        if ($rated = get_record_sql("SELECT count(*) as num 
                                       FROM {$CFG->prefix}glossary_entries e, 
                                            {$CFG->prefix}glossary_ratings r
                                      WHERE e.glossaryid = '$glossaryid'
                                        AND e.id = r.entryid 
                                        AND r.userid = '$userid'")) {
            $difference = $entries->num - $rated->num;
            if ($difference > 0) {
                return $difference;
            } else {
                return 0;    // Just in case there was a counting error
            }
        } else {
            return $entries->num;
        }
    } else {
        return 0;
    }
}

function glossary_print_ratings_mean($entryid, $scale) {
/// Print the multiple ratings on a entry given to the current user by others.
/// Scale is an array of ratings

    static $strrate;

    $mean = glossary_get_ratings_mean($entryid, $scale);
    
    if ($mean !== "") {

        if (empty($strratings)) {
            $strratings = get_string("ratings", "glossary");
        }

        echo "<font size=-1>$strratings: ";
        link_to_popup_window ("/mod/glossary/report.php?id=$entryid", "ratings", $mean, 400, 600);
        echo "</font>";
    }
}


function glossary_get_ratings_mean($entryid, $scale, $ratings=NULL) {
/// Return the mean rating of a entry given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("glossary_ratings", "entryid", $entryid)) {
            foreach ($rates as $rate) {
                $ratings[] = $rate->rating;
            }
        }
    }

    $count = count($ratings);

    if ($count == 0) {
        return "";

    } else if ($count == 1) {
        return $scale[$ratings[0]];

    } else {
        $total = 0;
        foreach ($ratings as $rating) {
            $total += $rating;
        }
        $mean = round( ((float)$total/(float)$count) + 0.001);  // Little fudge factor so that 0.5 goes UP
    
        if (isset($scale[$mean])) {
            return $scale[$mean]." ($count)";
        } else {
            return "$mean ($count)";    // Should never happen, hopefully
        }
    }
}

function glossary_get_ratings_summary($entryid, $scale, $ratings=NULL) {
/// Return a summary of entry ratings given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("glossary_ratings", "entryid", $entryid)) {
            foreach ($rates as $rate) {
                $rating[] = $rate->rating;
            }
        }
    }


    if (!$count = count($ratings)) {
        return "";
    }


    foreach ($scale as $key => $scaleitem) {
        $sumrating[$key] = 0;
    }

    foreach ($ratings as $rating) {
        $sumrating[$rating]++;
    }

    $summary = "";
    foreach ($scale as $key => $scaleitem) {
        $summary = $sumrating[$key].$summary;
        if ($key > 1) {
            $summary = "/$summary";
        }
    }
    return $summary;
}

function glossary_print_rating_menu($entryid, $userid, $scale) {
/// Print the menu of ratings as part of a larger form.  
/// If the entry has already been - set that value.
/// Scale is an array of ratings

    static $strrate;

    if (!$rating = get_record("glossary_ratings", "userid", $userid, "entryid", $entryid)) {
        $rating->rating = 0;
    }

    if (empty($strrate)) {
        $strrate = get_string("rate", "glossary");
    }

    choose_from_menu($scale, $entryid, $rating->rating, "$strrate...");
}

?>
