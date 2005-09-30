<?php

/**
If you're using custompix in your theme, but you don't want to have to copy every pix from /pix into /theme/yourtheme/pix, use this as a 404 handler.
You need to put a snippet like the following into your apacheconfig:

<Location /moodle/theme/yourtheme/pix >
   ErrorDocument 404 /moodle/fixpix.php
</Location>

**/


require_once('config.php');

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

?><html>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML><HEAD>
<TITLE>404 Not Found</TITLE>
</HEAD><BODY>
<H1>Not Found</H1>
<P><?php echo $_SERVER['REDIRECT_ERROR_NOTES']; ?></P>
<HR>
</BODY></HTML>