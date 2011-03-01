#!/usr/bin/php -f
<?php

//error_reporting(0);
//ini_set('display_errors',0);
require_once(dirname(dirname(__FILE__)).'/config.php');
$tmp = explode('@',$_ENV['RECIPIENT']);
$address = $tmp[0];

// BOUNCE EMAILS TO NOREPLY
if ($_ENV['RECIPIENT'] == $CFG->noreplyaddress) {
    $user = new stdClass();
    $user->email = $_ENV['SENDER'];

    if (!validate_email($user->email)) {
        die();
    }

    $site = get_site();
    $subject = get_string('noreplybouncesubject','moodle',format_string($site->fullname));
    $body = get_string('noreplybouncemessage','moodle',format_string($site->fullname))."\n\n";

    $fd = fopen('php://stdin','r');
    if ($fd) {
        while(!feof($fd)) {
            $body .=  fgets($fd);
        }
        fclose($fd);
    }

    $user->id = 0; // to prevent anything annoying happening

    $from->firstname = null;
    $from->lastname = null;
    $from->email = '<>';
    $from->maildisplay = true;

    email_to_user($user,$from,$subject,$body);
    die ();
}
/// ALL OTHER PROCESSING
// we need to split up the address
$prefix = substr($address,0,4);
$mod = substr($address,4,2);
$modargs = substr($address,6,-16);
$hash = substr($address,-16);

if (substr(md5($prefix.$mod.$modargs.$CFG->siteidentifier),0,16) != $hash) {
    die("HASH DIDN'T MATCH!\n");
}
list(,$modid) = unpack('C',base64_decode($mod.'=='));

if ($modid == '0') { // special
    $modname = 'moodle';
}
else {
    $modname = $DB->get_field("modules", "name", array("id"=>$modid));
    include_once('mod/'.$modname.'/lib.php');
}
$function = $modname.'_process_email';

if (!function_exists($function)) {
    die();
}
$fd = fopen('php://stdin','r');
if (!$fd) {
    exit();
}

while(!feof($fd)) {
    $body .= fgets($fd);
}

$function($modargs,$body);

fclose($fd);




