<?PHP  // $Id$

/// Library of extra functions for the dialogue module

// SQL FUNCTIONS ///////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
function dialogue_count_all_needing_replies_self($user = '') {
// count [conversations] needing replies [from] self for all dialogues
// function requested by Williams Castillo 17 Oct 2003 
    global $USER;
    
    if ($user) {	
    	return count_records_select("dialogue_conversations", "(userid = $user->id OR 
            recipientid = $user->id) AND lastid != $user->id AND closed = 0");
    } else {
    	return count_records_select("dialogue_conversations", "(userid = $USER->id OR 
            recipientid = $USER->id) AND lastid != $USER->id AND closed = 0");
	}
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_count_closed($dialogue, $user) {
	
	return count_records_select("dialogue_conversations", "dialogueid = $dialogue->id AND 
        (userid = $user->id OR recipientid = $user->id) AND closed = 1");
	}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_count_open($dialogue, $user) {
	
	return count_records_select("dialogue_conversations", "dialogueid = $dialogue->id AND 
        (userid = $user->id OR recipientid = $user->id) AND closed = 0");
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_count_needing_replies_other($dialogue, $user) {
// count [conversations] needing replies [from] other [person]	
	return count_records_select("dialogue_conversations", "dialogueid = $dialogue->id AND 
        (userid = $user->id OR recipientid = $user->id) AND lastid = $user->id AND closed = 0");
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_count_needing_replies_self($dialogue, $user) {
// count [conversations] needing replies [from] self
	
	return count_records_select("dialogue_conversations", "dialogueid = $dialogue->id AND 
        (userid = $user->id OR recipientid = $user->id) AND lastid != $user->id AND closed = 0");
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_participants($dialogueid) {
//Returns the users with data in one dialogue
//(users with records in dialogue_conversations, creators and receivers)

    global $CFG;

    //Get conversation's creators
    $creators = get_records_sql("SELECT DISTINCT u.*
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}dialogue_conversations c
                                WHERE c.dialogueid = '$dialogueid' and
                                      u.id = c.userid");

    //Get conversation's receivers
    $receivers = get_records_sql("SELECT DISTINCT u.*
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}dialogue_conversations c
                                WHERE c.dialogueid = '$dialogueid' and
                                      u.id = c.recipientid");

    //Add receivers to creators
    if ($receivers) {
        foreach ($receivers as $receiver) {
            $creators[$receiver->id] = $receiver;
        }
    }

    //Return creators array (it contains an array of unique users, creators and receivers)
    return ($creators);
} 

//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_available_users($dialogue) {

    if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
    }
    switch ($dialogue->dialoguetype) {
        case 0 : // teacher to student
            if (isteacher($course->id)) {
                return dialogue_get_available_students($dialogue);
            }
            else {
                return dialogue_get_available_teachers($dialogue);
            }
        case 1: // student to student
            if (isstudent($course->id)) {
                return dialogue_get_available_students($dialogue);
            }
            else {
                return;
            }
        case 2: // everyone
            if ($teachers = dialogue_get_available_teachers($dialogue)) {
                foreach ($teachers as $userid=>$name) {
                    $names[$userid] = $name;
                }
                $names[-1] = "-------------";
            }
            if ($students = dialogue_get_available_students($dialogue)) {
                foreach ($students as $userid=>$name) {
                    $names[$userid] = $name;
                }
            }
            if (isset($names)) {
                return $names;
            }
            return;
    }
}

                    
//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_available_students($dialogue) {
global $USER;
   	
    if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
    }
    // add current group before list of students if it's the teacher
    if (isteacher($course->id) and groupmode($course)) {
        // show teacher their current group
        $groupid = get_current_group($course->id);
        if ($groupid) {
            if (!$group = get_record("groups", "id", $groupid)) {
                error("Dialogue get available students: group not found");
            }
            $gnames["g$groupid"] = $group->name;
        } else { // all participants
            $gnames["g0"] = get_string("allparticipants");
        }
        $gnames["spacer"] = "------------";
    }
    // get the students on this course (default sort order)...
	if ($users = get_course_students($course->id)) {
		foreach ($users as $otheruser) {
			// ...exclude self and...
			if ($USER->id != $otheruser->id) {
                // if teacher and groups then exclude students not in the current group
                if (isteacher($course->id) and groupmode($course) and $groupid) {
                    if (!ismember($groupid, $otheruser->id)) {
                        continue;
                    }
                }
				// ...any already in any open conversations unless multiple conversations allowed
				if ($dialogue->multipleconversations or count_records_select("dialogue_conversations", 
                        "dialogueid = $dialogue->id AND 
                        ((userid = $USER->id AND recipientid = $otheruser->id) OR 
                        (userid = $otheruser->id AND recipientid = $USER->id)) AND closed = 0") == 0) {
					$names[$otheruser->id] = fullname($otheruser);
				}
			}
		}
	}
    if (isset($gnames)) {
        $list = $gnames;
    }
    if (isset($names)) {
        natcasesort($names);
        if (isset($list)) {
            $list += $names;
        } else {
            $list = $names;
        }
    }
    if (isset($list)) {
        return $list;
    } else {
        return;
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_available_teachers($dialogue) {
global $USER;
   	
    if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
        }
    // get the teachers on this course (default sort order)...
	if ($users = get_course_teachers($course->id)) {
		// $names[0] = "-----------------------";
		foreach ($users as $otheruser) {
            // ...exclude self and ...
			if ($USER->id != $otheruser->id) {
                // ...any already in open conversations unless multiple conversations allowed 
				if ($dialogue->multipleconversations or count_records_select("dialogue_conversations", 
                        "dialogueid = $dialogue->id AND ((userid = $USER->id AND 
                        recipientid = $otheruser->id) OR (userid = $otheruser->id AND 
                        recipientid = $USER->id)) AND closed = 0") == 0) {
					$names[$otheruser->id] = fullname($otheruser);
				}
			}
		}
	}
	if (isset($names)) {
        natcasesort($names);
		return $names;
	}
	return;
}



//////////////////////////////////////////////////////////////////////////////////////
function dialogue_get_users_done($dialogue) {
    global $CFG;
    return get_records_sql("SELECT u.* 
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_students s, 
                                   {$CFG->prefix}user_teachers t, 
                                   {$CFG->prefix}dialogue_entries j
                             WHERE ((s.course = '$dialogue->course' AND s.userid = u.id) 
                                OR  (t.course = '$dialogue->course' AND t.userid = u.id))
                               AND u.id = j.userid 
                               AND j.dialogue = '$dialogue->id'
                          ORDER BY j.modified DESC");
}


// OTHER dialogue FUNCTIONS ///////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
function dialogue_list_conversations_closed($dialogue) {
// list the closed for the current user
    global $USER;
	
	if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
	
    if ($conversations = dialogue_get_conversations($dialogue, $USER, "closed = 1")) {
        // reorder the conversations by (other) name
        foreach ($conversations as $conversation) {
			if ($USER->id != $conversation->userid) {
				if (!$with = get_record("user", "id", $conversation->userid)) {
					error("User's record not found");
				}
			}
			else {
				if (!$with = get_record("user", "id", $conversation->recipientid)) {
					error("User's record not found");
				}
			}
            $names[$conversation->id] = fullname($with);
        }
        natcasesort($names);
        
		print_simple_box_start();
		$table->head = array (get_string("dialoguewith", "dialogue"), get_string("subject", "dialogue"),  
            get_string("numberofentries", "dialogue"), get_string("lastentry", "dialogue"), 
            get_string("status", "dialogue"));
		$table->width = "100%";
		$table->align = array ("left", "left", "center", "left", "left");
		$table->size = array ("*", "*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;

		foreach ($names as $cid=>$name) {
            if (!$conversation = get_record("dialogue_conversations", "id", $cid)) {
                error("Closed conversations: could not find conversation record");
            }
		    $total = dialogue_count_entries($dialogue, $conversation);
			$byuser = dialogue_count_entries($dialogue, $conversation, $USER);
			if ($conversation->closed) {
				$status = get_string("closed", "dialogue");
			} else {
				$status = get_string("open", "dialogue");
			}
			$table->data[] = array("<a href=\"dialogues.php?id=$cm->id&action=showdialogues&cid=$conversation->id\">".
				"$name</a>", $conversation->subject, $byuser." ".get_string("of", "dialogue")." ".$total,
				userdate($conversation->timemodified), $status);
			}
		print_table($table);
	    print_simple_box_end();
	} 
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_list_conversations_other($dialogue) {
// list the conversations of the current user awaiting response from the other person
    global $THEME, $USER;
	
	if (!$course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
    }
    if (!$cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
	
    $timenow = time();
    if ($conversations = dialogue_get_conversations($dialogue, $USER, "lastid = $USER->id AND closed = 0")) {
        // reorder the conversations by (other) name
        foreach ($conversations as $conversation) {
			if ($USER->id != $conversation->userid) {
				if (!$with = get_record("user", "id", $conversation->userid)) {
					error("User's record not found");
				}
			}
			else {
				if (!$with = get_record("user", "id", $conversation->recipientid)) {
					error("User's record not found");
				}
			}
            $names[$conversation->id] = fullname($with);
        }
        natcasesort($names);
        
		print_simple_box_start();
		$table->head = array (get_string("dialoguewith", "dialogue"), get_string("subject", "dialogue"),  
            get_string("numberofentries", "dialogue"), get_string("lastentry", "dialogue"), 
            get_string("status", "dialogue"));
		$table->width = "100%";
		$table->align = array ("left", "left", "center", "left", "left");
		$table->size = array ("*", "*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;

		foreach ($names as $cid=>$name) {
            if (!$conversation = get_record("dialogue_conversations", "id", $cid)) {
                error("Closed conversations: could not find conversation record");
            }
		    $total = dialogue_count_entries($dialogue, $conversation);
			$byuser = dialogue_count_entries($dialogue, $conversation, $USER);
			if ($conversation->seenon) {
				$status = get_string("seen", "dialogue", format_time($timenow - $conversation->seenon));
			} else {
				$status = get_string("notyetseen", "dialogue");
			}
			$table->data[] = array("<a href=\"dialogues.php?id=$cm->id&action=printdialogue&cid=$conversation->id\">".
				"$name</a>", $conversation->subject, $byuser." ".get_string("of", "dialogue")." ".$total,
				userdate($conversation->timemodified), $status);
			}
		print_table($table);
	    print_simple_box_end();
	} 
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_list_conversations_self($dialogue) {
// list open conversations of the current user awaiting their reply
    global $THEME, $USER;
	
	if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    // set up some general variables
    $usehtmleditor = can_use_html_editor();

	$timenow = time();
	$showbutton = false;
	
	echo "<form name=\"replies\" method=\"post\" action=\"dialogues.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"insertentries\">\n";
	echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
	echo "<input type=\"hidden\" name=\"pane\" value=\"1\">\n";

	// list the conversations requiring a resonse from this user in full
	if ($conversations = dialogue_get_conversations($dialogue, $USER, "lastid != $USER->id AND closed = 0")) {
		$showbutton = true;
		print_simple_box_start("center");
		foreach ($conversations as $conversation) {
            // set seenon if required
            if (!$conversation->seenon) {
                if (!set_field("dialogue_conversations", "seenon", $timenow, "id", $conversation->id)) {
                    error("List conversations self: could not set seenon");
                }
            }
			echo "<center><table border=\"1\" cellspacing=\"0\" valign=\"top\" cellpadding=\"4\" 
                width=\"100%\">\n";
			echo "<tr><TD BGCOLOR=\"$THEME->cellheading2\" valign=\"top\">\n";
			if ($conversation->userid == $USER->id) {
				if (!$otheruser = get_record("user", "id", $conversation->recipientid)) {
					error("User not found");
				}
			}
			else {
				if (!$otheruser = get_record("user", "id", $conversation->userid)) {
					error("User not found");
				}
			}
			// print_user_picture($user->id, $course->id, $user->picture);
			echo "<b>".get_string("dialoguewith", "dialogue", "$otheruser->firstname $otheruser->lastname").
                "</b></td>";
			echo "<td bgcolor=\"$THEME->cellheading2\"><i>$conversation->subject&nbsp;</i><br />\n";
            echo "<div align=\"right\">\n";
			if (!$conversation->subject) {
                // conversation does not have a subject, show add subject link
                echo "<a href=\"dialogues.php?action=getsubject&id=$cm->id&cid=$conversation->id&pane=2\">".
	    			get_string("addsubject", "dialogue")."</a>\n";
		    	helpbutton("addsubject", get_string("addsubject", "dialogue"), "dialogue");
                echo "&nbsp; | ";
            }
    		if (dialogue_count_entries($dialogue, $conversation)) {
				echo "<a href=\"dialogues.php?action=confirmclose&id=$cm->id&cid=$conversation->id&pane=1\">".
					get_string("close", "dialogue")."</a>\n";
				helpbutton("closedialogue", get_string("close", "dialogue"), "dialogue");
			}
			else {
				echo "&nbsp;";
			}
			echo "<div></td></tr>";
		
			if ($entries = get_records_select("dialogue_entries", "conversationid = $conversation->id", 
                    "id")) {
				foreach ($entries as $entry) {
					if ($entry->userid == $USER->id) {
						echo "<tr><td colspan=\"2\" bgcolor=\"#FFFFFF\">\n";
						echo text_to_html("<font size=\"1\">".get_string("onyouwrote", "dialogue", 
                            userdate($entry->timecreated)).":</font><br />".$entry->text);
						echo "</td></tr>\n";
					}
					else {
						echo "<tr><td colspan=\"2\" bgcolor=\"$THEME->body\">\n";
						echo text_to_html("<font size=\"1\">".get_string("onwrote", "dialogue", 
                               userdate($entry->timecreated)." ".$otheruser->firstname).
                               ":</font><br />".$entry->text);
						echo "</td></tr>\n";
					}
				}
			}
		
			echo "<tr><td colspan=\"2\" align=\"center\" valign=\"top\">\n";
			if ($entries) {
				echo "<i>".get_string("typereply", "dialogue")."</i>\n";
			}
			else {
				echo "<i>".get_string("typefirstentry", "dialogue")."</i>\n";
			}
			echo "</td></tr>\n";
			echo "<tr><td valign=\"top\" align=\"right\">\n";
			helpbutton("writing", get_string("helpwriting"), "dialogue", true, true);
			echo "<br />";
			echo "</td><td>\n";
			// use a cumbersome name on the textarea is just historical :-) 
            print_textarea($usehtmleditor, 20, 75, 630, 300, "reply$conversation->id");
			echo "</td></tr>";
			echo "</table></center><br />\n";
		}
		print_simple_box_end();
        use_html_editor();
    	if ($showbutton) {
	    	echo "<hr />\n";
		    echo "<br /><input type=\"submit\" value=\"".get_string("addmynewentries", "dialogue")."\">\n";
	    }
	    echo "</form>\n";
	}
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_print_conversation($dialogue, $conversation) {
// print a conversation and allow a new entry
    global $THEME, $USER;
	
	if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
	
	$timenow = time();
	$showbutton = false;
	
	echo "<form name=\"replies\" method=\"post\" action=\"dialogues.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"insertentries\">\n";
	echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
	echo "<input type=\"hidden\" name=\"pane\" value=\"2\">\n";

    $showbutton = true;
    print_simple_box_start("center", "", $THEME->cellcontent2);
    echo "<center><table border=\"1\" cellspacing=\"0\" valign=\"top\" cellpadding=\"4\" 
        width=\"100%\">\n";
    echo "<tr><td bgcolor=\"$THEME->cellheading2\" valign=\"top\">\n";
    if ($conversation->userid == $USER->id) {
        if (!$otheruser = get_record("user", "id", $conversation->recipientid)) {
            error("User not found");
        }
    }
    else {
        if (!$otheruser = get_record("user", "id", $conversation->userid)) {
            error("User not found");
        }
    }
    // print_user_picture($user->id, $course->id, $user->picture);
    echo "<b>".get_string("dialoguewith", "dialogue", "$otheruser->firstname $otheruser->lastname").
        "</b></td>";
    echo "<td bgcolor=\"$THEME->cellheading2\"><i>$conversation->subject&nbsp;</i><br />\n";
    echo "<div align=\"right\">\n";
    if (!$conversation->subject) {
        // conversation does not have a subject, show add subject link
        echo "<a href=\"dialogues.php?action=getsubject&id=$cm->id&cid=$conversation->id&pane=2\">".
            get_string("addsubject", "dialogue")."</a>\n";
        helpbutton("addsubject", get_string("addsubject", "dialogue"), "dialogue");
        echo "&nbsp; | ";
    }
    echo "<a href=\"dialogues.php?action=confirmclose&id=$cm->id&cid=$conversation->id&pane=2\">".
        get_string("close", "dialogue")."</a>\n";
    helpbutton("closedialogue", get_string("close", "dialogue"), "dialogue");
    echo "</div></td></tr>\n";

    if ($entries = get_records_select("dialogue_entries", "conversationid = $conversation->id", "id")) {
        foreach ($entries as $entry) {
            if ($entry->userid == $USER->id) {
                echo "<tr><td colspan=\"2\" bgcolor=\"#FFFFFF\">\n";
                echo text_to_html("<font size=\"1\">".get_string("onyouwrote", "dialogue", 
                            userdate($entry->timecreated)).":</font><br />".$entry->text);
            }
            else {
                echo "<tr><td colspan=\"2\" bgcolor=\"$THEME->body\">\n";
                echo text_to_html("<font size=\"1\">".get_string("onwrote", "dialogue", 
                            userdate($entry->timecreated)." ".$otheruser->firstname).":</font><br />".
                        $entry->text);
            }
        }
        echo "</td></tr>\n";
    }
    echo "<tr><td colspan=\"2\" align=\"center\" valign=\"top\"><i>".
        get_string("typefollowup", "dialogue")."</i></td></tr>\n";
    echo "<tr><td valign=\"top\" align=\"right\">\n";
    helpbutton("writing", get_string("helpwriting"), "dialogue", true, true);
    echo "<br />";
    echo "</td><td>\n";
    // use a cumbersome name on the textarea for historical reasons :-) 
    $usehtmleditor = can_use_html_editor();
    print_textarea($usehtmleditor, 20, 75, 630, 300, "reply$conversation->id");
    use_html_editor();
    echo "</td></tr>";
    echo "</table></center><br />\n";
	print_simple_box_end();
	if ($showbutton) {
		echo "<hr />\n";
		echo "<br /><input type=\"submit\" value=\"".get_string("addmynewentry", "dialogue")."\">\n";
	}
	echo "</form>\n";
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_print_tabbed_heading($tabs) {
// Prints a tabbed heading where one of the tabs highlighted.
// $tabs is an object with several properties.
// 		$tabs->names      is an array of tab names
//		$tabs->urls       is an array of links
// 		$tabs->align     is an array of column alignments (defaults to "center")
// 		$tabs->size      is an array of column sizes
// 		$tabs->wrap      is an array of "nowrap"s or nothing
// 		$tabs->highlight    is an index (zero based) of "active" heading .
// 		$tabs->width     is an percentage of the page (defualts to 80%)
// 		$tabs->cellpadding    padding on each cell (defaults to 5)

	global $CFG, $THEME;
	
    if (isset($tabs->names)) {
        foreach ($tabs->names as $key => $name) {
            if (!empty($tabs->urls[$key])) {
				$url =$tabs->urls[$key];
				if ($tabs->highlight == $key) {
					$tabcontents[$key] = "<b>$name</b>";
				} else {
					$tabcontents[$key] = "<a class= \"dimmed\" href=\"$url\"><b>$name</b></a>";
				}
            } else {
                $tabcontents[$key] = "<b>$name</b>";
            }
        }
    }

    if (empty($tabs->width)) {
        $tabs->width = "80%";
    }

    if (empty($tabs->cellpadding)) {
        $tabs->cellpadding = "5";
    }

    // print_simple_box_start("center", "$table->width", "#ffffff", 0);
    echo "<table width=\"$tabs->width\" border=\"0\" valign=\"top\" align=\"center\" ";
    echo " cellpadding=\"$tabs->cellpadding\" cellspacing=\"0\" class=\"generaltable\">\n";

    if (!empty($tabs->names)) {
        echo "<tr>";
		echo "<td  class=\"generaltablecell\">".
			"<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td>\n";
        foreach ($tabcontents as $key => $tab) {
            if (isset($align[$key])) {
				$alignment = "align=\"$align[$key]\"";
			} else {
                $alignment = "align=\"center\"";
            }
            if (isset($size[$key])) {
                $width = "width=\"$size[$key]\"";
            } else {
				$width = "";
			}
            if (isset($wrap[$key])) {
				$wrapping = "no wrap";
			} else {
                $wrapping = "";
            }
			if ($key == $tabs->highlight) {
				echo "<td valign=top class=\"generaltabselected\" $alignment $width $wrapping bgcolor=\"$THEME->cellheading2\">$tab</td>\n";
			} else {
				echo "<td valign=top class=\"generaltab\" $alignment $width $wrapping bgcolor=\"$THEME->cellheading\">$tab</td>\n";
			}
		echo "<td  class=\"generaltablecell\">".
			"<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td>\n";
        }
        echo "</tr>\n";
    } else {
		echo "<tr><td>No names specified</td></tr>\n";
	}
	// bottom stripe
	$ncells = count($tabs->names)*2 +1;
	$height = 2;
	echo "<tr><td colspan=\"$ncells\" bgcolor=\"$THEME->cellheading2\">".
		"<img height=\"$height\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td></tr>\n";
    echo "</table>\n";
	// print_simple_box_end();

    return true;
}



//////////////////////////////////////////////////////////////////////////////////////
function dialogue_show_conversation($dialogue, $conversation) {
    global $THEME, $USER;
	
	if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
	
	$timenow = time();
	print_simple_box_start("center");
	echo "<center><TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=4 width=\"100%\">\n";
		
	echo "<tr>";
	echo "<td bgcolor=\"$THEME->cellheading2\" valign=\"top\">\n";
	if ($conversation->userid == $USER->id) {
		if (!$otheruser = get_record("user", "id", $conversation->recipientid)) {
			error("User not found");
		}
	}
	else {
		if (!$otheruser = get_record("user", "id", $conversation->userid)) {
			error("User not found");
		}
	}
	// print_user_picture($user->id, $course->id, $user->picture);
	echo "<b>".get_string("dialoguewith", "dialogue", "$otheruser->firstname $otheruser->lastname").
        "</b></td>";
	echo "<td bgcolor=\"$THEME->cellheading2\" valign=\"top\"><i>$conversation->subject&nbsp;</i></td></tr>";

	if ($entries = get_records_select("dialogue_entries", "conversationid = $conversation->id", "id")) {
		foreach ($entries as $entry) {
			if ($entry->userid == $USER->id) {
				echo "<tr><td  colspan=\"2\" bgcolor=\"#FFFFFF\">\n";
				echo text_to_html("<font size=\"1\">".get_string("onyouwrote", "dialogue", 
                    userdate($entry->timecreated)).
					":</font><br />".$entry->text);
				echo "</td></tr>\n";
			}
			else {
				echo "<tr><td  colspan=\"2\" bgcolor=\"$THEME->body\">\n";
				echo text_to_html("<font size=\"1\">".get_string("onwrote", "dialogue", 
                    userdate($entry->timecreated)." ".$otheruser->firstname).":</font><br />".$entry->text);
				echo "</td></tr>\n";
			}
		}
	}
	echo "</TABLE></center><BR CLEAR=ALL>\n";
	print_simple_box_end();
	print_continue("view.php?id=$cm->id&pane=3");
}


//////////////////////////////////////////////////////////////////////////////////////
function dialogue_show_other_conversations($dialogue, $conversation) {
// prints the other CLOSED conversations for this pair of users
    global $THEME;
	
	if (! $course = get_record("course", "id", $dialogue->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
	
	if (!$user = get_record("user", "id", $conversation->userid)) {
		error("User not found");
		}
	if (!$otheruser = get_record("user", "id", $conversation->recipientid)) {
		error("User not found");
		}
	
	if ($conversations = get_records_select("dialogue_conversations", "dialogueid = $dialogue->id AND 
			((userid = $user->id AND recipientid = $otheruser->id) OR (userid = $otheruser->id AND 
            recipientid = $user->id)) AND closed = 1", "timemodified DESC")) {
		if (count($conversations) > 1) {
			$timenow = time();
			foreach ($conversations as $otherconversation) {
				if ($conversation->id != $otherconversation->id) {
					// for this conversation work out which is the other user
                    if ($otherconversation->userid == $user->id) {
                        if (!$otheruser = get_record("user", "id", $otherconversation->recipientid)) {
                            error("Show other conversations: could not get user record");
                        }
                    }
                    else {
                        if (!$otheruser = get_record("user", "id", $otherconversation->userid)) {
                            error("Show other conversations: could not get user record");
                        }
                    } 
                    print_simple_box_start("center");
					echo "<center><TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=4 width=\"100%\">\n";
						
					echo "<TR>";
					echo "<TD BGCOLOR=\"$THEME->cellheading2\" VALIGN=TOP>\n";
				    // print_user_picture($otheruser->id, $course->id, $otheruser->picture);
				    echo "<b>".get_string("dialoguewith", "dialogue", 
                        "$otheruser->firstname $otheruser->lastname")."</b></td>";
                    echo "<td bgcolor=\"$THEME->cellheading2\" valign=\"top\"><i>$otherconversation->subject&nbsp;</i></td></tr>";
				    if ($entries = get_records_select("dialogue_entries", 
                            "conversationid = $otherconversation->id", "id")) {
						foreach ($entries as $entry) {
							if ($entry->userid == $user->id) {
								echo "<tr><td  colspan=\"2\" bgcolor=\"#FFFFFF\">\n";
								echo text_to_html("<font size=\"1\">".get_string("onyouwrote", "dialogue", 
                                    userdate($entry->timecreated)).":</font><br />".$entry->text);
								echo "</td></tr>\n";
							}
							else {
								echo "<tr><td  colspan=\"2\" bgcolor=\"$THEME->body\">\n";
								echo text_to_html("<font size=\"1\">".get_string("onwrote", "dialogue", 
                                    userdate($entry->timecreated)." ".$otheruser->firstname).
                                    ":</font><br />".$entry->text);
								echo "</td></tr>\n";
							}
						}
					}
				
					echo "</TABLE></center><BR CLEAR=ALL>\n";
					print_simple_box_end();
				}
            }
        	print_continue("view.php?id=$cm->id&pane=3");
        }
  	}
}


?>
