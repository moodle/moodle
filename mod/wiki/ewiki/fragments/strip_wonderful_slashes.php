<?php

/*

  this strips all "\" from $_REQUEST and
  disables the runtime garbaging as well

  just include() it before ewiki.php
  and everythink should work fine


  for Apache+mod_php you should however rather use the
  [.htaccess] PHP reconfiguration trick:
    php_flag magic_quotes_gpc off
    php_flag magic_quotes_runtime off

*/


 #-- this is very evil too
 set_magic_quotes_runtime(0);


 #-- strip \'s only if the variables garbaging is really enabled
 if (get_magic_quotes_gpc()) {

    $superglobals = array(
       "_REQUEST",
       "_GET",
       "_POST",
       "_COOKIE",
       "_ENV",
       "_SERVER"
    );

    foreach ($superglobals as $AREA) {

       foreach ($GLOBALS[$AREA] as $name => $value) {

          if (!is_array($value)) {
             $GLOBALS[$AREA][$name] = stripslashes($value);
          }
       }
    }

 }


?>