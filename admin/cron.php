<?PHP // $Id$

// This script looks through all the module directories for cron.php files
// and runs them.  These files can contain cleanup functions, email functions
// or anything that needs to be run on a regular basis.
//
// This file is best run from cron on the host system (ie outside PHP).
// The script can either be invoked via the web server or via a standalone
// version of PHP compiled for CGI.
//
// eg   wget -q -O /dev/null 'http://moodle.somewhere.edu/admin/cron.php'
// or   php /web/moodle/admin/cron.php 

    require("../config.php");

    echo "<PRE>\n";

    $timenow  = time();

//  Run all cron jobs for each module

    if ($mods = get_records_sql("SELECT * FROM modules WHERE cron > 0 AND (($timenow - lastcron) > cron)")) {
        foreach ($mods as $mod) {
            $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
            if (file_exists($libfile)) {
                include_once($libfile);
                $cron_function = $mod->name."_cron";
                if (function_exists($cron_function)) {
                    if ($cron_function()) {
                        if (! set_field("modules", "lastcron", $timenow, "id", $mod->id)) {
                            echo "Error: could not update timestamp for $mod->fullname\n";
                        }
                    }
                }
            }
        }
    }


//  Any system-wide Moodle cron jobs should be run here

    // Unsubscribe users who haven't logged in for $CFG->longtimenosee

    if ($CFG->longtimenosee) { // value in days
        $cutofftime = time() - ($CFG->longtimenosee * 3600 * 24);
        if ($users = get_records_sql("SELECT u.* FROM user u, user_students s
                                       WHERE lastaccess > '0' AND 
                                             lastaccess < '$cutofftime'
                                             u.id = s.user GROUP BY u.id")) {
            foreach ($users as $user) {
                if (delete_records("user_students", "user", $user->id)) {
                    echo "Deleted student enrolment for $user->firstname $user->lastname ($user->id)\n";
                }
            }
        }
    }

    // Delete users who haven't confirmed within seven days

    $cutofftime = time() - (7 * 24 * 3600);
    if ($users = get_records_sql("SELECT * FROM user 
                                  WHERE confirmed = '0' AND 
                                        firstaccess > '0' AND 
                                        firstaccess < '$cutofftime'")) {
        foreach ($users as $user) {
            if (delete_records("user", "id", $user->id)) {
                echo "Deleted unconfirmed user for $user->firsname $user->lastname ($user->id)\n";
            }
        }
    }

    echo "Cron script completed correctly\n";

?>
