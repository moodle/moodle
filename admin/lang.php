<?PHP // $Id$

    require_once("../config.php");

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
            $title = $strmissingstrings;
            break;
        case "compare":
            $navigation = "<A HREF=\"lang.php\">$strchecklanguage</A> -> $strcomparelanguage";
            $title = $strcomparelanguage;
            break;
        default:
            $title = $strchecklanguage;
            $navigation = $strchecklanguage;
            break;
    }

    $currentlang = current_language();

    print_header("$site->shortname: $title", "$site->fullname",
                 "<A HREF=\"index.php\">$stradministration</A> -> $navigation");

    if (!$mode) {
        print_heading("$strcurrentlanguage: $currentlang - ".get_string("thislanguage"));
        print_heading("<A HREF=\"lang.php?mode=missing\">$strmissingstrings</A>");
        print_heading("<A HREF=\"lang.php?mode=compare\">$strcomparelanguage</A>");
        print_footer();
        exit;
    }

    // Get a list of all the root files in the English directory

    $langdir = "$CFG->dirroot/lang/$currentlang";
    $enlangdir = "$CFG->dirroot/lang/en";

    if (! $stringfiles = get_directory_list($enlangdir, "", false)) {
        error("Could not find English language pack!");
    }

    foreach ($stringfiles as $key => $file) {
        if ($file == "README" or $file == "help" or $file == "docs" or $file == "fonts") {
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
                if (!isset($string[$key]) or $string[$key] == "") {
                    $value = htmlspecialchars($value);
                    $value = str_replace("$"."a", "\\$"."a", $value);
                    $value = str_replace("%%","%",$value);
                    if ($first) {
                        echo "</PRE><HR><P><B>".get_string("stringsnotset","","$langdir/$file")."</B></P><PRE>";
                        $first = false;
                        $somethingfound = true;
                    }
                    echo "$"."string['$key'] = \"$value\";<BR>";
                }
            }
        }
    
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
    
        if (!empty($somethingfound)) {
            print_continue("lang.php");
        } else {
            notice(get_string("languagegood"), "lang.php");
        }

    } else if ($mode == "compare") {

        if (isset($_POST['file'])){   // Save a file
            $newstrings = $_POST;
            $file = $newstrings['file'];
            unset($newstrings['file']);
            if (lang_save_file($langdir, $file, $newstrings)) {
                notify(get_string("changessaved")." ($langdir/$file)");
            } else {
                error("Could not save the file '$file'!", "lang.php?mode=compare");
            }
        }

        print_heading($strcomparelanguage);
        echo "<CENTER>";
        helpbutton("langedit",$strcomparelanguage);
        echo "</CENTER>";

        foreach ($stringfiles as $file) {

            print_heading("$file", "LEFT", 4);

            if (!file_exists("$langdir/$file")) {
                echo "<P><FONT COLOR=red>".get_string("filemissing", "", "$langdir/$file")."</FONT></P>";
                continue;
            }
    
            error_reporting(0);
            if ($f = fopen("$langdir/$file","r+")) {
                $editable = true;
                fclose($f);
            } else {
                $editable = false;
                echo "<P><FONT SIZE=1>".get_string("makeeditable", "", "$langdir/$file")."</FONT></P>";
            }
            error_reporting(7);


            unset($string);
            include("$enlangdir/$file");
            $enstring = $string;  
            ksort($enstring);
    
            unset($string);
            include("$langdir/$file");

            if ($editable) {
                echo "<FORM NAME=\"$file\" ACTION=\"lang.php\" METHOD=\"POST\">";
            }
            echo "<TABLE WIDTH=\"100%\" CELLPADDING=2 CELLSPACING=3 BORDER=0>";
            foreach ($enstring as $key => $envalue) {
                $envalue = nl2br(htmlspecialchars($envalue));
                $envalue = str_replace("\$a","<B>\$a</B>", $envalue);  // Make variables bold
                $envalue = str_replace("%%","%",$envalue);
                // TODO:  It would be nice if all the $a->something variables were bold too

                echo "<TR>";
                echo "<TD WIDTH=20% BGCOLOR=\"$THEME->cellheading\" NOWRAP VALIGN=TOP>$key</TD>";
                echo "<TD WIDTH=40% BGCOLOR=\"$THEME->cellheading\" VALIGN=TOP>$envalue</TD>";

                $value = str_replace("\\","",$string[$key]);          // Delete all slashes
                $value = str_replace("%%","%",$value);
                $value = htmlspecialchars($value);

                $cellcolour = $value ? $THEME->cellcontent: $THEME->highlight;

                if ($editable) {
                    echo "<TD WIDTH=40% BGCOLOR=\"$cellcolour\" VALIGN=TOP>";
                    if (isset($string[$key])) {
                        $valuelen = strlen($value);
                    } else {
                        $valuelen = strlen($envalue);
                    }
                    $cols=50;
                    if (strstr($value, "\r") or strstr($value, "\n") or $valuelen > $cols) {
                        $rows = ceil($valuelen / $cols);
                        echo "<TEXTAREA NAME=\"string-$key\" cols=\"$cols\" rows=\"$rows\">$value</TEXTAREA>";
                    } else {
                        if ($valuelen) {
                            $cols = $valuelen + 2;
                        }
                        echo "<INPUT TYPE=\"TEXT\" NAME=\"string-$key\" VALUE=\"$value\" SIZE=\"$cols\"></TD>";
                    }
                    echo "</TD>";

                } else {
                    echo "<TD WIDTH=40% BGCOLOR=\"$cellcolour\" VALIGN=TOP>$value</TD>";
                }
            }
            if ($editable) {
                echo "<TR><TD COLSPAN=2>&nbsp;<TD>";
                echo "    <INPUT TYPE=\"hidden\" NAME=\"file\" VALUE=\"$file\">";
                echo "    <INPUT TYPE=\"hidden\" NAME=\"mode\" VALUE=\"compare\">";
                echo "    <INPUT TYPE=\"submit\" NAME=\"update\" VALUE=\"".get_string("savechanges").": $file\">";
                echo "</TD></TR>";
            }
            echo "</TABLE>";
            echo "</FORM>";
        }

        print_continue("lang.php");
    
    }

    print_footer();

//////////////////////////////////////////////////////////////////////

function lang_save_file($path, $file, $strings) {
// Thanks to Petri Asikainen for the original version of code 
// used to save language files.
//
// $path is a full pathname to the file
// $file is the file to overwrite.
// $strings is an array of strings to write

    global $CFG;

    error_reporting(0);
    if (!$f = fopen("$path/$file","w")) {
        return false;
    }
    error_reporting(7);


    fwrite($f, "<?PHP // \$Id\$ \n");
    fwrite($f, "      // $file - created with Moodle $CFG->release ($CFG->version)\n\n\n");

    ksort($strings);

    foreach ($strings as $key => $value) {
        list($id, $stringname) = explode("-",$key);
        if ($CFG->lang != "zh_hk" and $CFG->lang != "zh_tw") {  // Some MB languages include backslash bytes
            $value = str_replace("\\","",$value);           // Delete all slashes
        }
        $value = str_replace("$"."a", "\\$"."a", $value);   // Add slashes for $a
        $value = str_replace("\"", "\\\\\\\"", $value);     // Add slashes for "
        $value = str_replace("%","%%",$value);              // Escape % characters
        if ($id == "string" and $value != ""){
            fwrite($f,"\$string['$stringname'] = \"$value\";\n");
        }
    }
    fwrite($f,"\n?>\n");
    fclose($f);

    return true;
}

?>
