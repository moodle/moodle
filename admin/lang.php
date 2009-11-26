<?PHP // $Id$
    /**
    * Display the admin/language menu and process strings translation.
    *
    * @param string $mode the mode of the script: null, "compare", "missing"
    * @param string $currentfile the filename of the English file to edit (if mode==compare)
    * @param bool $uselocal save translations into *_local pack?
    */

    $dbg = '';  // debug output;

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('langedit');

    $context = get_context_instance(CONTEXT_SYSTEM);

    define('LANG_SUBMIT_REPEAT', 1);            // repeat displaying submit button?
    define('LANG_SUBMIT_REPEAT_EVERY', 20);     // if so, after how many lines?
    define('LANG_DISPLAY_MISSING_LINKS', 1);    // display "go to first/next missing string" links?
    define('LANG_DEFAULT_FILE', '');            // default file to translate. Empty allowed
    define('LANG_DEFAULT_HELPFILE', '');        // default helpfile to translate. Empty allowed
    define('LANG_LINK_MISSING_STRINGS', 1);     // create links from "missing" page to "compare" page?
    define('LANG_DEFAULT_USELOCAL', 0);         // should *_utf8_local be used by default?
    define('LANG_MISSING_TEXT_MAX_LEN', 60);    // maximum length of the missing text to display
    define('LANG_KEEP_ORPHANS', 1);             // keep orphaned strings (i.e. strings w/o English reference)
    define('LANG_SEARCH_EXTRA', 1);             // search lang files in extra locations
    define('LANG_ALWAYS_TEXTAREA', 1);          // always use <textarea> even for short strings MDL-15738

    $mode = optional_param('mode', '', PARAM_ALPHA);
    if ($mode == 'helpfiles') {
        // use different PARAM_ options according to mode
        $currentfile = optional_param('currentfile', LANG_DEFAULT_HELPFILE, PARAM_PATH);
    } else {
        $currentfile = optional_param('currentfile', LANG_DEFAULT_FILE, PARAM_FILE);
    }
    $uselocal = optional_param('uselocal', -1, PARAM_INT);

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
    $streditingnoncorelangfile = get_string('editingnoncorelangfile', 'admin');
    $strlanglocalpackage = get_string('langlocalpackage', 'admin');
    $strlangmasterpackage = get_string('langmasterpackage', 'admin');
    $strlangmasterenglish = get_string('langmasterenglish', 'admin');

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
        case "helpfiles":
            $title = $stredithelpdocs;
        default:
            $title = $strlanguage;
            break;
    }
    $navlinks[] = array('name' => $strlanguage, 'link' => "$CFG->wwwroot/$CFG->admin/lang.php", 'type' => 'misc');
    $navigation = build_navigation($navlinks);

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
            "$CFG->wwwroot/$CFG->admin/lang.php?mode=$mode&amp;currentfile=$currentfile&amp;uselocal=1",
            $strlocalstringcustomization );
    }
    if (has_capability('moodle/site:langeditmaster', $context, $USER->id, false)) {
        $firstrow[] = new tabobject('usemaster',
            "$CFG->wwwroot/$CFG->admin/lang.php?mode=$mode&amp;currentfile=$currentfile&amp;uselocal=0",
            $strlangpackmaintaining );
    }
    $secondrow[] = new tabobject('missing', "$CFG->wwwroot/$CFG->admin/lang.php?mode=missing", $strmissingstrings );
    $secondrow[] = new tabobject('compare', "$CFG->wwwroot/$CFG->admin/lang.php?mode=compare", $streditstrings );
    $secondrow[] = new tabobject('helpfiles', "$CFG->wwwroot/$CFG->admin/lang.php?mode=helpfiles", $stredithelpdocs );
    $tabs = array($firstrow, $secondrow);
    print_tabs($tabs, $currenttab, $inactive, $activated);


    if (!$mode) {
        // TODO this is a very nice place to put some translation statistics
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
    $saveto = $uselocal ? $locallangdir : $langdir;

    if (($mode == 'missing') || ($mode == 'compare')) {
        // get the list of all English stringfiles
        $stringfiles = lang_standard_locations();
        if (LANG_SEARCH_EXTRA) {
            $stringfiles += lang_extra_locations();
        }
        if (count($stringfiles) == 0) {
            error("Could not find English language pack!");
        }
    } elseif ($mode == 'helpfiles') {
        $helpfiles = lang_help_standard_locations();
        if (LANG_SEARCH_EXTRA) {
            $helpfiles += lang_help_extra_locations();
        }
        if (count($helpfiles) == 0) {
            error("Could not find help files in the English language pack!");
        }
    }



    if ($mode == 'missing') {
        if (!file_exists($langdir)) {
            error ('to edit this language pack, you need to put it in '.$CFG->dataroot.'/lang');
        }

        // Following variables store the HTML output to be echo-ed
        $m = '';
        $o = '';

        $m_x = false;

        // Total number of strings and missing strings
        $totalcounter->strings = 0;
        $totalcounter->missing = 0;       

        // For each file, check that a counterpart exists, then check all the strings
        foreach ($stringfiles as $stringfile) {
            $location = $stringfile['location'];
            $plugin = $stringfile['plugin'];
            $prefix = $stringfile['prefix'];
            $filename = $stringfile['filename'];
            unset($string);
            
            // Get some information about file locations:
            //  $enfilepath = the path to the English file distributed either in the core space or in plugin space
            //  $trfilepath = the path to the translated file distributed either in the lang pack or in plugin space
            //  $lcfilepath = the path to the _local customization
            //  $trfilename = the filename of the translated version of the file (including prefix for non-core files)
            if ($location || $plugin) {
                // non-core file in an extra location
                $enfilepath = "$CFG->dirroot/$location/$plugin/lang/en_utf8/$filename";
                $trfilepath = "$CFG->dirroot/$location/$plugin/lang/$currentlang/$filename";
                $lcfilepath = "$locallangdir/$filename";
                $trfilename = $filename;
                if (!$m_x) {
                    $m .= '<hr />';
                    $m_x = true;
                }
            } else {
                // core file in standard location
                $enfilepath = "$CFG->dirroot/lang/en_utf8/$filename";
                $trfilepath = "$langdir/$filename";
                $lcfilepath = "$locallangdir/$filename";
                $trfilename = $filename;
            }
            // $enstring = English strings distributed either in the core space or in plugin space
            include($enfilepath);
            $enstring = isset($string) ? $string : array();
            unset($string);
            ksort($enstring);
            
            //$lcstring = local customizations
            $lcstring = array();
            if (file_exists($lcfilepath)) {
                include($lcfilepath);
                $localfileismissing = 0;
                if (isset($string) && is_array($string)) {
                    $lcstring = $string;
                }
                unset($string);
                ksort($lcstring);
            } else {
                $localfileismissing = 1;
            }

            // $string = translated strings distibuted either in core lang pack or in plugin space
            $string = array();
            if (file_exists($trfilepath)) {
                include($trfilepath);
                $fileismissing = 0;
            } else {
                $fileismissing = 1;
                $o .= notify(get_string("filemissing", "", $trfilepath), "notifyproblem", "center", true);
            }

            $missingcounter = 0;

            $first = true; // first missing string found in the file
            // For all English strings in the current file check distributed translations and _local customizations
            foreach ($enstring as $key => $value) {
                $totalcounter->strings++;
                $missingstring = false;
                $missinglocalstring = false;
                $translationsdiffer = false;
                if (empty($string[$key]) and $string[$key] != "0") { // MDL-4735
                    // string is missing in distributed pack
                    $missingstring = true;
                }
                if (empty($lcstring[$key]) and $lcstring[$key] != "0") { // MDL-4735
                    // string is missing in _local customization
                    $missinglocalstring = true;
                }
                if (!$missingstring && !$missinglocalstring && ($lcstring[$key] != $string[$key])) {
                    $translationsdiffer = true;
                }
                if ($missingstring || $translationsdiffer) {
                    $value = htmlspecialchars($value);
                    $value = str_replace("$"."a", "\\$"."a", $value);
                    $value = str_replace("%%","%",$value);
                    if ($first) {
                        $m .= "<a href=\"lang.php?mode=missing#$trfilename\">$trfilename";
                        $m .= $fileismissing ? '*' : '';
                        $m .= '</a> &nbsp; ';
                        $o .= "<p><a name=\"$trfilename\"></a><b>".
                            get_string("stringsnotset","", $trfilepath)."</b></p><pre>";
                        $first = false;
                        $somethingfound = true;
                    }
                    if ($missingstring) {
                        $missingcounter++;
                        $totalcounter->missing++;
                    }
                    if ($translationsdiffer) {
                        $missingcounter++;
                    }
                    if (LANG_LINK_MISSING_STRINGS && ($missingstring || $translationsdiffer)) {
                        $missinglinkstart = "<a href=\"lang.php?mode=compare&amp;currentfile=$filename#$key\">";
                        $missinglinkend = '</a>';
                    } else {
                        $missinglinkstart = '';
                        $missinglinkend = '';
                    }
                    if (strlen($value) > LANG_MISSING_TEXT_MAX_LEN) {
                        $value = lang_xhtml_save_substr($value, 0, LANG_MISSING_TEXT_MAX_LEN) . ' ...'; // MDL-8852
                    }
                    if ($translationsdiffer) {
                        $o .= '// ';
                    }
                    $o .= "$"."string['".$missinglinkstart.$key.$missinglinkend."'] = \"$value\";";
                    if ($translationsdiffer) {
                        $o .= '    // differs from the translation in _local';
                    } elseif (!$missinglocalstring) {
                        $o .= '    // translated only in _local';
                    }
                    $o .= "\n";
                }
            }
            if (!$first) {
                $o .= '</pre><hr />';
            }
        }

        if ($totalcounter->missing > 0) {
            $totalcounter->missingpercent = sprintf('%02.1f', ($totalcounter->missing / $totalcounter->strings * 100));
            print_heading(get_string('numberofstrings', 'admin', $totalcounter), '', 4);
        } else {
            print_heading($strnomissingstrings, '', 4, 'notifysuccess');
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
                notify(get_string("filemissing", "", "$langdir/help/$file"), 'notifyproblem');
                $somethingfound = true;
                continue;
            }
        }

        if (! $files = get_directory_list("$CFG->dirroot/lang/en_utf8/docs", "CVS")) {
            error("Could not find English language docs files!");
        }
        foreach ($files as $filekey => $file) {    // check all the docs files.
            if (!file_exists("$langdir/docs/$file")) {
                notify(get_string("filemissing", "", "$langdir/docs/$file"), 'notifyproblem');
                $somethingfound = true;
                continue;
            }
        }

        if (!empty($somethingfound)) {
            print_continue("lang.php");
        } else {
            notice(get_string("languagegood"), "lang.php" );
        }

    } else if ($mode == 'compare') {

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

        if ($currentfile <> '') {
            if (!$fileinfo = lang_get_file_info($currentfile, $stringfiles)) {
                error('Unable to find info for: '.$currentfile);
            }
            // check the filename is set up correctly, prevents bugs similar to MDL-10920
            $location = $fileinfo['location'];
            $plugin = $fileinfo['plugin'];
            $prefix = $fileinfo['prefix'];
            $filename = $fileinfo['filename'];
            if ($location || $plugin) {
                // file in an extra location
                if ($currentfile != "{$prefix}{$plugin}.php") {
                    error("Non-core filename mismatch. The file $currentfile should be {$prefix}{$plugin}.php");
                }
                if (!$uselocal) {
                    notify($streditingnoncorelangfile);
                    $editable = false;
                }
            } else {
                // file in standard location
                if ($currentfile != $filename) {
                    error("Core filename mismatch. The file $currentfile should be $filename");
                }
            }

            // Get some information about file locations:
            //  $enfilepath = the path to the English file distributed either in the core space or in plugin space
            //  $trfilepath = the path to the translated file distributed either in the lang pack or in plugin space
            //  $lcfilepath = the path to the _local customization
            //  $trfilename = the filename of the translated version of the file (including prefix for non-core files)
            if ($location || $plugin) {
                // non-core file in an extra location
                $enfilepath = "$CFG->dirroot/$location/$plugin/lang/en_utf8/$filename";
                $trfilepath = "$CFG->dirroot/$location/$plugin/lang/$currentlang/$filename";
                $lcfilepath = "$locallangdir/$filename";
                $trfilename = $filename;
            } else {
                // core file in standard location
                $enfilepath = "$CFG->dirroot/lang/en_utf8/$filename";
                $trfilepath = "$langdir/$filename";
                $lcfilepath = "$locallangdir/$filename";
                $trfilename = $filename;
            }
        }

        if (isset($_POST['currentfile'])){   // Save a file
            if (!confirm_sesskey()) {
                print_error('confirmsesskeybad', 'error');
            }

            $newstrings = array();

            foreach ($_POST as $postkey => $postval) {
                $stringkey = lang_file_string_key($postkey);
                $newstrings[$stringkey] = $postval;
            }

            unset($newstrings['currentfile']);

            $packstring = array();
            $saveinto = $langdir;
            if ($uselocal) {
                if(file_exists($trfilepath)) {
                    include($trfilepath);
                    if (isset($string)) {
                        $packstring = $string;
                    }
                    unset($string);
                }
                $saveinto = $locallangdir;
            }
             
            if (lang_save_file($saveinto, $currentfile, $newstrings, $uselocal, $packstring)) {
                notify(get_string("changessaved")." ($saveinto/$currentfile)", "notifysuccess");
            } else {
                error("Could not save the file '$saveinto/$currentfile'!", "lang.php?mode=compare&amp;currentfile=$currentfile");
            }
            unset($packstring);
        }

        print_box_start('generalbox editstrings');
        $menufiles = array();
        $menufiles_coregrp = 1;
        foreach ($stringfiles as $stringfile) {
            $item_key = $stringfile['filename'];
            $item_label = $stringfile['filename'];
            if ($stringfile['location'] != '' && $stringfile['plugin'] != '') {
                $item_label .= ' ('.$stringfile['location'].'/'.$stringfile['plugin'].')';
                if ($menufiles_coregrp == 1) {
                    $menufiles['extra'] = '------------';
                    $menufiles_coregrp = 0;
                }
            }
            $menufiles[$item_key] = $item_label;
        }
        $selectionlabel = '<code class="path">';
        //$selectionlabel .= $strfilestoredin;
        $selectionlabel .= $uselocal ? "{$currentlang}_local" : $currentlang;
        $selectionlabel .= '/</code>';
        popup_form("$CFG->wwwroot/$CFG->admin/lang.php?mode=compare&amp;currentfile=", $menufiles, "choosefile",
            $currentfile, $strchoosefiletoedit, '', '', false, 'self', $selectionlabel);
        helpbutton('langswitchstorage', $strfilestoredinhelp, 'moodle');
        print_box_end();
        
        if ($currentfile <> '') {
            error_reporting(0);
            if (!isset($editable) || $editable) {
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
                    notify(get_string("makeeditable", "", "$saveto/$currentfile"), 'notifyproblem');
                }
            }
            error_reporting($CFG->debug);

            $o = '';    // stores the HTML output to be echo-ed
            
            unset($string);
            include($enfilepath);
            $enstring = isset($string) ? $string : array();
            //
            // Following strings have moved into langconfig.php, but keep the here for backward compatibility
            //
            if ($currentlang != 'en' and $currentfile == 'moodle.php') {
                $enstring['thislanguage'] = "<< TRANSLATORS: Specify the name of your language here.  If possible use Unicode Numeric Character References >>";
                $enstring['thischarset'] = "<< TRANSLATORS:  Charset encoding - always use utf-8 >>";
                $enstring['thisdirection'] = "<< TRANSLATORS: This string specifies the direction of your text, either left-to-right or right-to-left.  Insert either 'ltr' or 'rtl' here. >>";
                $enstring['parentlanguage'] = "<< TRANSLATORS: If your language has a Parent Language that Moodle should use when strings are missing from your language pack, then specify the code for it here.  If you leave this blank then English will be used.  Example: nl >>";
            }
            unset($string);
            ksort($enstring);

            @include($lcfilepath);
            $localstring = isset($string) ? $string : array();
            unset($string);
            ksort($localstring);

            @include($trfilepath);
            $string = isset($string) ? $string : array();
            ksort($string);

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
                    $o .= '<input type="submit" tabindex="'.$missingcounter.'" name="update" value="'.get_string('savechanges').': '.$currentfile.'" />';
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
                $o .= '<span id="'.$key.'" class="stren">'.$envalue.'</span>';
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
                $missingtarget = '';
                $missingnext = '';
                $missingprev = '';
                $cellcolour = '';
                $usetabindex = false;
                if (!$value) {
                    // the string is not present in the pack being processed
                    if (!$value2) {
                        $cellcolour = 'class="bothmissing"';
                        $usetabindex = true;
                    } else {
                        $cellcolour = 'class="mastermissing"';
                        $usetabindex = true;
                    }
                    $missingcounter++;
                    if (LANG_DISPLAY_MISSING_LINKS) {
                        $missingtarget = '<a name="missing'.$missingcounter.'"></a>';
                        $missingnext = '<a href="#missing'.($missingcounter+1).'">'.
                        '<img src="' . $CFG->pixpath . '/t/down.gif" class="iconsmall" alt="'.$strnext.'" /></a>';
                        $missingprev = '<a href="#missing'.($missingcounter-1).'">'.
                        '<img src="' . $CFG->pixpath . '/t/up.gif" class="iconsmall" alt="'.$strprev.'" /></a>';
                    }
                } else {
                    // the string is translated in the pack being processed
                    if ($value <> $value2 && ($value2 <> '')) {
                        $cellcolour = 'class="localdifferent"';
                        $usetabindex = true;
                        $missingcounter++;
                        if (LANG_DISPLAY_MISSING_LINKS) {
                            $missingtarget = '<a name="missing'.$missingcounter.'"></a>';
                            $missingnext = '<a href="#missing'.($missingcounter+1).'">'.
                            '<img src="' . $CFG->pixpath . '/t/down.gif" class="iconsmall" alt="'.$strnext.'" /></a>';
                            $missingprev = '<a href="#missing'.($missingcounter-1).'">'.
                            '<img src="' . $CFG->pixpath . '/t/up.gif" class="iconsmall" alt="'.$strprev.'" /></a>';
                        }
                    }
                }

                if ($editable) {
                    $o .= '<td '.$cellcolour.' valign="top">';
                    if ($missingcounter > 1) {
                        $o .= $missingprev;
                    }
                    $o .= $missingtarget."\n";
                    if (isset($string[$key])) {
                        $valuelen = strlen($value);
                    } else {
                        $valuelen = strlen($envalue);
                    }
                    $cols=40;
                    if ($usetabindex) {
                        $tabindex = 'tabindex="'.$missingcounter.'"';
                    } else {
                        $tabindex = '';
                    }
                    if (strstr($value, "\r") or strstr($value, "\n") or $valuelen > $cols) {
                        $rows = ceil($valuelen / $cols);
                        $o .= '<textarea name="stringXXX'.lang_form_string_key($key).'" cols="'.$cols.'" rows="'.$rows.'" '.$tabindex.'>'.$value.'</textarea>'."\n";
                    } else {
                        if ($valuelen) {
                            $cols = $valuelen + 5;
                        }
                        if (LANG_ALWAYS_TEXTAREA) {
                            $o .= '<textarea name="stringXXX'.lang_form_string_key($key).'" cols="'.$cols.'" rows="1" '.$tabindex.'>'.$value.'</textarea>'."\n";
                        } else {
                            $o .= '<input type="text" name="stringXXX'.lang_form_string_key($key).'" value="'.$value.'" size="'.$cols.'" '.$tabindex.' />';
                        }
                    }
                    if ($value2 <> '' && $value <> $value2) {
                        $o .= '<br /><span style="font-size:small">'.$value2.'</span>';
                    }
                    $o .= $missingnext . '</td>';

                } else {
                    $o .= '<td '.$cellcolour.' valign="top">'.$value.'<br />'.$value2.'</td>';
                }
                $o .= '</tr>'."\n";
            }
            if ($editable) {
                $o .= '<tr><td>&nbsp;</td><td><br />';
                $o .= '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
                $o .= '<input type="hidden" name="currentfile" value="'.$currentfile.'" />';
                $o .= '<input type="hidden" name="mode" value="compare" />';
                $o .= '<input type="submit" name="update" tabindex="'.$missingcounter.'" value="'.get_string('savechanges').': '.$currentfile.'" />';
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

    } elseif ($mode == 'helpfiles') {

        $saveto = $saveto.'/help';                      // the edited content will be saved to
        $enlangdir = $enlangdir.'/help';                // English master help files localtion
        $langdir = $langdir.'/help';                    // current language master help files location
        $locallangdir = $locallangdir.'/help';          // local modifications of help files location
        $altdir = $uselocal ? $langdir : $locallangdir; // alternative to $saveto

        $fileeditorrows = 10;           // number of textareas' rows
        $fileeditorcols = 100;          // dtto cols
        $filemissingmark = ' (***)';    // mark to add to non-existing or zero-length files
        $fileoldmark = ' (old?)';       // mark to add to filenames in selection form if the English version is newer
        $filetemplate = '';             // template for new files, e.g. CVS identification

        if (isset($_POST['currentfile'])) {  // Save a file
            if (!confirm_sesskey()) {
                print_error('confirmsesskeybad', 'error');
            }
            if (lang_help_save_file($saveto, $currentfile, $_POST['filedata'])) {
                notify(get_string("changessaved")." ($saveto/$currentfile)", "notifysuccess");
            } else {
                error("Could not save the file '$currentfile'!", "lang.php?mode=helpfiles&amp;currentfile=$currentfile&amp;sesskey=$USER->sesskey");
            }
        }

        print_box_start('generalbox editstrings');
        $menufiles = array();
        $menufiles_coregrp = 1;
        $origlocation = ''; // the location of the currentfile's English source will be stored here
        $origplugin = '';   // dtto plugin
        foreach ($helpfiles as $helppath => $helpfile) {
            $item_key = $helpfile['filename'];
            $item_label = $helpfile['filename'];
            if ((!file_exists($saveto.'/'.$helpfile['filename'])) || (filesize($saveto.'/'.$helpfile['filename']) == 0)) {
                $item_label .= $filemissingmark;
            } else {
                if (filemtime($saveto.'/'.$helpfile['filename']) < filemtime($helppath)) {
                    $item_label .= $fileoldmark;
                }
                if ($helpfile['location'] != '' && $helpfile['plugin'] != '') {
                    $item_label .= ' ('.$helpfile['location'].'/'.$helpfile['plugin'].')';
                    if ($menufiles_coregrp == 1) {
                        $menufiles['extra'] = '------------';
                        $menufiles_coregrp = 0;
                    }
                }
            }
            $menufiles[$item_key] = $item_label;
            if ($currentfile == $helpfile['filename']) {
                $origlocation = $helpfile['location'];
                $origplugin = $helpfile['plugin'];
            }
        }
        $selectionlabel = '<code class="path">';
        //$selectionlabel .= $strfilestoredin;
        $selectionlabel .= $uselocal ? "{$currentlang}_local" : $currentlang;
        $selectionlabel .= '/help/</code>';
        popup_form("$CFG->wwwroot/$CFG->admin/lang.php?mode=helpfiles&amp;currentfile=", $menufiles, "choosefile",
            $currentfile, $strchoosefiletoedit, '', '', false, 'self', $selectionlabel);
        helpbutton('langswitchstorage', $strfilestoredinhelp, 'moodle');
        print_box_end();

        if (!empty($currentfile)) {

            if (!file_exists("$saveto/$currentfile")) {
                $dbg .= "File does not exist: $saveto/$currentfile\n";
                //check if directory exist
                if (!file_exists(dirname("$saveto/$currentfile"))) {
                     if(!lang_make_directory(dirname("$saveto/$currentfile"))) {
                         echo ('Cannot create directory: '.dirname("$saveto/$currentfile"));
                     }
                }
                //
                // file doesn't exist - let's check webserver's permission to create it
                //
                if (!@touch("$saveto/$currentfile")) {
                    //
                    // webserver is unable to create new file
                    //
                    notify(get_string('filemissing', '', "$saveto/$currentfile" ));
                    notify(get_string('makeeditable', '', "$saveto/$currentfile"));
                    $editable = false;
                } else {
                    //
                    // webserver can create new file - we can delete it now and let
                    // it create again if its filesize() > 0
                    //
                    $editable = true;
                    unlink("$saveto/$currentfile");
                }
            } elseif (is_writable("$saveto/$currentfile")) {
                $editable = true;
            } else {
                //
                // file exists but it is not writeable by web server process :-(
                //
                $editable = false;
                notify(get_string('makeeditable', '', "$saveto/$currentfile"));
            }

            // master en_utf8 in dataroot is not editable
            if ((!$uselocal) && ($currentlang == 'en_utf8')) {
                $editable = false;
            }

            echo '<div>';

            if ($uselocal) {
                $strsavetotitle = $strlanglocalpackage . helpbutton('langpackages', $strlanglocalpackage, 'moodle', true, false, '', true);
                $straltdirtitle = $strlangmasterpackage . helpbutton('langpackages', $strlangmasterpackage, 'moodle', true, false, '', true);
            } else {
                $straltdirtitle = $strlanglocalpackage . helpbutton('langpackages', $strlanglocalpackage, 'moodle', true, false, '', true);
                $strsavetotitle = $strlangmasterpackage . helpbutton('langpackages', $strlangmasterpackage, 'moodle', true, false, '', true);

            }

            if ($editable) {
                // generate an editor for the current help file in $saveto
                echo '<fieldset><legend>'.$strsavetotitle.'</legend>';
                echo "<form id=\"helpfileeditor\" action=\"lang.php\" method=\"post\">";
                echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
                echo '<input type="hidden" name="currentfile" value="'.$currentfile.'" />';
                echo '<input type="hidden" name="mode" value="helpfiles" />';
                echo "<div class='mdl-align'>\n";
                echo "<textarea rows=\"$fileeditorrows\" cols=\"$fileeditorcols\" name=\"filedata\">";
                if (file_exists("$saveto/$currentfile")) {
                    echo htmlspecialchars(file_get_contents("$saveto/$currentfile"));
                } else {
                    echo ($filetemplate);
                }
                echo "</textarea>\n</div>\n";
                echo '<div class="mdl-align"><input type="submit" value="'.get_string('savechanges').'" /></div>';
                echo '</form>';
                $preview_url = lang_help_preview_url($currentfile, !$uselocal);
                if ($preview_url) {
                    link_to_popup_window($preview_url, 'popup', get_string('preview'));
                }
                echo '</fieldset>';
            }

            if (is_readable("$altdir/$currentfile")) {
                // show the content of the same help file in alternative location
                echo '<fieldset><legend>'.$straltdirtitle.'</legend>';
                echo "<div class='mdl-align'>\n";
                echo "<textarea rows=\"$fileeditorrows\" cols=\"$fileeditorcols\" name=\"\">";
                if (file_exists("$altdir/$currentfile")) {
                    echo htmlspecialchars(file_get_contents("$altdir/$currentfile"));
                } else {
                    echo ($filetemplate);
                }
                echo "</textarea>\n</div>\n";
                $preview_url = lang_help_preview_url($currentfile, $uselocal);
                if ($preview_url) {
                    link_to_popup_window($preview_url, 'popup', get_string('preview'));
                }
                echo '</fieldset>';
            }

            // show the content of the original English file either in core space or plugin space
            if ($origlocation != '' && $origplugin != '') {
                // non-core help file
                $ensrc = "$CFG->dirroot/$origlocation/$origplugin/lang/en_utf8/help/$currentfile";
            } else {
                // core help file
                $ensrc = "$enlangdir/$currentfile";
            }
            if (is_readable($ensrc)) {
                echo '<fieldset><legend>'.$strlangmasterenglish;
                helpbutton('langpackages', $strlangmasterenglish);
                echo '</legend>';
                echo "<div class='mdl-align'>\n<textarea rows=\"$fileeditorrows\" cols=\"$fileeditorcols\" name=\"\">";
                echo htmlspecialchars(file_get_contents($ensrc));
                echo "</textarea>\n</div>\n";
                $preview_url = lang_help_preview_url($currentfile, true, 'en_utf8');   // do not display en_utf8_local
                if ($preview_url) {
                    link_to_popup_window($preview_url, 'popup', get_string('preview'));
                }
                echo '</fieldset>';
            }

            echo '</div>'; // translator box
            error_reporting($CFG->debug);
        }

        if (false && $CFG->debugdisplay && debugging('', DEBUG_DEVELOPER) ) {
            echo '<hr />';
            print_heading('Debugging info');
            echo '<pre class="notifytiny">';
            print_r($dbg);
            print_r("\n\$currentfile = $currentfile");
            print_r("\n\$enlangdir = $enlangdir");
            print_r("\n\$langdir = $langdir");
            print_r("\n\$locallangdir = $locallangdir");
            print_r("\n\$saveto = $saveto");
            print_r("\n\$altdir = $altdir");
            print_r("\n\$origlocation = $origlocation");
            print_r("\n\$origplugin = $origplugin");
            print_r("\n\$ensrc = $ensrc");
            print_r("\n\$helpfiles = ");
            print_r($helpfiles);
            echo '</pre>';
        }

    } // fi $mode == 'helpfiles'


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
            if ((!$local) || (!isset($packstrings[$stringname])) || (lang_fix_value_from_file($packstrings[$stringname]) <> lang_fix_value_from_file($value))) {
                // Either we are saving the master language pack
                // or the string is not saved in packstring - fixes PHP notices about missing key
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
    // escape all embedded variables
    $value = str_replace('$', '\$', $value);            // Add slashes for $
    // unescape placeholders: only $a and $a->something are allowed. All other $variables are left escaped
    $value = preg_replace('/\\\\\$a($|[^_a-zA-Z0-9\-]|\->[a-zA-Z0-9_]+)/', '$a\\1', $value);
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
 * Uses PHP>=5.0 syntax of mkdir and tries to create directories recursively.
 *
 * @uses $CFG
 * @param string $directory full path to the directory under $langbase
 * @return string|false Returns full path to directory if successful, false if not
 */
function lang_make_directory($dir, $shownotices=true) {
    global $CFG;
    umask(0000);
    if (! file_exists($dir)) {
        if (! @mkdir($dir, $CFG->directorypermissions, true)) { // recursive=true; PHP>=5.0 needed
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
* Core lang files should always be stored here and not in the module space (MDL-10920).
* The English version of the file may be found in
*  $CFG->dirroot/lang/en_utf8/filename
* The localised version of the found file should be saved into
*  $CFG->dataroot/lang/currentlang[_local]/filename
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
*  $CFG->dataroot/lang/currentlang[_local]/prefix_plugin.php
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

/**
 * Lookup for a stringfile details.
 *
 * English files can be stored in several places (core space or module/plugin space). Their translations
 * go into the one directory - the current language pack. Therefore, the name of the stringfile may be
 * considered as a key of the list of all stringfiles.
 *
 * @param string $currentfile the filename
 * @param array $stringfiles the array of file info returned by {@link lang_extra_locations()}
 * @return array Array of a file information (filename, location, plugin, prefix) or null.
 */
function lang_get_file_info($currentfile, $stringfiles) {
    $found = false;
    foreach ($stringfiles as $path=>$stringfile) {
        if ($stringfile['filename'] == $currentfile) {
            $found = true;
            $ret = $stringfile;
            $ret['fullpath'] = $path;
            break;
        }
    }
    if ($found) {
        return $ret;
    } else {
        return null;
    }
}

/**
 * Returns all English help files in the standard lang/en_utf8/help location.
 *
 * Core help files should always be stored here and not in the module space (MDL-10920).
 * The English version of the file may be found in
 *  $CFG->dirroot/lang/en_utf8/help/filename
 * The localised version of the found file should be saved into
 *  $CFG->dataroot/lang/currentlang[_local]/help/filename
 * where "filename" is returned as a part of the file record.
 *
 * @return array Array of a file information. Compatible format with {@link lang_extra_locations()}
 */
function lang_help_standard_locations() {
    global $CFG;
    $files = array();
    // Standard location of master English help files.
    $places = array($CFG->dirroot.'/lang/en_utf8/help');
        foreach ($places as $place) {
            foreach (get_directory_list($place, 'CVS') as $file) {
                if ((substr($file, -5) == '.html') || (substr($file, -4) == '.txt' )) {
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
 * Returns all English help files in non-standard location.
 *
 * Searches for lang/en_utf8/help/* files in various types of plugins (blocks, database presets, question types,
 * 3rd party modules etc.) and returns an array of found files details.
 *
 * The English version of the file may be found in
 *  $CFG->dirroot/location/plugin/lang/en_utf8/help/filename
 * The localised version of the found file should be saved into
 *  $CFG->dataroot/lang/currentlang[_local]/help/prefix_plugin/filename (XXX is "prefix" here right?)
 * where "location", "plugin", "prefix" and "filename" are returned as a part of the file record.
 *
 * @return array Array of a file information. Compatible format with {@link lang_standard_locations()}
 */
function lang_help_extra_locations() {
    global $CFG;
    $files = array();
    $places = places_to_search_for_lang_strings();
    foreach ($places as $prefix => $directories) {
        if ($prefix != '__exceptions') {
            foreach ($directories as $directory) {
                foreach (get_list_of_plugins($directory) as $plugin) {
                    $enlangdirlocation = $CFG->dirroot.'/'.$directory.'/'.$plugin.'/lang/en_utf8/help';
                    foreach (get_directory_list($enlangdirlocation, 'CVS') as $file) {
                        if ((substr($file, -5) == '.html') || (substr($file, -4) == '.txt' )) {
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

/**
 * Return a preview URL for help file, if available.
 *
 * @param string $currentfile The relative path to the help file, e.g. "assignment/types.html" - MDL-12291
 * @param bool $skiplocal Force displaying the helpfile from a master lang pack
 * @param string $forcelang Force language of the help, e.g. "en_utf8"
 * @return string $url
 */
function lang_help_preview_url($currentfile, $skiplocal=false, $forcelang = '') {
    $currentpathexp = explode('/', $currentfile);
    if (count($currentpathexp) > 1) {
        $url = '/help.php?module='.implode('/',array_slice($currentpathexp,0,count($currentpathexp)-1)).'&amp;file='.end($currentpathexp);
    } else {
        $url = '/help.php?module=moodle&amp;file='.$currentfile;
    }
    if ($skiplocal) {
        $url .= '&amp;skiplocal=1';
    }
    if ($forcelang) {
        $url .= '&amp;forcelang='.$forcelang;
    }
    return $url;
}


/**
 * Saves (overwrites) translated help file.
 *
 * @param string $helproot The path to the "help" folder
 * @param string $file The relative path to the html help file
 * @param string $content HTML data to be saved
 * @return bool False if save failed, true otherwise
 */
function lang_help_save_file($helproot, $file, $content) {
    global $CFG, $USER;

    $content = str_replace("\r", "",$content);              // Remove linefeed characters
    $content = preg_replace("/\n{3,}/", "\n\n", $content);  // Collapse runs of blank lines
    $content = trim($content);                              // Delete leading/trailing whitespace
    if (is_readable("$helproot/$file") && filesize("$helproot/$file") > 0 && $content == '') {
        notify(get_string('langrmyourself', 'admin'));
        return true;
    }

    error_reporting(0);
    if (!$f = fopen("$helproot/$file","w")) {
        error_reporting($CFG->debug);
        return false;
    }
    error_reporting($CFG->debug);

    fwrite($f, stripslashes($content));
    fclose($f);

    // Remove file if its empty
    if (filesize("$helproot/$file") == 0) {
        unlink("$helproot/$file");
    }

    return true;
}


?>
