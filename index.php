<?PHP  // $Id$
       // index.php - the front page.
    
    require("config.php");
    require("course/lib.php");
    require("mod/resource/lib.php"); 
    require("mod/forum/lib.php");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/index.php");
    }

    if (isadmin()) {
        if (moodle_needs_upgrading()) {
            redirect("$CFG->wwwroot/admin/index.php");
        }
        $headerbutton = update_course_icon($site->id);
    } else {
        if (isset($USER->id)) {
            $headerbutton = "<FONT SIZE=2><A HREF=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</A></FONT>";
        } else {
            $headerbutton = "<FONT SIZE=2><A HREF=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</A></FONT>";
        }
    }
    print_header("$site->fullname", "$site->fullname", "home", "",
                 "<META NAME=\"Description\" CONTENT=\"".stripslashes(strip_tags($site->summary))."\">",
                 true, "", $headerbutton);

    $side = 180;

?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
  <?PHP 
     $sections = get_all_sections($site->id);
  
     if ($site->newsitems > 0 or $sections[0]->sequence or isediting($site->id)) {

         echo "<TD WIDTH=\"$side\" VALIGN=TOP NOWRAP>"; $firstcolumn=true;
  
         if ($sections[0]->sequence or isediting($site->id)) {
             get_all_mods($site->id, $mods, $modnames, $modnamesplural, $modnamesused);
             print_section_block(get_string("mainmenu"), $site, $sections[0], 
                                 $mods, $modnames, $modnamesused, true, $side, isediting($site->id));
         }

         if ($site->newsitems > 0 ) {
             $categories = get_all_categories();
             if (count($categories) > 1) {
                 print_course_categories($categories, "none", $side);
             } else {
                 $category = array_shift($categories);
                 print_all_courses($category->id, "minimal", 10);
             }
         } 
         print_spacer(1,$side);
     }
 
     if (isadmin()) {
         if (!$firstcolumn) {
             echo "<TD WIDTH=\"$side\" VALIGN=TOP NOWRAP>"; $firstcolumn=true;
         }
         print_admin_links($site->id, $side);
     }

     if ($firstcolumn) {
         echo "</TD>";
     }
     echo "<TD WIDTH=70% VALIGN=TOP>";

     if ($site->newsitems == 0 ) {
         print_heading_block(get_string("availablecourses"));
         print_spacer(8,1);
         $categories = get_all_categories();
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
             save_session("SESSION");
             if (forum_is_subscribed($USER->id, $newsforum->id)) {
                 $subtext = get_string("unsubscribe", "forum");
             } else {
                 $subtext = get_string("subscribe", "forum");
             }
             $headertext = "<TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR>
                            <TD>".get_string("sitenews")."</TD>
                            <TD ALIGN=RIGHT><FONT SIZE=1>
                            <A HREF=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</A>
                            </TD></TR></TABLE>";
         } else {
             $headertext = get_string("sitenews");
         }
         print_heading_block($headertext);
         print_spacer(8,1);
         forum_print_latest_discussions($newsforum->id, $site->newsitems);
     }

     echo "</TD>";
     echo "<TD WIDTH=30% VALIGN=TOP>";
     
     if (isediting($site->id)) {
         $site->summary .= "<BR><CENTER><A HREF=\"admin/site.php\"><IMG SRC=\"pix/i/edit.gif\" BORDER=0></A>";
     }

     print_simple_box($site->summary, "", "100%", $THEME->cellcontent2);
     print_spacer(1,$side);
     echo "</TD>";
  ?>

  </TR>
</TABLE>

<?PHP print_footer("home");     // Please do not modify this line ?>

