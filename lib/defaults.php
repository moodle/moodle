<?PHP // $Id$

// This file is generally only included from admin/index.php 
// It defines default values for any important configuration variables

   $defaults = array (
       "theme"            => "standard",
       "lang"             => "en",
       "langdir"          => "LTR",
       "locale"           => "en",
       "auth"             => "email",
       "smtphosts"        => "",
       "smtpuser"         => "",
       "smtppass"         => "",
       "gdversion"        =>  1,
       "longtimenosee"    =>  100,
       "zip"              => "/usr/bin/zip",
       "unzip"            => "/usr/bin/unzip",
       "slasharguments"   =>  1,
       "htmleditor"       =>  true,
       "proxyhost"        => "",
       "proxyport"        => "",
       "maxeditingtime"   =>  1800,
       "changepassword"   =>  true,
       "country"          => "",
       "prefix"           => "",
       "guestloginbutton" =>  1,
       "sessiontimeout"   =>  7200,
       "debug"            =>  7
    );

?>
