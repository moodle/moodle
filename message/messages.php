<?php

    require('../config.php');

    @header('Content-Type: text/html; charset=utf-8');

    $PAGE->set_generaltype('popup');
    $PAGE->set_title(get_string('messages', 'message').' - '.format_string($SITE->fullname));
    echo $OUTPUT->header();
    echo "<div id='messages'></div>";
    echo $OUTPUT->footer();
    
?>