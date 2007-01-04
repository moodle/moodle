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

    if (!isset($CFG->mnet_dispatcher_mode)) set_config('mnet_dispatcher_mode', 'off');

/// If data submitted, process and store
    if (($form = data_submitted()) && confirm_sesskey()) {
        if (!empty($form->submit) && $form->submit == get_string('savechanges')) {
            if (in_array($form->mode, array("off", "strict", "promiscuous"))) {
                if (set_config('mnet_dispatcher_mode', $form->mode)) {
                    redirect('index.php', get_string('changessaved'));
                } else {
                    error('Invalid action parameter.', 'index.php');
                }
            }
        } elseif (!empty($form->submit) && $form->submit == get_string('delete')) {
            $MNET->get_private_key();
            $_SESSION['mnet_confirm_delete_key'] = md5(sha1($MNET->keypair['keypair_PEM'])).':'.time();
            notice_yesno(get_string("deletekeycheck", "mnet"),
                                    "index.php?sesskey=$USER->sesskey&amp;confirm=".md5($MNET->public_key),
                                    "index.php",
                                     array('sesskey' => $USER->sesskey),
                                     NULL,
                                    'post',
                                    'get');
            exit;
        } else {
            // We're deleting
            
            
            if (!isset($_SESSION['mnet_confirm_delete_key'])) {
                // fail - you're being attacked?
            }

            $key = '';
            $time = '';
            @list($key, $time) = explode(':',$_SESSION['mnet_confirm_delete_key']);
            $MNET->get_private_key();

            if($time < time() - 60) {
                // fail - you're out of time.
                print_error ('deleteoutoftime', 'mnet', 'index.php');
                exit;
            }

            if ($key != md5(sha1($MNET->keypair['keypair_PEM']))) {
                // fail - you're being attacked?
                print_error ('deletewrongkeyvalue', 'mnet', 'index.php');
                exit;
            }

            $MNET->replace_keys();
            redirect('index.php', get_string('keydeleted','mnet'));
            exit;
        }
    }
    $hosts = get_records_select('mnet_host', " id != '{$CFG->mnet_localhost_id}' AND deleted = '0' ",'wwwroot ASC' );
    include('./index.html');
?>
