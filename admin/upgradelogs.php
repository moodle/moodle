<?PHP  //$Id$

    require("../config.php");

    optional_variable($confirm);

    require_login();

    if (!isadmin()) {
        error("You must be an admin to use this script");
    }

    if ($CFG->version < 2004013101) {
        error("This script does not work with this old version of Moodle");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

/// Turn off time limits, sometimes upgrades can be slow.

    @set_time_limit(0);


/// Print header

    $stradministration = get_string("administration");
    $strupgradinglogs  = get_string("upgradinglogs", "admin");

    print_header("$site->shortname: $stradministration: $strupgradinglogs", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strupgradinglogs");

    if (empty($_GET['confirm'])) {
        notice_yesno(get_string("upgradelogsinfo", "admin"), 
                     "upgradelogs.php?confirm=true", 
                     "index.php");
        print_footer();
        exit;
    }


/// Try and extract as many cmids as possible from the existing logs

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

        if ($cmcount > 10) {
            print_simple_box('This process may take a very long time ... please be patient and let it finish.', 
                             'center', '', '#ffcccc');
            $sleeptime = 1;
        }
        foreach ($coursemodules as $cm) {

            switch ($cm->name) { 
                case "forum":
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'view.php?id=$cm->id'", false);

                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'view.php?f=$cm->instance'", false);
    
                    if ($discussions = get_records("forum_discussions", "forum", $cm->instance)) {
                        foreach ($discussions as $discussion) {
                            execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                         WHERE module = '$cm->name' AND url $LIKE 'discuss.php?d=$discussion->id%'", false);
                        }
                    }
                    break;

                case "glossary": 
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url $LIKE 'view.php?id=$cm->id%'", false);
                    break;

                case "quiz": 
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'view.php?id=$cm->id'", false);
                    break;

                case "assignment": 
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'view.php?id=$cm->id'", false);
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'view.php?a=$cm->instance'", false);
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'submissions.php?id=$cm->instance'", false);
                    break;
                    
                case "journal": 
                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'report.php?id=$cm->id'", false);

                    execute_sql("UPDATE {$CFG->prefix}log SET cmid = '$cm->id' 
                                 WHERE module = '$cm->name' AND url = 'view.php?id=$cm->id'", false);
                    break;

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

    delete_records("config", "name", "upgrade", "value", "logs");

    notify("Log upgrading was successful!");


    print_footer();

?>
