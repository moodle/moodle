<?PHP // $Id$
	require("../../config.php");
	require("surveylib.php");

	require_login();

	print_header("Edit my surveys", "Edit my surveys", "Edit my surveys", "");

	if ($edit == "new") {
		include("edit_new.phtml");
		print_footer($course);
		die;
	} 

	if ($edit == "release") {
		release_survey($id);
		unset($edit);
	}

	if ($edit == "update") {
		if (match_referer() && isset($HTTP_POST_VARS)) {
			$survey = (object)$HTTP_POST_VARS;
			validate_form($survey, $err);
			if (count($err) == 0) {
				update_survey($survey);
				notify("The survey \"$survey->name\" was updated.");
				unset($edit);
			}
		} else {
			notify("Error: This page was called wrongly.");
		}
	}

	if ($edit == "delete") {
		if ($id) {

			if (get_responses_for_survey($id) > 0) {
				notify("Could not delete survey as it has responses");

			} else if ($ss = $db->Execute("DELETE FROM surveys WHERE owner = $USER->id AND id = $id")) {
				notify("The survey was deleted.");

			} else {
				notify("Serious error: could not find any templates.");
			}

		} else {
			notify("Serious error: could not find any templates.");
		}
		unset($edit);
	} 


	if ($edit == "add") {
		if ($template) {
			if ($tt = $db->Execute("SELECT * FROM surveys WHERE id = $template")) {
				$survey = (object)$tt->fields;
				$survey->owner = $USER->id;
                $survey->name = "";
				$survey->template = $template;
				add_survey($survey);
			} else {
				notify("Serious error: could not find template $template");
			}
		} else {
			unset($edit);
		}
	}

	if ($edit == "edit") {
		if ($id) {
			$survey = get_survey($id);
		} else {
			notify("Error: script called badly.");
			die;
		}
	}
	
	if ($edit) {
	    $survey->status = get_survey_status($survey);
		include("edit_form.phtml");

	} else {
        clean_up_surveys();
	    print_list_of_my_surveys();
        print_additional_commands();
	}
	print_footer($course);




/// FUNCTIONS //////////////////

function print_additional_commands() {
    echo "<HR><P ALIGN=center><A HREF=\"edit.php?edit=new\">Add a new survey</A></P>";
}

function validate_form(&$survey, &$err) {

	if (empty($survey->id)) {
		notify("A serious error occurred.");
		die;
	}

	if (empty($survey->name))
		$err["name"] = "Missing name";

	if (empty($survey->password))
		$err["password"] = "Missing password";

	else if ($survey->password == "changeme")
		$err["password"] = "You should change this password";

    settype($survey->days, "integer");

	if ($survey->days < 0 || $survey->days > 365)
		$err["days"] = "Must be a number between 0 and 365";


}

function add_survey(&$survey) {

	global $db, $USER;

	$timenow = time();

	$survey->intro = addslashes($survey->intro);     // to make sure

	$rs = $db->Execute("INSERT INTO surveys SET
						timecreated = $timenow,
						timemodified = $timenow,
						template  = '$survey->template',
						course    = '$survey->course', 
						owner     = '$survey->owner', 
						name      = '$survey->name', 
						password  = '$survey->password',
						intro     = '$survey->intro', 
						url       = '$survey->url', 
						questions = '$survey->questions' ");
        
	if (!$rs) {
		notify("Could not insert a record");
		die;
	}

//  Now get it out again (most compatible way to get the id)

	$rs = $db->Execute("SELECT * FROM surveys WHERE owner = $survey->owner AND timecreated = $timenow");
	if ($rs) {
		$survey = (object) $rs->fields;
		$survey->intro = stripslashes($survey->intro);
	} else {
		notify("Error: Could not find the record I just inserted!");
		die;
	}
}

function release_survey($id) {

	global $db;

	if ($ss = $db->Execute("SELECT * FROM surveys WHERE id = $id")) {
		$survey = (object)$ss->fields;
	} else {
		notify("Serious error: could not find survey $id");
		die;
	}

	$timenow = time();
	$timeend = $timenow + ($survey->days * 86400);

	if ($ss = $db->Execute("UPDATE surveys SET locked=1, timeopen = $timenow, timeclose = $timeend 
										   WHERE id = $survey->id")) {
		notify("The survey \"$survey->name\" was released and can no longer be edited.");
	} else {
		notify("An error occurred while releasing \"$survey->name\"");
	}
}


function update_survey($survey) {

	global $db, $USER;

	$timenow = time();

	$rs = $db->Execute("UPDATE surveys SET
							timemodified = $timenow,
							name      = '$survey->name', 
							password  = '$survey->password',
							intro     = '$survey->intro', 
							url       = '$survey->url',
							days      =  $survey->days
						WHERE
							id        =  $survey->id AND
							owner     =  $USER->id ");
        
	if ($rs) {
		return true;
	} else {
		notify("Could not update the survey!");
		die;
	}
}

function get_survey($id) {
	global $db, $USER;

	if ($ss = $db->Execute("SELECT * FROM surveys WHERE id = $id AND owner = $USER->id")) {
		$survey = (object)$ss->fields;
		$survey->intro = stripslashes($survey->intro);
		$survey->name = stripslashes($survey->name);
		$survey->password = stripslashes($survey->password);
		return $survey;
	} else {
		notify("Serious error: could not find specified survey.");
		die;
	}
}


function make_survey_menu($chosenid) {
	global $db;

	$chosenname = get_template_name($chosenid);
	if ($ss = $db->Execute("SELECT name,id FROM surveys WHERE owner = 0 ORDER BY id")) {
		print $ss->GetMenu("template", $chosenname, true);
	} else {
		notify("Serious error: could not find any templates.");
	}
}

function clean_up_surveys() {
	global $db, $USER;

    if (!$rs = $db->Execute("DELETE FROM surveys WHERE owner = $USER->id AND name = ''")) {
		notify("Error: could not clean up surveys");
    }
}
 

function print_list_of_my_surveys() {
	global $db, $USER;
	
	if ($rs = $db->Execute("SELECT * FROM surveys WHERE owner = $USER->id ORDER BY id")) {
		if ($rs->RowCount()) {
			echo "<H3 ALIGN=center>Existing surveys</H3>";
			echo "<TABLE align=center cellpadding=6>";
			echo "<TR><TD><B><P>Survey name<TD><B><P>Survey type<TD><B><P>Details<TD><B><P>Status<TD><B><P></TR>";
			while (!$rs->EOF) {
				$survey = (object)$rs->fields;

				$numresponses = get_responses_for_survey($survey->id);
				$templatename = get_template_name($survey->template);
				$status = get_survey_status($survey);

				echo "<TR bgcolor=#f0f0f0>";
				echo "<TD><P><B><A HREF=\"view.php?id=$survey->id\">$survey->name</A>";
				echo "<TD><P>$templatename";
				if ($status == "editing") {
					echo "<TD><P><B><A HREF=\"edit.php?edit=edit&id=$survey->id\">Edit</A>";
					echo "<TD><P><B><FONT COLOR=#990000>$status";
					echo "<TD><P><A HREF=\"edit.php?edit=release&id=$survey->id\">Release to students</A>";
				} else {
					echo "<TD><P><B><A HREF=\"edit.php?edit=edit&id=$survey->id\">View</A>";
					echo "<TD><P><B><FONT COLOR=#009900>$status";
					echo "<TD><P><A HREF=\"report.php?id=$survey->id\">$numresponses responses</A>";
				}
				echo "</TR>";
				$rs->MoveNext();
			}
			echo "</TABLE>";
		} else {
			echo "<H3 align=center>You don't have any surveys yet</H3>";
		}
	} else {
		notify("Error: could not list surveys");
	}

}

?>
