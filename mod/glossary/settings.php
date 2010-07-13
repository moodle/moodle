<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/glossary/lib.php');

    $settings->add(new admin_setting_heading('glossary_normal_header', get_string('glossaryleveldefaultsettings', 'glossary'), ''));

    $settings->add(new admin_setting_configtext('glossary_entbypage', get_string('entbypage', 'glossary'),
                       get_string('entbypage', 'glossary'), 10, PARAM_INT));


    $settings->add(new admin_setting_configcheckbox('glossary_dupentries', get_string('allowduplicatedentries', 'glossary'),
                       get_string('cnfallowdupentries', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_allowcomments', get_string('allowcomments', 'glossary'),
                       get_string('cnfallowcomments', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_linkbydefault', get_string('usedynalink', 'glossary'),
                       get_string('cnflinkglossaries', 'glossary'), 1));

    $settings->add(new admin_setting_configcheckbox('glossary_defaultapproval', get_string('defaultapproval', 'glossary'),
                       get_string('cnfapprovalstatus', 'glossary'), 1));


    if (empty($CFG->enablerssfeeds)) {
        $options = array(0 => get_string('rssglobaldisabled', 'admin'));
        $str = get_string('configenablerssfeeds', 'glossary').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

    } else {
        $options = array(0=>get_string('no'), 1=>get_string('yes'));
        $str = get_string('configenablerssfeeds', 'glossary');
    }
    $settings->add(new admin_setting_configselect('glossary_enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                       $str, 0, $options));


    $settings->add(new admin_setting_heading('glossary_levdev_header', get_string('entryleveldefaultsettings', 'glossary'), ''));

    $settings->add(new admin_setting_configcheckbox('glossary_linkentries', get_string('usedynalink', 'glossary'),
                       get_string('cnflinkentry', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_casesensitive', get_string('casesensitive', 'glossary'),
                       get_string('cnfcasesensitive', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_fullmatch', get_string('fullmatch', 'glossary'),
                       get_string('cnffullmatch', 'glossary'), 0));


    //Update and get available formats
    $recformats = glossary_get_available_formats();
    $formats = array();
    //Take names
    foreach ($recformats as $format) {
        $formats[$format->id] = get_string("displayformat$format->name", "glossary");
    }
    asort($formats);

    $str = '<table>';
    $pixpath = "$CFG->wwwroot/pix"; // ugly temporary hack until we get this rewritten to new setting
    // TODO MDL-19674 We cannot refer to $CFG->pixpath here. This code is executed
    // when the structure of the admin tree is being build. $CFG->pixpath is only
    // set up once output has started.
    foreach ($formats as $formatid=>$formatname) {
        $recformat = $DB->get_record('glossary_formats', array('id'=>$formatid));
        $str .= '<tr>';
        $str .= '<td>' . $formatname . '</td>';
        $eicon = "<a title=\"".get_string("edit")."\" href=\"$CFG->wwwroot/mod/glossary/formats.php?id=$formatid&amp;mode=edit\"><img class=\"iconsmall\" src=\"$pixpath/t/edit.gif\" alt=\"".get_string("edit")."\" /></a>";
        if ( $recformat->visible ) {
            $vtitle = get_string("hide");
            $vicon  = "hide.gif";
        } else {
            $vtitle = get_string("show");
            $vicon  = "show.gif";
        }
        $vicon = "<a title=\"".$vtitle."\" href=\"$CFG->wwwroot/mod/glossary/formats.php?id=$formatid&amp;mode=visible&amp;sesskey=".sesskey()."\"><img class=\"iconsmall\" src=\"$pixpath/t/".$vicon."\" alt=\"$vtitle\" /></a>";

        $str .= '<td align="center">'.$eicon.'&nbsp;&nbsp;'.$vicon.'</td>';
        $str .= '</tr>';
    }
    $str .= '</table>';

    $settings->add(new admin_setting_heading('glossary_formats_header', get_string('displayformatssetup', 'glossary'), $str));
}
