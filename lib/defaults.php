<?PHP // $Id$

// This file is generally only included from admin/index.php 
// It defines default values for any important configuration variables

   $defaults = array (
       "theme"          => "standard",
       "lang"           => "en",
       "locale"         => "en",
       "smtphosts"      => "",
       "gdversion"      =>  1,
       "longtimenosee"  =>  100,
       "zip"            => "/usr/bin/zip",
       "unzip"          => "/usr/bin/unzip",
       "slasharguments" =>  true,
       "proxyhost"      => "",
       "proxyport"      => "",
       "maxeditingtime" =>  1800,
       "errorlevel"     =>  7
    );

?>
