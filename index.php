<?  // $Id$
    // index.php - the front page.
    
    require("config.php");
    include("course/lib.php");
    include("mod/reading/lib.php"); 
    include("mod/forum/lib.php");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/");
    }

    if (isadmin()) {
        if (moodle_needs_upgrading()) {
            redirect("$CFG->wwwroot/admin/");
        }
        $headerbutton = update_course_icon($site->id);
    } else {
        if (isset($USER->id)) {
            $headerbutton = "<FONT SIZE=2><A HREF=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</A></FONT>";
        } else {
            $headerbutton = "<FONT SIZE=2><A HREF=\"$CFG->wwwroot/login/\">".get_string("login")."</A></FONT>";
        }
    }
    print_header("$site->fullname", "$site->fullname", "", "",
                 "<META NAME=\"Description\" CONTENT=\"".stripslashes(strip_tags($site->summary))."\">",
                 true, $headerbutton);

    $side = 180;

?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
  <? 
     $sections = get_all_sections($site->id);
  
     if ($site->newsitems > 0 or $sections[0]->sequence or isediting($site->id)) {

         echo "<TD WIDTH=\"$side\" VALIGN=TOP NOWRAP>"; $firstcolumn=true;
  
         if ($sections[0]->sequence or isediting($site->id)) {
             get_all_mods($site->id, $mods, $modnames, $modnamesplural, $modnamesused);
             print_simple_box(get_string("mainmenu"), "CENTER", $side, "$THEME->cellheading");
         }   

         if ($sections[0]->sequence) {
             print_section($site->id, $sections[0], $mods, $modnamesused, true, $side);
         }

         if (isediting($site->id)) {
             echo "<DIV ALIGN=right>";
             popup_form("$CFG->wwwroot/course/mod.php?id=$site->id&section=0&add=", 
                         $modnames, "section0", "", get_string("add")."...");
             echo "</DIV>";
         }

         if ($site->newsitems > 0 ) {
             print_simple_box(get_string("courses"), "CENTER", $side, "$THEME->cellheading");
             print_all_courses($cat=1, "minimal", 10);
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
         print_simple_box(get_string("availablecourses"), "CENTER", "100%", "$THEME->cellheading");
         print_spacer(8,1);
         print_all_courses();

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
         print_simple_box($headertext, "CENTER", "100%", $THEME->cellheading);
         print_spacer(8,1);
         forum_print_latest_discussions($newsforum->id, $site->newsitems);
     }

     echo "</TD>";
     echo "<TD WIDTH=30% VALIGN=TOP>";
     
     if (isediting($site->id)) {
         $site->summary .= "<BR><CENTER><A HREF=\"admin/site.php\"><IMG SRC=\"pix/i/edit.gif\" BORDER=0></A>";
     }

     print_simple_box($site->summary, "", "100%", $THEME->cellheading);
     print_spacer(1,$side);
     echo "</TD>";
  ?>

  </TR>
</TABLE>

<? print_footer("home");     // Please do not modify this line ?>

