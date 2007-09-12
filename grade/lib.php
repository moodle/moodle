<?php // $Id$

if (!defined('MOODLE_INTERNAL')) {
            die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/course/lib.php');

define('UNCATEGORISED', 'uncategorised');

global $GRADEPREFS, $GRADEPREFSDEFAULTS;    // This variables are going to be global... :-/

$GRADEPREFS = array('use_advanced',                // Only add new preferences to the end of this array!
                    'use_weighted_for_letter',     // as the order counts and will affect backward compatibility
                    'display_weighted',
                    'display_points',
                    'display_percent',
                    'display_letters',
                    'reprint_headers',
                    'show_hidden',
                    );


$GRADEPREFSDEFAULTS = array('use_advanced'                => 0,
                            'use_weighted_for_letter'     => 0,
                            'display_weighted'            => 0,
                            'display_points'              => 2,
                            'display_percent'             => 1,
                            'display_letters'             => 0,
                            'reprint_headers'             => 0,
                            'show_hidden'                 => 1
                            );


//******************************************************************
// SQL FUNCTIONS 
//******************************************************************

function grade_get_category_weight($course, $category) {
    global $CFG;
    $sql = "SELECT id, weight, drop_x_lowest, bonus_points, hidden, c.id AS cat_id
        FROM {$CFG->prefix}grade_category c  
        WHERE c.courseid=$course  
            AND c.name='$category'";
    $temp = get_record_sql($sql);
    return $temp;
}

function grade_get_grade_items($course) {
    global $CFG;
     $sql = "SELECT i.id, c.name as cname, i.modid, mm.name as modname, i.cminstance, c.hidden, cm.visible, i.sort_order, cm.id as cmid
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
    $sql = "SELECT cm.id, 1 FROM {$CFG->prefix}course_modules cm, {$CFG->prefix}modules mm, {$CFG->prefix}grade_item i 
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

function grade_get_exceptions_user($course, $userid) {
    global $CFG;
    $sql = "SELECT e.id, e.userid, gi.cminstance, gi.modid, c.name as catname, mm.name as modname
            FROM {$CFG->prefix}grade_exceptions e, 
                {$CFG->prefix}grade_item gi, 
                {$CFG->prefix}grade_category c,
                {$CFG->prefix}course_modules cm, 
                {$CFG->prefix}modules mm
            WHERE e.courseid=$course
            AND e.userid=$userid 
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
            // FIX ME: there is no $userid defined!!! - from where is this function used anyway??
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
    global $COURSE;
    global $preferences;
    $course = $COURSE;
    $i = 2; // index to differentiate activities with the same name MDL-6876
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
                                    $instance->name= $instance->name.' #'.$i++;
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
        if (!$students = grade_get_course_students($course->id)) {
            return false;  
        }

                              
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
    

        // if the user has selected a group to view by get the group members
        if ($currentgroup = get_current_group($course->id)) {
            $groupmembers = get_group_users($currentgroup);
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

                        foreach($main_category as $assignment => $items) {
                            if ($assignment != 'stats') {
                                if(!isset($grades_by_student["$student"]["$category"]["$assignment"]['grade'])) {
                                    if (isset($grades_by_student["$student"]["$category"]['stats']['allgrades'])) {
                                        $grades_by_student["$student"]["$category"]['stats']['allgrades'] .=  ',0';
                                    } else {
                                        $grades_by_student["$student"]["$category"]['stats']['allgrades'] = '0';
                                    }
                                }
                            }            
                        }
            
                        if (!isset($grades_by_student["$student"]["$category"]['stats']['points'])) {
                           $grades_by_student["$student"]["$category"]['stats']['points'] = '-';
               
                        }
        
                        else {
                            // points are set... see if the current category is using drop the x lowest and do so
                            // also drop exceptions first, so then this grades(s) won't be recoqnized as the x lowest
                // Get exception scores and assign them in the array
                            if ($main_category['stats']['drop'] != 0) {
                                $exceptions = grade_get_exceptions_user($course->id, $student);
                                if (isset($exceptions) && $exceptions) {
                                    foreach($exceptions as $exception) {
                                        if (isset($grades_by_student["$exception->userid"])) {
                                            if ($grades_by_student["$exception->userid"]["$exception->catname"]) {
                                                $assgn = get_record($exception->modname, 'id', $exception->cminstance, 'course', $course->id);
                                                $grade = $grades_by_student["$exception->userid"]["$exception->catname"]["$assgn->name"]['grade'];
                        if (isset($grade)) {
                            if (!isset($grades_by_student["$exception->userid"]["$exception->catname"]['stats']['exceptions'])) {
                                                             $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['exceptions'] = $grade;
                                                    }
                                                    elseif (isset($grades_by_student["$exception->userid"]["$exception->catname"]['stats']['exceptions'])) {
                                                             $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['exceptions'] .= ','. $grade;
                            }
                        }
                                            }
                                        }
                                    }
                }
                                if (isset($grades_by_student["$student"]["$category"]['stats']['exceptions'])) {
                                    $grades_by_student["$student"]["$category"]['stats']['allgrades'] = grade_drop_exceptions($grades_by_student["$student"]["$category"]['stats']['allgrades'], $grades_by_student["$student"]["$category"]['stats']['exceptions']);
                                }
                
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
            //total point should not be smaller than grade against
            if ($grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] - $all_categories["$exception->catname"]["$assgn->name"]['grade_against'] != 0 ) {
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] = $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['totalpoints'] - $all_categories["$exception->catname"]["$assgn->name"]['grade_against'];
            }
                        $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] = $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] - 1;
                        if ($grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] < 0) {
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['grade_items'] = 0;
            }
                        if ($all_categories["$exception->catname"]['stats']['drop'] == 0) {
                            $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['points'] = $grades_by_student["$exception->userid"]["$exception->catname"]['stats']['points'] - $grades_by_student["$exception->userid"]["$exception->catname"]["$assgn->name"]['grade'];
                        }
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
        $retval = array($grades_by_student, $all_categories);
    }
    else {
        $retval = array(0,0);
        // echo "<center><font color=red>Could not find any graded items for this course.</font></center>";
    }
    return $retval;
}

function grade_drop_exceptions($grades, $grades_exceptions) {
    $grade_array = explode(',',$grades);
    $grade_exception_array = explode(',',$grades_exceptions);
    $ret_grades = Array(); 
    foreach ($grade_array as $key => $val) {
        $posb = array_search($val,$grade_exception_array);
        if (is_integer($posb)) {
            unset($grade_exception_array[$posb]);
        } else {
        
            $ret_grades[] = $val;
        }
    }
    $grades = implode(',', $ret_grades);
    return $grades;
}

function grade_drop_lowest($grades, $drop, $total) {
    // drops the lowest $drop numbers from the comma seperated $grades making sure that if $grades has 
    // fewer items than $total that we don't drop too many
    $grade_array = explode(',',$grades);
    if (count($grade_array) == 1) {
    $grades = implode('', $grade_array);
    }
    else if ($drop > 0 AND (count($grade_array) > $drop)) {
        rsort($grade_array);

        for($i=0; $i < (count($grade_array) - $drop); $i++) {
            $ret_grades["$i"] = $grade_array["$i"];
        }
        if (isset($ret_grades)) {
            $grades = implode(',',$ret_grades);
        }
    }
    else {
        $grades = 0;
    }
    return $grades;    
}

function grade_get_grades() {
    global $CFG;
    global $course;
    $mods = grade_get_grade_items($course->id);
    $preferences = grade_get_preferences($course->id);
    
    if ($mods) {
        foreach ($mods as $mod)    {
            // hidden is a gradebook setting for an assignment and visible is a course_module setting 
            if (($mod->hidden != 1 && $mod->visible==1) or (has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $mod->cmid)) && $preferences->show_hidden==1)) {
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
                        delete_records('grade_exceptions', 'grade_itemid', $mod->id, 'courseid', $course->id);
                    }                
                }
                else {
                    //echo "<center><font color=red>Could not find lib file for $mod->modid</font></center>";
                }
            }
        }
    }
    else {
        // Do something here for no grades
        //echo "<center><font color=red>No grades returned. It appears that there are no items with grades for this course.</font></center>";
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
    $uncat = UNCATEGORISED;

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
                                    //echo "<b>modname: $mod->modname id: $mod->id course: $mod->course</b><br/>";
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


function grade_set_preference($courseid, $name, $value) {
    global $GRADEPREFS;

    if (false !== ($key = array_search($name, $GRADEPREFS))) {
        if ($record = get_record('grade_preferences', 'courseid', $courseid, 'preference', $key)) {
            $record->value = $value;
            update_record('grade_preferences', $record);
        } else {   // Make a new one
            $record->preference = $key;
            $record->courseid = $courseid;
            $record->value = $value;
            insert_record('grade_preferences', $record);
        }
    }
}

function grade_get_preference($courseid, $name) {
    global $GRADEPREFS, $GRADEPREFSDEFAULTS;

    if (false !== ($key = array_search($name, $GRADEPREFS))) {
        if (!($record = get_record('grade_preferences', 'courseid', $courseid, 'preference', $key))) {
            // Make a new one
            $record->preference = $key;
            $record->courseid = $courseid;
            $record->value = $GRADEPREFSDEFAULTS[$name];
            insert_record('grade_preferences', $record);
        }
        return $record->value;
    }
    return NULL;
}

function grade_get_preferences($courseid) {
    global $CFG;
    global $GRADEPREFS, $GRADEPREFSDEFAULTS;

    $preferences = NULL;

    // Get the preferences for the course.
    if ($rawprefs = get_records('grade_preferences', 'courseid', $courseid)) {
        foreach ($rawprefs as $pref) {
            if (isset($GRADEPREFS[$pref->preference])) {  // Valid pref
                $name = $GRADEPREFS[$pref->preference];
                $preferences->$name = $pref->value;
            }
        }
    }

    // Check for any missing ones and create them from defaults
    // We don't save them in the database so we save space
    foreach ($GRADEPREFS as $number => $name) {
        if (!isset($preferences->$name)) {
            $preferences->$name = $GRADEPREFSDEFAULTS[$name];
        }
    }

    // Construct some other ones about which fields are shown

    $isteacher = has_capability('moodle/course:managegrades', get_context_instance(CONTEXT_COURSE, $courseid));

    $preferences->show_weighted = (($preferences->display_weighted > 0  && $isteacher) || 
                                   ($preferences->display_weighted > 1 && !$isteacher));
    
    $preferences->show_points   = (($preferences->display_points > 0  && $isteacher) || 
                                   ($preferences->display_points > 1 && !$isteacher));
    
    $preferences->show_percent  = (($preferences->display_percent > 0  && $isteacher) || 
                                   ($preferences->display_percent > 1 && !$isteacher));

    $preferences->show_letters  = (($preferences->display_letters > 0  && $isteacher) || 
                                   ($preferences->display_letters > 1 && !$isteacher));

    return $preferences;
}


function grade_set_preferences($course, $newprefs) {
    
    if (!isset($newprefs->use_advanced) or ($newprefs->use_advanced == 1)) {
        foreach ($newprefs as $name => $value) {        /// Just save them all
            grade_set_preference($course->id, $name, $value);
        }
        return true;
    }

/// We don't need advanced features, and we need to unset all extra features
/// So they don't affect grades    (This approach should be revisited because it resets everything!!)
    
    grade_set_preference($course->id, 'use_advanced', 0);
    grade_set_preference($course->id, 'use_weighted_for_letter', 0);
    grade_set_preference($course->id, 'display_weighted', 0);
    grade_set_preference($course->id, 'display_points', 2);
    grade_set_preference($course->id, 'display_percent', 0);
    grade_set_preference($course->id, 'display_letters', 0);
    
/// Lose all exceptions
    delete_records('grade_exceptions', 'courseid', $course->id);
        
    if (!$uncat = get_record('grade_category', 'courseid', $course->id, 'name', UNCATEGORISED)) {
        /// Make a category for uncategorised stuff
        $uncat->name=UNCATEGORISED;
        $uncat->courseid=$course->id;
        if (!$uncat->id = insert_record('grade_category', $uncat)) {
            error(get_string('errornocategorizedid','grades'));
        }
    }
    
    set_field('grade_item', 'category', $uncat->id, 'courseid', $course->id);
    set_field('grade_item', 'scale_grade', 1.00, 'courseid', $course->id);
    set_field('grade_item', 'extra_credit', 0, 'courseid', $course->id);

    set_field('grade_category', 'weight', 100.0, 'courseid', $course->id, 'id', $uncat->id);
    set_field('grade_category', 'bonus_points', '0', 'courseid', $course->id);
}


function grade_preferences_menu($action, $course) {

    if (!has_capability('moodle/course:managegrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
        return;
    }

    // remap some actions to simplify later code        
    switch ($action) {
        case 'prefs':
        case 'set_grade_preferences':
            $curraction = 'prefs';
            break;
        case 'cats':
        case 'vcats':
            $curraction = '';
            break;
        case 'insert_category':
        case 'assign_categories':
        case 'delete_category':
            $curraction = 'cats';
            break;
        case 'set_grade_weights':
        case 'weights':
            $curraction = 'weights';
            break;
        case 'letters':
        case 'set_letter_grades':
            $curraction = 'letters';
            break;
        case 'view_student_grades':
        case 'view_student_category_grades':
        case 'grades':
            $curraction = 'grades';
            break;
        case 'excepts':
            $curraction = 'excepts';
            break;

        default:
            $curraction = 'grades';
    }

    $tabs = $row = array();
    $row[] = new tabobject('grades', 'index.php?id='.$course->id,
                           get_string('viewgrades', 'grades'));
    $row[] = new tabobject('prefs', 'index.php?id='.$course->id.'&amp;action=prefs',
                           get_string('setpreferences', 'grades'));
    // only show the extra options if advanced is turned on, they don't do anything otherwise
    if (grade_get_preference($course->id, 'use_advanced') == 1) {
        $row[] = new tabobject('cats', 'index.php?id='.$course->id.'&amp;action=cats',
                               get_string('setcategories', 'grades'));
        $row[] = new tabobject('weights', 'index.php?id='.$course->id.'&amp;action=weights',
                               get_string('setweights', 'grades'));
        $row[] = new tabobject('letters', 'index.php?id='.$course->id.'&amp;action=letters',
                               get_string('setgradeletters', 'grades'));
        $row[] = new tabobject('excepts', 'exceptions.php?id='.$course->id.'&amp;action=excepts',
                               get_string('gradeexceptions', 'grades'));
    }
    $tabs[] = $row;

    print_tabs($tabs, $curraction);
}


function grade_nav($course, $action='grades') {
    global $CFG;
    global $USER;
    global $cview;

    $strgrades = get_string('grades', 'grades');
    $gradenav = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>";

    if (has_capability('moodle/course:managegrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
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
    
        if ($action=='grades') {
            $gradenav .= " -> $strgrades";
        } else {
            $gradenav .= " -> <a href=\"index.php?id=$course->id&amp;action=grades\">$strgrades</a>";
        }
        
        // if we are on a grades sub-page provide a link back (including grade preferences and grade items
        
        if (isset($strcurpage)) {
            $gradenav .= " -> $strcurpage";
        } else if($action =='vcats') {
            // show sub category
            if (isset($cview)) {
                $gradenav .= " -> $cview";
            }
        }

    } else {
        $gradenav .= " -> $strgrades";
    }
    
    return $gradenav;    
}

function grade_download($download, $id) {
    global $CFG;

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_capability('moodle/course:viewcoursegrades', get_context_instance(CONTEXT_COURSE, $id));

    $strgrades = get_string("grades");
    $strgrade = get_string("grade");
    $strmax = get_string("maximumshort");
    $stractivityreport = get_string("activityreport");

/// Check to see if groups are being used in this course
    $currentgroup = get_current_group($course->id);

    if ($currentgroup) {
        $students = get_group_students($currentgroup, "u.lastname ASC");
    } else {
        $students = grade_get_course_students($course->id);
    }

    if (!empty($students)) {
        foreach ($students as $student) {
            $grades[$student->id] = array();    // Collect all grades in this array
            $gradeshtml[$student->id] = array(); // Collect all grades html formatted in this array
            $totals[$student->id] = array();    // Collect all totals in this array
        }
    }
    $columns = array();     // Accumulate column names in this array.
    $columnhtml = array();  // Accumulate column html in this array.


/// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

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
                                if (!empty($modgrades->maxgrade)) {
                                    if ($mod->visible) {
                                        $maxgrade = "$strmax: $modgrades->maxgrade";
                                    } else {
                                        $maxgrade = "$strmax: $modgrades->maxgrade";
                                    }
                                } else {
                                    $maxgrade = "";
                                }

                                $columns[] = "$mod->modfullname: ".format_string($instance->name,true)." - $maxgrade";

                                if (!empty($students)) {
                                    foreach ($students as $student) {
                                        if (!empty($modgrades->grades[$student->id])) {
                                            $grades[$student->id][] = $currentstudentgrade = $modgrades->grades[$student->id];
                                        } else {
                                            $grades[$student->id][] = $currentstudentgrade = "";
                                            $gradeshtml[$student->id][] = "";
                                        }
                                        if (!empty($modgrades->maxgrade)) {
                                            $totals[$student->id] = (float)($totals[$student->id]) + (float)($currentstudentgrade);
                                        } else {
                                            $totals[$student->id] = (float)($totals[$student->id]) + 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } // a new Moodle nesting record? ;-)

/// OK, we have all the data, now present it to the user
/// OK, we have all the data, now present it to the user
    if ($download == "ods" and confirm_sesskey()) {
        require_once("../lib/odslib.class.php");

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname $strgrades.ods");
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($strgrades);
    
    /// Print names of all the fields
        $myxls->write_string(0,0,get_string("firstname"));
        $myxls->write_string(0,1,get_string("lastname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("institution"));
        $myxls->write_string(0,4,get_string("department"));
        $myxls->write_string(0,5,get_string("email"));
        $pos=6;
        foreach ($columns as $column) {
            $myxls->write_string(0,$pos++,strip_tags($column));
        }
        $myxls->write_string(0,$pos,get_string("total"));
    
    /// Print all the lines of data.
        $i = 0;
        if (!empty($grades)) {
            foreach ($grades as $studentid => $studentgrades) {
                $i++;
                $student = $students[$studentid];
                if (empty($totals[$student->id])) {
                    $totals[$student->id] = '';
                }
        
                $myxls->write_string($i,0,$student->firstname);
                $myxls->write_string($i,1,$student->lastname);
                $myxls->write_string($i,2,$student->idnumber);
                $myxls->write_string($i,3,$student->institution);
                $myxls->write_string($i,4,$student->department);
                $myxls->write_string($i,5,$student->email);
                $j=6;
                foreach ($studentgrades as $grade) {
                    if (is_numeric($grade)) {
                        $myxls->write_number($i,$j++,strip_tags($grade));
                    }
                    else {
                        $myxls->write_string($i,$j++,strip_tags($grade));
                    }
                }
                $myxls->write_number($i,$j,$totals[$student->id]);
            }
        }

    /// Close the workbook
        $workbook->close();
    
        exit;

    } else if ($download == "xls" and confirm_sesskey()) {
        require_once("../lib/excellib.class.php");

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname $strgrades.xls");
    /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($strgrades);
    
    /// Print names of all the fields
        $myxls->write_string(0,0,get_string("firstname"));
        $myxls->write_string(0,1,get_string("lastname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("institution"));
        $myxls->write_string(0,4,get_string("department"));
        $myxls->write_string(0,5,get_string("email"));
        $pos=6;
        foreach ($columns as $column) {
            $myxls->write_string(0,$pos++,strip_tags($column));
        }
        $myxls->write_string(0,$pos,get_string("total"));
    
    /// Print all the lines of data.
        $i = 0;
        if (!empty($grades)) {
            foreach ($grades as $studentid => $studentgrades) {
                $i++;
                $student = $students[$studentid];
                if (empty($totals[$student->id])) {
                    $totals[$student->id] = '';
                }
        
                $myxls->write_string($i,0,$student->firstname);
                $myxls->write_string($i,1,$student->lastname);
                $myxls->write_string($i,2,$student->idnumber);
                $myxls->write_string($i,3,$student->institution);
                $myxls->write_string($i,4,$student->department);
                $myxls->write_string($i,5,$student->email);
                $j=6;
                foreach ($studentgrades as $grade) {
                    if (is_numeric($grade)) {
                        $myxls->write_number($i,$j++,strip_tags($grade));
                    }
                    else {
                        $myxls->write_string($i,$j++,strip_tags($grade));
                    }
                }
                $myxls->write_number($i,$j,$totals[$student->id]);
            }
        }

    /// Close the workbook
        $workbook->close();
    
        exit;

    } else if ($download == "txt" and confirm_sesskey()) {

/// Print header to force download

        header("Content-Type: application/download\n"); 
        $downloadfilename = clean_filename("$course->shortname $strgrades");
        header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");

/// Print names of all the fields

        echo get_string("firstname")."\t".
             get_string("lastname")."\t".
             get_string("idnumber")."\t".
             get_string("institution")."\t".
             get_string("department")."\t".
             get_string("email");
        foreach ($columns as $column) {
            $column = strip_tags($column);
            echo "\t$column";
        }
        echo "\t".get_string("total")."\n";
    
/// Print all the lines of data.
        foreach ($grades as $studentid => $studentgrades) {
            $student = $students[$studentid];
            if (empty($totals[$student->id])) {
                $totals[$student->id] = '';
            }
            echo "$student->firstname\t$student->lastname\t$student->idnumber\t$student->institution\t$student->department\t$student->email";
            foreach ($studentgrades as $grade) {
                $grade = strip_tags($grade);
                echo "\t$grade";
            }
            echo "\t".$totals[$student->id];
            echo "\n";
        }
    
        exit;
    
    }else if ($download == '' and confirm_sesskey()) {
        error("No file type specified");
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
                        
                        if ($stats[$cur_category]['totalpoints'] == get_string('excluded', 'grades')) {
                            $stats[$cur_category]['totalpoints'] = 1;
                            $stats['all']['sum'] = $stats['all']['sum'] + $stats[$cur_category]['sum'];
                            $stats['all']['items']  = $stats['all']['items'] + $stats[$cur_category]['items'];
                            $stats['all']['totalpoints'] = $stats['all']['totalpoints'] + $stats[$cur_category]['totalpoints'];
                            $stats['all']['weighted_sum'] = $stats['all']['weighted_sum'] + ($stats[$cur_category]['sum']/($stats[$cur_category]['totalpoints']))*$stats[$cur_category]['weight'];
                        }
                        
                        else if (isset($stats['all'])) {
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
    echo  '<table align="center"><tr><th colspan="3" scope="col">'.$category.' '.get_string('stats','grades').'</th></tr>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<tr><th scope="col">&nbsp;</th><th scope="col">'.get_string('points','grades').'<th scope="col">'.get_string('weight','grades').'</th></tr>';            
    }

    echo  '<tr><td align="right">'.get_string('max','grades').':</td><td align="right">'.$stats[$category]['max'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<td align="right">'.$stats[$category]['max_weighted'].'</td>';            
    }
    echo  '</tr>';
    
    echo  '<tr><td align="right">'.get_string('min','grades').':</td><td align="right">'.$stats[$category]['min'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<td align="right">'.$stats[$category]['min_weighted'].'</td>';            
    }
    echo  '</tr>';
    
    echo  '<tr><td align="right">'.get_string('average','grades').':</td><td align="right">'.$stats[$category]['average'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<td align="right">'.$stats[$category]['average_weighted'].'</td>';            
    }
    echo  '</tr>';
    
    echo  '<tr><td align="right">'.get_string('median','grades').':</td><td align="right">'.$stats[$category]['median'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<td align="right">'.$stats[$category]['median_weighted'].'</td>';            
    }
    echo  '</tr>';
    
    echo  '<tr><td align="right">'.get_string('mode','grades').':</td><td align="right">'.$stats[$category]['mode'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<td align="right">'.$stats[$category]['mode_weighted'].'</td>';            
    }
    echo  '</tr>';
    
    echo  '<tr><td align="right">'.get_string('standarddeviation','grades').':</td><td align="right">'.$stats[$category]['stddev'].'</td>';
    if ($preferences->show_weighted == 1 && $preferences->use_weighted_for_letter == 1 && $category== 'all') {
        echo  '<td align="right">'.$stats[$category]['stddev_weighted'].'</td>';            
    }
    echo  '</tr>';
    echo  '</table>';
    print_footer();
}

function grade_view_category_grades($view_by_student) {
    global $CFG;
    global $course;
    global $USER;
    global $preferences;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    
    // if can't see course grades, print single grade view
    if (!has_capability('moodle/course:viewcoursegrades', $context)) {
        $view_by_student = $USER->id;
    }

    if ($preferences->use_advanced == 0) {
        $cview = UNCATEGORISED;
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

            $grade_columns = $preferences->show_weighted + $preferences->show_points + $preferences->show_percent;

            $first = 0;
            //$maxpoints = 0;
            $maxpercent = 0;
            $reprint = 0;
            if (has_capability('moodle/course:viewcoursegrades', $context)) {
                $student_heading_link = get_string('student','grades');
                //only set sorting links if more than one student displayed.
                if ($view_by_student == -1) {
                    $student_heading_link .='<br /><a href="?id='.$course->id.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=lastname">'.get_string('sortbylastname','grades').'</a>';
                    $student_heading_link .= '<br /><a href="?id='.$course->id.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=firstname">'.get_string('sortbyfirstname','grades').'</a>';
                }
                else {
                    $student_heading_link .= '<br /><a href="?id='.$course->id.'&amp;action=vcats&amp;cview='.$cview.'">'.get_string('showallstudents','grades').'</a>';
                }
            }
            echo '<table align="center" class="grades">';
            if (has_capability('moodle/course:viewcoursegrades', $context)) {
                $header = '<tr class="header"><th rowspan="2" scope="col">'.$student_heading_link.'</th>';
            }
            else {
                $header = '<tr class="header">';
            }
            $header1 = '<tr class="header">';
            
            // to keep track of what we've output
            $colcount = 0;
            $oddrow = true;
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
            /// Make sure $item_order is initialised (bug 3424)
            if (empty($item_order)) $item_order = array();
            
            ksort($item_order);
            
            foreach($grades_by_student as $student => $categories) {
                
                if ($preferences->reprint_headers != 0 && $reprint >= $preferences->reprint_headers) {
                    echo  $header.$header1.'</tr>';
                    $reprint=0;
                }
                
                // alternate row classes
                $row = ($oddrow) ? '<tr class="r0">' : '<tr class="r1">';
                $oddrow = !$oddrow;

                // reset the col classes
                $oddcol = true;

                    
                // set the links to student information based on multiview or individual... if individual go to student info... if many go to individual grades view.
                if (has_capability('moodle/course:viewcoursegrades', $context)) {
                    if ($view_by_student != -1) {
                        $student_link = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$student.'&amp;course='.$course->id.'">';
                    }
                    else {
                        $student_link = '<a href="?id='.$course->id.'&amp;action=vcats&amp;user='.$student.'&amp;cview='.$cview.'">';
                    }
                    $student_link .= $grades_by_student[$student]['student_data']['lastname'].', '.$grades_by_student[$student]['student_data']['firstname'].'</a>';
                    $row .= '<th class="fullname" scope="row">'.$student_link.'</th>';
                }
                
                foreach($categories as $category => $items) {
                    if ($category == $cview) {
                        // make sure that the grades come out in the same order
                        foreach($item_order as $order=>$assignment) {
                            
                            $class = $all_categories[$category][$assignment]['modname'];

                            if ($assignment != 'stats') {
                                    
                                if ($first == 0) {
                                    $colcount++;
                                    $link_id = grade_get_module_link($course->id, $all_categories[$category][$assignment]['cminstance'], $all_categories[$category][$assignment]['modid']);

                                    $link = $CFG->wwwroot.'/mod/'.$all_categories[$category][$assignment]['modname'].'/view.php?id='.$link_id->id;
                                    $all_categories[$category][$assignment]['link'] = $link;
                                    if ($all_categories[$category][$assignment]['hidden'] == 0) {
                                        $header .= '<th class="'.$class.'" colspan="'.$grade_columns.'" scope="col"><a href="'.$link.'">'.format_string($assignment,true).'</a>';
                                    }
                                    else {
                                        $header .= '<th class="'.$class.'" colspan="'.$grade_columns.'" scope="col"><a class="dimmed" href="'.$link.'">'.format_string($assignment,true).'</a>';
                                    }
                                    if ($all_categories[$category][$assignment]['extra_credit'] == 1) {
                                        $header .= '<span class="extracredit">('.get_string('extracredit','grades').')</span>'; 
                                    }
                                    $header .='</th>';
                                    if ($preferences->show_points) {
                                        $header1 .= '<th class="'.$class.'" scope="col">'. $all_categories[$category][$assignment]['maxgrade'];
                                        if ($all_categories[$category][$assignment]['grade_against'] != $all_categories[$category][$assignment]['maxgrade']) {
                                            $header1 .= '('. $all_categories[$category][$assignment]['grade_against'].')';
                                        }
                                        $header1 .= '</th>';
                                    }
                                                                        
                                    if($preferences->show_percent)    {
                                        if ($all_categories[$category][$assignment]['grade_against'] != $all_categories[$category][$assignment]['maxgrade']) {
                                            $header1 .= '<th class="'.$class.'" scope="col">'.get_string('scaledpct','grades').'</th>';
                                        }
                                        else {
                                            $header1 .= '<th class="'.$class.'" scope="col">'.get_string('rawpct','grades').'</th>';
                                        }
                                    }
                                    if ($preferences->show_weighted) {
                                        if ($all_categories[$category]['stats']['totalpoints'] != 0) {
                                            $cur_weighted_max = sprintf("%0.2f", $all_categories[$category][$assignment]['grade_against']/$all_categories[$category]['stats']['totalpoints']*$all_categories[$category]['stats']['weight']);
                                        }
                                        else {
                                            $cur_weighted_max = 0;
                                        }
                                        $header1 .= '<th scope="col">'.$cur_weighted_max.get_string('pctoftotalgrade','grades').'</th>';
                                    }
                                }

                                // display points 
                                if ($preferences->show_points) { 
                                    $class .= ($oddcol) ? ' c0 points' : ' c1 points';
                                    $oddcol = !$oddcol;
                                    $row .= '<td class="'.$class.'"><a href="'.$all_categories[$category][$assignment]['link'].'">' . $items[$assignment]['grade'] . '</a></td>';
                                }

                                if ($preferences->show_percent) {
                                    $class .= ($oddcol) ? ' c0 percent' : ' c1 percent';
                                    $oddcol = !$oddcol;
                                    $row .= '<td class="'.$class.'">'. $items[$assignment]['percent'].'%</td>';
                                }
                                
                                if ($preferences->show_weighted) {
                                    $class .= ($oddcol) ? ' c0 weighted' : ' c1 weighted';
                                    $oddcol = !$oddcol;
                                    $row .= '<td class="'.$class.'">'.$items[$assignment]['weighted'].'%</td>';
                                }
                            }
                        }
                    } else {
                        $class = '';
                    }
                }
                
                if ($first == 0) {
                    if (has_capability('moodle/course:viewcoursegrades', $context) && $view_by_student == -1) {
                        $total_sort_link = '<a href="?id='.$course->id.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=highgrade_category"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('highgradedescending','grades').'" /></a>';
                        $total_sort_link .= '<a href="?id='.$course->id.'&amp;action=vcats&amp;cview='.$cview.'&amp;sort=highgrade_category_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('highgradeascending','grades').'" /></a>';
                    }
                    else {
                        $total_sort_link = '';
                    }
                    
                    $stats_link = '<a href="javascript:void(0)" onclick="window.open(\'?id='.$course->id.'&amp;action=stats&amp;category='.$cview.'\',\''.get_string('statslink','grades').'\',\'height=200,width=300,scrollbars=no\')">'.get_string('statslink','grades').'</a>';
                    if ($all_categories[$cview]['stats']['drop'] != 0) {
                        $header .= '<th class="'.$class.'" colspan="'.$grade_columns.'" scope="col">'.get_string('total','grades').'&nbsp; (Lowest '. $all_categories[$cview]['stats']['drop']. ' Dropped)'.$total_sort_link.' '.$stats_link.'</th>';
                    }
                    else {
                        $header .= '<th class="'.$class.'" colspan="'.$grade_columns.'" scope="col">'.get_string('total','grades').'&nbsp;'.$total_sort_link.' '.$stats_link.'</th>';
                    }
                    
                    if ($preferences->show_points) {
                        $header1 .= '<th class="'.$class.'" scope="col">'.$all_categories[$cview]['stats']['totalpoints'];
                        if ($all_categories[$cview]['stats']['bonus_points'] != 0) {
                            $header1 .='(+'.$all_categories[$cview]['stats']['bonus_points'].')';
                        }
                        $header1 .='</th>';
                    }
                    if ($preferences->show_percent) {
                        $header1 .= '<th class="'.$class.'" scope="col">'.get_string('percent','grades').'</th>';
                    }
                    
                    
                    if ($preferences->show_weighted) {
                        $header1 .= '<th class="'.$class.'" scope="col">'.$all_categories[$cview]['stats']['weight'].get_string('pctoftotalgrade','grades').'</th>';
                    }
                    
                    if (has_capability('moodle/course:viewcoursegrades', $context)) {
                        $header .= '<th rowspan="2" scope="col">'.$student_heading_link.'</th></tr>';
                    }
                    else {
                        $header .= '</tr>';
                    }
                    
                    //adjust colcount to reflect the actual number of columns output
                    $colcount++; // total column
                    $colcount = $colcount*$grade_columns + 2;
                    echo  '<tr class="title"><th colspan="'.$colcount.'" scope="col">';
                    if ($preferences->use_advanced != 0) {
                        echo  $cview.' '.get_string('grades','grades');
                    }
                    else {
                        echo  get_string('grades','grades');
                    }

                    if (has_capability('moodle/course:viewcoursegrades', $context)) {
                        helpbutton('teacher', get_string('gradehelp','grades'), 'grade');
                    }
                    else {
                        helpbutton('student', get_string('gradehelp','grades'), 'grade');
                    }
                    echo  '</th></tr>';
                    echo  $header;
                    echo  $header1.'</tr>';
                    $first = 1;
                }

                // total points for category
                if ($preferences->show_points) {
                    $class .= ($oddcol) ? ' c0 points' : ' c1 points';
                    $oddcol = !$oddcol;
                    $row .= '<td class="'.$class.'">'.$grades_by_student[$student][$cview]['stats']['points'].'</td>';
                }
                
                // total percent for category
                if ($preferences->show_percent) {
                    $class .= ($oddcol) ? ' c0 percent' : ' c1 percent';
                    $oddcol = !$oddcol;
                    $row .= '<td class="'.$class.'">'.$grades_by_student[$student][$cview]['stats']['percent'].'%</td>';
                }
                

                // total weighted for category
                if ($preferences->show_weighted) {
                    $class .= ($oddcol) ? ' c0 weighted' : ' c1 weighted';
                    $oddcol = !$oddcol;
                    $row .= '<td class="'.$class.'">'.$grades_by_student[$student][$cview]['stats']['weighted'].'%</td>';
                }

                if (has_capability('moodle/course:viewcoursegrades', $context)) {
                    $row .= '<td class="fullname">'.$student_link.'</td>';
                }
                $row .= '</tr>';
                echo  $row;
                $reprint++;
            }
            echo  '</table>';
        }
        else { // no grades returned
            error(get_string('nogradesreturned','grades'), $CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
    }
    else {
        error(get_string('nocategoryview','grades'), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
}

function grade_view_all_grades($view_by_student) { // if mode=='grade' then we are in user view
// displays all grades for the course
    global $CFG;
    global $course;
    global $USER;
    global $preferences;
    
    if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        return false;
    }

    // if can't see course grades, print single grade view
    if (!has_capability('moodle/course:viewcoursegrades', $context)) {
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
        
        $grade_columns = $preferences->show_weighted + $preferences->show_points + $preferences->show_percent;
        
        $first = 0;
        $total_course_points = 0;
        $maxpercent = 0;
        $reprint=0;
        
        echo  '<table align="center" class="grades">';
        if (has_capability('moodle/course:viewcoursegrades', $context)) {
            $student_heading_link = get_string('student','grades');
            if ($view_by_student == -1) {
                $student_heading_link .='<a href="?id='.$course->id.'&amp;action=grades&amp;sort=lastname"><br /><font size="-2">'.get_string('sortbylastname','grades').'</font></a>';
                $student_heading_link .= '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=firstname"><br /><font size="-2">'.get_string('sortbyfirstname','grades').'</font></a>';
            }
            else {
                $student_heading_link .= '<br /><a href="?id='.$course->id.'&amp;&amp;action=grades"><font size="-2">'.get_string('showallstudents','grades').'</font></a>';
            }
            $header = '<tr><th rowspan="2" scope="col">'.$student_heading_link.'</th>';
        }
        else {
            $header = '<tr>';
        }
        $header1 = '<tr>';
        
        $rowcount = 0;
        $oddrow = true;
        $colcount = 0;
  
        foreach($grades_by_student as $student => $categories) {
 
            $totalpoints = 0;
            $totalgrade = 0;
            $total_bonus_points = 0;
            if ($preferences->reprint_headers != 0 && $reprint >= $preferences->reprint_headers) {
                echo  $header.$header1;
                $reprint=0;
            }
            
            // alternate row classes
            $row = ($oddrow) ? '<tr class="r0">' : '<tr class="r1">';
            $oddrow = !$oddrow;
            
            // set the links to student information based on multiview or individual... if individual go to student info... if many go to individual grades view.
            if (has_capability('moodle/course:viewcoursegrades', $context)) {
                if ($view_by_student != -1) {
                    $studentviewlink = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$student.'&amp;course='.$course->id.'">'.$grades_by_student[$student]['student_data']['lastname'].', '.$grades_by_student[$student]['student_data']['firstname'].'</a>';
                }
                else {
                    $studentviewlink = '<a href="?id='.$course->id.'&amp;action=view_student_grades&amp;user='.$student.'">'.$grades_by_student[$student]['student_data']['lastname'].', '.$grades_by_student[$student]['student_data']['firstname'].'</a>';
                }
                $row .= '<th scope="row">'. $studentviewlink .'</th>';
            }
            ksort($categories);

            foreach($categories as $category => $items) {
                if ($category != 'student_data') {
                   
                    if ($first == 0) {
                        $colcount++;
                        if ($category == UNCATEGORISED) {
                            $categoryname = get_string(UNCATEGORISED, 'grades');
                        } else {
                            $categoryname = $category;
                        }
                        // only print the category headers if something is displayed for them
                        if ($preferences->show_weighted || $preferences->show_percent || $preferences->show_points) {
                            $stats_link = '<a href="javascript:void(0)" onclick="window.open(\'?id='.$course->id.'&amp;action=stats&amp;category='.$category.'\',\''.get_string('statslink','grades').'\',\'height=200,width=300,scrollbars=no\')"><font size="-2">'.get_string('statslink','grades').'</font></a>';
                            $header .= '<th colspan="'.$grade_columns.'" scope="col"><a href="?id='.$course->id.'&amp;action=vcats&amp;cview='.$category;
                            if ($view_by_student != -1) {
                                $header .= '&amp;user='.$view_by_student;
                            }
                            $header .='">'. $categoryname .' '.$stats_link.'</a>';
                        }
                        if ($preferences->display_weighted != 0) {
                            $header .= '('. $all_categories[$category]['stats']['weight'] . '%)';
                        }
                        $header .= '</th>';
                        if ($preferences->show_points) {
                            $header1 .= '<th scope="col">'.get_string('points','grades').'('.$all_categories[$category]['stats']['totalpoints'].')';
                            if ($all_categories[$category]['stats']['bonus_points'] != 0) {
                                $header1 .='(+'.$all_categories[$category]['stats']['bonus_points'].')';
                            }
                            $header1 .='</th>';
                        }
                        if ($preferences->show_percent) {
                            $header1 .= '<th scope="col">'.get_string('percent','grades').'</th>';
                        }
                        if ($preferences->show_weighted) {
                            $header1 .= '<th scope="col">'.get_string('weightedpctcontribution','grades').'</th>';
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
                if ($preferences->show_letters) {
                    $total_columns = $grade_columns + 1;
                }
                else {
                    $total_columns = $grade_columns;
                }
                
                if (has_capability('moodle/course:viewcoursegrades', $context) && $view_by_student == -1) {
                    $grade_sort_link = '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=highgrade"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('highgradedescending','grades').'" /></a>';
                    $grade_sort_link .= '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=highgrade_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('highgradeascending','grades').'" /></a>';
                    $points_sort_link = '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=points"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('pointsdescending','grades').'" /></a>';
                    $points_sort_link .= '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=points_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('pointsascending','grades').'" /></a>';
                    $weighted_sort_link = '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=weighted"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('weighteddescending','grades').'" /></a>';
                    $weighted_sort_link .= '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=weighted_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('weightedascending','grades').'" /></a>';
                    $percent_sort_link = '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=percent"><img src="'.$CFG->wwwroot.'/pix/t/down.gif" alt="'.get_string('percentdescending','grades').'" /></a>';
                    $percent_sort_link .= '<a href="?id='.$course->id.'&amp;action=grades&amp;sort=percent_asc"><img src="'.$CFG->wwwroot.'/pix/t/up.gif" alt="'.get_string('percentascending','grades').'" /></a>';
                }
                $stats_link = '<a href="javascript:void(0)" onclick="window.open(\'?id='.$course->id.'&amp;action=stats&amp;category=all\',\''.get_string('statslink','grades').'\',\'height=200,width=300,scrollbars=no\')"><font size="-2">'.get_string('statslink','grades').'</font></a>';
                $header .= '<th colspan="'.$total_columns.'" scope="col">'.get_string('total','grades').'&nbsp;'.$stats_link.'</th>';
                if (has_capability('moodle/course:viewcoursegrades', $context) && $view_by_student == -1) {
                    if ($preferences->show_points) {
                        $header1 .= '<th scope="col">'.get_string('points','grades').'('.$all_categories['stats']['totalpoints'].')';
                        if ($category != 'student_data' && $all_categories[$category]['stats']['bonus_points'] != 0) {

                            $header1 .='(+'.$total_bonus_points.')';
                        }
                        $header1 .= '<br />'.$points_sort_link.' '.'</th>';
                    }
                    if ($preferences->show_percent) {
                        $header1 .= '<th scope="col">'.get_string('percentshort','grades').'<br />'.$percent_sort_link.' '.'</th>';
                    }
                    if ($preferences->show_weighted) {
                        $header1 .= '<th scope="col">'.get_string('weightedpct','grades').'('.$all_categories['stats']['weight'].')'.'<br />'.$weighted_sort_link.' '.'</th>';
                    }
                    if ($preferences->show_letters) {
                        $header1 .= '<th scope="col">'.get_string('lettergrade','grades').'<br />'.$grade_sort_link.' '.'</th>';
                    }
                    $header1 .= '</tr>';
                }
                else {
                    if ($preferences->show_points) {
                        $header1 .= '<th scope="col">'.get_string('points','grades').'('.$all_categories['stats']['totalpoints'].')';
                        if ($category != 'student_data' && $all_categories[$category]['stats']['bonus_points'] != 0) {
                            $header1 .='(+'.$total_bonus_points.')';
                        }
                        $header1 .= '</th>';
                    }
                    if ($preferences->show_percent) {
                        $header1 .= '<th scope="col">'.get_string('percentshort','grades').'</th>';
                    }
                    if ($preferences->show_weighted) {
                        $header1 .= '<th scope="col">'.get_string('weightedpct','grades').'('.$all_categories['stats']['weight'].')</th>';
                    }
                    if ($preferences->show_letters) {
                        $header1 .= '<th scope="col">'.get_string('lettergrade','grades').'</th>';
                    }
                    $header1 .= '</tr>';
                }
                if (has_capability('moodle/course:viewcoursegrades', $context)) {
                    $header .= '<th rowspan="2" scope="col">'.$student_heading_link.'</th></tr>';
                }
                // adjust colcount to reflect actual number of columns output
                $colcount = $colcount * $grade_columns + $total_columns + 2;
  
                echo  '<tr><th colspan="'.$colcount.'" scope="col"><font size="+1">'.get_string('allgrades','grades').'</font>';
                if (has_capability('moodle/course:viewcoursegrades', $context)) {
                    helpbutton('teacher', get_string('gradehelp','grades'), 'grade');
                }
                else {
                    helpbutton('student', get_string('gradehelp','grades'), 'grade');
                }
                echo  '</th></tr>';
                
                                
                echo  $header;
                echo  $header1;
                $first = 1;
            }
            if ($preferences->show_points) {
                $row .= '<td align="right">'.$grades_by_student[$student]['student_data']['points'].'</td>';
            }
            if ($preferences->show_percent) {
                $row .= '<td align="right">'.$grades_by_student[$student]['student_data']['percent'].'%</td>';
            }
            if ($preferences->show_weighted) {
                $row .= '<td align="right">'.$grades_by_student[$student]['student_data']['weighted'].'%</td>'; 
            }
            if ($preferences->show_letters) {
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
            if (has_capability('moodle/course:viewcoursegrades', $context)) {
                $row .= '<td>'. $studentviewlink .'</td></tr>';
            }
            else {
                $row .= '</tr>';
            }

            echo $row;           
            $reprint++;
        }
        echo  '</table>';
    }
    else { // no grades returned
        error(get_string('nogradesreturned','grades'));
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
            $form_catname = preg_replace('/[.| ]/', '_', $category->name);

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
                echo  '<center><font color="red">'.get_string('nonumericweight','grades').
                        format_string($category->name) .': "'.$submitted_category.'"</font></center><br />';
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
        echo  '<form id="grade_weights" action="./index.php" method="post">';
        echo  '<table border="0" cellspacing="2" cellpadding="5" align="center" class="generalbox">';
        echo  '<tr><th colspan="5" class="header" scope="col">'.get_string('setweights','grades');
        helpbutton('weight', get_string('gradeweighthelp','grades'), 'grade');
        echo  '</th></tr>';
        echo  '<tr><td align="center" class="generaltableheader">'.get_string('category','grades').'</td>';
        echo  '<td align="center" class="generaltableheader">'.get_string('weight','grades').'</td>';
        echo  '<td align="center" class="generaltableheader">'.get_string('dropxlowest','grades').'</td>';
        echo  '<td align="center" class="generaltableheader">'.get_string('bonuspoints','grades').'</td>';
        echo  '<td align="center" class="generaltableheader">'.get_string('hidecategory','grades').'</td>';
        echo  '</tr>';
        echo  '<input type="hidden" name="id" value="'.$course->id.'" />';
        echo  '<input type="hidden" name="action" value="set_grade_weights" />';
        echo  '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';

        $sum = 0;
        
        foreach($categories as $category) {
            $val = $category->weight;
            $sum = $sum + $val;
                        
            // make names form safe
            $form_catname = str_replace(' ', '_', $category->name);
            if ($category->name == UNCATEGORISED) {
                $category->name = get_string(UNCATEGORISED, 'grades');
            }
            echo  '<tr><td align="center" class="generalboxcontent">'. format_string($category->name) .'</td>';
            echo  '<td align="center" class="generalboxcontent"><input type="text" size="5" name="'.$form_catname.'" value="'.$val.'" /></td>';
            echo  '<td align="center" class="generalboxcontent"><input type="text" size="5" name="drop_x_lowest'.$form_catname.'" value="'.$category->drop_x_lowest.'" /></td>';
            echo  '<td align="center" class="generalboxcontent"><input type="text" size="5" name="bonus_points'.$form_catname.'" value="'.$category->bonus_points.'" /></td>';
            echo  '<td align="center" class="generalboxcontent"><input type="checkbox" name="hidden'.$form_catname.'" ';
            if ($category->hidden == 1) {
                echo  ' checked="checked"';
            }
            echo  ' /></td></tr>';
        }
        echo  '<tr><td colspan="5" align="center" class="generalboxcontent">';
        echo  '<input type="submit" value="'.get_string('savechanges','grades').'" />';
        echo  '</td></tr>';
        if ($sum != 100) {
            echo  '<tr><td colspan="5" align="center" class="generalboxcontent"><font color="red">'.get_string('totalweightnot100','grades').'</font></td></tr>';
        }
        else {
            echo  '<tr><td colspan="5" align="center" class="generalboxcontent"><font color="green">'.get_string('totalweight100','grades').'</font></td></tr>';
        }
    }
    else {
        /// maybe this should just do the default population of the categories instead?
        echo  '<font color="red">'.get_string('setcategorieserror','grades').'</font>';
    }
    echo  '</table>';
    echo  '</form>';
    echo  '<center>'.get_string('dropxlowestwarning','grades').'</center><br />';
}

function grade_set_categories() {
    global $CFG;
    global $course;
    global $USER;
    

    /// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
    
    echo  '<table border="0" cellspacing="2" cellpadding="5" align="center" class="generalbox">';
    echo  '<tr><th colspan="5" class="header" scope="col">'.get_string('setcategories','grades');
    helpbutton('category', get_string('gradecategoryhelp','grades'), 'grade');
    echo  '</th></tr>';
    echo  '<tr><td align="center" class="generaltableheader">'.get_string('gradeitem','grades').'</td>';
    echo  '<td align="center" class="generaltableheader">'.get_string('category','grades').'</td>';
    echo  '<td align="center" class="generaltableheader">'.get_string('maxgrade','grades').'</td>';
    echo  '<td align="center" class="generaltableheader">'.get_string('curveto','grades').'</td>';
    echo  '<td align="center" class="generaltableheader">'.get_string('extracredit','grades').'</td></tr>';
    echo  '<form id="set_categories" method="post" action="./index.php" >';
    echo  '<input type="hidden" name="action" value="assign_categories" />';
    echo  '<input type="hidden" name="id" value="'.$course->id.'" />';
    echo  '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    
    $itemcount = 0;
    
    /// Search through all the modules, pulling out grade data
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    for ($i=0; $i<=$course->numsections; $i++) {
        if (isset($sections[$i])) {   // should always be true
            $section = $sections[$i];
            if ($section->sequence) {
                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    if (empty($mods[$sectionmod])) {
                        continue;
                    }
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
                                    //echo  "<b>modname: $mod->modname id: $mod->id course: $mod->course</b><br />";
                                    echo  '<input type="hidden" name="modname'.$itemcount.'" value="'.$mod->modname.'" />';
                                    echo  '<input type="hidden" name="mod'.$itemcount.'" value="'.$mod->instance.'" />';
                                    echo  '<input type="hidden" name="course'.$itemcount.'" value="'.$mod->course.'" />';
                                    echo  '<tr><td align="center" class="generalboxcontent">';
                                    // get instance name from db.
                                    $instance = get_record($mod->modname, 'id', $mod->instance);
                                    echo  format_string($instance->name)."</td>";
                                    // see if the item is already in the category table and if it is call category select with the id so it is selected
                                    echo  '<td align="center" class="generalboxcontent"><select name="category'.$itemcount.'">';
                                    $item_cat_id = get_record('grade_item', 'modid', $mod->module, 'courseid', $course->id, 'cminstance', $mod->instance);
                                    //print_object($item_cat_id);
                                    if (isset($item_cat_id)) {
                                        grade_category_select($item_cat_id->category);
                                    }
                                    else {
                                        grade_category_select(-1);
                                    }
                                    echo  '</select></td><td align="center" class="generalboxcontent">'.$modgrades->maxgrade.'<input type="hidden" name="maxgrade'.$itemcount.'" value="'.$modgrades->maxgrade.'" /></td>';
                                        
                                    if (isset($item_cat_id)) {
                                        // the value held in scale_grade is a scaling percent. The next line just formats it so it is easier for the user (they just enter the point value they want to be 100%)
                                        if ($item_cat_id->scale_grade == '' || $item_cat_id->scale_grade == 0)
                                            $scale_to = $modgrades->maxgrade;
                                        else
                                            $scale_to = round($modgrades->maxgrade/$item_cat_id->scale_grade);
                                        echo  '<td align="center" class="generalboxcontent"><input type="text" size="5" name="scale_grade'.$itemcount.'" value="'.$scale_to.'" /></td>';
                                    }
                                    else {
                                        echo  '<td align="center" class="generalboxcontent"><input type="text" size="5" name="scale_grade'.$itemcount.'" value="'.$modgrades->maxgrade.'" /></td>';
                                    }
                                    
                                    echo  '<td align="center" class="generalboxcontent"><input type="checkbox" name="extra_credit'.$itemcount.'" ';
                                    if ($item_cat_id->extra_credit == 1) {
                                        echo  ' checked="checked"';
                                    }
                                    echo  ' /></td></tr>';
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    echo  '<input type="hidden" name="totalitems" value="'.$itemcount.'" />';
    echo  '<tr><td colspan="5" align="center" class="generalboxcontent"><input type="submit" value="'.get_string('savechanges','grades').'" /></td></tr>';
    echo  '</form>';
    echo  '<tr><td colspan="5" align="center" class="generalboxcontent">';
    grade_add_category_form();
    echo  '</td></tr><tr><td colspan="5" align="center" class="generalboxcontent">';
    grade_delete_category_form();
    echo  '</td></tr></table>';
    echo '<center>'.get_string('extracreditwarning','grades').'</center>';
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
        if ($cur_extra_credit) {
        $cur_extra_credit = 1;
        } else {
            $cur_extra_credit = 0;
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

            set_field('grade_item', 'extra_credit', $cur_extra_credit, 'id', $db_cat->id);
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
    echo  '<form id="new_category">';
    echo  get_string('addcategory','grades').':<input type="text" name="name" size="20" />';
    echo  '<input type="hidden" name="id" value="'.$course->id.'" />';
    echo  '<input type="hidden" name="action" value="insert_category" />';
    echo  '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo  '<input type="submit" value="'.get_string('addcategory','grades').'" />';
    echo  '</form>';
}

function grade_delete_category_form() {
    // outputs a form to delete a category
    global $course;
    global $USER;
    echo  '<form id="delete_category">';
    echo  get_string('deletecategory','grades').': <select name="category_id">';
    grade_category_select();
    echo  '</select><input type="hidden" name="id" value="'.$course->id.'" />';
    echo  '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo  '<input type="hidden" name="action" value="delete_category" />';
    echo  '<input type="submit" value="'.get_string('deletecategory','grades').'" /></form>';
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
    if (record_exists('grade_category', 'name', $category->name, 'courseid', $category->courseid)) {
            // category already exists
    }
    elseif ($category->name != ''){
        if (!insert_record('grade_category', $category) ) {
            echo  '<font color="red">'.get_string('addcategoryerror','grades').'</font>';
        }
    }
}

function grade_category_select($id_selected = 0) {
    /// prints out a select box containing categories.
    global $CFG;
    global $course;

    
    echo  '<option value="blank">'.get_string('choosecategory','grades').'</option>';
    
    $categories = get_records('grade_category', 'courseid', $course->id, 'name');
    
    if (!isset($categories)) {
        error(get_string("nocategories"));
    }
    else {
        foreach($categories as $category) {
            if ($category->name == UNCATEGORISED) {
                $category->name = get_string(UNCATEGORISED, 'grades'); 
            }
            if ($category->id == $id_selected) {
                echo  '<option value="'.$category->id.'" selected="selected">'. format_string($category->name) .'</option>';
            }
            else {
                echo  '<option value="'.$category->id.'">'. format_string($category->name) .'</option>';
            }
        }
    }
}

function grade_display_grade_preferences($course, $preferences) {
    global $CFG;
    global $USER;

    if ($preferences->use_advanced == 0) {
        $useadvanced = 1;
        $buttonlabel = get_string('useadvanced', 'grades');
    } else {
        $useadvanced = 0;
        $buttonlabel = get_String('hideadvanced', 'grades');
    }

    $buttonoptions = array('action'  => 'set_grade_preferences',
                           'id'      => $course->id,
                           'sesskey' => sesskey(),
                           'use_advanced' => $useadvanced);
                           

    print_heading_with_help(get_string('setpreferences','grades'), 'preferences', 'grade');
    
    echo '<center>';
    print_single_button('index.php', $buttonoptions, $buttonlabel, 'post');
    echo '<br /></center>';

    echo  '<form id="set_grade_preferences" method="post" action="./index.php">';
    echo  '<input type="hidden" name="action" value="set_grade_preferences" />';
    echo  '<input type="hidden" name="id" value="'.$course->id.'" />';
    echo  '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo  '<table border="0" cellspacing="2" cellpadding="5" align="center" class="gradeprefs generalbox">';
    
    $optionsyesno = NULL;
    $optionsyesno[0] = get_string('no');
    $optionsyesno[1] = get_string('yes');

    
    if ($preferences->use_advanced) {
        $options = NULL;
        $options[0] = get_string('no');
        $options[1] = get_string('toonly', 'moodle', $course->teachers);
        $options[2] = get_string('toeveryone', 'moodle');

        // display grade weights
        echo  '<tr><td class="c0">'.get_string('displayweighted','grades').':</td>';
        echo  '<td class="c1">';
        choose_from_menu($options, 'display_weighted', $preferences->display_weighted, '');
        echo  '</td></tr>';
        
        // display points    
        echo  '<tr><td class="c0">'.get_string('displaypoints','grades').':</td>';
        echo  '<td class="c1">';
        choose_from_menu($options, 'display_points', $preferences->display_points, '');
        echo  '</td></tr>';

        // display percent
        echo  '<tr><td class="c0">'.get_string('displaypercent','grades').':</td>';
        echo  '<td class="c1">';
        choose_from_menu($options, 'display_percent', $preferences->display_percent, '');
        echo  '</td></tr>';
        
        // display letter grade
        echo  '<tr><td class="c0">'.get_string('displaylettergrade','grades').':</td>';
        echo  '<td class="c1">';
        choose_from_menu($options, 'display_letters', $preferences->display_letters, '');
        echo  '</td></tr>';

        // letter grade uses weighted percent
        $options = NULL;
        $options[0] = get_string('usepercent','grades');
        $options[1] = get_string('useweighted','grades');

        echo  '<tr><td class="c0">'.get_string('lettergrade','grades').':</td>';
        echo  '<td class="c1">';
        choose_from_menu($options, 'use_weighted_for_letter', $preferences->use_weighted_for_letter, '');
        echo  '</td></tr>';
    }

    $headerlist[0] = get_string('none');
    for ($i=1; $i<=100; $i++) {
        $headerlist[$i] = $i;
    }
    
    // reprint headers every n lines default n=0
    echo '<tr><td class="c0">'.get_string('reprintheaders','grades').':</td>';
    echo '<td class="c1">';
    choose_from_menu($headerlist, 'reprint_headers', $preferences->reprint_headers, '');
    echo '</td></tr>';
    
    // show hidden grade items to teacher
    echo  '<tr><td class="c0">'.get_string('showhiddenitems','grades').'</td>';
    echo  '<td class="c1">';
    choose_from_menu($optionsyesno, 'show_hidden', $preferences->show_hidden, '');
    echo  '</td></tr>';
    
    echo  '<tr><td colspan="3" align="center"><input type="submit" value="'.get_string('savepreferences','grades').'" /></td></tr></table></form>';
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

    echo '<form id="grade_letter"><input type="hidden" name="id" value="'.$course->id.'" />';
    echo '<table border="0" cellspacing="2" cellpadding="5" align="center" class="generalbox"><tr>';
    echo '<th colspan="3" class="header" scope="col">'.get_string('setgradeletters','grades');
    helpbutton('letter', get_string('gradeletterhelp','grades'), 'grade');
    echo '</th></tr>';
    echo '<tr><td align="center" class="generaltableheader">'.get_string('gradeletter','grades').'</td>';
    echo '<td align="center" class="generaltableheader">'.get_string('lowgradeletter','grades').'</td>';
    echo '<td align="center" class="generaltableheader">'.get_string('highgradeletter','grades').'</td></tr>';
    echo '<input type="hidden" name="action" value="set_letter_grades" />';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    $i=0;
    foreach ($letters as $id=>$items) {
        if ($id !='' && !$using_defaults) {
            // send the record id so if the user deletes the values we can delete the row.
            echo '<input type="hidden" name="id'.$i.'" value="'.$id.'" />';
        }
        echo '<tr><td align="center" class="generalboxcontent"><input size="8" type="text" name="letter'.$i.'" value="'.$items->letter.'" /></td>'."\n";
        echo '<td align="center" class="generalboxcontent"><input size="8" type="text" name="grade_low'.$i.'" value="'.$items->grade_low.'" /></td>'."\n";
        echo '<td align="center" class="generalboxcontent"><input size="8" type="text" name="grade_high'.$i.'" value="'.$items->grade_high.'" /></td></tr>'."\n";
        $i++;
    }
    echo '<tr><td align="center" class="generalboxcontent"><input size="8" type="text" name="letter'.$i.'" value="" /></td><td align="center" class="generalboxcontent"><input size="8" type="text" name="grade_low'.$i.'" value="" /></td><td align="center" class="generalboxcontent"><input type="text" size="8" name="grade_high'.$i.'" value="" /></td></tr>';
    echo '<tr><td colspan="3" align="center" class="generalboxcontent"><input type="submit" value="'.get_string('savechanges','grades').'" /></td></tr>';
    echo '<input type="hidden" name="totalitems" value="'.$i.'" />';
    echo '<tr><td colspan="3" align="center" class="generalboxcontent">'.get_string('gradeletternote','grades').'</td></tr></table>';
    echo '</form>';
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
            // grade_low && grade_high don't need cleaning as they are possibly floats (no appropriate clean method) so we check is_numeric
            $updateletters[$letterid]->grade_low = $_REQUEST["grade_low$i"];
            $updateletters[$letterid]->grade_high = $_REQUEST["grade_high$i"];
            $updateletters[$letterid]->id = $letterid;
        }
        else {
            // its a new item
            $newletter->letter = clean_param($_REQUEST["letter$i"], PARAM_CLEAN);
            $newletter->grade_low = $_REQUEST["grade_low$i"];
            $newletter->grade_high = $_REQUEST["grade_high$i"];
            $newletter->courseid = $course->id;
            if (is_numeric($newletter->grade_high) && is_numeric($newletter->grade_low)) {
                insert_record('grade_letter', $newletter);
            }
            else {
                if ($i < $totalitems) {
                    if ($newletter->grade_high != '' or $newletter->grade_low != '') {
                        echo '<center>'.get_string('lettergradenonnumber','grades').' '.$newletter->letter.' item number: '.$i.'<br /></center>';
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
                    echo '<center><font color="red">'.get_string('errorgradevaluenonnumeric','grades').$letter.'</font></center>';
                }
            }
        }
    }
}

function grade_download_form($type='both') {
    global $course,$USER, $action, $cview;
    if ($type != 'both' and $type != 'ods' and $type != 'excel' and $type != 'text') {
        $type = 'both';
    }
    
    if (has_capability('moodle/course:viewcoursegrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
        echo '<table align="center"><tr>';
        $options['id'] = $course->id;
        $options['sesskey'] = $USER->sesskey;
        
        if ($type == 'both' || $type == 'ods') {
            $options['action'] = 'ods';
            echo '<td align="center">';
            print_single_button("index.php", $options, get_string("downloadods"));
            echo '</td>';
        }
        if ($type == 'both' || $type == 'excel') {
            $options['action'] = 'excel';
            echo '<td align="center">';
            print_single_button("index.php", $options, get_string("downloadexcel"));
            echo '</td>';
        }
        if ($type == 'both' || $type == 'text') {
            $options['action'] = 'text';
            echo '<td align="center">';
            print_single_button("index.php", $options, get_string("downloadtext"));
            echo '</td>';
        }
        echo '<td>';
        
        $url = 'index.php?id='.$course->id;
        if (!empty($action)) {
            $url .= '&amp;action='.$action;
            if ($action == 'vcats') {
               $url .= '&amp;cview='.$cview;
            }
        }
        echo '</td>';

        echo '</tr></table>';
    }
}





/** 
 * Simply prints all grade of one student from all modules from a given course
 * used in the grade book for student view, and grade button under user profile
 * @param int $userid;
 * @param int $courseid;
 */
function print_student_grade($user, $course) {
    
    global $CFG;
  
    if (!empty($user)) {
        $grades[$user->id] = array();    // Collect all grades in this array
        $gradeshtml[$user->id] = array(); // Collect all grades html formatted in this array
        $totals[$user->id] = array();    // Collect all totals in this array
    }
  
    $strmax = get_string("maximumshort");
  /// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

/// Search through all the modules, pulling out grade data
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    
    // prints table

    // flag for detecting whether to print table header or not
    $nograde = 0;
    
    for ($i=0; $i<=$course->numsections; $i++) {
        if (isset($sections[$i])) {   // should always be true
            $section = $sections[$i];
            if ($section->sequence) {
                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                 
                    $mod = $mods[$sectionmod];
                    if (empty($mod->modname)) {
                        continue;  // Just in case, see MDL-7150
                    }
                    $instance = get_record($mod->modname, 'id', $mod->instance);
                    $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";    
                    
                    if (file_exists($libfile)) {
                        require_once($libfile);
                        $gradefunction = $mod->modname.'_grades';
                        if (function_exists($gradefunction)) {   // Skip modules without grade function
                            if ($modgrades = $gradefunction($mod->instance)) {
                                if (!empty($modgrades->maxgrade)) {
                                    if ($mod->visible) {
                                        $maxgrade = $modgrades->maxgrade;
                                    } else {
                                        $maxgrade = $modgrades->maxgrade;
                                    }
                                } else {
                                    $maxgrade = '';
                                }
                                
                                if ($maxgrade) { 
                                    if (!$nograde) {
                                         echo ('<table align="center" class="grades"><tr><th scope="col">'.get_string('activity').'</th><th scope="col">'.get_string('yourgrade','grades').'</th><th scope="col">'.get_string('maxgrade','grades').'</th></tr>');
                                    }
                                    $nograde++;                               
                                  
                                    $link_id = grade_get_module_link($course->id, $mod->instance, $mod->module);
                                    $link = $CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$link_id->id;

                                    echo '<tr>';
                                    if (!empty($modgrades->grades[$user->id])) {
                                        $currentgrade = $modgrades->grades[$user->id];
                                        echo "<td><a href='$link'>$mod->modfullname: ".format_string($instance->name,true)."</a></td><td>$currentgrade</td><td>$maxgrade</td>";            
                                    } else {
                                        echo "<td><a href='$link'>$mod->modfullname: ".format_string($instance->name,true)."</a></td><td>".get_string('nograde')."</td><td>$maxgrade</td>";                                        
                                    }
                                    echo '</tr>';                       
                                }
                            }
                        }
                    }
                }
            }
        }
    } // a new Moodle nesting record? ;-)
    
    if ($nograde) {
        echo '</table>';
    }
}

function grade_get_course_students($courseid) {
    global $CFG;
    // The list of roles to display is stored in CFG->gradebookroles
    if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
        return false;  
    } 
        
    $configvar = get_config('', 'gradebookroles');
    if (empty($configvar)) {
        notify ('no roles defined in admin->appearance->graderoles');
        return false; // no roles to displayreturn false;  
    }
         
    if ($rolestoget = explode(',', $configvar)) {
        foreach ($rolestoget as $crole) {
            if ($tempstudents = get_role_users($crole, $context, true)) {
                foreach ($tempstudents as $tempuserid=>$tempstudent) {
                    $students[$tempuserid] = $tempstudent;  
                }            
            }
        }
    } else {
        notify ('no roles defined in admin->appearance->graderoles');
        return false; // no roles to displayreturn false;  
    }
    return isset($students)?$students:'';
}
?>
