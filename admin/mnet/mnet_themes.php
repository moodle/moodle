<?php
    // Allows the admin to configure services for remote hosts

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');
    require_login();
    admin_externalpage_setup('mnetpeers');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    if (!$site = get_site()) {
        print_error('nosite', '', '', NULL, true);
    }

/// Initialize variables.

    $hostid = required_param('hostid', PARAM_INT);

    $stradministration   = get_string('administration');
    $strconfiguration    = get_string('configuration');

    $strmnetedithost   = get_string('reviewhostdetails', 'mnet');
    $strmnetsettings     = get_string('mnetsettings', 'mnet');
    $strmnetservices     = get_string('mnetservices', 'mnet');
    $strmnetthemes       = get_string('mnetthemes', 'mnet');
    $strmnetlog          = get_string('mnetlog', 'mnet');


    $mnet_peer = new mnet_peer();
    if (is_int($hostid)) {
        $mnet_peer->set_id($hostid);
    }

    $choose = optional_param("choose",'',PARAM_FILE);   // set this theme as default
    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strthemes = get_string("themes");
    $strpreview = get_string("preview");
    $strchoose = get_string("choose");
    $strinfo = get_string("info");
    $strtheme = get_string("theme");
    $strthemesaved = get_string("themesaved");
    $strscreenshot = get_string("screenshot");
    $stroldtheme = get_string("oldtheme");
    $report = array();
    $unlikely_name = 'ZoqZoqZ'; // Something unlikely to ever be a theme name

    if ($choose) {
        if (confirm_sesskey()) {
            if ($choose == $unlikely_name) {
                $mnet_peer->force_theme = 1;
                $mnet_peer->updateparams->force_theme = 1;
                $mnet_peer->theme = '';
                $mnet_peer->updateparams->theme = '';
                if ($mnet_peer->commit()) {
                    $report = array(get_string('themesaved'), 'informationbox');
                } else {
                    $report = array(get_string('themesavederror', 'mnet'), 'errorbox');
                }
            } elseif (!is_dir($CFG->themedir .'/'. $choose) || !file_exists($CFG->themedir .'/'. $choose .'/config.php')) {
                        echo 'CHOOSE -'.$choose.' '. $CFG->themedir .'/'. $choose .'/config.php' ;
                $report = array('This theme is not installed!'.'3', 'errorbox');
            } else {
                $mnet_peer->force_theme = 1;
                $mnet_peer->theme = $choose;
                $mnet_peer->updateparams->theme = addslashes($choose);
                if ($mnet_peer->commit()) {
                    $report = array(get_string('themesaved').'1', 'informationbox');
                } else {
                    $report = array(get_string('themesavederror', 'mnet').'2', 'errorbox');
                }
            }
        }
    }

    $adminroot = admin_get_root();
    require('./mnet_themes.html');
?>
