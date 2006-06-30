<?php // $Id$
      // Display all the reports available in admin/report

    require_once('../config.php');

    if (!isadmin()) {
        error("You are not allowed to look at this page");
    }

    $stradmin = get_string('administration');
    $strreports = get_string('reports');
    $strmisc = get_string('miscellaneous');

    print_header($strreports, $strreports,
                 '<a href="index.php">'.$stradmin.'</a> -> <a href="misc.php">'.$strmisc.'</a> ->'.
                 $strreports);

    $directories = get_list_of_plugins('admin/report');

    foreach ($directories as $directory) {
        echo '<div class="plugin '.$directory.'">';
        include_once($CFG->dirroot.'/admin/report/'.$directory.'/mod.php');  // Fragment for listing
        echo '</div>';
    }

    print_footer();
?>

