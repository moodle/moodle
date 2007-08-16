<?PHP // $Id$
    /**
    * Display the admin/language menu and process strings translation.
    */

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('langedit');

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    define('LANG_SUBMIT_REPEAT', 1);            // repeat displaying submit button?
    define('LANG_SUBMIT_REPEAT_EVERY', 20);     // if so, after how many lines?
    define('LANG_DISPLAY_MISSING_LINKS', 1);    // display "go to first/next missing string" links?
    define('LANG_DEFAULT_FILE', '');            // default file to translate. Empty allowed
    define('LANG_LINK_MISSING_STRINGS', 1);     // create links from "missing" page to "compare" page?
    define('LANG_DEFAULT_USELOCAL', 0);         // should *_utf8_local be used by default?
    define('LANG_MISSING_TEXT_MAX_LEN', 60);    // maximum length of the missing text to display
    define('LANG_KEEP_ORPHANS', 1);             // keep orphaned strings (i.e. strings w/o English reference)

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

    if (!has_capability('moodle/site:langeditmaster', $context, $USER->id, false)) {
        // Force using _local
        $uselocal = 1;
    }

    if (!has_capability('moodle/site:langeditmaster', $context, $USER->id, false) && (!$uselocal)) {
        print_error('cannoteditmasterlang');
    }

    if ((!has_capability('moodle/site:langeditlocal', $context, $USER->id, false)) && ($uselocal)) {
        print_error('cannotcustomizelocallang');
    }

    $strlanguage = get_string("language");
    $strcurrentlanguage = get_string("currentlanguage");
    $strmissingstrings = get_string("missingstrings");
    $streditstrings = get_string("editstrings", 'admin');
    $stredithelpdocs = get_string("edithelpdocs", 'admin');
    $strthislanguage = get_string("thislanguage");
    $strgotofirst = get_string('gotofirst','admin');
    $strfilestoredin = get_string('filestoredin', 'admin');
    $strfilestoredinhelp = get_string('filestoredinhelp', 'admin');
    $strswitchlang = get_string('switchlang', 'admin');
    $strchoosefiletoedit = get_string('choosefiletoedit', 'admin');
    $streditennotallowed = get_string('langnoeditenglish', 'admin');
    $strfilecreated = get_string('filecreated', 'admin');
    $strprev = get_string('previous');
    $strnext = get_string('next');
    $strlocalstringcustomization = get_string('localstringcustomization', 'admin');
    $strlangpackmaintaining = get_string('langpackmaintaining', 'admin');
    $strnomissingstrings = get_string('nomissingstrings', 'admin');

    $currentlang = current_language();

    switch ($mode) {
        case "missing":
            // Missing array keys are not bugs here but missing strings
            error_reporting(E_ALL ^ E_NOTICE);
            $title = $strmissingstrings;
            break;
        case "compare":
            $title = $streditstrings;
            break;
        default:
            $title = $strlanguage;
            break;
    }
    $crumbs[] = array('name' => $strlanguage, 'link' => "$CFG->wwwroot/admin/lang.php");
    $navigation = build_navigation($crumbs);

    admin_externalpage_print_header();

    // Prepare and render menu tabs
    $firstrow = array();
    $secondrow = array();
    $inactive = NULL;
    $activated = NULL;
    $currenttab = $mode;
    if ($uselocal) {
        $inactive = array('uselocal');
        $activated = array('uselocal');
    } else {
        $inactive = array('usemaster');
        $activated = array('usemaster');
    }
    if (has_capability('moodle/site:langeditlocal', $context, $USER->id, false)) {
        $firstrow[] = new tabobject('uselocal',
            $CFG->wwwroot."/admin/lang.php?mode=$mode&amp;currentfile=$currentfile&amp;uselocal=1",
            $strlocalstringcustomization );
    }
    if (has_capability('moodle/site:langeditmaster', $context, $USER->id, false)) {
        $firstrow[] = new tabobject('usemaster',
            $CFG->wwwroot."/admin/lang.php?mode=$mode&amp;currentfile=$currentfile&amp;uselocal=0",
            $strlangpackmaintaining );
    }
    $secondrow[] = new tabobject('missing', $CFG->wwwroot.'/admin/lang.php?mode=missing', $strmissingstrings );
    $secondrow[] = new tabobject('compare', $CFG->wwwroot.'/admin/lang.php?mode=compare', $streditstrings );
    // TODO
    // langdoc.php functionality is planned to be merged into lang.php
    $secondrow[] = new tabobject('langdoc', $CFG->wwwroot.'/admin/langdoc.php', $stredithelpdocs );
    $tabs = array($firstrow, $secondrow);
    print_tabs($tabs, $currenttab, $inactive, $activated);


    if (!$mode) {
        print_box_start();
        $currlang = current_language();
        $langs = get_list_of_languages(false, true);
        popup_form ("$CFG->wwwroot/$CFG->admin/lang.php?lang=", $langs, "chooselang", $currlang, "", "", "", false, 'self', $strcurrentlanguage.':');
        print_box_end();
        admin_externalpage_print_footer();
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
                $o .= '<div class="notifyproblem">'.get_string("filemissing", "", "$langdir/$file").'</div><br />';
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
                    if (strlen($value) > LANG_MISSING_TEXT_MAX_LEN) {
                        $value = lang_xhtml_save_substr($value, 0, LANG_MISSING_TEXT_MAX_LEN) . ' ...'; // MDL-8852
                    }
                    $o .= "$"."string['".$missinglinkstart.$key.$missinglinkend."'] = \"$value\";<br />";
                }
            }
            if (!$first) {
                $o .= '</pre><hr />';
            }
        }

        if ($m <> '') {
            print_box($m, 'filenames');
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
            notice(get_string("languagegood"), "lang.php" );
        }

    } else if ($mode == "compare") {

        if (!file_exists($langbase) ){
            if (!lang_make_directory($langbase) ){
                error('ERROR: Could not create base lang directory ' . $langbase);
            } else {
                echo '<div class="notifysuccess">Created directory '.
                                                     $langbase .'</div>'."<br />\n";
            }
        }
        if (!$uselocal && !file_exists($langdir)) {
            if (!lang_make_directory($langdir)) {
                error('ERROR: Could not create directory '.$langdir);
            } else {
                echo '<div class="notifysuccess">Created directory '.
                                                     $langdir .'</div>'."<br />\n";
            }
        }
        if ($uselocal && !file_exists($locallangdir)) {
            if (!lang_make_directory($locallangdir)) {
                echo '<div class="notifyproblem">ERROR: Could not create directory '.
                                     $locallangdir .'</div>'."<br />\n";
                $uselocal = 0;
            } else {
                echo '<div class="notifysuccess">Created directory '.
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

        print_box_start('generalbox editstrings');
        $menufiles = array();
        foreach ($stringfiles as $file) {
            $menufiles[$file] = $file;
        }
        popup_form("$CFG->wwwroot/$CFG->admin/lang.php?mode=compare&amp;currentfile=", $menufiles, "choosefile",
            $currentfile, $strchoosefiletoedit);

        echo '<div class="filestorageinfobox">';
        echo $strfilestoredin;
        echo '<code class="path">';
        echo $uselocal ? "{$currentlang}_local" : $currentlang;
        echo '</code>';
        helpbutton('langswitchstorage', $strfilestoredinhelp, 'moodle');
        echo '</div>';
        print_box_end();

        if ($currentfile <> '') {
            $saveto = $uselocal ? $locallangdir : $langdir;
            error_reporting(0);
            if (!file_exists("$saveto/$currentfile")) {
                if (!@touch("$saveto/$currentfile")) {
                    print_heading(get_string("filemissing", "", "$saveto/$currentfile"), '', 4, 'error');
                } else {
                    print_heading($strfilecreated, '', 4, 'notifysuccess');
                }
            }
            if ($currentlang == "en_utf8" && !$uselocal) {
                $editable = false;
                print_heading($streditennotallowed, '', 4);
            } elseif ($f = fopen("$saveto/$currentfile","r+")) {
                $editable = true;
                fclose($f);
            } else {
                $editable = false;
                echo "<p><font size=\"1\">".get_string("makeeditable", "", "$saveto/$currentfile")."</font></p>";
            }
            error_reporting($CFG->debug);

            $o = '';    // stores the HTML output to be echo-ed
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
                $o .= "<form id=\"$currentfile\" action=\"lang.php\" method=\"post\">";
                $o .= '<div>';
            }
            $o .= "<table summary=\"\" width=\"100%\" class=\"translator\">";
            $linescounter = 0;
            $missingcounter = 0;
            foreach ($enstring as $key => $envalue) {
                $linescounter++ ;
                if (LANG_SUBMIT_REPEAT &&  $editable && $linescounter % LANG_SUBMIT_REPEAT_EVERY == 0) {
                    $o .= '<tr><td>&nbsp;</td><td><br />';
                    $o .= '<input type="submit" name="update" value="'.get_string('savechanges').': '.$currentfile.'" />';
                    $o .= '<br />&nbsp;</td></tr>';
                }
                $envalue = nl2br(htmlspecialchars($envalue));
                $envalue = preg_replace('/(\$a\-\&gt;[a-zA-Z0-9]*|\$a)/', '<b>$0</b>', $envalue);  // Make variables bold.
                $envalue = str_replace("%%","%",$envalue);
                $envalue = str_replace("\\","",$envalue);              // Delete all slashes

                $o .= "\n\n".'<tr class="';
                if ($linescounter % 2 == 0) {
                    $o .= 'r0';
                } else {
                    $o .= 'r1';
                }
                $o .= '">';
                $o .= '<td dir="ltr" lang="en">';
                $o .= '<span class="stren">'.$envalue.'</span>';
                $o .= '<br />'."\n";
                $o .= '<span class="strkey">'.$key.'</span>';
                $o .= '</td>'."\n";

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
                        $cellcolour = 'class="bothmissing"';
                    } else {
                        $cellcolour = 'class="mastermissing"';
                    }
                    $missingcounter++;
                    if (LANG_DISPLAY_MISSING_LINKS) {
                        $missingtarget = '<a name="missing'.$missingcounter.'"></a>';
                        $missingnext = '<a href="#missing'.($missingcounter+1).'">'.
                        '<img src="' . $CFG->pixpath . '/t/down.gif" class="iconsmall" alt="'.$strnext.'" /></a>';
                        $missingprev = '<a href="#missing'.($missingcounter-1).'">'.
                        '<img src="' . $CFG->pixpath . '/t/up.gif" class="iconsmall" alt="'.$strprev.'" /></a>';
                    } else {
                        $missingtarget = '';
                        $missingnext = '';
                        $missingprev = '';
                    }
                } else {
                    if ($value <> $value2 && $value2 <> '') {
                        $cellcolour = 'class="localdifferent"';
                    } else {
                        $cellcolour = '';
                    }
                    $missingtarget = '';
                    $missingnext = '';
                    $missingprev = '';
                }

                if ($editable) {
                    $o .= '<td '.$cellcolour.' valign="top">'. $missingprev . $missingtarget."\n";
                    if (isset($string[$key])) {
                        $valuelen = strlen($value);
                    } else {
                        $valuelen = strlen($envalue);
                    }
                    $cols=40;
                    if (strstr($value, "\r") or strstr($value, "\n") or $valuelen > $cols) {
                        $rows = ceil($valuelen / $cols);
                        $o .= '<textarea name="stringXXX'.lang_form_string_key($key).'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>'."\n";
                    } else {
                        if ($valuelen) {
                            $cols = $valuelen + 5;
                        }
                        $o .= '<input type="text" name="stringXXX'.lang_form_string_key($key).'" value="'.$value.'" size="'.$cols.'" />';
                    }
                    if ($value2 <> '' && $value <> $value2) {
                        $o .= '<br /><span style="font-size:small">'.$value2.'</span>';
                    }
                    $o .= $missingnext . '</td>';

                } else {
                    $o .= '<td '.$cellcolour.' valign="top">'.$value.'</td>';
                }
                $o .= '</tr>'."\n";
            }
            if ($editable) {
                $o .= '<tr><td>&nbsp;</td><td><br />';
                $o .= '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
                $o .= '<input type="hidden" name="currentfile" value="'.$currentfile.'" />';
                $o .= '<input type="hidden" name="mode" value="compare" />';
                $o .= '<input type="submit" name="update" value="'.get_string('savechanges').': '.$currentfile.'" />';
                $o .= '</td></tr>';
            }
            $o .= '</table>';
            if ($editable) {
                $o .= '</div>';
                $o .= '</form>';
            }

            if (LANG_DISPLAY_MISSING_LINKS) {
                if ($missingcounter > 0) {
                    print_heading(get_string('numberofmissingstrings', 'admin', $missingcounter), '', 4);
                    if ($editable) {
                        print_heading('<a href="#missing1">'.$strgotofirst.'</a>', "", 4);
                    }
                } else {
                    print_heading($strnomissingstrings, '', 4, 'notifysuccess');
                }
            }
            echo $o;

        } else {
            // no $currentfile specified
            // no useful information to display - maybe some help? instructions?
        }
    }

    admin_externalpage_print_footer();

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
    if (LANG_KEEP_ORPHANS) {
        // let us load the current content of the file
        unset($string);
        @include("$path/$file");
        if (isset($string)) {
            $orphans = $string;
            unset($string);
        } else {
            $orphans = array();
        }
    }
    // let us rewrite the file
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
        $value = lang_fix_value_before_save($value);
        if ($id == "string" and $value != ""){
            if ((!$local) || (lang_fix_value_from_file($packstrings[$stringname]) <> lang_fix_value_from_file($value))) {
                // Either we are saving the master language pack
                // or we are saving local language pack and the strings differ.
                fwrite($f,"\$string['$stringname'] = '$value';\n");
            }
            if (LANG_KEEP_ORPHANS && isset($orphans[$stringname])) {
                unset($orphans[$stringname]);
            }
        }
    }
    if (LANG_KEEP_ORPHANS) {
        // let us add orphaned strings, i.e. already translated strings without the English referential source
        foreach ($orphans as $key => $value) {
            fwrite($f,"\$string['$key'] = '".lang_fix_value_before_save($value)."'; // ORPHANED\n");
        }
    }
    fwrite($f,"\n?>\n");
    fclose($f);
    return true;
}

/**
 * Fix value of the translated string after it is load from the file.
 *
 * These modifications are typically necessary to work with the same string coming from two sources.
 * We need to compare the content of these sources and we want to have e.g. "This string\r\n"
 * to be the same as " This string\n".
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
    $value = str_replace("&","&amp;",$value);          // Fixes MDL-9248
    $value = str_replace("<","&lt;",$value);
    $value = str_replace(">","&gt;",$value);
    $value = str_replace('"',"&quot;",$value);
    return $value;
}

/**
 * Fix value of the translated string before it is saved into the file
 *
 * @uses $CFG
 * @param string $value Raw string to be saved into the lang pack
 * @return string Fixed value
 */
function lang_fix_value_before_save($value='') {
    global $CFG;
    if ($CFG->lang != "zh_hk" and $CFG->lang != "zh_tw") {  // Some MB languages include backslash bytes
        $value = str_replace("\\","",$value);           // Delete all slashes
    }
    if (ini_get_bool('magic_quotes_sybase')) {          // Unescape escaped sybase quotes
        $value = str_replace("''", "'", $value);
    }
    $value = str_replace("'", "\\'", $value);           // Add slashes for '
    $value = str_replace('"', "\\\"", $value);          // Add slashes for "
    $value = str_replace("%","%%",$value);              // Escape % characters
    $value = str_replace("\r", "",$value);              // Remove linefeed characters
    $value = trim($value);                              // Delete leading/trailing white space
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

/**
 * Return the string key name for use in HTML form.
 *
 * Required because '.' in form input names get replaced by '_' by PHP.
 *
 * @param string $keyfromfile The key name containing '.'
 * @return string The key name without '.'
 */
function lang_form_string_key($keyfromfile) {
    return str_replace('.', '##46#', $keyfromfile);  /// Derived from &#46, the ascii value for a period.
}

/**
 * Return the string key name for use in file.
 *
 * Required because '.' in form input names get replaced by '_' by PHP.
 *
 * @param string $keyfromfile The key name without '.'
 * @return string The key name containing '.'
 */
function lang_file_string_key($keyfromform) {
    return str_replace('##46#', '.', $keyfromform);
}

/**
 * Return the substring of the string and take care of XHTML compliance.
 *
 * There was a problem with pure substr() which could possibly produce XHTML parsing error:
 *  substr('Marks &amp; Spencer', 0, 9) -> 'Marks &am' ... is not XHTML compliance
 * This function takes care of these cases. Fixes MDL-8852.
 *
 * Thanks to kovacsendre, the author of the function at http://php.net/substr
 *
 * @param string $str The original string
 * @param int $start Start position in the $value string
 * @param int $length Optional length of the returned substring
 * @return string The substring as returned by substr() with XHTML compliance
 * @todo Seems the function does not work with negative $start together with $length being set
 */
function lang_xhtml_save_substr($str, $start, $length = NULL) {
    if ($length === 0) {
        //stop wasting our time ;)
        return "";
    }

    //check if we can simply use the built-in functions
    if (strpos($str, '&') === false) {
        // No entities. Use built-in functions
        if ($length === NULL) {
            return substr($str, $start);
        } else {
            return substr($str, $start, $length);
        }
    }

    // create our array of characters and html entities
    $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
    $html_length = count($chars);

    // check if we can predict the return value and save some processing time, i.e.:
    // input string was empty OR
    // $start is longer than the input string OR
    // all characters would be omitted
    if (($html_length === 0) or ($start >= $html_length) or (isset($length) and ($length <= -$html_length))) {
        return '';
    }

    //calculate start position
    if ($start >= 0) {
        $real_start = $chars[$start][1];
    } else {
        //start'th character from the end of string
        $start = max($start,-$html_length);
        $real_start = $chars[$html_length+$start][1];
    }

    if (!isset($length)) {
        // no $length argument passed, return all remaining characters
        return substr($str, $real_start);
    } elseif ($length > 0) {
        // copy $length chars
        if ($start+$length >= $html_length) {
            // return all remaining characters
            return substr($str, $real_start);
        } else {
            //return $length characters
            return substr($str, $real_start, $chars[max($start,0)+$length][1] - $real_start);
        }
    } else {
        //negative $length. Omit $length characters from end
        return substr($str, $real_start, $chars[$html_length+$length][1] - $real_start);
    }
}

/**
* Finds all English string files in the standard lang/en_utf8 location.
*
* The English version of the file may be found in
*  $CFG->dirroot/lang/en_utf8/filename
* The localised version of the found file should be saved into
*  $CFG->dataroot/lang/current_lang[_local]/filename
* where "filename" is returned as a part of the file record.
*
* @return array Array of a file information. Compatible format with {@link lang_extra_locations()}
*/
function lang_standard_locations() {
    global $CFG;
    $files = array();
    // Standard location of master English string files.
    $places = array($CFG->dirroot.'/lang/en_utf8');
        foreach ($places as $place) {
            foreach (get_directory_list($place, '', false) as $file) {
                if ((substr($file, -4) == ".php") && ($file != "langconfig.php")) {
                    $fullpath = $place.'/'.$file;
                    $files[$fullpath] = array(
                        'filename' => $file,
                        'location' => '',
                        'plugin' => '',
                        'prefix' => '',
                    );
                }
            }
        }
    return $files;
}

/**
* Finds all English string files in non-standard location.
*
* Searches for lang/en_utf8/*.php in various types of plugins (blocks, database presets, question types,
* 3rd party modules etc.) and returns an array of found files details.
*
* The English version of the file may be found in
*  $CFG->dirroot/location/plugin/lang/en_utf8/filename
* The localised version of the found file should be saved into
*  $CFG->dataroot/lang/current_lang[_local]/prefix_plugin.php
* where "location", "plugin", "prefix" and "filename" are returned as a part of the file record.
*
* @return array Array of a file information. Compatible format with {@link lang_standard_locations()}
*/
function lang_extra_locations() {
    global $CFG;
    $files = array();
    $places = places_to_search_for_lang_strings();
    foreach ($places as $prefix => $directories) {
        if ($prefix != '__exceptions') {
            foreach ($directories as $directory) {
                foreach (get_list_of_plugins($directory) as $plugin) {
                    $enlangdirlocation = $CFG->dirroot.'/'.$directory.'/'.$plugin.'/lang/en_utf8';
                    foreach (get_directory_list($enlangdirlocation, '', false) as $file) {
                        if ((substr($file, -4) == ".php") && ($file != "langconfig.php")) {
                            $fullpath = $enlangdirlocation.'/'.$file;
                            $files[$fullpath] = array(
                                'filename' => $file,
                                'location' => $directory,
                                'plugin' => $plugin,
                                'prefix' => $prefix,
                            );
                        }
                    }
                }
            }
        }
    }
    return $files;
}


?>
