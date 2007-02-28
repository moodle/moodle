<?php // $Id$
      // Allows a creator to edit custom scales, and also display help about scales

    require_once("../config.php");
    require_once("lib.php");

    $id          = required_param('id', PARAM_INT);               // course id
    $scaleid     = optional_param('scaleid', 0, PARAM_INT);       // scale id
    $action      = optional_param('action', 'undefined', PARAM_ALPHA); // action to execute
    $name        = optional_param('name', '', PARAM_CLEAN);       // scale name
    $description = optional_param('description', '', PARAM_CLEAN);// scale description
    $list        = optional_param('list', 0, PARAM_BOOL);         // show listing in help window

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $strscale = get_string("scale");
    $strscales = get_string("scales");
    $strcustomscale = get_string("scalescustom");
    $strstandardscale = get_string("scalesstandard");
    $strcustomscales = get_string("scalescustom");
    $strstandardscales = get_string("scalesstandard");
    $strname = get_string("name");
    $strdescription = get_string("description");
    $strsavechanges = get_string("savechanges");
    $strchangessaved = get_string("changessaved");
    $strdeleted = get_string("deleted");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $strdown = get_string("movedown");
    $strup = get_string("moveup");
    $strmoved = get_string("changessaved");
    $srtcreatenewscale = get_string("scalescustomcreate");
    $strhelptext = get_string("helptext");
    $stractivities = get_string("activities");
    $stroptions = get_string("action");
    $strtype = get_string("group");

    /// init this here so we can pass it by reference to every call to site_scale_used to avoid getting the courses out of the db over and over again
    $courses = array();

    /// If scale data is being submitted, then save it and continue
    $focus = "";
    $errors = NULL;

    if ($action == 'sendform' and confirm_sesskey()) {
        if ($form = data_submitted()) {
            if (empty($form->name)) {
                $errors[$scaleid]->name = true;
                $focus = "form$scaleid.save";
            }
            if (empty($form->scalescale)) {
                $errors[$scaleid]->scalescale = true;
                $focus = "form$scaleid.save";
            }

            if (!$errors) {
                $newscale=NULL;
                $newscale->name = $form->name;
                $newscale->scale = $form->scalescale;
                $newscale->description = $form->description;
                $newscale->courseid = $form->courseid;
                $newscale->userid = $USER->id;
                $newscale->timemodified = time();

                if (empty($scaleid)) {
                    $newscale->courseid = $course->id;
                    if (!insert_record("scale", $newscale)) {
                        error("Could not insert the new scale!");
                    }
                } else {
                    $newscale->id = $scaleid;
                    if (!update_record("scale", $newscale)) {
                        error("Could not update that scale!");
                    }
                }

                $notify = "$newscale->name: $strchangessaved";
                $focus = "form$scaleid.save";
            } else {
                if (!empty($scaleid)) {
                    $action = "edit";
                } else {
                    $action = "new";
                }
            }
        }
    }

    //If action is details, show the popup info
    if ($action == "details") {
        //Check for teacher edit
           require_capability('moodle/course:managescales', $context);
       
        //Check for scale
        if (! $scale = get_record("scale", "id", $scaleid)) {
            error("Scale ID was incorrect");
        }

        //        $scales_course_uses = course_scale_used($course->id,$scale->id);
        //        $scales_site_uses = site_scale_used($scale->id,$courses);
        $scalemenu = make_menu_from_list($scale->scale);

        print_header("$course->shortname: $strscales", $course->fullname,
                 "$course->shortname -> $strscales -> $scale->name", "", "", true, "&nbsp;", "&nbsp;");

        close_window_button();

        echo "<p/>";
        print_simple_box_start("center");
        print_heading($scale->name);
        echo "<center>";
        choose_from_menu($scalemenu, "", "", "");
        echo "</center>";
        echo text_to_html($scale->description);
        print_simple_box_end();
        echo "<p/>";

        close_window_button();
        print_footer();
        exit;
    }

    //If action is edit or new, show the form
    if ($action == "edit" || $action == "new") {

        $sesskey = !empty($USER->id) ? $USER->sesskey : '';

           require_capability('moodle/course:managescales', $context);
           
        //Check for scale if action = edit
        if ($action == "edit") {
            if (! $scale = get_record("scale", "id", $scaleid)) {
                error("Scale ID was incorrect");
            }
        } else {
            $scale = new object();
            $scale->id = 0;
            $scale->courseid = $course->id;
            $scale->name = "";
            $scale->scale = "";
            $scale->description = "";
        }

        //Calculate the uses
        if ($scale->courseid == 0) {
            $scale_uses = site_scale_used($scale->id,$courses);
        } else {
            $scale_uses = course_scale_used($course->id,$scale->id);
        }

        //Check for scale_uses
        if (!empty($scale_uses)) {
            error("Scale is in use and cannot be modified",$CFG->wwwroot.'/course/scales.php?id='.$course->id);
        }

        //Check for standard scales
        if ($scale->courseid == 0 and !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            error("Only administrators can edit this scale",$CFG->wwwroot.'/course/scales.php?id='.$course->id);
        }

        //Print out the headers
        print_header("$course->shortname: $strscales", $course->fullname,
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a>".
                 " -> <a href=\"scales.php?id=$course->id\">$strscales</a>".
                 " -> ".get_string("editinga","",$strscale), $focus);

        //Title
        print_heading_with_help($strscales, "scales");

        if (!empty($errors) and ($form->scaleid == $scale->id)) {
            $scale->name = $form->name;
            $scale->scale = $form->scalescale;
            $scale->description = $form->description;
        }
        echo "<form method=\"post\" action=\"scales.php\" id=\"form$scale->id\">";
        echo "<table cellpadding=\"9\" cellspacing=\"0\" align=\"center\" class=\"generalbox\">";
        echo "<tr valign=\"top\">";
        if (!empty($errors[$scale->id]->name)) {
            $class = "class=\"highlight\"";
        } else {
            $class = "";
        }
        echo "<td align=\"right\"><b>$strname:</b></td>";
        echo "<td $class><input type=\"text\" name=\"name\" size=\"50\" value=\"".s($scale->name)."\" />";
        echo "</td>";
        echo "</tr>";
        echo "<tr valign=\"top\">";
        if (!empty($errors[$scale->id]->scalescale)) {
            $class = "class=\"highlight\"";
        } else {
            $class = "";
        }
        echo "<td align=\"right\"><b>$strscale:</b></td>";
        echo "<td $class><textarea name=\"scalescale\" cols=\"50\" rows=\"2\" wrap=\"virtual\">".s($scale->scale)."</textarea>";
        echo "</td>";
        echo "</tr>";
        echo "<tr valign=\"top\">";
        echo "<td align=\"right\"><b>$strdescription:</b>";
        helpbutton("text", $strhelptext);
        echo "</td>";
        echo "<td><textarea name=\"description\" cols=\"50\" rows=\"8\" wrap=\"virtual\">".s($scale->description)."</textarea>";
        echo "</td>";
        echo "</tr>";
        if ($scale->id) {
            echo "<tr valign=\"top\">";
            echo "<td align=\"right\">";
            echo "</td>";
            echo "<td>".get_string("usedinnplaces","",$scale_uses);
            echo "</td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td colspan=\"2\" align=\"center\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
        echo "<input type=\"hidden\" name=\"sesskey\" value=\"$sesskey\" />";
        echo "<input type=\"hidden\" name=\"courseid\" value=\"$scale->courseid\" />";
        echo "<input type=\"hidden\" name=\"scaleid\" value=\"$scale->id\" />";
        echo "<input type=\"hidden\" name=\"action\" value=\"sendform\" />";
        echo "<input type=\"submit\" name=\"save\" value=\"$strsavechanges\" />";
        echo "</td></tr></table>";
        echo "</form>";
        echo "<br />";

        print_footer($course);

        exit;
    }

    //If action is delete, do it
    if ($action == "delete" and confirm_sesskey()) {
        //Check for teacher edit
           require_capability('moodle/course:managescales', $context);
        //Check for scale if action = edit
        if (! $scale = get_record("scale", "id", $scaleid)) {
            error("Scale ID was incorrect");
        }

        //Calculate the uses
        if ($scale->courseid == 0) {
            $scale_uses = site_scale_used($scale->id,$courses);
        } else {
            $scale_uses = course_scale_used($course->id,$scale->id);
        }

        //Check for scale_uses
        if (!empty($scale_uses)) {
            error("Scale is in use and cannot be deleted",$CFG->wwwroot.'/course/scales.php?id='.$course->id);
        }

        //Check for standard scales
        if ($scale->courseid == 0 and !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            error("Only administrators can delete this scale",$CFG->wwwroot.'/course/scales.php?id='.$course->id);
        }

        if (delete_records("scale", "id", $scaleid)) {
            $notify = "$scale->name: $strdeleted";
        }
    }

    //If action is down or up, do it
    if (($action == "down" || $action == "up") and confirm_sesskey()) {
        //Check for teacher edit
           require_capability('moodle/course:managescales', $context);
        //Check for scale if action = edit
        if (! $scale = get_record("scale", "id", $scaleid)) {
            error("Scale ID was incorrect");
        }

        //Calculate the uses
        if ($scale->courseid == 0) {
            $scale_uses = site_scale_used($scale->id,$courses);
        } else {
            $scale_uses = course_scale_used($course->id,$scale->id);
        }

        //Check for scale_uses
        if (!empty($scale_uses)) {
            error("Scale is in use and cannot be moved",$CFG->wwwroot.'/course/scales.php?id='.$course->id);
        }

        if ($action == "down") {
            $scale->courseid = 0;
        } else {
            $scale->courseid = $course->id;
        }

        if (set_field("scale", "courseid", $scale->courseid, "id", $scale->id)) {
            $notify = "$scale->name: $strmoved";
        }
    }

    if ($list) {       /// Just list the scales (in a helpwindow)
           require_capability('moodle/course:viewscales', $context);
        print_header($strscales);

        if (!empty($scaleid)) {
            if ($scale = get_record("scale", "id", "$scaleid")) {
                $scalemenu = make_menu_from_list($scale->scale);

                print_simple_box_start("center");
                print_heading($scale->name);
                echo "<center>";
                choose_from_menu($scalemenu, "", "", "");
                echo "</center>";
                echo text_to_html($scale->description);
                print_simple_box_end();
            }
            echo "<br />";
            close_window_button();
            exit;
        }

        if ($scales = get_records("scale", "courseid", "$course->id", "name ASC")) {
            print_heading($strcustomscales);

            if (has_capability('moodle/course:managescales', get_context_instance(CONTEXT_COURSE, $course->id))) {
                echo "<p align=\"center\">(";
                print_string("scalestip");
                echo ")</p>";
            }

            foreach ($scales as $scale) {
                $scalemenu = make_menu_from_list($scale->scale);

                print_simple_box_start("center");
                print_heading($scale->name);
                echo "<center>";
                choose_from_menu($scalemenu, "", "", "");
                echo "</center>";
                echo text_to_html($scale->description);
                print_simple_box_end();
                echo "<hr />";
            }

        } else {
            if (has_capability('moodle/course:managescales', $context)) {
                echo "<p align=\"center\">(";
                print_string("scalestip");
                echo ")</p>";
            }
        }

        if ($scales = get_records("scale", "courseid", "0", "name ASC")) {
            print_heading($strstandardscales);
            foreach ($scales as $scale) {
                $scalemenu = make_menu_from_list($scale->scale);

                print_simple_box_start("center");
                print_heading($scale->name);
                echo "<center>";
                choose_from_menu($scalemenu, "", "", "");
                echo "</center>";
                echo text_to_html($scale->description);
                print_simple_box_end();
                echo "<hr />";
            }
        }

        close_window_button();
        exit;
    }


/// The rest is all about editing the scales

    require_capability('moodle/course:managescales', $context);

/// Print out the main page

    print_header("$course->shortname: $strscales", $course->fullname, 
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> 
                  -> $strscales");

    print_heading_with_help($strscales, "scales");

    $options = array();
    $options['id'] = $course->id;
    $options['scaleid'] = 0;
    $options['action'] = 'new';

    print_simple_box_start('center');
    print_single_button($CFG->wwwroot.'/course/scales.php',$options,$srtcreatenewscale,'POST');
    print_simple_box_end();
    echo "<p />";

    if (!empty($notify)) {
        notify($notify, "green");
    }

    $scales = array();
    $customscales = get_records("scale", "courseid", "$course->id", "name ASC");
    $standardscales = get_records("scale", "courseid", "0", "name ASC");

    if ($customscales) {
        foreach($customscales as $scale) {
            $scales[] = $scale;
        }
    }

    if ($standardscales) {
        foreach($standardscales as $scale) {
            $scales[] = $scale;
        }
    }

    if ($scales) {
        //Calculate the base path
        $path = "$CFG->wwwroot/course";

        $data = array();
        $incustom = true;
        foreach($scales as $scale) {
            //Check the separator
            if (empty($scale->courseid) && $incustom) {
                $incustom = false;
                $line = "hr";
                $data[] = $line;
            }
            $line = array();
            $line[] = "<a target=\"scale\" title=\"$scale->name\" href=\"$CFG->wwwroot/course/scales.php?id=$course->id&amp;scaleid=$scale->id&amp;action=details\" "."onclick=\"return openpopup('/course/scales.php?id=$course->id\&amp;scaleid=$scale->id&amp;action=details', 'scale', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">".$scale->name."</a><br /><font size=\"-1\">".str_replace(",",", ",$scale->scale)."</font>";
            if (!empty($scale->courseid)) {
                $scales_uses = course_scale_used($course->id,$scale->id);
            } else {
                $scales_uses = site_scale_used($scale->id,$courses);
            }
            $line[] = $scales_uses;
            if ($incustom) {
                $line[] = $strcustomscale;
            } else {
                $line[] = $strstandardscale;
            }
            $buttons = "";
            if (empty($scales_uses) && ($incustom || has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM, SITEID)))) {
                $buttons .= "<a title=\"$stredit\" href=\"$path/scales.php?id=$course->id&amp;scaleid=$scale->id&amp;action=edit\"><img".
                            " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
                if ($incustom && has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                    $buttons .= "<a title=\"$strdown\" href=\"$path/scales.php?id=$course->id&amp;scaleid=$scale->id&amp;action=down&amp;sesskey=$USER->sesskey\"><img".
                                " src=\"$CFG->pixpath/t/down.gif\" class=\"iconsmall\" alt=\"$strdown\" /></a> ";
                }
                if (!$incustom && has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                    $buttons .= "<a title=\"$strup\" href=\"$path/scales.php?id=$course->id&amp;scaleid=$scale->id&amp;action=up&amp;sesskey=$USER->sesskey\"><img".
                                " src=\"$CFG->pixpath/t/up.gif\" class=\"iconsmall\" alt=\"$strup\" /></a> ";
                }
                $buttons .= "<a title=\"$strdelete\" href=\"$path/scales.php?id=$course->id&amp;scaleid=$scale->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                            " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
            }
            $line[] = $buttons;

            $data[] = $line;
        }
        $head = $strscale.",".$stractivities.",".$strtype.",".$stroptions;
        $table->head = explode(",",$head);
        $size = "50%,20%,20%,10%";
        $table->size = explode(",",$size);
        $align = "left,center,center,center";
        $table->align = explode(",",$align);
        $wrap = ",nowrap,nowrap,nowrap";
        $table->wrap = explode(",",$wrap);
        $table->width = "90%";
        $table->data = $data;
        print_table ($table);
    }

    print_footer($course);


?>
