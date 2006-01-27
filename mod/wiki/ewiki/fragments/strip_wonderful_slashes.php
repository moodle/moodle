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

 #-- Moodle always addslashes to everything so
 #-- we strip them back again here to allow
 #-- the wiki module itself to add them before
 #-- insert. Strange triple add-strip-add but
 #-- this was the best way to solve problems
 #-- without changing how the rest of the 
 #-- module works.

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



?>
