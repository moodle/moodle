<?php // $Id$

    require_once('../config.php');

    $action     = optional_param('action', '', PARAM_ACTION);
    $filterpath = optional_param('filterpath', '', PARAM_PATH);

    require_login();
    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=managefilters";

    if (!confirm_sesskey()) {
        redirect($returnurl);
    }

    // get a list of installed filters
    $installedfilters = array();
    $filterlocations = array('mod','filter');
    foreach ($filterlocations as $filterlocation) {
        $plugins = get_list_of_plugins($filterlocation);
        foreach ($plugins as $plugin) {
            $pluginpath = "$CFG->dirroot/$filterlocation/$plugin/filter.php";
            if (is_readable($pluginpath)) {
                $installedfilters["$filterlocation/$plugin"] = "$filterlocation/$plugin";
            }
        }
    }

    // get all the currently selected filters
    if (!empty($CFG->textfilters)) {
        $activefilters = explode(',', $CFG->textfilters);
    } else {
        $activefilters = array();
    }

    //======================
    // Process Actions
    //======================

    switch ($action) {

    case 'hide':
        $key=array_search($filterpath, $activefilters);
        // check filterpath is valid
        if ($key===false) {
            break;
        }
        // just delete it
        unset($activefilters[$key]);
        break;

    case 'show':
        // check filterpath is valid
        if (!array_key_exists($filterpath, $installedfilters)) {
            error("Filter $filterpath is not currently installed", $url);
        } elseif (array_search($filterpath,$activefilters)) {
            // filterpath is already active - doubleclick??
        } else {
            // add it to installed filters
            $activefilters[] = $filterpath;
            $activefilters = array_unique($activefilters);
        }
        break;

    case 'down':
        $key=array_search($filterpath, $activefilters);
        // check filterpath is valid
        if ($key===false) {
            error("Filter $filterpath is not currently active", $url);
        } elseif ($key>=(count($activefilters)-1)) {
            // cannot be moved any further down - doubleclick??
        } else {
            // swap with $key+1
            $fsave = $activefilters[$key];
            $activefilters[$key] = $activefilters[$key+1];
            $activefilters[$key+1] = $fsave;
        }
        break;

    case 'up':
        $key=array_search($filterpath, $activefilters);
        // check filterpath is valid
        if ($key===false) {
            error("Filter $filterpath is not currently active", $url);
        } elseif ($key<1) {
            //cannot be moved any further up - doubleclick??
        } else {
            // swap with $key-1
            $fsave = $activefilters[$key];
            $activefilters[$key] = $activefilters[$key-1];
            $activefilters[$key-1] = $fsave;
        }
        break;
    }

    // save, reset cache and return
    set_config('textfilters', implode(',', $activefilters));
    reset_text_filters_cache();
    redirect($returnurl);

?>
