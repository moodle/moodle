<?PHP  // $Id: lesson.php, v 1.0 25 Jan 2004

/*************************************************
    ACTIONS handled are:

    addbranchtable
    addendofbranch
    addcluster      /// CDC-FLAG /// added two new items
    addendofcluster
    addpage
    confirmdelete
    continue
    delete
    editpage
    insertpage
    move
    moveit
    updatepage

************************************************/

    require("../../config.php");
    require("locallib.php");
    
    $id = required_param('id', PARAM_INT);    // Course Module ID
 
    // get some esential stuff...
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $lesson = get_record("lesson", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);
    
    // set up some general variables
    $usehtmleditor = can_use_html_editor();
    
    $navigation = "";
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strlessons = get_string("modulenameplural", "lesson");
    $strlesson  = get_string("modulename", "lesson");
    $strlessonname = $lesson->name;
    
    // ... print the header and...
    print_header("$course->shortname: $lesson->name", "$course->fullname",
                 "$navigation <a href=index.php?id=$course->id>$strlessons</a> -> 
                  <a href=\"view.php?id=$cm->id\">$lesson->name</a>", 
                  "", "<style type=\"text/css\">@import url($CFG->wwwroot/mod/lesson/styles.php);</style>", true);

    //...get the action 
    $action = required_param('action');

	// include the appropriate action (check to make sure the file is there first)
	if (file_exists($CFG->dirroot.'/mod/lesson/action/'.$action.'.php')) {
		include($CFG->dirroot.'/mod/lesson/action/'.$action.'.php');    
	} else {
		error("Fatal Error: Unknown action\n");
	}
	
    print_footer($course);
 
?>
