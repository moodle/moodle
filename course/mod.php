<?PHP // $Id$

//  Moves, adds, updates or deletes modules in a course

    require("../config.php");
    require("lib.php");

    require_login();

    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $mod = $SESSION->modform;
        unset($SESSION->modform);
    } else {
        $mod = (object)$_POST;
    }

    if (isset($cancel)) {  
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course");
        }
    }


    if (isset($_POST["course"])) {    // add or update form submitted

        if (!$course = get_record("course", "id", $mod->course)) {
            error("This course doesn't exist");
        }

        if (!isteacheredit($course->id)) {
            error("You can't modify this course!");
        }

        $modlib = "$CFG->dirroot/mod/$mod->modulename/lib.php";
        if (file_exists($modlib)) {
            include_once($modlib);
        } else {
            error("This module is missing important code! ($modlib)");
        }
        $addinstancefunction    = $mod->modulename."_add_instance";
        $updateinstancefunction = $mod->modulename."_update_instance";
        $deleteinstancefunction = $mod->modulename."_delete_instance";
        $moderr = "$CFG->dirroot/mod/$mod->modulename/moderr.html";

        switch ($mod->mode) {
            case "update":
                $return = $updateinstancefunction($mod);
                if (!$return) {
                    if (file_exists($moderr)) {
                        $form = $mod;
                        include_once($moderr);
                        die;
                    }
                    error("Could not update the $mod->modulename", "view.php?id=$course->id");
                }
                if (is_string($return)) {
                    error($return, "view.php?id=$course->id");
                }
                add_to_log($course->id, "course", "update mod", 
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule", 
                           "$mod->modulename $mod->instance"); 
                add_to_log($course->id, $mod->modulename, "update", 
                           "view.php?id=$mod->coursemodule", 
                           "$mod->instance", $mod->coursemodule); 
                break;

            case "add":
                $return = $addinstancefunction($mod);
                if (!$return) {
                    if (file_exists($moderr)) {
                        $form = $mod;
                        include_once($moderr);
                        die;
                    }
                    error("Could not add a new instance of $mod->modulename", "view.php?id=$course->id");
                }
                if (is_string($return)) {
                    error($return, "view.php?id=$course->id");
                }

                $mod->groupmode = $course->groupmode;  /// Default groupmode the same as course

                $mod->instance = $return;
                // course_modules and course_sections each contain a reference 
                // to each other, so we have to update one of them twice.

                if (! $mod->coursemodule = add_course_module($mod) ) {
                    error("Could not add a new course module");
                }
                if (! $sectionid = add_mod_to_section($mod) ) {
                    error("Could not add the new course module to that section");
                }
                //We get the section's visible field status
                $visible = get_field("course_sections","visible","id",$sectionid);

                if (! set_field("course_modules", "visible", $visible, "id", $mod->coursemodule)) {
                    error("Could not update the course module with the correct visibility");
                }   

                if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                    error("Could not update the course module with the correct section");
                }   
                add_to_log($course->id, "course", "add mod", 
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule", 
                           "$mod->modulename $mod->instance"); 
                add_to_log($course->id, $mod->modulename, "add", 
                           "view.php?id=$mod->coursemodule", 
                           "$mod->instance", $mod->coursemodule); 
                break;
            case "delete":
                if (! $deleteinstancefunction($mod->instance)) {
                    notify("Could not delete the $mod->modulename (instance)");
                }
                if (! delete_course_module($mod->coursemodule)) {
                    notify("Could not delete the $mod->modulename (coursemodule)");
                }
                if (! delete_mod_from_section($mod->coursemodule, "$mod->section")) {
                    notify("Could not delete the $mod->modulename from that section");
                }
                add_to_log($course->id, "course", "delete mod", 
                           "view.php?id=$mod->course", 
                           "$mod->modulename $mod->instance", $mod->coursemodule); 
                break;
            default:
                error("No mode defined");

        }

        rebuild_course_cache($course->id);

        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        } else {
            redirect("view.php?id=$course->id");
        }
        exit;
    }


    if (isset($_GET['move'])) {  

        require_variable($id);   

        if (! $cm = get_record("course_modules", "id", $id)) {
            error("This course module doesn't exist");
        }

        if (!isteacheredit($cm->course)) {
            error("You can't modify this course!");
        }
    
        move_module($cm, $_GET['move']);

        rebuild_course_cache($cm->course);

        $site = get_site();
        if ($site->id == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course");
        }
        exit;

    } else if (isset($_GET['movetosection']) or isset($_GET['moveto'])) {  
        
        if (! $cm = get_record("course_modules", "id", $USER->activitycopy)) {
            error("The copied course module doesn't exist!");
        }

        if (isset($_GET['movetosection'])) {
            if (! $section = get_record("course_sections", "id", $_GET['movetosection'])) {
                error("This section doesn't exist");
            }
            $beforecm = NULL;

        } else {                      // normal moveto
            if (! $beforecm = get_record("course_modules", "id", $_GET['moveto'])) {
                error("The destination course module doesn't exist");
            }
            if (! $section = get_record("course_sections", "id", $beforecm->section)) {
                error("This section doesn't exist");
            }
        }

        if (!isteacheredit($section->course)) {
            error("You can't modify this course!");
        }

        if (!ismoving($section->course)) {
            error("You need to copy something first!");
        }

        moveto_module($cm, $section, $beforecm);

        unset($USER->activitycopy);
        unset($USER->activitycopycourse);
        unset($USER->activitycopyname);

        rebuild_course_cache($section->course);

        $site = get_site();
        if ($site->id == $section->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$section->course");
        }

    } else if (isset($_GET['indent'])) {  

        require_variable($id);   

        if (! $cm = get_record("course_modules", "id", $id)) {
            error("This course module doesn't exist");
        }

        $cm->indent += $_GET['indent'];

        if ($cm->indent < 0) {
            $cm->indent = 0;
        }

        if (!set_field("course_modules", "indent", $cm->indent, "id", $cm->id)) {
            error("Could not update the indent level on that course module");
        }

        $site = get_site();
        if ($site->id == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course");
        }
        exit;

    } else if (isset($_GET['hide'])) {

        if (! $cm = get_record("course_modules", "id", $_GET['hide'])) {
            error("This course module doesn't exist");
        }

        if (!isteacheredit($cm->course)) {
            error("You can't modify this course!");
        }
   
        hide_course_module($cm->id);

        rebuild_course_cache($cm->course);

        $site = get_site();
        if ($site->id == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course");
        }
        exit;

    } else if (isset($_GET['show'])) {

        if (! $cm = get_record("course_modules", "id", $_GET['show'])) {
            error("This course module doesn't exist");
        }

        if (!isteacheredit($cm->course)) {
            error("You can't modify this course!");
        }

        if (! $section = get_record("course_sections", "id", $cm->section)) {
            error("This module doesn't exist");
        }

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        $site = get_site();

        if ($module->visible and ($section->visible or ($site->id == $cm->course))) {
            show_course_module($cm->id);
            rebuild_course_cache($cm->course);
        }

        if ($site->id == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course");
        }
        exit;

    } else if (isset($_GET['groupmode'])) {

        if (! $cm = get_record("course_modules", "id", $_GET['id'])) {
            error("This course module doesn't exist");
        }

        if (!isteacheredit($cm->course)) {
            error("You can't modify this course!");
        }
   
        set_groupmode_for_module($cm->id, $_GET['groupmode']);

        rebuild_course_cache($cm->course);

        $site = get_site();
        if ($site->id == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course");
        }
        exit;

    } else if (isset($_GET['copy'])) { // value = course module

        if (! $cm = get_record("course_modules", "id", $_GET['copy'])) {
            error("This course module doesn't exist");
        }

        if (!isteacheredit($cm->course)) {
            error("You can't modify this course!");
        }

        if (! $section = get_record("course_sections", "id", $cm->section)) {
            error("This module doesn't exist");
        }

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $instance = get_record($module->name, "id", $cm->instance)) {
            error("Could not find the instance of this module");
        }

        $USER->activitycopy = $copy;
        $USER->activitycopycourse = $cm->course;
        $USER->activitycopyname = $instance->name;

        redirect("view.php?id=$cm->course");

    } else if (isset($_GET['cancelcopy'])) { // value = course module

        $courseid = $USER->activitycopycourse;

        unset($USER->activitycopy);
        unset($USER->activitycopycourse);
        unset($USER->activitycopyname);

        redirect("view.php?id=$courseid");

    } else if (isset($_GET['delete'])) {   // value = course module

        if (! $cm = get_record("course_modules", "id", $_GET['delete'])) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        if (!isteacheredit($course->id)) {
            error("You can't modify this course!");
        }

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $instance = get_record($module->name, "id", $cm->instance)) {
            // Delete this module from the course right away
            if (! delete_course_module($cm->id)) {
                notify("Could not delete the $module->name (coursemodule)");
            }
            if (! delete_mod_from_section($cm->id, $cm->section)) {
                notify("Could not delete the $module->name from that section");
            }
            error("The required instance of this module didn't exist.  Module deleted.",
                  "$CFG->wwwroot/course/view.php?id=$course->id");
        }

        $fullmodulename = get_string("modulename", $module->name);

        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;
        $form->course       = $cm->course;
        $form->instance     = $cm->instance;
        $form->modulename   = $module->name;
        $form->fullmodulename  = $fullmodulename;
        $form->instancename = $instance->name;

        $strdeletecheck = get_string("deletecheck", "", "$form->fullmodulename");
        $strdeletecheckfull = get_string("deletecheckfull", "", "$form->fullmodulename '$form->instancename'");

        print_header("$course->shortname: $strdeletecheck", "$course->fullname",
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      $strdeletecheck");

        print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
        print_heading($strdeletecheckfull);
        include_once("mod_delete.html");
        print_simple_box_end();
        print_footer($course);

        exit;


    } else if (isset($_GET['update'])) {   // value = course module

        if (! $cm = get_record("course_modules", "id", $_GET['update'])) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        if (!isteacheredit($course->id)) {
            error("You can't modify this course!");
        }

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $form = get_record($module->name, "id", $cm->instance)) {
            error("The required instance of this module doesn't exist");
        }
        
        if (! $cw = get_record("course_sections", "id", $cm->section)) {
            error("This course section doesn't exist");
        }

        if (isset($return)) {  
            $SESSION->returnpage = "$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id";
        }

        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;     // The section ID
        $form->course       = $course->id;
        $form->module       = $module->id;
        $form->modulename   = $module->name;
        $form->instance     = $cm->instance;
        $form->mode         = "update";

        $sectionname    = get_string("name$course->format");
        $fullmodulename = strtolower(get_string("modulename", $module->name));

        if ($form->section) {
            $heading->what = $fullmodulename;
            $heading->in   = "$sectionname $cw->section";
            $pageheading = get_string("updatingain", "moodle", $heading);
        } else {
            $pageheading = get_string("updatinga", "moodle", $fullmodulename);
        }

        
    } else if (isset($_GET['add'])) {

        if (empty($_GET['add'])) {
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        require_variable($id);
        require_variable($section);

        if (! $course = get_record("course", "id", $id)) {
            error("This course doesn't exist");
        }

        if (! $module = get_record("modules", "name", $_GET['add'])) {
            error("This module type doesn't exist");
        }

        $form->section    = $section;         // The section number itself
        $form->course     = $course->id;
        $form->module     = $module->id;
        $form->modulename = $module->name;
        $form->instance   = "";
        $form->coursemodule = "";
        $form->mode       = "add";

        $sectionname    = get_string("name$course->format");
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section) {
            $heading->what = $fullmodulename;
            $heading->to   = "$sectionname $form->section";
            $pageheading = get_string("addinganewto", "moodle", $heading);
        } else {
            $pageheading = get_string("addinganew", "moodle", $fullmodulename);
        }

    } else {
        error("No action was specfied");
    }

    if (!isteacheredit($course->id)) {
        error("You can't modify this course!");
    }

    $streditinga = get_string("editinga", "moodle", $fullmodulename);
    $strmodulenameplural = get_string("modulenameplural", $module->name);

    if ($module->name == "label") {
        $focuscursor = "";
    } else {
        $focuscursor = "form.name";
    }

    if ($course->category) {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      <A HREF=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</A> -> 
                      $streditinga", $focuscursor, "", false);
    } else {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "$streditinga", $focuscursor, "", false);
    }

    unset($SESSION->modform); // Clear any old ones that may be hanging around.

    $modform = "../mod/$module->name/mod.html";

    if (file_exists($modform)) {

        if ($usehtmleditor = can_use_html_editor()) {
            $defaultformat = FORMAT_HTML;
        } else {
            $defaultformat = FORMAT_MOODLE;
        }

        $icon = "<img align=absmiddle height=16 width=16 src=\"$CFG->modpixpath/$module->name/icon.gif\">&nbsp;";

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        print_simple_box_start("center", "", "$THEME->cellheading");
        include_once($modform);
        print_simple_box_end();

        if ($usehtmleditor and empty($nohtmleditorneeded)) { 
            use_html_editor();
        }

    } else {
        notice("This module cannot be added to this course yet! (No file found at: $modform)", "$CFG->wwwroot/course/view.php?id=$course->id");
    }

    print_footer($course);

?>
