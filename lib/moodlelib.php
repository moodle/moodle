<?PHP // $Id$

//
// moodlelib.php
//
// Large collection of useful functions used by many parts of Moodle.
//
// Martin Dougiamas, 2000
//


/// STANDARD WEB PAGE PARTS ///////////////////////////////////////////////////

function print_header ($title="", $heading="", $navigation="", $focus="", $meta="",$cache=true) {
// $title - appears top of window
// $heading - appears top of page
// $navigation - premade navigation string
// $focus - indicates form element eg  inputform.password
// $meta - meta tags in the header
    global $USER, $CFG, $THEME;

    if (file_exists("$CFG->dirroot/theme/$CFG->theme/styles.css")) {
        $styles = "$CFG->wwwroot/theme/$CFG->theme/styles.css";
    } else {
        $styles = "$CFG->wwwroot/theme/standard/styles.css";
    }
 
    if (!$cache) {   // Do everything we can to prevent clients and proxies caching
        @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        @header("Pragma: no-cache");
        $meta .= "\n<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">";
        $meta .= "\n<META HTTP-EQUIV=\"Expires\" CONTENT=\"0\">";
    }
 
    include ("$CFG->dirroot/theme/$CFG->theme/header.html");
}

function print_footer ($course=NULL) {
// Can provide a course object to make the footer contain a link to 
// to the course home page, otherwise the link will go to the site home
    global $USER, $CFG, $THEME;

    if ($course) {
        $homelink = "<A TARGET=_top HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A>";
    } else {
        $homelink = "<A TARGET=_top HREF=\"$CFG->wwwroot\">Home</A>";
    }
    include ("$CFG->dirroot/theme/$CFG->theme/footer.html");
}

function print_navigation ($navigation) {
   global $CFG;

   if (! $site = get_record("course", "category", 0)) {
       $site->shortname = "Home";
   }
   if ($navigation) {
       echo "<A TARGET=_top HREF=\"$CFG->wwwroot/\">$site->shortname</A> -> $navigation";
   }
}

function print_heading($text, $align="CENTER", $size=3) {
    echo "<P ALIGN=\"$align\"><FONT SIZE=\"$size\"><B>$text</B></FONT></P>";
}

function print_simple_box($message, $align="", $width="", $color="#FFFFFF", $padding=5, $border=1) {
    print_simple_box_start($align, $width, $color, $padding, $border);
    echo "<P>$message</P>";
    print_simple_box_end();
}

function print_simple_box_start($align="", $width="", $color="#FFFFFF", $padding=5, $border=1) {
    global $THEME;

    if ($align) {
        $tablealign = "ALIGN=\"$align\"";
    }
    if ($width) {
        $tablewidth = "WIDTH=\"$width\"";
        $innertablewidth = "WIDTH=\"100%\"";
    }
    echo "<TABLE $tablealign $tablewidth BORDER=0 CELLPADDING=\"$border\" CELLSPACING=0>";
    echo "<TR><TD BGCOLOR=\"$THEME->borders\">\n";
    echo "<TABLE $innertablewidth BORDER=0 CELLPADDING=\"$padding\" CELLSPACING=0><TR><TD BGCOLOR=\"$color\">";
}

function print_simple_box_end() {
    echo "</TD></TR></TABLE>";
    echo "</TD></TR></TABLE>";
}

function print_single_button($link, $options, $label="OK") {
    echo "<FORM ACTION=\"$link\" METHOD=GET>";
    foreach ($options as $name => $value) {
        echo "<INPUT TYPE=hidden NAME=\"$name\" VALUE=\"$value\">";
    }
    echo "<INPUT TYPE=submit VALUE=\"$label\"></FORM>";
}

function print_user_picture($userid, $courseid, $picture, $large=false, $returnstring=false) {
    global $CFG;

    $output = "<A HREF=\"$CFG->wwwroot/user/view.php?id=$userid&course=$courseid\">";
    if ($large) {
        $file = "f1.jpg";
        $size = 100;
    } else {
        $file = "f2.jpg";
        $size = 35;
    }
    if ($picture) {
        $output .= "<IMG SRC=\"$CFG->wwwroot/user/pix.php/$userid/$file\" BORDER=0 WIDTH=$size HEIGHT=$size ALT=\"\">";
    } else {
        $output .= "<IMG SRC=\"$CFG->wwwroot/user/default/$file\" BORDER=0 WIDTH=$size HEIGHT=$size ALT=\"\">";
    }
    $output .= "</A>";

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}

function print_table($table) {
// Prints a nicely formatted table.
// $table is an object with three properties.
//     $table->head      is an array of heading names.
//     $table->align     is an array of column alignments
//     $table->data[]    is an array of arrays containing the data.

    if ( $table->align) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = "ALIGN=\"$aa\"";
            } else {
                $align[$key] = "";
            }
        }
    }

    echo "<BR>";

    print_simple_box_start("CENTER","","#FFFFFF",0);
    echo "<TABLE BORDER=0 valign=top align=center cellpadding=10 cellspacing=1>\n";

    if ($table->head) {
        echo "<TR>";
        foreach ($table->head as $heading) {
            echo "<TH>$heading</TH>";
        }
        echo "</TR>\n";
    }

    foreach ($table->data as $row) {
        echo "<TR VALIGN=TOP>";
        foreach ($row as $key => $item) {
            echo "<TD ".$align[$key].">$item</TD>";
        }
        echo "</TR>\n";
    }
    echo "</TABLE>\n";
    print_simple_box_end();

    return true;
}

function print_editing_switch($courseid) {
    global $CFG, $USER;

    if (isadmin() || isteacher($courseid)) {
        if ($USER->editing) {
            echo "<A HREF=\"$CFG->wwwroot/course/view.php?id=$courseid&edit=off\">Turn editing off</A>";
        } else {
            echo "<A HREF=\"$CFG->wwwroot/course/view.php?id=$courseid&edit=on\">Turn editing on</A>";
        }
    }
}


function userdate($date, $format="", $timezone=99) {
    global $USER;

    if ($format == "") {
        $format = "l, j F Y, g:i A";
    }
    if ($timezone == 99) {
        $timezone = (float)$USER->timezone;
    }
    if (abs($timezone) > 12) {
        return date("$format T", $date);
    }
    return gmdate($format, $date + (int)($timezone * 3600));
}

function usergetdate($date, $timezone=99) {
    global $USER;

    if ($timezone == 99) {
        $timezone = (float)$USER->timezone;
    }
    if (abs($timezone) > 12) {
        return getdate($date);
    }
    return getdate($date + (int)($timezone * 3600));
}


function error ($message, $link="") {
    global $CFG, $SESSION;

    print_header("Error");
    echo "<BR>";
    print_simple_box($message, "center", "", "#FFBBBB");
   
    if (!$link) {
        if ( !empty($SESSION->fromurl) ) {
            $link = "$SESSION->fromurl";
            unset($SESSION->fromurl);
        } else {
            $link = "$CFG->wwwroot";
        }
    }
    print_heading("<A HREF=\"$link\">Continue</A>");
    print_footer();
    die;
}

function helpbutton ($info, $type="file") {
    global $CFG;
    $url = "/help.php?$type=help.$info.php";
    $image = "<IMG BORDER=0 ALT=help SRC=\"$CFG->wwwroot/pix/help.gif\">";
    link_to_popup_window ($url, "popup", $image, $height=400, $width=500);
}

function notice ($message, $link="") {
    global $THEME, $HTTP_REFERER;

    if (!$link) {
        $link = $HTTP_REFERER;
    }

    echo "<BR>";
    print_simple_box($message, "center", "", "$THEME->cellheading");
    print_heading("<A HREF=\"$link\">Continue</A>");
    print_footer();
    die;
}

function notice_yesno ($message, $linkyes, $linkno) {
    global $THEME;

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<P ALIGN=CENTER><FONT SIZE=3>$message</FONT></P>";
    echo "<P ALIGN=CENTER><FONT SIZE=3><B>";
    echo "<A HREF=\"$linkyes\">Yes</A>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<A HREF=\"$linkno\">No</A>";
    echo "</B></FONT></P>";
    print_simple_box_end();
}

function redirect($url, $message="", $delay=0) {
// Uses META tags to redirect the user, after printing a notice
    global $THEME;

    echo "<META HTTP-EQUIV='Refresh' CONTENT='$delay; URL=$url'>";

    if (!empty($message)) {
        print_header();
        echo "<CENTER>";
        echo "<P>$message</P>";
        echo "<P>( <A HREF=\"$url\">Continue</A> )</P>";
        echo "</CENTER>";
    }
    die; 
}

function notify ($message) {
    echo "<P align=center><B><FONT COLOR=#FF0000>$message</FONT></B></P>\n";
}



/// PARAMETER HANDLING ////////////////////////////////////////////////////

function require_variable($var) {

    if (! isset($var)) {
        error("A required parameter was missing");
    }
}

function optional_variable(&$var, $default=0) {
    if (! isset($var)) {
        $var = $default;
    }
}




/// DATABASE HANDLING ////////////////////////////////////////////////

function execute_sql($command) {
// Completely general

    global $db;
    
    $result = $db->Execute("$command");

    if ($result) {
        echo "<P><FONT COLOR=green>SUCCESS: $command</FONT></P>";
        return true;
    } else {
        echo "<P><FONT COLOR=red>ERROR: $command </FONT></P>";
        return false;
    }
}

function modify_database($sqlfile) {
// Assumes that the input text file consists of a number 
// of SQL statements ENDING WITH SEMICOLONS.  The semicolons
// MUST be the last character in a line.
// Lines that are blank or that start with "#" are ignored.
// Only tested with mysql dump files (mysqldump -p -d moodle)


    if (file_exists($sqlfile)) {
        $success = true;
        $lines = file($sqlfile);
        $command = "";

        while ( list($i, $line) = each($lines) ) {
            $line = chop($line);
            $length = strlen($line);

            if ($length  &&  substr($line, 0, 1) <> "#") { 
                if (substr($line, $length-1, 1) == ";") {
                    $line = substr($line, 0, $length-1);   // strip ;
                    $command .= $line;
                    if (! execute_sql($command)) {
                        $success = false;
                    }
                    $command = "";
                } else {
                    $command .= $line;
                }
            }
        }

    } else {
        $success = false;
        echo "<P>Tried to modify database, but \"$sqlfile\" doesn't exist!</P>";
    }

    return $success;
}


function record_exists($table, $field, $value) {
    global $db;

    $rs = $db->Execute("SELECT * FROM $table WHERE $field = '$value' LIMIT 1");
    if (!$rs) return false;

    if ( $rs->RecordCount() ) {
        return true;
    } else {
        return false;
    }
}

function record_exists_sql($sql) {
    global $db;

    $rs = $db->Execute($sql);
    if (!$rs) return false;

    if ( $rs->RecordCount() ) {
        return true;
    } else {
        return false;
    }
}


function count_records($table, $selector, $value) {
// Get all the records and count them
    global $db;

    $rs = $db->Execute("SELECT COUNT(*) FROM $table WHERE $selector = '$value'");
    if (!$rs) return 0;

    return $rs->fields[0];
}

function count_records_sql($sql) {
// Get all the records and count them
    global $db;

    $rs = $db->Execute("$sql");
    if (!$rs) return 0;

    return $rs->fields[0];
}

function get_record($table, $selector, $value) {
// Get a single record as an object
    global $db;

    $rs = $db->Execute("SELECT * FROM $table WHERE $selector = '$value'");
    if (!$rs) return false;

    if ( $rs->RecordCount() == 1 ) {
        return (object)$rs->fields;
    } else {
        return false;
    }
}

function get_record_sql($sql) {
// Get a single record as an object
// The sql statement is provided as a string.

    global $db;

    $rs = $db->Execute("$sql");
    if (!$rs) return false;

    if ( $rs->RecordCount() == 1 ) {
        return (object)$rs->fields;
    } else {
        return false;
    }
}

function get_records($table, $selector, $value, $sort="") {
// Get a number of records as an array of objects
// Can optionally be sorted eg "time ASC" or "time DESC"
// The "key" is the first column returned, eg usually "id"
    global $db;

    if ($sort) {
        $sortorder = "ORDER BY $sort";
    }
    $sql = "SELECT * FROM $table WHERE $selector = '$value' $sortorder";

    return get_records_sql($sql);
}

function get_records_sql($sql) {
// Get a number of records as an array of objects
// The "key" is the first column returned, eg usually "id"
// The sql statement is provided as a string.

    global $db;

    $rs = $db->Execute("$sql");
    if (!$rs) return false;

    if ( $rs->RecordCount() > 0 ) {
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
                $objects[$key] = (object) $record;
            }
            return $objects;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_records_sql_menu($sql) {
// Given an SQL select, this function returns an associative 
// array of the first two columns.  This is most useful in 
// combination with the choose_from_menu function to create 
// a form menu.

    global $db;

    $rs = $db->Execute("$sql");
    if (!$rs) return false;

    if ( $rs->RecordCount() > 0 ) {
        while (!$rs->EOF) {
            $menu[$rs->fields[0]] = $rs->fields[1];
            $rs->MoveNext();
        }
        return $menu;
        
    } else {
        return false;
    }
}

function get_field($table, $field, $selector, $value) {
    global $db;

    $rs = $db->Execute("SELECT $field FROM $table WHERE $selector = '$value'");
    if (!$rs) return false;

    if ( $rs->RecordCount() == 1 ) {
        return $rs->fields["$field"];
    } else {
        return false;
    }
}

function set_field($table, $field, $newvalue, $selector, $value) {
    global $db;

    return $db->Execute("UPDATE $table SET $field = '$newvalue' WHERE $selector = '$value'");
}


function delete_records($table, $selector, $value) {
// Delete one or more records from a table
    global $db;

    return $db->Execute("DELETE FROM $table WHERE $selector = '$value'");
}

function insert_record($table, $dataobject) {
// Insert a record into a table and return the "id" field
// $dataobject is an object containing needed data

    global $db;

    // Determine all the fields needed
    if (! $columns = $db->MetaColumns("$table")) {
        return false;
    }

    $data = (array)$dataobject;

    // Pull out data matching these fields
    foreach ($columns as $column) {
        if ($column->name <> "id" && $data[$column->name] ) {
            $ddd[$column->name] = $data[$column->name];
        }
    }

    // Construct SQL queries
    if (! $numddd = count($ddd)) {
        return 0;
    }

    $count = 0;
    $insert = "";
    $select = "";

    foreach ($ddd as $key => $value) {
        $count++;
        $insert .= "$key = '$value'";
        $select .= "$key = '$value'";
        if ($count < $numddd) {
            $insert .= ", ";
            $select .= " AND ";
        }
    }

    if (! $rs = $db->Execute("INSERT INTO $table SET $insert")) {
        return false;
    } 

    // Pull it out again to find the id.  This is the most cross-platform method.
    if ($rs = $db->Execute("SELECT id FROM $table WHERE $select")) {
        return $rs->fields[0];
    } else {
        return false;
    }
}


function update_record($table, $dataobject) {
// Update a record in a table
// $dataobject is an object containing needed data

    global $db;

    if (! isset($dataobject->id) ) {
        return false;
    }

    // Determine all the fields in the table
    $columns = $db->MetaColumns($table);
    $data = (array)$dataobject;

    // Pull out data matching these fields
    foreach ($columns as $column) {
        if ($column->name <> "id" && isset($data[$column->name]) ) {
            $ddd[$column->name] = $data[$column->name];
        }
    }

    // Construct SQL queries
    $numddd = count($ddd);
    $count = 0;
    $update = "";

    foreach ($ddd as $key => $value) {
        $count++;
        $update .= "$key = '$value'";
        if ($count < $numddd) {
            $update .= ", ";
        }
    }

    if ($rs = $db->Execute("UPDATE $table SET $update WHERE id = '$dataobject->id'")) {
        return true;
    } else {
        return false;
    }
}



/// USER DATABASE ////////////////////////////////////////////////

function get_user_info_from_db($field, $value) {

    global $db;

    if (!$field || !$value) 
        return false;

    $result = $db->Execute("SELECT * FROM user WHERE $field = '$value'");

    if ( $result->RecordCount() == 1 ) {
        $user = (object)$result->fields;

        $rs = $db->Execute("SELECT course FROM user_students WHERE user = '$user->id' ");
        while (!$rs->EOF) {
            $course = $rs->fields["course"];
            $user->student["$course"] = true;
            $rs->MoveNext();
        }

        $rs = $db->Execute("SELECT course FROM user_teachers WHERE user = '$user->id' ");
        while (!$rs->EOF) {
            $course = $rs->fields["course"];
            $user->teacher["$course"] = true;
            $rs->MoveNext();
        }

        $rs = $db->Execute("SELECT * FROM user_admins WHERE user = '$user->id' ");
        while (!$rs->EOF) {
            $user->admin = true;
            $rs->MoveNext();
        }

        if ($course = get_record("course", "category", 0)) {  
            // Everyone is always a member of the top course
            $user->student["$course->id"] = true;
        }

        return $user;

    } else {
        return false;
    }
}

function update_user_in_db() {

   global $db, $USER, $REMOTE_ADDR;

   if (!isset($USER->id)) 
       return false;

   $timenow = time();
   if ($db->Execute("UPDATE LOW_PRIORITY user SET lastIP='$REMOTE_ADDR', lastaccess='$timenow' 
                                               WHERE id = '$USER->id' ")) {
       return true;
   } else {
       return false;
   }
}

function require_login($course=0) {
// if they aren't already logged in, then send them off to login
// $course is optional - if left out then it just requires that 
// that they have an account on the system.

    global $CFG, $SESSION, $USER, $FULLME, $HTTP_REFERER, $PHPSESSID;
      
    if (! (isset( $USER->loggedin ) && $USER->confirmed) ) { 
        $SESSION->wantsurl = $FULLME;
        $SESSION->fromurl = $HTTP_REFERER;
        if ($PHPSESSID) { // Cookies not enabled.
            redirect("$CFG->wwwroot/login/?PHPSESSID=$PHPSESSID");
        } else {
            redirect("$CFG->wwwroot/login/");
        }
        die;
 
    } else if ($course) {
        if (! ($USER->student[$course] || $USER->teacher[$course] || $USER->admin ) ) {
            if (!record_exists("course", "id", $course)) {
                error("That course doesn't exist");
            }

            $SESSION->wantsurl = $FULLME;
            redirect("$CFG->wwwroot/course/enrol.php?id=$course");
            die;
        }
    }

    update_user_in_db();
}



function update_login_count() {
    global $SESSION;

    $max_logins = 10;

    if (empty($SESSION->logincount)) {
        $SESSION->logincount = 1;
    } else {
        $SESSION->logincount++;
    }

    if ($SESSION->logincount > $max_logins) {
        unset($SESSION->wantsurl);
        error("Sorry, you have exceeded the allowed number of login attempts. Restart your browser.");
    }
}


function isadmin($userid=0) {
    global $USER;

    if (!$userid) {
        return $USER->admin;
    }

    return record_exists_sql("SELECT * FROM user_admins WHERE user='$userid'");
}

function isteacher($course, $userid=0) {
    global $USER;

    if (isadmin($userid)) {  // admins can do anything the teacher can
        return true;
    }

    if (!$userid) {
        return $USER->teacher[$course];
    }

    return record_exists_sql("SELECT * FROM user_teachers WHERE user='$userid' AND course='$course'");
}


function isstudent($course, $userid=0) {
    global $USER;

    if (!$userid) {
        return $USER->student[$course];
    }

    $timenow = time();   // todo:  add time check below

    return record_exists_sql("SELECT * FROM user_students WHERE user='$userid' AND course='$course'");
}


function reset_login_count() {
    global $SESSION;

    $SESSION->logincount = 0;
}


function set_moodle_cookie($thing) {

    $days = 60;
    $seconds = 60*60*24*$days;

    setCookie ('MOODLEID', "", time() - 3600, "/");
    setCookie ('MOODLEID', rc4encrypt($thing), time()+$seconds, "/");
}


function get_moodle_cookie() {
    global $MOODLEID;
    return rc4decrypt($MOODLEID);
}



function verify_login($username, $password) {

    $user = get_user_info_from_db("username", $username);

    if (! $user) {
        return false;
    } else if ( $user->password == md5($password) ) {
        return $user;
    } else {
        return false;
    }
}

function get_site () {
// Returns $course object of the top-level site.
    if ( $course = get_record("course", "category", 0)) {
        return $course;
    } else {
        return false;
    }
}

function get_admin () {
// Returns $user object of the main admin user

    if ( $admins = get_records_sql("SELECT u.* FROM user u, user_admins a WHERE a.user = u.id ORDER BY u.id ASC")) {
        foreach ($admins as $admin) {
            return $admin;   // ie the first one (yeah I know it's bodgy)
        }
    } else {
        return false;
    }
}

function get_teacher($courseid) {
// Returns $user object of the main teacher for a course
    if ( $teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                      WHERE t.user = u.id AND t.course = '$courseid' 
                                      ORDER BY t.authority ASC")) {
        foreach ($teachers as $teacher) {
            return $teacher;   // ie the first one (yeah I know it's bodgy)
        }
    } else {
        return false;
    }
}



/// MODULE FUNCTIONS /////////////////////////////////////////////////

function get_coursemodule_from_instance($modulename, $instance, $course) {
// Given an instance of a module, finds the coursemodule description

    return get_record_sql("SELECT cm.*, m.name
                           FROM course_modules cm, modules md, $modulename m 
                           WHERE cm.course = '$course' AND 
                                 cm.deleted = '0' AND
                                 cm.instance = m.id AND 
                                 md.name = '$modulename' AND 
                                 md.id = cm.module AND
                                 m.id = '$instance'");

}

function get_all_instances_in_course($modulename, $course, $sort="cw.week") {
// Returns an array of all the active instances of a particular
// module in a given course.   Returns false on any errors.

    return get_records_sql("SELECT m.*,cw.week,cm.id as coursemodule 
                            FROM course_modules cm, course_weeks cw, modules md, $modulename m 
                            WHERE cm.course = '$course' AND 
                                  cm.instance = m.id AND 
                                  cm.deleted = '0' AND
                                  cm.week = cw.id AND 
                                  md.name = '$modulename' AND 
                                  md.id = cm.module
                            ORDER BY $sort");

}

function print_update_module_icon($moduleid) {
    global $CFG;

    echo "&nbsp; &nbsp; 
          <A HREF=\"$CFG->wwwroot/course/mod.php?update=$moduleid\" TARGET=_top><IMG 
             SRC=\"$CFG->wwwroot/pix/t/edit.gif\" ALIGN=right BORDER=0 ALT=\"Update this module\"></A>";
}




/// CORRESPONDENCE  ////////////////////////////////////////////////

function email_to_user($user, $from, $subject, $messagetext, $messagehtml="", $attachment="", $attachname="") {
//  user        - a user record as an object
//  from        - a user record as an object
//  subject     - plain text subject line of the email
//  messagetext - plain text version of the message
//  messagehtml - complete html version of the message (optional)
//  attachment  - a file on the filesystem, relative to $CFG->dataroot
//  attachname  - the name of the file (extension indicates MIME)

    global $CFG, $_SERVER;

    include_once("$CFG->libdir/phpmailer/class.phpmailer.php");

    if (!$user) {
        return false;
    }
    
    $mail = new phpmailer;

    $mail->Version = "Moodle";                         // mailer version 
    $mail->PluginDir = "$CFG->libdir/phpmailer/";      // plugin directory (eg smtp plugin)
    if ($CFG->smtphosts) {
        $mail->IsSMTP();                                   // use SMTP directly
        $mail->Host = "$CFG->smtphosts";                   // specify main and backup servers
    } else {
        $mail->IsMail();                                   // use PHP mail() = sendmail
    }

    $mail->From     = "$from->email";
    $mail->FromName = "$from->firstname $from->lastname";
    $mail->Subject  =  stripslashes($subject);

    $mail->AddBCC("$user->email","$user->firstname $user->lastname"); 

    $mail->WordWrap = 70;                               // set word wrap

    if ($messagehtml) {
        $mail->IsHTML(true);
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
            $adminuser = get_admin();
            $mail->AddAddress("$adminuser->email", "$adminuser->firstname $adminuser->lastname");
            $mail->AddStringAttachment("Error in attachment.  User attempted to attach a filename with a unsafe name.", "error.txt", "8bit", "text/plain");
        } else {
            include_once("$CFG->dirroot/files/mimetypes.php");
            $mimetype = mimeinfo("type", $attachname);
            $mail->AddAttachment("$CFG->dataroot/$attachment", "$attachname", "base64", "$mimetype");
        }
    }

    if ($mail->Send()) {
        return true;
    } else {
        echo "ERROR: $mail->ErrorInfo\n";
        $site = get_site();
        add_to_log($site->id, "library", "mailer", $_SERVER["REQUEST_URI"], "ERROR: $mail->ErrorInfo");
        return false;
    }
}


/// FILE HANDLING  /////////////////////////////////////////////

function get_directory_list( $rootdir ) {
// Returns an array with all the filenames in 
// all subdirectories, relative to the given rootdir.

    $dirs = array();
   
    $dir = opendir( $rootdir );

    while( $file = readdir( $dir ) ) {
        $fullfile = $rootdir."/".$file;
        if ($file != "." and $file != "..") {
            if (filetype($fullfile) == "dir") {
                $subdirs = get_directory_list($fullfile);
                foreach ($subdirs as $subdir) {
                    $dirs[] = $file."/".$subdir;
                }
            } else {
                $dirs[] = $file;
            }
        }
    }

    return $dirs;
}



/// ENCRYPTION  ////////////////////////////////////////////////

function rc4encrypt($data) {
    $password = "nfgjeingjk";
    return endecrypt($password, $data, "");
}

function rc4decrypt($data) {
    $password = "nfgjeingjk";
    return endecrypt($password, $data, "de");
}

function endecrypt ($pwd, $data, $case) {
// Based on a class by Mukul Sabharwal [mukulsabharwal@yahoo.com]

    if ($case == 'de') {
        $data = urldecode($data);
    }

    $key[] = "";
    $box[] = "";
    $temp_swap = "";
    $pwd_length = 0;

    $pwd_length = strlen($pwd);

    for ($i = 0; $i <= 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
        $box[$i] = $i;
    }

    $x = 0;

    for ($i = 0; $i <= 255; $i++) {
        $x = ($x + $box[$i] + $key[$i]) % 256;
        $temp_swap = $box[$i];
        $box[$i] = $box[$x];
        $box[$x] = $temp_swap;
    }

    $temp = "";
    $k = "";

    $cipherby = "";
    $cipher = "";

    $a = 0;
    $j = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $temp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $temp;
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipherby = ord(substr($data, $i, 1)) ^ $k;
        $cipher .= chr($cipherby);
    }

    if ($case == 'de') {
        $cipher = urldecode(urlencode($cipher));
    } else {
        $cipher = urlencode($cipher);
    }

    return $cipher;
}


/// MISCELLANEOUS ////////////////////////////////////////////////////////////////////

function getweek ($startdate, $thedate) {
// Given dates in seconds, how many weeks is the date from startdate
// The first week is 1, the second 2 etc ... 
    
    if ($thedate < $startdate) {   // error
        return 0;  
    }

    return floor(($thedate - $startdate) / 604800.0) + 1;
}

function add_to_log($course, $module, $action, $url="", $info="") {
// Add an entry to the log table.  These are "action" focussed rather
// than web server hits, and provide a way to easily reconstruct what 
// any particular student has been doing.
//
// course = the course id
// module = discuss, journal, reading, course, user etc
// action = view, edit, post (often but not always the same as the file.php)
// url    = the file and parameters used to see the results of the action
// info   = additional description information 


    global $db, $USER, $REMOTE_ADDR;

    $timenow = time();
    $info = addslashes($info);

    $result = $db->Execute("INSERT INTO log
                            SET time = '$timenow', 
                                user = '$USER->id',
                                course = '$course',
                                ip = '$REMOTE_ADDR', 
                                module = '$module',
                                action = '$action',
                                url = '$url',
                                info = '$info'");
    if (!$result) {
        echo "<P>Error: Could not insert a new entry to the Moodle log</P>";  // Don't throw an error
    }    
}

function generate_password($maxlen=10) {
/* returns a randomly generated password of length $maxlen.  inspired by
 * http://www.phpbuilder.com/columns/jesus19990502.php3 */

    global $CFG;

    $fillers = "1234567890!$-+";
    $wordlist = file($CFG->wordlist);

    srand((double) microtime() * 1000000);
    $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $filler1 = $fillers[rand(0, strlen($fillers) - 1)];

    return substr($word1 . $filler1 . $word2, 0, $maxlen);
}


function format_time($totalsecs) {

    $days  = floor($totalsecs/86400);
    $remainder = $totalsecs - ($days*86400);
    $hours = floor($remainder/3600);
    $remainder = $remainder - ($hours*3600);
    $mins  = floor($remainder/60);
    $secs = $remainder - ($mins*60);

    if ($secs  != 1) $ss = "s";
    if ($mins  != 1) $ms = "s";
    if ($hours != 1) $hs = "s";
    if ($days  != 1) $ds = "s";

    if ($days)  $odays  = "$days day$ds";
    if ($hours) $ohours = "$hours hr$hs";
    if ($mins)  $omins  = "$mins min$ms";
    if ($secs)  $osecs  = "$secs sec$ss";

    if ($days)  return "$odays $ohours";
    if ($hours) return "$ohours $omins";
    if ($mins)  return "$omins $osecs";
    if ($secs)  return "$osecs";
    return "now";
}


?>
