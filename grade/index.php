<?PHP
    require_once("../config.php");
    require_once("lib.php");

    $id       = required_param('id');              // course id
    $download = optional_param('download');
    $student  = optional_param('student', -1);
    $group    = optional_param('group', -1);
    $action   = optional_param('action', 'grades');

    if (!$course = get_record('course', 'id', $id)) {
        error('No course ID');
    }

    require_login($course->id);
    
    if (isteacher($course->id)) {
        $group = get_and_set_current_group($course, $course->groupmode, $group);
    } else {
        $group = get_current_group($course->id);
    }

    
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
    } else if ($action == 'excel') {
        grade_download_excel();
        exit();
    } else if ($action == 'text') {
        grade_download_text();
        exit();
    }

    print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_get_grades_menu());

    grade_preferences_menu($action, $course, $group);

    grade_set_uncategorized();

    if (isteacher($course->id)) {
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
                    grade_view_all_grades($student);
                }
                else {
                    // all the grades will be in the 'uncategorized' category
                    grade_view_category_grades($student);
                }
                break;
            case "vcats":
                grade_view_category_grades($student);
                break;
            case "prefs":
                grade_display_grade_preferences();
                break;
            case "set_grade_preferences":
                grade_display_grade_preferences();
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
                grade_view_student_grades();
                break;
            case "view_student_category_grades":
                grade_view_student_category_grades();
                break;
            default:
                if ($preferences->use_advanced == 1) {
                    grade_view_all_grades($student);
                }
                else {
                    grade_view_category_grades($student);
                }
        } // end switch
    } // end if isTeacher
    else {
        if ($preferences->show_weighted || $preferences->show_points || $preferences->show_points)
        {
            // teacher has set to show student something
            $student = $USER->id;
            switch ($action) {
                case "view_student_category_grades":
                    grade_view_student_category_grades();
                    break;
                case "view_student_grades":
                    if ($preferences->use_advanced == 1) {
                        grade_view_student_grades();
                    }
                    else {
                        grade_view_student_category_grades();
                    }
                    break;
                case "vcats":
                    grade_view_category_grades($student);
                    break;
                default:
                    if ($preferences->use_advanced == 1) {
                        grade_view_student_grades($student);
                    }
                    else {
                        grade_view_student_category_grades();
                    }
                    break;
            } // end switch
        } // end if display something
        else {
            error(get_string('gradebookhiddenerror','grades'));
        }
    } // end else (!teacher)
    
    print_footer($course);


?>
