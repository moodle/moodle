<?php
      // Edit course settings

    require_once('../config.php');
    require_once($CFG->dirroot.'/enrol/enrol.class.php');
    require_once('lib.php');
    require_once('edit_form.php');

    $id         = optional_param('id', 0, PARAM_INT);       // course id
    $categoryid = optional_param('category', 0, PARAM_INT); // course category - can be changed in edit form

    $PAGE->set_pagelayout('admin');

/// basic access control checks
    if ($id) { // editing course

        if($id == SITEID){
            // don't allow editing of  'site course' using this from
            print_error('cannoteditsiteform');
        }

        if (!$course = $DB->get_record('course', array('id'=>$id))) {
            print_error('invalidcourseid');
        }
        require_login($course->id);
        $category = $DB->get_record('course_categories', array('id'=>$course->category));
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/course:update', $coursecontext);
    } else if ($categoryid) { // creating new course in this category
        $course = null;
        require_login();
        if (!$category = $DB->get_record('course_categories', array('id'=>$categoryid))) {
            print_error('unknowcategory');
        }
        require_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $category->id));
    } else {
        require_login();
        print_error('needcoursecategroyid');
    }

    $PAGE->set_url('/course/edit.php');
    if ($id !== 0) {
        $PAGE->url->param('id',$id);
    } else {
        $PAGE->url->param('category',$categoryid);
    }

    /// Prepare course and the editor
    $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
    if (!empty($course)) {
        $allowedmods = array();
        if (!empty($course)) {
            if ($am = $DB->get_records('course_allowed_modules', array('course'=>$course->id))) {
                foreach ($am as $m) {
                    $allowedmods[] = $m->module;
                }
            } else {
                if (empty($course->restrictmodules)) {
                    $allowedmods = explode(',',$CFG->defaultallowedmodules);
                } // it'll be greyed out but we want these by default anyway.
            }
            $course->allowedmods = $allowedmods;
        }
        $course = file_prepare_standard_editor($course, 'summary', $editoroptions, $coursecontext, 'course_summary', $course->id);
    } else {
        $course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course_summary', null);
    }

/// first create the form
    $editform = new course_edit_form('edit.php', compact('course', 'category', 'editoroptions'));
    // now override defaults if course already exists
    if (!empty($course->id)) {
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
        $data->timemodified = time();

        if (empty($course->id)) {
            // In creating the course
            if (!$course = create_course($data)) {
                print_error('coursenotcreated');
            }

            // Get the context of the newly created course
            $context = get_context_instance(CONTEXT_COURSE, $course->id);

            // Save the files used in the summary editor
            $editordata = new stdClass;
            $editordata->id = $course->id;
            $editordata->summary_editor = $data->summary_editor;
            $editordata = file_postupdate_standard_editor($editordata, 'summary', $editoroptions, $context, 'course_summary', $course->id);
            $DB->update_record('course', $editordata);

            // assign default role to creator if not already having permission to manage course assignments
            if (!is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
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
            // Save any changes to the files used in the editor
            $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $coursecontext, 'course_summary', $data->id);
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

    if (!empty($course->id)) {
        $PAGE->navbar->add($streditcoursesettings);
        $title = $streditcoursesettings;
        $fullname = $course->fullname;
    } else {
        $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
        $PAGE->navbar->add($strcategories, new moodle_url('/course/index.php'));
        $PAGE->navbar->add($straddnewcourse);
        $title = "$site->shortname: $straddnewcourse";
        $fullname = $site->fullname;
    }

    $PAGE->set_title($title);
    $PAGE->set_heading($fullname);
    $PAGE->set_focuscontrol($editform->focus());
    echo $OUTPUT->header();
    echo $OUTPUT->heading($streditcoursesettings);

    $editform->display();

    echo $OUTPUT->footer();

