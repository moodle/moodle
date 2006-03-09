<?php // $Id$

    require_once("../../../../../config.php");

    if (empty($CFG->hivehost) or empty($CFG->hiveport) or empty($CFG->hiveprotocol) or empty($CFG->hivepath)) {
        print_header();
        notify('A Hive repository is not yet configured in Moodle.  Please see Resource settings.');
        print_footer();
        die;
    }

    if (empty($SESSION->HIVE_SESSION)) {
        print_header();
        notify('You do not have access to the Hive repository. Moodle signs you into Hive when you log in. This process may have failed.');
        close_window_button();
        print_footer();
        die;
    }

// MW Redirect to the Hive page. No need for a self-posting form as there is no sensitive data.

///=====================================================
    $stylesheets = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        if(empty($stylesheets)) {
          $stylesheets = $stylesheet;
        } else {
          $stylesheets .= '%26'.$stylesheet;
        }
    }

 $query  = '';
 /// $query .= 'HIVE_REF=hin:hive@Demo%20LMS%20Browse';
 $query .= 'HIVE_REF=hin:hive@LMS%20Search';
 $query .= '&HIVE_RET=ORG';
 $query .= '&HIVE_REQ=2113';
 $query .= '&HIVE_PROD=0';
 $query .= '&HIVE_CURRENTBUREAUID='.$CFG->decsbureauid;
 $query .= '&HIVE_BUREAU='.$CFG->decsbureauid;
 $query .= '&HIVE_ITEMTYPE='.$CFG->decsitemtypeid;
 $query .= '&mkurl='.$CFG->wwwroot.'/mod/resource/type/repository/hive/makelink.php';
 $query .= '&mklms=Moodle';
 $query .= '&HIVE_SEREF='.$CFG->wwwroot.'/sso/hive/expired.php';
 $query .= '&HIVE_SESSION='.$SESSION->HIVE_SESSION;
 $query .= '&mklmsstyle='.$stylesheets;
    redirect($CFG->hiveprotocol .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath .'?'.$query);
?>
