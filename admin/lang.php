<?PHP // $Id$

	require("../config.php");

    optional_variable($mode, "");

    require_login();

    if (!isadmin()) {
        error("You need to be admin to edit this page");
    }     

    if (! $site = get_site()) {
        error("Site not defined!");
    }

    $stradministration = get_string("administration");
    $strchecklanguage = get_string("checklanguage");
    $strcurrentlanguage = get_string("currentlanguage");
    $strmissingstrings = get_string("missingstrings");
    $strcomparelanguage = get_string("comparelanguage");
    $strthislanguage = get_string("thislanguage");

    switch ($mode) {
        case "missing":
            $navigation = "<A HREF=\"lang.php\">$strchecklanguage</A> -> $strmissingstrings";
            break;
        case "compare":
            $navigation = "<A HREF=\"lang.php\">$strchecklanguage</A> -> $strcomparelanguage";
            break;
        default:
            $navigation = $strchecklanguage;
            break;
    }

    print_header("$site->fullname", "$site->fullname",
                 "<A HREF=\"index.php\">$stradministration</A> -> $navigation");

    if (!$mode) {
        print_heading("$strcurrentlanguage: $CFG->lang");
        print_heading("<A HREF=\"lang.php?mode=missing\">$strmissingstrings</A>");
        print_heading("<A HREF=\"lang.php?mode=compare\">$strcomparelanguage</A>");
        print_footer();
        exit;
    }

    if ($CFG->lang == "en") {
        notice("Nothing to check - you are using the English language pack!", "lang.php");
    }

    // Get a list of all the root files in the English directory

    $langdir = "$CFG->dirroot/lang/$CFG->lang";
    $enlangdir = "$CFG->dirroot/lang/en";

    if (! $stringfiles = get_directory_list($enlangdir, "", false)) {
        error("Could not find English language pack!");
    }

    foreach ($stringfiles as $key => $file) {
        if ($file == "README" or $file == "help" or $file == "docs") {
            unset($stringfiles[$key]);
        }
    }

    if ($mode == "missing") {
        // For each file, check that a counterpart exists, then check all the strings
    
        foreach ($stringfiles as $file) {
            if (!file_exists("$langdir/$file")) {
                echo "<P><FONT COLOR=red>".get_string("filemissing", "", "$langdir/$file")."</FONT></P>";
                continue;
            }
    
            unset($string);
            include("$enlangdir/$file");
            $enstring = $string;  
    
            unset($string);
            include("$langdir/$file");
    
            $first = true;
            foreach ($enstring as $key => $value) {
                if (!isset($string[$key])) {
                    $value = htmlentities($value);
                    $value = str_replace("$"."a", "\\$"."a", $value);
                    if ($first) {
                        echo "</PRE><HR><P><B>".get_string("stringsnotset","","$langdir/$file")."</B></P><PRE>";
                        $first = false;
                        $somethingfound = true;
                    }
                    echo "$"."string[$key] = \"$value\";<BR>";
                }
            }
        }
        closedir($dir);
    
        if (! $files = get_directory_list("$CFG->dirroot/lang/en/help", "CVS")) {
            error("Could not find English language help files!");
        }
    
        foreach ($files as $filekey => $file) {    // check all the help files.
            if (!file_exists("$langdir/help/$file")) {
                echo "<P><FONT COLOR=red>".get_string("filemissing", "", "$langdir/help/$file")."</FONT></P>";
                $somethingfound = true;
                continue;
            }
        }
    
        if (! $files = get_directory_list("$CFG->dirroot/lang/en/docs", "CVS")) {
            error("Could not find English language docs files!");
        }
        foreach ($files as $filekey => $file) {    // check all the docs files.
            if (!file_exists("$langdir/docs/$file")) {
                echo "<P><FONT COLOR=red>".get_string("filemissing", "", "$langdir/docs/$file")."</FONT></P>";
                $somethingfound = true;
                continue;
            }
        }
    
        if ($somethingfound) {
            print_continue("lang.php");
        } else {
            notice(get_string("languagegood"), "lang.php");
        }

    } else if ($mode == "compare") {

        foreach ($stringfiles as $file) {
            print_heading("$file", "LEFT", 4);

            if (!file_exists("$langdir/$file")) {
                echo "<P><FONT COLOR=red>".get_string("filemissing", "", "$langdir/$file")."</FONT></P>";
                continue;
            }
    
            unset($string);
            include("$enlangdir/$file");
            $enstring = $string;  
    
            unset($string);
            include("$langdir/$file");
    
            echo "<TABLE WIDTH=\"100%\" BORDER=1>";
            foreach ($enstring as $key => $value) {
                $value = htmlentities($value);
                $value = str_replace("$"."a", "\\$"."a", $value);
                echo "<TR>";
                echo "<TD WIDTH=20% BGCOLOR=white NOWRAP VALIGN=TOP>$key</TD>";
                echo "<TD WIDTH=40% VALIGN=TOP>$value</TD>";
                if (isset($string[$key])) {
                    $value = htmlentities($string[$key]);
                    $value = str_replace("$"."a", "\\$"."a", $value);
                    echo "<TD WIDTH=40% BGCOLOR=white VALIGN=TOP>$value</TD>";
                } else {
                    echo "<TD WIDTH=40% BGCOLOR=white ALIGN=CENTER VALIGN=TOP>-</TD>";
                }
            }
            echo "</TABLE>";
        }

        print_continue("lang.php");
    
    }

    print_footer();

?>
