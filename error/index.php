<?PHP // $Id$

    require("../config.php");

    if (isset($text)) {    // form submitted
        if (!$admin = get_admin() ) {
            error("Could not find the admin user to mail to!");
        }

        email_to_user($admin, $USER, "Error: $referer -> $requested", "$text");

        redirect("$CFG->wwwroot/course/", "Message sent, thanks", 3);
        die;
    }
    
    print_header("$CFG->sitename:Error", "$CFG->sitename: Error 404", "", "form.text");

    print_simple_box("An unusual error occurred (tried to reach a page that doesn't exist).<P align=center>$REQUEST_URI", "center", "", "$THEME->cellheading");
  
?>
  
  <CENTER>
  <P>If you have time, please let us know what you were trying 
     to do when the error occurred:
  <P><FORM action="<?=$CFG->wwwroot ?>/error/index.php" name=form method=post>
     <TEXTAREA ROWS=3 COLS=50 NAME=text></TEXTAREA><BR>
     <INPUT TYPE=hidden NAME=referer VALUE="<?=$HTTP_REFERER ?>">
     <INPUT TYPE=hidden NAME=requested VALUE="<?=$REQUEST_URI ?>">
     <INPUT TYPE=submit VALUE="Send this off">
     </FORM>
<?

  print_footer();

?>
