<?php  // $Id$
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set


    if (empty($currenttab) or empty($user) or empty($course)) {
        error('You cannot call this script in that way');
    }

    print_heading(fullname($user, isteacher($course->id)));

//if (!empty($USER) and (isteacher($course->id) or (($USER->id == $user->id) and !isguest()))) { // tabs are shown

    $inactive = NULL;
    $activetwo = NULL;
    $toprow = array();

    $toprow[] = new tabobject('profile', $CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id, 
                get_string('profile'));



/// Can only edit profile if it belongs to user or current user is admin and not editing primary admin

    if (($mainadmin = get_admin()) === false) {
        $mainadmin->id = 0; /// Weird - no primary admin!
    }
    if ((!empty($USER->id) and ($USER->id == $user->id) and !isguest()) or 
        (isadmin() and ($user->id != $mainadmin->id)) ) {

        if(empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http','https',$CFG->wwwroot);
        }
        $toprow[] = new tabobject('editprofile', $wwwroot.'/user/edit.php?id='.$user->id.'&amp;course='.$course->id, 
                    get_string('editmyprofile'));
    }


/// Everyone can see posts for this user

    $toprow[] = new tabobject('forumposts', $CFG->wwwroot.'/mod/forum/user.php?id='.$user->id.'&amp;course='.$course->id, 
                get_string('forumposts', 'forum'));

    if (in_array($currenttab, array('posts', 'discussions'))) {
        $inactive = array('forumposts');
        $activetwo = array('forumposts');

        $secondrow = array();
        $secondrow[] = new tabobject('posts', $CFG->wwwroot.'/mod/forum/user.php?course='.$course->id.
                                  '&amp;id='.$user->id.'&amp;mode=posts', get_string('posts', 'forum'));
        $secondrow[] = new tabobject('discussions', $CFG->wwwroot.'/mod/forum/user.php?course='.$course->id.
                                  '&amp;id='.$user->id.'&amp;mode=discussions', get_string('discussions', 'forum'));
    }


/// Current user must be teacher of the course or the course allows user to view their reports
    if (isteacher($course->id) or ($course->showreports and $USER->id == $user->id)) {

        $toprow[] = new tabobject('reports', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                  '&amp;user='.$user->id.'&amp;mode=outline', get_string('activityreports'));

        if (in_array($currenttab, array('outline', 'complete', 'todaylogs', 'alllogs', 'stats'))) {
            $inactive = array('reports');
            $activetwo = array('reports');

            $secondrow = array();
            $secondrow[] = new tabobject('outline', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                      '&amp;user='.$user->id.'&amp;mode=outline', get_string('outlinereport'));
            $secondrow[] = new tabobject('complete', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                      '&amp;user='.$user->id.'&amp;mode=complete', get_string('completereport'));
            $secondrow[] = new tabobject('todaylogs', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                      '&amp;user='.$user->id.'&amp;mode=todaylogs', get_string('todaylogs'));
            $secondrow[] = new tabobject('alllogs', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                      '&amp;user='.$user->id.'&amp;mode=alllogs', get_string('alllogs'));
            if (!empty($CFG->enablestats)) {
                $secondrow[] = new tabobject('stats',$CFG->wwwroot.'/course/user.php?id='.$course->id.
                                             '&amp;user='.$user->id.'&amp;mode=stats',get_string('stats'));
            }
        }

    }


/// Add second row to display if there is one

    if (!empty($secondrow)) {
        $tabs = array($toprow, $secondrow);
    } else {
        $tabs = array($toprow);
    }


/// Print out the tabs and continue!

    print_tabs($tabs, $currenttab, $inactive, $activetwo);

?>
