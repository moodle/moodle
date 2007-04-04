<?PHP
    require_once("../config.php");
    require_once("lib.php");

    $id       = required_param('id');              // course id
    $download = optional_param('download');
    $user     = optional_param('user', -1);
    $group    = optional_param('group', -1);
    $action   = optional_param('action', 'grades');
    $cview    = optional_param('cview', -1);

    if (!$course = get_record('course', 'id', $id)) {
        error('No course ID');
    }

    require_login($course->id);
    
    /* 
    if (has_capability('moodle/site:accessallgroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
        $group = get_and_set_current_group($course, $course->groupmode, $group);
    } else {
        $group = get_current_group($course->id);
    }
    */
   
    // if the user set new prefs make sure they happen now
    if ($action == 'set_grade_preferences' && $prefs = data_submitted()) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
        grade_set_preferences($course, $prefs);
    }

    $preferences = grade_get_preferences($course->id);

    
    // we want this in its own window
    if ($action == 'stats') {
        grade_stats();
        exit();
    } else if ($action == 'ods') {
        grade_download('ods', $id);
        exit();
    } else if ($action == 'excel') {
        grade_download('xls', $id);
        exit();
    } else if ($action == 'text') {
        grade_download('txt', $id);
        exit();
    }

    print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));
    
    grade_preferences_menu($action, $course, $group);

    /// copied code from assignment module, if this is not the way to do this please change it
    /// the above code does not work
    /// set_and_print_groups() is not fully implemented as function groups_instance_print_grouping_selector()
    /// and function groups_instance_print_group_selector() are missing. 
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $changegroup = optional_param('group', -1, PARAM_INT);   // choose the current group
    $groupmode = groupmode($course);
    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);   
    
    /// Now we need a menu for separategroups as well!
    if ($groupmode == VISIBLEGROUPS || ($groupmode
        && has_capability('moodle/site:accessallgroups', $context))) {
        
        //the following query really needs to change
        if ($groups = groups_get_groups_names($course->id)) { //TODO:
            print_box_start('groupmenu');
            print_group_menu($groups, $groupmode, $currentgroup, 'index.php?id='.$course->id);
            print_box_end(); // groupmenu
        }
    }

    grade_set_uncategorized();

    if (has_capability('moodle/course:viewcoursegrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
        switch ($action) {
            case "cats":
                grade_set_categories();
                break;
            case "insert_category":
                grade_insert_category();
                grade_set_categories();
                break;
            case "assign_categories":
                grade_assign_categories();
                grade_set_categories();
                break;
            case "set_grade_weights":
                grade_set_grade_weights();
                grade_display_grade_weights();
                break;
            case "weights":
                grade_display_grade_weights();
                break;
            case "grades":
                if ($preferences->use_advanced == 1) {
                    grade_view_all_grades($user);
                }
                else {
                    // all the grades will be in the 'uncategorized' category
                    grade_view_category_grades($user);
                }
                break;
            case "vcats":
                grade_view_category_grades($user);
                break;
            case "prefs":
            case "set_grade_preferences":
                grade_display_grade_preferences($course, $preferences);
                break;
            case "letters":
                grade_display_letter_grades();
                break;
            case "set_letter_grades":
                grade_set_letter_grades();
                grade_display_letter_grades();
                break;
            case "delete_category":
                grade_delete_category();
                // re-run set_uncategorized as they may have deleted a category that had items in it 
                grade_set_uncategorized();
                grade_set_categories();
                break;
            case "view_student_grades":
                grade_view_all_grades($user);
                break;
            case "view_student_category_grades":
                grade_view_category_grades($user);
                break;
            default:
                if ($preferences->use_advanced == 1) {
                    grade_view_all_grades($user);
                }
                else {
                    grade_view_category_grades($user);
                }
        } // end switch
    } // end if isTeacher
    else {
        if ($preferences->show_weighted || $preferences->show_points || $preferences->show_percent) {

            if ($preferences->use_advanced == 1) {
                if($action != 'vcats') {
                    grade_view_all_grades($USER->id);
                }
                else {
                    grade_view_category_grades($USER->id);
                }
            } else {
                grade_view_category_grades($USER->id);
            }

        } else {
            error(get_string('gradebookhiddenerror','grades'));
        }
    } // end else (!teacher)
    
    print_footer($course);


?>
