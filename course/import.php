<?php // $Id$
      // Display all the interfaces for importing data into a specific course

    require_once('../config.php');

    $id = required_param('id', PARAM_INT);   // course id to import TO

    if (!$course = get_record('course', 'id', $id)) {
        error("That's an invalid course id");
    }

    $strimport = get_string('import');

    print_header($course->fullname.': '.$strimport, $course->fullname.': '.$strimport, 
                 '<a href="view.php?id='.$course->id.'">'.$course->shortname.'</a> -> '.$strimport);

    $directories = get_list_of_plugins('course/import');

    foreach ($directories as $directory) {
        echo '<div class="plugin">';
        include_once($CFG->dirroot.'/course/import/'.$directory.'/mod.html');
        echo '</div>';
    }

    print_footer();
?>
