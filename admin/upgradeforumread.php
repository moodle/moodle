<?PHP  //$Id$

    require_once('../config.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('upgradeforumread');

    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    if ($CFG->version < 2005042300) {
        error("This script does not work with this old version of Moodle");
    }

    if (!$site = get_site()) {
        redirect('index.php');
    }


/// Print header

    $strupgradingdata  = get_string('upgradingdata', 'admin');

    admin_externalpage_print_header();
    print_heading($strupgradingdata);

    if (!data_submitted() or empty($confirm) or !confirm_sesskey()) {
        $optionsyes = array('confirm'=>'1', 'sesskey'=>sesskey());
        notice_yesno(get_string('upgradeforumreadinfo', 'admin'),
                    'upgradeforumread.php', 'index.php', $optionsyes, NULL, 'post', 'get');
        admin_externalpage_print_footer();
        exit;
    }


/// Turn off time limits, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    while(@ob_end_flush());

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
                            $groups[$discussion->groupid][$user->id] = groups_is_member($discussion->groupid, $user->id);
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

    admin_externalpage_print_footer();

?>
