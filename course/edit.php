<?php // $Id$
      // Edit course settings

    require_once('../config.php');
    require_once($CFG->dirroot.'/enrol/enrol.class.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once('lib.php');
    require_once('edit_form.php');

    $id         = optional_param('id', 0, PARAM_INT);       // course id
    $categoryid = optional_param('category', 0, PARAM_INT); // course category - can be changed in edit form


/// basic access control checks
    if ($id) { // editing course

        if($id == SITEID){
            // don't allow editing of  'site course' using this from
            error('You cannot edit the site course using this form');
        }

        if (!$course = get_record('course', 'id', $id)) {
            error('Course ID was incorrect');
        }
        require_login($course->id);
        $category = get_record('course_categories', 'id', $course->category);
        require_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id));

    } else if ($categoryid) { // creating new course in this category
        $course = null;
        require_login();
        if (!$category = get_record('course_categories', 'id', $categoryid)) {
            error('Category ID was incorrect');
        }
        require_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $category->id));
    } else {
        require_login();
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
        $course->enrolpassword = $course->password; // we need some other name for password field MDL-9929
        $editform->set_data($course);
    }
    if ($editform->is_cancelled()){
        if (empty($course)) {
            redirect($CFG->wwwroot);
        } else {
            redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
        }

    } else if ($data = $editform->get_data()) {

        $data->password = $data->enrolpassword;  // we need some other name for password field MDL-9929
/// process data if submitted

        //preprocess data
        if (!empty($data->enrolstartdisabled)) {
            $data->enrolstartdate = 0;
        }

        if (!empty($data->enrolenddisabled)) {
            $data->enrolenddate = 0;
        }

        $data->timemodified = time();

        if (empty($course)) {
            if (!$course = create_course($data)) {
                print_error('coursenotcreated');
            }

            $context = get_context_instance(CONTEXT_COURSE, $course->id);

            // assign default role to creator if not already having permission to manage course assignments
            if (!has_capability('moodle/course:view', $context) or !has_capability('moodle/role:assign', $context)) {
                role_assign($CFG->creatornewroleid, $USER->id, 0, $context->id);
            }

            // ensure we can use the course right after creating it
            // this means trigger a reload of accessinfo...
            mark_context_dirty($context->path);

            if ($data->metacourse and has_capability('moodle/course:managemetacourse', $context)) {
                // Redirect users with metacourse capability to student import
                redirect($CFG->wwwroot."/course/importstudents.php?id=$course->id");
            } else {
                // Redirect to roles assignment
                redirect($CFG->wwwroot."/$CFG->admin/roles/assign.php?contextid=$context->id");
            }

        } else {
            if (!update_course($data)) {
                print_error('coursenotupdated');
            }
            redirect($CFG->wwwroot."/course/view.php?id=$course->id");
        }
    }


/// Print the form

    $site = get_site();

    $streditcoursesettings = get_string("editcoursesettings");
    $straddnewcourse = get_string("addnewcourse");
    $stradministration = get_string("administration");
    $strcategories = get_string("categories");
    $navlinks = array();

    if (!empty($course)) {
        $navlinks[] = array('name' => $streditcoursesettings,
                            'link' => null,
                            'type' => 'misc');
        $title = $streditcoursesettings;
        $fullname = $course->fullname;
    } else {
        $navlinks[] = array('name' => $stradministration,
                            'link' => "$CFG->wwwroot/$CFG->admin/index.php",
                            'type' => 'misc');
        $navlinks[] = array('name' => $strcategories,
                            'link' => 'index.php',
                            'type' => 'misc');
        $navlinks[] = array('name' => $straddnewcourse,
                            'link' => null,
                            'type' => 'misc');
        $title = "$site->shortname: $straddnewcourse";
        $fullname = $site->fullname;
    }

    $navigation = build_navigation($navlinks);
    print_header($title, $fullname, $navigation, $editform->focus());
    print_heading($streditcoursesettings);

    $editform->display();

    print_footer($course);

?>
