<?  // $Id$
    // index.php - the front page.
    
    require("config.php");
    include("course/lib.php");
    include("mod/reading/lib.php"); 
    include("mod/forum/lib.php");

    if (! $site = get_record("course", "category", 0)) {
        redirect("$CFG->wwwroot/admin/");
    }

    if ($USER) {
        $headerbutton = update_course_icon($site->id);
    } else {
        $headerbutton = "<FONT SIZE=2><A HREF=\"login/\">Log in</A></FONT>";
    }
    print_header("$site->fullname", "$site->fullname", "", "",
                 "<META NAME=\"Description\" CONTENT=\"".stripslashes(strip_tags($site->summary))."\">",
                 true, $headerbutton);


?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
    <TD VALIGN="TOP" NOWRAP>
      <? $readings = list_all_readings();
      
         if ($site->format > 0 or $readings or $USER->editing) {
      
             print_simple_box("Main Menu", $align="CENTER", $width="100%", $color="$THEME->cellheading");

             if ($site->format > 0 ) {
                 echo "<LI><A TITLE=\"Available courses on this server\" HREF=\"course/\"><B>Courses</B></A><BR></LI>";
             } 

             if ($readings) {
                 foreach ($readings as $reading) {
	             echo "<LI>$reading";
                 }
             }
             if ($USER->editing) {
                 echo "<P align=right><A HREF=\"$CFG->wwwroot/course/mod.php?id=$site->id&week=0&add=reading\">Add Reading</A>...</P>";
             } else {
                 echo "<BR><BR>";
             }
         }
     

         if (isadmin()) {
             print_simple_box("Admin", $align="CENTER", $width="100%", $color="$THEME->cellheading");
             echo "<LI><A HREF=\"$CFG->wwwroot/admin/\">Admin Page...</A></LI>";
             echo "<LI><A HREF=\"$CFG->wwwroot/course/log.php?id=$site->id\">Site logs...</A></LI>";
             echo "<LI><A HREF=\"$CFG->wwwroot/admin/site.php\">Site settings...</A></LI>";
         }
      ?>

    </TD>

    <TD WIDTH="70%" VALIGN="TOP">
      <? if ($site->format == 0 ) {
             print_simple_box("Available Courses", $align="CENTER", $width="100%", $color="$THEME->cellheading");
             echo "<IMG HEIGHT=8 SRC=\"pix/spacer.gif\" ALT=\"\"><BR>";
             print_all_courses();

         } else {
             if (! $newsforum = get_course_news_forum($site->id)) {
                 error("Could not find or create a main forum for the site");
             }

             if ($USER) {
                 $SESSION->fromdiscuss = "$CFG->wwwroot";
                 if (is_subscribed($USER->id, $newsforum->id)) {
                     $subtext = "Unsubscribe from news";
                 } else {
                     $subtext = "Subscribe me by mail";
                 }
                 $headertext = "<TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR>
                                <TD>Site News</TD>
                                <TD ALIGN=RIGHT><FONT SIZE=1>
                                <A HREF=\"mod/forum/subscribe.php?id=$newsforum->id\">$subtext</A>
                                </TD></TR></TABLE>";
             } else {
                 $headertext = "Site News";
             }
             print_simple_box("$headertext", $align="CENTER", $width="100%", $color="$THEME->cellheading");
             echo "<IMG HEIGHT=8 SRC=\"pix/spacer.gif\" ALT=\"\"><BR>";
             forum_latest_topics($newsforum->id, $site->format);
         }
      ?>

    </TD>
    <TD WIDTH="30%" VALIGN="TOP"> 
      <? 
         if ($USER->editing) {
             $site->summary .= "<BR><CENTER><A HREF=\"admin/site.php\"><IMG SRC=\"pix/i/edit.gif\" BORDER=0></A>";
         }
         print_simple_box($site->summary, $align="", $width="100%", $color="$THEME->cellheading");
      ?>
    </TD>
  </TR>
</TABLE>

<? include("$CFG->dirroot/theme/$CFG->theme/footer.html"); ?>

<P ALIGN=center>
<A WIDTH=85 HEIGHT=25 HREF="http://moodle.com/"><IMG SRC="pix/madewithmoodle.gif" BORDER=0></A>
</P>

