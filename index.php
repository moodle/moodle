<?PHP  // $Id$
       // index.php - the front page.
    
    require_once("config.php");
    require_once("course/lib.php");
    require_once("mod/resource/lib.php"); 
    require_once("mod/forum/lib.php");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if (isadmin()) {
        if (moodle_needs_upgrading()) {
            redirect("$CFG->wwwroot/$CFG->admin/index.php");
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
        $langmenu = popup_form ("$CFG->wwwroot/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    print_header(strip_tags($site->fullname), "$site->fullname", "home", "",
                 "<meta name=\"description\" content=\"".s(strip_tags($site->summary))."\">",
                 true, "", "$loginstring$langmenu");

    $firstcolumn = false;  // for now
    $lastcolumn = false;   // for now
    $side = 175;

    $site_summary_editbuttons = '';
    if (isediting($site->id)) {
        $site_summary_editbuttons = "<br><center><a href=\"$CFG->admin/site.php\"><img src=\"pix/i/edit.gif\" border=0></a>";
    }

    if ($site->summary) {
        $lastcolumn = true;
    }


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

         switch ($CFG->frontpage) {
             case FRONTPAGENEWS:       // print news links on the side
                 print_courses_sideblock(0, "$side");
             break;
    
             case FRONTPAGECOURSELIST:
             case FRONTPAGECATEGORYNAMES:
                 if ($site->newsitems) {
                     if ($news = forum_get_course_forum($site->id, "news")) {
                         print_side_block_start(get_string("latestnews"), $side, "sideblocklatestnews");
                         echo "<font size=\"-2\">";
                         forum_print_latest_discussions($news->id, $site->newsitems, "minimal", "", false);
                         echo "</font>";
                         print_side_block_end();
                     }
                 }
             break;
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
     if ($lastcolumn) {
         echo "<td width=\"70%\" valign=\"top\">";
     } else {
         echo "<td width=\"100%\" valign=\"top\">";
     }

     switch ($CFG->frontpage) {     /// Display the main part of the front page.
         case FRONTPAGENEWS:
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
                 $headertext = "<table border=0 width=100% cellpadding=0 cellspacing=0 class=headingblockcontent><tr>
                                <td>$newsforum->name</td>
                                <td align=right><font size=1>
                                <a href=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</a>
                                </td></tr></table>";
             } else {
                 $headertext = $newsforum->name;
             }
             print_heading_block($headertext);
             print_spacer(8,1);
             forum_print_latest_discussions($newsforum->id, $site->newsitems);
         break;
    
         case FRONTPAGECOURSELIST:
         case FRONTPAGECATEGORYNAMES:
             if (isset($USER->id) and !isset($USER->admin)) {
                 print_heading_block(get_string("mycourses"));
                 print_spacer(8,1);
                 print_my_moodle();
             } else {
                 if (count_records("course_categories") > 1) {
                     if ($CFG->frontpage == FRONTPAGECOURSELIST) {
                         print_heading_block(get_string("availablecourses"));
                     } else {
                         print_heading_block(get_string("categories"));
                     }
                     print_spacer(8,1);
                     print_simple_box_start("center", "100%");
                     print_whole_category_list();
                     print_simple_box_end();
                     print_course_search("", false, "short");
                 } else {
                     print_heading_block(get_string("availablecourses"));
                     print_spacer(8,1);
                     print_courses(0, "100%");
                 }
             }
         break;

     }

     echo "</td>";

     if ($lastcolumn) {
         echo "<td width=\"30%\" valign=\"top\">";
         print_simple_box(format_text($site->summary, FORMAT_HTML).$site_summary_editbuttons, 
                          "", "100%", $THEME->cellcontent2, 5, "siteinfo");
         print_spacer(1,$side);
         echo "</td>";
     }
  ?>

  </tr>
</table>

<?PHP print_footer("home");     // Please do not modify this line ?>

