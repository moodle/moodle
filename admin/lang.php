<?PHP // $Id$

	require("../config.php");

    require_login();

    if (!isadmin()) {
        error("You need to be admin to edit this page");
    }     

    if (! $site = get_site()) {
        error("Site not defined!");
    }

    $stradministration = get_string("administration");
    $strchecklanguage = get_string("checklanguage");

    print_header("$site->fullname", "$site->fullname",
                 "<A HREF=\"index.php\">$stradministration</A> 
                  -> $strchecklanguage -> $CFG->lang");


    // Get a list of all the files in the English directory

    if (! $files = get_directory_list("$CFG->dirroot/lang/en", "CVS")) {
        error("Could not find English language pack!");
    }

    // For each file, check that a counterpart exists, then check all the strings

    $langdir = "$CFG->dirroot/lang/$CFG->lang";
    $enlangdir = "$CFG->dirroot/lang/en";

    $dir = opendir($enlangdir);


    while ($file = readdir($dir)) {
        if ($file == "." or $file == ".." or $file == "CVS" or $file == "README" or $file == "help" or $file == "docs") {
            continue;
        }

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

    if (!$somethingfound) {
        notice(get_string("languagegood"), "index.php");
    } else {
        print_continue("index.php");
    }

    print_footer();

?>
