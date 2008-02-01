<?php  // $Id$
       // Provide interface for topics AJAX course formats

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir .'/pagelib.php');
require_once($CFG->libdir .'/blocklib.php');


// Initialise ALL the incoming parameters here, up front.
$courseid   = required_param('courseId', PARAM_INT);
$class      = required_param('class', PARAM_ALPHA);
$field      = optional_param('field', '', PARAM_ALPHA);
$instanceid = optional_param('instanceId', 0, PARAM_INT);
$sectionid  = optional_param('sectionId', 0, PARAM_INT);
$beforeid   = optional_param('beforeId', 0, PARAM_INT);
$value      = optional_param('value', 0, PARAM_INT);
$column     = optional_param('column', 0, PARAM_ALPHA);
$id         = optional_param('id', 0, PARAM_INT);
$summary    = optional_param('summary', '', PARAM_RAW);
$sequence   = optional_param('sequence', '', PARAM_SEQUENCE);
$visible    = optional_param('visible', 0, PARAM_INT);


// Authorise the user and verify some incoming data
if (!$course = get_record('course', 'id', $courseid)) {
    error_log('AJAX commands.php: Course does not exist');
    die;
}

$PAGE = page_create_object(PAGE_COURSE_VIEW, $course->id);
$pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);

if (!empty($instanceid)) {
    $blockinstance = blocks_find_instance($instanceid, $pageblocks);
    if (!$blockinstance || $blockinstance->pageid != $course->id
                        || $blockinstance->pagetype != 'course-view') {
        error_log('AJAX commands.php: Bad block ID '.$instanceid);
        die;
    }
}

$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_login($course->id);
require_capability('moodle/course:update', $context);



// OK, now let's process the parameters and do stuff
switch($_SERVER['REQUEST_METHOD']) {
    case 'POST':

        switch ($class) {
            case 'block':

                switch ($field) {
                    case 'visible':   
                        blocks_execute_action($PAGE, $pageblocks, 'toggle', $blockinstance);
                        break;

                    case 'position':  // Misleading case. Should probably call it 'move'.
                        // We want to move the block around. This means changing
                        // the column (position field) and/or block sort order
                        // (weight field).
                        blocks_move_block($PAGE, $blockinstance, $column, $value);
                        break;
                }
                break;

            case 'section':
 
                if (!record_exists('course_sections','course',$course->id,'section',$id)) {
                    error_log('AJAX commands.php: Bad Section ID '.$id);
                    die;
                }
 
                switch ($field) {
                    case 'visible':
                        set_section_visible($course->id, $id, $value);
                        break;

                    case 'move':
                        move_section($course, $id, $value);
                        break;
                }
                rebuild_course_cache($course->id);
                break;

            case 'resource':
                if (!$mod = get_record('course_modules', 'id', $id, 'course', $course->id)) {
                    error_log('AJAX commands.php: Bad course module ID '.$id);
                    die;
                }
                switch ($field) {
                    case 'visible':
                        set_coursemodule_visible($mod->id, $value);
                        break;

                    case 'groupmode':
                        set_coursemodule_groupmode($mod->id, $value);
                        break;

                    case 'indentleft':
                        if ($mod->indent > 0) {
                            $mod->indent--;
                            update_record('course_modules', $mod);
                        }
                        break;

                    case 'indentright':
                        $mod->indent++;
                        update_record('course_modules', $mod);
                        break;

                    case 'move':
                        if (!$section = get_record('course_sections','course',$course->id,'section',$sectionid)) {
                            error_log('AJAX commands.php: Bad section ID '.$sectionid);
                            die;
                        }
                        
                        if ($beforeid > 0){
                            $beforemod = get_record('course_modules', 'id', $beforeid);
                        } else {
                            $beforemod = NULL;
                        }

                        if (debugging('',DEBUG_DEVELOPER)) {
                            error_log(serialize($beforemod));
                        }

                        moveto_module($mod, $section, $beforemod);
                        break;
                }
                rebuild_course_cache($course->id);
                break;
        
            case 'course': 
                switch($field) {
                    case 'marker':
                        $newcourse = new object;
                        $newcourse->id = $course->id;
                        $newcourse->marker = $value;
                        if (!update_record('course',$newcourse)) {
                            error_log('AJAX commands.php: Failed to update course marker for course '.$newcourse->id);
                            die;
                        }
                        break;
                }
                break;
        }
        break;

    case 'DELETE':
        switch ($class) {
            case 'block':
                blocks_execute_action($PAGE, $pageblocks, 'delete', $blockinstance);
                break; 
                
            case 'resource':
                if (!$cm = get_record('course_modules', 'id', $id, 'course', $course->id)) {
                    error_log('AJAX rest.php: Bad course module ID '.$id);
                    die;
                }
                if (!$mod = get_record('modules', 'id', $cm->module)) {
                    error_log('AJAX rest.php: Bad module ID '.$cm->module);
                    die;
                }
                $mod->name = clean_param($mod->name, PARAM_SAFEDIR);  // For safety
                $modlib = "$CFG->dirroot/mod/$mod->name/lib.php";

                if (file_exists($modlib)) {
                    include_once($modlib);
                } else {
                    error_log("Ajax rest.php: This module is missing important code ($modlib)");
                    die;
                }
                $deleteinstancefunction = $mod->name."_delete_instance";

                // Run the module's cleanup funtion.
                if (!$deleteinstancefunction($cm->instance)) {
                    error_log("Ajax rest.php: Could not delete the $mod->name (instance)");
                    die;
                }
                // Remove the course_modules entry.
                if (!delete_course_module($cm->id)) {
                    error_log("Ajax rest.php: Could not delete the $mod->modulename (coursemodule)");
                    die;
                }

                rebuild_course_cache($course->id);

                add_to_log($courseid, "course", "delete mod",
                           "view.php?id=$courseid",
                           "$mod->name $cm->instance", $cm->id);
                break;
        }
        break;
}

?>