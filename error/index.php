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

    $site = get_site();
    
    print_header("$site->fullname:Error", "$site->fullname: Error 404", "", "form.text");

    print_simple_box("An unusual error occurred (tried to reach a page that doesn't exist).<P align=center>$REDIRECT_URL", "center", "", "$THEME->cellheading");
  
?>
  
  <CENTER>
  <P>If you have time, please let us know what you were trying 
     to do when the error occurred:
  <P><FORM action="<?php echo $CFG->wwwroot ?>/error/index.php" name=form method=post>
     <TEXTAREA ROWS=3 COLS=50 NAME=text></TEXTAREA><BR>
     <INPUT TYPE=hidden NAME=referer VALUE="<?php echo $HTTP_REFERER ?>">
     <INPUT TYPE=hidden NAME=requested VALUE="<?php echo $REQUEST_URI ?>">
     <INPUT TYPE=submit VALUE="Send this off">
     </FORM>
<?php

  print_footer();

?>
