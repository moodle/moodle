<?php  // $Id$
/// Extended by Michael Schneider
/// This page prints a particular instance of wiki

    global $CFG,$USER;

    require_once("../../config.php");
    require_once("lib.php");
    #require_once("$CFG->dirroot/course/lib.php"); // For side-blocks    
    require_once($CFG->libdir . '/ajax/ajaxlib.php');
    require_js(array('yui_yahoo', 'yui_event', 'yui_connection'));

    $ewiki_action = optional_param('ewiki_action', '', PARAM_ALPHA);     // Action on Wiki-Page
    $id           = optional_param('id', 0, PARAM_INT);                  // Course Module ID, or
    $wid          = optional_param('wid', 0, PARAM_INT);                 // Wiki ID
    $page         = optional_param('page', false);                       // Wiki Page Name
    $q            = optional_param('q',"", PARAM_PATH);                  // Search Context
    $userid       = optional_param('userid', 0, PARAM_INT);              // User wiki.
    $groupid      = optional_param('groupid', 0, PARAM_INT);             // Group wiki.
    $canceledit   = optional_param('canceledit','', PARAM_ALPHA);        // Editing has been cancelled
    $cacheme      = optional_param('allowcache', 1, PARAM_INT);          // Set this to 0 to try and disable page caching.
    
    // Only want to add edit log entries if we have made some changes ie submitted a form
    $editsave = optional_param('thankyou', '');
    
    if($page) {
        // Split page command into action and page
        $actions = explode('/', $page,2);
        if(count($actions)==2) {
            $pagename=$actions[1];
        } else {
            $pagename=$actions[0];
        }
    } else {
        $actions=array('');
        $pagename='';
    }
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('wiki', $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $wiki = get_record("wiki", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $wiki = get_record("wiki", "id", $wid)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $wiki->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("wiki", $wiki->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_course_login($course, true, $cm);
    
    /// Add the course module info to the wiki object, for easy access.
    $wiki->groupmode = $cm->groupmode;
    $wiki->groupingid = $cm->groupingid;
    $wiki->groupmembersonly = $cm->groupmembersonly;
    $wiki->cmid = $cm->id;
    
    /// Default format:
    $moodle_format=FORMAT_MOODLE;

/// Globally disable CamelCase, if the option is selected for this wiki.
    $moodle_disable_camel_case = ($wiki->disablecamelcase == 1);
    
    if (($wiki_entry = wiki_get_default_entry($wiki, $course, $userid, $groupid))) {
        // OK, now we know the entry ID, we can do lock etc.
        
        // If true, we are 'really' on an editing page, not just on edit/something
        $reallyedit=$actions[0]=='edit' && !$canceledit && !$editsave;

        // Remove lock when we go to another wiki page (such as the cancel page)
        if(!$reallyedit) {
            wiki_release_lock($wiki_entry->id,$pagename);
        } else if(array_key_exists('content',$_POST)) {
            // Do not allow blank content because it causes problems (the wiki decides
            // the page should automatically go into edit mode, but Moodle doesn't realise
            // this and filters out the JS)
            if($_POST['content']=='') {
                $_POST['content']="\n";
                $_REQUEST['content']="\n";
            }
    
            // We must have the edit lock in order to be permitted to save    
            list($ok,$lock)=wiki_obtain_lock($wiki_entry->id,$pagename);
            if(!$ok) {
                $strsavenolock=get_string('savenolock','wiki');
                error($strsavenolock,$CFG->wwwroot.'/mod/wiki/view.php?id='.$cm->id.'&page=view/'.urlencode($pagename));
            }
        }
        
///     ################# EWIKI Part ###########################
///     The wiki_entry->pagename is set to the specified value of the wiki,
///     or the default value in the 'lang' file if the specified value was empty.
        define("EWIKI_PAGE_INDEX",$wiki_entry->pagename);

        /// If the page has a ' in it, it may have slashes added to it. Remove them if it does.
        $page = ($page === false) ?  stripslashes(EWIKI_PAGE_INDEX) : stripslashes($page);

///     # Prevent ewiki getting id as PageID...
        unset($_REQUEST["id"]);
        unset($_GET["id"]);
        unset($_POST["id"]);
        unset($_POST["id"]);
        unset($_SERVER["QUERY_STRING"]);
        if (isset($HTTP_GET_VARS)) {
            unset($HTTP_GET_VARS["id"]);
        }
        if (isset($HTTP_POST_VARS)) {
            unset($HTTP_POST_VARS["id"]);
        }
        global $ewiki_title;

///     #-- predefine some of the configuration constants


        /// EWIKI_NAME is defined in ewikimoodlelibs, so that also admin.php can use this
        #define("EWIKI_NAME", $wiki_entry->pagename);

        /// Search Hilighting
        if($ewiki_title=="SearchPages") {
            $qArgument="&amp;q=".urlencode($q);
        }

        /// Build the ewsiki script constant
        /// ewbase will also be needed by EWIKI_SCRIPT_BINARY
        $ewbase = 'view.php?id='.$cm->id;
        if (isset($userid) && $userid!=0) $ewbase .= '&amp;userid='.$userid;
        if (isset($groupid) && $groupid!=0) $ewbase .= '&amp;groupid='.$groupid;
        $ewscript = $ewbase.'&amp;page=';
        define("EWIKI_SCRIPT", $ewscript);
        define("EWIKI_SCRIPT_URL", $ewscript);

        /// # Settings for this specific Wiki
        define("EWIKI_PRINT_TITLE", $wiki->ewikiprinttitle);

        define("EWIKI_INIT_PAGES", wiki_content_dir($wiki));

///     # Moodle always addslashes to everything so we are going to strip them always
///     # to allow wiki itself to add them again. It's a triple add-strip-add but
///     # was the only way to solve the problem without modifying how the rest of
///     # the module works.
        include($CFG->dirroot."/mod/wiki/ewiki/fragments/strip_wonderful_slashes.php");

        if (ini_get("register_globals")) {
            #    include($CFG->dirroot."/mod/wiki/ewiki/fragments/strike_register_globals.php");
        }

        # Database Handler
        include_once($CFG->dirroot."/mod/wiki/ewikimoodlelib.php");
        # Plugins
        //include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/email_protect.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/patchsaving.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/notify.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/feature/imgresize_gd.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_highlight.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/f_fixhtml.php");
        #include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/wikinews.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/sitemap.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_wikidump.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/aview/backlinks.php");
        #include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/markup/css.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/markup/footnotes.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/diff.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/page/pageindex.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/page/orphanedpages.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/wantedpages.php");

        # Binary Handling
        if($wiki->ewikiacceptbinary) {
            define("EWIKI_UPLOAD_MAXSIZE", get_max_upload_file_size());
            define("EWIKI_SCRIPT_BINARY", $ewbase."&binary=");
            define("EWIKI_ALLOW_BINARY",1);
            define("EWIKI_IMAGE_CACHING",1);
            #define("EWIKI_AUTOVIEW",1);
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/lib/mime_magic.php");
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/aview/downloads.php");
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/downloads.php");
            #include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/db/binary_store.php");
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_binary_store.php");
        } else {
            define("EWIKI_SCRIPT_BINARY", 0);
            define("EWIKI_ALLOW_BINARY",0);
        }

        # The mighty Wiki itself
        include_once($CFG->dirroot."/mod/wiki/ewiki/ewiki.php");

        if($canceledit) {
          if ($delim = strpos($page, EWIKI_ACTION_SEP_CHAR)) {
            @$page = substr($page, $delim + 1);
          } else {
            @$page="";
          }
        }
        # Language-stuff: eWiki gets language from Browser. Lets correct it. Empty arrayelements do no harm
        $ewiki_t["languages"]=array(current_language(), $course->lang, $CFG->lang,"en","c");

        # Check Access Rights
        $canedit = wiki_can_edit_entry($wiki_entry, $wiki, $USER, $course);
        if (!$canedit) {
            # Protected Mode
            unset($ewiki_plugins["action"]["edit"]);
            unset($ewiki_plugins["action"]["info"]);
        }

        # HTML Handling
        $ewiki_use_editor=0;
        if($wiki->htmlmode == 0) {
            # No HTML
            $ewiki_config["htmlentities"]=array(); // HTML is managed by moodle
            $moodle_format=FORMAT_TEXT;
        }
        if($wiki->htmlmode == 1) {
            # Safe HTML
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_rescue_html.php");
            $moodle_format=FORMAT_HTML;
        }
        if($wiki->htmlmode == 2) {
            # HTML Only
            $moodle_format=FORMAT_HTML;
            $ewiki_use_editor=1;
            $ewiki_config["htmlentities"]=array(); // HTML is allowed
            $ewiki_config["wiki_link_regex"] = "\007 [!~]?(
                        \#?\[[^<>\[\]\n]+\] |
                        \^[-".EWIKI_CHARS_U.EWIKI_CHARS_L."]{3,} |
                        \b([\w]{3,}:)*([".EWIKI_CHARS_U."]+[".EWIKI_CHARS_L."]+){2,}\#?[\w\d]* |
                        \w[-_.+\w]+@(\w[-_\w]+[.])+\w{2,}   ) \007x";
        }

        global $ewiki_author, $USER;
        $ewiki_author=fullname($USER);
        $content=ewiki_page($page);
        $content2='';

///     ################# EWIKI Part ###########################
    }
    else {
        $content = '';
        $content2 = '<div class="boxaligncenter">'.get_string('nowikicreated', 'wiki').'</div>';
        
    }

    # Group wiki, ...: No page and no ewiki_title
    if(!isset($ewiki_title)) {
          $ewiki_title="";
    }


/// Moodle Log
    if ($editsave != NULL) { /// We've submitted an edit and have been redirected back here
        add_to_log($course->id, "wiki", 'edit', 
               addslashes("view.php?id=$cm->id&amp;groupid=$groupid&amp;userid=$userid&amp;page=$ewiki_title"),
               format_string($wiki->name,true).": ".$ewiki_title, $cm->id, $userid);
    } else if ($ewiki_action != 'edit') {
        add_to_log($course->id, "wiki", $ewiki_action, 
               addslashes("view.php?id=$cm->id&amp;groupid=$groupid&amp;userid=$userid&amp;page=$ewiki_title"),
               format_string($wiki->name,true).": ".$ewiki_title, $cm->id, $userid);
    } 


/// Print the page header

    $strwikis = get_string("modulenameplural", "wiki");
    $strwiki  = get_string("modulename", "wiki");

    $navlinks = array();
/// Add page name if not main page
    if ($ewiki_title != $wiki->name) {
        $navlinks[] = array('name' => format_string($ewiki_title), 'link' => '', 'type' => 'title');
    }

    $navigation = build_navigation($navlinks, $cm);
    print_header_simple($ewiki_title?$ewiki_title:format_string($wiki->name), "", $navigation,
                "", "", $cacheme, update_module_button($cm->id, $course->id, $strwiki),
                navmenu($course, $cm));


    /// Print Page
    echo '    <div id="wikiPageActions">
    ';
    /// The top row contains links to other wikis, if applicable.
    if ($wiki_entry && $wiki_list = wiki_get_other_wikis($wiki, $USER, $course, $wiki_entry->id)) {
        //echo "wiki list ";print_r($wiki_list);
        $selected="";
        
        if (isset($wiki_list['selected'])) {
            $selected = $wiki_list['selected'];
            unset($wiki_list['selected']);
        }
        echo '<tr><td colspan="2">';

        echo '<form id="otherwikis" action="'.$CFG->wwwroot.'/mod/wiki/view.php">';
        echo '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
        echo '<td class="sideblockheading">&nbsp;'
            .$WIKI_TYPES[$wiki->wtype].' '
            .get_string('modulename', 'wiki')." ".get_string('for',"wiki")." "
            .wiki_get_owner($wiki_entry).':</td>';

        echo '<td class="sideblockheading">'
            .get_string('otherwikis', 'wiki').':&nbsp;&nbsp;';
        $script = 'self.location=getElementById(\'otherwikis\').wikiselect.options[getElementById(\'otherwikis\').wikiselect.selectedIndex].value';
        choose_from_menu($wiki_list, "wikiselect", $selected, "choose", $script);
        echo '</td>';
        echo '</tr></table>';
        echo '</form>';

        echo '</td>';
        echo '</tr>';
    }

    if ($wiki_entry) {
        $specialpages=array("WikiExport", "SiteMap", "SearchPages", "PageIndex","NewestPages","MostVisitedPages","MostOftenChangedPages","UpdatedPages","FileDownload","FileUpload","OrphanedPages","WantedPages");
    /// Page Actions
        echo '<table border="0" width="100%">';
        echo '<tr>';

        /// Searchform
        echo '<td class="wikisearchform">';
        wiki_print_search_form($cm->id, $q, $userid, $groupid, false);
        echo '</td>';

        /// Internal Wikilinks
        echo '<td class="wikilinksblock">';
        wiki_print_wikilinks_block($cm->id,  $wiki->ewikiacceptbinary);
        echo '</td>';

        /// Administrative Links
        if($canedit) {
          echo '<td class="wikiadminactions">';
          wiki_print_administration_actions($wiki, $cm->id, $userid, $groupid, $ewiki_title, $wiki->htmlmode!=2, $course);
          echo '</td>';
        }

        /// Formatting Rules
        echo '<td class="howtowiki">';
        helpbutton('howtowiki', get_string('howtowiki', 'wiki'), 'wiki');
        echo '</td>';

        echo '</tr></table>';
    }

    echo '</div>
    <div id="wiki-view" class="mwiki">
    ';

    if($wiki_entry && $ewiki_title==$wiki_entry->pagename && !empty($wiki->summary)) {
      if (trim(strip_tags($wiki->summary))) {
          print_box(format_text($wiki->summary, FORMAT_MOODLE), 'generalbox', 'intro');
      }
    }

    // The wiki Contents

    if (!empty($canedit)) {   /// Print tabs with commands for this page
        $tabs = array('view', 'edit','links','info');
        if ($wiki->ewikiacceptbinary) {
            $tabs[] = 'attachments';
        }

        $tabrows = array();
        $row  = array();
        $currenttab = '';
        foreach ($tabs as $tab) {
            $tabname = get_string("tab$tab", 'wiki');
            $row[] = new tabobject($tabname, $ewbase.'&amp;page='.$tab.'/'.s($ewiki_id), $tabname);
            if ($ewiki_action == "$tab" or in_array($page, $specialpages)) {
                $currenttab = $tabname;
            }
        }
        $tabrows[] = $row;

        print_tabs($tabrows, $currenttab);
    }

    /// Insert a link to force page refresh if new content isn't showing.
    
    // build new URL + query string
    $queries = preg_split('/[?&]/', me());  
    $nqueries = count($queries);
    $me = $queries[0] . '?';
    for($i=1; $i < $nqueries; $i++)
    {
        if( !strstr($queries[$i], 'allowcache') )
            $me .= $queries[$i] . '&amp;'; 
    }
    $me .= 'allowcache=0';

    // Insert the link
    $linkdesc = get_string('reloadlinkdescription', 'wiki');
    $linktext = get_string('reloadlinktext', 'wiki');
    echo "<div class='wikilinkright'><a href='$me' title='$linkdesc'><input type='button' value='$linktext' /></a></div>";

    print_simple_box_start('center', '100%', '', '20');

    /// Don't filter any pages containing wiki actions (except view). A wiki page containing
    /// actions will have the form [action]/[pagename]. If the action is 'view' or the  '/'
    /// isn't there (so the action defaults to 'view'), filter it.
    /// If the page does not yet exist, the display will default to 'edit'.
    if((count($actions) < 2 || $actions[0] == "view") && $wiki_entry && 
        record_exists('wiki_pages', 'pagename', addslashes($page), 'wiki', $wiki_entry->id)) {
        print(format_text($content, $moodle_format));
    } else if($actions[0]=='edit' && $reallyedit) {
        // Check the page isn't locked before printing out standard wiki content. (Locking
        // is implemented as a wrapper over the existing wiki.)
        list($gotlock,$lock)=wiki_obtain_lock($wiki_entry->id,$pagename);
        if(!$gotlock) {
            $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
            $canoverridelock = has_capability('mod/wiki:overridelock', $modcontext);
            
            $a=new stdClass;
            $a->since=userdate($lock->lockedsince);
            $a->seen=userdate($lock->lockedseen);
            $user=get_record('user','id',$lock->lockedby);
            $a->name=fullname($user, 
              has_capability('moodle/site:viewfullnames', $modcontext));
                
            print_string('pagelocked','wiki',$a);
            
            if($canoverridelock) {
                $pageesc=htmlspecialchars($page);
                $stroverrideinfo=get_string('overrideinfo','wiki');
                $stroverridebutton=get_string('overridebutton','wiki');
                $sesskey=sesskey();
                print "
<form id='overridelock' method='post' action='overridelock.php'>
  <div>
  <input type='hidden' name='sesskey' value='$sesskey' />
  <input type='hidden' name='id' value='$cm->id' />
  <input type='hidden' name='page' value='$pageesc' />
  $stroverrideinfo
  <input type='submit' value='$stroverridebutton' />
  </div>
</form>
";
            }
        } else {
            if (ajaxenabled()) {
                // OK, the page is now locked to us. Put in the AJAX for keeping the lock
                $strlockcancelled=addslashes(get_string('lockcancelled','wiki'));
                $strnojslockwarning=get_string('nojslockwarning','wiki');
                $intervalms=WIKI_LOCK_RECONFIRM*1000;
                print "
<script type='text/javascript'>
var intervalID;
function handleResponse(o) {
    if(o.responseText=='cancel') {
        document.forms['ewiki'].elements['preview'].disabled=true;
        document.forms['ewiki'].elements['save'].disabled=true;
        clearInterval(intervalID);
        alert('$strlockcancelled');
    }
}
function handleFailure(o) {
    // Ignore for now
}
intervalID=setInterval(function() {
    YAHOO.util.Connect.asyncRequest('POST','confirmlock.php',
        {success:handleResponse,failure:handleFailure},'lockid={$lock->id}');    
    },$intervalms);
</script>
<noscript><p>
$strnojslockwarning
</p></noscript>
";
            }
            // Print editor etc
            print $content;
        }
    } else {
        print $content;
    }
    print $content2;
    print_simple_box_end();
    echo "<br />";

/// Finish the page
    echo '
    </div>
    ';

    print_footer($course);
?>
