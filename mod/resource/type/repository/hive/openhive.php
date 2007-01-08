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

$query .= 'HISTORY=';
$query .= '&hiveLanguage=en_AU';
$query .= '&PUBCATEGORY_LIST=';
$query .= '&CATEGORY_LIST=';
$query .= '&HIDE_LOGOUT=Y';
$query .= '&mkurl='.$CFG->wwwroot.'/mod/resource/type/repository/hive/makelink.php';
$query .= '&HIDE_CHANGEUSERDETAILS=Y';
$query .= '&HIVE_RET=ORG';
$query .= '&HIVE_PAGE=Lite%20Browse';
$query .= '&HIVE_REQ=2113';
$query .= '&HIVE_ERRCODE=0';
$query .= '&mklms=Doodle';
$query .= '&HIVE_PROD=0';
$query .= '&HIVE_CURRENTBUREAUID='.$CFG->decsbureauid;
$query .= '&HIVE_REF=hin:hive@Hive%20Login%20HTML%20Template';
$query .= '&HIVE_LITEMODE=liteBrowse';
$query .= '&HIVE_SEREF='.$CFG->wwwroot.'/sso/hive/expired.php';
$query .= '&HIVE_SESSION='.$SESSION->HIVE_SESSION;

    redirect($CFG->hiveprotocol .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath .'?'.$query);
/***********8
    notify('Opening HarvestRoad Hive. Please wait. Contacting '. $CFG->hivehost );

    echo '<form id="OPEN_HIVE_FORM" action="'. $CFG->hiveprotocol .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath .'" method="post">';

    echo '<input type="hidden" name="HISTORY" value="" />';
    echo '<input type="hidden" name="hiveLanguage" value="en_AU" />';
    echo '<input type="hidden" name="PUBCATEGORY_LIST" value="" />';
    echo '<input type="hidden" name="CATEGORY_LIST" value="" />';
    echo '<input type="hidden" name="HIDE_LOGOUT" value="Y" />';
    echo '<input type="hidden" name="mkurl" value="'.$CFG->wwwroot.'/mod/resource/type/repository/hive/makelink.php">';
    echo '<input type="hidden" name="HIDE_CHANGEUSERDETAILS" value="Y" />';
    echo '<input type="hidden" name="HIVE_RET" value="ORG" />';
    echo '<input type="hidden" name="HIVE_PAGE" value="Lite Browse" />';
    echo '<input type="hidden" name="HIVE_REQ" value="2113" />';
    echo '<input type="hidden" name="HIVE_ERRCODE" value="0" />';
    echo '<input type="hidden" name="mklms" value="Moodle" />';
    echo '<input type="hidden" name="HIVE_PROD" value="0" />';
    echo '<input type="hidden" name="HIVE_REF" value="hin:hive@Hive Login HTML Template" />';
    echo '<input type="hidden" name="HIVE_LITEMODE" value="liteBrowse" />';
    echo '<input type="hidden" name="HIVE_SEREF" value="'.$CFG->wwwroot.'/sso/hive/expired.php">';
    echo '<input type="hidden" name="HIVE_SESSION" value="'.$SESSION->HIVE_SESSION.'">';
    echo '</form>';
    echo '<script type="text/javascript"/>';
    echo "\n//<![CDATA[\n";
    echo 'getElementById(\'OPEN_HIVE_FORM\').submit();';
    echo "\n//]]>\n";
    echo '</script>';

    print_footer();
*************/
?>
