<?PHP
/// Extended by Michael Schneider

    require_once("../../config.php");
    require_once("lib.php");

    $id      = optional_param('id', '', PARAM_INT);        // Course Module ID, or
    $a       = optional_param('a', '', PARAM_INT);         // wiki ID
    $page    = optional_param('page', false, PARAM_CLEAN); // Pagename
    $confirm = optional_param('confirm', '', PARAM_RAW);
    $action  = optional_param('action', '', PARAM_ACTION); // Admin Action
    $userid  = optional_param('userid', 0, PARAM_INT);     // User wiki.
    $groupid = optional_param('groupid', 0, PARAM_INT);    // Group wiki.

    if ($id) {
        if (! $cm = get_coursemodule_from_id('wiki', $id)) {
            print_error('invalidcoursemodule');
        }

        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }

        if (! $wiki = $DB->get_record("wiki", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }

    } else {
        if (! $wiki = $DB->get_record("wiki", array("id"=>$a))) {
            print_error('coursemisconf');
        }
        if (! $course = $DB->get_record("course", array("id"=>$wiki->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance("wiki", $wiki->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    }

    $url = new moodle_url('/mod/wiki/admin.php', array('id'=>$cm->id));
    if ($page !== false) {
        $url->param('page', $page);
    }
    if ($confirm !== '') {
        $url->param('confirm', $confirm);
    }
    if ($action !== '') {
        $url->param('action', $action);
    }
    if ($userid !== 0) {
        $url->param('userid', $userid);
    }
    if ($groupid !== 0) {
        $url->param('groupid', $groupid);
    }
    $PAGE->set_url($url);

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/wiki:manage', $context);

    /// Build the ewsiki script constant
    $ewbase = 'view.php?id='.$id;
    if (isset($userid) && $userid!=0) $ewbase .= '&amp;userid='.$userid;
    if (isset($groupid) && $groupid!=0) $ewbase .= '&amp;groupid='.$groupid;
    $ewscript = $ewbase.'&amp;page=';
    define("EWIKI_SCRIPT", $ewscript);
    if($wiki->ewikiacceptbinary) {
      define("EWIKI_UPLOAD_MAXSIZE", get_max_upload_file_size());
      define("EWIKI_SCRIPT_BINARY", $ewbase."&binary=");
    }


    /// Add the course module 'groupmode' to the wiki object, for easy access.
    $wiki->groupmode = $cm->groupmode;

    /// Is an Action given ?
    if(!$action) {
      print_error('noadministrationaction','wiki');
    }

    /// Correct Action ?
    if(!in_array($action, array("setpageflags", "removepages", "strippages", "checklinks", "revertpages"))) {
      print_error('unknowaction');
    }


    /// May the User administrate it ?
    if (($wiki_entry = wiki_get_entry($wiki, $course, $userid, $groupid)) === false || wiki_can_edit_entry($wiki_entry, $wiki, $USER, $course) === false) {
      print_error('notadministratewiki', 'wiki');
    }

    $canedit = wiki_can_edit_entry($wiki_entry, $wiki, $USER, $course);
    # Check for dangerous events (hacking) !
    if(in_array($action,array("removepages","strippages","revertpages"))) {
      if(!($wiki->wtype=="student" || ($wiki->wtype=="group" and $canedit) || wiki_is_teacher($wiki))) {
        add_to_log($course->id, "wiki", "hack", "", $wiki->name.": Tried to trick admin.php with action=$action.");
        print_error('hackdetected');
      }
    }

    # Database and Binary Handler
    include_once($CFG->dirroot."/mod/wiki/ewikimoodlelib.php");
    include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_binary_store.php");

    /// The wiki_entry->pagename is set to the specified value of the wiki,
    /// or the default value in the 'lang' file if the specified value was empty.
    define("EWIKI_PAGE_INDEX",$wiki_entry->pagename);
    # The mighty Wiki itself
    include_once($CFG->dirroot."/mod/wiki/ewiki/ewiki.php");

    $strwikis = get_string("modulenameplural", "wiki");
    $strwiki  = get_string("modulename", "wiki");

    /// Validate Form
    if ($form = data_submitted()) {
      switch($action) {
        case "revertpages":
              if(!$form->deleteversions || 0 > $form->deleteversions || $form->deleteversions > 1000) {
                $focus="form.deleteversions";
                $err->deleteversions=get_string("deleteversionserror","wiki");
              }
              if(!$form->changesfield || 0 > $form->changesfield || $form->changesfield > 100000) {
                $focus="form.changesfield";
                $err->changesfield=get_string("changesfielderror","wiki");
              }
              if($form->authorfieldpattern=="") {
                $focus="form.authorfieldpattern";
                $err->authorfieldpattern=get_string("authorfieldpatternerror","wiki");
              }
          break;
          default: break;
       }
    }

    $PAGE->navbar->add(get_string("administration","wiki"));
    $PAGE->set_title($wiki_entry->pagename);
    $PAGE->set_focuscontrol($focus);
    $PAGE->set_button($OUTPUT->update_module_button($cm->id, 'wiki'));
    echo $OUTPUT->header();

    ////////////////////////////////////////////////////////////
    /// Check if the Form has been submitted and display confirmation
    ////////////////////////////////////////////////////////////
    if ($form = data_submitted()) {
      /// Moodle Log
      /// Get additional info
      $addloginfo="";
      switch($action) {
        case "removepages":
          $addloginfo=@join(", ", $form->pagestodelete);
        break;
        case "strippages":
          $addloginfo=@join(", ", $form->pagestostrip);
        break;
        case "checklinks":
          $addloginfo=$form->pagetocheck;
        break;
        case "setpageflags":
          // No additional info
        break;
        case "revertpages":
          // No additional info
        break;
      }
      add_to_log($course->id, "wiki", $action, "admin.php?action=$action&amp;userid=$userid&amp;groupid=$groupid&amp;id=$id", $wiki->name.($addloginfo?": ".$addloginfo:""));
      $link="admin.php?action=$action".($userid?"&amp;userid=".$userid:"").($groupid?"&amp;groupid=".$groupid:"")."&amp;id=$id&amp;page=$page";
      switch($action) {
        case "removepages":
            if($form->proceed) {
              if(!$confirm && $form->pagestodelete) {
                echo $OUTPUT->confirm(get_string("removepagecheck", "wiki")."<br />".join(", ", $form->pagestodelete),
                  $link."&confirm=".urlencode(join(" ",$form->pagestodelete)), $link);
                echo $OUTPUT->footer();
                exit;
              }
            }
          break;
        case "strippages":
            if($form->proceed) {
              if(!$confirm && $form->pagestostrip) {
                $err=array();
                $strippages=wiki_admin_strip_versions($form->pagestostrip,$form->version, $err);
                $confirm="";
                foreach($strippages as $cnfid => $cnfver) {
                  $confirm.="&confirm[$cnfid]=".urlencode(join(" ",$cnfver));
                }
                if(count($err)==0) {
                  $pagestostrip=array();
                  foreach($form->pagestostrip as $pagetostrip) {
                    $pagestostrip[]=htmlspecialchars(urldecode($pagetostrip));
                  }
                  echo $OUTPUT->confirm(get_string("strippagecheck", "wiki")."<br />".join(", ", $pagestostrip), $link.$confirm, $link);
                  echo $OUTPUT->footer();
                  exit;
                }
              }
            }
            break;
        case "checklinks":
            if($form->proceed) {
              if(!$confirm && $form->pagetocheck) {
                $confirm="&amp;confirm=".$form->pagetocheck;
                echo $OUTPUT->confirm(get_string("checklinkscheck", "wiki").$form->pagetocheck, $link.$confirm, $link);
                echo $OUTPUT->footer();
                exit;
              }
            }
            break;
        case "setpageflags":
            // pageflagstatus is used in setpageflags.html
            $pageflagstatus=wiki_admin_setpageflags($form->flags);
            break;
        case "revertpages":
              if(!$err) {
                if(!$confirm) {
                  $confirm="&confirm[changesfield]=".urlencode($form->changesfield).
                           "&confirm[authorfieldpattern]=".urlencode($form->authorfieldpattern).
                           "&confirm[howtooperate]=".urlencode($form->howtooperate).
                           "&confirm[deleteversions]=".urlencode($form->deleteversions);
                  $revertedpages=wiki_admin_revert("", $form->authorfieldpattern, $form->changesfield, $form->howtooperate, $form->deleteversions);
                  if($revertedpages) {
                    echo $OUTPUT->confirm(get_string("revertpagescheck", "wiki")."<br />".$revertedpages, $link.$confirm, $link);
                    echo $OUTPUT->footer();
                    exit;
                  } else {
                    $err->remark=get_string("nochangestorevert","wiki");
                  }
                }
              }
            break;
        default: print_error('unknowaction');
          break;
      }
    }

    /// Actions which need a confirmation. If confirmed, do the action
    $redirect="view.php?".($groupid?"&amp;groupid=".$groupid:"").($userid?"&amp;userid=".$userid:"")."&amp;id=$id&amp;page=$page";
    if($confirm && !$err) {
      switch($action) {
        case "removepages":
           $ret=wiki_admin_remove(split(" ",$confirm), $course, $wiki, $userid, $groupid);
           if(!$ret) {
             redirect($redirect, get_string("pagesremoved","wiki"), 1);
           } else {
             print_error('invalidaction');
           }
           exit;
        case "strippages":
           $strippages=array();
           foreach($confirm as $pageid => $versions) {
             $strippages[$pageid]=split(" ",$versions);
           }
           $ret=wiki_admin_strip($strippages);
           if(!$ret) {
             redirect($redirect, get_string("pagesstripped","wiki"), 1);
           } else {
             print_error('invalidaction');
           }
           exit;
        case "checklinks":
           $ret=wiki_admin_checklinks($confirm);
           redirect($redirect, get_string("linkschecked","wiki")."<br />".$ret, 5);
           exit;
        case "revertpages":
           $revertedpages=wiki_admin_revert(1, $confirm["authorfieldpattern"], $confirm["changesfield"], $confirm["howtooperate"], $confirm["deleteversions"]);
           redirect($redirect, get_string("pagesreverted","wiki"), 1);
           exit;
        case "setpageflags":
           # No confirmation needed
           break;
        default: print_error('unknowaction');
      }
    }


    /// The top row contains links to other wikis, if applicable.
    if ($wiki_list = wiki_get_other_wikis($wiki, $USER, $course, $wiki_entry->id)) {
        if (isset($wiki_list['selected'])) {
            $selected = $wiki_list['selected'];
            unset($wiki_list['selected']);
        }
        echo '<tr><td colspan="2">';

        echo '<form id="otherwikis" action="'.$CFG->wwwroot.'/mod/wiki/admin.php">';
        echo '<fieldset class="invisiblefieldset">';
        echo '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
        echo '<td class="sideblockheading">&nbsp;'
            .$WIKI_TYPES[$wiki->wtype].' '
            .get_string('modulename', 'wiki').' for '
            .wiki_get_owner($wiki_entry).':</td>';

        echo '<td class="sideblockheading" align="right">'
            .get_string('otherwikis', 'wiki').':&nbsp;&nbsp;';
        $script = 'self.location=getElementById(\'otherwikis\').wikiselect.options[getElementById(\'otherwikis\').wikiselect.selectedIndex].value';

        /// Add Admin-Action
        reset($wiki_list);
        $wiki_admin_list=array();
        while(list($key,$val)=each($wiki_list)) {
          $wiki_admin_list[$key."&amp;action=$action"]=$val;
        }
        $attributes = array('id'=>'changeid');
        echo html_writer::select($wiki_admin_list, 'wikiselect', $selected, array(''=>'choose'), $attributes);
        $PAGE->add_action_handler('changeid', new component_action('change', 'go_to_wiki'));
        echo '</td>';
        echo '</tr></table>';
        echo '</fieldset></form>';

        echo '</td>';
        echo '</tr>';
    }

    if ($wiki_entry) {


        /// Page Actions
        echo '<table border="0" width="100%">';
        echo '<tr>';
/*        echo '<tr><td align="center">';
*        $specialpages=array("SearchPages", "PageIndex","NewestPages","MostVisitedPages","MostOftenChangedPages","UpdatedPages","FileDownload","FileUpload","OrphanedPages","WantedPages");
*        wiki_print_page_actions($cm->id, $specialpages, $ewiki_id, $ewiki_action, $wiki->ewikiacceptbinary, $canedit);
*        echo '</td>';*/

        /// Searchform
        echo '<td align="center">';
        wiki_print_search_form($cm->id, $q, $userid, $groupid, false);
        echo '</td>';

        /// Internal Wikilinks

        /// TODO: DOES NOT WORK !!!!
        echo '<td align="center">';
        wiki_print_wikilinks_block($cm->id,  $wiki->ewikiacceptbinary);
        echo '</td>';

        /// Administrative Links
        echo '<td align="center">';
        wiki_print_administration_actions($wiki, $cm->id, $userid, $groupid, $page, $wiki->htmlmode!=2, $course);
        echo '</td>';

/**        if($wiki->htmlmode!=2) {
*          echo '<td align="center">';
*          helpbutton('formattingrules', get_string('formattingrules', 'wiki'), 'wiki');
*          echo get_string("formattingrules","wiki");
*          echo '</td>';
*        }*/

        echo '</tr></table>';
    }

    // The wiki Contents
    echo $OUTPUT->box_start();
    // Do the Action
    # "setpageflags", "removepages", "strippages", "checklinks", "revertpages"

    echo $OUTPUT->heading_with_help(get_string($action,"wiki"), $action, 'wiki');

    include $action.".html";
    echo $OUTPUT->box_end();

/// Finish the page
    echo $OUTPUT->footer();
    exit;


