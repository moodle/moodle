<?PHP // $Id$

//  Moves, adds, updates or deletes modules in a course

    require("../config.php");
    require("lib.php");

    if (isset($course) && isset($HTTP_POST_VARS)) {    // add or update form submitted
        $mod = (object)$HTTP_POST_VARS;

        require_login($mod->course);

        if (!isteacher($mod->course)) {
            error("You can't modify this course!");
        }

        $modcode = "../mod/$mod->modulename/mod.php";
        if (file_exists($modcode)) {
            include($modcode);
        } else {
            error("This module is missing important code! (mod.php)");
        }

        switch ($mod->mode) {
            case "update":
                if (! update_instance($mod)) {
                    error("Could not update the $mod->modulename");
                }
                add_to_log($mod->course, "course", "update mod", "../mod/$mod->modulename/view.php?id=$mod->coursemodule", "$mod->modulename $mod->instance"); 
                break;

            case "add":
                if (! $mod->instance = add_instance($mod)) {
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
                if (! delete_instance($mod->instance)) {
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

        if ($SESSION->returnpage) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course");
        }
        exit;
    }

    if (isset($return)) {  
        $SESSION->returnpage = $HTTP_REFERER;
    }

    if (isset($move)) {  

        require_variable($id);   

        move_module($id, $move);

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


        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;
        $form->course       = $cm->course;
        $form->instance     = $cm->instance;
        $form->modulename   = $module->name;
        $form->fullmodulename  = strtolower($module->fullname);
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

        $sectionname = $SECTION[$course->format];

        if (! $form = get_record($module->name, "id", $cm->instance)) {
            error("The required instance of this module doesn't exist");
        }
        
        if (! $cw = get_record("course_sections", "id", $cm->section)) {
            error("This course section doesn't exist");
        }

        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;     // The section ID
        $form->course       = $course->id;
        $form->module       = $module->id;
        $form->modulename   = $module->name;
        $form->instance     = $cm->instance;
        $form->mode         = "update";

        $pageheading = "Updating a ".strtolower($module->fullname)." in $sectionname $cw->section";

        
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

        $sectionname = $SECTION[$course->format];

        if (! $module = get_record("modules", "name", $add)) {
            error("This module type doesn't exist");
        }

        $form->section    = $section;         // The section number itself
        $form->course     = $course->id;
        $form->module     = $module->id;
        $form->modulename = $module->name;
        $form->instance   = $cm->instance;
        $form->mode       = "add";

        if ($form->section) {
            $pageheading = "Adding a new ".strtolower($module->fullname)." to $sectionname $form->section";
        } else {
            $pageheading = "Adding a new ".strtolower($module->fullname);
        }

    } else {
        error("No action was specfied");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("You can't modify this course!");
    }

    if ($course->category) {
        print_header("$course->shortname: Editing a $module->fullname", 
                     "$course->shortname: Editing a $module->fullname",
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      Editing a $module->fullname", "form.name", "", false);
    } else {
        print_header("$course->shortname: Editing a $module->fullname", 
                     "$course->shortname: Editing a $module->fullname",
                     "Editing a $module->fullname", "form.name", "", false);
    }

    $modform = "../mod/$module->name/mod.html";

    if (file_exists($modform)) {

        print_heading($pageheading);
        print_simple_box_start("center", "", "$THEME->cellheading");
        include($modform);
        print_simple_box_end();

    } else {
        notice("This module cannot be added to this course yet!", "$CFG->wwwroot/course/view.php?id=$course->id");
    }

    print_footer($course);

?>
