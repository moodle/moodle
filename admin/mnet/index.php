<?PHP // $Id$

    // Allows the admin to configure mnet stuff

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    require_login();
    admin_externalpage_setup('net');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");


    if (!extension_loaded('openssl')) {
        admin_externalpage_print_header();
        set_config('mnet_dispatcher_mode', 'off');
        print_error('requiresopenssl', 'mnet', '', NULL, true);
    }

    if (!$site = get_site()) {
        admin_externalpage_print_header();
        set_config('mnet_dispatcher_mode', 'off');
        print_error('nosite', '', '', NULL, true);
    }

    if (!function_exists('curl_init') ) {
        admin_externalpage_print_header();
        set_config('mnet_dispatcher_mode', 'off');
        print_error('nocurl', 'mnet', '', NULL, true);
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

    admin_externalpage_print_header();
?>
<center>
<form method="post" action="index.php">
    <table align="center" width="635" class="generalbox" border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td  class="generalboxcontent">
            <table cellpadding="9" cellspacing="0" >
                <tr valign="top">
                    <td colspan="2" class="header" cellpadding="0"><?php print_string('aboutyourhost', 'mnet'); ?></td>
                </tr>
                <tr valign="top">
                    <td align="right"><?php print_string('publickey', 'mnet'); ?>:</td>
                    <td><pre><?php echo $MNET->public_key; ?></pre></td>
                </tr>
                <tr valign="top">
                    <td align="right"><?php print_string('expires', 'mnet'); ?>:</td>
                    <td><?php echo userdate($MNET->public_key_expires); ?></td>
                </tr>
                <tr valign="top">
                    <td align="right"><?php print_string('net', 'mnet'); ?>:</td>
                    <td><input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />
                        <input type="radio" name="mode" value="off" <?php echo ("off" == $CFG->mnet_dispatcher_mode)? 'checked="checked"' : '' ?> /> <?php print_string('off', 'mnet'); ?> <br />
                        <input type="radio" name="mode" value="strict" <?php echo ("strict" == $CFG->mnet_dispatcher_mode)? 'checked="checked"' : '' ?> /> <?php print_string('on', 'mnet'); ?><br />
                        <input type="submit" name="submit" value="<?php print_string('savechanges'); ?>" />
                    </td>
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
                    <td colspan="2" class="header" cellpadding="0"><?php print_string('expireyourkey', 'mnet'); ?></td>
                </tr>
                <tr valign="top">
                    <td colspan="2" cellpadding="0"><?php print_string('expireyourkeyexplain', 'mnet'); ?></td>
                </tr>
                <tr valign="top">
                    <td align="left" width="10" nowrap="nowrap"><?php print_string('expireyourkey', 'mnet'); ?></td>
                    <td align="left"><input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />
                        <input type="hidden" name="deleteKey" value="" />
                        <input type="submit" name="submit" value="<?php print_string('delete'); ?>" />
                    </td>
                </tr>
            </table>
            </td>
        </tr>
    </table>
</form>
</center>

<?php
admin_externalpage_print_footer();
?>
