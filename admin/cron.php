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

    $FULLME = "cron";

    function microtime_diff($a, $b) {
        list($a_dec, $a_sec) = explode(" ", $a);
        list($b_dec, $b_sec) = explode(" ", $b);
        return $b_sec - $a_sec + $b_dec - $a_dec;
    }

    $starttime = microtime();

    require_once("/usr/local/moodle/config.php");

    echo "<PRE>\n";

    $timenow  = time();

//  Run all cron jobs for each module

    if ($mods = get_records_select("modules", "cron > 0 AND (($timenow - lastcron) > cron)")) {
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


// Unenrol users who haven't logged in for $CFG->longtimenosee

    if ($CFG->longtimenosee) { // value in days
        $longtime = $timenow - ($CFG->longtimenosee * 3600 * 24);
        if ($users = get_users_longtimenosee($longtime)) {
            foreach ($users as $user) {
                if (unenrol_student($user->id)) {
                    echo "Deleted student enrolment for $user->firstname $user->lastname ($user->id)\n";
                }
            }
        }
    }


// Delete users who haven't confirmed within seven days

    $oneweek = $timenow - (7 * 24 * 3600);
    if ($users = get_users_unconfirmed($oneweek)) {
        foreach ($users as $user) {
            if (delete_records("user", "id", $user->id)) {
                echo "Deleted unconfirmed user for $user->firstname $user->lastname ($user->id)\n";
            }
        }
    }

    echo "Cron script completed correctly\n";

    $difftime = microtime_diff($starttime, microtime());
    echo "Execution took ".$difftime." seconds\n"; 

?>
