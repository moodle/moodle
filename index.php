<?  // $Id$
    // index.php - the front page.
    
    require("config.php");

    if (! $site = get_record("course", "category", 0)) {
        redirect("$CFG->wwwroot/admin/");
    }

    print_header("$site->fullname", "$site->fullname", "", "",
                 "<META NAME=\"Description\" CONTENT=\"$site->summary\">");


?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="5">
  <TR>
    <TD WIDTH="15%" VALIGN="TOP" NOWRAP>
      <? print_simple_box("Main Menu", $align="CENTER", $width="100%", $color="$THEME->cellheading"); ?>

	  <LI>Home</LI>
      <LI><A TITLE="Available courses on this server" HREF="course/"><B>Courses</B></A><BR></LI>
      <LI><A TITLE="Site-level Forums" HREF="mod/discuss/index.php?id=<?=$site->id?>">Forums</A></LI>

      <? include("mod/reading/lib.php"); 
         list_all_readings();
      ?>

    </TD>

    <TD WIDTH="55%" VALIGN="TOP">
      <? print_simple_box("Site News", $align="CENTER", $width="100%", $color="$THEME->cellheading"); ?>

      <BR>

      <? include("mod/discuss/lib.php");
         forum_latest_topics();
      ?>
    
    </TD>
    <TD WIDTH="30%" VALIGN="TOP"> 
      <? print_simple_box($site->summary, $align="", $width="100%", $color="$THEME->cellheading"); ?>
    </TD>
  </TR>
</TABLE>

<CENTER><P>
<? print_editing_switch($site->id); ?>
</P><CENTER>

<? include("$CFG->dirroot/theme/$CFG->theme/footer.html"); ?>

<P ALIGN=center>
<A WIDTH=85 HEIGHT=25 HREF="http://moodle.com/"><IMG SRC="pix/madewithmoodle.gif" BORDER=0></A>
</P>

