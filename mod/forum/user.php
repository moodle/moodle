<?php // $Id$

// Display user activity reports for a course

    require_once('../../config.php');
    require_once('lib.php');

    $course  = required_param('course');     // course id
    $id      = optional_param('id');       // user id
    $mode    = optional_param('mode', 'posts');
    $page    = optional_param('page', 0);
    $perpage = optional_param('perpage', 5);

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

    if (! $user = get_record("user", "id", $id)) {
        error("User ID is incorrect");
    }

    if (! $course = get_record("course", "id", $course)) {
        error("Course id is incorrect.");
    }

    require_course_login($course);


    add_to_log($course->id, "forum", "user report", "user.php?id=$course->id&amp;user=$user->id&amp;mode=$mode", "$user->id"); 

    $strforumposts   = get_string('forumposts', 'forum');
    $strparticipants = get_string('participants');
    $strmode         = get_string($mode, 'forum');
    $fullname        = fullname($user, isteacher($course->id));

    if ($course->category) {
        print_header("$course->shortname: $fullname: $strmode", "$course->fullname",
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">$strparticipants</a> ->
                  <a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a> -> 
                  $strforumposts -> $strmode");
    } else {
        print_header("$course->shortname: $fullname: $strmode", "$course->fullname",
                 "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a> -> 
                  $strforumposts -> $strmode");
    }

    $currenttab = $mode;
    include($CFG->dirroot.'/user/tabs.php');   /// Prints out tabs as part of user page

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and
                         $course->groupmodeforce and
                         !isteacheredit($course->id));

    $groupid = $isseparategroups ? get_current_group($course->id) : NULL;

    switch ($mode) {
        case 'posts' :
            $searchterms = array('userid:'.$user->id);
            $extrasql = '';
            break;

        default:
            $searchterms = array('userid:'.$user->id);
            $extrasql = 'AND p.parent = 0';
            break;
    }
    
    echo '<div class="user-content">';
    if ($posts = forum_search_posts($searchterms, $course->id, $page*$perpage, $perpage, 
                                    $totalcount, $groupid, $extrasql)) {
        print_paging_bar($totalcount, $page, $perpage, 
                         "user.php?id=$user->id&amp;course=$course->id&amp;mode=$mode&amp;perpage=$perpage&amp;");
        foreach ($posts as $post) {
    
            if (! $discussion = get_record('forum_discussions', 'id', $post->discussion)) {
                error('Discussion ID was incorrect');
            }
            if (! $forum = get_record('forum', 'id', "$discussion->forum")) {
                error("Could not find forum $discussion->forum");
            }
    
            $fullsubject = "<a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";
            if ($forum->type != 'single') {
                $fullsubject .= " -> <a href=\"discuss.php?d=$discussion->id\">".format_string($discussion->name,true)."</a>";
                if ($post->parent != 0) {
                    $fullsubject .= " -> <a href=\"discuss.php?d=$post->discussion&amp;parent=$post->id\">".format_string($post->subject,true)."</a>";
                }
            }

            if (isadmin() && $course->id == SITEID) {
                $postcoursename = get_field('course', 'shortname', 'id', $forum->course);
                $fullsubject = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$forum->course.'">'.$postcoursename.'</a> -> '. $fullsubject;
            }
    
            $post->subject = $fullsubject;
    
            $fulllink = "<a href=\"discuss.php?d=$post->discussion#$post->id\">".
                         get_string("postincontext", "forum")."</a>";

            forum_print_post($post, $course->id, false, false, false, false, $fulllink);
    
            echo "<br />";
        }
    
        print_paging_bar($totalcount, $page, $perpage, 
                         "user.php?id=$user->id&amp;course=$course->id&amp;mode=$mode&amp;perpage=$perpage&amp;");
    } else {
        print_heading(get_string('noposts', 'forum'));
    }
    echo '</div>';
    print_footer($course);

?>

