<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/filelib.php');

    $agree = optional_param('agree', 0, PARAM_BOOL);

    define('MESSAGE_WINDOW', true);  // This prevents the message window coming up

    if (!isloggedin()) {
        require_login();
    }

    if ($agree and confirm_sesskey()) {    // User has agreed
        if (!isguestuser()) {              // Don't remember guests
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

    print_header($strpolicyagreement, $SITE->fullname, build_navigation(array(array('name'=>$strpolicyagreement, 'link'=>null, 'type'=>'misc'))));

    print_heading($strpolicyagreement);

    $mimetype = mimeinfo('type', $CFG->sitepolicy);
    if ($mimetype == 'document/unknown') {
        //fallback for missing index.php, index.html
        $mimetype = 'text/html';
    }

    echo '<div class="noticebox">';
    echo '<object id="policyframe" data="'.$CFG->sitepolicy.'" type="'.$mimetype.'">';
    // we can not use our popups here, because the url may be arbitrary, see MDL-9823
    echo '<a href="'.$CFG->sitepolicy.'" onclick="this.target=\'_blank\'">'.$strpolicyagreementclick.'</a>';
    echo '</object></div>';

    $linkyes    = 'policy.php';
    $optionsyes = array('agree'=>1, 'sesskey'=>sesskey());
    $linkno     = $CFG->wwwroot.'/login/logout.php';
    $optionsno  = array('sesskey'=>sesskey());
    notice_yesno($strpolicyagree, $linkyes, $linkno, $optionsyes, $optionsno);

    print_footer();

?>
