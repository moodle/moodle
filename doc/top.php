<?PHP  // $Id$

    require("../config.php");

    if (!empty($SESSION->doclang)) {
        $currlang = $SESSION->doclang;
    } else {
        $currlang = current_language();
    }

    $langs = get_list_of_languages();
    $langmenu = popup_form ("$CFG->wwwroot/doc/?lang=", $langs, "chooselang", $currlang, "", "", "", true);

    if (! $site = get_site()) {
        error("Site is misconfigured");
    }
    $strdocumentation = get_string("documentation");
    print_header("$site->shortname: $strdocumentation", "$site->fullname", "$strdocumentation", "", "", true, $langmenu, navmenu($site));
    
?>

