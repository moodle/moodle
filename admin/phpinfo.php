<?PHP  // $Id$
       // phpinfo.php - shows phpinfo for the current server

    require_once("../config.php");

    require_login();

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    $stradministration = get_string("administration");
    $site = get_site();

	print_header("$site->shortname: phpinfo", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> phpinfo");

    phpinfo();

    print_footer();

?>
