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

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $instance = get_record($module->name, "id", $cm->instance)) {
            error("The required instance of this module doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
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

    exit;


/// FUNCTIONS //////////////////////////////////////////////////////////////////////

function add_course_module($mod) {
    GLOBAL $db;

    $timenow = time();

    if (!$rs = $db->Execute("INSERT into course_modules 
                                SET course   = '$mod->course', 
                                    module   = '$mod->module',
                                    instance = '$mod->instance',
                                    section     = '$mod->section',
                                    added    = '$timenow' ")) {
        return 0;
    }
    
    // Get it out again - this is the most compatible way to determine the ID
    if ($rs = $db->Execute("SELECT id FROM course_modules 
                            WHERE module = $mod->module AND added = $timenow")) {
        return $rs->fields[0];
    } else {
        return 0;
    }

}

function add_mod_to_section($mod) {
// Returns the course_sections ID where the mod is inserted
    GLOBAL $db;

    if ($cw = get_record_sql("SELECT * FROM course_sections 
                              WHERE course = '$mod->course' AND section = '$mod->section'") ) {

        if ($cw->sequence) {
            $newsequence = "$cw->sequence,$mod->coursemodule";
        } else {
            $newsequence = "$mod->coursemodule";
        }
        if (!$rs = $db->Execute("UPDATE course_sections SET sequence = '$newsequence' WHERE id = '$cw->id'")) {
            return 0;
        } else {
            return $cw->id;     // Return course_sections ID that was used.
        }
       
    } else {  // Insert a new record
        if (!$rs = $db->Execute("INSERT into course_sections 
                                 SET course   = '$mod->course', 
                                     section     = '$mod->section',
                                     summary  = '',
                                     sequence = '$mod->coursemodule' ")) {
            return 0;
        }
        // Get it out again - this is the most compatible way to determine the ID
        if ($rs = $db->Execute("SELECT id FROM course_sections 
                                WHERE course = '$mod->course' AND section = '$mod->section'")) {
            return $rs->fields[0];
        } else {
            return 0;
        }
    }
}

function delete_course_module($mod) {
    return set_field("course_modules", "deleted", 1, "id", $mod);
}

function delete_mod_from_section($mod, $section) {
    GLOBAL $db;

    if ($cw = get_record("course_sections", "id", "$section") ) {

        $modarray = explode(",", $cw->sequence);

        if ($key = array_keys ($modarray, $mod)) {
            array_splice($modarray, $key[0], 1);
            $newsequence = implode(",", $modarray);
            return set_field("course_sections", "sequence", $newsequence, "id", $cw->id);
        } else {
            return false;
        }
       
    } else {  
        return false;
    }
}


function move_module($id, $move) {
    GLOBAL $db;

    if (!$move) {
        return true;
    }

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("This course module doesn't exist");
    }
    
    if (! $thissection = get_record("course_sections", "id", $cm->section)) {
        error("This course section doesn't exist");
    }

    $mods = explode(",", $thissection->sequence);

    $len = count($mods);
    $pos = array_keys($mods, $cm->id);
    $thepos = $pos[0];

    if ($len == 0 || count($pos) == 0 ) {
        error("Very strange. Could not find the required module in this section.");
    }

    if ($len == 1) {
        $first = true;
        $last = true;
    } else {
        $first = ($thepos == 0);
        $last  = ($thepos == $len - 1);
    }

    if ($move < 0) {    // Moving the module up

        if ($first) {
            if ($thissection->section == 1) {  // First section, do nothing
                return true;
            } else {               // Push onto end of previous section
                $prevsectionnumber = $thissection->section - 1;
                if (! $prevsection = get_record_sql("SELECT * FROM course_sections 
                                                  WHERE course='$thissection->course'
                                                  AND section='$prevsectionnumber' ")) {
                    error("Previous section ($prevsection->id) doesn't exist");
                }

                if ($prevsection->sequence) {
                    $newsequence = "$prevsection->sequence,$cm->id";
                } else {
                    $newsequence = "$cm->id";
                }

                if (! set_field("course_sections", "sequence", $newsequence, "id", $prevsection->id)) {
                    error("Previous section could not be updated");
                }

                if (! set_field("course_modules", "section", $prevsection->id, "id", $cm->id)) {
                    error("Module could not be updated");
                }

                array_splice($mods, 0, 1);
                $newsequence = implode(",", $mods);
                if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                    error("Module could not be updated");
                }

                return true;

            }
        } else {        // move up within this section
            $swap = $mods[$thepos-1];
            $mods[$thepos-1] = $mods[$thepos];
            $mods[$thepos] = $swap;
            
            $newsequence = implode(",", $mods);
            if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                error("This section could not be updated");
            }
            return true;
        }

    } else {            // Moving the module down

        if ($last) {
            $nextsectionnumber = $thissection->section + 1;
            if ($nextsection = get_record_sql("SELECT * FROM course_sections 
                                            WHERE course='$thissection->course'
                                            AND section='$nextsectionnumber' ")) {

                if ($nextsection->sequence) {
                    $newsequence = "$cm->id,$nextsection->sequence";
                } else {
                    $newsequence = "$cm->id";
                }

                if (! set_field("course_sections", "sequence", $newsequence, "id", $nextsection->id)) {
                    error("Next section could not be updated");
                }

                if (! set_field("course_modules", "section", $nextsection->id, "id", $cm->id)) {
                    error("Module could not be updated");
                }

                array_splice($mods, $thepos, 1);
                $newsequence = implode(",", $mods);
                if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                    error("This section could not be updated");
                }
                return true;

            } else {        // There is no next section, so just return
                return true;

            }
        } else {      // move down within this section
            $swap = $mods[$thepos+1];
            $mods[$thepos+1] = $mods[$thepos];
            $mods[$thepos] = $swap;
            
            $newsequence = implode(",", $mods);
            if (! set_field("course_sections", "sequence", $newsequence, "id", $thissection->id)) {
                error("This section could not be updated");
            }
            return true;
        }
    }
}

?>


