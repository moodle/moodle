<?PHP // $Id$

// Code fragment called by /courses/new.php
// Prints out a formatted table of all the new things that have happened 
// with this module (since the user's last login).  Suitable for students.

// Assumes $course and $USER are defined and page has been started.

// First, print all the users who have updated their records.

    if ($users = get_records_sql("SELECT u.* FROM user u, user_students s 
                                  WHERE u.id = s.user 
                                        AND s.course = '$course->id' 
                                        AND u.timemodified > '$USER->lastlogin' ")) {
   
        echo "<P><B>Updated User Profiles</B></P>";
        echo "<FONT SIZE=2><UL>";

        foreach ($users as $user) {
            echo "<LI><A HREF=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A>";
            echo " ".moodledate($user->timemodified);
        }
        echo "</UL></FONT>";
    }

    if ($users = get_records_sql("SELECT u.* FROM user u, user_students s 
                                  WHERE u.id = s.user 
                                        AND s.course = '$course->id' 
                                        AND u.currentlogin > '$USER->lastlogin' ")) {
   
        echo "<P><B>User logins</B></P>";
        echo "<FONT SIZE=2><UL>";

        foreach ($users as $user) {
            echo "<LI><A HREF=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A>";
            echo " ".moodledate($user->currentlogin);
        }
        echo "</UL></FONT>";
    }

?>
