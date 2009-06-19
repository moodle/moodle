<?php // $Id$
      // Display all the interfaces for importing data into a specific course

    require_once('../config.php');

    $id = required_param('id', PARAM_INT);   // course id to import TO

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error("That's an invalid course id");
    }

    require_login($course->id);

    require_capability('moodle/site:import', get_context_instance(CONTEXT_COURSE, $id));

/// Always we begin an import, we delete all backup/restore/import session structures
    if (isset($SESSION->course_header)) {
        unset ($SESSION->course_header);
    }
    if (isset($SESSION->info)) {
        unset ($SESSION->info);
    }
    if (isset($SESSION->backupprefs)) {
        unset ($SESSION->backupprefs);
    }
    if (isset($SESSION->restore)) {
        unset ($SESSION->restore);
    }
    if (isset($SESSION->import_preferences)) {
        unset ($SESSION->import_preferences);
    }

    $strimport = get_string('import');
    $navlinks = array();
    $navlinks[] = array('name' => $strimport, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header($course->fullname.': '.$strimport, $course->fullname.': '.$strimport, $navigation);

    $imports = get_plugin_list('import');

    foreach ($imports as $import => $importdir) {
        echo '<div class="plugin">';
        include($importdir.'/mod.php');
        echo '</div>';
    }

    print_footer();
?>
