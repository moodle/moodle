<?PHP  // $Id$
       // phpinfo.php - shows phpinfo for the current server

    require_once("../config.php");

    require_login();

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    if (isset($topframe)) {
        $stradministration = get_string("administration");
        $site = get_site();
    
	    print_header("$site->shortname: phpinfo", "$site->fullname", 
                     "<a target=\"$CFG->framename\" href=\"index.php\">$stradministration</a> -> PHP info");
        exit;
    }

    if (isset($bottomframe)) {
        phpinfo();
        exit;
    }

?>
<head>
<title>PHP info</title>
</head>

<frameset rows="80,*">
   <frame src="phpinfo.php?topframe=true">
   <frame src="phpinfo.php?bottomframe=true">
</frameset>
