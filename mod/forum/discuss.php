<?PHP // $Id$

//  Displays a post, and all the posts below it.
//  If no post is given, displays all posts in a discussion

    require("../../config.php");
    require("lib.php");

    require_variable($d);       // Discussion ID
    optional_variable($parent); // If set, then display this post and all children.
    optional_variable($mode);   // If set, changes the layout of the thread

    if (! $discussion = get_record("forum_discussions", "id", $d)) {
        error("Discussion ID was incorrect or no longer exists");
    }

    if (! $course = get_record("course", "id", $discussion->course)) {
        error("Course ID is incorrect - discussion is faulty");
    }

    if (! $forum = get_record("forum", "id", $discussion->forum)) {
        notify("Bad forum ID stored in this discussion");
    }

    if ($course->category) {
        require_login($course->id);
    }

    add_to_log($course->id, "forum", "view discussion", "discuss.php?".$_SERVER["QUERY_STRING"], "$discussion->id");

    unset($SESSION->fromdiscussion);

    forum_set_display_mode($mode);

    if (abs($USER->mode) == 1) {  // If flat display then display the lot.
        $parent = 0;  
    }

    if (!$parent) {
        $parent = $discussion->firstpost;
        $navtail = "$discussion->name";
    }

    if (! $post = forum_get_post_full($parent)) {
        error("Discussion no longer exists", "$CFG->wwwroot/mod/forum/view.php?f=$forum->id");
    }

    if (!$navtail) {
        $navtail = "<A HREF=\"discuss.php?d=$discussion->id\">$discussion->name</A> -> $post->subject";
    }

    $navmiddle = "<A HREF=\"../forum/index.php?id=$course->id\">".get_string("forums", "forum")."</A> -> <A HREF=\"../forum/view.php?f=$forum->id\">$forum->name</A>";

    if ($cm->id) {
        $updatebutton = update_module_icon($cm->id, $course->id);
    } else {
        $updatebutton = "";
    }

    if ($course->category) {
        print_header("$course->shortname: $discussion->name", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> ->
                  $navmiddle -> $navtail", "", "", true, $updatebutton);
    } else {
        print_header("$course->shortname: $discussion->name", "$course->fullname",
                 "$navmiddle -> $navtail", "", "", true, $updatebutton);
    }

    forum_print_discussion($course, $discussion, $post, $USER->mode);

    print_footer($course);

?>
