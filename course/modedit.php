<?php // $Id$

//  adds or updates modules in a course using new formslib

    require_once("../config.php");
    require_once("lib.php");

    require_login();

    $add           = optional_param('add', '', PARAM_ALPHA);
    $update        = optional_param('update', 0, PARAM_INT);
    //return to course/view.php if false or mod/modname/view.php if true
    $return        = optional_param('return', 0, PARAM_BOOL);
    $type          = optional_param('type', '', PARAM_ALPHANUM);

    if (!empty($add)) {
        $section = required_param('section', PARAM_INT);
        $course = required_param('course', PARAM_INT);

        if (! $course = get_record("course", "id", $course)) {
            error("This course doesn't exist");
        }

        if (! $module = get_record("modules", "name", $add)) {
            error("This module type doesn't exist");
        }

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/course:manageactivities', $context);

        if (!course_allowed_module($course, $module->id)) {
            error("This module has been disabled for this particular course");
        }

        require_login($course->id); // needed to setup proper $COURSE

        $form->section    = $section;         // The section number itself
        $form->course     = $course->id;
        $form->module     = $module->id;
        $form->modulename = $module->name;
        $form->instance   = "";
        $form->coursemodule = "";
        $form->add=$add;
        $form->return=0;//must be false if this is an add, go back to course view on cancel
        if (!empty($type)) {
            $form->type = $type;
        }

        $sectionname = get_section_name($course->format);
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section && $course->format != 'site') {
            $heading->what = $fullmodulename;
            $heading->to   = "$sectionname $form->section";
            $pageheading = get_string("addinganewto", "moodle", $heading);
        } else {
            $pageheading = get_string("addinganew", "moodle", $fullmodulename);
        }

        $CFG->pagepath = 'mod/'.$module->name;
        if (!empty($type)) {
            $CFG->pagepath .= '/'.$type;
        } else {
            $CFG->pagepath .= '/mod';
        }

    } else if (!empty($update)) {
        if (! $cm = get_record("course_modules", "id", $update)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('moodle/course:manageactivities', $context);

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $form = get_record($module->name, "id", $cm->instance)) {
            error("The required instance of this module doesn't exist");
        }

        if (! $cw = get_record("course_sections", "id", $cm->section)) {
            error("This course section doesn't exist");
        }


        $form->coursemodule = $cm->id;
        $form->section      = $cm->section;     // The section ID
        $form->cmidnumber   = $cm->idnumber;    // The cm IDnumber
        $form->course       = $course->id;
        $form->module       = $module->id;
        $form->modulename   = $module->name;
        $form->instance     = $cm->instance;
        $form->return = $return;
        $form->update = $update;

        $sectionname = get_section_name($course->format);
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section && $course->format != 'site') {
            $heading->what = $fullmodulename;
            $heading->in   = "$sectionname $cw->section";
            $pageheading = get_string("updatingain", "moodle", $heading);
        } else {
            $pageheading = get_string("updatinga", "moodle", $fullmodulename);
        }
        
        $navlinksinstancename = array('name' => format_string($form->name,true), 'link' => "$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id", 'type' => 'activityinstance');
       
        $CFG->pagepath = 'mod/'.$module->name;
        if (!empty($type)) {
            $CFG->pagepath .= '/'.$type;
        } else {
            $CFG->pagepath .= '/mod';
        }
    } else {
        error('Invalid operation.');
    }

    $modmoodleform = "$CFG->dirroot/mod/$module->name/mod_form.php";
    if (file_exists($modmoodleform)) {
        require_once($modmoodleform);

    } else {
        error('No formslib form description file found for this activity.');
    }

    $modlib = "$CFG->dirroot/mod/$module->name/lib.php";
    if (file_exists($modlib)) {
        include_once($modlib);
    } else {
        error("This module is missing important code! ($modlib)");
    }

    $mformclassname = 'mod_'.$module->name.'_mod_form';
    $cousesection=isset($cw->section)?$cw->section:$section;
    $mform=& new $mformclassname($form->instance, $cousesection, ((isset($cm))?$cm:null));
    $mform->set_data($form);

    if ($mform->is_cancelled()) {
        if ($return && isset($cm)){
            redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id");
        } else {
            redirect("view.php?id=$course->id#section-".$cousesection);
        }
    } else if ($fromform=$mform->get_data()){
        if (empty($fromform->coursemodule)) { //add
            if (! $course = get_record("course", "id", $fromform->course)) {
                error("This course doesn't exist");
            }
            $fromform->instance = '';
            $fromform->coursemodule = '';
        } else { //update
            if (! $cm = get_record("course_modules", "id", $fromform->coursemodule)) {
                error("This course module doesn't exist");
            }

            if (! $course = get_record("course", "id", $cm->course)) {
                error("This course doesn't exist");
            }
            $fromform->instance = $cm->instance;
            $fromform->coursemodule = $cm->id;
        }

        require_login($course->id); // needed to setup proper $COURSE
        
        if (!empty($fromform->coursemodule)) {
            $context = get_context_instance(CONTEXT_MODULE, $fromform->coursemodule);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
        }
        require_capability('moodle/course:manageactivities', $context);

        $fromform->course = $course->id;
        $fromform->modulename = clean_param($fromform->modulename, PARAM_SAFEDIR);  // For safety

        $addinstancefunction    = $fromform->modulename."_add_instance";
        $updateinstancefunction = $fromform->modulename."_update_instance";

        if (!empty($fromform->update)) {

            if (isset($fromform->name)) {
                if (trim($fromform->name) == '') {
                    unset($fromform->name);
                }
            }

            $returnfromfunc = $updateinstancefunction($fromform);
            if (!$returnfromfunc) {
                error("Could not update the $fromform->modulename", "view.php?id=$course->id");
            }
            if (is_string($returnfromfunc)) {
                error($returnfromfunc, "view.php?id=$course->id");
            }

            if (isset($fromform->visible)) {
                set_coursemodule_visible($fromform->coursemodule, $fromform->visible);
            }

            if (isset($fromform->groupmode)) {
                set_coursemodule_groupmode($fromform->coursemodule, $fromform->groupmode);
            }
            
            // set cm id number
            if (isset($fromform->cmidnumber)) {
                set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);  
            }

            add_to_log($course->id, "course", "update mod",
                       "../mod/$fromform->modulename/view.php?id=$fromform->coursemodule",
                       "$fromform->modulename $fromform->instance");
            add_to_log($course->id, $fromform->modulename, "update",
                       "view.php?id=$fromform->coursemodule",
                       "$fromform->instance", $fromform->coursemodule);

        } else if (!empty($fromform->add)){

            if (!course_allowed_module($course,$fromform->modulename)) {
                error("This module ($fromform->modulename) has been disabled for this particular course");
            }

            if (!isset($fromform->name) || trim($fromform->name) == '') {
                $fromform->name = get_string("modulename", $fromform->modulename);
            }

            $returnfromfunc = $addinstancefunction($fromform);
            if (!$returnfromfunc) {
                /*if (file_exists($moderr)) {
                    $form = $fromform;
                    include_once($moderr);
                    die;
                }*/
                error("Could not add a new instance of $fromform->modulename", "view.php?id=$course->id");
            }
            if (is_string($returnfromfunc)) {
                error($returnfromfunc, "view.php?id=$course->id");
            }

            if (!isset($fromform->groupmode)) { // to deal with pre-1.5 modules
                $fromform->groupmode = $course->groupmode;  /// Default groupmode the same as course
            }

            $fromform->instance = $returnfromfunc;

            // course_modules and course_sections each contain a reference
            // to each other, so we have to update one of them twice.

            if (! $fromform->coursemodule = add_course_module($fromform) ) {
                error("Could not add a new course module");
            }
            if (! $sectionid = add_mod_to_section($fromform) ) {
                error("Could not add the new course module to that section");
            }

            if (! set_field("course_modules", "section", $sectionid, "id", $fromform->coursemodule)) {
                error("Could not update the course module with the correct section");
            }

            if (!isset($fromform->visible)) {   // We get the section's visible field status
                $fromform->visible = get_field("course_sections","visible","id",$sectionid);
            }
            // make sure visibility is set correctly (in particular in calendar)
            set_coursemodule_visible($fromform->coursemodule, $fromform->visible);
            
            // set cm idnumber
            if (isset($fromform->cmidnumber)) {
                set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);  
            }
            
            add_to_log($course->id, "course", "add mod",
                       "../mod/$fromform->modulename/view.php?id=$fromform->coursemodule",
                       "$fromform->modulename $fromform->instance");
            add_to_log($course->id, $fromform->modulename, "add",
                       "view.php?id=$fromform->coursemodule",
                       "$fromform->instance", $fromform->coursemodule);
        } else {
            error("Data submitted is invalid.");
        }

        rebuild_course_cache($course->id);

        redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$fromform->coursemodule");
        exit;

    } else {
        if (!empty($cm->id)) {
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
        }
        require_capability('moodle/course:manageactivities', $context);
        
        $streditinga = get_string("editinga", "moodle", $fullmodulename);
        $strmodulenameplural = get_string("modulenameplural", $module->name);
        
        $navlinks = array();
        $navlinks[] = array('name' => $strmodulenameplural, 'link' => "$CFG->wwwroot/mod/$module->name/index.php?id=$course->id", 'type' => 'activity');
        if (isset($navlinksinstancename)) {
            $navlinks[] = $navlinksinstancename;
        }
        $navlinks[] = array('name' => $streditinga, 'link' => '', 'type' => 'title');
        
        $navigation = build_navigation($navlinks);
        
        print_header_simple($streditinga, '', $navigation, $mform->focus(), "", false);

        if (!empty($cm->id)) {
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $currenttab = 'update';
            include_once($CFG->dirroot.'/'.$CFG->admin.'/roles/tabs.php');
        }
        $icon = '<img src="'.$CFG->modpixpath.'/'.$module->name.'/icon.gif" alt=""/>';

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        $mform->display();
        print_footer($course);
    }
?>
