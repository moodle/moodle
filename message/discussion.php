<?php // $Id$
      
    require('../config.php');
    require('lib.php');

    require_login();

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");
    }

/// Script parameters
    $userid = required_param('id', PARAM_INT);

/// Check the user we are talking to is valid
    if (! $user = get_record('user', 'id', $userid)) {
        error("User ID was incorrect");
    }

/// Select encoding
    if (!empty($CFG->unicode)) {
        $encoding = 'utf-8';
    } else {
        $encoding = get_string('thischarset');
    }
/// Print frameset to contain all the various panes
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
    <html>
     <head>
       <meta http-equiv="content-type" content="text/html; charset=<?php echo $encoding ?>" />
       <title><?php echo get_string('discussion', 'message').': '.fullname($user) ?></title>
     </head>
     <frameset rows="110,*,0,200" border="0" marginwidth="2" marginheight="1">
       <frame src="user.php?id=<?php p($user->id)?>&amp;frame=user"     name="user"     
              scrolling="no"  marginwidth="0" marginheight="">
       <frame src="messages.php"  name="messages" 
              scrolling="yes" marginwidth="10" marginheight="10">
       <frame src="refresh.php?id=<?php p($user->id)?>&amp;name=<?php echo urlencode(fullname($user)) ?>"  name="refresh" 
              scrolling="no"  marginwidth="0" marginheight="0">
       <frame src="send.php?id=<?php p($user->id)?>"     name="send" 
              scrolling="no"  marginwidth="2" marginheight="2">
     </frameset>
     <noframes>Sorry, but support for Frames is required to use Messaging</noframes>

    </html>
