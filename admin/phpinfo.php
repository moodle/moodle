<?php
       // phpinfo.php - shows phpinfo for the current server

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('phpinfo');

    echo $OUTPUT->header();

    echo '<div class="phpinfo text-ltr">';

    ob_start();
    phpinfo(INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES + INFO_VARIABLES);
    $html = ob_get_contents();
    ob_end_clean();

/// Delete styles from output
    $html = preg_replace('#(\n?<style[^>]*?>.*?</style[^>]*?>)|(\n?<style[^>]*?/>)#is', '', $html);
    $html = preg_replace('#(\n?<head[^>]*?>.*?</head[^>]*?>)|(\n?<head[^>]*?/>)#is', '', $html);
/// Delete DOCTYPE from output
    $html = preg_replace('/<!DOCTYPE html PUBLIC.*?>/is', '', $html);
/// Delete body and html tags
    $html = preg_replace('/<html.*?>.*?<body.*?>/is', '', $html);
    $html = preg_replace('/<\/body><\/html>/is', '', $html);

    echo $html;

    echo '</div>';

    echo $OUTPUT->footer();


