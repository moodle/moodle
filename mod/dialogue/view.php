<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $dialogue = get_record("dialogue", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    add_to_log($course->id, "dialogue", "view", "view.php?id=$cm->id", $dialogue->id, $cm->id);

    if (! $cw = get_record("course_sections", "id", $cm->section)) {
        error("Course module is incorrect");
    }

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strdialogue = get_string("modulename", "dialogue");
    $strdialogues = get_string("modulenameplural", "dialogue");

    print_header("$course->shortname: $dialogue->name", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strdialogues</a> -> $dialogue->name",
                 "", "", true,
                  update_module_button($cm->id, $course->id, $strdialogue), navmenu($course, $cm));

	// ...and if necessary set default action 
	
	optional_variable($action);
	
	if (!isguest()) { // it's a teacher or student
		if (!$cm->visible and isstudent($course->id)) {
			$action = 'notavailable';
		}
		if (empty($action)) {
			$action = 'view';
		}
	}
	else { // it's a guest, oh no!
		$action = 'notavailable';
	}
	


/*********************** dialogue not available (for gusets mainly)***********************/
	if ($action == 'notavailable') {
		print_heading(get_string("notavailable", "dialogue"));
	}


	/************ view **************************************************/
	elseif ($action == 'view') {
	
		print_simple_box( format_text($dialogue->intro) , "center");
		echo "<br />";
		// get some stats
        $countneedingrepliesself = dialogue_count_needing_replies_self($dialogue, $USER);
        $countneedingrepliesother = dialogue_count_needing_replies_other($dialogue, $USER);
        $countclosed = dialogue_count_closed($dialogue, $USER);

        // set the pane if it's in a GET or POST
        if (isset($_REQUEST['pane'])) {
            $pane = $_REQUEST['pane'];
        } else {
            // set default pane
            $pane = 0;
            if ($countneedingrepliesother) {
                $pane = 2;
           }
            if ($countneedingrepliesself) {
                $pane =1;
            }
        }
        
        // set up tab table
        $tabs->names[0] = get_string("pane0", "dialogue");
        if ($countneedingrepliesself == 1) {
            $tabs->names[1] = get_string("pane1one", "dialogue");
        } else {
            $tabs->names[1] = get_string("pane1", "dialogue", $countneedingrepliesself);
        }
        if ($countneedingrepliesother == 1) {
            $tabs->names[2] = get_string("pane2one", "dialogue");
        } else {
            $tabs->names[2] = get_string("pane2", "dialogue", $countneedingrepliesother);
        } 
        if ($countclosed == 1) {
            $tabs->names[3] = get_string("pane3one", "dialogue");
        } else {
            $tabs->names[3] = get_string("pane3", "dialogue", $countclosed);
        }

        $tabs->urls[0] = "view.php?id=$cm->id&pane=0";
        $tabs->urls[1] = "view.php?id=$cm->id&pane=1";
        $tabs->urls[2] = "view.php?id=$cm->id&pane=2";
        $tabs->urls[3] = "view.php?id=$cm->id&pane=3";
        $tabs->highlight = $pane;
        dialogue_print_tabbed_heading($tabs);
        echo "<br/><center>\n";
		switch ($pane) {
            case 0: 
                if ($names = dialogue_get_available_users($dialogue)) {
		        	print_simple_box_start("center");
        			echo "<center>";
		        	echo "<form name=\"startform\" method=\"post\" action=\"dialogues.php\">\n";
        			echo "<input type=\"hidden\" name=\"id\"value=\"$cm->id\">\n";
		        	echo "<input type=\"hidden\" name=\"action\" value=\"openconversation\">\n";
        			echo "<table border=\"0\"><tr>\n";
                    echo "<td align=\"right\"><b>".get_string("openadialoguewith", "dialogue").
                        " : </b></td>\n";
        			echo "<td>";
                    choose_from_menu($names, "recipientid");
                    echo "</td></tr>\n";
                    echo "<tr><td align=\"right\"><b>".get_string("subject", "dialogue")." : </b></td>\n";
                    echo "<td><input type=\"text\" size=\"50\" maxsize=\"100\" name=\"subject\" 
                        value=\"\"></td></tr>\n";
        			echo "<tr><td colspan=\"2\" align=\"center\" valign=\"top\"><i>".
                        get_string("typefirstentry", "dialogue")."</i></td></tr>\n";
        			echo "<tr><td valign=\"top\" align=\"right\">\n";
		        	helpbutton("writing", get_string("helpwriting"), "dialogue", true, true);
        			echo "<br />";
        			$showemoticon = false;
		        	if ($showemoticon) {
				        emoticonhelpbutton("replies", "firstentry");
	        		}
			        echo "</td><td>\n";
			        echo "<textarea name=\"firstentry\" rows=\"5\" cols=\"60\" wrap=\"virtual\">";
        			echo "</textarea>\n";
		        	echo "</td></tr>";
        			echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".
                        get_string("opendialogue","dialogue")."\"></td></tr>\n";
        			echo "</table></form>\n";
		        	echo "</center>";
        			print_simple_box_end();
		        } else {
                    print_heading(get_string("noavailablepeople", "dialogue"));
                    print_continue("view.php?id=$cm->id");
                }
                break;
            case 1:
                // print active conversations requiring a reply
        	    dialogue_list_conversations_self($dialogue, $USER);
                break;
            case 2:
                // print active conversations requiring a reply from the other person.
        	    dialogue_list_conversations_other($dialogue, $USER);
                break;
            case 3:
		        dialogue_list_conversations_closed($dialogue, $USER);
		}
	}
		
	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

    print_footer($course);

?>
