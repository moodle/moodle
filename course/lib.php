<? // $Id$
   // Library of useful functions


$COURSE_MAX_LOG_DISPLAY = 150;       // days

$COURSE_TEACHER_COLOR = "#990000";   // To hilight certain items that are teacher-only

$COURSE_LIVELOG_REFRESH = 60;        // Seconds


function print_log_selector_form($course, $selecteduser=0, $selecteddate="today") {

    global $USER, $CFG;

    // Get all the possible users
    $users = array();

    if ($course->category) {
        if ($students = get_records_sql("SELECT u.* FROM user u, user_students s 
                                         WHERE s.course = '$course->id' AND s.user = u.id
                                         ORDER BY u.lastaccess DESC")) {
            foreach ($students as $student) {
                $users["$student->id"] = "$student->firstname $student->lastname";
            }
        }
        if ($teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                         WHERE t.course = '$course->id' AND t.user = u.id
                                         ORDER BY u.lastaccess DESC")) {
            foreach ($teachers as $teacher) {
                $users["$teacher->id"] = "$teacher->firstname $teacher->lastname";
            }
        }
    }

    if (isadmin()) {
        if ($ccc = get_records_sql("SELECT * FROM course ORDER BY fullname")) {
            foreach ($ccc as $cc) {
                if ($cc->category) {
                    $courses["$cc->id"] = "$cc->fullname";
                } else {
                    $courses["$cc->id"] = " $cc->fullname (Site)";
                }
            }
        }
        asort($courses);
    }

    asort($users);

    // Get all the possible dates
    // Note that we are keeping track of real (GMT) time and user time
    // User time is only used in displays - all calcs and passing is GMT

    $timenow = time(); // GMT

    // What day is it now for the user, and when is midnight that day (in GMT).
    $timemidnight = $today = usergetmidnight($timenow);

    // Put today up the top of the list
    $dates = array("$timemidnight" => get_string("today").", ".userdate($timenow, "%e %B %Y") );

    if (! $course->startdate) {
        $course->startdate = $course->timecreated;
    }

    $numdates = 1;
    while ($timemidnight > $course->startdate and $numdates < 365) {
        $timemidnight = $timemidnight - 86400;
        $timenow = $timenow - 86400;
        $dates["$timemidnight"] = userdate($timenow, "%A, %e %B %Y");
        $numdates++;
    }

    if ($selecteddate == "today") {
        $selecteddate = $today;
    }

    echo "<CENTER>";
    echo "<FORM ACTION=log.php METHOD=get>";
    if (isadmin()) {
        choose_from_menu ($courses, "id", $course->id, "");
    } else {
        echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    }
    if ($course->category) {
        choose_from_menu ($users, "user", $selecteduser, get_string("allparticipants") );
    }
    choose_from_menu ($dates, "date", $selecteddate, get_string("alldays"));
    echo "<INPUT TYPE=submit VALUE=\"".get_string("showtheselogs")."\">";
    echo "</FORM>";
    echo "</CENTER>";
}

function make_log_url($module, $url) {
    switch ($module) {
        case "course":
        case "user":
        case "file":
        case "login":
        case "lib":
        case "admin":
            return "/$module/$url";
            break;
        default:
            return "/mod/$module/$url";
            break;
    }
}


function print_log($course, $user=0, $date=0, $order="ORDER BY l.time ASC") {
// It is assumed that $date is the GMT time of midnight for that day, 
// and so the next 86400 seconds worth of logs are printed.

    if ($course->category) {
        $selector = "WHERE l.course='$course->id' AND l.user = u.id";

    } else {
        $selector = "WHERE l.user = u.id";  // Show all courses
        if ($ccc = get_records_sql("SELECT * FROM course ORDER BY fullname")) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = "$cc->shortname";
            }
        }
    }

    if ($user) {
        $selector .= " AND l.user = '$user'";
    }

    if ($date) {
        $enddate = $date + 86400;
        $selector .= " AND l.time > '$date' AND l.time < '$enddate'";
    }

    if (!$logs = get_records_sql("SELECT l.*, u.firstname, u.lastname, u.picture 
                                  FROM log l, user u $selector $order")){
        notify("No logs found!");
        print_footer($course);
        exit;
    }

    $count=0;
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);
    echo "<P ALIGN=CENTER>Displaying ".count($logs)." records</P>";
    echo "<TABLE BORDER=0 ALIGN=center CELLPADDING=3 CELLSPACING=3>";
    foreach ($logs as $log) {

        if ($ld = get_record_sql("SELECT * FROM log_display WHERE module='$log->module' AND action='$log->action'")) {
            $log->info = get_field($ld->mtable, $ld->field, "id", $log->info);
        }

        echo "<TR NOWRAP>";
        if (! $course->category) {
            echo "<TD NOWRAP><FONT SIZE=2><A HREF=\"view.php?id=$log->course\">".$courses[$log->course]."</A></TD>";
        }
        echo "<TD NOWRAP ALIGN=right><FONT SIZE=2>".userdate($log->time, "%A")."</TD>";
        echo "<TD NOWRAP><FONT SIZE=2>".userdate($log->time, "%e %B %Y, %I:%M %p")."</TD>";
        echo "<TD NOWRAP><FONT SIZE=2>";
        link_to_popup_window("$CFG->wwwroot/lib/ipatlas/plot.php?address=$log->ip&user=$log->user", "ipatlas","$log->ip", 400, 700);
        echo "</TD>";
        echo "<TD NOWRAP><FONT SIZE=2><A HREF=\"../user/view.php?id=$log->user&course=$log->course\"><B>$log->firstname $log->lastname</B></TD>";
        echo "<TD NOWRAP><FONT SIZE=2>";
        link_to_popup_window( make_log_url($log->module,$log->url), "fromloglive","$log->module $log->action", 400, 600);
        echo "</TD>";
        echo "<TD NOWRAP><FONT SIZE=2>$log->info</TD>";
        echo "</TR>";
    }
    echo "</TABLE>";
}


function print_all_courses($cat=1, $style="full", $maxcount=999) {
    global $CFG;

    if ($courses = get_records("course", "category", $cat, "fullname ASC")) {
        if ($style == "minimal") {
            $count = 0;
            $icon  = "<IMG SRC=\"pix/i/course.gif\" HEIGHT=16 WIDTH=16 ALT=\"".get_string("course")."\">";
            foreach ($courses as $course) {
                $moddata[]="<A TITLE=\"$course->shortname\" HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</A>";
                $modicon[]=$icon;
                if ($count++ >= $maxcount) {
                    break;
                }
            }   
            $fulllist = "<P><A HREF=\"$CFG->wwwroot/course/\">".get_string("fulllistofcourses")."</A>...";
            print_side_block("", $moddata, "$fulllist", $modicon);

        } else {
            foreach ($courses as $course) {
                print_course($course);
                echo "<BR>\n";
            }
        }

    } else {
        echo "<H3>".get_string("nocoursesyet")."</H3>";
    }
}


function print_course($course) {

    global $CFG;

    if (! $site = get_site()) {
        error("Could not find a site!");
    }

    print_simple_box_start("CENTER", "100%");

    echo "<TABLE WIDTH=100%>";
    echo "<TR VALIGN=top>";
    echo "<TD VALIGN=top WIDTH=50%>";
    echo "<P><FONT SIZE=3><B><A TITLE=\"".get_string("entercourse")."\" 
              HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</A></B></FONT></P>";
    if ($teachers = get_course_teachers($course->id)) {
        echo "<P><FONT SIZE=1>\n";
        foreach ($teachers as $teacher) {
            if ($teacher->authority > 0) {
                echo "$course->teacher: <A HREF=\"$CFG->wwwroot/user/view.php?id=$teacher->id&course=$site->id\">$teacher->firstname $teacher->lastname</A><BR>";
            }
        }
        echo "</FONT></P>";
    }
    if ($course->guest or ($course->password == "")) {
        echo "<A TITLE=\"".get_string("allowguests")."\" HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">";
        echo "<IMG VSPACE=4 ALT=\"\" HEIGHT=16 WIDTH=16 BORDER=0 SRC=\"$CFG->wwwroot/user/user.gif\"></A>&nbsp;&nbsp;";
    }
    if ($course->password) {
        echo "<A TITLE=\"".get_string("requireskey")."\" HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">";
        echo "<IMG VSPACE=4 ALT=\"\" HEIGHT=16 WIDTH=16 BORDER=0 SRC=\"$CFG->wwwroot/pix/i/key.gif\"></A>";
    }


    echo "</TD><TD VALIGN=top WIDTH=50%>";
    echo "<P><FONT SIZE=2>".text_to_html($course->summary)."</FONT></P>";
    echo "</TD></TR>";
    echo "</TABLE>";

    print_simple_box_end();
}

function print_headline($text, $size=2) {
    echo "<B><FONT SIZE=\"$size\">$text</FONT></B><BR>\n";
}

function print_recent_activity($course) {
    // $course is an object
    // This function trawls through the logs looking for 
    // anything new since the user's last login

    global $CFG, $USER, $COURSE_TEACHER_COLOR;

    if (! $USER->lastlogin ) {
        echo "<P ALIGN=CENTER><FONT SIZE=1>";
        print_string("welcometocourse", "", $course->shortname);
        echo "</FONT></P>";
        return;
    } else {
        echo "<P ALIGN=CENTER><FONT SIZE=1>";
        echo get_string("yourlastlogin").":<BR>"; 
        echo userdate($USER->lastlogin, "%A, %e %b %Y, %H:%M");
        echo "</FONT></P>";
    }

    if (! $logs = get_records_sql("SELECT * FROM log WHERE time > '$USER->lastlogin' AND course = '$course->id' ORDER BY time ASC")) {
        return;
    }


    // Firstly, have there been any new enrolments?

    $heading = false;
    $content = false;
    foreach ($logs as $log) {
        if ($log->module == "course" and $log->action == "enrol") {
            if (! $heading) {
                print_headline(get_string("newusers").":");
                $heading = true;
                $content = true;
            }
            $user = get_record("user", "id", $log->info);
            echo "<P><FONT SIZE=1><A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A></FONT></P>";
        }
    }

    // Next, have there been any changes to the course structure?

    foreach ($logs as $log) {
        if ($log->module == "course") {
            if ($log->action == "add mod" or $log->action == "update mod" or $log->action == "delete mod") {
                $info = split(" ", $log->info);
                $modname = get_field($info[0], "name", "id", $info[1]);
            
                switch ($log->action) {
                    case "add mod":
                       $stradded = get_string("added", "moodle", get_string("modulename", $info[0]));
                       $changelist["$log->info"] = array ("operation" => "add", "text" => "$stradded:<BR><A HREF=\"$CFG->wwwroot/course/$log->url\">$modname</A>");
                    break;
                    case "update mod":
                       $strupdated = get_string("updated", "moodle", get_string("modulename", $info[0]));
                       if (! $changelist["$log->info"]) {
                           $changelist["$log->info"] = array ("operation" => "update", "text" => "$strupdated:<BR><A HREF=\"$CFG->wwwroot/course/$log->url\">$modname</A>");
                       }
                    break;
                    case "delete mod":
                       if ($changelist["$log->info"]["operation"] == "add") {
                           $changelist["$log->info"] = NULL;
                       } else {
                           $strdeleted = get_string("deletedactivity", "moodle", get_string("modulename", $info[0]));
                           $changelist["$log->info"] = array ("operation" => "delete", "text" => $strdeleted);
                       }
                    break;
                }
            }
        }
    }

    if ($changelist) {
        foreach ($changelist as $changeinfo => $change) {
            if ($change) {
                $changes[$changeinfo] = $change;
            }
        }
        if (count($changes) > 0) {
            print_headline(get_string("courseupdates").":");
            $content = true;
            foreach ($changes as $changeinfo => $change) {
                echo "<P><FONT SIZE=1>".$change["text"]."</FONT></P>";
            }
        }
    }


    // Now all we need to know are the new posts.

    $heading = false;
    foreach ($logs as $log) {
        
        if ($log->module == "forum") {
            $post = NULL;

            if ($log->action == "add post") {
                $post = get_record_sql("SELECT p.*, d.forum, u.firstname, u.lastname, 
                                               u.email, u.picture, u.id as userid
                                        FROM forum_discussions d, forum_posts p, user u 
                                        WHERE p.id = '$log->info' AND d.id = p.discussion AND p.user = u.id");

            } else if ($log->action == "add discussion") {
                $post = get_record_sql("SELECT p.*, d.forum, u.firstname, u.lastname, 
                                               u.email, u.picture, u.id as userid
                                        FROM forum_discussions d, forum_posts p, user u 
                                        WHERE d.id = '$log->info' AND d.firstpost = p.id AND p.user = u.id");
            }

            if ($post) {

                $teacherpost = "";
                if ($forum = get_record("forum", "id", $post->forum) ) {
                    if ($forum->type == "teacher") {
                        if (!isteacher($course->id)) {
                            continue;
                        } else {
                            $teacherpost = "COLOR=$COURSE_TEACHER_COLOR";
                        }
                    }
                }
                if (! $heading) {
                    print_headline(get_string("newforumposts").":");
                    $heading = true;
                    $content = true;
                }
                $date = userdate($post->modified, "%e %b, %H:%M");
                echo "<P><FONT SIZE=1 $teacherpost>$date - $post->firstname $post->lastname<BR>";
                echo "\"<A HREF=\"$CFG->wwwroot/mod/forum/$log->url\">";
                if ($log->action == "add") {
                    echo "<B>$post->subject</B>";
                } else {
                    echo "$post->subject";
                }
                echo "</A>\"</FONT></P>";
            }

        }
    }

    if (! $content) {
        echo "<FONT SIZE=2>".get_string("nothingnew")."</FONT>";
    }
}


function unenrol_student_in_course($user, $course) {
    global $db;

    return $db->Execute("DELETE FROM user_students WHERE user = '$user' AND course = '$course'");
}



function enrol_student_in_course($user, $course) {
    global $db;

	$timenow = time();

	$rs = $db->Execute("INSERT INTO user_students (user, course, start, end, time) 
                        VALUES ($user, $course, 0, 0, $timenow)");
	if ($rs) {
		return true;
	} else {
	    return false;
	}
}

function get_all_mods($courseid, &$mods, &$modnames, &$modnamesplural, &$modnamesused) {
// Returns a number of useful structures for course displays

    $mods          = NULL;    // course modules indexed by id
    $modnames      = NULL;    // all course module names
    $modnamesplural= NULL;    // all course module names (plural form)
    $modnamesused  = NULL;    // course module names used

    if ($allmods = get_records_sql("SELECT * FROM modules") ) {
        foreach ($allmods as $mod) {
            $modnames[$mod->name] = get_string("modulename", "$mod->name");
            $modnamesplural[$mod->name] = get_string("modulenameplural", "$mod->name");
        }
        asort($modnames);
    } else {
        error("No modules are installed!");
    }

    if ($rawmods = get_records_sql("SELECT cm.*, m.name as modname
                                     FROM modules m, course_modules cm
                                     WHERE cm.course = '$courseid' 
                                       AND cm.deleted = '0'
                                       AND cm.module = m.id ") ) {
        foreach($rawmods as $mod) {    // Index the mods
            $mods[$mod->id] = $mod;
            $mods[$mod->id]->modfullname = $modnames[$mod->modname];
            $modnamesused[$mod->modname] = $modnames[$mod->modname];
        }
        asort($modnamesused);
    }
}

function get_all_sections($courseid) {
    
    return get_records_sql("SELECT section, id, course, summary, sequence
                            FROM course_sections 
                            WHERE course = '$courseid' 
                            ORDER BY section");
}

function print_section($courseid, $section, $mods, $modnamesused, $absolute=false, $width="100%") {
    global $CFG;


    echo "<TABLE WIDTH=\"$width\"><TR><TD>\n";
    if ($section->sequence) {

        $sectionmods = explode(",", $section->sequence);

        foreach ($sectionmods as $modnumber) {
            $mod = $mods[$modnumber];
            $instancename = get_field("$mod->modname", "name", "id", "$mod->instance");
            echo "<IMG SRC=\"$CFG->wwwroot/mod/$mod->modname/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"$mod->modfullname\">";
            echo " <FONT SIZE=2><A TITLE=\"$mod->modfullname\"";
            echo "   HREF=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">$instancename</A></FONT>";
            if (isediting($courseid)) {
                echo make_editing_buttons($mod->id, $absolute);
            }
            echo "<BR>\n";
        }
    }
    echo "</TD></TR></TABLE><BR>\n\n";
}

function print_side_block($heading="", $list=NULL, $footer="", $icons=NULL, $width=180) {
    
    echo "<TABLE WIDTH=\"$width\">\n";
    echo "<TR><TD COLSPAN=2><P><B><FONT SIZE=2>$heading</TD></TR>\n";
    if ($list) {
        foreach($list as $key => $string) {
            echo "<TR><TD VALIGN=top WIDTH=12>";
            if ($icons[$key]) {
                echo $icons[$key];
            } else {
                echo "";
            }
            echo "</TD>\n<TD WIDTH=100% VALIGN=top>";
            echo "<P><FONT SIZE=2>$string</FONT></P>";
            echo "</TD></TR>\n";
        }
    }
    if ($footer) {
        echo "<TR><TD></TD><TD ALIGN=left><P><FONT SIZE=2>$footer</TD></TR>\n";
    }
    echo "</TABLE><BR>\n\n";
}

function print_admin_links ($siteid, $width=180) {
    global $THEME, $CFG;
    
    print_simple_box(get_string("administration"), $align="CENTER", $width, $color="$THEME->cellheading");
    $icon = "<IMG SRC=\"$CFG->wwwroot/pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    $moddata[]="<A HREF=\"$CFG->wwwroot/course/log.php?id=$siteid\">".get_string("sitelogs")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/admin/site.php\">".get_string("sitesettings")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/theme/index.php\">".get_string("choosetheme")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/admin/lang.php\">".get_string("checklanguage")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/course/edit.php\">".get_string("addnewcourse")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/course/teacher.php\">".get_string("assignteachers")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/course/delete.php\">".get_string("deletecourse")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/admin/user.php?newuser=true\">".get_string("addnewuser")."</A>";
    $modicon[]=$icon;
    $moddata[]="<A HREF=\"$CFG->wwwroot/admin/user.php\">".get_string("edituser")."</A>";
    $modicon[]=$icon;
    $fulladmin = "<P><A HREF=\"$CFG->wwwroot/admin/\">".get_string("admin")."</A>...";
    print_side_block("", $moddata, "$fulladmin", $modicon, $width);
    echo "<IMG SRC=\"$CFG->wwwroot/pix/spacer.gif\" WIDTH=\"$width\" HEIGHT=1><BR>";
}

function print_course_admin_links($courseid, $width=180) {
    global $THEME, $CFG;

    echo "<BR>";
    $adminicon[]="<IMG SRC=\"$CFG->wwwroot/pix/i/edit.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    if (isediting($courseid)) {
        $admindata[]="<A HREF=\"view.php?id=$courseid&edit=off\">".get_string("turneditingoff")."</A>";
    } else {
        $admindata[]="<A HREF=\"view.php?id=$courseid&edit=on\">".get_string("turneditingon")."</A>";
    }
    $admindata[]="<A HREF=\"edit.php?id=$courseid\">".get_string("settings")."...</A>";
    $adminicon[]="<IMG SRC=\"$CFG->wwwroot/pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    $admindata[]="<A HREF=\"log.php?id=$courseid\">".get_string("logs")."...</A>";
    $adminicon[]="<IMG SRC=\"$CFG->wwwroot/pix/i/log.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    $admindata[]="<A HREF=\"$CFG->wwwroot/files/index.php?id=$courseid\">".get_string("files")."...</A>";
    $adminicon[]="<IMG SRC=\"$CFG->wwwroot/files/pix/files.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";

    $admindata[]="<A HREF=\"$CFG->wwwroot/doc/view.php?id=$courseid&file=teacher.html\">".get_string("help")."...</A>";
    $adminicon[]="<IMG SRC=\"$CFG->wwwroot/mod/reading/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";

    if ($teacherforum = forum_get_course_forum($courseid, "teacher")) {
        $admindata[]="<A HREF=\"$CFG->wwwroot/mod/forum/view.php?f=$teacherforum->id\">".get_string("teacherforum")."</A>";
        $adminicon[]="<IMG SRC=\"$CFG->wwwroot/mod/forum/icon.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
    }

    print_simple_box(get_string("administration"), $align="CENTER", $width, $color="$THEME->cellheading");
    print_side_block("", $admindata, "", $adminicon, $width);
}


function print_log_graph($course, $userid=0, $type="course.png", $date=0) {
    global $CFG;
    echo "<IMG BORDER=0 SRC=\"$CFG->wwwroot/course/loggraph.php?id=$course->id&user=$userid&type=$type&date=$date\">";
}



/// MODULE FUNCTIONS /////////////////////////////////////////////////////////////////

function add_course_module($mod) {
    GLOBAL $db;

    $timenow = time();

    if (!$rs = $db->Execute("INSERT into course_modules 
                                SET course   = '$mod->course', 
                                    module   = '$mod->module',
                                    instance = '$mod->instance',
                                    section     = '$mod->section',
                                    added    = '$timenow' ")) {
        return 0;
    }
    
    // Get it out again - this is the most compatible way to determine the ID
    if ($rs = $db->Execute("SELECT id FROM course_modules 
                            WHERE module = $mod->module AND added = $timenow")) {
        return $rs->fields[0];
    } else {
        return 0;
    }

}

function add_mod_to_section($mod) {
// Returns the course_sections ID where the mod is inserted
    GLOBAL $db;

    if ($cw = get_record_sql("SELECT * FROM course_sections 
                              WHERE course = '$mod->course' AND section = '$mod->section'") ) {

        if ($cw->sequence) {
            $newsequence = "$cw->sequence,$mod->coursemodule";
        } else {
            $newsequence = "$mod->coursemodule";
        }
        if (!$rs = $db->Execute("UPDATE course_sections SET sequence = '$newsequence' WHERE id = '$cw->id'")) {
            return 0;
        } else {
            return $cw->id;     // Return course_sections ID that was used.
        }
       
    } else {  // Insert a new record
        if (!$rs = $db->Execute("INSERT into course_sections 
                                 SET course   = '$mod->course', 
                                     section     = '$mod->section',
                                     summary  = '',
                                     sequence = '$mod->coursemodule' ")) {
            return 0;
        }
        // Get it out again - this is the most compatible way to determine the ID
        if ($rs = $db->Execute("SELECT id FROM course_sections 
                                WHERE course = '$mod->course' AND section = '$mod->section'")) {
            return $rs->fields[0];
        } else {
            return 0;
        }
    }
}

function delete_course_module($mod) {
    return set_field("course_modules", "deleted", 1, "id", $mod);
}

function delete_mod_from_section($mod, $section) {
    GLOBAL $db;

    if ($cw = get_record("course_sections", "id", "$section") ) {

        $modarray = explode(",", $cw->sequence);

        if ($key = array_keys ($modarray, $mod)) {
            array_splice($modarray, $key[0], 1);
            $newsequence = implode(",", $modarray);
            return set_field("course_sections", "sequence", $newsequence, "id", $cw->id);
        } else {
            return false;
        }
       
    } else {  
        return false;
    }
}


function move_module($id, $move) {
    GLOBAL $db;

    if (!$move) {
        return true;
    }

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("This course module doesn't exist");
    }
    
    if (! $thissection = get_record("course_sections", "id", $cm->section)) {
        error("This course section doesn't exist");
    }

    $mods = explode(",", $thissection->sequence);

    $len = count($mods);
    $pos = array_keys($mods, $cm->id);
    $thepos = $pos[0];

    if ($len == 0 || count($pos) == 0 ) {
        error("Very strange. Could not find the required module in this section.");
    }

    if ($len == 1) {
        $first = true;
        $last = true;
    } else {
        $first = ($thepos == 0);
        $last  = ($thepos == $len - 1);
    }

    if ($move < 0) {    // Moving the module up

        if ($first) {
            if ($thissection->section == 1) {  // First section, do nothing
                return true;
            } else {               // Push onto end of previous section
                $prevsectionnumber = $thissection->section - 1;
                if (! $prevsection = get_record_sql("SELECT * FROM course_sections 
                                                  WHERE course='$thissection->course'
                                                  AND section='$prevsectionnumber' ")) {
                    error("Previous section ($prevsection->id) doesn't exist");
                }

                if ($prevsection->sequence) {
                    $newsequence = "$prevsection->sequence,$cm->id";
                } else {
                    $newsequence = "$cm->id";
                }

                if (! set_field("course_sections", "sequence", $newsequence, "id", $prevsection->id)) {
                    error("Previous section could not be updated");
                }

                if (! set_field("course_modules", "section", $prevsection->id, "id", $cm->id)) {
                    error("Module could not be updated");
                }

                array_splice($mods, 0, 1);
                $newsequence = implode(",", $mods);
                if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                    error("Module could not be updated");
                }

                return true;

            }
        } else {        // move up within this section
            $swap = $mods[$thepos-1];
            $mods[$thepos-1] = $mods[$thepos];
            $mods[$thepos] = $swap;
            
            $newsequence = implode(",", $mods);
            if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                error("This section could not be updated");
            }
            return true;
        }

    } else {            // Moving the module down

        if ($last) {
            $nextsectionnumber = $thissection->section + 1;
            if ($nextsection = get_record_sql("SELECT * FROM course_sections 
                                            WHERE course='$thissection->course'
                                            AND section='$nextsectionnumber' ")) {

                if ($nextsection->sequence) {
                    $newsequence = "$cm->id,$nextsection->sequence";
                } else {
                    $newsequence = "$cm->id";
                }

                if (! set_field("course_sections", "sequence", $newsequence, "id", $nextsection->id)) {
                    error("Next section could not be updated");
                }

                if (! set_field("course_modules", "section", $nextsection->id, "id", $cm->id)) {
                    error("Module could not be updated");
                }

                array_splice($mods, $thepos, 1);
                $newsequence = implode(",", $mods);
                if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                    error("This section could not be updated");
                }
                return true;

            } else {        // There is no next section, so just return
                return true;

            }
        } else {      // move down within this section
            $swap = $mods[$thepos+1];
            $mods[$thepos+1] = $mods[$thepos];
            $mods[$thepos] = $swap;
            
            $newsequence = implode(",", $mods);
            if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                error("This section could not be updated");
            }
            return true;
        }
    }
}

function make_editing_buttons($moduleid, $absolute=false) {
    global $CFG;

    $delete   = get_string("delete");
    $moveup   = get_string("moveup");
    $movedown = get_string("movedown");
    $update   = get_string("update");

    if ($absolute) {
        $path = "$CFG->wwwroot/course/";
    } else {
        $path = "";
    }
    return "&nbsp; &nbsp; 
          <A TITLE=\"$delete\" HREF=\"".$path."mod.php?delete=$moduleid\"><IMG 
             SRC=".$path."../pix/t/delete.gif BORDER=0></A>
          <A TITLE=\"$moveup\" HREF=\"".$path."mod.php?id=$moduleid&move=-1\"><IMG 
             SRC=".$path."../pix/t/up.gif BORDER=0></A>
          <A TITLE=\"$movedown\" HREF=\"".$path."mod.php?id=$moduleid&move=1\"><IMG 
             SRC=".$path."../pix/t/down.gif BORDER=0></A>
          <A TITLE=\"$update\" HREF=\"".$path."mod.php?update=$moduleid\"><IMG 
             SRC=".$path."../pix/t/edit.gif BORDER=0></A>";
}

?>
