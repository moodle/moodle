<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);           // course id
    optional_variable($search, "");  // search string
    optional_variable($page, "0");   // which page to show
    optional_variable($perpage, "20");   // which page to show

    $search = trim(strip_tags($search));

    if ($search) {
        $searchterms = explode(" ", $search);    // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (strlen($searchterm) < 2) {
                unset($searchterms[$key]);
            }
        }
        $search = s(trim(implode(" ", $searchterms)));
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    if ($course->category or $CFG->forcelogin) {
        require_login($course->id);
    }

    add_to_log($course->id, "forum", "search", "search.php?id=$course->id&amp;search=".urlencode($search), $search);

    $strforums = get_string("modulenameplural", "forum");
    $strsearch = get_string("search", "forum");
    $strsearchresults = get_string("searchresults", "forum");
    $strpage = get_string("page");
    $strmissingsearchterms = get_string('missingsearchterms','forum');

    $searchform = forum_print_search_form($course, $search, true, "plain");

    if (!$search) {
        print_header_simple("$strsearch", "",
                 "<a href=\"index.php?id=$course->id\">$strforums</a> -> $strsearch", "search.search",
                  "", "", "&nbsp;", navmenu($course));

        print_simple_box_start("center");
        echo "<center>";
        echo "<br />";
        echo $searchform;
        echo "<br /><p>";
        print_string("searchhelp");
        echo "</p>";
        echo "</center>";
        print_simple_box_end();
    }

    if ($search) {
        $strippedsearch = str_replace("user:","",$search);
        $strippedsearch = str_replace("subject:","",$strippedsearch);
        $strippedsearch = str_replace("&quot;","",$strippedsearch);

        if (!$posts = forum_search_posts($searchterms, $course->id, $page*$perpage, $perpage, $totalcount)) {

            print_header_simple("$strsearchresults", "",
                     "<a href=\"index.php?id=$course->id\">$strforums</a> ->
                      <a href=\"search.php?id=$course->id\">$strsearch</a> -> \"$search\"", "search.search",
                      "", "", "&nbsp;", navmenu($course));
            print_heading(get_string("nopostscontaining", "forum", $search));

            print_simple_box_start("center");
            echo "<center>";
            echo "<br />";
            echo $searchform;
            echo "<br /><p>";
            print_string("searchhelp");
            echo "</p>";
            echo "</center>";
            print_simple_box_end();
            print_footer($course);
            exit;
        }

        print_header_simple("$strsearchresults", "",
                 "<a href=\"index.php?id=$course->id\">$strforums</a> ->
                  <a href=\"search.php?id=$course->id\">$strsearch</a> -> \"$search\"", "search.search",
                  "", "",  $searchform, navmenu($course));

        print_heading("$strsearchresults: $totalcount");

        echo "<center>";
        print_paging_bar($totalcount, $page, $perpage, "search.php?search=$search&amp;id=$course->id&amp;perpage=$perpage&amp;");
        echo "</center>";

        //added to implement highlighting of search terms found only in HTML markup
        //fiedorow - 9/2/2005
        $searchterms = explode(" ", $strippedsearch);    // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (preg_match('/^\-/',$searchterm)) {
                unset($searchterms[$key]);
            } else {
                $searchterms[$key] = preg_replace('/^\+/','',$searchterm);
            }
        }

        foreach ($posts as $post) {

            if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
                error("Discussion ID was incorrect");
            }
            if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                error("Could not find forum $discussion->forum");
            }

            $post->subject = highlight("$strippedsearch", $post->subject);
            $discussion->name = highlight("$strippedsearch", $discussion->name);

            $fullsubject = "<a href=\"view.php?f=$forum->id\">$forum->name</a>";
            if ($forum->type != "single") {
                $fullsubject .= " -> <a href=\"discuss.php?d=$discussion->id\">$discussion->name</a>";
                if ($post->parent != 0) {
                    $fullsubject .= " -> <a href=\"discuss.php?d=$post->discussion&amp;parent=$post->id\">$post->subject</a>";
                }
            }

            $post->subject = $fullsubject;

            /// Add the forum id to the post object - used by read tracking.
            $post->forum = $forum->id;

            //Indicate search terms only found in HTML markup
            //Use highlight() with nonsense tags to spot search terms in the
            //actual text content first.
            //fiedorow - 9/2/2005
            $missing_terms = "";
            $message = highlight($strippedsearch,format_text($post->message, $post->format, NULL, $course->id),
                                 0,'<fgw9sdpq4>','</fgw9sdpq4>');
            foreach ($searchterms as $searchterm) {
               if (preg_match("/$searchterm/i",$message) && !preg_match('/<fgw9sdpq4>'.$searchterm.'<\/fgw9sdpq4>/i',$message)) {
                  $missing_terms .= " $searchterm";
               }
            }
            $message = str_replace('<fgw9sdpq4>','<span class="highlight">',$message);
            $message = str_replace('</fgw9sdpq4>','</span>',$message);

            if ($missing_terms) {
                $post->message = '<p class="highlight2">'.$strmissingsearchterms.' '.$missing_terms.'</p>'.$message;
            }

            $fulllink = "<a href=\"discuss.php?d=$post->discussion#$post->id\">".get_string("postincontext", "forum")."</a>";
            //search terms already highlighted - fiedorow - 9/2/2005
            forum_print_post($post, $course->id, false, false, false, false, $fulllink);

            echo "<br />";
        }

        echo "<center>";
        print_paging_bar($totalcount, $page, $perpage, "search.php?search=".urlencode($search)."&amp;id=$course->id&amp;perpage=$perpage&amp;");
        echo "</center>";
    }

    print_footer($course);

?>

