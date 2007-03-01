<?php // $Id$

/// Displays external information about a course

    require_once("../config.php");
    require_once("lib.php");

    $search    = optional_param('search', '', PARAM_RAW);  // search words
    $page      = optional_param('page', 0, PARAM_INT);     // which page to show
    $perpage   = optional_param('perpage', 10, PARAM_INT); // how many per page
    $moveto    = optional_param('moveto', 0, PARAM_INT);   // move to category
    $edit      = optional_param('edit', -1, PARAM_BOOL);
    $hide      = optional_param('hide', 0, PARAM_INT);
    $show      = optional_param('show', 0, PARAM_INT);
    $blocklist = optional_param('blocklist', 0, PARAM_INT);
    $modulelist= optional_param('modulelist', '', PARAM_ALPHAEXT);

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

    if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        if ($edit !== -1) {
            $USER->categoryediting = $edit;
            // If the edit mode we are leaving has higher per page than the one we are entering,
            // with pages, chances are you will get a no courses found error. So when we are switching
            // modes, set page to 0.
            $page = 0;
        }
    } 

/// Editing functions

    if (has_capability('moodle/course:visibility', get_context_instance(CONTEXT_SYSTEM, SITEID))) {

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

    if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID)) && $perpage != 99999) {
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
    $strfrontpage = get_string('frontpage', 'admin');
    $strnovalidcourses = get_string('novalidcourses');

    if (empty($search) and empty($blocklist) and empty($modulelist)) {
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

    // get list of courses containing blocks if required
    if (!empty($blocklist) and confirm_sesskey()) {
        $blockid = $blocklist;
        if (!$blocks = get_records('block_instance', 'blockid', $blockid)) {
            error( "Could not read data for blockid=$blockid" );
        }

        // run through blocks and get (unique) courses
        $courses = array();
        foreach ($blocks as $block) {
            $courseid = $block->pageid;
            if ($courseid==0) {
                continue;
            }
            if (!$course = get_record('course', 'id', $courseid)) {
                error( "Could not read data for courseid=$courseid" );
            }
            $courses[$courseid] = $course;
        }
        $totalcount = count( $courses );
    }
    // get list of courses containing modules if required
    elseif (!empty($modulelist) and confirm_sesskey()) {
        $modulename = $modulelist;
        if (!$modules = get_records($modulename)) {
            error( "Could not read data for module=$modulename" );
        }

        // run through modules and get (unique) courses
        $courses = array();
        foreach ($modules as $module) {
            $courseid = $module->course;
            if ($courseid==0) {
                continue;
            }
            if (!$course = get_record('course', 'id', $courseid)) {
                error( "Could not read data for courseid=$courseid" );
            }
            $courses[$courseid] = $course;
        }
        $totalcount = count($courses);
    }
    else {
        $courses = get_courses_search($searchterms, "fullname ASC", 
            $page*$perpage, $perpage, $totalcount);
    }

    $searchform = print_course_search($search, true, "navbar");

    if (!empty($courses) && has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
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

        if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            foreach ($courses as $course) {
                $course->fullname = highlight("$search", $course->fullname);
                $course->summary = highlight("$search", $course->summary);
                $course->summary .= "<br /><p style=\"text-align:right\">";
                $course->summary .= "$strcategory: <a href=\"category.php?id=$course->category\">";
                $course->summary .= $displaylist[$course->category];
                $course->summary .= "</a></p>";
                print_course($course);
                print_spacer(5,5);
            }
        } else { // slightly more sophisticated

            echo "<form id=\"movecourses\" action=\"search.php\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\">";
            echo "<input type=\"hidden\" name=\"search\" value=\"".s($search, true)."\" />";
            echo "<input type=\"hidden\" name=\"page\" value=\"$page\" />";
            echo "<input type=\"hidden\" name=\"perpage\" value=\"$perpage\" />";
            echo "<table style=\"text-align:center\" border=0 cellspacing=2 cellpadding=4 class=\"generalbox\"><tr>";
            echo "<th scope=\"col\">$strcourses</th>";
            echo "<th scope=\"col\">$strcategory</th>";
            echo "<th scope=\"col\">$strselect</th>";
            echo "<th scope=\"col\">$stredit</th>";
            foreach ($courses as $course) {    		    
                
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

                $course->fullname = highlight("$search", $course->fullname);
                $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                // are we displaying the front page (courseid=1)?
                if ($course->id == 1) {
                    echo "<tr>";
                    echo "<td><a href=\"$CFG->wwwroot\">$strfrontpage</a></td>";

                    // can't do anything else with the front page
                    echo "  <td>&nbsp;</td>"; // category place
                    echo "  <td>&nbsp;</td>"; // select place
                    echo "  <td>&nbsp;</td>"; // edit place
                    echo "</tr>";
                    continue;
                }

                echo "<tr>";
                echo "<td><a $linkcss href=\"view.php?id=$course->id\">" . format_string($course->fullname) . "</a></td>";
                echo "<td>".$displaylist[$course->category]."</td>";
                echo "<td style=\"text-align:center\">";
                
                // this is ok since this will get inherited from course category context
                // if it is set
                if (has_capability('moodle/category:update', $coursecontext)) {
                    echo "<input type=\"checkbox\" name=\"c$course->id\">";
                } else {
                    echo "<input type=\"checkbox\" name=\"c$course->id\" disabled=\"disabled\">";
                }
                
                echo "</td>";
                echo "<td>";
                $pixpath = $CFG->pixpath;
                
                // checks whether user can update course settings
                if (has_capability('moodle/course:update', $coursecontext)) {
                    echo "<a title=\"".get_string("settings")."\" href=\"$CFG->wwwroot/course/edit.php?id=$course->id\"><img".
                        " src=\"$pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"".get_string("settings")."\"></a> ";
                }

                // checks whether user can do role assignment
    		    if (has_capability('moodle/role:assign', $coursecontext)) {
                    echo'<a title="'.get_string('assignroles', 'role').'" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$coursecontext->id.'">';
    		        echo '<img src="'.$CFG->pixpath.'/i/roles.gif" class="iconsmall" alt="'.get_string('assignroles', 'role').'" /></a> ';
    		    }                

                // checks whether user can delete course
                if (has_capability('moodle/course:delete', $coursecontext)) {  
                    echo "<a title=\"".get_string("delete")."\" href=\"delete.php?id=$course->id\"><img".
                        " src=\"$pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"".get_string("delete")."\"></a> ";
                }  

                // checks whether user can change visibility
                if (has_capability('moodle/course:visibility', $coursecontext)) {
                    if (!empty($course->visible)) {
                        echo "<a title=\"".get_string("hide")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;hide=$course->id&amp;sesskey=$USER->sesskey\"><img".
                            " src=\"$pixpath/t/hide.gif\" class=\"iconsmall\" alt=\"".get_string("hide")."\"></a> ";
                    } else {
                        echo "<a title=\"".get_string("show")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;show=$course->id&amp;sesskey=$USER->sesskey\"><img".
                            " src=\"$pixpath/t/show.gif\" class=\"iconsmall\" alt=\"".get_string("show")."\"></a> ";
                    }
                }              

                // checks whether user can do site backup
                if (has_capability('moodle/site:backup', $coursecontext)) {
                    echo "<a title=\"".get_string("backup")."\" href=\"../backup/backup.php?id=$course->id\"><img".
                        " src=\"$pixpath/t/backup.gif\" class=\"iconsmall\" alt=\"".get_string("backup")."\"></a> ";
                }
                
                // checks whether user can do restore
                if (has_capability('moodle/site:restore', $coursecontext)) {
                    echo "<a title=\"".get_string("restore")."\" href=\"../files/index.php?id=$course->id&wdir=/backupdata\"><img".
                        " src=\"$pixpath/t/restore.gif\" class=\"iconsmall\" alt=\"".get_string("restore")."\"></a> ";
                }

                echo "</td></tr>";
            }
            echo "<tr><td colspan=\"4\" style=\"text-align:center\">";
            echo "<br />";
            echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
            echo "<input type=\"button\" onclick=\"uncheckall()\" value=\"$strdeselectall\" />\n";
            choose_from_menu ($displaylist, "moveto", "", get_string("moveselectedcoursesto"), "javascript: getElementById('movecourses').submit()");
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
        if (!empty($search)) {
            print_heading(get_string("nocoursesfound", "", s($search, true)));
        }
        else {
            print_heading( $strnovalidcourses );
        }
    }

    echo "<br /><br />";

    print_course_search($search);

    print_footer();


?>

