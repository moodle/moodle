<?PHP // $Id$
      // Allows a creator to edit custom scales, and also display help about scales

	require_once("../config.php");
	require_once("lib.php");

    require_variable($id);             // course id
    optional_variable($scaleid);  // scale id
    optional_variable($name);          // scale name
    optional_variable($description);   // scale description
    optional_variable($scale);         // scale scale
    optional_variable($delete);        // scale id

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

	require_login($course->id);

    $strscale = get_string("scale");
    $strscales = get_string("scales");
    $strcustomscales = get_string("scalescustom");
    $strstandardscales = get_string("scalesstandard");
    $strname = get_string("name");
    $strdescription = get_string("description");
    $strsavechanges = get_string("savechanges");
    $strchangessaved = get_string("changessaved");
    $strdeleted = get_string("deleted");
    $strdelete = get_string("delete");


    if (isset($_GET['list'])) {       /// Just list the scales (in a helpwindow)

        print_header($strscales);

        if (isset($_GET['scale'])) {
            if ($scale = get_record("scale", "id", "$scale")) {
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

    if (!isteacheredit($course->id)) {
        error("Only editing teachers can modify scales !");
    }


/// If scale data is being submitted, then save it and continue

    $errors = NULL;

    if ($form = data_submitted()) {
        if (!empty($form->delete)) {       /// Delete a scale
            $scale = get_record("scale", "id", $scaleid);
            if (delete_records("scale", "id", $scaleid)) {
                $notify = "$scale->name: $strdeleted";
            }
        } else {                           /// Update scale data
            if (empty($form->name)) {
                $errors[$scaleid]->name = true;
                $focus = "form$scaleid.save";
            }
            if (empty($form->scale)) {
                $errors[$scaleid]->scale = true;
                $focus = "form$scaleid.save";
            }
    
            if (!$errors) {
                $newscale=NULL;
                $newscale->name = $form->name;
                $newscale->scale = $form->scale;
                $newscale->description = $form->description;
                $newscale->courseid = $course->id;
                $newscale->userid = $USER->id;
                $newscale->timemodified = time();
    
                if (empty($scaleid)) {
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
            }
        }
	}


/// Print out the headers

	print_header("$course->shortname: $strscales", "$course->fullname", 
                 "<a href=\"view.php?id=$course->id\">$course->shortname</A> 
                  -> $strscales", $focus);

    print_heading_with_help($strcustomscales, "scales");

    if (!empty($notify)) {
        notify($notify);
    }


/// Otherwise print out all the scale forms

    $customscales = get_records("scale", "courseid", "$course->id", "name ASC");

    $blankscale->id = "";
    $blankscale->name = "";
    $blankscale->scale = "";
    $blankscale->description = "";
    $customscales[] = $blankscale;

    foreach ($customscales as $scale) {
        if (!empty($errors) and ($form->scaleid == $scale->id)) {
            $scale->name = $form->name;
            $scale->scale = $form->scale;
            $scale->description = $form->description;
        }
        echo "<form method=\"post\" action=\"scales.php\" name=\"form$scale->id\">";
        echo "<table cellpadding=9 cellspacing=0 align=center class=generalbox>";
        echo "<tr valign=top>";
        if (!empty($errors[$scale->id]->name)) {
            $class = "class=\"highlight\"";
        } else {
            $class = "";
        }
        echo "<td align=\"right\"><p><b>$strname:</b></p></td>";
        echo "<td $class><input type=\"text\" name=\"name\" size=\"50\" value=\"".s($scale->name)."\">";
        echo "</td>";
        echo "</tr>";
        echo "<tr valign=top>";
        if (!empty($errors[$scale->id]->scale)) {
            $class = "class=\"highlight\"";
        } else {
            $class = "";
        }
        echo "<td align=\"right\"><p><b>$strscale:</b></p></td>";
        echo "<td $class><textarea name=\"scale\" cols=50 rows=1 wrap=virtual>".s($scale->scale)."</textarea>";
        echo "</td>";
        echo "</tr>";
        echo "<tr valign=top>";
        echo "<td align=\"right\"><p><b>$strdescription:</b></p>";
        helpbutton("text", get_string("helptext"));
        echo "</td>";
        echo "<td><textarea name=\"description\" cols=50 rows=8 wrap=virtual>".s($scale->description)."</textarea>";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
	    echo "<td colspan=2 align=\"center\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\">";
        echo "<input type=\"hidden\" name=\"scaleid\" value=\"$scale->id\">";
        echo "<input type=\"submit\" name=\"save\" value=\"$strsavechanges\">";
        if ($scale->id) {
            echo "<input type=\"submit\" name=\"delete\" value=\"$strdelete\">";
        }
        echo "</td></tr></table>";
        echo "</form>";
        echo "<br />";
    }

    echo "<br /><hr noshade=\"noshade\" size=\"1\">";


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

    print_footer($course);


?>
