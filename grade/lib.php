<?php // $Id$

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');



$UNCATEGORIZED = 'Uncategorized';

//******************************************************************
// SQL FUNCTIONS 
//******************************************************************

function grade_get_category_weight($course, $category) {
    global $CFG;
    $sql = "SELECT id, weight, drop_x_lowest, bonus_points, hidden, c.id cat_id
        FROM {$CFG->prefix}grade_category c  
        WHERE c.courseid=$course  
            AND c.name='$category'";
    $temp = get_record_sql($sql);
    return $temp;
}

function grade_get_grade_items($course) {
    global $CFG;
     $sql = "SELECT i.id, c.name as cname, i.modid, mm.name as modname, i.cminstance, c.hidden, cm.visible, i.sort_order
            FROM    {$CFG->prefix}grade_item i, 
                    {$CFG->prefix}grade_category c, 
                    {$CFG->prefix}course_modules cm, 
                    {$CFG->prefix}modules mm
            WHERE c.id=i.category 
                AND i.courseid=c.courseid 
                AND c.courseid=$course 
                AND cm.course=c.courseid 
                AND cm.module=mm.id
                AND i.modid=mm.id
                AND cm.instance=i.cminstance";
    $temp = get_records_sql($sql);
    return $temp;
}

function grade_get_module_link($course, $cminstance, $modid) {
    global $CFG;
    $sql = "SELECT cm.id FROM {$CFG->prefix}course_modules cm, {$CFG->prefix}modules mm, {$CFG->prefix}grade_item i 
            WHERE i.modid='".$modid."' 
                AND i.modid=mm.id 
                AND cm.instance = i.cminstance 
                AND i.cminstance=".$cminstance."
                AND i.courseid=cm.course AND cm.module=mm.id AND i.courseid=$course";
    $temp = get_record_sql($sql);
    return $temp;
}

function grade_get_grade_letter($course, $grade) {
    global $CFG;
    $sql = "SELECT id, letter FROM {$CFG->prefix}grade_letter WHERE courseid=$course AND grade_high >= $grade AND grade_low <= $grade";
    $temp = get_record_sql($sql);
    return $temp;
}

function grade_get_exceptions($course) {
    global $CFG;
    $sql = "SELECT e.id, e.userid, gi.cminstance, gi.modid, c.name as catname, mm.name as modname
            FROM {$CFG->prefix}grade_exceptions e, 
                {$CFG->prefix}grade_item gi, 
                {$CFG->prefix}grade_category c,
                {$CFG->prefix}course_modules cm, 
                {$CFG->prefix}modules mm
            WHERE e.courseid=$course 
                AND gi.id = e.grade_itemid 
                AND c.id = gi.category
                AND cm.course=c.courseid 
                AND cm.module=mm.id
                AND gi.modid=mm.id";
    
    $temp = get_records_sql($sql);
    return $temp;
}

function grade_letters_set($course) {
    global $CFG;
    $sql = "SELECT * FROM {$CFG->prefix}grade_letter WHERE courseid=$course";
    $letters_set = get_records_sql($sql);
    if ($letters_set) {
        return true;
    }
    else {
        return false;
    }
}

function grade_get_users_by_group($course, $group) {
    global $CFG;
    $sql = "SELECT userid FROM {$CFG->prefix}groups_members WHERE courseid=$course AND groupid = $group";
    $members = get_records_sql($sql);
    if ($members) {
        foreach($members as $member) {
            $group->$userid = true;
        }
        return $group;
    }
    else {
        return NULL;
    }
}

//******************************************************************
// END SQL FUNCTIONS 
//******************************************************************

function grade_get_formatted_grades() {
    global $CFG;
    global $course;
    global $preferences;
    $i = 1;
    $grades = grade_get_grades();
    if (isset($grades)) {
        // iterate through all students
        foreach($grades as $cur_category=>$grade_category) {
            // $cur_category holds the value of the current category name
            // $grade_category holds an array of all the mod types that are in this category
            if (isset($grade_category)) {
                foreach($grade_category as $cur_mod=>$mod_category)  {
                    // $cur_mod is the id of the current moodle module type
                    // $mod_category holds the grades for $cur_mod (current mod type)
                    $module_info = get_record('modules', 'id', $cur_mod);
                    $cur_modname = $module_info->name;
                    if (isset($mod_category)) {
                        foreach($mod_category as $cur_cminstance=>$students_grade) {
                            // $cur_cminstance is the course module instance for the cur_mod
                            $instance = get_record($cur_modname, 'id',$cur_cminstance, 'course',$course->id);
                            // it's necessary to check if the name is blank because some mods don't clean up the grades when the instance is deleted
                            // this is a bug. as it is plausible that some item could get created and have old data from a previous mod laying around
                            if ($instance->name != '') {                
                                // duplicate grade item name, the user should know better than to name to things by the same name, but to make sure grades don't disappear lets modify the name slightly
                                if (isset($all_categories["$cur_category"]["$instance->name"])) {
                                    $instance->name= $instance->name.' *'.$i.'*';
                                }

                                if (isset($students_grade->grades) && $students_grade->grades != '') {
                                    foreach($students_grade->grades as $student=>$grade) {
                                        // add an entry for any student that has a grade
                                        $grades_by_student["$student"]["$cur_category"]["$instance->name"]['grade'] = $grade;
                                        $grades_by_student["$student"]["$cur_category"]["$instance->name"]['sort_order'] = $students_grade->sort_order;
                                        
                                        if (!isset($grades_by_student["$student"]["$cur_category"]['stats'])) {
                                            $grades_by_student["$student"]["$cur_category"]['stats'] = array();
                                        }
                                        
                                        if (!isset($grades_by_student["$student"]["$cur_category"]['stats']['points'])) {
                                            $grades_by_student["$student"]["$cur_category"]['stats']['points'] = $grade;
                                        }
                                        else {
                                            $grades_by_student["$student"]["$cur_category"]['stats']['points'] = $grades_by_student["$student"]["$cur_category"]['stats']['points'] + $grade;
                                        }
        
                                        // This next block just creates a comma seperated list of all grades for the category
                                        if (isset($grades_by_student["$student"]["$cur_category"]['stats']['allgrades'])) {
                                            $grades_by_student["$student"]["$cur_category"]['stats']['allgrades'] .=  ','.$grade;
                                        }
                                        else {
                                            $grades_by_student["$student"]["$cur_category"]['stats']['allgrades'] = $grade;
                                        }
                                    }
                                }
                                // set up a list of all categories and assignments (adjusting things for extra credit where necessary)
                                $all_categories["$cur_category"]["$instance->name"]['hidden'] = $students_grade->hidden;
                                $all_categories["$cur_category"]["$instance->name"]['sort_order'] = $students_grade->sort_order;
                                
                                $all_categories["$cur_category"]["$instance->name"]['extra_credit'] = $students_grade->extra_credit;
                                
                                if ($all_categories["$cur_category"]["$instance->name"]['extra_credit'] != 1) {
                                    $all_categories["$cur_category"]["$instance->name"]['maxgrade'] = $students_grade->maxgrade;
                                }
                                else {
                                    $all_categories["$cur_category"]["$instance->name"]['maxgrade'] = 0; 
                                }                                
                                $all_categories["$cur_category"]["$instance->name"]['scale_grade'] = $students_grade->scale_grade;
                                if ($students_grade->scale_grade != 0) {
                                    $all_categories["$cur_category"]["$instance->name"]['scaled_max'] = round($all_categories["$cur_category"]["$instance->name"]['maxgrade']/$students_grade->scale_grade);
                                }
                                else {
                                    // avoids divide by zero... scale_grade shouldn't be set to 0 anyway
                                    $all_categories["$cur_category"]["$instance->name"]['scaled_max'] = $all_categories["$cur_category"]["$instance->name"]['maxgrade'];
                                    $all_categories["$cur_category"]["$instance->name"]['scale_grade'] = 1.0;
                                }
                                if (! isset($all_categories["$cur_category"]['stats']) ) {
                                    $all_categories["$cur_category"]['stats'] = array();
                                }
                                $all_categories["$cur_category"]["$instance->name"]['grade_against'] = $all_categories["$cur_category"]["$instance->name"]['scaled_max'];
                                if (!isset($all_categories["$cur_category"]['stats']['weight'])) {
                                    $weight = grade_get_category_weight($course->id, $cur_category);        
                                    $all_categories["$cur_category"]['stats']['weight'] = $weight->weight;
                                }
        
                                $all_categories["$cur_category"]["$instance->name"]['cminstance'] = $cur_cminstance;
                                $all_categories["$cur_category"]["$instance->name"]['modid'] = $cur_mod;
                                $modname = get_record('modules','id',$cur_mod);
                                $all_categories["$cur_category"]["$instance->name"]['modname'] = $modname->name;
        
                                // get bonus points and drop the x lowest                            
                                $drop = get_record('grade_category', 'courseid', $course->id, 'name', $cur_category);
                                $all_categories["$cur_category"]['stats']['drop'] = $drop->drop_x_lowest;
                                $all_categories["$cur_category"]['stats']['bonus_points'] = $drop->bonus_points;
                            }
                        }
                    }
                } 
            }
        }
        
        // set the students name under student_data (this has the added bonus of creating an entry for students who do not have any grades)
        $students = get_course_students($course->id);
        if (isset($students) && $students) {
            foreach ($students as $userid => $student) {
                $grades_by_student["$userid"]['student_data']['firstname'] = $student->firstname;
                $grades_by_student["$userid"]['student_data']['lastname'] = $student->lastname;
                $grades_by_student["$userid"]['student_data']['email'] = $student->email;
                if (isset($student->location)) {
                    $grades_by_student["$userid"]['student_data']['location'] = $student->location;
                }
                $grades_by_student["$userid"]['student_data']['department'] = $student->department;
                $grades_by_student["$userid"]['student_data']['idnumber'] = $student->idnumber;
            }
        }
        
        // unset any item that has a "" for a name at this point this inludes instructors who have grades or any student formerly enrolled.
        if (isset($grades_by_student)) {
            foreach ($grades_by_student as $student => $assignments) {
                if (!isset($grades_by_student["$student"]['student_data']['firstname']) && !isset($grades_by_student["$student"]['student_data']['lastname'])) {
                    unset($grades_by_student["$student"]);
                }
            }
        }
        
        
        // set the totalpoints for each category taking into account drop_x_lowest
        // also set the number of grade items for the category to make calculating grades for students who have not taken anything easier        
        foreach($all_categories as $category => $assignments) {
            $dropcount = 0;    
            $all_categories["$category"]['stats']['totalpoints'] = 0;
            $all_categories["$category"]['stats']['grade_items'] = 0;
            if (isset($assignments)) {
                foreach($assignments as $assignment=>$grade) {
                    if ($assignment != 'stats') {
                        if ($dropcount < $all_categories["$category"]['stats']['drop']) {
                            // skip a grade in the total
                            $dropcount++;
                        }
                        else {
                            // make sure the current assignment is not extra credit and then add it to the totalpoints
                            if ($all_categories["$category"][$assignment]['extra_credit'] != 1) {
                                $all_categories["$category"]['stats']['totalpoints'] = $all_categories["$category"]['stats']['totalpoints'] + $assignments["$assignment"]['grade_against'];
                                $all_categories["$category"]['stats']['grade_items'] = $all_categories["$category"]['stats']['grade_items'] + 1;
                            }
                        }
                    }
                }
            }
        }

        if (isset($_REQUEST['group'])) {
            $group = clean_param($_REQUEST['group'], PARAM_INT);
        }
        // if the user has selected a group to view by get the group members
        if (isset($group) && $group != 0) {
            $groupmembers = get_group_users($group);
        }

        // this next block catches any students who do not have a grade for any item in a particular category
        foreach($all_categories as $category => $main_category) {
            // make sure each student has an entry for each category
            if (isset($grades_by_student)) {
                foreach($grades_by_student as $student=>$categories) {
                    if ( (isset($groupmembers) && isset($groupmembers[$student])) || !isset($groupmembers)) {
                        $grades_by_student["$student"]["$category"]['stats']['totalpoints'] = $main_category['stats']['totalpoints'];
                        $grades_by_student["$student"]["$category"]['stats']['weight'] = $main_category['stats']['weight'];
                        $grades_by_student["$student"]["$category"]['stats']['grade_items'] = $main_category['stats']['grade_items'];
                        if (!isset($grades_by_student["$student"]["$category"]['stats']['points'])) {
                            $grades_by_student["$student"]["$category"]['stats']['points'] = '-';
                        }
                        else {
                            // points are set... see if the current category is using drop the x lowest and do so
                            if ($main_category['stats']['drop'] != 0) {
                                $grades_by_student["$student"]["$category"]['stats']['allgrades'] = grade_drop_lowest($grades_by_student["$student"]["$category"]['stats']['allgrades'], $main_category['stats']['drop'], $main_category['stats']['grade_items']);
                                if ($grades_by_student["$student"]["$category"]['stats']['points'] != '-') {
                                    $cat_points = 0;
                                    $count_grades = explode(',',$grades_by_student["$student"]["$category"]['stats']['allgrades']);
                                    foreach($count_grades as $grade) {
                                        $cat_points = $cat_points + $grade;
                                    }
                                    $grades_by_student["$student"]["$category"]['stats']['points'] = $cat_points;
                                }
                            }
                        }
        
                        // add any bonus points for the category
                        if ($all_categories["$category"]['stats']['bonus_points'] != 0) {
                            $grades_by_student["$student"]["$category"]['stats']['points'] = $grades_by_student["$student"]["$category"]['stats']['points'] + $all_categories["$category"]['stats']['bonus_points'];
                        }
        
                        foreach($main_category as $assignment => $items) {
                            if ($assignment != 'stats') {
                                if(!isset($grades_by_student["$student"]["$category"]["$assignment"]['maxgrade'])) {
                                    $grades_by_student["$student"]["$category"]["$assignment"]['maxgrade'] = $all_categories["$category"]["$assignment"]['grade_against'];
                                }
                                if(!isset($grades_by_student["$student"]["$category"]["$assignment"]['grade'])) {
                                    $grades_by_student["$student"]["$category"]["$assignment"]['grade'] = '-';
                                    $grades_by_student["$student"]["$category"]["$assignment"]['sort_order'] = $all_categories["$category"]["$assignment"]['sort_order'];
                                }
                            }            
                        }
                    } // end groupmember if
                    else {
                        // unset grade since they are not in the selected group.
                        unset($grades_by_student["$student"]);
                    }
                }
            }
        }

        // set the total coursepoints
        $all_categories['stats']['weight'] = 0;
        $all_categories['stats']['totalpoints'] = 0;
        foreach($all_categories as $category => $info) {
            if ($category != 'stats') {
                $all_categories['stats']['weight'] = $all_categories['stats']['weight'] + $all_categories["$category"]['stats']['weight'];
                $all_categories['stats']['totalpoints'] = $all_categories['stats']['totalpoints'] + $all_categories["$category"]['stats']['totalpoints'];
            }
        }
        
        // set each individuals total points by category so we can then exclude some grades if set to use exceptions
        if (isset($grades_by_student)) {
            foreach($grades_by_student as $student => $categories) {
                foreach($all_categories as $category => $assignments) {
                    if ($category != 'stats') {
                        $grades_by_student["$student"]["$category"]['stats']['totalpoints'] = $all_categories["$category"]['stats']['totalpoints'];
                    }
                }
                $grades_by_student["$student"]['student_data']['totalpoints'] = $all_categories['stats']['totalpoints'];
            }
        }

        // take into account any excluded grade_items
        $strexcluded = get_string('excluded', 'grades');
        $exceptions = grade_get_exceptions($course->id);
        if (isset($exceptions) && $exceptions) {
            foreach($exceptions as $exception) {
                if (isset($grades_by_student["$exception->userid"])) {
                    if ($grades_by_student["$exception->userid"]["$exception->catname"]) {
                        $assgn = get_record($exception->modname, 'id', $exception->cminstance, 'course', $course->id);
                        $grades_by_student["$exception->userid"]['student_data']['totalpoints'] = $grades_by_student["$exception->userid"]['student_data']['totalpoints'] - $all_categories["$exception->catname"]["$assgn->name"]['maxgrade'];
                        $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] = $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] - $all_categories["$exception->catname"]["$assgn->name"]['grade_against'];
                        $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] = $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] - 1;
                        if ($grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] < 0) {
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] = 0;
                        }
                        $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['points'] = $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['points'] - $grades_by_student["$exception->userid"]["$exception->catname"]["$assgn->name"]['grade'];
                        $grades_by_student["$exception->userid"]["$exception->catname"]["$assgn->name"]['maxgrade'] = $strexcluded;
                        $grades_by_student["$exception->userid"]["$exception->catname"]["$assgn->name"]['grade'] = $strexcluded;
                        // see if they are excluded entirely from a category
                        if ($grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] == 0) {
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] = $strexcluded;
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['percent'] = $strexcluded;
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['points'] = $strexcluded;
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['weight'] = $strexcluded;
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['weighted'] = $strexcluded;
                        }
                    }
                }
                else {
                    // the user had exceptions, but was unenrolled from the course... we could delete it here, but we will leave it because the user may be re-added to the course
                }
            }
        }

        if (isset($grades_by_student)) {
            foreach($grades_by_student as $student => $categories) {
                $grades_by_student["$student"]['student_data']['points'] = '-';
                $grades_by_student["$student"]['student_data']['totalpoints'] = 0;
                $grades_by_student["$student"]['student_data']['weight'] = 0;
                $grades_by_student["$student"]['student_data']['weighted'] = 0;
                foreach($categories as $category => $assignments) {
                    if ($category != 'student_data') {
                        // set the student's total points earned 
                        if ($grades_by_student["$student"]["$category"]['stats']['points'] != $strexcluded) {
                            if ($grades_by_student["$student"]["$category"]['stats']['points'] != '-') {
                                $grades_by_student["$student"]['student_data']['points'] = $grades_by_student["$student"]['student_data']['points'] + $grades_by_student["$student"]["$category"]['stats']['points'];
                            }
                            $grades_by_student["$student"]['student_data']['totalpoints'] = $grades_by_student["$student"]['student_data']['totalpoints'] + $grades_by_student["$student"]["$category"]['stats']['totalpoints'];
                        }
                
                        // set percents and weights for each assignment
                        foreach($assignments as $assignment => $info) {
                            if ($assignment != 'stats') {
                                if ($grades_by_student["$student"]["$category"]["$assignment"]['grade'] != $strexcluded) {    
                                    if ($grades_by_student["$student"]["$category"]["$assignment"]['maxgrade'] != 0) {
                                        $grades_by_student["$student"]["$category"]["$assignment"]['percent'] = round($grades_by_student["$student"]["$category"]["$assignment"]['grade']/$grades_by_student["$student"]["$category"]["$assignment"]['maxgrade']*100,2);
                                    }
                                    else {
                                        $grades_by_student["$student"]["$category"]["$assignment"]['percent'] = 0;
                                    }
                                    if ($grades_by_student["$student"]["$category"]['stats']['totalpoints'] != 0) {
                                        $grades_by_student["$student"]["$category"]["$assignment"]['weight'] = round($all_categories["$category"]['stats']['weight']*($grades_by_student["$student"]["$category"]["$assignment"]['maxgrade']/$grades_by_student["$student"]["$category"]['stats']['totalpoints']),2);
                                    }
                                    else {
                                        $grades_by_student["$student"]["$category"]["$assignment"]['weight'] = 0.00;
                                    }
                                    if ($grades_by_student["$student"]["$category"]["$assignment"]['weight'] != 0) {
                                        $grades_by_student["$student"]["$category"]["$assignment"]['weighted'] = round($grades_by_student["$student"]["$category"]["$assignment"]['percent']*$grades_by_student["$student"]["$category"]["$assignment"]['weight']/100,2);
                                    }
                                    else {
                                        // should only be here if this is extra credit
                                        $grades_by_student["$student"]["$category"]["$assignment"]['weighted'] = 0.00;
                                    }
                                }
                                else {
                                    $grades_by_student["$student"]["$category"]["$assignment"]['percent'] = $strexcluded;
                                    $grades_by_student["$student"]["$category"]["$assignment"]['weight'] = $strexcluded;
                                    $grades_by_student["$student"]["$category"]["$assignment"]['weighted'] = $strexcluded;
                                }
                            }
                        }
                        // set the percent and weight per category
                        if ($grades_by_student["$student"]["$category"]['stats']['totalpoints'] != 0 ) {
                            $grades_by_student["$student"]["$category"]['stats']['percent']  = round($grades_by_student["$student"]["$category"]['stats']['points']/$grades_by_student["$student"]["$category"]['stats']['totalpoints']*100,2);
                            $grades_by_student["$student"]["$category"]['stats']['weighted'] = round($grades_by_student["$student"]["$category"]['stats']['points']/$grades_by_student["$student"]["$category"]['stats']['totalpoints']*$grades_by_student["$student"]["$category"]['stats']['weight'],2);
                        }
                        else {
                            if ($grades_by_student["$student"]["$category"]['stats']['totalpoints'] != $strexcluded) {
                                $grades_by_student["$student"]["$category"]['stats']['percent'] = 0.00;
                                $grades_by_student["$student"]["$category"]['stats']['weighted'] = 0.00;
                            }
                        }
                        
                        // set students overall weight (this is what percent they will be graded against)
                        if ($grades_by_student["$student"]["$category"]['stats']['weight'] != $strexcluded) {
                            $grades_by_student["$student"]['student_data']['weight'] = $grades_by_student["$student"]['student_data']['weight'] + $grades_by_student["$student"]["$category"]['stats']['weight'];
                        }
                        
                        // set the students total categories towards point total we have to defer the percent calculation until we know what total weight they should be graded against since they may
                        // be excluded from a whole category.
                        if ($all_categories["$category"]['stats']['totalpoints'] != 0)  {
                            $grades_by_student["$student"]['student_data']['weighted'] = $grades_by_student["$student"]['student_data']['weighted'] + $grades_by_student["$student"]["$category"]['stats']['weighted'];
                        }
                    }
                    
    
                }
    
                // set the percent and weight overall
                if ($grades_by_student["$student"]['student_data']['totalpoints'] != 0 && $grades_by_student["$student"]['student_data']['totalpoints'] != $strexcluded) {
                    $grades_by_student["$student"]['student_data']['percent'] = round($grades_by_student["$student"]['student_data']['points']/$grades_by_student["$student"]['student_data']['totalpoints']*100,2);
                    if ($grades_by_student["$student"]['student_data']['weight'] != 0) {
                        $grades_by_student["$student"]['student_data']['weighted'] = round($grades_by_student["$student"]['student_data']['weighted']/$grades_by_student["$student"]['student_data']['weight']*100,2);
                    }
                    else {
                        $grades_by_student["$student"]['student_data']['weighted'] = 0.00;
                    }
                }
                else if ($grades_by_student["$student"]['student_data']['totalpoints'] == 0) {
                    $grades_by_student["$student"]['student_data']['percent'] = 0.00;
                }
                
            }
        }
        
        if (isset($grades_by_student)) {
            $sort = optional_param('sort','default');
            
            switch ($sort) {
                case 'highgrade_category':
                    uasort($grades_by_student, 'grade_sort_by_highgrade_category');
                    break;
                case 'highgrade_category_asc':
                    uasort($grades_by_student, 'grade_sort_by_highgrade_category_asc');
                    break;
                case 'highgrade':
                {
                    if ($preferences->use_weighted_for_letter == 1) {
                        uasort($grades_by_student, 'grade_sort_by_weighted');
                    }
                    else {
                        uasort($grades_by_student, 'grade_sort_by_percent');
                    }
                    break;
                }
                case 'points':
                    uasort($grades_by_student, 'grade_sort_by_points');
                    break;
                case 'points_asc':
                    uasort($grades_by_student, 'grade_sort_by_points_asc');
                    break;
                case 'weighted':
                    uasort($grades_by_student, 'grade_sort_by_weighted');
                    break;
                case 'weighted_asc':
                    uasort($grades_by_student, 'grade_sort_by_weighted_asc');
                    break;
                case 'percent':
                    uasort($grades_by_student, 'grade_sort_by_percent');
                    break;
                case 'percent_asc':
                    uasort($grades_by_student, 'grade_sort_by_percent_asc');
                    break;
                case 'highgrade_asc':
                {
                    if ($preferences->use_weighted_for_letter == 1) {
                        uasort($grades_by_student, 'grade_sort_by_weighted_asc');
                    }
                    else {
                        uasort($grades_by_student, 'grade_sort_by_percent_asc');
                    }
                    break;
                }
                case 'firstname':
                    uasort($grades_by_student, 'grade_sort_by_firstname');
                    break;
                default:
                    uasort($grades_by_student, 'grade_sort_by_lastname');
            }
        }
        else {
            $grades_by_student = 0;
        }        
        //print_object($grades_by_student);
        //print_object($all_categories);
        $retval = array($grades_by_student, $all_categories);
    }
    else {
        $retval = array(0,0);
        // print "<center><font color=red>Could not find any graded items for this course.</font></center>";
    }
    return $retval;
}

function grade_drop_lowest($grades, $drop, $total) {
    // drops the lowest $drop numbers from the comma seperated $grades making sure that if $grades has 
    // fewer items than $total that we don't drop too many
    $grade_array = explode(',',$grades);

    $actually_drop = (count($grade_array) - $total);
    if ($actually_drop > 0) {
        rsort($grade_array);

        for($i=0; $i < (count($grade_array) - $actually_drop); $i++) {
            $ret_grades["$i"] = $grade_array["$i"];
        }
        if ($ret_grades) {
            $grades = implode(',',$ret_grades);
        }
        else {
            // set grades to 0... they decided to drop too many
            $grades = 0;
        }
    }
    return $grades;    
}

function grade_get_grades() {
    global $CFG;
    global $course;
    $mods = grade_get_grade_items($course->id);
    $preferences = grade_get_preferences();
    
    if ($mods) {
        foreach ($mods as $mod)    {
            // hidden is a gradebook setting for an assignment and visible is a course_module setting 
            if (($mod->hidden != 1 && $mod->visible==1) or (isteacher($course->id) && $preferences->show_hidden==1)) {
                $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";
                if (file_exists($libfile)) {
                    require_once($libfile);
                    $gradefunction = $mod->modname."_grades";
                    if ($grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"] = $gradefunction($mod->cminstance)) {
                        // added grades for particular mod
                        // now get the grade_item modifiers ie. scale_grade and extra credit
                        $scale_grade = get_record('grade_item', 'courseid', $course->id, 'cminstance', $mod->cminstance, 'modid', $mod->modid);

                        if (isset($scale_grade)) {
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->scale_grade = $scale_grade->scale_grade;
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->extra_credit = $scale_grade->extra_credit;
                        }
                        else {
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->scale_grade = 1.00;
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->extra_credit = 0;
                        }
                        
                        if ($mod->hidden != 1 && $mod->visible==1) {
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->hidden = 0;
                        }
                        else {
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->hidden = 1;
                        }
                        
                        $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->sort_order = $scale_grade->sort_order;
                        
                        // I don't think this should be necessary but it appears that the forum doesn't follow the grades API properly it returns blank or NULL when it 
                        // should return a value for maxgrade according to the moodle API... so if it doesn't want to give us a grade let's not use it.
                        // this happens when grading is set to a non-numeric for a forum ie. uses "seperate and connected ways of knowing"
                        if ($grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->maxgrade == '')
                        {
                            $grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]->maxgrade = 100;
                            //unset($grades["$mod->cname"]["$mod->modid"]["$mod->cminstance"]);
                        }
                    }
                    else {
                        // delete this item from the grade_item table since it was deleted through the mod interface
                        delete_records('grade_item', 'modid', $mods->modid, 'courseid', $course->id);
                    }                
                }
                else {
                    //print "<center><font color=red>Could not find lib file for $mod->modid</font></center>";
                }
            }
        }
    }
    else {
        // Do something here for no grades
        //print "<center><font color=red>No grades returned. It appears that there are no items with grades for this course.</font></center>";
    }
    if (isset($grades)) {
        return $grades;
    }
    else {
        return NULL;
    }    
}

function grade_set_uncategorized() {
    // this function checks to see if any mods have not been assigned a category and sets them to uncategorized.
    global $CFG;
    global $course;
    global $UNCATEGORIZED;
    $uncat = $UNCATEGORIZED;

    $uncat_id = get_record('grade_category', 'courseid', $course->id, 'name', $uncat);
    
    if (!$uncat_id) {
        // insert the uncategorized category 
        $temp->name=$uncat;
        $temp->courseid=$course->id;
        $temp->drop_x_lowest = 0;
        $temp->bonus_points = 0;
        $temp->hidden = 0;
        $temp->weight = 100.00;
        
        insert_record('grade_category', $temp);
        $uncat_id = get_record('grade_category', 'courseid', $course->id, 'name', $uncat);
        if (!$uncat_id) {
            error(get_string('errornocategorizedid','grades'));
            exit(0);
        }
    }
    

    /// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
    $itemcount = 0;
    
    // this will let us establish the order for gradebook item display
    $sort = 0;
    
    /// Search through all the modules, pulling out grade data
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    for ($i=0; $i<=$course->numsections; $i++) {
        if (isset($sections["$i"])) {   // should always be true
            $section = $sections["$i"];
            if ($section->sequence) {
                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    if (isset($mods["$sectionmod"])) {
                        $mod = $mods["$sectionmod"];
                        $instance = get_record("$mod->modname", "id", "$mod->instance");
                        $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";
                        if (file_exists($libfile)) {
                            require_once($libfile);
                            $gradefunction = $mod->modname."_grades";
                            if (function_exists($gradefunction)) {   // Skip modules without grade function
                                if ($modgrades = $gradefunction($mod->instance)) {
                                    $itemcount++;
                                    //modgrades contains student information with associated grade
                                    //print "<b>modname: $mod->modname id: $mod->id course: $mod->course</b><br>";
                                    // get instance name from db.
                                    $instance = get_record($mod->modname, 'id', $mod->instance);
                                    // see if the item is already in the category table and if it is call category select with the id so it is selected
                                    get_record('modules', 'name', $mod->modname);
                                    $item = get_record('grade_item', 'courseid', $course->id, 'modid', $mod->module, 'cminstance', $mod->instance);
                                    if (!$item) {
                                        // set the item to uncategorized in grade_item
                                        $item->courseid = $course->id;
                                        $item->category = $uncat_id->id;
                                        $item->modid = $mod->module;
                                        $item->cminstance = $mod->instance;
                                        $item->id = insert_record('grade_item', $item);
                                    }
                                    else if ($item->category == 0) {
                                        // this catches any errors where they may have some wierd category set
                                        set_field('grade_item', 'category', $uncat_id->id, 'id', $item->id);
                                    }
                                    set_field('grade_item', 'sort_order', $sort, 'id', $item->id);
                                    $sort++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// sorting functions for grades
function grade_sort_by_lastname($x,$y)
{
    //$grades_by_student["$student->userid"]['student_data']['firstname'] = $student->firstname;
    //$grades_by_student["$student->userid"]['student_data']['lastname'] = $student->lastname;
    if (strnatcasecmp($x['student_data']['lastname'],$y['student_data']['lastname']) == 0) {
        return strnatcasecmp($x['student_data']['firstname'],$y['student_data']['firstname']);
    }
    else {
        return strnatcasecmp($x['student_data']['lastname'],$y['student_data']['lastname']);
    }
}

function grade_sort_by_firstname($x,$y)
{
    //$grades_by_student["$student->userid"]['student_data']['firstname'] = $student->firstname;
    //$grades_by_student["$student->userid"]['student_data']['lastname'] = $student->lastname;
    if (strnatcasecmp($x['student_data']['firstname'],$y['student_data']['firstname']) == 0) {
        return strnatcasecmp($x['student_data']['lastname'],$y['student_data']['lastname']);
    }
    else {
        return strnatcasecmp($x['student_data']['firstname'],$y['student_data']['firstname']);
    }
}

function grade_sort_by_points($x,$y) {
    if ($x['student_data']['points'] == $y['student_data']['points']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        if ($x['student_data']['points'] > $y['student_data']['points'])
            return -1;
        else 
            return 1;
    }
}

function grade_sort_by_points_asc($x,$y) {
    if ($x['student_data']['points'] == $y['student_data']['points']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        if ($x['student_data']['points'] < $y['student_data']['points'])
            return -1;
        else 
            return 1;
    }
}

function grade_sort_by_weighted($x,$y) {
    if ($x['student_data']['weighted'] == $y['student_data']['weighted']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        if ($x['student_data']['weighted'] > $y['student_data']['weighted'])
            return -1;
        else 
            return 1;
    }
}

function grade_sort_by_percent($x,$y) {
    if ($x['student_data']['percent'] == $y['student_data']['percent']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        if ($x['student_data']['percent'] > $y['student_data']['percent'])
            return -1;
        else 
            return 1;
    }
}

function grade_sort_by_percent_asc($x,$y) {
    if ($x['student_data']['percent'] == $y['student_data']['percent']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        if ($x['student_data']['percent'] < $y['student_data']['percent'])
            return -1;
        else 
            return 1;
    }
}

function grade_sort_by_weighted_asc($x,$y) {
    if ($x['student_data']['weighted'] == $y['student_data']['weighted']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        if ($x['student_data']['weighted'] < $y['student_data']['weighted'])
            return -1;
        else 
            return 1;
    }
}
    
function grade_sort_by_highgrade_category($x,$y) {
    global $cview;
    
    if(!$cview) {
        $cview = optional_param('cview');
    }
    
    if ($x["$cview"]['stats']['points'] == $y["$cview"]['stats']['points']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        return ($y["$cview"]['stats']['points'] - $x["$cview"]['stats']['points']);
    }
}

function grade_sort_by_highgrade_category_asc($x,$y) {
    global $cview;
    
    if(!$cview)
        $cview = optional_param('cview');
    
    if ($x["$cview"]['stats']['points'] == $y["$cview"]['stats']['points']) {
        return grade_sort_by_lastname($x,$y);
    }
    else {
        return ($x["$cview"]['stats']['points'] - $y["$cview"]['stats']['points']);
    }
}



function grade_get_preferences() {
    global $course;
    global $CFG;
    // Get the preferences for the course.
    $preferences = get_record('grade_preferences', 'courseid', $course->id);
    if (!$preferences) {
        // set the default preferences (could proably just insert a record with the course id since the defaults are set in db... maybe cross db issues though? ie. postgress, oracle etc.
        $new_prefs['use_advanced'] = 0;
        $new_prefs['display_weighted']=0;
        $new_prefs['display_points']=1;
        $new_prefs['display_percent']=1;
        $new_prefs['display_letter_grade']=0;
        $new_prefs['use_weighted_for_letter']=0;
        $new_prefs['reprint_headers']=0;
        $new_prefs['display_weighted_student']=0;
        $new_prefs['display_points_student']=1;
        $new_prefs['display_percent_student']=0;
        $new_prefs['display_letter_grade_student']=0;
        $new_prefs['show_hidden']=1;
        $new_prefs['courseid']=$course->id;
        insert_record('grade_preferences', $new_prefs);
        $preferences = get_record('grade_preferences', 'courseid', $course->id);
    }
    if (($preferences->display_weighted == 1 && isteacher($course->id)) || ($preferences->display_weighted_student == 1 && !isteacher($course->id))) {
        $preferences->show_weighted = true;
    }
    else {
        $preferences->show_weighted = false;
    }
    
    if (($preferences->display_points == 1 && isteacher($course->id)) || ($preferences->display_points_student == 1 && !isteacher($course->id))) {
        $preferences->show_points = true;
    }
    else {
        $preferences->show_points = false;
    }
    
    if (($preferences->display_percent == 1 && isteacher($course->id)) || ($preferences->display_percent_student == 1 && !isteacher($course->id))) {
        $preferences->show_percent = true;
    }
    else {
        $preferences->show_percent = false;
    }
    if (($preferences->display_letter_grade == 1 && isteacher($course->id)) || ($preferences->display_letter_grade_student == 1 && !isteacher($course->id))) {
        $preferences->show_letter_grade = true;
    }
    else {
        $preferences->show_letter_grade = false;
    }
    return $preferences;
}

function grade_preferences_menu() {
    global $course;
    global $action;
    global $USER;
    global $group;
    
    if (isteacher($course->id) && $USER->editing) {
        
        if (!isset($action)) {
            $action = $_REQUEST['action'];
        }

        // remap some actions to simplify later code        
        switch ($action) {
            case 'prefs':
            case 'set_grade_preferences':
                $curaction = 'prefs';
                break;
            case 'cats':
            case 'vcats':
            case 'insert_category':
            case 'assign_categories':
            case 'delete_category':
                $curaction = 'cats';
                break;
            case 'set_grade_weights':
            case 'weights':
                $curaction = 'weights';
                break;
            case 'letters':
            case 'set_letter_grades':
                $curaction = 'letters';
                break;
            case 'view_student_grades':
            case 'view_student_category_grades':
            case 'grades':
                $curaction = 'grades';
                break;

            default:
                $curaction = 'prefs';
        }
    
        $strsetpreferences = get_string('setpreferences','grades');
        $strsetcategories = get_string('setcategories','grades');
        $strsetweights = get_string('setweights','grades');
        $strsetgradeletters = get_string('setgradeletters','grades');
        $strviewgrades = get_string('viewgrades','grades');
        $strgradeexceptions = get_string('gradeexceptions','grades');    
        
    
        print '<table align="center"><tr>';
        print '<td';
        if ($curaction == 'prefs') {
            print ' class="header"';
        }
        print "><a href=\"index.php?id=$course->id&amp;action=prefs&amp;group=$group\">$strsetpreferences</a></td>";
        print '<td';
        if ($curaction == 'cats') {
            print ' class="header"';
        }
        print "><a href=\"index.php?id=$course->id&amp;action=cats&amp;group=$group\">$strsetcategories</a></td>";
        print '<td';
        if ($curaction == 'weights') {
            print ' class="header"';
        }
        print "><a href=\"index.php?id=$course->id&amp;action=weights&amp;group=$group\">$strsetweights</a></td>";
        print '<td';
        if ($curaction == 'letters') {
            print ' class="header"';
        }
        print "><a href=\"index.php?id=$course->id&amp;action=letters&amp;group=$group\">$strsetgradeletters</a></td>";
        print '<td';
        if ($curaction == 'excepts') {
            print ' class="header"';
        }
        print "><a href=\"exceptions.php?id=$course->id&amp;action=excepts&amp;group=$group\">$strgradeexceptions</a></td>";
        print '</td></tr></table>';
    }
}

function grade_preferences_button() {
    global $CFG, $USER, $course, $action, $group, $cview;

    if (isteacher($course->id)) {
        if (!empty($USER->editing)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        
        if ($action == 'excepts') {
            $formaction = "$CFG->wwwroot/grade/exceptions.php";
        }
        else {
            $formaction = "$CFG->wwwroot/grade/index.php";
        }
        $ret = "<form target=\"$CFG->framename\" method=\"get\" action=\"$formaction\">".
               "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />";
               
        if ($edit == 'off') {
            $ret .= "<input type=\"hidden\" name=\"action\" value=\"grades\" />";
        }
        else {
            $ret .= "<input type=\"hidden\" name=\"action\" value=\"$action\" />";
        }
        $ret .= "<input type=\"hidden\" name=\"group\" value=\"$group\" />";
        if (isset($cview)) {
            $ret .= "<input type=\"hidden\" name=\"cview\" value=\"$cview\" />";
        }
        $ret .= "<input type=\"submit\" value=\"$string\" /></form>";
        return $ret;
    }
}

function grade_get_grades_menu() {
    global $course;
    global $CFG;
    global $USER;
    global $cview;

    
    $strgrades = get_string('grades', 'grades');
    $strviewgrades = get_string('viewgrades','grades');
    
    $preferences = grade_get_preferences();
    
    if (!isset($action)) {
        if (isset($_REQUEST['action'])) {
            $action = $_REQUEST['action'];
        }
        else {
            $action = 'grades';
        }
        switch ($action) {
            case 'prefs':
            case 'set_grade_preferences':
                $strcurpage = get_string('setpreferences','grades');
                break;
            case 'cats':
            case 'delete_category':
            case 'cats':
            case 'insert_category':
            case 'assign_categories':
                $strcurpage = get_string('setcategories','grades');
                break;
            case 'weights':
            case 'set_grade_weights':
                $strcurpage = get_string('setweights','grades');
                break;
            case 'set_letter_grades':
            case 'letters':
                $strcurpage = get_string('setgradeletters','grades');
                break;
            case 'excepts':
                $strcurpage = get_string('gradeexceptions', 'grades');
                break;
            default:
                unset($strcurpage);
                break;
        }
    }

    // just to make the link back to all grades maintain group info    
    if (isset($_REQUEST['group'])) {
        $group = clean_param($_REQUEST['group'], PARAM_INT);
    }
    else {
        $group = NULL;
    }
    
    $grade_menu = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>";


    if (isteacher($course->id)) {
        if ($action=='grades') {
            $grade_menu .= " -> $strgrades";
        }
        else {
            $grade_menu .= " -> <a href=\"index.php?id=$course->id&amp;action=grades&amp;group={$group}\">$strgrades</a>";
        }
        
        
        
        // if we are on a grades sub-page provide a link back (including grade preferences and grade items
    
        
        if (isset($strcurpage)) {
            $grade_menu .= " -> $strcurpage";
        }
        else if($action =='vcats') {
            // show sub category
            if (isset($cview)) {
                $grade_menu .= " -> $cview";
            }
        }
    }
    
    return $grade_menu;    
}

function grade_download_excel() {
    global $CFG;
    global $course;

    require_once("../lib/excel/Worksheet.php");
    require_once("../lib/excel/Workbook.php");
        
    $strgrades = get_string("grades");
    
    if (isteacher($course->id)) {
        // HTTP headers
        header("Content-type: application/vnd.ms-excel");
        $downloadfilename = clean_filename("$course->shortname $strgrades");
        header("Content-Disposition: attachment; filename=\"$downloadfilename.xls\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");
        
        /// Creating a workbook
        $workbook = new Workbook("-");
        $myxls =& $workbook->add_worksheet($strgrades);
        $first = 0;
        /// Print names of all the fields
        list($grades_by_student, $all_categories) = grade_get_formatted_grades();
        if ($grades_by_student != 0 && $all_categories != 0) {
            $j = 1;
            foreach($grades_by_student as $student => $categories) {
                if ($first == 0) {
                    ksort($categories);
                    $myxls->write_string(0,0,get_string("firstname"));
                    $myxls->write_string(0,1,get_string("lastname"));
                    $myxls->write_string(0,2,get_string("idnumber"));
                    $myxls->write_string(0,3,get_string("institution"));
                    $myxls->write_string(0,4,get_string("department"));
                    $myxls->write_string(0,5,get_string("email"));
                    $i = 6;
                    foreach ($categories as $category => $assignments) {
                        if ($category != 'student_data') {
                            foreach ($assignments as $assignment => $info) {
                                if ($assignment != 'stats') {
                                    $myxls->write_string(0,$i,$assignment);
                                    $i++;
                                }
                            }
                        }
                    }
                    $first++;
                }
            }
            foreach($grades_by_student as $student => $categories) {
                $myxls->write_string($j,0,$grades_by_student["$student"]['student_data']['firstname']);
                $myxls->write_string($j,1,$grades_by_student["$student"]['student_data']['lastname']);
                $myxls->write_string($j,2,$grades_by_student["$student"]['student_data']['idnumber']);
                if (isset($grades_by_student["$student"]['student_data']['institution'])) {
                    $myxls->write_string($j,3,$grades_by_student["$student"]['student_data']['institution']);
                }
                $myxls->write_string($j,4,$grades_by_student["$student"]['student_data']['department']);
                $myxls->write_string($j,5,$grades_by_student["$student"]['student_data']['email']);
                $i = 6;
                foreach($categories as $category => $assignments) {
                    if ($category != 'student_data') {
                        foreach ($assignments as $assignment => $info) {
                            if ($assignment != 'stats') {
                                // account for excluded and untaken items
                                if (is_numeric($info['grade'])) {
                                    $myxls->write_number($j,$i,$info['grade']);
                                }
                                else {
                                    $myxls->write_string($j,$i,$info['grade']);
                                }
                                $i++;
                            }
                        }
                    }
                }
                $j++;
            }
        }
        $workbook->close();
        exit;
    }
}


function grade_download_text() {
    global $CFG;
    global $course;
        
    $strgrades = get_string("grades");
    
    if (isteacher($course->id)) {
        // HTTP headers
        header("Content-type: text/plain");
        $downloadfilename = clean_filename("$course->shortname $strgrades");
        header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");
        $first = 0;        
        
        /// Print names of all the fields
        list($grades_by_student, $all_categories) = grade_get_formatted_grades();
        if ($grades_by_student != 0 && $all_categories != 0) {
            foreach($grades_by_student as $student => $categories) {
                if ($first == 0) {
                    $first++;
                    ksort($categories);
                    print get_string('firstname')."\t";
                    print get_string('lastname')."\t";
                    print get_string('idnumber')."\t";
                    print get_string('institution')."\t";
                    print get_string('department')."\t";
                    print get_string('email')."\t";
                    foreach ($categories as $category => $assignments) {
                        if ($category != 'student_data') {
                            foreach ($assignments as $assignment => $info) {
                                if ($assignment != 'stats') {
                                    print $assignment."\t";
                                }
                            }
                        }
                    }
                    print "\n";
                }
                
            }
            foreach($grades_by_student as $student => $categories) {
                print $grades_by_student["$student"]['student_data']['firstname']."\t";
                print $grades_by_student["$student"]['student_data']['lastname']."\t";
                print $grades_by_student["$student"]['student_data']['idnumber']."\t";
                if (isset($grades_by_student["$student"]['student_data']['institution'])) {
                    print $grades_by_student["$student"]['student_data']['institution'];
                }
                print "\t";
                print $grades_by_student["$student"]['student_data']['department']."\t";
                print $grades_by_student["$student"]['student_data']['email']."\t";
                foreach($categories as $category => $assignments) {
                    if ($category != 'student_data') {
                        foreach ($assignments as $assignment => $info) {
                            if ($assignment != 'stats') {
                                // account for excluded and untaken items
                                print $info['grade']."\t";
                            }
                        }
                    }
                }
                print "\n";
            }
        }
        exit;
    }
}

function grade_get_stats($category='all') {
    list($grades_by_student, $all_categories) = grade_get_formatted_grades();

    if ($grades_by_student != 0 && $all_categories != 0) {
        switch($category) {
            case 'all':
            {
                //populate the sum of student points, # items and totalpoints for each category
                foreach($grades_by_student as $student=>$categories) {
                    foreach($categories as $cur_category=>$assignments) {
                        if($category != 'student_data') {
                            if (isset($assignments['stats'])) {
                                if (isset($stats[$cur_category]['sum'])) {
                                    $stats[$cur_category]['sum'] = $stats[$cur_category]['sum'] + $assignments['stats']['points'];
                                }
                                else {
                                    $stats[$cur_category]['sum'] = $assignments['stats']['points'];
                                }
                                $stats[$cur_category]['items'] = $assignments['stats']['grade_items'];
                                $stats[$cur_category]['totalpoints'] = $assignments['stats']['totalpoints'];
                                $stats[$cur_category]['weight'] = $all_categories[$cur_category]['stats']['weight'];
                            }
                        }
                    }
                }
                // populate the overall sum,items and totalpoints 
                foreach($stats as $cur_category=>$info) {
                    if($cur_category != 'all' && $cur_category != 'student_data') {
                        if (isset($stats['all'])) {
                            $stats['all']['sum'] = $stats['all']['sum'] + $stats[$cur_category]['sum'];
                            $stats['all']['items']  = $stats['all']['items'] + $stats[$cur_category]['items'];
                            $stats['all']['totalpoints'] = $stats['all']['totalpoints'] + $stats[$cur_category]['totalpoints'];
                            $stats['all']['weighted_sum'] = $stats['all']['weighted_sum'] + ($stats[$cur_category]['sum']/($stats[$cur_category]['totalpoints']))*$stats[$cur_category]['weight'];
                        }
                        else {
                            $stats['all']['sum'] = $stats[$cur_category]['sum'];
                            $stats['all']['items']  = $stats[$cur_category]['items'];
                            $stats['all']['totalpoints'] = $stats[$cur_category]['totalpoints'];
                            $stats['all']['weighted_sum'] = ($stats[$cur_category]['sum']/($stats[$cur_category]['totalpoints']))*$stats[$cur_category]['weight'];
                        }
                    } 
                }
                $stats['all']['students'] = count($grades_by_student);
                $stats['all']['average'] = $stats['all']['sum'] / $stats['all']['students'];
                $stats['all']['average_weighted'] = $stats['all']['weighted_sum']/$stats['all']['students'];
                
                // calculate the average squared deviation and populate a list of all scores while we're at it
                $stats['all']['avgsqddev'] = 0;
                $stats['all']['avgsqddev_weighted'] = 0;
                foreach($grades_by_student as $student=>$categories) {
                    foreach($categories as $cur_category=>$assignments) {
                        if ($cur_category != 'student_data') {
                            $stats['all']['avgsqddev'] = $stats['all']['avgsqddev'] + pow(($grades_by_student[$student]['student_data']['points']-$stats['all']['average']),2);
                            $stats['all']['avgsqddev_weighted'] = $stats['all']['avgsqddev_weighted'] + pow(($grades_by_student[$student]['student_data']['weighted']-$stats['all']['average_weighted']),2);
                        }
                    }
                    if (isset($stats['all']['all_scores'])) {
                        $stats['all']['all_scores'] .= ','.$grades_by_student[$student]['student_data']['points'];
                        $stats['all']['all_scores_weighted'] .= ','.$grades_by_student[$student]['student_data']['weighted'];
                    }
                    else {
                        $stats['all']['all_scores'] = $grades_by_student[$student]['student_data']['points'];
                        $stats['all']['all_scores_weighted'] = $grades_by_student[$student]['student_data']['weighted'];
                    }
                }
                $stats['all']['avgsqddev']=$stats['all']['avgsqddev']/$stats['all']['students'];
                $stats['all']['avgsqddev_weighted']=$stats['all']['avgsqddev_weighted']/$stats['all']['students'];
                $stats['all']['stddev'] = sqrt($stats['all']['avgsqddev']);
                $stats['all']['stddev_weighted'] = sqrt($stats['all']['avgsqddev_weighted']);
                $stats['all']['mode'] = grade_mode($stats['all']['all_scores']);
                $stats['all']['mode_weighted'] = grade_mode($stats['all']['all_scores_weighted']);
                
                // make sure the mode is not set to every score
                if(count($stats['all']['mode']) == count($grades_by_student)) {
                    $stats['all']['mode'] = get_string('nomode','grade');
                }
                if(count($stats['all']['mode_weighted']) == count($grades_by_student)) {
                    $stats['all']['mode_weighted'] = get_string('nomode','grade');
                }
                break;
            }
            default:
            {
                // get the stats for category
                //populate the sum of student points, # items and totalpoints for each category
                foreach($grades_by_student as $student=>$categories) {
                        if(isset($grades_by_student[$student][$category]['stats'])) {
                            if (isset($stats[$category]['sum'])) {
                                $stats[$category]['sum'] = $stats[$category]['sum'] + $grades_by_student[$student][$category]['stats']['points'];
                            }
                            else {
                                $stats[$category]['sum'] = $grades_by_student[$student][$category]['stats']['points'];
                            }
                            $stats[$category]['items'] = $grades_by_student[$student][$category]['stats']['grade_items'];
                            $stats[$category]['totalpoints'] = $grades_by_student[$student][$category]['stats']['totalpoints'];
                        }
                }
                $stats[$category]['students'] = count($grades_by_student);
                $stats[$category]['average'] = $stats[$category]['sum']/$stats[$category]['students'];
                
                // calculate the average squared deviation and populate a list of all scores too
                $stats[$category]['avgsqddev'] = 0;
                foreach($grades_by_student as $student=>$categories) {
                    foreach($categories as $cur_category=>$assignment) {
                        if ($cur_category != 'student_data') {
                            if ($grades_by_student[$student][$category]['stats']['points'] == '-' || $grades_by_student[$student][$category]['stats']['points'] == get_string('grades','excluded')) {
                                // count grade as a zero
                                $stats[$category]['avgsqddev'] = $stats[$category]['avgsqddev'] + pow(($stats[$category]['average']),2);
                            }
                            else {
                                $stats[$category]['avgsqddev'] = $stats[$category]['avgsqddev'] + pow(($grades_by_student[$student][$category]['stats']['points']-$stats[$category]['average']),2);
                            }
                        }
                    }

                    if (isset($stats[$category]['all_scores'])) {
                        $stats[$category]['all_scores'] .= ','.$grades_by_student[$student][$category]['stats']['points'];
                    }
                    else {
                        $stats[$category]['all_scores'] = $grades_by_student[$student][$category]['stats']['points'];
                    }
                }
                $stats[$category]['avgsqddev'] = $stats[$category]['avgsqddev']/$stats[$category]['students'];
                $stats[$category]['stddev'] = sqrt($stats[$category]['avgsqddev']);
                $stats[$category]['mode'] = grade_mode($stats[$category]['all_scores']);
                break;
            }
        } // end switch
        // do a little cleanup 
        $stats[$category]['stddev'] = sprintf("%0.2f", $stats[$category]['stddev']);
        $stats[$category]['average'] = sprintf("%0.2f", $stats[$category]['average']);
        $stats[$category]['max'] = max(explode(',',$stats[$category]['all_scores'])); 
        $stats[$category]['min'] = min(explode(',',$stats[$category]['all_scores']));
        $stats[$category]['median'] = explode(',',$stats[$category]['all_scores']);
        
        if (isset($stats[$category]['stddev_weighted'])) {
            $stats[$category]['stddev_weighted'] = sprintf("%0.2f", $stats[$category]['stddev_weighted']);
        }
        if (isset($stats[$category]['average_weighted'])) {
            $stats[$category]['average_weighted'] = sprintf("%0.2f", $stats[$category]['average_weighted']);
        }
        if (isset($stats[$category]['max_weighted'])) {
            $stats[$category]['max_weighted'] = max(explode(',',$stats[$category]['all_scores_weighted'])); 
        }
        if (isset($stats[$category]['min_weighted'])) {
            $stats[$category]['min_weighted'] = min(explode(',',$stats[$category]['all_scores_weighted']));
        }
        
        if (isset($stats[$category]['all_scores_weighted'])) {
            $stats[$category]['median_weighted'] = explode(',',$stats[$category]['all_scores_weighted']);
        }
        else {
        
        }

        
        sort($stats[$category]['median']);

        if (count($stats[$category]['median'])/2 == floor(count($stats[$category]['median'])/2) ) {
            // even number of scores
            $temp = $stats[$category]['median'][count($stats[$category]['median'])/2-1] + $stats[$category]['median'][count($stats[$category]['median'])/2];
            $temp = $temp/2;
        }
        else {
            // odd number of scores
            $temp = $stats[$category]['median'][floor(count($stats[$category]['median'])/2)];
        }        
        unset($stats[$category]['median']);
        $stats[$category]['median'] = $temp;
        
        if (isset($stats[$category]['median_weighted'])) {
            if (count($stats[$category]['median_weighted'])/2 == floor(count($stats[$category]['median_weighted'])/2)) {
                // even number of scores
                $temp = $stats[$category]['median_weighted'][count($stats[$category]['median_weighted'])/2-1] + $stats[$category]['median_weighted'][count($stats[$category]['median_weighted'])/2+1];
                $temp = $temp/2;
            }
            else {
                // odd number of scores
                $temp = $stats[$category]['median_weighted'][floor(count($stats[$category]['median_weighted'])/2)];
            }         
            unset($stats[$category]['median_weighted']);
            $stats[$category]['median_weighted'] = $temp;
        }
    }
    return $stats;
}

// returns a comma seperated list of the most common values in $items, $items is expected to be a comma sperated list of numbers
function grade_mode($items) {
    $all_scores = explode(',',$items);
    foreach($all_scores as $value) {
        if (isset($frequency[$value])) {
            $frequency[$value]++;
        }
        else {
            $frequency[$value] = 1;
        }
    }
    $max = max($frequency);
    foreach($frequency as $key=>$value) {
        if ($value == $max) {
            if (isset($retval)) {
                $retval .= ', '.$key;
            }
            else {
                $retval = $key;
            }
        }
    }
    return $retval;
}


function grade_stats() {
    global $CFG;
    global $course;
    global $USER;
    global $preferences;

    if (!isset($category)) {
        $category = clean_param($_REQUEST['category'], PARAM_CLEAN);
    }
    
    $stats = grade_get_stats($category);

    // output our data    
    print_header();
    print '<table align="center"><tr><th colspan="3">'.$category.' '.get_string('stats','grades').'</th></tr>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<tr><th>&nbsp;</th><th>'.get_string('points','grades').'<th>'.get_string('weight','grades').'</th></tr>';            
    }

    print '<tr><td align="right">'.get_string('max','grades').':</td><td align="right">'.$stats[$category]['max'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<td align="right">'.$stats[$category]['max_weighted'].'</td>';            
    }
    print '</tr>';
    
    print '<tr><td align="right">'.get_string('min','grades').':</td><td align="right">'.$stats[$category]['min'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<td align="right">'.$stats[$category]['min_weighted'].'</td>';            
    }
    print '</tr>';
    
    print '<tr><td align="right">'.get_string('average','grades').':</td><td align="right">'.$stats[$category]['average'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<td align="right">'.$stats[$category]['average_weighted'].'</td>';            
    }
    print '</tr>';
    
    print '<tr><td align="right">'.get_string('median','grades').':</td><td align="right">'.$stats[$category]['median'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<td align="right">'.$stats[$category]['median_weighted'].'</td>';            
    }
    print '</tr>';
    
    print '<tr><td align="right">'.get_string('mode','grades').':</td><td align="right">'.$stats[$category]['mode'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<td align="right">'.$stats[$category]['mode_weighted'].'</td>';            
    }
    print '</tr>';
    
    print '<tr><td align="right">'.get_string('standarddeviation','grades').':</td><td align="right">'.$stats[$category]['stddev'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        print '<td align="right">'.$stats[$category]['stddev_weighted'].'</td>';            
    }
    print '</tr>';
    print '</table>';
    //print_footer();
}

function grade_view_category_grades($view_by_student) {
    global $CFG;
    global $course;
    global $USER;
    global $preferences;
    global $group;
    global $UNCATEGORIZED;
    
    if (!isteacher($course->id)) {
        $view_by_student = $USER->id;
    }

    if ($preferences->use_advanced == 0) {
        $cview = $UNCATEGORIZED;
    }
    else {
        $cview=clean_param($_REQUEST['cview'], PARAM_CLEAN);
    }
    
    if ($cview) {
        list($grades_by_student, $all_categories) = grade_get_formatted_grades();

        if ($grades_by_student != 0 && $all_categories != 0) {
            // output a form for the user to download the grades.
            grade_download_form();

            if ($view_by_student != -1) {
                // unset all grades except for this student
                foreach ($grades_by_student as $student=>$junk) {
                    if($student != $view_by_student) {
                        unset($grades_by_student[$student]);
                    }
                }
            }

            if (isteacher($course->id)) {
                $grade_columns = $preferences->display_weighted + $preferences->display_points + $preferences->display_percent;
            }
            else {
                $grade_columns = $preferences->display_weighted_student + $preferences->display_points_student + $preferences->display_percent_student;
            }

            $first = 0;
            //$maxpoints = 0;
            $maxpercent = 0;
            $reprint = 0;
            if (isteacher($course->id)) {
                $student_heading_link = get_string('student','grades');
                //only set sorting links if more than one student displayed.
                if ($view_by_student == -1) {
                    $student_heading_link .='<br /><a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=lastname"><font size=-2>'.get_string('sortbylastname','grades').'</font></a>';
                    $student_heading_link .= '<br /><a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=firstname"><font size=-2>'.get_string('sortbyfirstname','grades').'</font></a>';
                }
                else {
                    $student_heading_link .= '<br /><a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;cview='.$cview.'"><font size="-2">'.get_string('showallstudents','grades').'</font></a>';
                }
            }
            print '<table align="center" border="1">';
            if (isteacher($course->id)) {
                $header = '<tr><th rowspan="2">'.$student_heading_link.'</th>';
            }
            else {
                $header = '<tr>';
            }
            $header1 = '<tr>';
            
            // to keep track of what we've output
            $colcount = 0;
            $rowcount = 0;
            $reprint = 0;
            
            // this next section is to display the items in the course order
            foreach($grades_by_student as $student => $categories) {
                if (isset($item_order)) {
                    // we already have the sort order let's jump out
                    break;
                }
                $item_order = array();
                foreach($categories as $category => $items) {
                    if ($category == $cview) {
                        foreach ($items as $assignment=>$points) {
                            if ($assignment != 'stats') {
                                $temp = $points['sort_order'];
                                $item_order[$temp] = $assignment;
                            }
                        }
                    }
                }
            }
            ksort($item_order);
            
            foreach($grades_by_student as $student => $categories) {
                
                if ($preferences->reprint_headers != 0 && $reprint >= $preferences->reprint_headers) {
                    print $header.$header1;
                    $reprint=0;
                }
                
                // highlight every 3 rows for readability
                if ($rowcount < 3) {
                    $row = '<tr class="header">';
                    $rowcount ++;
                }
                else {
                    $row = '<tr>';
                    $rowcount++;
                    if ($rowcount >= 6) {
                        $rowcount = 0;
                    }
                }
                // set the links to student information based on multiview or individual... if individual go to student info... if many go to individual grades view.
                if (isteacher($course->id)) {
                    if ($view_by_student != -1) {
                        $student_link = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$student.'&amp;course='.$course->id.'">';
                    }
                    else {
                        $student_link = '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;student='.$student.'&amp;cview='.$cview.'">';
                    }
                    $student_link .= $grades_by_student[$student]['student_data']['lastname'].', '.$grades_by_student[$student]['student_data']['firstname'].'</a>';
                    $row .= '<td>'.$student_link.'</td>';
                }
                
                foreach($categories as $category => $items) {
                    if ($category == $cview) {
                        // make sure that the grades come out in the same order
                        foreach($item_order as $order=>$assignment)
                        {
                            if ($assignment != 'stats') {
                                if ($first == 0) {
                                    $colcount++;
                                    $link_id = grade_get_module_link($course->id, $all_categories[$category][$assignment]['cminstance'], $all_categories[$category][$assignment]['modid']);

                                    $link = $CFG->wwwroot.'/mod/'.$all_categories[$category][$assignment]['modname'].'/view.php?id='.$link_id->id;
                                    if ($all_categories[$category][$assignment]['hidden'] == 0) {
                                        $header .= '<th colspan="'.$grade_columns.'"><a href="'.$link.'">'.$assignment.'</a>';
                                    }
                                    else {
                                        $header .= '<th style="background: #FFFFFF;" colspan="'.$grade_columns.'"><a class="dimmed" href="'.$link.'">'.$assignment.'</a>';
                                    }
                                    if ($all_categories[$category][$assignment]['extra_credit'] == 1) {
                                        $header .= '<font size ="-2">('.get_string('extracredit','grades').')</font>'; 
                                    }
                                    $header .='</th>';
                                    if ($preferences->show_points) {
                                        $header1 .= '<th>'.get_string('points','grades').'('. $all_categories[$category][$assignment]['maxgrade'];
                                        if ($all_categories[$category][$assignment]['grade_against'] != $all_categories[$category][$assignment]['maxgrade']) {
                                            $header1 .= ')('. $all_categories[$category][$assignment]['grade_against'];
                                        }
                                        $header1 .= ')</th>';
                                    }
                                                                        
                                    if($preferences->show_percent)    {
                                        if ($all_categories[$category][$assignment]['grade_against'] != $all_categories[$category][$assignment]['maxgrade']) {
                                            $header1 .= '<th>'.get_string('scaledpct','grades').'</td>';
                                        }
                                        else {
                                            $header1 .= '<th>'.get_string('rawpct','grades').'</th>';
                                        }
                                    }
                                    if ($preferences->show_weighted) {
                                        if ($all_categories[$category]['stats']['totalpoints'] != 0) {
                                            $cur_weighted_max = sprintf("%0.2f", $all_categories[$category][$assignment]['grade_against']/$all_categories[$category]['stats']['totalpoints']*$all_categories[$category]['stats']['weight']);
                                        }
                                        else {
                                            $cur_weighted_max = 0;
                                        }
                                        $header1 .= '<th>'.$cur_weighted_max.get_string('pctoftotalgrade','grades').'</th>';
                                    }
                                }

                                // display points 
                                if ($preferences->show_points) { 
                                    $row .= '<td align="right">' . $items[$assignment]['grade'] . '</td>';
                                }

                                if ($preferences->show_percent) {
                                    $row .= '<td align="right">'. $items[$assignment]['percent'].'%</td>';
                                }
                                
                                if ($preferences->show_weighted) {
                                    $row .= '<td align="right">'.$items[$assignment]['weighted'].'%</td>';
                                }
                            }
                        }
                    }
                }
                
                if ($first == 0) {
                    if (isteacher($course->id) && $view_by_student == -1) {
                        $total_sort_link = '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=highgrade_category"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('highgradedescending','grades').'" /></a>';
                        $total_sort_link .= '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=highgrade_category_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('highgradeascending','grades').'" /></a>';
                    }
                    else {
                        $total_sort_link = '';
                    }
                    
                    $stats_link = '<a href="javascript:void(0)"onclick="window.open(\'?id='.$course->id.'&amp;action=stats&amp;group='.$group.'&amp;category='.$cview.'\',\''.get_string('statslink','grades').'\',\'height=200,width=300,scrollbars=no\')"><font size=-2>'.get_string('statslink','grades').'</font></a>';
                    if ($all_categories[$cview]['stats']['drop'] != 0) {
                        $header .= '<th colspan="'.$grade_columns.'">'.get_string('total','grades').'&nbsp; (Lowest '. $all_categories[$cview]['stats']['drop']. ' Dropped)'.$total_sort_link.' '.$stats_link.'</th>';
                    }
                    else {
                        $header .= '<th colspan="'.$grade_columns.'">'.get_string('total','grades').'&nbsp;'.$total_sort_link.' '.$stats_link.'</th>';
                    }
                    
                    if ($preferences->show_points) {
                        $header1 .= '<th>'.get_string('points','grades').'('.$all_categories[$cview]['stats']['totalpoints'].')';
                        if ($all_categories[$cview]['stats']['bonus_points'] != 0) {
                            $header1 .='(+'.$all_categories[$cview]['stats']['bonus_points'].')';
                        }
                        $header1 .='</th>';
                    }
                    if ($preferences->show_percent) {
                        $header1 .= '<th>'.get_string('percent','grades').'</th>';
                    }
                    
                    
                    if ($preferences->show_weighted) {
                        $header1 .= '<th>'.$all_categories[$cview]['stats']['weight'].get_string('pctoftotalgrade','grades').'</th>';
                    }
                    
                    if (isteacher($course->id) ) {
                        $header .= '<th rowspan="2">'.$student_heading_link.'</th></tr>';
                    }
                    else {
                        $header .= '</tr>';
                    }
                    
                    //adjust colcount to reflect the actual number of columns output
                    $colcount++; // total column
                    $colcount = $colcount*$grade_columns + 2;
                    print '<tr><th colspan="'.$colcount.'"><font size="+1">';
                    if ($preferences->use_advanced != 0) {
                        print $cview.' '.get_string('grades','grades');
                    }
                    else {
                        print get_string('grades','grades');
                    }

                    print '</font>';

                    if (isteacher($course->id)) {
                        helpbutton('coursegradeteacher', get_string('gradehelp','grades'), 'gradebook');
                    }
                    else {
                        helpbutton('coursegradestudent', get_string('gradehelp','grades'), 'gradebook');
                    }
                    print '</th></tr>';
                    print $header;
                    print $header1;
                    $first = 1;
                }

                // total points for category
                if ($preferences->show_points) {
                    $row .= '<td align="right">'.$grades_by_student[$student][$cview]['stats']['points'].'</td>';
                }
                
                // total percent for category
                if ($preferences->show_percent) {
                    $row .= '<td align="right">'.$grades_by_student[$student][$cview]['stats']['percent'].'%</td>';
                }
                

                // total weighted for category
                if ($preferences->show_weighted) {
                    $row .= '<td align="right">'.$grades_by_student[$student][$cview]['stats']['weighted'].'%</td>';
                }

                if (isteacher($course->id) ) {
                    $row .= '<td>'.$student_link.'</td>';
                }
                $row .= '</tr>';
                print $row;
                $reprint++;
            }
            print '</table>';
        }
        else { // no grades returned
            error(get_string('nogradesreturned','grades'));
        }
    }
    else {
        error(get_string('nocategoryview','grades'));
    }
}

function grade_view_all_grades($view_by_student) {
// displays all grades for the course
    global $CFG;
    global $course;
    global $preferences;
    global $USER;
    global $group;
    
    if (!isteacher($course->id)) {
        $view_by_student = $USER->id;
    }
    
    list($grades_by_student, $all_categories) = grade_get_formatted_grades();

    if ($grades_by_student != 0 && $all_categories != 0) {
        // output a form for the user to download the grades.
        grade_download_form();
        
        if ($view_by_student != -1) {
            // unset all grades except for this student
            foreach ($grades_by_student as $student=>$junk) {
                if($student != $view_by_student) {
                    unset($grades_by_student[$student]);
                }
            }
        }
        if (isteacher($course->id)) {
            $grade_columns = $preferences->display_weighted + $preferences->display_points + $preferences->display_percent;
        }
        else {
            $grade_columns = $preferences->display_weighted_student + $preferences->display_points_student + $preferences->display_percent_student;
        }
        
        $first = 0;
        $total_course_points = 0;
        $maxpercent = 0;
        $reprint=0;

        print '<table align="center" border="1">';
        if (isteacher($course->id) ) {
            $student_heading_link = get_string('student','grades');
            if ($view_by_student == -1) {
                $student_heading_link .='<a href="?id='.$course->id.'&amp;action=grades&amp;group='.$group.'&amp;sort=lastname"><br /><font size="-2">'.get_string('sortbylastname','grades').'</font></a>';
                $student_heading_link .= '<a href="?id='.$course->id.'&amp;action=grades&amp;group='.$group.'&amp;sort=firstname"><br /><font size="-2">'.get_string('sortbyfirstname','grades').'</font></a>';
            }
            else {
                $student_heading_link .= '<br /><a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades"><font size="-2">'.get_string('showallstudents','grades').'</font></a>';
            }
            $header = '<tr><th rowspan="2">'.$student_heading_link.'</th>';
        }
        else {
            $header = '</tr>';
        }
        $header1 = '<tr>';
        
        $rowcount = 0;
        $colcount = 0;
        foreach($grades_by_student as $student => $categories) {
            $totalpoints = 0;
            $totalgrade = 0;
            $total_bonus_points = 0;
            if ($preferences->reprint_headers != 0 && $reprint >= $preferences->reprint_headers) {
                print $header.$header1;
                $reprint=0;
            }
            if ($rowcount < 3) {
                $row = '<tr class="header">';
                $rowcount ++;
            }
            else {
                $row = '<tr>';
                $rowcount++;
                if ($rowcount >= 6) {
                    $rowcount = 0;
                }
            }
            // set the links to student information based on multiview or individual... if individual go to student info... if many go to individual grades view.
            if (isteacher($course->id)) {
                if ($view_by_student != -1) {
                    $studentviewlink = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$student.'&amp;group='.$group.'&amp;course='.$course->id.'">'.$grades_by_student[$student]['student_data']['lastname'].', '.$grades_by_student[$student]['student_data']['firstname'].'</a>';
                }
                else {
                    $studentviewlink = '<a href="?id='.$course->id.'&amp;action=view_student_grades&amp;group='.$group.'&amp;student='.$student.'">'.$grades_by_student[$student]['student_data']['lastname'].', '.$grades_by_student[$student]['student_data']['firstname'].'</a>';
                }
                $row .= '<td>'. $studentviewlink .'</td>';
            }
            ksort($categories);
            foreach($categories as $category => $items) {
                if ($category != 'student_data') {
                    if ($first == 0) {
                        $colcount++;
                        // only print the category headers if something is displayed for them
                        if ($preferences->show_weighted || $preferences->show_percent || $preferences->show_points) {
                            $stats_link = '<a href="javascript:void(0)"onclick="window.open(\'?id='.$course->id.'&amp;action=stats&amp;group='.$group.'&amp;category='.$category.'\',\''.get_string('statslink','grades').'\',\'height=200,width=300,scrollbars=no\')"><font size=-2>'.get_string('statslink','grades').'</font></a>';
                            $header .= '<th colspan="'.$grade_columns.'"><a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=vcats&amp;cview='.$category;
                            if ($view_by_student != -1) {
                                $header .= '&amp;student='.$view_by_student;
                            }
                            $header .='">'. $category .' '.$stats_link.'</a>';
                        }
                        if ($preferences->display_weighted != 0) {
                            $header .= '('. $all_categories[$category]['stats']['weight'] . '%)';
                        }
                        $header .= '</th>';
                        if ($preferences->show_points) {
                            $header1 .= '<th>'.get_string('points','grades').'('.$all_categories[$category]['stats']['totalpoints'].')';
                            if ($all_categories[$category]['stats']['bonus_points'] != 0) {
                                $header1 .='(+'.$all_categories[$category]['stats']['bonus_points'].')';
                            }
                            $header1 .='</th>';
                        }
                        if ($preferences->show_percent) {
                            $header1 .= '<th>'.get_string('percent','grades').'</th>';
                        }
                        if ($preferences->show_weighted) {
                            $header1 .= '<th>'.get_string('weightedpctcontribution','grades').'</th>';
                        }
                        $maxpercent = $all_categories["$category"]['stats']['weight'] + $maxpercent;
                        //$total_course_points = $all_categories[$category]['stats']['totalpoints']+ $total_course_points;
                        //$total_course_points = $all_categories[$category]['stats']['totalpoints']+ $total_course_points;
                    }
                    
                    
                    if ($preferences->show_points) {
                        $row .= '<td align="right">' . $items['stats']['points'] . '</td>';
                    }
                    if ($preferences->show_percent) {
                        $row .= '<td align="right">'. $items['stats']['percent'].'%</td>';
                    }

                    if ($preferences->show_weighted) {
                        $row .= '<td align="right">'. $items['stats']['weighted'] . '%</td>';
                    }
                    $total_bonus_points = $all_categories[$category]['stats']['bonus_points'];
                } 
            }
            if ($first == 0) {
                if ($preferences->show_letter_grade) {
                    $total_columns = $grade_columns + 1;
                }
                else {
                    $total_columns = $grade_columns;
                }
                
                if (isteacher($course->id) && $view_by_student == -1) {
                    $grade_sort_link = '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=highgrade"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('highgradedescending','grades').'" /></a>';
                    $grade_sort_link .= '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=highgrade_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('highgradeascending','grades').'" /></a>';
                    $points_sort_link = '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=points"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('pointsdescending','grades').'" /></a>';
                    $points_sort_link .= '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=points_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('pointsascending','grades').'" /></a>';
                    $weighted_sort_link = '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=weighted"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('weighteddescending','grades').'" /></a>';
                    $weighted_sort_link .= '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=weighted_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('weightedascending','grades').'" /></a>';
                    $percent_sort_link = '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=percent"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('percentdescending','grades').'" /></a>';
                    $percent_sort_link .= '<a href="?id='.$course->id.'&amp;group='.$group.'&amp;action=grades&amp;sort=percent_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('percentascending','grades').'" /></a>';
                }
                $stats_link = '<a href="javascript:void(0)"onclick="window.open(\'?id='.$course->id.'&amp;group='.$group.'&amp;action=stats&amp;category=all\',\''.get_string('statslink','grades').'\',\'height=200,width=300,scrollbars=no\')"><font size=-2>'.get_string('statslink','grades').'</font></a>';
                $header .= '<th colspan="'.$total_columns.'">'.get_string('total','grades').'&nbsp;'.$stats_link.'</th>';
                if (isteacher($course->id) && $view_by_student == -1) {
                    if ($preferences->show_points) {
                        $header1 .= '<th>'.get_string('points','grades').'('.$all_categories['stats']['totalpoints'].')';
                        if ($category != 'student_data' && $all_categories[$category]['stats']['bonus_points'] != 0) {

                            $header1 .='(+'.$total_bonus_points.')';
                        }
                        $header1 .= '<br />'.$points_sort_link.' '.'</th>';
                    }
                    if ($preferences->show_percent) {
                        $header1 .= '<th>'.get_string('percentshort','grades').'<br />'.$percent_sort_link.' '.'</th>';
                    }
                    if ($preferences->show_weighted) {
                        $header1 .= '<th>'.get_string('weightedpct','grades').'('.$all_categories['stats']['weight'].')'.'<br />'.$weighted_sort_link.' '.'</th>';
                    }
                    if ($preferences->show_letter_grade) {
                        $header1 .= '<th>'.get_string('lettergrade','grades').'<br />'.$grade_sort_link.' '.'</th>';
                    }
                    $header1 .= '</tr>';
                }
                else {
                    if ($preferences->show_points) {
                        $header1 .= '<th>'.get_string('points','grades').'('.$all_categories['stats']['totalpoints'].')';
                        if ($category != 'student_data' && $all_categories[$category]['stats']['bonus_points'] != 0) {
                            $header1 .='(+'.$total_bonus_points.')';
                        }
                        $header1 .= '</th>';
                    }
                    if ($preferences->show_percent) {
                        $header1 .= '<th>'.get_string('percentshort','grades').'</th>';
                    }
                    if ($preferences->show_weighted) {
                        $header1 .= '<th>'.get_string('weightedpct','grades').'('.$all_categories['stats']['weight'].')</th>';
                    }
                    if ($preferences->show_letter_grade) {
                        $header1 .= '<th>'.get_string('lettergrade','grades').'</th>';
                    }
                    $header1 .= '</tr>';
                }
                if (isteacher($course->id)) {
                    $header .= '<th rowspan="2">'.$student_heading_link.'</th></tr>';
                }
                // adjust colcount to reflect actual number of columns output
                $colcount = $colcount * $grade_columns + $total_columns + 2;
                
                print '<tr><th colspan="'.$colcount.'"><font size="+1">'.get_string('allgrades','grades').'</font>';
                if (isteacher($course->id)) {
                    helpbutton('coursegradeteacher', get_string('gradehelp','grades'), 'gradebook');
                }
                else {
                    helpbutton('coursegradestudent', get_string('gradehelp','grades'), 'gradebook');
                }
                print '</th></tr>';
                
                                
                print $header;
                print $header1;
                $first = 1;
            }
            if ($preferences->show_points) {
                $row .= '<td align="right">'.$grades_by_student[$student]['student_data']['points'].'</td>';
            }
            if ($preferences->show_percent) {
                $row .= '<td align="right">'.$grades_by_student[$student]['student_data']['percent'].'%</td>';
            }
            if ($preferences->show_weighted) {
                $row .= '<td align=right>'.$grades_by_student[$student]['student_data']['weighted'].'%</td>';
            }
            if ($preferences->show_letter_grade) {
                if ($preferences->use_weighted_for_letter == 1) {
                    $grade = $grades_by_student[$student]['student_data']['weighted'];
                }
                else {
                    $grade = $grades_by_student[$student]['student_data']['percent'];
                }
                $letter_grade = grade_get_grade_letter($course->id, $grade);
                if ($letter_grade) {
                    $row .= '<td align="right">'.$letter_grade->letter.'</td>';
                }
                else {
                    // there wasn't an appropriate entry to use in the gradebook.
                    if (grade_letters_set($course->id)) {
                        $row .= '<td align="right">'.get_string('nolettergrade','grades').' '.$grade.'</td>';
                    }
                    else {
                        $row .= '<td align="right">'.get_string('nogradeletters','grades').'</td>';
                    }
                }
            }
            if (isteacher($course->id) ) {
                $row .= '<td>'. $studentviewlink .'</td></tr>';
            }
            else {
                $row .= '</tr>';
            }
            print $row;
            $reprint++;
        }
        print '</table>';
    }
    else { // no grades returned
        error(get_string('nogradesreturned','grades'));
    }
}

// deprecated
function grade_view_student_grades() {
    global $USER;
    global $student;
    global $course;
    if (!isteacher($course->id)) {
        grade_view_all_grades($USER->id);
    }
    else {
        grade_view_all_grades($student);
    }
}

// deprecated
function grade_view_student_category_grades() {
    global $USER;
    global $student;
    global $course;
    if (!isteacher($course->id)) {
        grade_view_category_grades($USER->id);
    }
    else {
        grade_view_category_grades($student);
    }
}

function grade_set_grade_weights() {
// set the grade weights as submitted from the form generated by display_grade_weights    
    global $CFG;
    global $course;
    global $USER;
    
    if (!empty($USER->id)) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
    }
    
    // get list of all categories
    $categories = get_records('grade_category', 'courseid', $course->id);
    if ($categories) {
        foreach ($categories as $category) {
            $form_catname = str_replace(' ', '_', $category->name);

            $submitted_category = optional_param($form_catname);    
            if (is_numeric($submitted_category)) {
                // see if there is a weight if so see if it needs to be updated
                $weight = grade_get_category_weight($course->id, $category->name);
                if ($weight) {
                    if ($weight->weight != $submitted_category)
                    {                        
                        set_field('grade_category', 'weight', $submitted_category, 'id', $weight->id);
                    }
                    
                    $cur_drop = optional_param("drop_x_lowest$form_catname");
                    $cur_bonus_points = optional_param("bonus_points$form_catname");
                    $cur_hidden = optional_param("hidden$form_catname");
                    if ($cur_hidden) {
                        $cur_hidden = true;
                    }
                    else {
                        $cur_hidden = false;
                    }
                    
                    if ($weight->drop_x_lowest != $cur_drop) {
                        set_field('grade_category', 'drop_x_lowest', $cur_drop, 'id', $weight->cat_id);
                    }
                    if ($weight->bonus_points != $cur_bonus_points) {
                        set_field('grade_category', 'bonus_points', $cur_bonus_points, 'id', $weight->cat_id);
                    }
                    if ($cur_hidden) {
                        set_field('grade_category', 'hidden', 1, 'id', $weight->cat_id);
                    }
                    else {
                        set_field('grade_category', 'hidden', 0, 'id', $weight->cat_id);
                    }
                }
                else {
                    // insert the new record... we shouldn't reach this point anymore
                    //$new_weight->course = $course->id;
                    //$new_weight->category = $category->id;
                    //$new_weight->weight = $submitted_category;
                    //insert_record('grade_weight', $new_weight);
                }
            }
            else {
                print '<center><font color="red">'.get_string('nonumericweight','grades').$category->name.': "'.$submitted_category.'"</font></center><br />';
            }
        }
    }
}

function grade_display_grade_weights() {
// get all categories with weights
// then set and display that entry.
    global $CFG;
    global $course;
    global $USER;
    
    $categories = get_records('grade_category', 'courseid', $course->id);
    if ($categories) {
        print '<table align=center><tr><th colspan="5">'.get_string('setweights','grades');
        helpbutton('coursegradeweight', get_string('gradeweighthelp','grades'), 'gradebook');
        print '</th></tr>';
        print '<tr><th>'.get_string('category','grades').'</th><th>'.get_string('weight','grades').'</th><th>'.get_string('dropxlowest','grades').'</th><th>'.get_string('bonuspoints','grades').'</th><th>'.get_string('hidecategory','grades').'</th></tr>';
        print '<form name="grade_weights" action="./index.php" method="post">';
        print '<input type="hidden" name="id" value="'.$course->id.'" />';
        print '<input type="hidden" name="action" value="set_grade_weights" />';
        print '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    
        $sum = 0;
        
        foreach($categories as $category) {
            $val = $category->weight;
            $sum = $sum + $val;
                        
            // make names form safe
            $form_catname = str_replace(' ', '_', $category->name);
            print '<tr><td>'.$category->name.'</td>';
            print '<td align="right"><input type="text" size="5" name="'.$form_catname.'" value="'.$val.'" /></td>';
            print '<td align="right"><input type="text" size="5" name="drop_x_lowest'.$form_catname.'" value="'.$category->drop_x_lowest.'" /></td>';
            print '<td align="right"><input type="text" size="5" name="bonus_points'.$form_catname.'" value="'.$category->bonus_points.'" /></td>';
            print '<td align="right"><input type="checkbox" name="hidden'.$form_catname.'" ';
            if ($category->hidden == 1) {
                print ' checked="checked"';
            }
            print ' /></td>';
        }
        print '<tr><td colspan="5" align="center"><input type="submit" value="'.get_string('savechanges','grades').'" /></td></tr></form>';
        if ($sum != 100) {
            print '<tr><td colspan="5" align="center"><font color="red">'.get_string('totalweightnot100','grades').'</font></td></tr>';
        }
        else {
            print '<tr><td colspan="5" align="center"><font color="green">'.get_string('totalweight100','grades').'</font></td></tr>';
        }
    }
    else {
        /// maybe this should just do the default population of the categories instead?
        print '<font color="red">'.get_string('setcategorieserror','grades').'</font>';
    }
    print '</table>';
    print '<center>'.get_string('dropxlowestwarning','grades').'</center><br />';
}

function grade_set_categories() {
    global $CFG;
    global $course;
    global $USER;
    

    /// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
    
    print '<table align="center"><tr><th colspan="5">'.get_string('setcategories','grades');
    helpbutton('coursegradecategory', get_string('gradecategoryhelp','grades'), 'gradebook');
    print '<tr><th>'.get_string('gradeitem','grades').'</th><th>'.get_string('category','grades').'</th><th>'.get_string('maxgrade','grades').'</th><th>'.get_string('curveto','grades').'</th><th>'.get_string('extracredit','grades').'</th></tr>';
    print '<form name="set_categories" method="post" action="./index.php" >';
    print '<input type="hidden" name="action" value="assign_categories" />';
    print '<input type="hidden" name="id" value="'.$course->id.'" />';
    print '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    
    $itemcount = 0;
    
    /// Search through all the modules, pulling out grade data
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    for ($i=0; $i<=$course->numsections; $i++) {
        if (isset($sections[$i])) {   // should always be true
            $section = $sections[$i];
            if ($section->sequence) {
                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    $mod = $mods[$sectionmod];
                    $instance = get_record("$mod->modname", "id", "$mod->instance");
                    $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";
                    if (file_exists($libfile)) {
                        require_once($libfile);
                        $gradefunction = $mod->modname."_grades";
                        if (function_exists($gradefunction)) {   // Skip modules without grade function
                            if ($modgrades = $gradefunction($mod->instance)) {
                                
                                if ($modgrades->maxgrade != '')
                                // this block traps out broken modules that don't return a maxgrade according to the moodle API
                                {
                                    $itemcount++;
                                    //modgrades contains student information with associated grade
                                    //print "<b>modname: $mod->modname id: $mod->id course: $mod->course</b><br />";
                                    print '<input type="hidden" name="modname'.$itemcount.'" value="'.$mod->modname.'" />';
                                    print '<input type="hidden" name="mod'.$itemcount.'" value="'.$mod->instance.'" />';
                                    print '<input type="hidden" name="course'.$itemcount.'" value="'.$mod->course.'" />';
                                    print '<tr><td>';
                                    // get instance name from db.
                                    $instance = get_record($mod->modname, 'id', $mod->instance);
                                    print "$instance->name</td>";
                                    // see if the item is already in the category table and if it is call category select with the id so it is selected
                                    print '<td><select name="category'.$itemcount.'">';
                                    $item_cat_id = get_record('grade_item', 'modid', $mod->module, 'courseid', $course->id, 'cminstance', $mod->instance);
                                    //print_object($item_cat_id);
                                    if (isset($item_cat_id)) {
                                        grade_category_select($item_cat_id->category);
                                    }
                                    else {
                                        grade_category_select(-1);
                                    }
                                    print '</select></td><td align="right">'.$modgrades->maxgrade.'<input type="hidden" name="maxgrade'.$itemcount.'" value="'.$modgrades->maxgrade.'" /></td>';
                                        
                                    if (isset($item_cat_id)) {
                                        // the value held in scale_grade is a scaling percent. The next line just formats it so it is easier for the user (they just enter the point value they want to be 100%)
                                        if ($item_cat_id->scale_grade == '' || $item_cat_id->scale_grade == 0)
                                            $scale_to = $modgrades->maxgrade;
                                        else
                                            $scale_to = round($modgrades->maxgrade/$item_cat_id->scale_grade);
                                        print '<td><input type="text" size="5" name="scale_grade'.$itemcount.'" value="'.$scale_to.'" /></td>';
                                    }
                                    else {
                                        print '<td><input type="text" size="5" name="scale_grade'.$itemcount.'" value="'.$modgrades->maxgrade.'" /></td>';
                                    }
                                    
                                    print '<td align="right"><input type="checkbox" name="extra_credit'.$itemcount.'" ';
                                    if ($item_cat_id->extra_credit == 1) {
                                        print ' checked="checked"';
                                    }
                                    print ' /></td></tr>';
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    print '<input type="hidden" name="totalitems" value="'.$itemcount.'" />';
    print '<tr><td colspan="5" align="center"><input type="submit" value="'.get_string('savechanges','grades').'" /></td></tr>';
    print '</form>';
    print '<tr><td colspan="5" align="center">';
    grade_add_category_form();
    print '</td></tr><tr><td colspan="5" align="center">';
    grade_delete_category_form();
    print '</td></tr><tr><td colspan="5">'.get_string('extracreditwarning','grades').'</td></tr></table>';
}

function grade_delete_category() {
    global $CFG;
    global $course;
    global $USER;
    
    if (!empty($USER->id)) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
    }
    
    $cat_id = optional_param('category_id');
    if ($cat_id != 'blank') {
        // delete the record
        delete_records('grade_category', 'id', $cat_id, 'courseid', $course->id);
        // set grade_item category field=0 where it was the deleted category (set uncategorized will clean this up)
        set_field('grade_item', 'category', 0, 'category', $cat_id);
    }
}

function grade_assign_categories() {
    global $CFG;
    global $course;
    global $USER;
    $num_categories = optional_param('totalitems');

    if (!empty($USER->id)) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
    }

    for ($i = 1; $i <= $num_categories; $i++) {

        // these next sets of lines are a bit obtuse, but it lets there be a dynamic number of grade items
        // in the grade category form (maybe there's a better way?)
        $cur_cat_id = '$_REQUEST[\'category'.$i.'\'];';
        eval( "\$cur_cat_id = $cur_cat_id;" ); 
        $cur_modname = '$_REQUEST[\'modname'.$i.'\'];';
        eval( "\$cur_modname = $cur_modname;" ); 
        $cur_mod = '$_REQUEST[\'mod'.$i.'\'];';
        eval( "\$cur_mod = $cur_mod;" );
        $cur_maxgrade = '$_REQUEST[\'maxgrade'.$i.'\'];';
        eval( "\$cur_maxgrade = $cur_maxgrade;" );
        $cur_scale_grade = '$_REQUEST[\'scale_grade'.$i.'\'];';
        eval( "\$cur_scale_grade = $cur_scale_grade;" );
        $cur_extra_credit = '$_REQUEST[\'extra_credit'.$i.'\'];';
        $temp = 'extra_credit'.$i;
        $junk = get_record('modules','name',$cur_modname);
        $cur_modid = $junk->id;
        if (isset($_REQUEST[$temp])) {
            eval( "\$cur_extra_credit = $cur_extra_credit;" );
        }
        else {
            $cur_extra_credit = false;
        }
        
        if ($cur_scale_grade == 0 || $cur_scale_grade == '') {
            $cur_scale_grade = 1.0;
        }
        
        $db_cat = get_record('grade_item', 'modid', $cur_modid, 'cminstance', $cur_mod, 'courseid', $course->id);
        if ( $db_cat ) {
            if ($db_cat->category != $cur_cat_id) {
                // item doesn't match in the db so update it to point to the new category
                set_field('grade_item', 'category', $cur_cat_id, 'id', $db_cat->id);
            }

            if ($db_cat->scale_grade != $cur_maxgrade/$cur_scale_grade) {
                // scale_grade doesn't match
                set_field('grade_item', 'scale_grade', ($cur_maxgrade/$cur_scale_grade), 'id', $db_cat->id);
            }
            
            if ($cur_extra_credit) {
                set_field('grade_item', 'extra_credit', 1, 'id', $db_cat->id);
            }
            else {
                set_field('grade_item', 'extra_credit', 0, 'id', $db_cat->id);
            }
        }
        else {
            // add a new record
            $item->courseid = $course->id;
            $item->category = $cur_cat_id;
            $item->modid = $cur_modid;
            $item->cminstance = $cur_mod;
            $item->scale_grade = $cur_scale_grade;
            $item->extra_credit = $cur_extra_credit;
            insert_record('grade_item', $item);
        }
    }
}

function grade_add_category_form() {
    /// outputs a form to add a category
    /// just a simple text box with submit
    global $course;
    global $USER;
    print '<form name="new_category">';
    print get_string('addcategory','grades').':<input type="text" name="name" size="20" />';
    print '<input type="hidden" name="id" value="'.$course->id.'" />';
    print '<input type="hidden" name="action" value="insert_category" />';
    print '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    print '<input type="submit" value="'.get_string('addcategory','grades').'" />';
    print '</form>';
}

function grade_delete_category_form() {
    // outputs a form to delete a category
    global $course;
    global $USER;
    print '<form name="delete_category">';
    print get_string('deletecategory','grades').': <select name="category_id">';
    grade_category_select();
    print '</select><input type="hidden" name="id" value="'.$course->id.'" />';
    print '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    print '<input type="hidden" name="action" value="delete_category" />';
    print '<input type="submit" value="'.get_string('deletecategory','grades').'" /></form>';
}

function grade_insert_category() {
    global $CFG;
    global $course;
    global $USER;
    
    $category->name=optional_param('name');
    $category->courseid=$course->id;
    
    if (!empty($USER->id)) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
    }
    
    // make sure the record isn't already there and insert if okay
    if (record_exists('grade_category', 'name', $category->name, 'courseid', $category->course)) {
            // category already exists
    }
    elseif ($category->name != ''){
        if (!insert_record('grade_category', $category) ) {
            print '<font color="red">'.get_string('addcategoryerror','grades').'</font>';
        }
    }
}

function grade_category_select($id_selected) {
    /// prints out a select box containing categories.
    global $CFG;
    global $course;

    
    print '<option value="blank">'.get_string('choosecategory','grades').'</option>';
    
    $categories = get_records('grade_category', 'courseid', $course->id, 'name');
    
    if (!isset($categories)) {
        error(get_string("nocategories"));
    }
    else {
        foreach($categories as $category) {
            if ($category->id == $id_selected) {
                print '<option value="'.$category->id.'" selected="selected">'.$category->name.'</option>';
            }
            else {
                print '<option value="'.$category->id.'">'.$category->name.'</option>';
            }
        }
    }
}

function grade_display_grade_preferences() {
    global $CFG;
    global $course;
    global $USER;

    $preferences = grade_get_preferences();
    
    $stryes = get_string('yes','grades');
    $strno = get_string('no','grades');
        
    print '<table align="center"><tr><th colspan="3">'.get_string('setpreferences','grades');
    helpbutton('coursegradepreferences', get_string('gradepreferenceshelp','grades'), 'gradebook');
    print '</th></tr><tr><th>'.get_string('item','grades').'</th><th>'.get_string('setting','grades').'</th>';
    if ($preferences->use_advanced != 0) {
        print '<th>'.get_string('forstudents','grades').'</th>';
    }
    print '</tr>';
    print '<form name="set_grade_preferences" method="post" action="./index.php">';
    print '<input type="hidden" name="action" value="set_grade_preferences" />';
    print '<input type="hidden" name="id" value='.$course->id.' />';
    print '<input type="hidden" name="pref_id" value='.$preferences->id.' />';
    print '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    
    print '<tr><td>'.get_string('useadvanced','grades').'</td><td><select name="use_advanced">';
    if ($preferences->use_advanced ==0) {
        print '<option value="0" selected="selected">'.$strno.'</option><option value=1>'.$stryes.'</option>';
    }
    else {
        print '<option value="0">'.$strno.'</option><option value=1 selected="selected">'.$stryes.'</option>';
    }
    
    if ($preferences->use_advanced != 0) {
        // display grade weights
        print '<tr><td>'.get_string('displayweighted','grades').'</td><td><select name="display_weighted">';
        if ($preferences->display_weighted == 0) {
            print '<option value="0" selected="selected">'.$strno.'</option><option value="1">'.$stryes.'</option>';
        }
        else {
            print '<option value="0">'.$strno.'</option><option value="1" selected="selected">'.$stryes.'</option>';
        }
        print '</select></td>';
        
        // add user view checkbox
        print '<td><input type="checkbox" name="display_weighted_student"';
        if ($preferences->display_weighted_student == 1) {
            print ' checked="checked"';
        }
        print ' />';
        
        // display points    
        print '<tr><td>'.get_string('displaypoints','grades').'</td><td><select name="display_points">';
        if ($preferences->display_points == 0) {
            print '<option value="0" selected="selected">'.$strno.'</option><option value="1">'.$stryes.'</option>';
        }
        else {
            print '<option value="0">'.$strno.'</option><option value="1" selected="selected">'.$stryes.'</option>';
        }
        print '</select></td>';
    
        // add user view checkbox
        print '<td><input type=checkbox name="display_points_student"';
        if ($preferences->display_points_student == 1) {
            print ' checked="checked"';
        }
        print ' />';
    
        // display percent
        print '<tr><td>Display Percent</td><td><select name="display_percent">';
        if ($preferences->display_percent == 0) {
            print '<option value="0" selected="selected">'.$strno.'</option><option value="1">'.$stryes.'</option>';
        }
        else {
            print '<option value="0">'.$strno.'</option><option value="1" selected="selected">'.$stryes.'</option>';
        }
        print '</select></td>';
        
        // add user view checkbox
        print '<td><input type="checkbox" name="display_percent_student"';
        if ($preferences->display_percent_student == 1) {
            print ' checked="checked"';
        }
        print ' />';
        
        // display letter grade
        print '<tr><td>'.get_string('displaylettergrade','grades').'</td><td><select name="display_letter_grade">';
        if ($preferences->display_letter_grade == 0) {
            print '<option value="0" selected="selected">'.$strno.'</option><option value="1">'.$stryes.'</option>';
        }
        else {
            print '<option value="0">'.$strno.'</option><option value="1" selected="selected">'.$stryes.'</option>';
        }
        print '</select></td>';
        
        print '<td><input type="checkbox" name="display_letter_grade_student"';
        if ($preferences->display_letter_grade_student == 1) {
            print ' checked="checked"';
        }
        print ' />';
        
        // letter grade uses weighted percent
        $strusepercent = get_string('usepercent','grades');
        $struseweighted = get_string('useweighted','grades');
        print '<tr><td>'.get_string('lettergrade','grades').':</td><td><select name="use_weighted_for_letter">';
        if ($preferences->use_weighted_for_letter == 0) {
            print '<option value="0" selected="selected">'.$strusepercent.'</option><option value="1">'.$struseweighted.'</option>';
        }
        else {
            print '<option value="0">'.$strusepercent.'</option><option value="1" selected="selected">'.$struseweighted.'</option>';
        }
        print '</select></td></tr>';
    }
    
    // reprint headers every n lines default n=0
    print '<tr><td>'.get_string('reprintheaders','grades').':</td><td><input type="text" name="reprint_headers" value="'.$preferences->reprint_headers.'" /></td></tr>';
    
    // show hidden grade items to teacher
    print '<tr><td>'.get_string('showhiddenitems','grades').'</td><td><select name="show_hidden">';
    if ($preferences->show_hidden ==0) {
        print '<option value="0" selected="selected">'.$strno.'</option><option value=1>'.$stryes.'</option>';
    }
    else {
        print '<option value="0">'.$strno.'</option><option value=1 selected="selected">'.$stryes.'</option>';
    }
    print '</td></tr>';
    
    print '<tr><td colspan="3" align="center"><input type="submit" value="'.get_string('savepreferences','grades').'" /></td></tr></form></table>';
}

function grade_set_grade_preferences() {
    global $CFG;
    global $course;
    global $UNCATEGORIZED;
    global $USER;
    
    if (!empty($USER->id)) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
    }

    $new_prefs->display_weighted=optional_param('display_weighted');
    $new_prefs->display_points=optional_param('display_points');
    $new_prefs->display_percent=optional_param('display_percent');
    $new_prefs->display_letter_grade=optional_param('display_letter_grade');
    $new_prefs->use_weighted_for_letter=optional_param('use_weighted_for_letter');
    $new_prefs->reprint_headers = optional_param('reprint_headers');
    $new_prefs->use_advanced = optional_param('use_advanced');
    
    if (isset($_REQUEST['display_weighted_student'])) {
        $new_prefs->display_weighted_student=1;
    }
    else {
        $new_prefs->display_weighted_student = 0;
    }
    
    if (isset($_REQUEST['display_points_student'])) {
        $new_prefs->display_points_student=1;
    }
    else {
        $new_prefs->display_points_student = 0;
    }
    
    if (isset($_REQUEST['display_percent_student'])) {
        $new_prefs->display_percent_student=1;
    }
    else {
        $new_prefs->display_percent_student =0;
    }
    
    if (isset($_REQUEST['display_letter_grade_student'])) {
        $new_prefs->display_letter_grade_student=1;
    }
    else {
        $new_prefs->display_letter_grade_student = 0;
    }
        
    if(!is_numeric($new_prefs->reprint_headers)) {
        $new_prefs->reprint_headers = 0;
        print '<font color="red">'.get_string('errorreprintheadersnonnumeric','grades').'</font>';
    }
    
    if (isset($_REQUEST['show_hidden'])) {
        $new_prefs->show_hidden = $_REQUEST['show_hidden'];
    }
    $new_prefs->courseid=$course->id;    
    
    $preferences = grade_get_preferences();

    if($preferences->use_advanced == 1) {
        $new_prefs->id = $preferences->id;
        update_record('grade_preferences', $new_prefs);
    }
    else {
        set_field('grade_preferences', 'reprint_headers', $new_prefs->reprint_headers, 'courseid', $course->id);
        set_field('grade_preferences', 'use_advanced', $new_prefs->use_advanced, 'courseid', $course->id);
        set_field('grade_preferences', 'show_hidden', $new_prefs->show_hidden, 'courseid', $course->id);
    }
    
    if($new_prefs->use_advanced == 0) {
        // need to set all 'extra' features so they don't affect grades or points
        // set grade_scale to 1.0 for all grade_items
        // set bonus_points to 0
        // set all grade_items to uncategorized
        // set uncategorized weight to 100%
        // unset all exceptions
        $nonadvanced->use_weighted_for_letter=0;
        $nonadvanced->display_weighted=0;
        $nonadvanced->display_points=1;
        $nonadvanced->display_percent=0;
        $nonadvanced->display_letter_grade=0;
        $nonadvanced->display_weighted_student=0;
        $nonadvanced->display_points_student=1;
        $nonadvanced->display_percent_student=0;
        $nonadvanced->display_letter_grade_student=0;
        $nonadvanced->id = $preferences->id;
        update_record('grade_preferences', $nonadvanced);
        
        delete_records('grade_exceptions', 'courseid', $course->id);
        
        // this was pulled out of the lang file as it could cause some problems
        $uncat = $UNCATEGORIZED;
        $uncat_id = get_record('grade_category', 'courseid', $course->id, 'name', $uncat);
    
        if (!$uncat_id) {
            // insert the uncategorized category 
            $temp->name=$uncat;
            $temp->courseid=$course->id;
            insert_record('grade_category', $temp);
            $uncat_id = get_record('grade_category', 'courseid', $course->id, 'name', $uncat);
            if (!$uncat_id) {
                error(get_string('errornocategorizedid','grades'));
                exit(0);
            }
        }
        
        set_field('grade_item', 'category', $uncat_id->id, 'courseid', $course->id);
        set_field('grade_category', 'bonus_points', '0', 'courseid', $course->id);
        set_field('grade_item', 'scale_grade', 1.00, 'courseid', $course->id);
        set_field('grade_item', 'extra_credit', 0, 'courseid', $course->id);
        set_field('grade_category', 'weight', 100.0, 'courseid', $course->id, 'id', $uncat_id->id);
    }
}


function grade_display_letter_grades() {
    global $CFG;
    global $course;
    global $USER;
    
    $db_letters = get_records('grade_letter', 'courseid', $course->id, 'grade_high DESC');
    
    if ($db_letters) {
        $using_defaults = false;
        foreach ($db_letters as $letter) {
            $letters[$letter->id]->letter = $letter->letter;
            $letters[$letter->id]->grade_low = $letter->grade_low;
            $letters[$letter->id]->grade_high = $letter->grade_high;
            $letters[$letter->id]->courseid = $course->id;
        }
    }
    else {
        $using_defaults = true;
        // default A
        $letters[0]->letter='A';
        $letters[0]->grade_low=93.00;
        $letters[0]->grade_high=100.00;
        $letters[0]->courseid = $course->id;
        // default A-
        $letters[1]->letter='A-';
        $letters[1]->grade_low=90.00;
        $letters[1]->grade_high=92.99;
        $letters[1]->courseid = $course->id;
        // default B+
        $letters[2]->letter='B+';
        $letters[2]->grade_low=87.00;
        $letters[2]->grade_high=89.99;
        $letters[2]->courseid = $course->id;
        // default B
        $letters[3]->letter='B';
        $letters[3]->grade_low=83.00;
        $letters[3]->grade_high=86.99;
        $letters[3]->courseid = $course->id;
        // default B-
        $letters[4]->letter='B-';
        $letters[4]->grade_low=80.00;
        $letters[4]->grade_high=82.99;
        $letters[4]->courseid = $course->id;
        // default C+
        $letters[5]->letter='C+';
        $letters[5]->grade_low=77.00;
        $letters[5]->grade_high=79.99;
        $letters[5]->courseid = $course->id;
        // default C
        $letters[6]->letter='C';
        $letters[6]->grade_low=73.00;
        $letters[6]->grade_high=76.99;
        $letters[6]->courseid = $course->id;
        // default C-
        $letters[7]->letter='C-';
        $letters[7]->grade_low=70.00;
        $letters[7]->grade_high=72.99;
        $letters[7]->courseid = $course->id;
        // default D+
        $letters[8]->letter='D+';
        $letters[8]->grade_low=67.00;
        $letters[8]->grade_high=69.99;
        $letters[8]->courseid = $course->id;
        // default D
        $letters[9]->letter='D';
        $letters[9]->grade_low=60.00;
        $letters[9]->grade_high=66.99;
        $letters[9]->courseid = $course->id;
        // default F
        $letters[10]->letter='F';
        $letters[10]->grade_low=0.00;
        $letters[10]->grade_high=59.99;
        $letters[10]->courseid = $course->id;
    }
    
    print '<table align="center"><tr><th colspan="3">'.get_string('setgradeletters','grades');
    helpbutton('coursegradeletter', get_string('gradeletterhelp','grades'), 'gradebook');
    print '</th></tr><tr><th>'.get_string('gradeletter','grades').'</th><th>'.get_string('lowgradeletter','grades').'</th><th>'.get_string('highgradeletter','grades').'</th></tr>';
    print '<form name="grade_letter"><input type="hidden" name="id" value="'.$course->id.'" />';
    print '<input type="hidden" name="action" value="set_letter_grades" />';
    print '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    $i=0;
    foreach ($letters as $id=>$items) {
        if ($id !='' && !$using_defaults) {
            // send the record id so if the user deletes the values we can delete the row.
            print '<input type="hidden" name="id'.$i.'" value="'.$id.'" />';
        }
        print '<tr><td><input size="8" type="text" name="letter'.$i.'" value="'.$items->letter.'" /></td>'."\n";
        print '<td><input size="8" type="text" name="grade_low'.$i.'" value="'.$items->grade_low.'" /></td>'."\n";
        print '<td><input size="8" type="text" name="grade_high'.$i.'" value="'.$items->grade_high.'" /></td></tr>'."\n";
        $i++;
    }
    print '<tr><td><input size="8" type="text" name="letter'.$i.'" value="" /></td><td><input size="8" type="text" name="grade_low'.$i.'" value="" /></td><td><input type="text" size="8" name="grade_high'.$i.'" value="" /></td></tr>';
    print '<tr><td colspan="3" align="center"><input type="submit" value="'.get_string('savechanges','grades').'" /></td></tr>';
    print '<input type="hidden" name="totalitems" value="'.$i.'" />';
    print '</form><tr><td colspan="3">'.get_string('gradeletternote','grades').'</table>';
}

function grade_set_letter_grades() {
    global $CFG;
    global $course;
    global $USER;
    
    if (!empty($USER->id)) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }
    }
    
    $totalitems= clean_param($_REQUEST['totalitems'], PARAM_CLEAN);
    
    for($i=0; $i<=$totalitems; $i++) {
        if (isset($_REQUEST["id$i"])) {
            // item submitted was already in database
            $letterid = $_REQUEST["id$i"];
            $updateletters[$letterid]->letter = clean_param($_REQUEST["letter$i"], PARAM_CLEAN);
            $updateletters[$letterid]->grade_low = clean_param($_REQUEST["grade_low$i"], PARAM_CLEAN);
            $updateletters[$letterid]->grade_high = clean_param($_REQUEST["grade_high$i"], PARAM_CLEAN);
            $updateletters[$letterid]->id = $letterid;
        }
        else {
            // its a new item
            $newletter->letter = clean_param($_REQUEST["letter$i"], PARAM_CLEAN);
            $newletter->grade_low = clean_param($_REQUEST["grade_low$i"], PARAM_CLEAN);
            $newletter->grade_high = clean_param($_REQUEST["grade_high$i"], PARAM_CLEAN);
            $newletter->courseid = $course->id;
            if (is_numeric($newletter->grade_high) && is_numeric($newletter->grade_low)) {
                insert_record('grade_letter', $newletter);
            }
            else {
                if ($i < $totalitems) {
                    if ($newletter->grade_high != '' or $newletter->grade_low != '') {
                        print '<center>'.get_string('lettergradenonnumber','grades').' '.$newletter->letter.' item number: '.$i.'<br /></center>';
                    }
                }
            }
        }
    }

    if (isset($updateletters)) {
        foreach($updateletters as $id=>$items) {
            // see if any of the values are blank... if so delete them
            if ($items->letter == '' || $items->grade_low == '' || $items->grade_high == '') {
                delete_records('grade_letter', 'id', $id);
            }
            else {
                if (is_numeric($items->grade_high) && is_numeric($items->grade_low)) {
                    update_record('grade_letter', $items);
                }
                else {
                    print '<center><font color="red">'.get_string('errorgradevaluenonnumeric','grades').$letter.'</font></center>';
                }
            }
        }
    }
}

function grade_show_group_select() {
    global $course;
    global $USER;
    global $group;

    if (!isset($group)) {
        if (isset($_REQUEST['group'])) {
            $group = clean_param($_REQUEST['group'], PARAM_INT);
        }
        else {
            $group = 0;
        }
    }

    if ($groups = get_groups($course->id)) {
        // the course uses groups so let them choose one
        print '<td>'.get_string('viewbygroup', 'grades').':</td><td>';
        print '<form id="groupselect">';
        print '<input type="hidden" name="id" value="'.$course->id.'" />';

        if (isset($_REQUEST['action'])) {
            print '<input type="hidden" name="action" value="'.clean_param($_REQUEST['action'], PARAM_CLEAN).'" />';
        }
        
        if (isset($_REQUEST['cview'])) {
            print '<input type="hidden" name="cview" value="'.clean_param($_REQUEST['cview'], PARAM_CLEAN).'" />';
        }
        print '<select name="group" onchange="submit();">';
        print '<option value="0">'.get_string('allstudents','grades').'</option>';

        foreach ($groups as $id => $groupname) {
            print '<option value="'.$id.'" ';
            if ($group == $id) {
                print ' selected="selected" ';
            }
            print '>'.$groupname->name.'</option>';
        }
        print '</select>';
        print '</form></td>';
    }
}

function grade_download_form($type='both') {
    global $course;
    if ($type != 'both' || $type != 'excel' || $type != 'text') {
        $type = 'both';
    }
    
    if (isteacher($course->id)) {
        print '<table align="center"><tr>';
        $options['id'] = $course->id;
        
        if ($type = 'both' || $type == 'excel') {
            $options['action'] = 'excel';
            print '<td align="center">';
            print_single_button("index.php", $options, get_string("downloadexcel"));
            print '</td>';
        }
        if ($type = 'both' || $type == 'text') {
            $options['action'] = 'text';
            print '<td align="center">';
            print_single_button("index.php", $options, get_string("downloadtext"));
            print '</td>';
        }
        grade_show_group_select();
        print '</tr></table>';
    }
}

?>
