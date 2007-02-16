<?php // $Id$
      // Edit course settings

    require_once('../config.php');
    require_once($CFG->dirroot.'/enrol/enrol.class.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once('lib.php');
    require_once('edit_form.php');

    $id         = optional_param('id', 0, PARAM_INT);       // course id
    $categoryid = optional_param('category', 0, PARAM_INT); // course category - can be changed in edit form

    require_login();

/// basic access control checks
    if ($id) { // editing course
        if (!$course = get_record('course', 'id', $id)) {
            error('Course ID was incorrect');
        }
        $category = get_record('course_categories', 'id', $course->category);
        require_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id));

    } else if ($categoryid) { // creating new course in this category
        $course = null;
        if (!$category = get_record('course_categories', 'id', $categoryid)) {
            error('Category ID was incorrect');
        }
        require_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $category->id));
    } else {
        error('Either course id or category must be specified');
    }

/// prepare course
if (!empty($course)) {
    $allowedmods = array();
    if (!empty($course)) {
        if ($am = get_records('course_allowed_modules','course',$course->id)) {
            foreach ($am as $m) {
                $allowedmods[] = $m->module;
            }
        } else {
            if (empty($course->restrictmodules)) {
                $allowedmods = explode(',',$CFG->defaultallowedmodules);
            } // it'll be greyed out but we want these by default anyway.
        }
        $course->allowedmods = $allowedmods;

        if ($course->enrolstartdate){
            $course->enrolstartdisabled = 0;
        }

        if ($course->enrolenddate) {
            $course->enrolenddisabled = 0;
        }
    }

}


/// first create the form
    $editform = new course_edit_form('edit.php', compact('course', 'category'));
    // now override defaults if course already exists
    if (!empty($course)) {
        $editform->set_data($course);
    }
    if ($editform->is_cancelled()){
        if (empty($course)) {
            redirect($CFG->wwwroot);
        } else {
            redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
        }

    } elseif ($data = $editform->get_data()) {
/// process data if submitted

        //preprocess data
        if ($data->enrolstartdisabled){
            $data->enrolstartdate = 0;
        }

        if ($data->enrolenddisabled) {
            $data->enrolenddate = 0;
        }

        $data->timemodified = time();

        if (empty($course)) {
            create_course($data);
        } else {
            update_course($data);
        }
    }


///print the form

    $site = get_site();

    $streditcoursesettings = get_string("editcoursesettings");
    $straddnewcourse = get_string("addnewcourse");
    $stradministration = get_string("administration");
    $strcategories = get_string("categories");

    if (!empty($course)) {
        print_header($streditcoursesettings, "$course->fullname",
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>
                      -> $streditcoursesettings", $editform->focus());
    } else {
        print_header("$site->shortname: $straddnewcourse", "$site->fullname",
                     "<a href=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradministration</a> -> ".
                     "<a href=\"index.php\">$strcategories</a> -> $straddnewcourse", $editform->focus());
    }

    print_heading($streditcoursesettings);
    $editform->display();
    print_footer($course);

    die;


/// internal functions

function create_course($data) {
    global $CFG, $USER;

    // preprocess allowed mods
    $allowedmods = empty($data->allowedmods) ? array() : $data->allowedmods;
    unset($data->allowedmods);
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        if ($CFG->restrictmodulesfor == 'all') {
            $data->restrictmodules = 1;
        } else {
            $data->restrictmodules = 0;
        }
    }


    $data->timecreated = time();

    // place at beginning of category
    fix_course_sortorder();
    $data->sortorder = get_field_sql("SELECT min(sortorder)-1 FROM {$CFG->prefix}course WHERE category=$data->category");
    if (empty($data->sortorder)) {
        $data->sortorder = 100;
    }

    if ($newcourseid = insert_record('course', $data)) {  // Set up new course

        $course = get_record('course', 'id', $newcourseid);

        // Setup the blocks
        $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
        blocks_repopulate_page($page); // Return value not checked because you can always edit later

        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            update_restricted_mods($course, $allowedmods);
        }

        $section = new object();
        $section->course = $course->id;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record('course_sections', $section);

        fix_course_sortorder();
        add_to_log(SITEID, "course", "new", "view.php?id=$course->id", "$data->fullname (ID $course->id)")        ;
        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if ($data->metacourse and has_capability('moodle/course:managemetacourse', $context)) { // Redirect users with metacourse capability to student import
            redirect($CFG->wwwroot."/course/importstudents.php?id=$course->id");

        } else if (has_capability('moodle/role:assign', $context)) { // Redirect users with assign capability to assign users to different roles
            redirect($CFG->wwwroot."/$CFG->admin/roles/assign.php?contextid=$context->id");

        } else {         // Add current teacher and send to course
            // find a role with legacy:edittingteacher
            if ($teacherroles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW, $context)) {
                // assign the role to this user
                $teachereditrole = array_shift($teacherroles);
                role_assign($teachereditrole->id, $USER->id, 0, $context->id);
            }
            redirect($CFG->wwwroot."/course/view.php?id=$course->id");
        }

    } else {
        error("Serious Error! Could not create the new course!");
    }
    die;
}

function update_course($data) {
    global $USER, $CFG;

    // preprocess allowed mods
    $allowedmods = empty($data->allowedmods) ? array() : $data->allowedmods;
    unset($data->allowedmods);
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        unset($data->restrictmodules);
    }

    $oldcourse = get_record('course', 'id', $data->id); // should not fail, already tested above
    if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $oldcourse->category))
      or !has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $data->category))) {
        // can not move to new category, keep the old one
        unset($data->category);
    }

    // Update with the new data
    if (update_record('course', $data)) {

        $course = get_record('course', 'id', $data->id);

        add_to_log($course->id, "course", "update", "edit.php?id=$course->id", "");
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            update_restricted_mods($course, $allowedmods);
        }
        fix_course_sortorder();

        // Test for and remove blocks which aren't appropriate anymore
        $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
        blocks_remove_inappropriate($page);

        redirect($CFG->wwwroot."/course/view.php?id=$course->id");

    } else {
        error("Serious Error! Could not update the course record! (id = $form->id)");
    }
    die;
}

?>
