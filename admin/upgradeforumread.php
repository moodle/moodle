<?PHP  //$Id$

    require("../config.php");
    require("$CFG->dirroot/mod/forum/lib.php");

    optional_variable($confirm);

    require_login();

    if (!isadmin()) {
        error("You must be an admin to use this script");
    }

    if ($CFG->version < 2005042300) {
        error("This script does not work with this old version of Moodle");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }


/// Print header

    $stradministration = get_string("administration");
    $strupgradingdata  = get_string("upgradingdata", "admin");

    print_header("$site->shortname: $stradministration: $strupgradingdata", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strupgradingdata");

    if (empty($_GET['confirm'])) {
        notice_yesno(get_string("upgradeforumreadinfo", "admin"), 
                     "upgradeforumread.php?confirm=true&sesskey=$USER->sesskey", 
                     "index.php");
        print_footer();
        exit;
    } else if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }


/// Turn off time limits, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    @ob_end_flush();

    execute_sql('TRUNCATE TABLE '.$CFG->prefix.'forum_read;', false);   // Trash all old entries

/// Enter initial read records for all posts older than 1 day.

/// Timestamp for old posts (and therefore considered read).
    $dateafter = time() - ($CFG->forum_oldpostdays*24*60*60);

/// Timestamp for one day ago.
    $onedayago = time() - (24*60*60);


/// Get all discussions that have had posts since the old post date.
    if ($discussions = get_records_select('forum_discussions', 'timemodified > '.$dateafter,
                                          'course', 'id,course,forum,groupid,userid')) {
        $dtotal = count($discussions);
        print_heading('Updating forum post read/unread records for '.$dtotal.' discussions...'.
                      'Please keep this window open until it completes', '', 3);

        $groups = array();
    
        $currcourse = 0;
        $users = 0;
        $count = 0;
        $dcount = 0;

        foreach ($discussions as $discussion) {
            $dcount++;
            print_progress($dcount, $dtotal);

            if ($discussion->course != $currcourse) {
                /// Discussions are ordered by course, so we only need to get any course's users once.
                $currcourse = $discussion->course;
                $users = get_course_users($currcourse, '', '', 'u.id,u.confirmed');
            }
            /// If this course has users, and posts more than a day old, mark them for each user.
            if ($users &&
                    ($posts = get_records_select('forum_posts', 'discussion = '.$discussion->id.
                                                 ' AND '.$dateafter.' < modified AND modified < '.$onedayago, 
                                                 '', 'id,discussion,modified'))) {
                foreach ($users as $user) {
                    /// If its a group discussion, make sure the user is in the group.
                    if ($discussion->groupid) {
                        if (!isset($groups[$discussion->groupid][$user->id])) {
                            $groups[$discussion->groupid][$user->id] = ismember($discussion->groupid, $user->id);
                        }
                    }
                    if (!$discussion->groupid || !empty($groups[$discussion->groupid][$user->id])) {
                        foreach ($posts as $post) {
                            print_progress($dcount, $dtotal);
                            forum_tp_mark_post_read($user->id, $post, $discussion->forum);
                        }
                    }
                }
            }
        }
        print_progress($dcount, $dtotal, 0);
    }
    

    delete_records('config', 'name', 'upgrade', 'value', 'forumread');

    notify('Log upgrading was successful!', 'notifysuccess');

    print_continue('index.php');

    print_footer();

    exit;



function print_progress($done, $total, $updatetime=3, $sleeptime=1) {
    static $count;
    static $starttime;
    static $lasttime;

    if (empty($starttime)) {
        $starttime = $lasttime = time();
        $lasttime = $starttime - $updatetime;
        echo '<table width="500" cellpadding="0" cellspacing="0" align="center"><tr><td width="500">';
        echo '<div id="bar" style="border-style:solid;border-width:1px;width:500px;height:50px;">';
        echo '<div id="slider" style="border-style:solid;border-width:1px;height:48px;width:10px;background-color:green;"></div>';
        echo '</div>';
        echo '<div id="text" align="center" style="width:500px;"></div>';
        echo '</td></tr></table>';
        echo '</div>';
    }

    if (!isset($count)) {
        $count = 0;
    }

    $count++;

    $now = time();

    if ($done && (($now - $lasttime) >= $updatetime)) {
        $elapsedtime = $now - $starttime;
        $projectedtime = (int)(((float)$total / (float)$done) * $elapsedtime) - $elapsedtime;
        $percentage = format_float((float)$done / (float)$total, 2);
        $width = (int)(500 * $percentage);

        echo '<script>';
        echo 'document.getElementById("text").innerHTML = "'.$count.' done.  Ending: '.format_time($projectedtime).'";'."\n";
        echo 'document.getElementById("slider").style.width = \''.$width.'px\';'."\n";
        echo '</script>';

        $lasttime = $now;
        sleep($sleeptime);
    }
}

?>
