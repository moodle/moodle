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

    return insert_record("assignment", $assignment);
}


function assignment_update_instance($assignment) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $assignment->timemodified = time();
    $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, $assignment->dueday, 
                                          $assignment->duehour, $assignment->dueminute);
    $assignment->id = $assignment->instance;

    return update_record("assignment", $assignment);
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

    return $result;
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

/// SQL STATEMENTS //////////////////////////////////////////////////////////////////

function assignment_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT a.name, u.firstname, u.lastname
                             FROM {$CFG->prefix}assignment a, 
                                  {$CFG->prefix}user u
                            WHERE a.id = '$log->info' 
                              AND u.id = '$log->userid'");
}

function assignment_count_real_submissions($assignment) {
/// Return all real assignment submissions by ENROLLED students (not empty ones)
    global $CFG;

    return count_records_sql("SELECT count(a.id)
                              FROM {$CFG->prefix}assignment_submissions a, 
                                   {$CFG->prefix}user_students s,
                                   {$CFG->prefix}user u
                             WHERE a.userid = s.userid
                               AND u.id = a.userid
                               AND s.course = '$assignment->course'
                               AND a.assignment = '$assignment->id' 
                               AND a.timemodified > 0");
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
    if ($submission->grade) {
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

                echo "<IMG SRC=\"$CFG->wwwroot/files/pix/$icon\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"File\">";
                echo "&nbsp;<A TARGET=\"uploadedfile\" HREF=\"$CFG->wwwroot/$ffurl\">$file</A>";
                echo "<BR>";
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
                    notify("Existing file '$file' has been deleted!");
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

?>
