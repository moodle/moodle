<?php // $Id$

//  Moves, adds, updates, duplicates or deletes modules in a course

    require("../config.php");
    require_once("lib.php");

    require_login();

    $sectionreturn = optional_param('sr', '', PARAM_INT);
    $add           = optional_param('add','', PARAM_ALPHA);
    $type          = optional_param('type', '', PARAM_ALPHA);
    $indent        = optional_param('indent', 0, PARAM_INT);
    $update        = optional_param('update', 0, PARAM_INT);
    $hide          = optional_param('hide', 0, PARAM_INT);
    $show          = optional_param('show', 0, PARAM_INT);
    $copy          = optional_param('copy', 0, PARAM_INT);
    $moveto        = optional_param('moveto', 0, PARAM_INT);
    $movetosection = optional_param('movetosection', 0, PARAM_INT);
    $delete        = optional_param('delete', 0, PARAM_INT);
    $course        = optional_param('course', 0, PARAM_INT);
    $groupmode     = optional_param('groupmode', -1, PARAM_INT);
    $duplicate     = optional_param('duplicate', 0, PARAM_INT);
    $cancel        = optional_param('cancel', 0, PARAM_BOOL);
    $cancelcopy    = optional_param('cancelcopy', 0, PARAM_BOOL);

    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $mod = $SESSION->modform;
        unset($SESSION->modform);
    } else {
        $mod = (object)$_POST;
    }

    if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        } else {
            redirect("view.php?id=$mod->course#section-$sectionreturn");
        }
    }

    //check if we are adding / editing a module that has new forms using formslib
    if (!empty($add)){
        $modname=$add;
        if (file_exists("../mod/$modname/mod_form.php")) {
            $id          = required_param('id', PARAM_INT);
            $section     = required_param('section', PARAM_INT);
            $type        = optional_param('type', '', PARAM_ALPHA);
            $returntomod = optional_param('return', 0, PARAM_BOOL);

            redirect("modedit.php?add=$add&type=$type&course=$id&section=$section&return=$returntomod");
        }
    }elseif (!empty($update)){
        if (!$modname=get_field_sql("SELECT md.name
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md
                           WHERE cm.id = '$update' AND
                                 md.id = cm.module")){
            error('Invalid course module id!');
        }
        $returntomod = optional_param('return', 0, PARAM_BOOL);
        if (file_exists("../mod/$modname/mod_form.php")) {
            redirect("modedit.php?update=$update&return=$returntomod");
        }
    }
    //not adding / editing a module that has new forms using formslib
    //carry on

    if (!empty($course) and confirm_sesskey()) {    // add, delete or update form submitted

        if (empty($mod->coursemodule)) { //add
            if (! $course = get_record("course", "id", $mod->course)) {
                error("This course doesn't exist");
            }
            $mod->instance = '';
            $mod->coursemodule = '';
        } else { //delete and update
            if (! $cm = get_record("course_modules", "id", $mod->coursemodule)) {
                error("This course module doesn't exist");
            }

            if (! $course = get_record("course", "id", $cm->course)) {
                error("This course doesn't exist");
            }
            $mod->instance = $cm->instance;
            $mod->coursemodule = $cm->id;
        }

        require_login($course->id); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/course:manageactivities', $context);

        $mod->course = $course->id;
        $mod->modulename = clean_param($mod->modulename, PARAM_SAFEDIR);  // For safety
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

                if (isset($mod->name)) {
                    if (trim($mod->name) == '') {
                        unset($mod->name);
                    }
                }

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

                if (isset($mod->visible)) {
                    set_coursemodule_visible($mod->coursemodule, $mod->visible);
                }

                if (isset($mod->groupmode)) {
                    set_coursemodule_groupmode($mod->coursemodule, $mod->groupmode);
                }
                
                if (isset($mod->groupingid)) {
                    set_coursemodule_groupingid($mod->coursemodule, $mod->groupingid);
                }
                
                if (isset($mod->groupmembersonly)) {
                    set_coursemodule_groupmembersonly($mod->coursemodule, $mod->groupmembersonly);
                }

                if (isset($mod->redirect)) {
                    $SESSION->returnpage = $mod->redirecturl;
                } else {
                    $SESSION->returnpage = "$CFG->wwwroot/mod/$mod->modulename/view.php?id=$mod->coursemodule";
                }

                add_to_log($course->id, "course", "update mod",
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule",
                           "$mod->modulename $mod->instance");
                add_to_log($course->id, $mod->modulename, "update",
                           "view.php?id=$mod->coursemodule",
                           "$mod->instance", $mod->coursemodule);
                break;

            case "add":

                if (!course_allowed_module($course,$mod->modulename)) {
                    error("This module ($mod->modulename) has been disabled for this particular course");
                }

                if (!isset($mod->name) || trim($mod->name) == '') {
                    $mod->name = get_string("modulename", $mod->modulename);
                }

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

                if (!isset($mod->groupmode)) { // to deal with pre-1.5 modules
                    $mod->groupmode = $course->groupmode;  /// Default groupmode the same as course
                }
                
                if (isset($mod->groupingid)) {
                    set_coursemodule_groupingid($mod->coursemodule, $mod->groupingid);
                }
                
                if (isset($mod->groupmembersonly)) {
                    set_coursemodule_groupmembersonly($mod->coursemodule, $mod->groupmembersonly);
                }
                $mod->instance = $return;

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

                if (!isset($mod->visible)) {   // We get the section's visible field status
                    $mod->visible = get_field("course_sections","visible","id",$sectionid);
                }
                // make sure visibility is set correctly (in particular in calendar)
                set_coursemodule_visible($mod->coursemodule, $mod->visible);

                if (isset($mod->redirect)) {
                    $SESSION->returnpage = $mod->redirecturl;
                } else {
                    $SESSION->returnpage = "$CFG->wwwroot/mod/$mod->modulename/view.php?id=$mod->coursemodule";
                }

                add_to_log($course->id, "course", "add mod",
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule",
                           "$mod->modulename $mod->instance");
                add_to_log($course->id, $mod->modulename, "add",
                           "view.php?id=$mod->coursemodule",
                           "$mod->instance", $mod->coursemodule);
                break;

            case "delete":
                if ($cm and $cw = get_record("course_sections", "id", $cm->section)) {
                    $sectionreturn = $cw->section;
                }

                if (! $deleteinstancefunction($mod->instance)) {
                    notify("Could not delete the $mod->modulename (instance)");
                }
                if (! delete_course_module($mod->coursemodule)) {
                    notify("Could not delete the $mod->modulename (coursemodule)");
                }
                if (! delete_mod_from_section($mod->coursemodule, "$mod->section")) {
                    notify("Could not delete the $mod->modulename from that section");
                }

                unset($SESSION->returnpage);

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
            redirect("view.php?id=$course->id#section-$sectionreturn");
        }
        exit;
    }

    if ((!empty($movetosection) or !empty($moveto)) and confirm_sesskey()) {

        if (! $cm = get_record("course_modules", "id", $USER->activitycopy)) {
            error("The copied course module doesn't exist!");
        }

        if (!empty($movetosection)) {
            if (! $section = get_record("course_sections", "id", $movetosection)) {
                error("This section doesn't exist");
            }
            $beforecm = NULL;

        } else {                      // normal moveto
            if (! $beforecm = get_record("course_modules", "id", $moveto)) {
                error("The destination course module doesn't exist");
            }
            if (! $section = get_record("course_sections", "id", $beforecm->section)) {
                error("This section doesn't exist");
            }
        }

        require_login($section->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $section->course);
        require_capability('moodle/course:manageactivities', $context);

        if (!ismoving($section->course)) {
            error("You need to copy something first!");
        }

        moveto_module($cm, $section, $beforecm);

        unset($USER->activitycopy);
        unset($USER->activitycopycourse);
        unset($USER->activitycopyname);

        rebuild_course_cache($section->course);

        if (SITEID == $section->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$section->course#section-$sectionreturn");
        }

    } else if (!empty($indent) and confirm_sesskey()) {

        $id = required_param('id',PARAM_INT);

        if (! $cm = get_record("course_modules", "id", $id)) {
            error("This course module doesn't exist");
        }

        require_login($cm->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $cm->course);
        require_capability('moodle/course:manageactivities', $context);

        $cm->indent += $indent;

        if ($cm->indent < 0) {
            $cm->indent = 0;
        }

        if (!set_field("course_modules", "indent", $cm->indent, "id", $cm->id)) {
            error("Could not update the indent level on that course module");
        }

        if (SITEID == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course#section-$sectionreturn");
        }
        exit;

    } else if (!empty($hide) and confirm_sesskey()) {

        if (! $cm = get_record("course_modules", "id", $hide)) {
            error("This course module doesn't exist");
        }

        require_login($cm->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('moodle/course:activityvisibility', $context);

        set_coursemodule_visible($cm->id, 0);

        rebuild_course_cache($cm->course);

        if (SITEID == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course#section-$sectionreturn");
        }
        exit;

    } else if (!empty($show) and confirm_sesskey()) {

        if (! $cm = get_record("course_modules", "id", $show)) {
            error("This course module doesn't exist");
        }

        require_login($cm->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $cm->course);
        require_capability('moodle/course:activityvisibility', $context);

        if (! $section = get_record("course_sections", "id", $cm->section)) {
            error("This module doesn't exist");
        }

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if ($module->visible and ($section->visible or (SITEID == $cm->course))) {
            set_coursemodule_visible($cm->id, 1);
            rebuild_course_cache($cm->course);
        }

        if (SITEID == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course#section-$sectionreturn");
        }
        exit;

    } else if ($groupmode > -1 and confirm_sesskey()) {

        $id = required_param( 'id', PARAM_INT );

        if (! $cm = get_record("course_modules", "id", $id)) {
            error("This course module doesn't exist");
        }

        require_login($cm->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('moodle/course:manageactivities', $context);

        set_coursemodule_groupmode($cm->id, $groupmode);

        rebuild_course_cache($cm->course);

        if (SITEID == $cm->course) {
            redirect($CFG->wwwroot);
        } else {
            redirect("view.php?id=$cm->course#section-$sectionreturn");
        }
        exit;

    } else if (!empty($copy) and confirm_sesskey()) { // value = course module

        if (! $cm = get_record("course_modules", "id", $copy)) {
            error("This course module doesn't exist");
        }

        require_login($cm->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $cm->course);
        require_capability('moodle/course:manageactivities', $context);

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

        redirect("view.php?id=$cm->course#section-$sectionreturn");

    } else if (!empty($cancelcopy) and confirm_sesskey()) { // value = course module

        $courseid = $USER->activitycopycourse;

        unset($USER->activitycopy);
        unset($USER->activitycopycourse);
        unset($USER->activitycopyname);

        redirect("view.php?id=$courseid#section-$sectionreturn");

    } else if (!empty($delete) and confirm_sesskey()) {   // value = course module

        if (! $cm = get_record("course_modules", "id", $delete)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        require_login($cm->course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $cm->course);
        require_capability('moodle/course:manageactivities', $context);

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $instance = get_record($module->name, "id", $cm->instance)) {
            // Delete this module from the course right away
            if (! delete_mod_from_section($cm->id, $cm->section)) {
                notify("Could not delete the $module->name from that section");
            }
            if (! delete_course_module($cm->id)) {
                notify("Could not delete the $module->name (coursemodule)");
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
        $form->sesskey      = !empty($USER->id) ? $USER->sesskey : '';

        $strdeletecheck = get_string('deletecheck', '', $form->fullmodulename);
        $strdeletecheckfull = get_string('deletecheckfull', '', "$form->fullmodulename '$form->instancename'");

        $CFG->pagepath = 'mod/'.$module->name.'/delete';

        print_header_simple($strdeletecheck, '', build_navigation(array(array('name'=>$strdeletecheck,'link'=>'','type'=>'misc'))));

        print_simple_box_start('center', '60%', '#FFAAAA', 20, 'noticebox');
        print_heading($strdeletecheckfull);
        include_once('mod_delete.html');
        print_simple_box_end();
        print_footer($course);

        exit;


    } else if (!empty($update) and confirm_sesskey()) {   // value = course module

        if (! $cm = get_record("course_modules", "id", $update)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
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
        $form->sesskey      = !empty($USER->id) ? $USER->sesskey : '';

        $sectionname = get_section_name($course->format);
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section && $course->format != 'site') {
            $heading->what = $fullmodulename;
            $heading->in   = "$sectionname $cw->section";
            $pageheading = get_string("updatingain", "moodle", $heading);
        } else {
            $pageheading = get_string("updatinga", "moodle", $fullmodulename);
        }
        $strnav = "<a href=\"$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id\">".format_string($form->name,true)."</a> ->";

        if ($module->name == 'resource') {
            $CFG->pagepath = 'mod/'.$module->name.'/'.$form->type;
        } else {
            $CFG->pagepath = 'mod/'.$module->name.'/mod';
        }

    } else if (!empty($duplicate) and confirm_sesskey()) {   // value = course module


        if (! $cm = get_record("course_modules", "id", $duplicate)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
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

        if (isset($return)) {
            $SESSION->returnpage = "$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id";
        }

        $section = get_field('course_sections', 'section', 'id', $cm->section);

        $form->coursemodule = $cm->id;
        $form->section      = $section;     // The section ID
        $form->course       = $course->id;
        $form->module       = $module->id;
        $form->modulename   = $module->name;
        $form->instance     = $cm->instance;
        $form->mode         = "add";
        $form->sesskey      = !empty($USER->id) ? $USER->sesskey : '';

        $sectionname    = get_section_name($course->format);
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section) {
            $heading->what = $fullmodulename;
            $heading->in   = "$sectionname $cw->section";
            $pageheading = get_string("duplicatingain", "moodle", $heading);
        } else {
            $pageheading = get_string("duplicatinga", "moodle", $fullmodulename);
        }
        $strnav = "<a href=\"$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id\">$form->name</a> ->";

        $CFG->pagepath = 'mod/'.$module->name.'/mod';


    } else if (!empty($add) and confirm_sesskey()) {

        $id = required_param('id',PARAM_INT);
        $section = required_param('section',PARAM_INT);

        if (! $course = get_record("course", "id", $id)) {
            error("This course doesn't exist");
        }

        if (! $module = get_record("modules", "name", $add)) {
            error("This module type doesn't exist");
        }

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/course:manageactivities', $context);

        if (!course_allowed_module($course,$module->id)) {
            error("This module has been disabled for this particular course");
        }

        require_login($course->id); // needed to setup proper $COURSE

        $form->section    = $section;         // The section number itself
        $form->course     = $course->id;
        $form->module     = $module->id;
        $form->modulename = $module->name;
        $form->instance   = "";
        $form->coursemodule = "";
        $form->mode       = "add";
        $form->sesskey    = !empty($USER->id) ? $USER->sesskey : '';
        if (!empty($type)) {
            $form->type = $type;
        }

        $sectionname    = get_section_name($course->format);
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
            $CFG->pagepath .= '/' . $type;
        }
        else {
            $CFG->pagepath .= '/mod';
        }

    } else {
        error("No action was specified");
    }

    require_login($course->id); // needed to setup proper $COURSE
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:manageactivities', $context);

    $streditinga = get_string("editinga", "moodle", $fullmodulename);
    $strmodulenameplural = get_string("modulenameplural", $module->name);

    if ($module->name == "label") {
        $focuscursor = "form.content";
    } else {
        $focuscursor = "form.name";
    }

    $navlinks = array();
    $navlinks[] = array('name' => $strmodulenameplural, 'link' => "$CFG->wwwroot/mod/$module->name/index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => $streditinga, 'link' => '', 'type' => 'action');
    $navigation = build_navigation($navlinks);

    print_header_simple($streditinga, '', $navigation, $focuscursor, "", false);

    if (!empty($cm->id)) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $currenttab = 'update';
        $overridableroles = get_overridable_roles($context);
        $assignableroles  = get_assignable_roles($context);
        include_once($CFG->dirroot.'/'.$CFG->admin.'/roles/tabs.php');
    }

    unset($SESSION->modform); // Clear any old ones that may be hanging around.

    $modform = "../mod/$module->name/mod.html";

    if (file_exists($modform)) {

        if ($usehtmleditor = can_use_html_editor()) {
            $defaultformat = FORMAT_HTML;
            $editorfields = '';
        } else {
            $defaultformat = FORMAT_MOODLE;
        }

        $icon = '<img class="icon" src="'.$CFG->modpixpath.'/'.$module->name.'/icon.gif" alt="'.get_string('modulename',$module->name).'"/>';

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        print_simple_box_start('center', '', '', 5, 'generalbox', $module->name);
        include_once($modform);
        print_simple_box_end();

        if ($usehtmleditor and empty($nohtmleditorneeded)) {
            use_html_editor($editorfields);
        }

    } else {
        notice("This module cannot be added to this course yet! (No file found at: $modform)", "$CFG->wwwroot/course/view.php?id=$course->id");
    }

    print_footer($course);
?>
