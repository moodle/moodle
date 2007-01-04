<?PHP // $Id$

    require('../config.php');

    if ($form = data_submitted('nomatch')) { // form submitted, do not check referer (origal page unknown)!
        if (!$admin = get_admin() ) {
            error('Could not find the admin user to mail to!');
        }

        if (empty($USER->id)) {
            $user = getremoteaddr(); // user not logged in, use IP address as name
        } else {
            $user = $USER;
        }
        email_to_user($admin, $user, 'Error: '. $form->referer .' -> '. $form->requested, $form->text);

        redirect($CFG->wwwroot .'/course/', 'Message sent, thanks', 3);
        die;
    }

    $site = get_site();
    $redirecturl = empty($_SERVER['REDIRECT_URL']) ? '' : $_SERVER['REDIRECT_URL'];
    $httpreferer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
    $requesturi  = empty($_SERVER['REQUEST_URI'])  ? '' : $_SERVER['REQUEST_URI'];
    
    print_header($site->fullname .':Error', $site->fullname .': Error 404', '', 'text');
    print_simple_box('<p align="center">'. get_string('pagenotexist', 'error'). '<br />'.s($requesturi).'</p>', 'center');
  
?>
  
  <center>
  <p><?php echo get_string('pleasereport', 'error'); ?>
  <p><form action="<?php echo $CFG->wwwroot ?>/error/index.php" method="post">
     <textarea rows="3" cols="50" name="text" id="text"></textarea><br />
     <input type="hidden" name="referer" value="<?php p($httpreferer) ?>">
     <input type="hidden" name="requested" value="<?php p($requesturi) ?>">
     <input type="submit" value="<?php echo get_string('sendmessage', 'error'); ?>">
     </form>
<?php

  print_footer();

?>
