<?  // $Id$
    // index.php - the front page.
    
    require("config.php");
    include("mod/reading/lib.php"); 

    if (! $site = get_record("course", "category", 0)) {
        redirect("$CFG->wwwroot/admin/");
    }

    print_header("$site->fullname", "$site->fullname", "", "",
                 "<META NAME=\"Description\" CONTENT=\"".stripslashes(strip_tags($site->summary))."\">");


?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
    <TD WIDTH="15%" VALIGN="TOP" NOWRAP>
      <? print_simple_box("Main Menu", $align="CENTER", $width="100%", $color="$THEME->cellheading"); ?>

	  <LI>Home</LI>
      <LI><A TITLE="Available courses on this server" HREF="course/"><B>Courses</B></A><BR></LI>
      <LI><A TITLE="Site-level Forums" HREF="mod/discuss/index.php?id=<?=$site->id?>">Forums</A></LI>

      <? 
         if ($readings = list_all_readings()) {
             foreach ($readings as $reading) {
	         echo "<LI>$reading";
             }
         }
     
         if ($USER->editing) {
             echo "<P align=right><A HREF=\"$CFG->wwwroot/course/mod.php?id=$site->id&week=0&add=reading\">Add Reading</A>...</P>";
         }
      ?>

        <BR><BR>

      <?
         if (isadmin()) {
             print_simple_box("Admin", $align="CENTER", $width="100%", $color="$THEME->cellheading");
             echo "<LI><A HREF=\"$CFG->wwwroot/admin/\">Admin Page</A></LI>";
             echo "<LI>";
             print_editing_switch($site->id); 
         }
      ?>

    </TD>

    <TD WIDTH="55%" VALIGN="TOP">
      <? print_simple_box("Site News", $align="CENTER", $width="100%", $color="$THEME->cellheading"); ?>

      <IMG HEIGHT=8 SRC="pix/spacer.gif" ALT=""><BR>

      <? include("mod/discuss/lib.php");
         forum_latest_topics();
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

