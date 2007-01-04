<?PHP // $Id$

    // Allows the admin to configure mnet stuff

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    require_login();
    $adminroot = admin_get_root();
    admin_externalpage_setup('net', $adminroot);

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    if (!function_exists('curl_init') ) {
        error('PHP Curl library is not installed');
    }

    $keypair = unserialize($CFG->openssl);

    if (!isset($CFG->mnet_dispatcher_mode)) set_config('mnet_dispatcher_mode', 'off');

/// If data submitted, process and store
    if (($form = data_submitted()) && confirm_sesskey()) {
        if (in_array($form->mode, array("off", "strict", "promiscuous"))) {
            if (set_config('mnet_dispatcher_mode', $form->mode)) {
                redirect('index.php', get_string('changessaved'));
            } else {
                error('Invalid action parameter.', 'index.php');
            }
        }
    }
    $hosts = get_records_select('mnet_host', " id != '{$CFG->mnet_localhost_id}' AND deleted = '0' ",'wwwroot ASC' );
    include('./index.html');
?>
