<?PHP

    // Allows the admin to configure mnet stuff

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    require_login();
    admin_externalpage_setup('net');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    $site = get_site();
    $mnet = get_mnet_environment();

    if (!extension_loaded('openssl')) {
        echo $OUTPUT->header();
        set_config('mnet_dispatcher_mode', 'off');
        print_error('requiresopenssl', 'mnet');
    }

    if (!function_exists('curl_init') ) {
        echo $OUTPUT->header();
        set_config('mnet_dispatcher_mode', 'off');
        print_error('nocurl', 'mnet');
    }

    if (!isset($CFG->mnet_dispatcher_mode)) {
        set_config('mnet_dispatcher_mode', 'off');
    }

/// If data submitted, process and store
    if (($form = data_submitted()) && confirm_sesskey()) {
        if (!empty($form->submit) && $form->submit == get_string('savechanges')) {
            if (in_array($form->mode, array("off", "strict", "dangerous"))) {
                if (set_config('mnet_dispatcher_mode', $form->mode)) {
                    redirect('index.php', get_string('changessaved'));
                } else {
                    print_error('invalidaction', '', 'index.php');
                }
            }
        } elseif (!empty($form->submit) && $form->submit == get_string('delete')) {
            $mnet->get_private_key();
            $SESSION->mnet_confirm_delete_key = md5(sha1($mnet->keypair['keypair_PEM'])).':'.time();

            $formcontinue = new single_button(new moodle_url('index.php', array('confirm' => md5($mnet->public_key))), get_string('yes'));
            $formcancel = new single_button(new moodle_url('index.php', array()), get_string('no'));
            echo $OUTPUT->confirm(get_string("deletekeycheck", "mnet"), $formcontinue, $formcancel);
            exit;
        } else {
            // We're deleting


            if (!isset($SESSION->mnet_confirm_delete_key)) {
                // fail - you're being attacked?
            }

            $key = '';
            $time = '';
            @list($key, $time) = explode(':',$SESSION->mnet_confirm_delete_key);
            $mnet->get_private_key();

            if($time < time() - 60) {
                // fail - you're out of time.
                print_error ('deleteoutoftime', 'mnet', 'index.php');
                exit;
            }

            if ($key != md5(sha1($mnet->keypair['keypair_PEM']))) {
                // fail - you're being attacked?
                print_error ('deletewrongkeyvalue', 'mnet', 'index.php');
                exit;
            }

            $mnet->replace_keys();
            redirect('index.php', get_string('keydeleted','mnet'));
            exit;
        }
    }
    $hosts = $DB->get_records_select('mnet_host', "id <> ? AND deleted = 0", array($CFG->mnet_localhost_id), 'wwwroot ASC');

    echo $OUTPUT->header();
?>
<form method="post" action="index.php">
    <table align="center" width="635" class="generalbox" border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td  class="generalboxcontent">
            <table cellpadding="9" cellspacing="0" >
                <tr valign="top">
                    <td colspan="2" class="header"><?php print_string('aboutyourhost', 'mnet'); ?></td>
                </tr>
                <tr valign="top">
                    <td align="right"><?php print_string('publickey', 'mnet'); ?>:</td>
                    <td><pre><?php echo $mnet->public_key; ?></pre></td>
                </tr>
                <tr valign="top">
                    <td align="right"><?php print_string('expires', 'mnet'); ?>:</td>
                    <td><?php echo userdate($mnet->public_key_expires); ?></td>
                </tr>
            </table>
            </td>
        </tr>
    </table>
</form>
<form method="post" action="index.php">
    <table align="center" width="635" class="generalbox" border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td  class="generalboxcontent">
            <table cellpadding="9" cellspacing="0" >
                <tr valign="top">
                    <td colspan="2" class="header"><?php print_string('expireyourkey', 'mnet'); ?></td>
                </tr>
                <tr valign="top">
                    <td colspan="2"><?php print_string('expireyourkeyexplain', 'mnet'); ?></td>
                </tr>
                <tr valign="top">
                    <td align="left" width="10" nowrap="nowrap"><?php print_string('expireyourkey', 'mnet'); ?></td>
                    <td align="left"><input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
                        <input type="hidden" name="deleteKey" value="" />
                        <input type="submit" name="submit" value="<?php print_string('delete'); ?>" />
                    </td>
                </tr>
            </table>
            </td>
        </tr>
    </table>
</form>

<?php
echo $OUTPUT->footer();
