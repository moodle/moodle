<?PHP  // $Id$

/*************************************************
	ACTIONS handled are:

    closeconversation
	confirmclose
    getsubject
	insertentries
	openconversation
    showdialogues
    updatesubject
	
************************************************/

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
        error("Course module dialogue is incorrect");
    }

	require_login($course->id);
	
    $navigation = "";
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strdialogues = get_string("modulenameplural", "dialogue");
    $strdialogue  = get_string("modulename", "dialogue");
    
	// ... print the header and...
    print_header("$course->shortname: $dialogue->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strdialogues</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$dialogue->name</A>", 
                  "", "", true);


	require_variable($action); // need something to do!
	
	/************** close conversation ************************************/
	if ($action == 'closeconversation') {
		if (empty($_GET['cid'])) {
			error("Close dialogue: Missing conversation id");
		}
		else {
			$conversationid = $_GET['cid'];
		}
		if (!set_field("dialogue_conversations", "closed", 1, "id", $conversationid)) {
			error("Close dialogue: unable to set closed");
		}
		if (!set_field("dialogue_conversations", "lastid", $USER->id, "id", $conversationid)) {
			error("Close dialogue: unable to set lastid");
		}
        $pane=$_GET['pane'];

		add_to_log($course->id, "dialogue", "closed", "view.php?id=$cm->id", "$conversationid");
		redirect("view.php?id=$cm->id&pane=$pane", get_string("dialogueclosed", "dialogue"));
	}
	
	
	/****************** confirm close ************************************/
	elseif ($action == 'confirmclose' ) {

		if (empty($_GET['cid'])) {
			error("Confirm Close: conversation id missing");
		}
		if (!$conversation = get_record("dialogue_conversations", "id", $_GET['cid'])) {
			error("Confirm close: cannot get conversation record");
		}
		if ($conversation->userid == $USER->id) {
			if (!$user = get_record("user", "id", $conversation->recipientid)) {
				error("Confirm Close: cannot get recipient record");
			}
		}
		else {
			if (!$user = get_record("user", "id", $conversation->userid)) {
				error("Confirm Close: cannot get user record");
			}
		}
        $pane = $_GET['pane'];
		notice_yesno(get_string("confirmclosure", "dialogue", fullname($user)), 
			 "dialogues.php?action=closeconversation&id=$cm->id&cid=$conversation->id&pane=$pane", 
			 "view.php?id=$cm->id&pane=$pane");
	}
	
	/****************** get subject ************************************/
	elseif ($action == 'getsubject' ) {

		if (empty($_GET['cid'])) {
			error("Confirm Close: conversation id missing");
		}
        print_heading(get_string("addsubject", "dialogue"));
        echo "<form name=\"getsubjectform\" method=\"post\" action=\"dialogues.php\">\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"updatesubject\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\">\n";
        echo "<input type=\"hidden\" name=\"cid\" value=\"$_GET[cid]\">\n";
        echo "<input type=\"hidden\" name=\"pane\" value=\"$_GET[pane]\">\n";
        echo "<center><table border=\"1\" width=\"60%\">\n";
        echo "<tr><td align=\"right\"><b>".get_string("subject", "dialogue")."</b></td>";
        echo "<td><input type=\"text\" size=\"50\" maxsize=\"100\" name=\"subject\" 
                value=\"\"></td></tr>\n";
        echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".
            get_string("addsubject", "dialogue")."\"></td></tr>\n";
        echo "</table></center></form>\n";
	}
	
		
	/****************** insert conversation entries ******************************/
	elseif ($action == 'insertentries' ) {

		$timenow = time();
		$n = 0;
		// get all the open conversations for this user
		if ($conversations = dialogue_get_conversations($dialogue, $USER, "closed = 0")) {
			foreach ($conversations as $conversation) {
				$textarea_name = "reply$conversation->id";
				if (!empty($_POST[$textarea_name])) {
                    unset($item);
					$item->dialogueid = $dialogue->id;
					$item->conversationid = $conversation->id;
					$item->userid = $USER->id;
					$item->timecreated = time(); 
					// set mailed flag if checkbox is not set
					if (empty($_POST['sendthis'])) {
						$item->mailed = 1;
					}
					$item->text = $_POST[$textarea_name];
					if (!$item->id = insert_record("dialogue_entries", $item)) {
						error("Insert Entries: Could not insert dialogue record!");
					}
					if (!set_field("dialogue_conversations", "lastid", $USER->id, "id", $conversation->id)) {
						error("Insert Dialogue Entries: could not set lastid");
					}
					if (!set_field("dialogue_conversations", "timemodified", $timenow, "id", 
                            $conversation->id)) {
						error("Insert Dialogue Entries: could not set lastid");
					}
                    // reset seenon time
					if (!set_field("dialogue_conversations", "seenon", 0, "id", 
                            $conversation->id)) {
						error("Insert Dialogue Entries: could not reset seenon");
					}
					add_to_log($course->id, "dialogue", "add entry", "view.php?id=$cm->id", "$item->id");
					$n++;
				}
			}
		}
		redirect("view.php?id=$cm->id&pane={$_POST['pane']}", get_string("numberofentriesadded", 
                    "dialogue", $n));
	}
	
	/****************** list closed conversations *********************************/
	elseif ($action == 'listclosed') {
	
		echo "<center>\n";
		print_simple_box( text_to_html($dialogue->intro) , "center");
		echo "<br />";
		
		dialogue_list_closed_conversations($dialogue);
	}
		
	/****************** open conversation ************************************/
	elseif ($action == 'openconversation' ) {

		if ($_POST['recipientid'] == 0) {
			redirect("view.php?id=$cm->id", get_string("nopersonchosen", "dialogue"));
        } elseif (empty($_POST['firstentry'])) {
			redirect("view.php?id=$cm->id", get_string("notextentered", "dialogue"));
        } else {
			$conversation->dialogueid = $dialogue->id;
			$conversation->userid = $USER->id;
			$conversation->recipientid = $_POST['recipientid'];
			$conversation->lastid = $USER->id; // this USER is adding an entry too
			$conversation->timemodified = time();
            $conversation->subject = $_POST['subject']; // may be blank
			if (!$conversation->id = insert_record("dialogue_conversations", $conversation)) {
				error("Open dialogue: Could not insert dialogue record!");
			}
			add_to_log($course->id, "dialogue", "open", "view.php?id=$cm->id", "$dialogue->id");
        
            // now add the entry
			$entry->dialogueid = $dialogue->id;
			$entry->conversationid = $conversation->id;
			$entry->userid = $USER->id;
			$entry->timecreated = time(); 
			// set mailed flag if checkbox is not set
			if (empty($_POST['sendthis'])) {
				$entry->mailed = 1;
				}
			$entry->text = $_POST['firstentry'];
			if (!$entry->id = insert_record("dialogue_entries", $entry)) {
				error("Insert Entries: Could not insert dialogue record!");
			}
			add_to_log($course->id, "dialogue", "add entry", "view.php?id=$cm->id", "$entry->id");
			
            if (!$user =  get_record("user", "id", $conversation->recipientid)) {
				error("Open dialogue: user record not found");
            }
			redirect("view.php?id=$cm->id", get_string("dialogueopened", "dialogue", fullname($user) ));
		}
	}
	

    /****************** print dialogue (allowing new entry)********************/
	elseif ($action == 'printdialogue') {
	
		if (!$conversation = get_record("dialogue_conversations", "id", $_GET['cid'])) {
			error("Print Dialogue: can not get conversation record");
		}
			
		echo "<center>\n";
		print_simple_box( text_to_html($dialogue->intro) , "center");
		echo "<br />";
		
		dialogue_print_conversation($dialogue, $conversation);
	}
	

    /****************** show dialogues ****************************************/
	elseif ($action == 'showdialogues') {
	
		if (!$conversation = get_record("dialogue_conversations", "id", $_GET['cid'])) {
			error("Show Dialogue: can not get conversation record");
		}
			
		echo "<center>\n";
		print_simple_box( text_to_html($dialogue->intro) , "center");
		echo "<br />";
		
		dialogue_show_conversation($dialogue, $conversation);
		dialogue_show_other_conversations($dialogue, $conversation);
	}
	

    /****************** update subject ****************************************/
	elseif ($action == 'updatesubject') {
	
		if (!$conversation = get_record("dialogue_conversations", "id", $_POST['cid'])) {
			error("Update Subject: can not get conversation record");
		}
			
        if (!$_POST['subject']) {
            redirect("view.php?id=$cm->id&pane=$_POST[pane]", get_string("nosubject", "dialogue"));
        } elseif (!set_field("dialogue_conversations", "subject", $_POST['subject'], "id", $_POST['cid'])) {
            error("Update subject: could not update conversation record");
        }
        redirect("view.php?id=$cm->id&pane=$_POST[pane]", get_string("subjectadded", "dialogue"));
	}
	

	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

    print_footer($course);

?>
