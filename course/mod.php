<?PHP // $Id$

//  Moves, adds, updates or deletes modules in a course

    require("../config.php");
    require("lib.php");

    if (isset($cancel)) {  
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            save_session("SESSION");
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course");
        }
    } 


    if (isset($course) && isset($HTTP_POST_VARS)) {    // add or update form submitted

        if (isset($SESSION->modform)) {   // Variables are stored in the session
            $mod = $SESSION->modform;
            unset($SESSION->modform);
            save_session("SESSION");
        } else {
            $mod = (object)$HTTP_POST_VARS;
        }

        require_login($mod->course);

        if (!isteacher($mod->course)) {
            error("You can't modify this course!");
        }

        $modlib = "../mod/$mod->modulename/lib.php";
        if (file_exists($modlib)) {
            include($modlib);
        } else {
            error("This module is missing important code! ($modlib)");
        }
        $addinstancefunction    = $mod->modulename."_add_instance";
        $updateinstancefunction = $mod->modulename."_update_instance";
        $deleteinstancefunction = $mod->modulename."_delete_instance";

        switch ($mod->mode) {
            case "update":
                if (! $updateinstancefunction($mod)) {
                    error("Could not update the $mod->modulename");
                }
                add_to_log($mod->course, "course", "update mod", "../mod/$mod->modulename/view.php?id=$mod->coursemodule", "$mod->modulename $mod->instance"); 
                break;

            case "add":
                if (! $mod->instance = $addinstancefunction($mod)) {
                    error("Could not add a new instance of $mod->modulename");
                }
                // course_modules and course_sections each contain a reference 
                // to each other, so we have to update one of them twice.

                if (! $mod->coursemodule = add_course_module($mod) ) {
                    error("Could not add a new course module");
                }
                if (! $sectionid = add_mod_to_section($mod) ) {
                    error("Could not add the new course module to that section");
                }
                if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                    error("Could not update the course module with the correct section");
                }   
                add_to_log($mod->course, "course", "add mod", "../mod/$mod->modulename/view.php?id=$mod->coursemodule", "$mod->modulename $mod->instance"); 
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
                add_to_log($mod->course, "course", "delete mod", "view.php?id=$mod->course", "$mod->modulename $mod->instance"); 
                break;
            default:
                error("No mode defined");

        }

        $modinfo = serialize(get_array_of_activities($mod->course));
        if (!set_field("course", "modinfo", $modinfo, "id", $mod->course)) {
            error("Could not cache module information!");
        }

        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            save_session("SESSION");
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course");
        }
        exit;
    }


    if (isset($move)) {  

        require_variable($id);   

        if (! $cm = get_record("course_modules", "id", $id)) {
            error("This course module doesn't exist");
        }
    
        move_module($cm, $move);

        $modinfo = serialize(get_array_of_activities($cm->course));
        if (!set_field("course", "modinfo", $modinfo, "id", $cm->course)) {
            error("Could not cache module information!");
        }

        redirect($HTTP_REFERER);
        exit;

    } else if (isset($delete)) {   // value = course module

        if (! $cm = get_record("course_modules", "id", $delete)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
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

        $fullmodulename = strtolower(get_string("modulename", $module->name));

        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;
        $form->course       = $cm->course;
        $form->instance     = $cm->instance;
        $form->modulename   = $module->name;
        $form->fullmodulename  = $fullmodulename;
        $form->instancename = $instance->name;

        include("mod_delete.html");

        exit;


    } else if (isset($update)) {   // value = course module

        if (! $cm = get_record("course_modules", "id", $update)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
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
            save_session("SESSION");
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

        
    } else if (isset($add)) {

        if (!$add) {
            redirect($HTTP_REFERER);
            die;
        }

        require_variable($id);
        require_variable($section);

        if (! $course = get_record("course", "id", $id)) {
            error("This course doesn't exist");
        }

        if (! $module = get_record("modules", "name", $add)) {
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
        $fullmodulename = strtolower(get_string("modulename", $module->name));

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

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("You can't modify this course!");
    }

    $streditinga = get_string("editinga", "moodle", $fullmodulename);
    $strmodulenameplural = get_string("modulenameplural", $module->name);

    if ($course->category) {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      <A HREF=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</A> -> 
                      $streditinga", "form.name", "", false);
    } else {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "$streditinga", "form.name", "", false);
    }

    unset($SESSION->modform); // Clear any old ones that may be hanging around.
    save_session("SESSION");

    $modform = "../mod/$module->name/mod.html";

    if (file_exists($modform)) {

        print_heading($pageheading);
        print_simple_box_start("center", "", "$THEME->cellheading");
        include($modform);
        print_simple_box_end();

    } else {
        notice("This module cannot be added to this course yet! (No file found at: $modform)", "$CFG->wwwroot/course/view.php?id=$course->id");
    }

    print_footer($course);

?>
