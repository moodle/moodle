<?php  // $Id$
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set

    if (!isset($filtertype)) {
        $filtertype = '';
    }
    if (!isset($filterselect)) {
        $filterselect = '';
    }

    //make sure everything is cleaned properly
    $filtertype   = clean_param($filtertype, PARAM_ALPHA);
    $filterselect = clean_param($filterselect, PARAM_INT);

    if (empty($currenttab) or empty($user) or empty($course)) {
        //error('You cannot call this script in that way');
    }

    if (($filtertype == 'site' && $filterselect) || ($filtertype=='user' && $filterselect)) {
        $user = get_record('user','id',$filterselect);
    }

    $inactive = NULL;
    $activetwo = NULL;
    $toprow = array();

    /**************************************
     * Site Level participation or Blogs  *
     **************************************/
    if ($filtertype == 'site') {

        $site = get_site();
        print_heading(format_string($site->fullname));
        
        if ($CFG->bloglevel >= 4) {
            if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                $toprow[] = new tabobject('participants', $CFG->wwwroot.'/user/index.php?id='.SITEID,
                    get_string('participants'));
            }

            $toprow[] = new tabobject('blogs', $CFG->wwwroot.'/blog/index.php?filtertype=site&amp;',
                get_string('blogs','blog'));
        }

    /**************************************
     * Course Level participation or Blogs  *
     **************************************/
    } else if ($filtertype == 'course' && $filterselect) {

        $course = get_record('course','id',$filterselect);
        print_heading(format_string($course->fullname));

        if ($CFG->bloglevel >= 3) {

            $toprow[] = new tabobject('participants', $CFG->wwwroot.'/user/index.php?id='.$filterselect.'&amp;group=0',
                get_string('participants'));    //the groupid hack is necessary, otherwise the group in the session willbe used
        
            $toprow[] = new tabobject('blogs', $CFG->wwwroot.'/blog/index.php?filtertype=course&amp;filterselect='.$filterselect, get_string('blogs','blog'));
        }

    /**************************************
     * Group Level participation or Blogs  *
     **************************************/
    } else if ($filtertype == 'group' && $filterselect) {

        $group_name = groups_get_group_name($filterselect); //TODO:
        print_heading($group_name);

        if ($CFG->bloglevel >= 2) {

            $toprow[] = new tabobject('participants', $CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$filterselect,
                get_string('participants'));

        
            $toprow[] = new tabobject('blogs', $CFG->wwwroot.'/blog/index.php?filtertype=group&amp;filterselect='.$filterselect, get_string('blogs','blog'));
        }

    /**************************************
     * User Level participation or Blogs  *
     **************************************/
    } else {
        if (isset($userid)) {
            $user = get_record('user','id', $userid);
        }
        print_heading(fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id))));

        $toprow[] = new tabobject('profile', $CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id, get_string('profile'));

        $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
        $coursecontext   = get_context_instance(CONTEXT_COURSE, $course->id);
        $personalcontext = get_context_instance(CONTEXT_USER, $user->id);

    /// Can only edit profile if it belongs to user or current user is admin and not editing primary admin

        $mainadmin = get_admin();

        if(empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }

        $edittype = 'none';
        if (is_mnet_remote_user($user)) {
            // cannot edit remote users

        } else if (isguest() or !isloggedin()) {
            // can not edit guest like accounts - TODO: add capability to edit own profile
            
        } else if ($USER->id == $user->id) {
            if (has_capability('moodle/user:update', $systemcontext)) {
                $edittype = 'advanced';
            } else {
                $edittype = 'normal';
            }

        } else if ($user->id != $mainadmin->id) {
            //no editing of primary admin!
            if (has_capability('moodle/user:update', $systemcontext)) {
                $edittype = 'advanced';
            } else if (has_capability('moodle/user:editprofile', $personalcontext)) {
                //teachers, parents, etc.
                $edittype = 'normal';
            }
        }

        if ($edittype == 'advanced') {
            $toprow[] = new tabobject('editprofile', $wwwroot.'/user/editadvanced.php?id='.$user->id.'&amp;course='.$course->id, get_string('editmyprofile'));
        } else if ($edittype == 'normal') {
            $toprow[] = new tabobject('editprofile', $wwwroot.'/user/edit.php?id='.$user->id.'&amp;course='.$course->id, get_string('editmyprofile'));
        }

    /// Everyone can see posts for this user
    
    /// add logic to see course read posts permission
        if (has_capability('moodle/user:readuserposts', $personalcontext) || has_capability('mod/forum:viewdiscussion', get_context_instance(CONTEXT_COURSE, $course->id))) {
            $toprow[] = new tabobject('forumposts', $CFG->wwwroot.'/mod/forum/user.php?id='.$user->id.'&amp;course='.$course->id,
                        get_string('forumposts', 'forum'));

            if (in_array($currenttab, array('posts', 'discussions'))) {
                $inactive = array('forumposts');
                $activetwo = array('forumposts');

                $secondrow = array();
                $secondrow[] = new tabobject('posts', $CFG->wwwroot.'/mod/forum/user.php?course='.$course->id.
                                      '&amp;id='.$user->id.'&amp;mode=posts', get_string('posts', 'forum'));
                $secondrow[] = new tabobject('discussions', $CFG->wwwroot.'/mod/forum/user.php?course='.$course->id.
                                      '&amp;id='.$user->id.'&amp;mode=discussions', get_string('discussionsstartedby', 'forum'));
            }

        }

    /// Personal blog entries tab
        require_once($CFG->dirroot.'/blog/lib.php');
        if ($CFG->bloglevel >= BLOG_USER_LEVEL and // blogs must be enabled
            (has_capability('moodle/user:readuserblogs', $personalcontext) // can review posts (parents etc)
            or has_capability('moodle/blog:manageentries', $systemcontext)     // entry manager can see all posts
            or ($user->id == $USER->id and has_capability('moodle/blog:create', $systemcontext)) // viewing self
            or (has_capability('moodle/blog:view', $systemcontext) or has_capability('moodle/blog:view', $coursecontext))
            ) // able to read blogs in site or course context
        ) { //end if

            $toprow[] = new tabobject('blogs', $CFG->wwwroot.'/blog/index.php?userid='.$user->id.'&amp;courseid='.$course->id, get_string('blog', 'blog'));
        }

    /// Current user must be teacher of the course or the course allows user to view their reports
    
    //print_object($course);
    //print_object($user);
    
        // add in logic to check course read report
        if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext) || ($course->showreports and $USER->id == $user->id) || has_capability('moodle/user:viewuseractivitiesreport', $coursecontext)) {

            $toprow[] = new tabobject('reports', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                      '&amp;user='.$user->id.'&amp;mode=outline', get_string('activityreports'));

            if (in_array($currenttab, array('outline', 'complete', 'todaylogs', 'alllogs', 'stats', 'grade'))) {
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
                
                if ($course->showgrades) {
                    $secondrow[] = new tabobject('grade', $CFG->wwwroot.'/course/user.php?id='.$course->id.
                                          '&amp;user='.$user->id.'&amp;mode=grade', get_string('grade'));
                }
                                
            }

        }

    }    //close last bracket (individual tags)


    /// this needs permission checkings

    
    if (!empty($showroles) and !empty($user)) { // this variable controls whether this roles is showed, or not, so only user/view page should set this flag
        $usercontext = get_context_instance(CONTEXT_USER, $user->id);
        if (has_capability('moodle/role:assign',$usercontext)) {
            $toprow[] = new tabobject('roles', $CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$usercontext->id.'&amp;userid='.$user->id.'&amp;courseid='.$course->id
                                  ,get_string('roles'));
                                  
            if (in_array($currenttab, array('assign', 'override'))) {
                $inactive = array('roles');
                $activetwo = array('roles');
    
                $secondrow = array();
                $secondrow[] = new tabobject('assign', $CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$usercontext->id.'&amp;userid='.$user->id.'&amp;courseid='.$course->id
                                  ,get_string('assignroles', 'role'));
                $secondrow[] = new tabobject('override', $CFG->wwwroot.'/'.$CFG->admin.'/roles/override.php?contextid='.$usercontext->id.'&amp;userid='.$user->id.'&amp;courseid='.$course->id
                                  ,get_string('overrideroles', 'role'));
                                    
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
