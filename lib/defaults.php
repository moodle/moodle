<?PHP // $Id$

// This file is generally only included from admin/index.php 
// It defines default values for any important configuration variables

   $defaults = array (
       "auth"             => "email",
       "allowunenroll"    =>  true,
       "changepassword"   =>  true,
       "country"          => "",
       "debug"            =>  7,
       "forcelogin"       =>  false,
       "fullnamedisplay"  => "firstname lastname",
       "framename"        => "_top",
       "frontpage"        =>  0,
       "gdversion"        =>  1,
       "guestloginbutton" =>  1,
       "htmleditor"       =>  true,
       "lang"             => "en",
       "langmenu"         =>  1,
       "langlist"         => "",
       "locale"           => "en",
       "loglifetime"      =>  0,
       "longtimenosee"    =>  100,
       "maxbytes"         =>  0,
       "maxeditingtime"   =>  1800,
       "prefix"           => "",
       "proxyhost"        => "",
       "proxyport"        => "",
       "secureforms"      =>  false,
       "sessioncookie"    => "",
       "sessiontimeout"   =>  7200,
       "slasharguments"   =>  1,
       "smtphosts"        => "",
       "smtppass"         => "",
       "smtpuser"         => "",
       "teacherassignteachers"  => true,
       "theme"            => "standard",
       "unzip"            => "",
       "zip"              => ""
    );

?>
