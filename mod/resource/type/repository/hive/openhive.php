<?php // $Id$

    require_once("../../../../../config.php");

    print_header();

    if (empty($CFG->hivehost) or empty($CFG->hivehost) or empty($CFG->hivehost) or empty($CFG->hivehost)) {
        notify('A Hive repository is not yet configured in Moodle.  Please see Resource settings.');
        die;
    }

    notify('Opening HarvestRoad Hive. Please wait. Contacting '. $CFG->hivehost );

    echo '<form name="OPEN_HIVE_FORM" action="'. $CFG->hiveprot .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath .'" method="post">';

    echo '<input type="hidden" name="HISTORY" value="">';
    echo '<input type="hidden" name="hiveLanguage" value="en_AU">';
    echo '<input type="hidden" name="PUBCATEGORY_LIST" value="">';
    echo '<input type="hidden" name="CATEGORY_LIST" value="">';
    echo '<input type="hidden" name="HIDE_LOGOUT" value="Y">';
    echo '<input type="hidden" name="mkurl" value="'.$CFG->wwwroot.'/mod/resource/type/repository/hive/makelink.php">';
    echo '<input type="hidden" name="HIDE_CHANGEUSERDETAILS" value="Y">';
    echo '<input type="hidden" name="HIVE_RET" value="ORG">';
    echo '<input type="hidden" name="HIVE_PAGE" value="Lite Browse">';
    echo '<input type="hidden" name="HIVE_REQ" value="2113">';
    echo '<input type="hidden" name="HIVE_ERRCODE" value="0">';
    echo '<input type="hidden" name="mklms" value="Moodle">';
    echo '<input type="hidden" name="HIVE_PROD" value="0">';
    echo '<input type="hidden" name="HIVE_REF" value="hin:hive@Hive Login HTML Template">';
    echo '<input type="hidden" name="HIVE_LITEMODE" value="liteBrowse">';
    echo '<input type="hidden" name="HIVE_SEREF" value="'.$CFG->wwwroot.'/sso/hive/expired.php">';
    echo '<input type="hidden" name="HIVE_SESSION" value="'.$SESSION->HIVE_SESSION.'">';
    echo '</form>';
    echo '<script language="javascript"/>';
    echo 'document.OPEN_HIVE_FORM.submit();';
    echo 'history.go(-1);';
    echo '</script>';

?>
