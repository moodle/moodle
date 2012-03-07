<?php

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

    $PAGE->set_url('/course/search.php', compact('search', 'page', 'perpage', 'blocklist', 'modulelist', 'edit'));
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
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

    $urlparams = array();
    foreach (array('search', 'page', 'blocklist', 'modulelist') as $param) {
        if (!empty($$param)) {
            $urlparams[$param] = $$param;
        }
    }
    if ($perpage != 10) {
        $urlparams['perpage'] = $perpage;
    }
    $PAGE->set_url('/course/search.php', $urlparams);
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->set_pagelayout('standard');

    if ($CFG->forcelogin) {
        require_login();
    }

    if (can_edit_in_category()) {
        if ($edit !== -1) {
            $USER->editing = $edit;
        }
        $adminediting = $PAGE->user_is_editing();
    } else {
        $adminediting = false;
    }

/// Editing functions
    if (has_capability('moodle/course:visibility', get_context_instance(CONTEXT_SYSTEM))) {
    /// Hide or show a course
        if ($hide or $show and confirm_sesskey()) {
            if ($hide) {
                $course = $DB->get_record("course", array("id"=>$hide));
                $visible = 0;
            } else {
                $course = $DB->get_record("course", array("id"=>$show));
                $visible = 1;
            }
            if ($course) {
                $DB->set_field("course", "visible", $visible, array("id"=>$course->id));
            }
        }
    }

    $capabilities = array('moodle/course:create', 'moodle/category:manage');
    if (has_any_capability($capabilities, get_context_instance(CONTEXT_SYSTEM)) && ($perpage != 99999)) {
        $perpage = 30;
    }

    $displaylist = array();
    $parentlist = array();
    make_categories_list($displaylist, $parentlist);

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
        $PAGE->navbar->add($strcourses, new moodle_url('/course/index.php'));
        $PAGE->navbar->add($strsearch);
        $PAGE->set_title("$site->fullname : $strsearch");
        $PAGE->set_heading($site->fullname);

        echo $OUTPUT->header();
        echo $OUTPUT->box_start();
        echo "<center>";
        echo "<br />";
        print_course_search("", false, "plain");
        echo "<br /><p>";
        print_string("searchhelp");
        echo "</p>";
        echo "</center>";
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    }

    if (!empty($moveto) and $data = data_submitted() and confirm_sesskey()) {   // Some courses are being moved
        if (! $destcategory = $DB->get_record("course_categories", array("id"=>$data->moveto))) {
            print_error('cannotfindcategory', '', '', $data->moveto);
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
        $blockname = $DB->get_field('block', 'name', array('id' => $blocklist));
        $courses = array();
        $courses = $DB->get_records_sql("
                SELECT * FROM {course} WHERE id IN (
                    SELECT DISTINCT ctx.instanceid
                    FROM {context} ctx
                    JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                    WHERE ctx.contextlevel = " . CONTEXT_COURSE . " AND bi.blockname = ?)",
                array($blockname));
        $totalcount = count($courses);
        //Keep only chunk of array which you want to display
        if ($totalcount > $perpage) {
            $courses = array_chunk($courses, $perpage, true);
            $courses = $courses[$page];
        }
        foreach ($courses as $course) {
            $courses[$course->id] = $course;
        }
    }
    // get list of courses containing modules if required
    elseif (!empty($modulelist) and confirm_sesskey()) {
        $modulename = $modulelist;
        $sql =  "SELECT DISTINCT c.id FROM {".$modulelist."} module, {course} c"
            ." WHERE module.course=c.id";

        $courseids = $DB->get_records_sql($sql);
        $courses = array();
        if (!empty($courseids)) {
            $firstcourse = $page*$perpage;
            $lastcourse = $page*$perpage + $perpage -1;
            $i = 0;
            foreach ($courseids as $courseid) {
                if ($i>= $firstcourse && $i<=$lastcourse) {
                    $courses[$courseid->id] = $DB->get_record('course', array('id'=> $courseid->id));
                }
                $i++;
            }
            $totalcount = count($courseids);
        }
        else {
            $totalcount = 0;
        }
    }
    else {
        $courses = get_courses_search($searchterms, "fullname ASC",
            $page, $perpage, $totalcount);
    }

    $searchform = print_course_search($search, true, "navbar");

    if (!empty($courses) && has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
        $searchform = '';
        // not sure if this capability is the best  here
        if (has_capability('moodle/category:manage', get_context_instance(CONTEXT_SYSTEM))) {
            if ($PAGE->user_is_editing()) {
                $string = get_string("turneditingoff");
                $edit = "off";
            } else {
                $string = get_string("turneditingon");
                $edit = "on";
            }

            $aurl = new moodle_url("$CFG->wwwroot/course/search.php", array(
                    'edit' => $edit,
                    'sesskey' => sesskey(),
                    'search' => $search,
                    'page' => $page,
                    'perpage' => $perpage));
            $searchform = $OUTPUT->single_button($aurl, $string, 'get');
        }
    }

    $PAGE->navbar->add($strcourses, new moodle_url('/course/index.php'));
    $PAGE->navbar->add($strsearch, new moodle_url('/course/search.php'));
    if (!empty($search)) {
        $PAGE->navbar->add(s($search));
    }
    $PAGE->set_title("$site->fullname : $strsearchresults");
    $PAGE->set_heading($site->fullname);
    $PAGE->set_button($searchform);

    echo $OUTPUT->header();

    $lastcategory = -1;
    if ($courses) {
        echo $OUTPUT->heading("$strsearchresults: $totalcount");
        $encodedsearch = urlencode($search);

        // add the module/block parameter to the paging bar if they exists
        $modulelink = "";
        if (!empty($modulelist) and confirm_sesskey()) {
            $modulelink = "&amp;modulelist=".$modulelist."&amp;sesskey=".sesskey();
        } else if (!empty($blocklist) and confirm_sesskey()) {
            $modulelink = "&amp;blocklist=".$blocklist."&amp;sesskey=".sesskey();
        }

        print_navigation_bar($totalcount, $page, $perpage, $encodedsearch, $modulelink);

        if (!$adminediting) {
            foreach ($courses as $course) {

                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

                $course->summary .= "<br /><p class=\"category\">";
                $course->summary .= "$strcategory: <a href=\"category.php?id=$course->category\">";
                $course->summary .= $displaylist[$course->category];
                $course->summary .= "</a></p>";
                print_course($course, $search);
                echo $OUTPUT->spacer(array('height'=>5, 'width'=>5, 'br'=>true)); // should be done with CSS instead
            }
        } else {
        /// Show editing UI.
            echo "<form id=\"movecourses\" action=\"search.php\" method=\"post\">\n";
            echo "<div><input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />\n";
            echo "<input type=\"hidden\" name=\"search\" value=\"".s($search)."\" />\n";
            echo "<input type=\"hidden\" name=\"page\" value=\"$page\" />\n";
            echo "<input type=\"hidden\" name=\"perpage\" value=\"$perpage\" /></div>\n";
            echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"4\" class=\"generalbox boxaligncenter\">\n<tr>\n";
            echo "<th scope=\"col\">$strcourses</th>\n";
            echo "<th scope=\"col\">$strcategory</th>\n";
            echo "<th scope=\"col\">$strselect</th>\n";
            echo "<th scope=\"col\">$stredit</th></tr>\n";

            foreach ($courses as $course) {

                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

                $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                // are we displaying the front page (courseid=1)?
                if ($course->id == 1) {
                    echo "<tr>\n";
                    echo "<td><a href=\"$CFG->wwwroot\">$strfrontpage</a></td>\n";

                    // can't do anything else with the front page
                    echo "  <td>&nbsp;</td>\n"; // category place
                    echo "  <td>&nbsp;</td>\n"; // select place
                    echo "  <td>&nbsp;</td>\n"; // edit place
                    echo "</tr>\n";
                    continue;
                }

                echo "<tr>\n";
                echo "<td><a $linkcss href=\"view.php?id=$course->id\">"
                    . highlight($search, format_string($course->fullname)) . "</a></td>\n";
                echo "<td>".$displaylist[$course->category]."</td>\n";
                echo "<td>\n";

                // this is ok since this will get inherited from course category context
                // if it is set
                if (has_capability('moodle/category:manage', $coursecontext)) {
                    echo "<input type=\"checkbox\" name=\"c$course->id\" />\n";
                } else {
                    echo "<input type=\"checkbox\" name=\"c$course->id\" disabled=\"disabled\" />\n";
                }

                echo "</td>\n";
                echo "<td>\n";

                // checks whether user can update course settings
                if (has_capability('moodle/course:update', $coursecontext)) {
                    echo "<a title=\"".get_string("settings")."\" href=\"$CFG->wwwroot/course/edit.php?id=$course->id\">\n<img".
                        " src=\"" . $OUTPUT->pix_url('t/edit') . "\" class=\"iconsmall\" alt=\"".get_string("settings")."\" /></a>\n ";
                }

                // checks whether user can do role assignment
                if (has_capability('moodle/course:enrolreview', $coursecontext)) {
                    echo'<a title="'.get_string('enrolledusers', 'enrol').'" href="'.$CFG->wwwroot.'/enrol/users.php?id='.$course->id.'">';
                    echo '<img src="'.$OUTPUT->pix_url('i/users') . '" class="iconsmall" alt="'.get_string('enrolledusers', 'enrol').'" /></a> ' . "\n";
                }

                // checks whether user can delete course
                if (has_capability('moodle/course:delete', $coursecontext)) {
                    echo "<a title=\"".get_string("delete")."\" href=\"delete.php?id=$course->id\">\n<img".
                        " src=\"" . $OUTPUT->pix_url('t/delete') . "\" class=\"iconsmall\" alt=\"".get_string("delete")."\" /></a>\n ";
                }

                // checks whether user can change visibility
                if (has_capability('moodle/course:visibility', $coursecontext)) {
                    if (!empty($course->visible)) {
                        echo "<a title=\"".get_string("hide")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;hide=$course->id&amp;sesskey=".sesskey()."\">\n<img".
                            " src=\"" . $OUTPUT->pix_url('t/hide') . "\" class=\"iconsmall\" alt=\"".get_string("hide")."\" /></a>\n ";
                    } else {
                        echo "<a title=\"".get_string("show")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;show=$course->id&amp;sesskey=".sesskey()."\">\n<img".
                            " src=\"" . $OUTPUT->pix_url('t/show') . "\" class=\"iconsmall\" alt=\"".get_string("show")."\" /></a>\n ";
                    }
                }

                // checks whether user can do site backup
                if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
                    $backupurl = new moodle_url('/backup/backup.php', array('id' => $course->id));
                    echo "<a title=\"".get_string("backup")."\" href=\"".$backupurl."\">\n<img".
                        " src=\"" . $OUTPUT->pix_url('t/backup') . "\" class=\"iconsmall\" alt=\"".get_string("backup")."\" /></a>\n ";
                }

                // checks whether user can do restore
                if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
                    $restoreurl = new moodle_url('/backup/restorefile.php', array('contextid' => $coursecontext->id));
                    echo "<a title=\"".get_string("restore")."\" href=\"".$restoreurl."\">\n<img".
                        " src=\"" . $OUTPUT->pix_url('t/restore') . "\" class=\"iconsmall\" alt=\"".get_string("restore")."\" /></a>\n ";
                }

                echo "</td>\n</tr>\n";
            }
            echo "<tr>\n<td colspan=\"4\" style=\"text-align:center\">\n";
            echo "<br />";
            echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
            echo "<input type=\"button\" onclick=\"checknone()\" value=\"$strdeselectall\" />\n";
            echo html_writer::select($displaylist, 'moveto', '', array(''=>get_string('moveselectedcoursesto')), array('id'=>'movetoid'));
            $PAGE->requires->js_init_call('M.util.init_select_autosubmit', array('movecourses', 'movetoid', false));
            echo "</td>\n</tr>\n";
            echo "</table>\n</form>";

        }

        print_navigation_bar($totalcount,$page,$perpage,$encodedsearch,$modulelink);

    } else {
        if (!empty($search)) {
            echo $OUTPUT->heading(get_string("nocoursesfound",'', s($search)));
        }
        else {
            echo $OUTPUT->heading( $strnovalidcourses );
        }
    }

    echo "<br /><br />";

    print_course_search($search);

    echo $OUTPUT->footer();

   /**
     * Print a list navigation bar
     * Display page numbers, and a link for displaying all entries
     * @param integer $totalcount - number of entry to display
     * @param integer $page - page number
     * @param integer $perpage - number of entry per page
     * @param string $encodedsearch
     * @param string $modulelink - module name
     */
    function print_navigation_bar($totalcount,$page,$perpage,$encodedsearch,$modulelink) {
        global $OUTPUT;
        echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "search.php?search=$encodedsearch".$modulelink."&perpage=$perpage");

        //display
        if ($perpage != 99999 && $totalcount > $perpage) {
            echo "<center><p>";
            echo "<a href=\"search.php?search=$encodedsearch".$modulelink."&amp;perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
            echo "</p></center>";
        } else if ($perpage === 99999) {
            $defaultperpage = 10;
            //If user has course:create or category:manage capability the show 30 records.
            $capabilities = array('moodle/course:create', 'moodle/category:manage');
            if (has_any_capability($capabilities, get_context_instance(CONTEXT_SYSTEM))) {
                $defaultperpage = 30;
            }

            echo "<center><p>";
            echo "<a href=\"search.php?search=$encodedsearch".$modulelink."&amp;perpage=".$defaultperpage."\">".get_string("showperpage", "", $defaultperpage)."</a>";
            echo "</p></center>";
        }
    }


