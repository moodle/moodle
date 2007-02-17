<?php // $Id$

/// Displays external information about a course

    require_once("../config.php");
    require_once("lib.php");

    $search  = optional_param('search', '', PARAM_RAW);  // search words
    $page    = optional_param('page', 0, PARAM_INT);     // which page to show
    $perpage = optional_param('perpage', 10, PARAM_INT); // how many per page
    $moveto  = optional_param('moveto', 0, PARAM_INT);   // move to category
    $edit    = optional_param('edit', -1, PARAM_BOOL);
    $hide    = optional_param('hide', 0, PARAM_INT);
    $show    = optional_param('show', 0, PARAM_INT);

    $search = trim(strip_tags($search)); // trim & clean raw searched string

    if ($search) {
        $searchterms = explode(" ", $search);    // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (strlen($searchterm) < 2) {
                unset($searchterms[$key]);
            }
        }
        $search = trim(implode(" ", $searchterms));
    }

    $site = get_site();

    if ($CFG->forcelogin) {
        require_login();
    }

    if (iscreator()) {
        if ($edit !== -1) {
            $USER->categoryediting = $edit;
            // If the edit mode we are leaving has higher per page than the one we are entering,
            // with pages, chances are you will get a no courses found error. So when we are switching
            // modes, set page to 0.
            $page = 0;
        }
        $creatorediting = !empty($USER->categoryediting);
        $adminediting = (isadmin() and $creatorediting);

    } else {
        $adminediting = false;
        $creatorediting = false;
    }

/// Editing functions

    if ($adminediting) {

    /// Hide or show a course

        if ($hide or $show and confirm_sesskey()) {
            if ($hide) {
                $course = get_record("course", "id", $hide);
                $visible = 0;
            } else {
                $course = get_record("course", "id", $show);
                $visible = 1;
            }
            if ($course) {
                if (! set_field("course", "visible", $visible, "id", $course->id)) {
                    notify("Could not update that course!");
                }
            }
        }

    }

    if ($adminediting && $perpage != 99999) {
        $perpage = 30;
    }

    $displaylist = array();
    $parentlist = array();
    make_categories_list($displaylist, $parentlist, "");

    $strcourses = get_string("courses");
    $strsearch = get_string("search");
    $strsearchresults = get_string("searchresults");
    $strcategory = get_string("category");
    $strselect   = get_string("select");
    $strselectall = get_string("selectall");
    $strdeselectall = get_string("deselectall");
    $stredit = get_string("edit");

    if (!$search) {
        print_header("$site->fullname : $strsearch", $site->fullname, 
                     "<a href=\"index.php\">$strcourses</a> -> $strsearch", "", "");
        print_simple_box_start("center");
        echo "<center>";
        echo "<br />";
        print_course_search("", false, "plain");
        echo "<br /><p>";
        print_string("searchhelp");
        echo "</p>";
        echo "</center>";
        print_simple_box_end();
        print_footer();
        exit;
    }

    if (!empty($moveto) and $data = data_submitted() and confirm_sesskey()) {   // Some courses are being moved
    
        if (! $destcategory = get_record("course_categories", "id", $data->moveto)) {
            error("Error finding the category");
        }
        
        $courses = array();        
        foreach ( $data as $key => $value ) {
            if (preg_match('/^c\d+$/', $key)) {
                array_push($courses, substr($key, 1));
            }
        }
        move_courses($courses, $data->moveto);
    }

    $courses = get_courses_search($searchterms, "fullname ASC", 
                                  $page*$perpage, $perpage, $totalcount);

    $searchform = print_course_search($search, true, "navbar");

    if (!empty($courses) && iscreator()) {
        $searchform .= update_categories_search_button($search,$page,$perpage);
    }
 

    print_header("$site->fullname : $strsearchresults", $site->fullname, 
                 "<a href=\"index.php\">$strcourses</a> -> <a href=\"search.php\">$strsearch</a> -> '".s($search, true)."'", "", "", "", $searchform);


    $lastcategory = -1;
    if ($courses) {

        print_heading("$strsearchresults: $totalcount");

        $encodedsearch = urlencode(stripslashes($search));
        print_paging_bar($totalcount, $page, $perpage, "search.php?search=$encodedsearch&amp;perpage=$perpage&amp;",'page',($perpage == 99999));

        if ($perpage != 99999 && $totalcount > $perpage) {
            echo "<center><p>";
            echo "<a href=\"search.php?search=$encodedsearch&perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
            echo "</p></center>";
        }

        if (!$adminediting) {
            foreach ($courses as $course) {
                $course->fullname = highlight("$search", $course->fullname);
                $course->summary = highlight("$search", $course->summary);
                $course->summary .= "<br /><p align=\"right\">";
                $course->summary .= "$strcategory: <a href=\"category.php?id=$course->category\">";
                $course->summary .= $displaylist[$course->category];
                $course->summary .= "</a></p>";
                print_course($course);
                print_spacer(5,5);
            }
        } else { // slightly more sophisticated

            echo "<form name=\"movecourses\" action=\"search.php\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\">";
            echo "<input type=\"hidden\" name=\"search\" value=\"".s($search, true)."\">";
            echo "<input type=\"hidden\" name=\"page\" value=\"$page\">";
            echo "<input type=\"hidden\" name=\"perpage\" value=\"$perpage\">";
            echo "<table align=\"center\" border=0 cellspacing=2 cellpadding=4 class=\"generalbox\"><tr>";
            echo "<th>$strcourses</th>";
            echo "<th>$strcategory</th>";
            echo "<th>$strselect</th>";
            echo "<th>$stredit</th>";
            foreach ($courses as $course) {
                $course->fullname = highlight("$search", $course->fullname);
                $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
                echo "<tr>";
                echo "<td><a $linkcss href=\"view.php?id=$course->id\">$course->fullname</a></td>";
                echo "<td>".$displaylist[$course->category]."</td>";
                echo "<td align=\"center\">";
                echo "<input type=\"checkbox\" name=\"c$course->id\">";
                echo "</td>";
                echo "<td>";
                if (empty($THEME->custompix)) {
                    $pixpath = "$CFG->wwwroot/pix";
                } else {
                    $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
                }
                echo "<a title=\"".get_string("settings")."\" href=\"$CFG->wwwroot/course/edit.php?id=$course->id\"><img".
                    " src=\"$pixpath/t/edit.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                echo "<a title=\"".get_string("assignteachers")."\" href=\"$CFG->wwwroot/course/teacher.php?id=$course->id\"><img".
                    " src=\"$pixpath/t/user.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                echo "<a title=\"".get_string("delete")."\" href=\"delete.php?id=$course->id\"><img".
                    " src=\"$pixpath/t/delete.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                if (!empty($course->visible)) {
                    echo "<a title=\"".get_string("hide")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;hide=$course->id&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$pixpath/t/hide.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                } else {
                    echo "<a title=\"".get_string("show")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;show=$course->id&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$pixpath/t/show.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                }
                
                echo "<a title=\"".get_string("backup")."\" href=\"../backup/backup.php?id=$course->id\"><img".
                    " src=\"$pixpath/t/backup.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                
                echo "<a title=\"".get_string("restore")."\" href=\"../files/index.php?id=$course->id&wdir=/backupdata\"><img".
                    " src=\"$pixpath/t/restore.gif\" height=\"11\" width=\"11\" border=\"0\"></a> ";
                echo "</td></tr>";
            }
            echo "<tr><td colspan=\"4\" align=\"center\">";
            echo "<br />";
            echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
            echo "<input type=\"button\" onclick=\"uncheckall()\" value=\"$strdeselectall\" />\n";
            choose_from_menu ($displaylist, "moveto", "", get_string("moveselectedcoursesto"), "javascript:document.movecourses.submit()");
            echo "</td></tr>";
            echo "</table>";

        }

        print_paging_bar($totalcount, $page, $perpage, "search.php?search=$encodedsearch&amp;perpage=$perpage&amp;",'page',($perpage == 99999));

        if ($perpage != 99999 && $totalcount > $perpage) {
            echo "<center><p>";
            echo "<a href=\"search.php?search=$encodedsearch&perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
            echo "</p></center>";
        }

    } else {
        print_heading(get_string("nocoursesfound", "", s($search, true)));
    }

    echo "<br /><br />";

    print_course_search($search);

    print_footer();


?>

