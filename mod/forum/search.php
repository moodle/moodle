<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);       // course id
    optional_variable($search, "");  // user id

    $search = strip_tags($search);

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    require_login($course->id);

    add_to_log($course->id, "course", "search", "search.php?id=$course->id&search=$search", "$search"); 

    if ($search) {
        print_header("$course->shortname: Search Results", "$course->fullname",
                 "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">Forums</A> -> 
                  <A HREF=\"search.php?id=$course->id\">Search</A> -> \"$search\"", "search.search");
    } else {
        print_header("$course->shortname: Search", "$course->fullname",
                 "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">Forums</A> -> Search", "search.search");
    }

    echo "<DIV ALIGN=CENTER>";
    print_forum_search_form($course, $search);
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
                $post->subject = "<A HREF=\"index.php?id=$course->id&forum=$forum->id\">$forum->name</A> -> ".
                                 "<A HREF=\"discuss.php?d=$discussion->id\">$discussion->name</A> -> ".
                                 "<A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</A>";

                $post->message = highlight("$search", $post->message);

                $fulllink = "<P ALIGN=right><A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">See this post in context</A></P>";
                print_post($post, $course->id, false, false, false, false, $fulllink);

                echo "<BR>";
            }
        }
    }

    print_footer($course);

?>

