<?PHP // $Id$

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    include_once($CFG->dirroot.'/mnet/lib.php');
    $stradministration = get_string('administration');
    $strconfiguration  = get_string('configuration');
    $strmnetsettings   = get_string('mnetsettings', 'mnet');
    $strmnetservices   = get_string('mnetservices', 'mnet');
    $strmnetlog        = get_string('mnetlog', 'mnet');
    $strmnetedithost   = get_string('reviewhostdetails', 'mnet');
    require_login();

    $context = get_context_instance(CONTEXT_SYSTEM);
    
    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    if (!$site = get_site()) {
        print_error('nosite', '', '', NULL, true);
    }

/// Initialize variables.

    // Step must be one of:
    // input   Parse the details of a new host and fetch its public key
    // commit  Save our changes (to a new OR existing host)
    $step   = optional_param('step', 'verify', PARAM_ALPHA);
    $hostid = required_param('hostid', PARAM_INT);
    $warn = array();

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        redirect('index.php', get_string('postrequired','mnet') ,7);
    }

    require_sesskey();

    if ('verify' == $step) {
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_id($hostid);
        $live_users = $mnet_peer->count_live_sessions();
        if ($live_users > 0) {
            $warn[] = get_string('usersareonline', 'mnet', $live_users);
        }
        include('delete.html');
    } elseif ('delete' == $step) {
        $mnet_peer = new mnet_peer();
        $mnet_peer->set_id($hostid);
        $mnet_peer->delete();
        redirect('peers.php', get_string('hostdeleted', 'mnet'), 5);
    }
?>
