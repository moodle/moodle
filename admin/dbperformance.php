<?PHP  // $Id$
       // dbperformance.php - shows latest ADOdb stats for the current server

    require_once("../config.php");

    require_login();

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    $strdatabaseperformance = get_string("databaseperformance");
    $stradministration = get_string("administration");
    $site = get_site();

    if (isset($topframe)) {
	    print_header("$site->shortname: $strdatabaseperformance", "$site->fullname", 
                     "<a target=\"$CFG->framename\" href=\"index.php\">$stradministration</a> -> Database performance");
        exit;
    }

    if (isset($bottomframe) or isset($_GET['do'])) {
        $perf =&NewPerfMonitor($db);
        $perf->UI($pollsecs=5);
        exit;
    }

?>
<head>
<title><?php echo "$site->shortname: $strdatabaseperformance" ?></title>
</head>

<frameset rows="80,*">
   <frame src="dbperformance.php?topframe=true">
   <frame src="dbperformance.php?bottomframe=true">
</frameset>
