<?PHP // $Id$

// This file is generally only included from admin/index.php 
// It defines default values for any important configuration variables

   $defaults = array (
       "auth"             => "email",
       "allowunenroll"    =>  true,
       "cachetext"        =>  60,
       "changepassword"   =>  true,
       "country"          => "",
       "debug"            =>  7,
       "deleteunconfirmed" => 168,
       "enablerssfeeds"   => 0,
       "extendedusernamechars"   => false,
       "filteruploadedfiles"  =>  true,
       "forcelogin"       =>  false,
       "forceloginforprofiles"  =>  false,
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
       "opentogoogle"     =>  false,
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
       "textfilters"      => "mod/glossary/dynalink.php",
       "timezone"         => 99,
       "theme"            => "standard",
       "unzip"            => "",
       "zip"              => ""
    );

?>
