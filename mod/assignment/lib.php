<?PHP  // $Id$

require_once("$CFG->dirroot/files/mimetypes.php");

define("OFFLINE",      "0");
define("UPLOADSINGLE", "1");

$ASSIGNMENT_TYPE = array (OFFLINE       => get_string("typeoffline",      "assignment"),
                          UPLOADSINGLE  => get_string("typeuploadsingle", "assignment") );

if (!isset($CFG->assignment_maxbytes)) {
    set_config("assignment_maxbytes", 1024000);  // Default maximum size for all assignments
} 


function assignment_add_instance($assignment) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $assignment->timemodified = time();
    
    $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, $assignment->dueday, 
                                          $assignment->duehour, $assignment->dueminute);

    if ($returnid = insert_record("assignment", $assignment)) {

        $event = NULL;
        $event->name        = $assignment->name;
        $event->description = $assignment->description;
        $event->courseid    = $assignment->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'assignment';
        $event->instance    = $returnid;
        $event->eventtype   = 'due';
        $event->timestart   = $assignment->timedue;
        $event->timeduration = 0;

        add_event($event);
    }

    return $returnid;
}


function assignment_update_instance($assignment) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $assignment->timemodified = time();
    $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, $assignment->dueday, 
                                          $assignment->duehour, $assignment->dueminute);
    $assignment->id = $assignment->instance;


    if ($returnid = update_record("assignment", $assignment)) {

        $event = NULL;

        if ($event->id = get_field('event', 'id', 'modulename', 'assignment', 'instance', $assignment->id)) {

            $event->name        = $assignment->name;
            $event->description = $assignment->description;
            $event->timestart   = $assignment->timedue;

            update_event($event);
        }
    }

    return $returnid;
}


function assignment_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $assignment = get_record("assignment", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("assignment_submissions", "assignment", "$assignment->id")) {
        $result = false;
    }

    if (! delete_records("assignment", "id", "$assignment->id")) {
        $result = false;
    }

    if (! delete_records('event', 'modulename', 'assignment', 'instance', $assignment->id)) {
        $result = false;
    }

    return $result;
}

function assignment_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every assignment event in the site is checked, else
// only assignment events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid == 0) {
        if (! $assignments = get_records("assignment")) {
            return true;
        }
    } else {
        if (! $assignments = get_records("assignment", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'assignment');

    foreach ($assignments as $assignment) {
        $event = NULL;
        $event->name        = addslashes($assignment->name);
        $event->description = addslashes($assignment->description);
        $event->timestart   = $assignment->timedue;

        if ($event->id = get_field('event', 'id', 'modulename', 'assignment', 'instance', $assignment->id)) {
            update_event($event);

        } else {
            $event->courseid    = $assignment->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'assignment';
            $event->instance    = $assignment->id;
            $event->eventtype   = 'due';
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $assignment->id);
            add_event($event);
        }

    }
    return true;
}


function assignment_user_outline($course, $user, $mod, $assignment) {
    if ($submission = assignment_get_submission($assignment, $user)) {
        
        if ($submission->grade) {
            $result->info = get_string("grade").": $submission->grade";
        }
        $result->time = $submission->timemodified;
        return $result;
    }
    return NULL;
}

function assignment_user_complete($course, $user, $mod, $assignment) {
    if ($submission = assignment_get_submission($assignment, $user)) {
        if ($basedir = assignment_file_area($assignment, $user)) {
            if ($files = get_directory_list($basedir)) {
                $countfiles = count($files)." ".get_string("uploadedfiles", "assignment");
                foreach ($files as $file) {
                    $countfiles .= "; $file";
                }
            }
        }

        print_simple_box_start();
        echo "<p><font size=1>";
        echo get_string("lastmodified").": ";
        echo userdate($submission->timemodified);
        echo assignment_print_difference($assignment->timedue - $submission->timemodified);
        echo "</font></p>";

        assignment_print_user_files($assignment, $user);

        echo "<br />";

        if (empty($submission->timemarked)) {
            print_string("notgradedyet", "assignment");
        } else {
            assignment_print_feedback($course, $submission);
        }

        print_simple_box_end();

    } else {
        print_string("notsubmittedyet", "assignment");
    }
}


function assignment_cron () {
// Function to be run periodically according to the moodle cron
// Finds all assignment notifications that have yet to be mailed out, and mails them

    global $CFG, $USER;

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($submissions = assignment_get_unmailed_submissions($cutofftime)) {
        $timenow = time();

        foreach ($submissions as $submission) {

            echo "Processing assignment submission $submission->id\n";

            if (! $user = get_record("user", "id", "$submission->userid")) {
                echo "Could not find user $post->userid\n";
                continue;
            }

            $USER->lang = $user->lang;

            if (! $course = get_record("course", "id", "$submission->course")) {
                echo "Could not find course $submission->course\n";
                continue;
            }

            if (! isstudent($course->id, $user->id) and !isteacher($course->id, $user->id)) {
                echo fullname($user)." not an active participant in $course->shortname\n";
                continue;
            }

            if (! $teacher = get_record("user", "id", "$submission->teacher")) {
                echo "Could not find teacher $submission->teacher\n";
                continue;
            }

            if (! $mod = get_coursemodule_from_instance("assignment", $submission->assignment, $course->id)) {
                echo "Could not find course module for assignment id $submission->assignment\n";
                continue;
            }

            if (! $mod->visible) {    /// Hold mail notification for hidden assignments until later
                continue;
            }

            $strassignments = get_string("modulenameplural", "assignment");
            $strassignment  = get_string("modulename", "assignment");

            unset($assignmentinfo);
            $assignmentinfo->teacher = fullname($teacher);
            $assignmentinfo->assignment = "$submission->name";
            $assignmentinfo->url = "$CFG->wwwroot/mod/assignment/view.php?id=$mod->id";

            $postsubject = "$course->shortname: $strassignments: $submission->name";
            $posttext  = "$course->shortname -> $strassignments -> $submission->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= get_string("assignmentmail", "assignment", $assignmentinfo);
            $posttext .= "---------------------------------------------------------------------\n";

            if ($user->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
                "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                "<a href=\"$CFG->wwwroot/mod/assignment/index.php?id=$course->id\">$strassignments</a> ->".
                "<a href=\"$CFG->wwwroot/mod/assignment/view.php?id=$mod->id\">$submission->name</a></font></p>";
                $posthtml .= "<hr><font face=\"sans-serif\">";
                $posthtml .= "<p>".get_string("assignmentmailhtml", "assignment", $assignmentinfo)."</p>";
                $posthtml .= "</font><hr>";
            } else {
                $posthtml = "";
            }

            if (! email_to_user($user, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: assignment cron: Could not send out mail for id $submission->id to user $user->id ($user->email)\n";
            }
            if (! set_field("assignment_submissions", "mailed", "1", "id", "$submission->id")) {
                echo "Could not update the mailed field for id $submission->id\n";
            }
        }
    }

    return true;
}

function assignment_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    $content = false;
    $assignments = NULL;

    if (!$logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'assignment' AND ".
                                           "action = 'upload' ", "time ASC")) {
        return false;
    }

    foreach ($logs as $log) {
        //Create a temp valid module structure (course,id)
        $tempmod->course = $log->course;
        $tempmod->id = $log->info;
        //Obtain the visible property from the instance
        $modvisible = instance_is_visible($log->module,$tempmod);
   
        //Only if the mod is visible
        if ($modvisible) {
            $assignments[$log->info] = assignment_log_info($log);
            $assignments[$log->info]->time = $log->time;
            $assignments[$log->info]->url  = $log->url;
        }
    }

    if ($assignments) {
        $strftimerecent = get_string("strftimerecent");
        $content = true;
        print_headline(get_string("newsubmissions", "assignment").":");
        foreach ($assignments as $assignment) {
            $date = userdate($assignment->time, $strftimerecent);
            echo "<p><font size=1>$date - ".fullname($assignment)."<br />";
            echo "\"<a href=\"$CFG->wwwroot/mod/assignment/$assignment->url\">";
            echo "$assignment->name";
            echo "</a>\"</font></p>";
        }
    }
 
    return $content;
}

function assignment_grades($assignmentid) {
/// Must return an array of grades, indexed by user, and a max grade.


    if (!$assignment = get_record("assignment", "id", $assignmentid)) {
        return NULL;
    }

    $grades = get_records_menu("assignment_submissions", "assignment", 
                               $assignment->id, "", "userid,grade");

    if ($assignment->grade >= 0) {
        $return->grades = $grades;
        $return->maxgrade = $assignment->grade;

    } else {
        $scaleid = - ($assignment->grade);
        if ($scale = get_record("scale", "id", $scaleid)) {
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

function assignment_get_participants($assignmentid) {
//Returns the users with data in one assignment
//(users with records in assignment_submissions, students and teachers)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}assignment_submissions a
                                 WHERE a.assignment = '$assignmentid' and
                                       u.id = a.userid");
    //Get teachers
    $teachers = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}assignment_submissions a
                                 WHERE a.assignment = '$assignmentid' and
                                       u.id = a.teacher");

    //Add teachers to students
    if ($teachers) {
        foreach ($teachers as $teacher) {
            $students[$teacher->id] = $teacher;
        }
    }
    //Return students array (it contains an array of unique users)
    return ($students);
}

function assignment_scale_used ($assignmentid,$scaleid) {
//This function returns if a scale is being used by one assignment

    $return = false;

    $rec = get_record("assignment","id","$assignmentid","grade","-$scaleid");

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/// SQL STATEMENTS //////////////////////////////////////////////////////////////////

function assignment_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT a.name, u.firstname, u.lastname
                             FROM {$CFG->prefix}assignment a, 
                                  {$CFG->prefix}user u
                            WHERE a.id = '$log->info' 
                              AND u.id = '$log->userid'");
}

function assignment_count_real_submissions($assignment, $groupid=0) {
/// Return all real assignment submissions by ENROLLED students (not empty ones)
    global $CFG;

    if ($groupid) {     /// How many in a particular group?
        return count_records_sql("SELECT COUNT(DISTINCT g.userid, g.groupid)
                                     FROM {$CFG->prefix}assignment_submissions a,
                                          {$CFG->prefix}groups_members g
                                    WHERE a.assignment = $assignment->id 
                                      AND a.timemodified > 0
                                      AND g.groupid = '$groupid' 
                                      AND a.userid = g.userid ");
    } else {                                  
        return count_records_sql("SELECT COUNT(*)
                                  FROM {$CFG->prefix}assignment_submissions a, 
                                       {$CFG->prefix}user_students s
                                 WHERE a.assignment = '$assignment->id' 
                                   AND a.timemodified > 0
                                   AND s.course = '$assignment->course'
                                   AND a.userid = s.userid ");
    }
}

function assignment_get_all_submissions($assignment, $sort="", $dir="DESC") {
/// Return all assignment submissions by ENROLLED students (even empty)
    global $CFG;

    if ($sort == "lastname" or $sort == "firstname") {
        $sort = "u.$sort $dir";
    } else if (empty($sort)) {
        $sort = "a.timemodified DESC";
    } else {
        $sort = "a.$sort $dir";
    }
    return get_records_sql("SELECT a.* 
                              FROM {$CFG->prefix}assignment_submissions a, 
                                   {$CFG->prefix}user_students s,
                                   {$CFG->prefix}user u
                             WHERE a.userid = s.userid
                               AND u.id = a.userid
                               AND s.course = '$assignment->course'
                               AND a.assignment = '$assignment->id' 
                          ORDER BY $sort");
}

function assignment_get_users_done($assignment) {
/// Return list of users who have done an assignment
    global $CFG;
    return get_records_sql("SELECT u.* 
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_students s, 
                                   {$CFG->prefix}assignment_submissions a
                             WHERE s.course = '$assignment->course' 
                               AND s.userid = u.id
                               AND u.id = a.userid 
                               AND a.assignment = '$assignment->id'
                          ORDER BY a.timemodified DESC");
}

function assignment_get_unmailed_submissions($cutofftime) {
/// Return list of marked submissions that have not been mailed out for currently enrolled students
    global $CFG;
    return get_records_sql("SELECT s.*, a.course, a.name
                              FROM {$CFG->prefix}assignment_submissions s, 
                                   {$CFG->prefix}assignment a,
                                   {$CFG->prefix}user_students us
                             WHERE s.mailed = 0 
                               AND s.timemarked < $cutofftime 
                               AND s.timemarked > 0
                               AND s.assignment = a.id
                               AND s.userid = us.userid
                               AND a.course = us.course");
}


//////////////////////////////////////////////////////////////////////////////////////

function assignment_file_area_name($assignment, $user) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$assignment->course/$CFG->moddata/assignment/$assignment->id/$user->id";
}

function assignment_file_area($assignment, $user) {
    return make_upload_directory( assignment_file_area_name($assignment, $user) );
}

function assignment_get_submission($assignment, $user) {
    $submission = get_record("assignment_submissions", "assignment", $assignment->id, "userid", $user->id);
    if (!empty($submission->timemodified)) {
        return $submission;
    }
    return NULL;
}

function assignment_print_difference($time) {
    if ($time < 0) {
        $timetext = get_string("late", "assignment", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("early", "assignment", format_time($time));
        return " ($timetext)";
    }
}

function assignment_print_submission($assignment, $user, $submission, $teachers, $grades) {
    global $THEME, $USER;

    echo "\n<TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=10 align=center>";

    echo "\n<TR>";
    if ($assignment->type == OFFLINE) {
        echo "\n<TD BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    } else {
        echo "\n<TD ROWSPAN=2 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    }
    print_user_picture($user->id, $assignment->course, $user->picture);
    echo "</TD>";
    echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\">".fullname($user, true);
    if ($assignment->type != OFFLINE and $submission->timemodified) {
        echo "&nbsp;&nbsp;<FONT SIZE=1>".get_string("lastmodified").": ";
        echo userdate($submission->timemodified);
        echo assignment_print_difference($assignment->timedue - $submission->timemodified);
        echo "</FONT>";
    }
    echo "</TR>";

    if ($assignment->type != OFFLINE) {
        echo "\n<TR><TD BGCOLOR=\"$THEME->cellcontent\">";
        if ($submission->timemodified) {
            assignment_print_user_files($assignment, $user);
        } else {
            print_string("notsubmittedyet", "assignment");
        }
        echo "</TD></TR>";
    }

    echo "\n<TR>";
    echo "<TD WIDTH=35 VALIGN=TOP>";
    if (!$submission->teacher) {
        $submission->teacher = $USER->id;
    }
    print_user_picture($submission->teacher, $assignment->course, $teachers[$submission->teacher]->picture);
    if ($submission->timemodified > $submission->timemarked) {
        echo "<TD BGCOLOR=\"$THEME->cellheading2\">";
    } else {
        echo "<TD BGCOLOR=\"$THEME->cellheading\">";
    }
    if (!$submission->grade and !$submission->timemarked) {
        $submission->grade = -1;   /// Hack to stop zero being selected on the menu below (so it shows 'no grade')
    }
    echo get_string("feedback", "assignment").":";
    choose_from_menu($grades, "g$submission->id", $submission->grade, get_string("nograde"));
    if ($submission->timemarked) {
        echo "&nbsp;&nbsp;<FONT SIZE=1>".userdate($submission->timemarked)."</FONT>";
    }
    echo "<BR><TEXTAREA NAME=\"c$submission->id\" ROWS=6 COLS=60 WRAP=virtual>";
    p($submission->comment);
    echo "</TEXTAREA><BR>";
    echo "</TD></TR>";
   
    echo "</TABLE><BR CLEAR=ALL>\n";
}

function assignment_print_feedback($course, $submission) {
    global $CFG, $THEME, $RATING;

    if (! $teacher = get_record("user", "id", $submission->teacher)) {
        error("Weird assignment error");
    }

    echo "\n<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 ALIGN=CENTER><TR><TD BGCOLOR=#888888>";
    echo "\n<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 VALIGN=TOP>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=3 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">".fullname($teacher);
    echo "&nbsp;&nbsp;<FONT SIZE=2><I>".userdate($submission->timemarked)."</I>";
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";

    echo "<P ALIGN=RIGHT><FONT SIZE=-1><I>";
    if ($submission->grade or $submission->timemarked) {
        echo get_string("grade").": $submission->grade";
    } else {
        echo get_string("nograde");
    }
    echo "</I></FONT></P>";

    echo text_to_html($submission->comment);
    echo "</TD></TR></TABLE>";
    echo "</TD></TR></TABLE>";
}


function assignment_print_user_files($assignment, $user) {
// Arguments are objects

    global $CFG;

    $filearea = assignment_file_area_name($assignment, $user);

    if ($basedir = assignment_file_area($assignment, $user)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$filearea/$file";
                } else {
                    $ffurl = "file.php?file=/$filearea/$file";
                }

                echo "<img src=\"$CFG->pixpath/f/$icon\" height=16 width=16 border=0 alt=\"file\">";
                echo "&nbsp;<a target=\"uploadedfile\" href=\"$CFG->wwwroot/$ffurl\">$file</a>";
                echo "<br />";
            }
        }
    }
}

function assignment_delete_user_files($assignment, $user, $exception) {
// Deletes all the user files in the assignment area for a user
// EXCEPT for any file named $exception

    if ($basedir = assignment_file_area($assignment, $user)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                if ($file != $exception) {
                    unlink("$basedir/$file");
                    notify(get_string("existingfiledeleted", "assignment", $file));
                }
            }
        }
    }
}

function assignment_print_upload_form($assignment) {
// Arguments are objects

    echo "<DIV ALIGN=CENTER>";
    echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=upload.php>";
    echo " <INPUT TYPE=hidden NAME=MAX_FILE_SIZE value=\"$assignment->maxbytes\">";
    echo " <INPUT TYPE=hidden NAME=id VALUE=\"$assignment->id\">";
    echo " <INPUT NAME=\"newfile\" TYPE=\"file\" size=\"50\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"".get_string("uploadthisfile")."\">";
    echo "</FORM>";
    echo "</DIV>";
}

function assignment_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $assignment="0", $user="", $groupid="")  {
// Returns all assignments since a given time.  If assignment is specified then
// this restricts the results
    
    global $CFG;

    if ($assignment) {
        $assignmentselect = " AND cm.id = '$assignment'";
    } else {
        $assignmentselect = "";
    }
    if ($user) {
        $userselect = " AND u.id = '$user'";
    } else { 
        $userselect = "";
    }

    $assignments = get_records_sql("SELECT asub.*, u.firstname, u.lastname, u.picture, u.id as userid,
                                           a.grade as maxgrade, name, cm.instance, cm.section, a.type
                                  FROM {$CFG->prefix}assignment_submissions asub,
                                       {$CFG->prefix}user u,
                                       {$CFG->prefix}assignment a,
                                       {$CFG->prefix}course_modules cm
                                 WHERE asub.timemodified > '$sincetime'
                                   AND asub.userid = u.id $userselect
                                   AND a.id = asub.assignment $assignmentselect
                                   AND cm.course = '$courseid'
                                   AND cm.instance = a.id
                                 ORDER BY asub.timemodified ASC");

    if (empty($assignments))
      return;

    foreach ($assignments as $assignment) {
        if (empty($groupid) || ismember($groupid, $assignment->userid)) {
    
          $tmpactivity->type = "assignment";
          $tmpactivity->defaultindex = $index;
          $tmpactivity->instance = $assignment->instance;
          $tmpactivity->name = $assignment->name;
          $tmpactivity->section = $assignment->section;

          $tmpactivity->content->grade = $assignment->grade;
          $tmpactivity->content->maxgrade = $assignment->maxgrade;
          $tmpactivity->content->type = $assignment->type;

          $tmpactivity->user->userid = $assignment->userid;
          $tmpactivity->user->fullname = fullname($assignment);
          $tmpactivity->user->picture = $assignment->picture;

          $tmpactivity->timestamp = $assignment->timemodified;

          $activities[] = $tmpactivity;

          $index++;
        }
    }

    return;
}

function assignment_print_recent_mod_activity($activity, $course, $detail=false)  {
    global $CFG, $THEME;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td width=\"100%\"><font size=2>";


    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=16 width=16 alt=\"$activity->type\">  ";
        echo "<a href=\"$CFG->wwwroot/mod/assignment/view.php?id=" . $activity->instance . "\">"
             . $activity->name . "</a> - ";

    }

    if (isteacher($USER)) {
        $grades = "(" .  $activity->content->grade . " / " . $activity->content->maxgrade . ") ";

        $assignment->id = $activity->instance;
        $assignment->course = $course;
        $user->id = $activity->user->userid;

        echo $grades;
        if ($activity->content->type == UPLOADSINGLE) {
            $file = assignment_get_user_file($assignment, $user);
            echo "<img src=\"$CFG->pixpath/f/$file->icon\" height=16 width=16 border=0 alt=\"file\">";
            echo "&nbsp;<a target=\"uploadedfile\" HREF=\"$CFG->wwwroot/$file->url\">$file->name</A>";
        }
        echo "<br>";
    }
    echo "<a href=\"$CFG->wwwroot/user/view.php?id="
         . $activity->user->userid . "&course=$course\">"
         . $activity->user->fullname . "</a> ";

    echo " - " . userdate($activity->timestamp);

    echo "</font></td></tr>";
    echo "</table>";

    return;
}

function assignment_get_user_file($assignment, $user) {
    global $CFG;

    $tmpfile = "";

    $filearea = assignment_file_area_name($assignment, $user);

    if ($basedir = assignment_file_area($assignment, $user)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$filearea/$file";
                } else {
                    $ffurl = "file.php?file=/$filearea/$file";
                }
                $tmpfile->url  = $ffurl;
                $tmpfile->name = $file;
                $tmpfile->icon = $icon;
            }
        }
    }
    return $tmpfile;
}

?>
