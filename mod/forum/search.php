<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);       // course id
    optional_variable($search, "");  // user id

    $search = strip_tags($search);

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    if ($course->category) {
        require_login($course->id);
    }

    add_to_log($course->id, "forum", "search", "search.php?id=$course->id&search=$search", "$search"); 

    $strforums = get_string("modulenameplural", "forum");
    $strsearch = get_string("search", "forum");
    $strsearchresults = get_string("searchresults", "forum");

    if ($search) {
        print_header("$course->shortname: $strsearchresults", "$course->fullname",
                 "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strforums</A> -> 
                  <A HREF=\"search.php?id=$course->id\">$strsearch</A> -> \"$search\"", "search.search");
    } else {
        print_header("$course->shortname: $strsearch", "$course->fullname",
                 "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strforums</A> -> $strsearch", "search.search");
    }

    echo "<DIV ALIGN=CENTER>";
    forum_print_search_form($course, $search);
    echo "</DIV>";

    if ($search) {
    
        if (!$posts = forum_search_posts($search, $course->id)) {
            print_heading("<BR>".get_string("nopostscontaining", "forum", $search));

        } else {
            foreach ($posts as $post) {
                if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
                    error("Discussion ID was incorrect");
                }
                if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                    error("Could not find forum $discussion->forum");
                }

                $post->subject = highlight("$search", $post->subject);
                $discussion->name = highlight("$search", $discussion->name);

                $fullsubject = "<A HREF=\"view.php?f=$forum->id\">$forum->name</A>";
                if ($forum->type != "single") {
                    $fullsubject .= " -> <A HREF=\"discuss.php?d=$discussion->id\">$discussion->name</A>";
                    if ($post->parent != 0) {
                        $fullsubject .= " -> <A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</A>";
                    }
                }

                $post->subject = $fullsubject;
                $post->message = highlight("$search", $post->message);

                $fulllink = "<P ALIGN=right><A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">".get_string("postincontext", "forum")."</A></P>";
                forum_print_post($post, $course->id, false, false, false, false, $fulllink);

                echo "<BR>";
            }
        }
    }

    print_footer($course);

?>

