<?php // $Id$
      // Display all the interfaces for importing data into a specific course

    require_once('../config.php');

    $id = required_param('id', PARAM_INT);   // course id to import TO

    if (!$course = get_record('course', 'id', $id)) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    require_capability('moodle/site:viewreports', get_context_instance(CONTEXT_COURSE, $course->id));

    $strreports = get_string('reports');

    print_header($course->fullname.': '.$strreports, $course->fullname.': '.$strreports, 
                 '<a href="view.php?id='.$course->id.'">'.$course->shortname.'</a> -> '.$strreports);

    $directories = get_list_of_plugins('course/report');

    foreach ($directories as $directory) {
        $pluginfile = $CFG->dirroot.'/course/report/'.$directory.'/mod.php';
        if (file_exists($pluginfile)) {
            echo '<div class="plugin">';
            //echo $pluginfile;
            include_once($pluginfile);  // Fragment for listing
            echo '</div>';
        }
    }
    
    print_footer();
?>
