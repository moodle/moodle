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
    
        if (!isteacher($course->id)) {
            $notteacherforum = "AND f.type <> 'teacher'";
        } else {
            $notteacherforum = "";
        }

        $posts = get_records_sql("SELECT p.*,u.firstname,u.lastname,u.email,u.picture,u.id as userid 
                                  FROM forum_posts p, forum_discussions d, user u, forum f
                                  WHERE message LIKE '%$search%' AND p.user = u.id 
                                        AND p.discussion = d.id AND d.course = '$course->id' 
                                        AND d.forum = f.id $notteacherforum
                                  ORDER BY p.modified DESC LIMIT 0, 50 ");

        if (!$posts) {
            print_heading("<BR>No posts found containing \"$search\"");

        } else {
            foreach ($posts as $post) {
                if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
                    error("Discussion ID was incorrect");
                }
                if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                    error("Could not find forum $discussion->forum");
                }

                $fullsubject = "<A HREF=\"view.php?f=$forum->id\">$forum->name</A>";
                if ($forum->type != "single") {
                    $fullsubject .= " -> <A HREF=\"discuss.php?d=$discussion->id\">$discussion->name</A>";
                    if ($post->parent != 0) {
                        $fullsubject .= " -> <A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</A>";
                    }
                }

                $post->subject = $fullsubject;
                $post->message = highlight("$search", $post->message);

                $fulllink = "<P ALIGN=right><A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">See this post in context</A></P>";
                forum_print_post($post, $course->id, false, false, false, false, $fulllink);

                echo "<BR>";
            }
        }
    }

    print_footer($course);

?>

