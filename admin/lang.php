<?PHP // $Id$

    require_once("../config.php");

    optional_variable($mode, "");
    optional_variable($currentfile, "moodle.php");

    require_login();

    if (!isadmin()) {
        error("You need to be admin to edit this page");
    }     

    if (! $site = get_site()) {
        error("Site not defined!");
    }

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strlanguage = get_string("language");
    $strcurrentlanguage = get_string("currentlanguage");
    $strmissingstrings = get_string("missingstrings");
    $strcomparelanguage = get_string("comparelanguage");
    $strthislanguage = get_string("thislanguage");

    switch ($mode) {
        case "missing":
            $navigation = "<A HREF=\"lang.php\">$strlanguage</A> -> $strmissingstrings";
            $title = $strmissingstrings;
            break;
        case "compare":
            $navigation = "<A HREF=\"lang.php\">$strlanguage</A> -> $strcomparelanguage";
            $title = $strcomparelanguage;
            break;
        default:
            $title = $strlanguage;
            $navigation = $strlanguage;
            break;
    }

    $currentlang = current_language();

    print_header("$site->shortname: $title", "$site->fullname",
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> $navigation");

    if (!$mode) {
        $currlang = current_language();
        $langs = get_list_of_languages();
        echo "<table align=center><tr><td align=\"right\">";
        echo "<b>$strcurrentlanguage:</b>";
        echo "</td><td>";
        echo popup_form ("$CFG->wwwroot/admin/lang.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
        echo "</td></tr></table>";
        print_heading("<a href=\"lang.php?mode=missing\">$strmissingstrings</a>");
        print_heading("<a href=\"lang.php?mode=compare\">$strcomparelanguage</a>");
        echo "<center><hr noshade size=1>";
        $options["lang"] = $currentlang;
        print_single_button("http://moodle.org/download/lang/", $options, get_string("latestlanguagepack"));
        echo "</center>";
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
        if (substr($file, -4) != ".php") {
            unset($stringfiles[$key]);
        }
    }

    if ($mode == "missing") {
        // For each file, check that a counterpart exists, then check all the strings
    
        foreach ($stringfiles as $file) {
            if (!file_exists("$langdir/$file")) {
                if (!touch("$langdir/$file")) {
                    echo "<p><font color=red>".get_string("filemissing", "", "$langdir/$file")."</font></p>";
                    continue;
                }
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
                echo "<p><font color=red>".get_string("filemissing", "", "$langdir/help/$file")."</font></p>";
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

        if (isset($_POST['currentfile'])){   // Save a file
            $newstrings = $_POST;
            unset($newstrings['currentfile']);
            if (lang_save_file($langdir, $currentfile, $newstrings)) {
                notify(get_string("changessaved")." ($langdir/$currentfile)", "green");
            } else {
                error("Could not save the file '$currentfile'!", "lang.php?mode=compare&currentfile=$currentfile");
            }
        }

        print_heading_with_help($strcomparelanguage, "langedit");

        print_simple_box_start("center", "80%");
        echo '<center><font size="2">';
        foreach ($stringfiles as $file) {
            if ($file == $currentfile) {
                echo "<b>$file</b> &nbsp; ";
            } else {
                echo "<a href=\"lang.php?mode=compare&currentfile=$file\">$file</a> &nbsp; ";
            }
        }
        echo '</font></center>';
        print_simple_box_end();

        
        print_heading("$currentfile", "center", 4);

        if (!file_exists("$langdir/$currentfile")) {
            if (!touch("$langdir/$currentfile")) {
                echo "<p><font color=red>".get_string("filemissing", "", "$langdir/$currentfile")."</font></p>";
                continue;
            }
        }

        error_reporting(0);
        if ($f = fopen("$langdir/$currentfile","r+")) {
            $editable = true;
            fclose($f);
        } else {
            $editable = false;
            echo "<p><font size=1>".get_string("makeeditable", "", "$langdir/$currentfile")."</font></p>";
        }
        error_reporting(7);


        unset($string);
        include("$enlangdir/$currentfile");
        $enstring = $string;  
        ksort($enstring);

        unset($string);
        include("$langdir/$currentfile");

        if ($editable) {
            echo "<form name=\"$currentfile\" action=\"lang.php\" method=\"post\">";
        }
        echo "<table width=\"100%\" cellpadding=2 cellspacing=3 border=0>";
        foreach ($enstring as $key => $envalue) {
            $envalue = nl2br(htmlspecialchars($envalue));
            $envalue = preg_replace('/(\$a\-\&gt;[a-zA-Z0-9]*|\$a)/', '<b>$0</b>', $envalue);  // Make variables bold. 
            $envalue = str_replace("%%","%",$envalue);
            $envalue = str_replace("\\","",$envalue);              // Delete all slashes

            echo "\n\n<tr>";
            echo "<td dir=ltr lang=en width=20% bgcolor=\"$THEME->cellheading\" nowrap valign=top>$key</td>\n";
            echo "<td dir=ltr lang=en width=40% bgcolor=\"$THEME->cellheading\" valign=top>$envalue</td>\n";

            $value = $string[$key];
            $value = str_replace("\r","",$value);              // Bad character caused by Windows
            $value = preg_replace("/\n{3,}/", "\n\n", $value); // Collapse runs of blank lines
            $value = trim($value, "\n");                       // Delete leading/trailing lines
            $value = str_replace("\\","",$value);              // Delete all slashes
            $value = str_replace("%%","%",$value);
            $value = str_replace("<","&lt;",$value);
            $value = str_replace(">","&gt;",$value);
            $value = str_replace('"',"&quot;",$value);

            $cellcolour = $value ? $THEME->cellcontent: $THEME->highlight;

            if ($editable) {
                echo "<td width=40% bgcolor=\"$cellcolour\" valign=top>\n";
                if (isset($string[$key])) {
                    $valuelen = strlen($value);
                } else {
                    $valuelen = strlen($envalue);
                }
                $cols=50;
                if (strstr($value, "\r") or strstr($value, "\n") or $valuelen > $cols) {
                    $rows = ceil($valuelen / $cols);
                    echo "<textarea name=\"string-$key\" cols=\"$cols\" rows=\"$rows\">$value</textarea>\n";
                } else {
                    if ($valuelen) {
                        $cols = $valuelen + 2;
                    }
                    echo "<input type=\"text\" name=\"string-$key\" value=\"$value\" size=\"$cols\"></td>";
                }
                echo "</TD>\n";

            } else {
                echo "<td width=40% bgcolor=\"$cellcolour\" valign=top>$value</td>\n";
            }
        }
        if ($editable) {
            echo "<tr><td colspan=2>&nbsp;<td><br />";
            echo "    <input type=\"hidden\" name=\"currentfile\" value=\"$currentfile\">";
            echo "    <input type=\"hidden\" name=\"mode\" value=\"compare\">";
            echo "    <input type=\"submit\" name=\"update\" value=\"".get_string("savechanges").": $currentfile\">";
            echo "</td></tr>";
        }
        echo "</table>";
        echo "</form>";

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

    global $CFG, $USER;

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
        $value = str_replace("'", "\\'", $value);           // Add slashes for '
        $value = str_replace('"', "\\\"", $value);          // Add slashes for "
        $value = str_replace("%","%%",$value);              // Escape % characters
        $value = str_replace("\r", "",$value);              // Remove linefeed characters
        if ($id == "string" and $value != ""){
            fwrite($f,"\$string['$stringname'] = '$value';\n");
        }
    }
    fwrite($f,"\n?>\n");
    fclose($f);

    return true;
}

?>
