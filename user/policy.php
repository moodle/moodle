<?php // $Id$

    require_once("../config.php");

    $agree = optional_param('agree', 0, PARAM_INT);

    define('MESSAGE_WINDOW', true);  // This prevents the message window coming up


    if (!isset($USER->id)) {
        require_login();
    }

    if ($agree == 1 and confirm_sesskey()) {        // User has agreed
        if ($USER->username != 'guest') {           // Don't remember guests
            if (!set_field('user', 'policyagreed', 1, 'id', $USER->id)) {
                error('Could not save your agreement');
            }
        }
        $USER->policyagreed = 1;

        if (!empty($SESSION->wantsurl)) {
            $wantsurl = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
            redirect($wantsurl);
        } else {
            redirect($CFG->wwwroot.'/');
        }
        exit;
    }

    $strpolicyagree = get_string('policyagree');
    $strpolicyagreement = get_string('policyagreement');
    $strpolicyagreementclick = get_string('policyagreementclick');

    print_header($strpolicyagreement, $SITE->fullname, $strpolicyagreement);

    print_heading($strpolicyagreement);

    echo '<center>';
    echo '<iframe align="center" width="90%" height="70%" src="'.$CFG->sitepolicy.'" />';
    echo link_to_popup_window ($CFG->sitepolicy, 'agreement', $strpolicyagreementclick,
                               500, 500, 'Popup window', 'none', true);
    echo '</iframe>';
    echo '</center>';

    notice_yesno($strpolicyagree, "policy.php?agree=1&amp;sesskey=$USER->sesskey", $CFG->wwwroot);

    print_footer();

?>
