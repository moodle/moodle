<?PHP  // $Id$

    require("../config.php");

    if ($CFG->forcelogin) {
        require_login();
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/doc/?lang=", $langs, "chooselang", $currlang, "", "", "", true, "parent");
    }

    if (! $site = get_site()) {
        error("Site is misconfigured");
    }
    $strdocumentation = get_string("documentation");
    print_header("$site->shortname: $strdocumentation", "$site->fullname", "$strdocumentation", "", "", true, $langmenu, navmenu($site, NULL, "parent"));
    
?>

