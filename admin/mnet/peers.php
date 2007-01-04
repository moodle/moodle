<?PHP // $Id$

// Allows the admin to configure other Moodle hosts info

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
include_once($CFG->dirroot.'/mnet/lib.php');

require_login();
$adminroot = admin_get_root();
admin_externalpage_setup('mnetpeers', $adminroot);

$context = get_context_instance(CONTEXT_SYSTEM, SITEID);

require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

if (!$site = get_site()) {
    error('Site isn\'t defined!');
}

if (!function_exists('curl_init') ) {
    error('PHP Curl library is not installed');
}

/// Initialize variables.

// Step must be one of:
// input   Parse the details of a new host and fetch its public key
// commit  Save our changes (to a new OR existing host)
$step   = optional_param('step', NULL, PARAM_ALPHA);
$hostid = optional_param('hostid', NULL, PARAM_INT);

// Fetch some strings for the HTML templates
$strmnetservices   = get_string('mnetservices', 'mnet');
$strmnetlog        = get_string('mnetlog', 'mnet');
$strmnetedithost   = get_string('reviewhostdetails', 'mnet');

if (!isset($CFG->mnet_dispatcher_mode)) set_config('mnet_dispatcher_mode', 'off');

/// If data submitted, process and store
if (($form = data_submitted()) && confirm_sesskey()) {

    if (!empty($form->updateregisterall)) {
        if (!empty($form->registerallhosts)) {
            set_config('mnet_register_allhosts',1);
        } else {
            set_config('mnet_register_allhosts',0);
        }
        redirect('peers.php', get_string('changessaved'));
    } else {

        $mnet_peer = new mnet_peer();
    
        if (!empty($form->id)) {
            $form->id = clean_param($form->id, PARAM_INT);
            $mnet_peer->set_id($form->id);
        } else {
            // PARAM_URL requires a genuine TLD (I think) This breaks my testing
            $temp_wwwroot = $form->wwwroot; //clean_param($form->wwwroot, PARAM_URL);
            if ($temp_wwwroot !== $form->wwwroot) {
                trigger_error("We now parse the wwwroot with PARAM_URL");
                error('Invalid URL parameter.', 'peers.php');
            }
            unset($temp_wwwroot);
            $mnet_peer->bootstrap($form->wwwroot);
        }
    
        if (isset($form->name) && $form->name != $mnet_peer->name) {
            $form->name = clean_param($form->name, PARAM_NOTAGS);
            $mnet_peer->set_name($form->name);
        }
    
        if (isset($form->deleted) && ($form->deleted == '0' || $form->deleted == '1')) {
            $mnet_peer->deleted = $form->deleted;
        }
    
        if (isset($form->public_key)) {
            $form->public_key = clean_param($form->public_key, PARAM_PEM);
            if (empty($form->public_key)) {
                // Public key was not in a correct format
            } else {
                $oldkey = $mnet_peer->public_key;
                $mnet_peer->public_key = $form->public_key;
                $mnet_peer->public_key_expires   = $mnet_peer->check_common_name($form->public_key);
                if ($mnet_peer->public_key_expires == false) {
                    $mnet_peer->public_key == $oldkey;
                }
            }
        }
    
        // PREVENT DUPLICATE RECORDS ///////////////////////////////////////////
        if ('input' == $form->step) {
            if ( isset($mnet_peer->id) && $mnet_peer->id > 0 ) {
                error(get_string("hostexists ".$mnet_peer->id, 'mnet', $mnet_peer->id),'peers.php?step=update&hostid='.$mnet_peer->id);
            }
        }
    
        if ('input' == $form->step) {
            include('./mnet_review.html');
        } elseif ('commit' == $form->step) {
            $bool = $mnet_peer->commit();
            if ($bool) {
                redirect('peers.php?step=update&hostid='.$mnet_peer->id, get_string('changessaved'));
            } else {
                error('Invalid action parameter.', 'index.php');
            }
        }
    }
} elseif (is_int($hostid)) {
    $mnet_peer = new mnet_peer();
    $mnet_peer->set_id($hostid);
    $form = new stdClass();
    if ($hostid != $CFG->mnet_all_hosts_id) {
        include('./mnet_review.html');
    } else {
        include('./mnet_review_allhosts.html');
    }
} else {
    $hosts = get_records_select('mnet_host', " id != '{$CFG->mnet_localhost_id}' AND deleted = '0' ",'wwwroot ASC' );
    if (empty($hosts)) $hosts = array();
    include('./peers.html');
}
?>