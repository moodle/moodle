<?PHP  // $Id$
       // phpinfo.php - shows phpinfo for the current server

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('phpinfo', $adminroot);

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID));

    admin_externalpage_print_header($adminroot);

    echo '<div class="phpinfo">';

    ob_start();
    phpinfo(INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES);
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

    admin_externalpage_print_footer($adminroot);

?>
