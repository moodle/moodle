<?PHP // $Id$

    require('../config.php');

    if (!empty($text)) {    // form submitted
        if (!$admin = get_admin() ) {
            error('Could not find the admin user to mail to!');
        }

        email_to_user($admin, $USER, 'Error: '. $referer .' -> '. $requested, $text);

        redirect($CFG->wwwroot .'/course/', 'Message sent, thanks', 3);
        die;
    }

    $site = get_site();
    $redirecturl = empty($_SERVER['REDIRECT_URL']) ? '' : $_SERVER['REDIRECT_URL'];
    $httpreferer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
    $requesturi  = empty($_SERVER['REQUEST_URI'])  ? '' : $_SERVER['REQUEST_URI'];
    
    print_header($site->fullname .':Error', $site->fullname .': Error 404', '', 'form.text');
    print_simple_box('<p align="center">An unusual error occurred (tried to reach a page that doesn\'t exist).<br />'.s($redirecturl).'</p>', 'center');
  
?>
  
  <center>
  <p>If you have time, please let us know what you were trying 
     to do when the error occurred:
  <p><form action="<?php echo $CFG->wwwroot ?>/error/index.php" name="form" method="post">
     <textarea rows="3" cols="50" name="text"></textarea><br />
     <input type="hidden" name="referer" value="<?php p($httpreferer) ?>">
     <input type="hidden" name="requested" value="<?php p($requesturi) ?>">
     <input type="submit" value="Send this off">
     </form>
<?php

  print_footer();

?>