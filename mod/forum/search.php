<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);           // course id
    optional_variable($search, "");  // search string

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

    $searchform = forum_print_search_form($course, $search, true, "plain");

    if ($search) {
        print_header("$course->shortname: $strsearchresults", "$course->fullname",
                 "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strforums</A> -> 
                  <A HREF=\"search.php?id=$course->id\">$strsearch</A> -> \"$search\"", "search.search", 
                  "", "",  $searchform);
    } else {
        print_header("$course->shortname: $strsearch", "$course->fullname",
                 "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strforums</A> -> $strsearch", "search.search",
                  "", "",  $searchform);
    }

    if ($search) {
    
        if (!$posts = forum_search_posts($search, $course->id)) {
            print_heading(get_string("nopostscontaining", "forum", $search));

        } else {
            foreach ($posts as $post) {
                if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
                    error("Discussion ID was incorrect");
                }
                if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                    error("Could not find forum $discussion->forum");
                }

                $post->subject = highlightfast("$search", $post->subject);
                $discussion->name = highlightfast("$search", $discussion->name);

                $fullsubject = "<a href=\"view.php?f=$forum->id\">$forum->name</a>";
                if ($forum->type != "single") {
                    $fullsubject .= " -> <a href=\"discuss.php?d=$discussion->id\">$discussion->name</a>";
                    if ($post->parent != 0) {
                        $fullsubject .= " -> <a href=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</a>";
                    }
                }

                $post->subject = $fullsubject;

                $fulllink = "<p align=\"right\"><a href=\"discuss.php?d=$post->discussion&parent=$post->id\">".get_string("postincontext", "forum")."</a></p>";
                forum_print_post($post, $course->id, false, false, false, false, $fulllink, $search);

                echo "<BR>";
            }
        }
    }

    print_footer($course);

?>

