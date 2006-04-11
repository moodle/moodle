<?php // $Id$

/**
If you're using custompix in your theme, but you don't want to have to copy every pix from /pix into /theme/yourtheme/pix, use this as a 404 handler.
You need to put a snippet like the following into your apacheconfig:

<Location /moodle/theme/yourtheme/pix >
   ErrorDocument 404 /moodle/pix/redirect.php
</Location>

**/


require_once('../config.php');

// obtain the requested path.
if (!array_key_exists('REDIRECT_STATUS',$_SERVER) || $_SERVER['REDIRECT_STATUS'] != 404) {
    die();
}

$matches = array();

if (!preg_match('/theme\/[^\/]*\/pix\/(.*)$/',$_SERVER['REDIRECT_URL'],$matches)) {
    die();
}

if (file_exists($CFG->dirroot.'/pix/'.$matches[1])) {
    header("Location: ".$CFG->wwwroot.'/pix/'.$matches[1]);
}

?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head>
<body>
<h1>Picture not found</h1>
<p><?php echo $_SERVER['REDIRECT_ERROR_NOTES']; ?></p>
</body>
</html>
