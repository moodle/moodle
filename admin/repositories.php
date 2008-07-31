<?php // $Id$

    require_once('../config.php');

    $action     = optional_param('action', '', PARAM_ACTION);
    $repositorypath = optional_param('filterpath', '', PARAM_PATH);

    require_login();
    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=managereps";

    if (!confirm_sesskey()) {
        redirect($returnurl);
    }

    // get a list of installed repositories
    $installedrepositories = array();
    $repositorylocations = array('repository');
    foreach ($repositorylocations as $repositorylocation) {
        $plugins = get_list_of_plugins($repositorylocation);
        foreach ($plugins as $plugin) {
            $pluginpath = "$CFG->dirroot/$repositorylocation/$plugin/version.php";
            if (is_readable($pluginpath)) {
                $installedrepositories["$repositorylocation/$plugin"] = "$repositorylocation/$plugin";
            }
        }
    }

    // get all the currently selected repositories
    if (!empty($CFG->textfilters)) {
        $activerepositories = explode(',', $CFG->textfilters);
    } else {
        $activerepositories = array();
    }

    //======================
    // Process Actions
    //======================

    switch ($action) {

    case 'hide':
        $key=array_search($repositorypath, $activerepositories);
        // check repositorypath is valid
        if ($key===false) {
            break;
        }
        // just delete it
        unset($activerepositories[$key]);
        break;

    case 'show':
        // check repositorypath is valid
        if (!array_key_exists($repositorypath, $installedrepositories)) {
            print_error('filternotinstalled', 'error', $url, $repositorypath);
        } elseif (array_search($repositorypath,$activerepositories)) {
            // repositorypath is already active - doubleclick??
        } else {
            // add it to installed filters
            $activerepositories[] = $repositorypath;
            $activerepositories = array_unique($activerepositories);
        }
        break;

    case 'down':
        $key=array_search($repositorypath, $activerepositories);
        // check repositorypath is valid
        if ($key===false) {
            print_error("filternotactive", 'error', $url, $repositorypath );
        } elseif ($key>=(count($activerepositories)-1)) {
            // cannot be moved any further down - doubleclick??
        } else {
            // swap with $key+1
            $fsave = $activerepositories[$key];
            $activerepositories[$key] = $activerepositories[$key+1];
            $activerepositories[$key+1] = $fsave;
        }
        break;

    case 'up':
        $key=array_search($repositorypath, $activerepositories);
        // check repositorypath is valid
        if ($key===false) {
            print_error("filternotactive", 'error', $url, $repositorypath );
        } elseif ($key<1) {
            //cannot be moved any further up - doubleclick??
        } else {
            // swap with $key-1
            $fsave = $activerepositories[$key];
            $activerepositories[$key] = $activerepositories[$key-1];
            $activerepositories[$key-1] = $fsave;
        }
        break;
    }

    // save, reset cache and return
    set_config('textfilters', implode(',', $activerepositories));
    reset_text_filters_cache();
    redirect($returnurl);

?>
