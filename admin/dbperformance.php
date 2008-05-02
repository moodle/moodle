<?PHP  // $Id$
       // dbperformance.php - shows latest ADOdb stats for the current server

    require_once('../config.php');

    // disable moodle specific debug messages that would be breaking the frames
    disable_debugging();

    $topframe    = optional_param('topframe', 0, PARAM_BOOL);
    $bottomframe = optional_param('bottomframe', 0, PARAM_BOOL);
    $do          = optional_param('do', '', PARAM_ALPHA);

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $strdatabaseperformance = get_string("databaseperformance");
    $stradministration = get_string("administration");
    $site = get_site();

    $navigation = build_navigation(array(
        array('name'=>$stradministration, 'link'=>'index.php', 'type'=>'misc'),
        array('name'=>$strdatabaseperformance, 'link'=>null, 'type'=>'misc')));
    if (!empty($topframe)) {
        print_header("$site->shortname: $strdatabaseperformance", "$site->fullname", $navigation);
        exit;
    }

    if (!empty($bottomframe) or !empty($do)) {
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
