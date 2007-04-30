<?PHP  //$Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('upgradelogs');

    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    if ($CFG->version < 2004013101) {
        error("This script does not work with this old version of Moodle");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

/// Turn off time limits, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    while(@ob_end_flush());


/// Print header

    $strupgradinglogs  = get_string("upgradinglogs", "admin");
    admin_externalpage_print_header();
    print_heading($strupgradinglogs);

    if (!data_submitted() or empty($confirm) or !confirm_sesskey()) {
        $optionsyes = array('confirm'=>'1', 'sesskey'=>sesskey());
        notice_yesno(get_string('upgradeforumreadinfo', 'admin'),
                    'upgradelogs.php', 'index.php', $optionsyes, NULL, 'post', 'get');
        admin_externalpage_print_footer();
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

        $LIKE = sql_ilike();

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

    notify('Log upgrading was successful!', 'notifysuccess');

    print_continue('index.php');

    admin_externalpage_print_footer();

?>
