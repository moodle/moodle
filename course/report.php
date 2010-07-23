<?php
      // Display all the interfaces for importing data into a specific course

    require_once('../config.php');

    $id = required_param('id', PARAM_INT);   // course id to import TO
    $course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

    $PAGE->set_pagelayout('standard');
    require_login($course);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/site:viewreports', $context); // basic capability for listing of reports

    $strreports = get_string('reports');

    $PAGE->set_url(new moodle_url('/course/report.php', array('id'=>$id)));
    $PAGE->set_title($course->fullname.': '.$strreports);
    $PAGE->set_heading($course->fullname.': '.$strreports);
    echo $OUTPUT->header();

    $reports = get_plugin_list('coursereport');

    foreach ($reports as $report => $reportdirectory) {
        $pluginfile = $reportdirectory.'/mod.php';
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

    echo $OUTPUT->footer();

