<?php // $Id$
      // Display all the interfaces for importing data into a specific course

    require_once('../config.php');

    $id = required_param('id', PARAM_INT);   // course id to import TO

    if (!$course = get_record('course', 'id', $id)) {
        error("That's an invalid course id");
    }

    require_login($course);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/site:viewreports', $context); // basic capability for listing of reports

    $strreports = get_string('reports');

    $navlinks = array();
    $navlinks[] = array('name' => $strreports, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($course->fullname.': '.$strreports, $course->fullname.': '.$strreports, $navigation);

    $directories = get_list_of_plugins('course/report');

    foreach ($directories as $directory) {
        $pluginfile = $CFG->dirroot.'/course/report/'.$directory.'/mod.php';
        if (file_exists($pluginfile)) {
            ob_start();
            include($pluginfile);  // Fragment for listing
            $html = ob_get_contents();
            ob_end_clean();
            // add div only if plugin accessible
            if ($html !== '') {
                echo '<div class="plugin">';
                echo $html;
                echo '</div>';
            }
        }
    }

    print_footer();
?>
