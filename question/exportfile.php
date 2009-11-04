<?php
    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->libdir . '/filelib.php');

    // Note: file.php always calls require_login() with $setwantsurltome=false
    //       in order to avoid messing redirects. MDL-14495
    require_login(0, true, null, false);

    $relativepath = get_file_argument();
    if (!$relativepath) {
        print_error('invalidarg', 'question');
    }

    $pathname = $CFG->dataroot . '/temp/questionexport/' . $USER->id . '/' .  $relativepath;

    send_temp_file($pathname, $relativepath);

