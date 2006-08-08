<?PHP  // $Id: lesson.php, v 1.0 25 Jan 2004

/*************************************************
    ACTIONS handled are:

    addbranchtable
    addendofbranch
    addcluster
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
    
    $id     = required_param('id', PARAM_INT);         // Course Module ID
    $action = required_param('action', PARAM_ALPHA);   // Action
 
    // get some esential stuff...
    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $lesson = get_record("lesson", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);
    
    if ($action != 'continue') {
        // All pages except for continue.php require teacher editing privs
        if (!isteacheredit($lesson->course)) {
            error('You must be a teacher with editing privileges to access this page.');
        }
    }
    
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
    print_header("$course->shortname: ".format_string($lesson->name), "$course->fullname",
                 "$navigation <a href=index.php?id=$course->id>$strlessons</a> -> 
                  <a href=\"view.php?id=$cm->id\">".format_string($lesson->name,true)."</a>", "", "", true);

    if ($action == 'continue' and isteacher($course->id)) {
        $currenttab = 'navigation';
        include('tabs.php');
    }

    // include the appropriate action (check to make sure the file is there first)
    if (file_exists($CFG->dirroot.'/mod/lesson/action/'.$action.'.php')) {
        include($CFG->dirroot.'/mod/lesson/action/'.$action.'.php');    
    } else {
        error("Fatal Error: Unknown action\n");
    }

    print_footer($course);
 
?>
