<?PHP  // $Id$

    require("../config.php");

    if (! $site = get_site()) {
        error("Site is misconfigured");
    }
    $strdocumentation = get_string("documentation");
    print_header("$site->shortname: $strdocumentation", "$site->fullname", "$strdocumentation");
    
?>

