<?PHP  //$Id$

    require("../config.php");

    require_login();

    if (!isadmin()) {
        error("You must be an admin to use this script");
    }

    if ($CFG->version < 2004013101) {
        error("This script does not work with this old version of Moodle");
    }

    print_header("Upgrading old logs", "Upgrading old logs");


    /// try and extract as many cmids as possible from the existing logs

    if ($coursemodules = get_records_sql("SELECT cm.*, m.name 
                                            FROM {$CFG->prefix}course_modules cm, 
                                                 {$CFG->prefix}modules m
                                            WHERE cm.module = m.id")) {
        $cmcount = count($coursemodules);
        $count = 0;
        $starttime = time();
        $sleeptime = 0;

        if ($CFG->dbtype == "mysql") {
            $LIKE = "LIKE";
        } else {
            $LIKE = "ILIKE";
        }

        if ($cmcount > 20) {
            print_simple_box('This process may take a very long time ... please be patient and let it finish.', 
                             'center', '', '#ffcccc');
            $sleeptime = 1;
        }
        foreach ($coursemodules as $cm) {
            execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                         WHERE module = '$cm->name' AND url $LIKE 'view.php?id=$cm->id%'", false);

            if ($cm->name == "forum") {

                execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                             WHERE module = 'forum' AND url $LIKE '%?f=$cm->instance%'", false);

                if ($discussions = get_records("forum_discussions", "forum", $cm->instance)) {
                    foreach ($discussions as $discussion) {
                        execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                     WHERE module = 'forum' AND url $LIKE '%?d=$discussion->id%'", false);
                    }
                }
            }

            $count++;
            $elapsedtime = time() - $starttime;
            $projectedtime = (int)(((float)$cmcount / (float)$count) * $elapsedtime) - $elapsedtime;

            if ($cmcount > 10) {
                notify("Processed $count of $cmcount coursemodules.  Estimated completion: ".format_time($projectedtime));
                flush();
                sleep($sleeptime);     // To help reduce database load
            }
        }
    }

    notify("Log upgrading was successful!");

    print_footer();

?>
