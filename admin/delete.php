<?php

// Deletes the moodledata directory, COMPLETELY!!
// BE VERY CAREFUL USING THIS!

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('purgemoodledata');

    require_login();

    $sure       = optional_param('sure', 0, PARAM_BOOL);
    $reallysure = optional_param('reallysure', 0, PARAM_BOOL);

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $deletedir = $CFG->dataroot;   // The directory to delete!

    echo $OUTPUT->header();
    echo $OUTPUT->heading('Purge moodledata');

    if (empty($sure)) {
        $optionsyes = array('sure'=>'yes', 'sesskey'=>sesskey());

        $formcontinue = new single_button(new moodle_url('delete.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button('index.php', get_string('no'), 'get');
        echo $OUTPUT->confirm('Are you completely sure you want to delete everything inside the directory '. $deletedir .' ?', $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        exit;
    }

    if (!data_submitted() or empty($reallysure)) {
        $optionsyes = array('sure'=>'yes', 'sesskey'=>sesskey(), 'reallysure'=>'yes');
        $formcontinue = new single_button(new moodle_url('delete.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button('index.php', get_string('no'), 'get');
        echo $OUTPUT->confirm('Are you REALLY REALLY completely sure you want to delete everything inside the directory '.
                $deletedir .' (this includes all user images, and any other course files that have been created) ?',
                $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        exit;
    }

    if (!confirm_sesskey()) {
        print_error('wrongcall', 'error');
    }

    /// OK, here goes ...

    delete_subdirectories($deletedir);

    echo '<h1 align="center">Done!</h1>';
    echo $OUTPUT->continue_button($CFG->wwwroot);
    echo $OUTPUT->footer();
    exit;


function delete_subdirectories($rootdir) {

    $dir = opendir($rootdir);

    while (false !== ($file = readdir($dir))) {
        if ($file != '.' and $file != '..') {
            $fullfile = $rootdir .'/'. $file;
            if (filetype($fullfile) == 'dir') {
                delete_subdirectories($fullfile);
                echo 'Deleting '. $fullfile .' ... ';
                if (rmdir($fullfile)) {
                    echo 'Done.<br />';
                } else {
                    echo 'FAILED.<br />';
                }
            } else {
                echo 'Deleting '. $fullfile .' ... ';
                if (unlink($fullfile)) {
                    echo 'Done.<br />';
                } else {
                    echo 'FAILED.<br />';
                }
            }
        }
    }
    closedir($dir);
}


