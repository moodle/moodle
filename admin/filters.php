<?PHP // $Id$
      // Allows the admin to create, delete and rename course categories

    require_once("../config.php");

    optional_variable($iselect);
    optional_variable($uselect);
    optional_variable($add);
    optional_variable($remove);
    optional_variable($up);
    optional_variable($down);

    require_login();

    if (!isadmin()) {
        error("Only administrators can use this page!");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

/// Print headings

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strmanagefilters = get_string("managefilters");
    $strversion = get_string("version");
    $strsettings = get_string("settings");
    $strup = get_string("up");
    $strdown = get_string("down");
    $stractive = get_string("active");
    $strinactive = get_string("inactive");
    $strcachetext = get_string("cachetext", "admin");
    $strconfigcachetext = get_string("configcachetext");
    $strfilteruploadedfiles = get_string("filteruploadedfiles", "admin");
    $strconfigfilteruploadedfiles = get_string("configfilteruploadedfiles");

    print_header("$site->shortname: $strmanagefilters", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> $strmanagefilters");

    print_heading($strmanagefilters);


/// Make a list of all available filters and the best names for them we can find
    $allfilters = array();

    $filterlocations = array("mod", "filter");

    foreach ($filterlocations as $filterlocation) {
        $plugins = get_list_of_plugins($filterlocation);
        foreach ($plugins as $key => $plugin) {
            if (is_readable("$CFG->dirroot/$filterlocation/$plugin/filter.php")) {
                $name = trim(get_string("filtername", $plugin));
                if (empty($name) or $name == "[[filtername]]") {
                    $name = $plugin;
                }
                $allfilters["$filterlocation/$plugin"] = $name;
            }
        }
    }

   
/// Make an array of all the currently installed filters

    $installedfilters = array();
    if (!empty($CFG->textfilters)) {
        $installedfilters = explode(',',$CFG->textfilters);

        // Do a little cleanup for robustness
        foreach ($installedfilters as $key => $installedfilter) {
            if (empty($installedfilter)) {
                unset($installedfilters[$key]);
                set_config("textfilters", implode(',', $installedfilters));
            }
        }
    }

    $selectedfilter = "none";

/// If data submitted, then process and store.

    if (!empty($options)) {
	    if ($config = data_submitted()) {  
            unset($config->options);
            foreach ($config as $name => $value) {
                set_config($name, $value);
            }
        }
    }

    if (!empty($add) and !empty($uselect)) {
        $selectedfilter = $uselect;
        if (!in_array($selectedfilter, $installedfilters)) {
            $installedfilters[] = $selectedfilter;
            set_config("textfilters", implode(',', $installedfilters));
        }

    } else if (!empty($remove) and !empty($iselect)) {
        $selectedfilter = $iselect;
        foreach ($installedfilters as $key => $installedfilter) {
            if ($installedfilter == $selectedfilter) {
                unset($installedfilters[$key]);
            }
        }
        set_config("textfilters", implode(',', $installedfilters));

    } else if ((!empty($up) or !empty($down)) and !empty($iselect)) {

        if (!empty($up)) {
            if ($allfilters[$iselect]) {
                foreach ($installedfilters as $key => $installedfilter) {
                    if ($installedfilter == $iselect) {
                        $movefilter = $key;
                        break;
                    }
                    $swapfilter = $key;
                }
            }
        }
        if (!empty($down)) {
            if ($allfilters[$iselect]) {
                $choosenext = false;
                foreach ($installedfilters as $key => $installedfilter) {
                    if ($choosenext) {
                        $swapfilter = $key;
                        break;
                    }
                    if ($installedfilter == $iselect) {
                        $movefilter = $key;
                        $choosenext = true;
                    }
                }
            }
        }
        if (isset($swapfilter) and isset($movefilter)) {
            $tempfilter = $installedfilters[$swapfilter];
            $installedfilters[$swapfilter] = $installedfilters[$movefilter];
            $installedfilters[$movefilter] = $tempfilter;
            set_config("textfilters", implode(',', $installedfilters));
        }
        $selectedfilter = $iselect;
    }



/// Make an array of all the currently uninstalled filters

    $uninstalledfilters = array();
    foreach ($allfilters as $filter => $name) {
        $installed = false;
        foreach ($installedfilters as $installedfilter) {
            if ($installedfilter == $filter) {
                $installed = true;
            }
        }
        if (!$installed) {
            $uninstalledfilters[] = $filter;
        }
    }

/// Print the current form

    include("filters.html");


    print_footer();

?>
