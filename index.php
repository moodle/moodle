<?PHP  // $Id$
       // index.php - the front page.
    
    require_once("config.php");
    require_once("course/lib.php");
    require_once("mod/resource/lib.php"); 
    require_once("mod/forum/lib.php");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/index.php");
    }

    if (isadmin()) {
        if (moodle_needs_upgrading()) {
            redirect("$CFG->wwwroot/admin/index.php");
        }
    }

    if (empty($USER->id)) {
        $loginstring = "<font size=2><a href=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</a></font>";
    } else {
        $loginstring = "<font size=1>".user_login_string($site)."</font>";
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    print_header(strip_tags($site->fullname), "$site->fullname", "home", "",
                 "<meta name=\"description\" content=\"".s(strip_tags($site->summary))."\">",
                 true, "", "<div align=right>$loginstring$langmenu</div>");

    $firstcolumn = false;  // for now
    $side = 175;

?>


<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
  <?PHP 
     $sections = get_all_sections($site->id);
  
     if ($site->newsitems > 0 or $sections[0]->sequence or isediting($site->id) or isadmin()) {

         echo "<td width=\"$side\" valign=top nowrap>"; 
         $firstcolumn=true;
  
         if ($sections[0]->sequence or isediting($site->id)) {
             get_all_mods($site->id, $mods, $modnames, $modnamesplural, $modnamesused);
             print_section_block(get_string("mainmenu"), $site, $sections[0], 
                                 $mods, $modnames, $modnamesused, true, $side);
         }

         if (isadmin()) {
             echo "<div align=\"center\">".update_course_icon($site->id)."</div>";
             echo "<br />";
         }

         if ($site->newsitems > 0 ) {
             $categories = get_categories();
             if (count($categories) > 1) {
                 print_course_categories($categories, "none", $side);
             } else {
                 $category = array_shift($categories);
                 print_all_courses($category->id, "minimal", 10, $side);
             }
         } 
         print_spacer(1,$side);
     }
 
     if (iscreator()) {
         if (!$firstcolumn) {
             echo "<td width=\"$side\" valign=top nowrap>"; 
             $firstcolumn=true;
         }
         print_admin_links($site->id, $side);
     }

     if ($firstcolumn) {
         echo "</td>";
     }
     echo "<td width=\"70%\" valign=\"top\">";

     if ($site->newsitems == 0 ) {
         print_heading_block(get_string("availablecourses"));
         print_spacer(8,1);
         $categories = get_categories();
         if (count($categories) > 1) {
             print_course_categories($categories, "index");
         } else {
             print_all_courses("all");
         }

     } else {
         if (! $newsforum = forum_get_course_forum($site->id, "news")) {
             error("Could not find or create a main news forum for the site");
         }

         if (isset($USER->id)) {
             $SESSION->fromdiscussion = "$CFG->wwwroot";
             if (forum_is_subscribed($USER->id, $newsforum->id)) {
                 $subtext = get_string("unsubscribe", "forum");
             } else {
                 $subtext = get_string("subscribe", "forum");
             }
             $headertext = "<table border=0 align=right cellpadding=0 cellspacing=0><tr>
                            <td align=right><font size=1>
                            <a href=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</a>
                            </td></tr></table>$newsforum->name";
         } else {
             $headertext = $newsforum->name;
         }
         print_heading_block($headertext);
         print_spacer(8,1);
         forum_print_latest_discussions($newsforum->id, $site->newsitems);
     }

     echo "</td>";
     echo "<td width=30% valign=top>";
     
     if (isediting($site->id)) {
         $site->summary .= "<br><center><a href=\"admin/site.php\"><img src=\"pix/i/edit.gif\" border=0></a>";
     }

     print_simple_box($site->summary, "", "100%", $THEME->cellcontent2, 5, "siteinfo");
     print_spacer(1,$side);
     echo "</td>";
  ?>

  </tr>
</table>

<?PHP print_footer("home");     // Please do not modify this line ?>

