<?php // $Id$

    require_once("../config.php");

    optional_param('agree', 0, PARAM_INT);


    if (!isset($USER->id)) {
        require_login();
    }

    if ($agree == 1 and confirm_sesskey()) {        // User has agreed
        if (!set_field('user', 'policyagreed', 1, 'id', $USER->id)) {
            error('Could not save your agreement');
        }
        $USER->policyagreed = 1;
        redirect($CFG->wwwroot);
        exit;
    }

    $strpolicyagree = get_string('policyagree');
    $strpolicyagreement = get_string('policyagreement');
    $strpolicyagreementclick = get_string('policyagreementclick');

    print_header($strpolicyagreement, $SITE->fullname, $strpolicyagreement);

    print_heading($strpolicyagreement);

    echo '<center>';
    echo '<iframe align="center" width="80%" height="70%" src="'.$CFG->sitepolicy.'" />';
    echo link_to_popup_window ($CFG->sitepolicy, 'agreement', $strpolicyagreementclick,
                               500, 500, 'Popup window', 'none', true);
    echo '</iframe>';
    echo '</center>';

    notice_yesno($strpolicyagree, "policy.php?agree=1&amp;sesskey=$USER->sesskey", $CFG->wwwroot);

    print_footer();

?>
