<?  // $Id$
    // index.php - the front page.
    
    require("config.php");
    include("course/lib.php");
    include("mod/reading/lib.php"); 
    include("mod/forum/lib.php");

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/");
    }

    if (isset($USER->id)) {
        $headerbutton = update_course_icon($site->id);
    } else {
        $headerbutton = "<FONT SIZE=2><A HREF=\"login/\">".get_string("login")."</A></FONT>";
    }
    print_header("$site->fullname", "$site->fullname", "", "",
                 "<META NAME=\"Description\" CONTENT=\"".stripslashes(strip_tags($site->summary))."\">",
                 true, $headerbutton);


?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
    <TD VALIGN="TOP" NOWRAP>
      <? 

         $sections = get_all_sections($site->id);
      
         if ($site->newsitems > 0 or $sections[0]->sequence or isediting($site->id)) {
      
             if ($site->newsitems > 0 ) {
                 print_simple_box(get_string("courses"), "CENTER", "100%", "$THEME->cellheading");

                 print_all_courses($cat=1, "minimal", 10);
             } 

             if ($sections[0]->sequence or isediting($site->id)) {
                 get_all_mods($site->id, $mods, $modnames, $modnamesplural, $modnamesused);
                 print_simple_box(get_string("mainmenu"), "CENTER", "100%", "$THEME->cellheading");
             }   

             if ($sections[0]->sequence) {
                 print_section($site->id, $sections[0], $mods, $modnamesused, true);
             }

             if (isediting($site->id)) {
                 echo "<DIV ALIGN=right>";
                 popup_form("$CFG->wwwroot/course/mod.php?id=$site->id&section=0&add=", 
                             $modnames, "section0", "", "Add...");
                 echo "</DIV>";
             }
         }
     
         if (isadmin()) {
             print_simple_box(get_string("administration"), $align="CENTER", $width="100%", $color="$THEME->cellheading");
             $icon = "<IMG SRC=\"pix/i/settings.gif\" HEIGHT=16 WIDTH=16 ALT=\"\">";
             $moddata[]="<A HREF=\"course/log.php?id=$site->id\">".get_string("sitelogs")."</A>";
             $modicon[]=$icon;
             $moddata[]="<A HREF=\"admin/site.php\">".get_string("sitesettings")."</A>";
             $modicon[]=$icon;
             $moddata[]="<A HREF=\"course/edit.php\">".get_string("addnewcourse")."</A>";
             $modicon[]=$icon;
             $moddata[]="<A HREF=\"course/teacher.php\">".get_string("assignteachers")."</A>";
             $modicon[]=$icon;
             $moddata[]="<A HREF=\"course/delete.php\">".get_string("deletecourse")."</A>";
             $modicon[]=$icon;
             $moddata[]="<A HREF=\"admin/user.php\">".get_string("edituser")."</A>";
             $modicon[]=$icon;
             print_side_block("", $moddata, "", $modicon);
         }
      ?>

    </TD>

    <TD WIDTH="70%" VALIGN="TOP">
      <? if ($site->newsitems == 0 ) {
             print_simple_box(get_string("availablecourses"), "CENTER", "100%", "$THEME->cellheading");
             echo "<IMG HEIGHT=8 SRC=\"pix/spacer.gif\" ALT=\"\"><BR>";
             print_all_courses();

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
                 $headertext = "<TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR>
                                <TD>".get_string("sitenews")."</TD>
                                <TD ALIGN=RIGHT><FONT SIZE=1>
                                <A HREF=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</A>
                                </TD></TR></TABLE>";
             } else {
                 $headertext = get_string("sitenews");
             }
             print_simple_box($headertext, "CENTER", "100%", $THEME->cellheading);
             echo "<IMG HEIGHT=8 SRC=\"pix/spacer.gif\" ALT=\"\"><BR>";
             forum_print_latest_discussions($newsforum->id, $site->newsitems);
         }
      ?>

    </TD>
    <TD WIDTH="30%" VALIGN="TOP"> 
      <? 
         if (isediting($site->id)) {
             $site->summary .= "<BR><CENTER><A HREF=\"admin/site.php\"><IMG SRC=\"pix/i/edit.gif\" BORDER=0></A>";
         }
         print_simple_box($site->summary, "", "100%", $THEME->cellheading);
      ?>
    </TD>
  </TR>
</TABLE>

<? include("$CFG->dirroot/theme/$CFG->theme/footer.html"); ?>

<P ALIGN=center>
<A WIDTH=85 HEIGHT=25 HREF="http://moodle.com/"><IMG SRC="pix/madewithmoodle.gif" BORDER=0></A>
</P>

