<?PHP
    require_once("../config.php");
    require_once("lib.php");

    $action = optional_param('action');
    if (!isset($action)) {
        $action = 'grades';
    }

    require_variable($id);              // course id
    optional_variable($download);
    if (! $course = get_record("course", "id", $id)) {
        error(get_string('errornocourse','grades'));
    }
    
    if (isset($_REQUEST['group'])) {
        $group = clean_param($_REQUEST['group'], PARAM_INT);
    }
    else {
        $group = NULL;
    }
    
    require_login($course->id);

    if (! $course = get_record("course", "id", $id)) {
        error(get_string('incorrectcourseid', 'grades'));
    }
    
    if (!isset($USER->editing)) {
        $USER->editing = false;
    }

    $editing = false;

    if (isteacheredit($course->id)) {
       if (isset($edit)) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }

        $editing = $USER->editing;
    }

    // if the user set new prefs make sure they happen now
    if ($action == 'set_grade_preferences') {
        grade_set_grade_preferences();
    }

    $preferences = grade_get_preferences();
    
    // we want this in it's own window
    if ($action == 'stats') {
        grade_stats();
        exit();
    }
    elseif ($action == 'excel') {
        grade_download_excel();
        exit();
    }
    elseif ($action == 'text') {
        grade_download_text();
        exit();
    }
    
    $loggedinas = user_login_string($course, $USER);
    
    if (isteacher($course->id)) {
        if (isset($_REQUEST['student'])) {
            $student = clean_param($_REQUEST['student'], PARAM_CLEAN);
        }
        else if (!isset($student)) {
            $student = -1;
        }
    }
    else {
        $student = $USER->id;
    }
    
    $grade_menu = grade_get_grades_menu();
    
    print_header($course->shortname, $course->fullname, $grade_menu,"", "", true, grade_preferences_button(), $loggedinas);
    grade_preferences_menu();
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
