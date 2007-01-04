<?PHP // $Id$
    /**
    * Display the admin/language menu and process strings translation.
    *
    * CHANGES
    *
    * 2006/11/07 mudrd8mz
    * Fixed bug MDL-7361. Thanks to Dan Poltawski for the patch proposal.
    *
    * 2006/06/08 mudrd8mz
    * 1) Fixed bug 5745 reported by Harry Smith so now can edit en_utf8_local pack
    * 2) More notification messages included
    *
    * 2006/05/30 mudrd8mz
    * Merged with version by Mitsuhiro Yoshida - display icon links instead of text links
    * (I have changed the position of arrow icons to not to be so close each other)
    *
    * 2006/05/19 mudrd8mz
    * A lot of changes to support translation of *_utf8_local langugage packs. Needs testing
    *
    * 2006/05/16 mudrd8mz
    * 1) LANG_DEFAULT_FILE can be now set. moodle.php used to be opened automatically.
    *    As it was (and still is) one of the biggest files it usually took a long time to load the page
    *    even if you just want to choose the file to translate.
    * 2) added links from "missing" to "compare" mode page
    * 3) english strings are now key-sorted in "missing" mode
    * 4) list of files with missing strings is now displayed at the top of "missing" page
    *
    * 2006/05/14 mudrd8mz Improvements of lang.php,v 1.65 2006/04/10 22:15:57 stronk7 Exp 
    *  1) "go to first missing string" link can be displayed (see LANG_DISPLAY_MISSING_LINKS)
    *  2) "go to next missing" link can be displayed (see LANG_DISPLAY_MISSING_LINKS)
    *  3) submit button may be repeated (see LANG_SUBMIT_REPEAT*)
    *  4) added (empty) "summary" attribute for the <table>'s
    *  5) added error_reporting(E_ALL ^ E_NOTICE); in "compare" mode (even in debug environment
    *     we know that missing keys are missing strings here, not bugs ;-)
    */

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    $adminroot = admin_get_root();
    admin_externalpage_setup('langedit', $adminroot);    

    define('LANG_SUBMIT_REPEAT', 1);            // repeat displaying submit button?
    define('LANG_SUBMIT_REPEAT_EVERY', 20);     // if so, after how many lines?
    define('LANG_DISPLAY_MISSING_LINKS', 1);    // display "go to first/next missing string" links?
    define('LANG_DEFAULT_FILE', '');            // default file to translate. Empty allowed
    define('LANG_LINK_MISSING_STRINGS', 1);     // create links from "missing" page to "compare" page?
    define('LANG_DEFAULT_USELOCAL', 0);         // should *_utf8_local be used by default?

    $mode        = optional_param('mode', '', PARAM_ALPHA);
    $currentfile = optional_param('currentfile', LANG_DEFAULT_FILE, PARAM_FILE);
    $uselocal    = optional_param('uselocal', -1, PARAM_INT);

    if ($uselocal == -1) {
        if (isset($SESSION->langtranslateintolocal)) {
            $uselocal = $SESSION->langtranslateintolocal;
        } else {
            $uselocal = LANG_DEFAULT_USELOCAL;
        }
    } else {
        $SESSION->langtranslateintolocal = $uselocal;
    }

    $strlanguage = get_string("language");
    $strcurrentlanguage = get_string("currentlanguage");
    $strmissingstrings = get_string("missingstrings");
    $streditstrings = get_string("editstrings", 'admin');
    $stredithelpdocs = get_string("edithelpdocs", 'admin');
    $strthislanguage = get_string("thislanguage");
    $strgotofirst = get_string('gotofirstmissing','admin');
    $strfilestoredin = get_string('langstorein', 'admin');
    $strfilestoredinhelp = get_string('langstoreinhelp', 'admin');
    $strswitchlangdirbtn = get_string('langstoreswitch', 'admin');
    $strchoosefiletoedit = get_string('langchoosefile', 'admin');
    $streditennotallowed = get_string('langnoeditenglish', 'admin');
    $strfilecreated = get_string('langfilecreated', 'admin');
    $strprev = get_string('previous');
    $strnext = get_string('next');

    // FIXME / TODO
    // remove following lines after adding string into proper english lang pack
    $strgotofirst = 'go to first missing string';
    $strfilestoredin = 'Save file into folder :';
    $strfilestoredinhelp = 'Where the file will be stored';
    $strswitchlangdirbtn = 'switch';
    $strchoosefiletoedit = 'Choose file to edit from the box above';
    $streditennotallowed = 'Language en_utf8 cannot be edited with this page - switch to local';
    $strfilecreated = 'New file created';

    $currentlang = current_language();

    switch ($mode) {
        case "missing":
            // Missing array keys are not bugs here but missing strings
            error_reporting(E_ALL ^ E_NOTICE);
            $navigation = "<a href=\"lang.php\">$strlanguage</a> -> $strmissingstrings";
            $title = $strmissingstrings;
            $button = '<form '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/'.$CFG->admin.'/lang.php">'.
                      '<input type="hidden" name="mode" value="compare" />'.
                      '<input type="submit" value="'.$streditstrings.'" /></form>';
            break;
        case "compare":
            $navigation = "<a href=\"lang.php\">$strlanguage</a> -> $streditstrings";
            $title = $streditstrings;
            $button = '<form  '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/'.$CFG->admin.'/lang.php">'.
                      '<input type="hidden" name="mode" value="missing" />'.
                      '<input type="submit" value="'.$strmissingstrings.'" /></form>';
            break;
        default:
            $title = $strlanguage;
            $navigation = $strlanguage;
            $button = '';
            break;
    }


    admin_externalpage_print_header($adminroot);

    if (!$mode) {
        print_simple_box_start('center','80%');
        echo '<div align="center">';
        print_string('editlang','admin');
        echo '<br />';
        $currlang = current_language();
        $langs = get_list_of_languages();
        echo "<b>$strcurrentlanguage:</b>";
        echo '<br />';
        echo popup_form ("$CFG->wwwroot/$CFG->admin/lang.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
        $options["lang"] = $currentlang;
        print_heading("<a href=\"lang.php?mode=missing\">$strmissingstrings</a>");
        print_heading("<a href=\"lang.php?mode=compare\">$streditstrings</a>");
        print_heading("<a href=\"langdoc.php\">$stredithelpdocs</a>");
        echo '</div>';
        print_simple_box_end();
        admin_externalpage_print_footer($adminroot);
        exit;
    }

    // Get a list of all the root files in the English directory

    $langbase = $CFG->dataroot . '/lang';
    $enlangdir = "$CFG->dirroot/lang/en_utf8";
    if ($currentlang == 'en_utf8') {
        $langdir = $enlangdir;
    } else {
        $langdir = "$langbase/$currentlang";
    }
    $locallangdir = "$langbase/{$currentlang}_local";

    if (! $stringfiles = get_directory_list($enlangdir, "", false)) {
        error("Could not find English language pack!");
    }

    foreach ($stringfiles as $key => $file) {
        if (substr($file, -4) != ".php") { //Avoid non php files to be showed
            unset($stringfiles[$key]);
        }
        if ($file == "langconfig.php") { //Avoid langconfig.php to be showed
            unset($stringfiles[$key]);
        }
    }

    if ($mode == "missing") {
        if (!file_exists($langdir)) {
            error ('to edit this language pack, you need to put it in '.$CFG->dataroot.'/lang');
        }

        // Following variables store the HTML output to be echo-ed
        $m = '';
        $o = '';

        // For each file, check that a counterpart exists, then check all the strings
        foreach ($stringfiles as $file) {
            unset($string);
            include("$enlangdir/$file");
            $enstring = $string;  

            ksort($enstring);
            
            unset($string);

            if (file_exists("$langdir/$file")) {
                include("$langdir/$file");
                $fileismissing = 0;
            } else {
                $fileismissing = 1;
                // notify(get_string("filemissing", "", "$langdir/$file"));
                $o .= '<div class="notifyproblem" align="center">'.get_string("filemissing", "", "$langdir/$file").'</div><br />';
                $string = array();
            }

            $missingcounter = 0;
    
            $first = true;
            foreach ($enstring as $key => $value) {
                if (empty($string[$key]) and $string[$key] != "0") {    //bug fix 4735 mits
                    $value = htmlspecialchars($value);
                    $value = str_replace("$"."a", "\\$"."a", $value);
                    $value = str_replace("%%","%",$value);
                    if ($first) {
                        $m .= "<a href=\"lang.php?mode=missing#$file\">$file";
                        $m .= $fileismissing ? '*' : '';
                        $m .= '</a> &nbsp; ';
                        $o .= "<p><a name=\"$file\"></a><b>".get_string("stringsnotset","","$langdir/$file")."</b></p><pre>";
                        $first = false;
                        $somethingfound = true;
                    }
                    $missingcounter++;
                    if (LANG_LINK_MISSING_STRINGS) {
                        $missinglinkstart = "<a href=\"lang.php?mode=compare&amp;currentfile=$file#missing$missingcounter\">";
                        $missinglinkend = '</a>';
                    } else {
                        $missinglinkstart = '';
                        $missinglinkend = '';
                    }
                    $o .= "$"."string['".$missinglinkstart.$key.$missinglinkend."'] = \"$value\";<br />";
                }
            }
            if (!$first) {
                $o .= '</pre><hr />';
            }
        }
  
        if ($m <> '') {
            echo '<br />';
            print_simple_box_start("center", "80%");
            echo '<center><font size="2">';
            echo $m;
            echo '</font></center>';
            print_simple_box_end();
        }
        echo $o;

        if (! $files = get_directory_list("$CFG->dirroot/lang/en_utf8/help", "CVS")) {
            error("Could not find English language help files!");
        }
    
        foreach ($files as $filekey => $file) {    // check all the help files.
            if (!file_exists("$langdir/help/$file")) {
                echo "<p><font color=\"red\">".get_string("filemissing", "", "$langdir/help/$file")."</font></p>";
                $somethingfound = true;
                continue;
            }
        }
    
        if (! $files = get_directory_list("$CFG->dirroot/lang/en_utf8/docs", "CVS")) {
            error("Could not find English language docs files!");
        }
        foreach ($files as $filekey => $file) {    // check all the docs files.
            if (!file_exists("$langdir/docs/$file")) {
                echo "<p><font color=\"red\">".get_string("filemissing", "", "$langdir/docs/$file")."</font></p>";
                $somethingfound = true;
                continue;
            }
        }
    
        if (!empty($somethingfound)) {
            print_continue("lang.php");
        } else {
            notice(get_string("languagegood"), "lang.php", '', $adminroot);
        }

    } else if ($mode == "compare") {
   
        if (!file_exists($langbase) ){
            if (!lang_make_directory($langbase) ){
                error('ERROR: Could not create base lang directory ' . $langbase);
            } else {
                echo '<div class="notifysuccess" align="center">Created directory '.
                                                     $langbase .'</div>'."<br />\n";
            }
        } 
        if (!$uselocal && !file_exists($langdir)) {
            if (!lang_make_directory($langdir)) {
                error('ERROR: Could not create directory '.$langdir);
            } else {
                echo '<div class="notifysuccess" align="center">Created directory '.
                                                     $langdir .'</div>'."<br />\n";
            }
        }
        if ($uselocal && !file_exists($locallangdir)) {
            if (!lang_make_directory($locallangdir)) {
                echo '<div class="notifyproblem" align="center">ERROR: Could not create directory '.
                                     $locallangdir .'</div>'."<br />\n";
                $uselocal = 0;
            } else {
                echo '<div class="notifysuccess" align="center">Created directory '.
                                                     $locallangdir .'</div>'."<br />\n";
            }
        }
        
        if (isset($_POST['currentfile'])){   // Save a file
            if (!confirm_sesskey()) {
                error(get_string('confirmsesskeybad', 'error'));
            }
            
            $newstrings = array();
            
            foreach ($_POST as $postkey => $postval) {
                $stringkey = lang_file_string_key($postkey);
                $newstrings[$stringkey] = $postval;
            }
            
            unset($newstrings['currentfile']);

            if ($uselocal) {
                include("$langdir/$currentfile");
                if (isset($string)) {
                    $packstring = $string;
                } else {
                    $packstring = array();
                }
                unset($string);
                $saveinto = $locallangdir;
            } else {
                $packstring = array();  
                $saveinto = $langdir;
            }
            
            if (lang_save_file($saveinto, $currentfile, $newstrings, $uselocal, $packstring)) {
                notify(get_string("changessaved")." ($saveinto/$currentfile)", "green");
            } else {
                error("Could not save the file '$saveinto/$currentfile'!", "lang.php?mode=compare&amp;currentfile=$currentfile");
            }
            unset($packstring);
        } 

        print_heading_with_help($streditstrings, "langedit");

        print_simple_box_start("center", "80%");
        echo '<center><font size="2">';
        foreach ($stringfiles as $file) {
            if ($file == $currentfile) {
                echo "<b>$file</b> &nbsp; ";
            } else {
                echo "<a href=\"lang.php?mode=compare&amp;currentfile=$file\">$file</a> &nbsp; ";
            }
        }
        echo '</font></center>';
        print_simple_box_end();


        print_simple_box_start("center", "80%");
        echo '<center>'.$strfilestoredin.' <strong>';
        echo $uselocal ? "{$currentlang}_local" : $currentlang;
        echo '</strong> ';
        helpbutton('langswitchstorage', $strfilestoredinhelp, 'moodle');
        
        echo '<form '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/'.$CFG->admin.'/lang.php">'.
             '<input type="hidden" name="mode" value="compare" />'.
             '<input type="hidden" name="currentfile" value="'.$currentfile.'" />'.
             '<input type="hidden" name="uselocal" value="'.(1 - $uselocal % 2).'" />'.
             '<input type="submit" value="'.$strswitchlangdirbtn.'" />'.
             '</form></center>';
        print_simple_box_end();
       
        if ($currentfile <> '') {
            $saveto = $uselocal ? $locallangdir : $langdir;
            error_reporting(0);
            if (!file_exists("$saveto/$currentfile")) {
                if (!@touch("$saveto/$currentfile")) {
                    print_heading(get_string("filemissing", "", "$saveto/$currentfile"), "center", 4, "error");
                } else {
                    print_heading($strfilecreated, "center", 4, "notifysuccess");
                }
            }
            if ($currentlang == "en_utf8" && !$uselocal) {
                $editable = false;
                print_heading($streditennotallowed, 'center', 4);
            } elseif ($f = fopen("$saveto/$currentfile","r+")) {
                $editable = true;
                fclose($f);
            } else {
                $editable = false;
                echo "<p><font size=\"1\">".get_string("makeeditable", "", "$saveto/$currentfile")."</font></p>";
            }
            error_reporting($CFG->debug);
            
            print_heading("$currentfile", "center", 4);
            if (LANG_DISPLAY_MISSING_LINKS && $editable) {
                print_heading('<a href="#missing1">'.$strgotofirst.'</a>', "center", 4);
            }

            unset($string);
            include("$enlangdir/$currentfile");
            $enstring = $string;  
            if ($currentlang != 'en' and $currentfile == 'moodle.php') {
                $enstring['thislanguage'] = "<< TRANSLATORS: Specify the name of your language here.  If possible use Unicode Numeric Character References >>";
                $enstring['thischarset'] = "<< TRANSLATORS:  Charset encoding - always use utf-8 >>";
                $enstring['thisdirection'] = "<< TRANSLATORS: This string specifies the direction of your text, either left-to-right or right-to-left.  Insert either 'ltr' or 'rtl' here. >>";
                $enstring['parentlanguage'] = "<< TRANSLATORS: If your language has a Parent Language that Moodle should use when strings are missing from your language pack, then specify the code for it here.  If you leave this blank then English will be used.  Example: nl >>";
            }
            ksort($enstring);

            unset($string);

            @include("$locallangdir/$currentfile");
            $localstring = isset($string) ? $string : array();
            unset($string);
            
            @include("$langdir/$currentfile");

            if ($editable) {
                echo "<form name=\"$currentfile\" action=\"lang.php\" method=\"post\">";
            }
            echo "<table summary=\"\" width=\"100%\" cellpadding=\"2\" cellspacing=\"3\" border=\"0\" class=\"generalbox\">";
            $linescounter = 0;
            $missingcounter = 0;
            foreach ($enstring as $key => $envalue) {
                $linescounter++ ;
                if (LANG_SUBMIT_REPEAT &&  $editable && $linescounter % LANG_SUBMIT_REPEAT_EVERY == 0) {
                    echo '<tr><td colspan="2">&nbsp;<td><br />';
                    echo '    <input type="submit" name="update" value="'.get_string('savechanges').': '.$currentfile.'" />';
                    echo '<br />&nbsp;</td></tr>';
                }
                $envalue = nl2br(htmlspecialchars($envalue));
                $envalue = preg_replace('/(\$a\-\&gt;[a-zA-Z0-9]*|\$a)/', '<b>$0</b>', $envalue);  // Make variables bold. 
                $envalue = str_replace("%%","%",$envalue);
                $envalue = str_replace("\\","",$envalue);              // Delete all slashes

                echo "\n\n".'<tr>';
                echo '<td dir="ltr" lang="en" width="20%" nowrap="nowrap" valign="top">'.$key.'</td>'."\n";
                echo '<td dir="ltr" lang="en" width="40%" valign="top">'.$envalue.'</td>'."\n";

                // Missing array keys are not bugs here but missing strings
                error_reporting(E_ALL ^ E_NOTICE);
                if ($uselocal) {
                    $value = lang_fix_value_from_file($localstring[$key]);
                    $value2 = lang_fix_value_from_file($string[$key]);
                    if ($value == '') {
                        $value = $value2;
                    }
                } else {
                    $value = lang_fix_value_from_file($string[$key]);
                    $value2 = lang_fix_value_from_file($localstring[$key]);
                }
                error_reporting($CFG->debug);

                // Color highlighting:
                // red #ef6868 - translation missing in both system and local pack
                // yellow #feff7f - translation missing in system pack but is translated in local
                // green #AAFFAA - translation present in both system and local but is different
                if (!$value) {
                    if (!$value2) {
                        $cellcolour = 'style="background-color: #ef6868"'; // TODO: replace by CSS class
                    } else {
                        $cellcolour = 'style="background-color: #feff7f"'; // TODO: replace by CSS class
                    }
                    $missingcounter++;
                    if (LANG_DISPLAY_MISSING_LINKS) {
                        $missingtarget = '<a name="missing'.$missingcounter.'"></a>';
                        $missingnext = '<a href="#missing'.($missingcounter+1).'">'.
                        '<img src="' . $CFG->pixpath . '/t/down.gif" height="11" width="11" border="0" alt="'.$strnext.'" /></a>';
                        $missingprev = '<a href="#missing'.($missingcounter-1).'">'.
                        '<img src="' . $CFG->pixpath . '/t/up.gif" height="11" width="11" border="0" alt="'.$strprev.'" /></a>';
                    } else {
                        $missingtarget = '';
                        $missingnext = '';
                        $missingprev = '';
                    }
                } else {
                    if ($value <> $value2 && $value2 <> '') {
                        $cellcolour = 'style="background-color: #AAFFAA"'; // TODO: replace by CSS class
                    } else {
                        $cellcolour = '';
                    }
                    $missingtarget = '';
                    $missingnext = '';
                    $missingprev = '';
                }

                if ($editable) {
                    echo '<td width="40%" '.$cellcolour.' valign="top">'. $missingprev . $missingtarget."\n";
                    if (isset($string[$key])) {
                        $valuelen = strlen($value);
                    } else {
                        $valuelen = strlen($envalue);
                    }
                    $cols=50;
                    if (strstr($value, "\r") or strstr($value, "\n") or $valuelen > $cols) {
                        $rows = ceil($valuelen / $cols);
                        echo '<textarea name="stringXXX'.lang_form_string_key($key).'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>'."\n";
                    } else {
                        if ($valuelen) {
                            $cols = $valuelen + 5;
                        }
                        echo '<input type="text" name="stringXXX'.lang_form_string_key($key).'" value="'.$value.'" size="'.$cols.'" />';
                    }
                    if ($value2 <> '' && $value <> $value2) {
                        echo '<br /><span style="font-size:small">'.$value2.'</span>';
                    }
                    echo $missingnext . '</td>';

                } else {
                    echo '<td width="40%" bgcolor="'.$cellcolour.'" valign="top">'.$value.'</td>';
                }
            }
            if ($editable) {
                echo '<tr><td colspan="2">&nbsp;<td><br />';
                echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
                echo '    <input type="hidden" name="currentfile" value="'.$currentfile.'" />';
                echo '    <input type="hidden" name="mode" value="compare" />';
                echo '    <input type="submit" name="update" value="'.get_string('savechanges').': '.$currentfile.'" />';
                echo '</td></tr>';
            }
            echo '</tr></table>';
            echo '</form>'; 

        } else {
            // no $currentfile specified
            print_heading($strchoosefiletoedit, "center", 4);
        }
    }

    admin_externalpage_print_footer($adminroot);

//////////////////////////////////////////////////////////////////////

/**
 * Save language translation file.
 *
 * Thanks to Petri Asikainen for the original version of code 
 * used to save language files.
 *
 * @uses $CFG
 * @uses $USER
 * @param string $path Full pathname to the directory to use
 * @param string $file File to overwrite
 * @param array $strings Array of strings to write
 * @param bool $local Should *_local version be saved?
 * @param array $packstrings Array of default langpack strings (needed if $local)
 * @return bool Created successfully?
 */ 
function lang_save_file($path, $file, $strings, $local, $packstrings) {
    global $CFG, $USER;
    if (!$f = @fopen("$path/$file","w")) {
        return false;
    }

    fwrite($f, "<?PHP // \$Id\$ \n");
    fwrite($f, "      // $file - created with Moodle $CFG->release ($CFG->version)\n");
    if ($local) {
        fwrite($f, "      // local modifications from $CFG->wwwroot\n");
    }
    fwrite($f, "\n\n");
    ksort($strings);
    foreach ($strings as $key => $value) {
        @list($id, $stringname) = explode('XXX',$key);
        if ($CFG->lang != "zh_hk" and $CFG->lang != "zh_tw") {  // Some MB languages include backslash bytes
            $value = str_replace("\\","",$value);           // Delete all slashes
        }
        $value = str_replace("'", "\\'", $value);           // Add slashes for '
        $value = str_replace('"', "\\\"", $value);          // Add slashes for "
        $value = str_replace("%","%%",$value);              // Escape % characters
        $value = str_replace("\r", "",$value);              // Remove linefeed characters
        $value = trim($value);                              // Delete leading/trailing white space
        if ($id == "string" and $value != ""){
            if ((!$local) || (lang_fix_value_from_file($packstrings[$stringname]) <> lang_fix_value_from_file($value))) {
                fwrite($f,"\$string['$stringname'] = '$value';\n");
            }
        }
    }
    fwrite($f,"\n?>\n");
    fclose($f);
    return true;
}

/**
 * Fix value of string to translate.
 *
 * @param string $value Original string from the file
 * @return string Fixed value
 */
function lang_fix_value_from_file($value='') {
    $value = str_replace("\r","",$value);              // Bad character caused by Windows
    $value = preg_replace("/\n{3,}/", "\n\n", $value); // Collapse runs of blank lines
    $value = trim($value);                             // Delete leading/trailing white space
    $value = str_replace("\\","",$value);              // Delete all slashes
    $value = str_replace("%%","%",$value);
    $value = str_replace("<","&lt;",$value);
    $value = str_replace(">","&gt;",$value);
    $value = str_replace('"',"&quot;",$value);
    return $value;
}

/**
 * Try and create a new language directory.
 *
 * @uses $CFG
 * @param string $directory full path to the directory under $langbase
 * @return string|false Returns full path to directory if successful, false if not
 */
function lang_make_directory($dir, $shownotices=true) {
    global $CFG;
    umask(0000);
    if (! file_exists($dir)) {
        if (! @mkdir($dir, $CFG->directorypermissions)) {
            return false;
        }
        //@chmod($dir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
    }
    return $dir;
}



/// Following functions are required because '.' in form input names
/// get replaced by '_' by PHP.

function lang_form_string_key($keyfromfile) {
    return str_replace('.', '##46#', $keyfromfile);  /// Derived from &#46, the ascii value for a period.
}

function lang_file_string_key($keyfromform) {
    return str_replace('##46#', '.', $keyfromform);
}


?>
